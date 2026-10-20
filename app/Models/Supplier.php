<?php
class Supplier {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->selfHealSchema();
    }

    private function selfHealSchema() {
        try {
            $this->db->query("SHOW COLUMNS FROM vendors LIKE 'status'");
            if (!$this->db->single()) {
                $this->db->query("ALTER TABLE vendors ADD COLUMN status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active' AFTER opening_balance_date");
                $this->db->execute();
            }
        } catch (Exception $e) {
            error_log("Supplier selfHealSchema Error: " . $e->getMessage());
        }
    }

    public function getAllSuppliers($activeOnly = false) {
        // Calculates the billed (GRNs), paid (Expenses/Payments), and returned (Supplier Returns) to determine outstanding balance
        $sql = "
            SELECT v.*,
                   (SELECT COALESCE(SUM(gri.total), 0) 
                    FROM grn_items gri 
                    JOIN goods_receipt_notes grn ON gri.grn_id = grn.id 
                    WHERE grn.vendor_id = v.id) as total_billed,
                   (SELECT COALESCE(SUM(amount), 0) 
                    FROM expenses 
                    WHERE vendor_id = v.id) 
                   + 
                   (SELECT COALESCE(SUM(amount), 0) 
                    FROM supplier_payments 
                    WHERE vendor_id = v.id AND status = 'Active') as total_paid,
                   (SELECT COALESCE(SUM(total_amount), 0) 
                    FROM supplier_returns 
                    WHERE vendor_id = v.id) as total_returned,
                   (SELECT SUM(t.credit) - SUM(t.debit) 
                    FROM transactions t
                    JOIN journal_entries je ON t.journal_entry_id = je.id
                    JOIN chart_of_accounts coa ON t.account_id = coa.id
                    WHERE (COALESCE(t.entity_type, je.entity_type) IN ('Vendor', 'Supplier')) 
                      AND (COALESCE(t.entity_id, je.entity_id) = v.id) 
                      AND je.status = 'Posted' 
                      AND coa.account_code = '2000') as total_je_balance
            FROM vendors v
        ";
        if ($activeOnly) {
            $sql .= " WHERE v.status = 'Active' ";
        }
        $sql .= " ORDER BY v.name ASC";

        $this->db->query($sql);
        $suppliers = $this->db->resultSet();
        foreach ($suppliers as $s) {
            $s->outstanding_balance = floatval($s->opening_balance ?? 0) + $s->total_billed - $s->total_paid - $s->total_returned + floatval($s->total_je_balance ?? 0);
        }
        return $suppliers;
    }

    public function getActiveSuppliers() {
        return $this->getAllSuppliers(true);
    }

    public function getActiveVendors() {
        return $this->getAllSuppliers(true);
    }

    public function getSupplierById($id) {
        $this->db->query("SELECT * FROM vendors WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function addSupplier($data) {
        $status = isset($data['status']) && in_array($data['status'], ['Active', 'Inactive']) ? $data['status'] : 'Active';
        $this->db->query("INSERT INTO vendors (name, email, phone, address, opening_balance, opening_balance_date, status) VALUES (:name, :email, :phone, :address, :opening_balance, :opening_balance_date, :status)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':opening_balance', $data['opening_balance'] ?? 0.00);
        $this->db->bind(':opening_balance_date', $data['opening_balance_date'] ?? null);
        $this->db->bind(':status', $status);
        $result = $this->db->execute();
        
        if ($result) {
            $newId = $this->db->lastInsertId();
            $newAmount = floatval($data['opening_balance'] ?? 0);
            if ($newAmount > 0) {
                $newDate = !empty($data['opening_balance_date']) ? $data['opening_balance_date'] : date('Y-m-d');
                $userId = $_SESSION['user_id'] ?? 1;
                $this->syncOpeningBalanceAccounting($newId, $newAmount, $newDate, $userId);

                try {
                    require_once __DIR__ . '/AuditLog.php';
                    $auditLog = new AuditLog();
                    $supName = $data['name'] ?? "Supplier #{$newId}";
                    $desc = "Added Opening Balance for Supplier ID {$newId} ({$supName}): Rs: " . number_format($newAmount, 2) . " (Effective Date: {$newDate})";
                    $auditLog->logAction(
                        $userId,
                        'Add Supplier Opening Balance',
                        'Supplier',
                        $desc,
                        $newId,
                        null,
                        ['opening_balance' => $newAmount, 'opening_balance_date' => $newDate]
                    );
                } catch (\Throwable $e) {
                    error_log("AuditLog Error in addSupplier: " . $e->getMessage());
                }
            }
        }
        return $result;
    }

    public function updateSupplier($data) {
        $existing = $this->getSupplierById($data['id']);
        $status = isset($data['status']) && in_array($data['status'], ['Active', 'Inactive']) ? $data['status'] : 'Active';
        $this->db->query("UPDATE vendors SET name = :name, email = :email, phone = :phone, address = :address, opening_balance = :opening_balance, opening_balance_date = :opening_balance_date, status = :status WHERE id = :id");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':opening_balance', $data['opening_balance'] ?? 0.00);
        $this->db->bind(':opening_balance_date', $data['opening_balance_date'] ?? null);
        $this->db->bind(':status', $status);
        $result = $this->db->execute();
        
        if ($result && isset($data['opening_balance']) && $existing) {
            $oldAmount = floatval($existing->opening_balance ?? 0);
            $newAmount = floatval($data['opening_balance'] ?? 0);
            $oldDate = $existing->opening_balance_date ?? null;
            $newDate = !empty($data['opening_balance_date']) ? $data['opening_balance_date'] : date('Y-m-d');

            if (abs($oldAmount - $newAmount) > 0.0001 || ($newAmount > 0 && $oldDate !== $newDate)) {
                $userId = $_SESSION['user_id'] ?? 1;
                $this->syncOpeningBalanceAccounting($data['id'], $newAmount, $newDate, $userId);

                try {
                    require_once __DIR__ . '/AuditLog.php';
                    $auditLog = new AuditLog();
                    $supName = $data['name'] ?? ($existing->name ?? "Supplier #{$data['id']}");
                    $actionType = ($oldAmount <= 0 && $newAmount > 0) ? 'Add Supplier Opening Balance' : 'Update Supplier Opening Balance';
                    $desc = "Supplier Opening Balance for Supplier ID {$data['id']} ({$supName}) updated from Rs: " . number_format($oldAmount, 2) . " to Rs: " . number_format($newAmount, 2) . " (Effective Date: {$newDate})";
                    $auditLog->logAction(
                        $userId,
                        $actionType,
                        'Supplier',
                        $desc,
                        $data['id'],
                        ['opening_balance' => $oldAmount, 'opening_balance_date' => $oldDate],
                        ['opening_balance' => $newAmount, 'opening_balance_date' => $newDate]
                    );
                } catch (\Throwable $e) {
                    error_log("AuditLog Error in updateSupplier: " . $e->getMessage());
                }
            }
        }
        return $result;
    }

    public function syncOpeningBalanceAccounting($supplierId, $amount, $date, $userId) {
        // 1. Find AP Account (2000 Accounts Payable)
        $this->db->query("SELECT * FROM chart_of_accounts WHERE account_code = '2000' OR (account_type = 'Liability' AND account_name LIKE '%Payable%') LIMIT 1");
        $apAccount = $this->db->single();
        if (!$apAccount) return false;

        // 2. Find Equity Account (3900 Opening Balance Equity, fallback to 3000)
        $this->db->query("SELECT * FROM chart_of_accounts WHERE account_code = '3900' OR account_name LIKE '%Opening Balance Equity%' LIMIT 1");
        $eqAccount = $this->db->single();
        if (!$eqAccount) {
            $this->db->query("SELECT * FROM chart_of_accounts WHERE account_code = '3000' LIMIT 1");
            $eqAccount = $this->db->single();
        }
        if (!$eqAccount) {
            $this->db->query("INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, is_active) VALUES ('3900', 'Opening Balance Equity', 'Equity', 'Equity', 1)");
            $this->db->execute();
            $this->db->query("SELECT * FROM chart_of_accounts WHERE account_code = '3900' LIMIT 1");
            $eqAccount = $this->db->single();
        }

        $ref = "OPBAL-SUP-" . $supplierId;
        $legacyRef = "OB-SUP-" . $supplierId;

        // 3. Reverse existing Journal Entries if any
        $this->db->query("SELECT id FROM journal_entries WHERE reference IN (:ref1, :ref2)");
        $this->db->bind(':ref1', $ref);
        $this->db->bind(':ref2', $legacyRef);
        $existingJes = $this->db->resultSet();

        foreach ($existingJes as $je) {
            $this->db->query("SELECT * FROM transactions WHERE journal_entry_id = :jid");
            $this->db->bind(':jid', $je->id);
            $oldTxns = $this->db->resultSet();

            foreach ($oldTxns as $txn) {
                $this->db->query("SELECT account_type FROM chart_of_accounts WHERE id = :id");
                $this->db->bind(':id', $txn->account_id);
                $acc = $this->db->single();
                if ($acc) {
                    $sql = "UPDATE chart_of_accounts SET balance = balance ";
                    $sql .= in_array($acc->account_type, ['Asset', 'Expense']) ? "- :debit + :credit " : "+ :debit - :credit ";
                    $sql .= "WHERE id = :id";
                    $this->db->query($sql);
                    $this->db->bind(':debit', $txn->debit);
                    $this->db->bind(':credit', $txn->credit);
                    $this->db->bind(':id', $txn->account_id);
                    $this->db->execute();
                }
            }

            $this->db->query("DELETE FROM transactions WHERE journal_entry_id = :jid");
            $this->db->bind(':jid', $je->id);
            $this->db->execute();

            $this->db->query("DELETE FROM journal_entries WHERE id = :jid");
            $this->db->bind(':jid', $je->id);
            $this->db->execute();
        }

        $amount = floatval($amount);
        // 4. Create new Journal Entry if amount > 0
        if ($amount > 0) {
            $desc = "Opening Balance for Supplier ID " . $supplierId;
            $this->db->query("INSERT INTO journal_entries (entry_date, reference, description, created_by, status) VALUES (:date, :ref, :desc, :uid, 'Posted')");
            $this->db->bind(':date', $date);
            $this->db->bind(':ref', $ref);
            $this->db->bind(':desc', $desc);
            $this->db->bind(':uid', $userId);
            $this->db->execute();
            $newJeId = $this->db->lastInsertId();

            // Double Entry:
            // Debit Equity (3900 Opening Balance Equity)
            // Credit Liability (2000 Accounts Payable)
            $lines = [
                ['account_id' => $eqAccount->id, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $apAccount->id, 'debit' => 0, 'credit' => $amount]
            ];

            foreach ($lines as $line) {
                $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit) VALUES (:jid, :aid, :deb, :cred)");
                $this->db->bind(':jid', $newJeId);
                $this->db->bind(':aid', $line['account_id']);
                $this->db->bind(':deb', $line['debit']);
                $this->db->bind(':cred', $line['credit']);
                $this->db->execute();

                $this->db->query("SELECT account_type FROM chart_of_accounts WHERE id = :id");
                $this->db->bind(':id', $line['account_id']);
                $acc = $this->db->single();
                if ($acc) {
                    $sql = "UPDATE chart_of_accounts SET balance = balance ";
                    $sql .= in_array($acc->account_type, ['Asset', 'Expense']) ? "+ :debit - :credit " : "- :debit + :credit ";
                    $sql .= "WHERE id = :id";
                    $this->db->query($sql);
                    $this->db->bind(':debit', $line['debit']);
                    $this->db->bind(':credit', $line['credit']);
                    $this->db->bind(':id', $line['account_id']);
                    $this->db->execute();
                }
            }
        }

        return true;
    }

    public function getSupplierStats($id) {
        // Total POs count and total PO amount
        $this->db->query("SELECT COUNT(id) as total_pos, COALESCE(SUM(total_amount), 0) as total_po_amount FROM purchase_orders WHERE vendor_id = :id");
        $this->db->bind(':id', $id);
        $poStats = $this->db->single();
        
        // Total Billed (GRNs)
        $this->db->query("
            SELECT COALESCE(SUM(gri.total), 0) as total_billed 
             FROM grn_items gri 
             JOIN goods_receipt_notes grn ON gri.grn_id = grn.id 
             WHERE grn.vendor_id = :id
        ");
        $this->db->bind(':id', $id);
        $billed = $this->db->single();

        // Total Paid (Expenses + Active Supplier Payments)
        $this->db->query("
            SELECT 
                (SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE vendor_id = :id1) 
                + 
                (SELECT COALESCE(SUM(amount), 0) FROM supplier_payments WHERE vendor_id = :id2 AND status = 'Active') 
                as total_paid
        ");
        $this->db->bind(':id1', $id);
        $this->db->bind(':id2', $id);
        $paid = $this->db->single();

        // Total Returned (Supplier Returns)
        $this->db->query("SELECT COALESCE(SUM(total_amount), 0) as total_returned FROM supplier_returns WHERE vendor_id = :id");
        $this->db->bind(':id', $id);
        $returned = $this->db->single();

        // Total JE Balance
        $this->db->query("
            SELECT COALESCE(SUM(t.credit) - SUM(t.debit), 0) as total_je_balance
            FROM transactions t
            JOIN journal_entries je ON t.journal_entry_id = je.id
            JOIN chart_of_accounts coa ON t.account_id = coa.id
            WHERE (COALESCE(t.entity_type, je.entity_type) IN ('Vendor', 'Supplier')) 
              AND (COALESCE(t.entity_id, je.entity_id) = :id) 
              AND je.status = 'Posted' 
              AND coa.account_code = '2000'
        ");
        $this->db->bind(':id', $id);
        $je = $this->db->single();

        $stats = new stdClass();
        $this->db->query("SELECT opening_balance FROM vendors WHERE id = :id");
        $this->db->bind(':id', $id);
        $supObj = $this->db->single();
        $opening = $supObj ? floatval($supObj->opening_balance) : 0.00;

        $stats->total_pos = $poStats->total_pos;
        $stats->total_po_amount = $poStats->total_po_amount;
        $stats->total_billed = $billed->total_billed;
        $stats->total_paid = $paid->total_paid;
        $stats->total_returned = $returned->total_returned;
        $stats->total_je_balance = $je->total_je_balance;
        $stats->opening_balance = $opening;
        $stats->outstanding = $opening + $stats->total_billed - $stats->total_paid - $stats->total_returned + $stats->total_je_balance;

        return $stats;
    }

    public function getActivityLedger($id) {
        $sql = "
            SELECT 'GRN' as type, grn.id, grn.grn_number as ref, grn.grn_date as date,
                   0 as debit, 
                   (SELECT COALESCE(SUM(total), 0) FROM grn_items WHERE grn_id = grn.id) as credit, 
                   grn.created_at
            FROM goods_receipt_notes grn
            WHERE grn.vendor_id = :vid1
            UNION ALL
            SELECT 'Expense' as type, e.id, CONCAT(e.reference, ' - ', e.description) as ref, e.expense_date as date,
                   e.amount as debit, 
                   0 as credit, 
                   e.created_at
            FROM expenses e
            WHERE e.vendor_id = :vid2
            UNION ALL
            SELECT 'Supplier Return' as type, sr.id, sr.return_number as ref, sr.return_date as date,
                   sr.total_amount as debit, 
                   0 as credit, 
                   sr.created_at
            FROM supplier_returns sr
            WHERE sr.vendor_id = :vid3
            UNION ALL
            SELECT 'Payment' as type, sp.id, CONCAT('Pay: ', sp.payment_method, IF(sp.reference != '', CONCAT(' (', sp.reference, ')'), '')) as ref, sp.payment_date as date,
                   sp.amount as debit, 
                   0 as credit, 
                   sp.created_at
            FROM supplier_payments sp
            WHERE sp.vendor_id = :vid4 AND sp.status = 'Active'
            UNION ALL
            SELECT 'Payment Reversal' as type, sp.id, CONCAT('Payment Reversed: ', sp.payment_method, IF(sp.reference != '', CONCAT(' (', sp.reference, ')'), '')) as ref, DATE(COALESCE(sp.reversed_at, sp.created_at)) as date,
                   0 as debit, 
                   sp.amount as credit, 
                   COALESCE(sp.reversed_at, sp.created_at) as created_at
            FROM supplier_payments sp
            WHERE sp.vendor_id = :vid7 AND sp.status = 'Reversed'
            UNION ALL
            SELECT 'Opening Bal' as type, id, 'System generated' as ref, COALESCE(opening_balance_date, DATE(created_at)) as date,
                   0 as debit,
                   opening_balance as credit,
                   created_at
            FROM vendors
            WHERE id = :vid5 AND opening_balance > 0
            UNION ALL
            SELECT 'Journal Entry' as type, je.id, IF(je.reference IS NOT NULL AND je.reference != '', CONCAT(je.reference, ' - ', COALESCE(t.description, je.description)), je.description) as ref, je.entry_date as date,
                   t.debit as debit,
                   t.credit as credit,
                   je.created_at
            FROM transactions t
            JOIN journal_entries je ON t.journal_entry_id = je.id
            JOIN chart_of_accounts coa ON t.account_id = coa.id
            WHERE (COALESCE(t.entity_type, je.entity_type) IN ('Vendor', 'Supplier')) 
              AND (COALESCE(t.entity_id, je.entity_id) = :vid6) 
              AND je.status = 'Posted' 
              AND coa.account_code = '2000'
            ORDER BY date ASC, created_at ASC
        ";
        $this->db->query($sql);
        $this->db->bind(':vid1', $id);
        $this->db->bind(':vid2', $id);
        $this->db->bind(':vid3', $id);
        $this->db->bind(':vid4', $id);
        $this->db->bind(':vid5', $id);
        $this->db->bind(':vid6', $id);
        $this->db->bind(':vid7', $id);
        $ledger = $this->db->resultSet();

        $balance = 0;
        foreach($ledger as $row) {
            $balance += $row->credit;
            $balance -= $row->debit;
            $row->balance = $balance;
        }
        return array_reverse($ledger);
    }

    public function getSupplierPOs($id) {
        $this->db->query("SELECT * FROM purchase_orders WHERE vendor_id = :vid ORDER BY po_date DESC");
        $this->db->bind(':vid', $id);
        return $this->db->resultSet();
    }

    public function getSupplierProducts($id) {
        $this->db->query("
            SELECT i.id as item_id, ivo.id as var_opt_id,
                   CASE 
                       WHEN ivo.id IS NOT NULL THEN CONCAT(i.name, ' - ', v.name, ': ', vv.value_name)
                       ELSE i.name 
                   END as product_name,
                   i.item_code as sku,
                   COALESCE(ivo.price, i.price) as price,
                   COALESCE(ivo.cost, i.cost_price) as cost_price,
                   COALESCE(ivo.quantity_on_hand, i.quantity_on_hand) as quantity_on_hand
            FROM items i
            INNER JOIN item_suppliers i_s ON i.id = i_s.item_id
            LEFT JOIN item_variation_options ivo ON ivo.item_id = i.id
            LEFT JOIN variations v ON ivo.variation_id = v.id
            LEFT JOIN variation_values vv ON ivo.variation_value_id = vv.id
            WHERE i_s.supplier_id = :vid
            ORDER BY product_name ASC
        ");
        $this->db->bind(':vid', $id);
        return $this->db->resultSet();
    }

    // --- Legacy Vendor Model Aliases for System-wide Compatibility ---
    public function getAllVendors() {
        return $this->getAllSuppliers();
    }

    public function getVendorById($id) {
        return $this->getSupplierById($id);
    }

    public function addVendor($data) {
        return $this->addSupplier($data);
    }

    public function updateVendor($data) {
        return $this->updateSupplier($data);
    }

    public function getVendorExpenses($vendorId) {
        $this->db->query("SELECT * FROM expenses WHERE vendor_id = :vid ORDER BY expense_date DESC");
        $this->db->bind(':vid', $vendorId);
        return $this->db->resultSet();
    }

    public function getVendorPOs($vendorId) {
        return $this->getSupplierPOs($vendorId);
    }

    public function recordPayment($data, $userId) {
        try {
            $this->db->beginTransaction();

            $desc = "Payment to Supplier: " . $data['method'] . " to Supplier ID " . $data['supplier_id'];
            $this->db->query("INSERT INTO journal_entries (entry_date, reference, description, created_by, status) VALUES (:date, :ref, :desc, :uid, 'Posted')");
            $this->db->bind(':date', $data['date']);
            $this->db->bind(':ref', $data['reference']);
            $this->db->bind(':desc', $desc);
            $this->db->bind(':uid', $userId);
            $this->db->execute();
            $journalId = $this->db->lastInsertId();

            // Double Entry: Debit Accounts Payable (ap_account_id), Credit Asset (asset_account_id)
            $lines = [
                ['account_id' => $data['ap_account_id'], 'debit' => $data['amount'], 'credit' => 0],
                ['account_id' => $data['asset_account_id'], 'debit' => 0, 'credit' => $data['amount']]
            ];

            foreach ($lines as $line) {
                $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit) VALUES (:jid, :aid, :deb, :cred)");
                $this->db->bind(':jid', $journalId);
                $this->db->bind(':aid', $line['account_id']);
                $this->db->bind(':deb', $line['debit']);
                $this->db->bind(':cred', $line['credit']);
                $this->db->execute();

                $this->db->query("SELECT account_type FROM chart_of_accounts WHERE id = :id");
                $this->db->bind(':id', $line['account_id']);
                $acc = $this->db->single();
                
                $sql = "UPDATE chart_of_accounts SET balance = balance ";
                $sql .= in_array($acc->account_type, ['Asset', 'Expense']) ? "+ :debit - :credit " : "- :debit + :credit ";
                $sql .= "WHERE id = :id";
                $this->db->query($sql);
                $this->db->bind(':debit', $line['debit']);
                $this->db->bind(':credit', $line['credit']);
                $this->db->bind(':id', $line['account_id']);
                $this->db->execute();
            }

            // Insert into expenses
            $this->db->query("INSERT INTO expenses (reference, vendor_id, expense_date, amount, description, journal_entry_id, created_by) 
                              VALUES (:ref, :vid, :edate, :amt, :desc, :jid, :uid)");
            $this->db->bind(':ref', $data['reference']);
            $this->db->bind(':vid', $data['supplier_id']);
            $this->db->bind(':edate', $data['date']);
            $this->db->bind(':amt', $data['amount']);
            $this->db->bind(':desc', "Supplier Payment - Reference: " . $data['reference']);
            $this->db->bind(':jid', $journalId);
            $this->db->bind(':uid', $userId);
            $this->db->execute();

            if ($data['method'] === 'Cheque') {
                $this->db->query("INSERT INTO cheques (vendor_id, customer_id, bank_name, cheque_number, amount, banking_date, status, created_by) 
                                  VALUES (:vid, NULL, :bn, :cn, :amt, :bdate, 'Pending', :uid)");
                $this->db->bind(':vid', $data['supplier_id']);
                $this->db->bind(':bn', $data['cheque_bank']);
                $this->db->bind(':cn', $data['cheque_number']);
                $this->db->bind(':amt', $data['amount']);
                $this->db->bind(':bdate', $data['cheque_date']);
                $this->db->bind(':uid', $userId);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
}

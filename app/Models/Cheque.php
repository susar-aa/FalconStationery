<?php
class Cheque {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->selfHealSchema();
    }

    private function selfHealSchema() {
        try {
            $this->db->query("SHOW COLUMNS FROM cheques LIKE 'cleared_date'");
            if (!$this->db->single()) {
                $this->db->query("ALTER TABLE cheques 
                                  ADD COLUMN cleared_date DATE NULL AFTER banking_date,
                                  ADD COLUMN cleared_by INT(11) NULL AFTER cleared_date,
                                  ADD COLUMN clearance_journal_id INT(11) NULL AFTER cleared_by,
                                  ADD COLUMN clearance_notes VARCHAR(255) NULL AFTER clearance_journal_id");
                $this->db->execute();
            }

            $this->db->query("SHOW INDEX FROM cheques WHERE Key_name = 'idx_chk_clearance'");
            if (!$this->db->single()) {
                $this->db->query("ALTER TABLE cheques ADD INDEX idx_chk_clearance (status, bank_account_id, cleared_date)");
                $this->db->execute();
            }
        } catch (Exception $e) {
            error_log("Cheque selfHealSchema Error: " . $e->getMessage());
        }
    }

    public function getAllCheques($search = '') {
        $sql = "SELECT ch.*, 
                       ba.account_name as drawn_bank_name, 
                       ba.account_code as drawn_bank_code,
                       c.name as customer_name,
                       v.name as vendor_name,
                       sp.name as service_provider_name,
                       u.name as cleared_by_name
                FROM cheques ch 
                LEFT JOIN chart_of_accounts ba ON ch.bank_account_id = ba.id
                LEFT JOIN customers c ON ch.customer_id = c.id
                LEFT JOIN vendors v ON ch.vendor_id = v.id
                LEFT JOIN service_providers sp ON ch.service_provider_id = sp.id
                LEFT JOIN users u ON ch.cleared_by = u.id";

        if (!empty($search)) {
            $sql .= " WHERE ch.cheque_number LIKE :search 
                       OR ch.payee_name LIKE :search 
                       OR c.name LIKE :search
                       OR v.name LIKE :search
                       OR sp.name LIKE :search
                       OR ba.account_name LIKE :search
                       OR ch.bank_name LIKE :search";
        }

        $sql .= " ORDER BY ch.status ASC, ch.banking_date ASC";

        $this->db->query($sql);
        if (!empty($search)) {
            $this->db->bind(':search', "%$search%");
        }
        return $this->db->resultSet();
    }

    public function addCheque($data) {
        try {
            $this->db->beginTransaction();
            
            $type = $data['type'] ?? 'Received';
            $customerId = (!empty($data['customer_id']) && $type === 'Received') ? intval($data['customer_id']) : null;
            $vendorId = (!empty($data['vendor_id']) && $type === 'Issued') ? intval($data['vendor_id']) : null;
            $spId = (!empty($data['service_provider_id']) && $type === 'Issued') ? intval($data['service_provider_id']) : null;

            $this->db->query("INSERT INTO cheques (type, payee_name, customer_id, vendor_id, service_provider_id, bank_name, cheque_number, amount, banking_date, created_by, bank_account_id, status) 
                              VALUES (:type, :payee, :cid, :vid, :spid, :bank, :cnum, :amt, :bdate, :uid, :bank_account_id, 'Pending')");
            $this->db->bind(':type', $type);
            $this->db->bind(':payee', $data['payee_name']);
            $this->db->bind(':cid', $customerId);
            $this->db->bind(':vid', $vendorId);
            $this->db->bind(':spid', $spId);
            $this->db->bind(':bank', $data['bank_name']);
            $this->db->bind(':cnum', $data['cheque_number']);
            $this->db->bind(':amt', $data['amount']);
            $this->db->bind(':bdate', $data['banking_date']);
            $this->db->bind(':uid', $data['created_by']);
            $this->db->bind(':bank_account_id', !empty($data['bank_account_id']) ? $data['bank_account_id'] : null);
            $this->db->execute();
            $chequeId = $this->db->lastInsertId();
            
            // Post Journal Entry
            $this->postChequeJournalEntry($chequeId, 'Pending', null);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("addCheque error: " . $e->getMessage());
            return false;
        }
    }

    public function updateCheque($data) {
        try {
            $this->db->beginTransaction();
            
            // Get old status and bank account id
            $this->db->query("SELECT status, bank_account_id FROM cheques WHERE id = :id FOR UPDATE");
            $this->db->bind(':id', $data['id']);
            $old = $this->db->single();
            
            $type = $data['type'] ?? 'Received';
            $customerId = (!empty($data['customer_id']) && $type === 'Received') ? intval($data['customer_id']) : null;
            $vendorId = (!empty($data['vendor_id']) && $type === 'Issued') ? intval($data['vendor_id']) : null;
            $spId = (!empty($data['service_provider_id']) && $type === 'Issued') ? intval($data['service_provider_id']) : null;

            $this->db->query("UPDATE cheques 
                              SET type = :type, customer_id = :cid, vendor_id = :vid, service_provider_id = :spid,
                                  payee_name = :payee, bank_name = :bank, cheque_number = :cnum, 
                                  amount = :amt, banking_date = :bdate, status = :status, bank_account_id = :bank_account_id 
                              WHERE id = :id");
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':type', $type);
            $this->db->bind(':cid', $customerId);
            $this->db->bind(':vid', $vendorId);
            $this->db->bind(':spid', $spId);
            $this->db->bind(':payee', $data['payee_name']);
            $this->db->bind(':bank', $data['bank_name']);
            $this->db->bind(':cnum', $data['cheque_number']);
            $this->db->bind(':amt', $data['amount']);
            $this->db->bind(':bdate', $data['banking_date']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':bank_account_id', !empty($data['bank_account_id']) ? $data['bank_account_id'] : null);
            $this->db->execute();
            
            // If status has changed or details updated, post adjustment / realization entries
            if ($old && ($old->status !== $data['status'])) {
                $this->postChequeJournalEntry($data['id'], $data['status'], $old->status);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("updateCheque error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteCheque($id) {
        $this->db->query("DELETE FROM cheques WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function returnCustomerCheque($chequeId, $reason, $date, $charge, $chargeAccountId, $userId) {
        try {
            $this->db->beginTransaction();

            $this->db->query("SELECT status, bank_account_id FROM cheques WHERE id = :id FOR UPDATE");
            $this->db->bind(':id', $chequeId);
            $old = $this->db->single();

            if (!$old || in_array($old->status, ['Bounced', 'Returned', 'Cancelled'])) {
                $this->db->rollBack();
                return false;
            }

            $this->db->query("UPDATE cheques 
                              SET status = 'Returned', 
                                  return_reason = :reason, 
                                  returned_date = :rdate, 
                                  return_charge = :charge, 
                                  return_charge_account_id = :cacc, 
                                  returned_by = :uid 
                              WHERE id = :id");
            $this->db->bind(':id', $chequeId);
            $this->db->bind(':reason', $reason);
            $this->db->bind(':rdate', $date);
            $this->db->bind(':charge', floatval($charge));
            $this->db->bind(':cacc', !empty($chargeAccountId) ? intval($chargeAccountId) : null);
            $this->db->bind(':uid', $userId);
            $this->db->execute();

            $this->postChequeJournalEntry($chequeId, 'Returned', $old->status);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("returnCustomerCheque error: " . $e->getMessage());
            return false;
        }
    }

    public function clearIssuedCheque($chequeId, $bankAccountId, $clearanceDate, $notes = '', $userId = 1) {
        try {
            $this->db->beginTransaction();

            $this->db->query("SELECT * FROM cheques WHERE id = :id FOR UPDATE");
            $this->db->bind(':id', intval($chequeId));
            $chk = $this->db->single();

            if (!$chk) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Cheque not found.'];
            }

            if ($chk->status === 'Cleared') {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Cheque is already cleared.'];
            }

            if (in_array($chk->status, ['Bounced', 'Returned', 'Cancelled', 'Voided'])) {
                $this->db->rollBack();
                return ['success' => false, 'message' => "Cannot clear a cheque with status {$chk->status}."];
            }

            $amount = floatval($chk->amount);
            $chequeNum = $chk->cheque_number;
            $payeeName = $chk->payee_name ?: ($chk->vendor_id ? 'Supplier' : 'Payee');
            $clearanceDate = !empty($clearanceDate) ? $clearanceDate : date('Y-m-d');

            // If Endorsed 3rd Party Cheque:
            // Customer cheque handed over to supplier. Company bank account is NOT deducted.
            if ($chk->status === 'Endorsed') {
                $this->db->query("UPDATE cheques 
                                  SET status = 'Cleared', 
                                      cleared_date = :cdate, 
                                      cleared_by = :uid, 
                                      clearance_notes = :notes 
                                  WHERE id = :id");
                $this->db->bind(':cdate', $clearanceDate);
                $this->db->bind(':uid', intval($userId));
                $this->db->bind(':notes', !empty($notes) ? $notes : 'Endorsed 3rd Party Cheque Cleared');
                $this->db->bind(':id', intval($chequeId));
                $this->db->execute();

                $this->db->commit();
                return ['success' => true, 'message' => "Endorsed 3rd party cheque #{$chequeNum} marked as cleared."];
            }

            // Standard Issued Cheque:
            $bankAccountId = !empty($bankAccountId) ? intval($bankAccountId) : intval($chk->bank_account_id);
            if (!$bankAccountId) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Please select the bank account from which this cheque cleared.'];
            }

            // Verify bank account exists in chart_of_accounts
            $this->db->query("SELECT id, account_code, account_name, balance, account_type FROM chart_of_accounts WHERE id = :bid");
            $this->db->bind(':bid', $bankAccountId);
            $bankAcc = $this->db->single();

            if (!$bankAcc || $bankAcc->account_type !== 'Asset') {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Invalid bank account selected.'];
            }

            $outstandingChequesId = $this->getOutstandingChequesAccountId();

            // Double-entry accounting:
            // Debit: Outstanding Cheques (Issued) [2050] (Liability decreases)
            // Credit: Selected Bank Account (Asset decreases)
            $ref = 'CQ-CLR-' . $chequeNum;
            $desc = "Cleared Issued Cheque #" . $chequeNum . ($payeeName ? " - " . $payeeName : "");
            if (!empty($notes)) {
                $desc .= " (" . $notes . ")";
            }

            // 1. Journal Entry Header
            $this->db->query("INSERT INTO journal_entries (entry_date, reference, description, created_by, status, entity_type, entity_id) 
                              VALUES (:date, :ref, :desc, :uid, 'Posted', :etype, :eid)");
            $this->db->bind(':date', $clearanceDate);
            $this->db->bind(':ref', $ref);
            $this->db->bind(':desc', $desc);
            $this->db->bind(':uid', intval($userId));
            $this->db->bind(':etype', $chk->vendor_id ? 'Vendor' : null);
            $this->db->bind(':eid', $chk->vendor_id ?: null);
            $this->db->execute();
            $jeId = $this->db->lastInsertId();

            // 2. Transaction 1: Debit Outstanding Cheques [2050] (Liability decreases)
            $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description, entity_type, entity_id, is_cleared) 
                              VALUES (:jid, :aid, :deb, 0.00, :desc, :etype, :eid, 1)");
            $this->db->bind(':jid', $jeId);
            $this->db->bind(':aid', $outstandingChequesId);
            $this->db->bind(':deb', $amount);
            $this->db->bind(':desc', "Cheque Clearance Dr (Outstanding Cheques) #" . $chequeNum);
            $this->db->bind(':etype', $chk->vendor_id ? 'Vendor' : null);
            $this->db->bind(':eid', $chk->vendor_id ?: null);
            $this->db->execute();

            // Update Outstanding Cheques (Liability) balance
            $this->db->query("UPDATE chart_of_accounts SET balance = balance - :deb WHERE id = :aid");
            $this->db->bind(':deb', $amount);
            $this->db->bind(':aid', $outstandingChequesId);
            $this->db->execute();

            // 3. Transaction 2: Credit Bank Account (Asset decreases)
            $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description, entity_type, entity_id, is_cleared) 
                              VALUES (:jid, :aid, 0.00, :cred, :desc, :etype, :eid, 1)");
            $this->db->bind(':jid', $jeId);
            $this->db->bind(':aid', $bankAccountId);
            $this->db->bind(':cred', $amount);
            $this->db->bind(':desc', "Cheque Clearance Cr (" . $bankAcc->account_name . ") #" . $chequeNum);
            $this->db->bind(':etype', $chk->vendor_id ? 'Vendor' : null);
            $this->db->bind(':eid', $chk->vendor_id ?: null);
            $this->db->execute();

            // Update Bank Account (Asset) balance
            $this->db->query("UPDATE chart_of_accounts SET balance = balance - :cred WHERE id = :aid");
            $this->db->bind(':cred', $amount);
            $this->db->bind(':aid', $bankAccountId);
            $this->db->execute();

            // 4. Update Cheque record
            $this->db->query("UPDATE cheques 
                              SET status = 'Cleared', 
                                  bank_account_id = :bid, 
                                  cleared_date = :cdate, 
                                  cleared_by = :uid, 
                                  clearance_journal_id = :jid, 
                                  clearance_notes = :notes 
                              WHERE id = :id");
            $this->db->bind(':bid', $bankAccountId);
            $this->db->bind(':cdate', $clearanceDate);
            $this->db->bind(':uid', intval($userId));
            $this->db->bind(':jid', $jeId);
            $this->db->bind(':notes', !empty($notes) ? $notes : null);
            $this->db->bind(':id', intval($chequeId));
            $this->db->execute();

            $this->db->commit();
            return ['success' => true, 'message' => "Cheque #{$chequeNum} successfully cleared and deducted from {$bankAcc->account_name}."];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("clearIssuedCheque error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error clearing cheque: ' . $e->getMessage()];
        }
    }

    public function revertIssuedCheque($chequeId, $userId = 1) {
        try {
            $this->db->beginTransaction();

            $this->db->query("SELECT * FROM cheques WHERE id = :id FOR UPDATE");
            $this->db->bind(':id', intval($chequeId));
            $chk = $this->db->single();

            if (!$chk || $chk->status !== 'Cleared') {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Cheque is not in Cleared status.'];
            }

            // If it has a clearance journal entry, reverse accounting
            if (!empty($chk->clearance_journal_id)) {
                $jeId = intval($chk->clearance_journal_id);

                $this->db->query("SELECT t.*, coa.account_type 
                                  FROM transactions t 
                                  JOIN chart_of_accounts coa ON t.account_id = coa.id 
                                  WHERE t.journal_entry_id = :jid");
                $this->db->bind(':jid', $jeId);
                $txns = $this->db->resultSet() ?: [];

                foreach ($txns as $tx) {
                    $accType = $tx->account_type;
                    $aid = $tx->account_id;
                    $deb = floatval($tx->debit);
                    $cred = floatval($tx->credit);

                    if (in_array($accType, ['Asset', 'Expense'])) {
                        $this->db->query("UPDATE chart_of_accounts SET balance = balance - :deb + :cred WHERE id = :aid");
                    } else {
                        $this->db->query("UPDATE chart_of_accounts SET balance = balance + :deb - :cred WHERE id = :aid");
                    }
                    $this->db->bind(':deb', $deb);
                    $this->db->bind(':cred', $cred);
                    $this->db->bind(':aid', $aid);
                    $this->db->execute();
                }

                $this->db->query("DELETE FROM transactions WHERE journal_entry_id = :jid");
                $this->db->bind(':jid', $jeId);
                $this->db->execute();

                $this->db->query("DELETE FROM journal_entries WHERE id = :jid");
                $this->db->bind(':jid', $jeId);
                $this->db->execute();
            }

            // Restore status
            $restoredStatus = (!empty($chk->customer_id) && (!empty($chk->vendor_id) || !empty($chk->payee_name))) ? 'Endorsed' : 'Pending';

            $this->db->query("UPDATE cheques 
                              SET status = :st, 
                                  cleared_date = NULL, 
                                  cleared_by = NULL, 
                                  clearance_journal_id = NULL, 
                                  clearance_notes = NULL 
                              WHERE id = :id");
            $this->db->bind(':st', $restoredStatus);
            $this->db->bind(':id', intval($chequeId));
            $this->db->execute();

            $this->db->commit();
            return ['success' => true, 'message' => "Clearance reverted successfully. Cheque #{$chk->cheque_number} is now {$restoredStatus}."];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("revertIssuedCheque error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error reverting cheque clearance: ' . $e->getMessage()];
        }
    }

    private function postChequeJournalEntry($chequeId, $newStatus, $oldStatus) {
        $this->db->query("SELECT * FROM cheques WHERE id = :id");
        $this->db->bind(':id', $chequeId);
        $chk = $this->db->single();
        if (!$chk) return;
        
        $amount = floatval($chk->amount);
        $chequeNum = $chk->cheque_number;
        $bankingDate = $chk->banking_date;
        $uid = $chk->created_by ?: ($_SESSION['user_id'] ?? 1);
        
        // Account IDs
        $chequeInHandId = $this->getAccountIdByCode('1010');
        $arAccountId = $this->getAccountIdByCode('1200');
        $apAccountId = $this->getAccountIdByCode('2000') ?: $this->getAccountIdByCode('2100');
        $outstandingChequesId = $this->getOutstandingChequesAccountId();
        
        if ($oldStatus === null && $newStatus === 'Pending') {
            // Cheque Created
            if ($chk->customer_id) {
                // Received Customer Cheque: Debit Cheques in Hand, Credit AR
                $ref = 'CQ-RCV-' . $chequeNum;
                $desc = "Received Customer Cheque #" . $chequeNum;
                $lines = [
                    ['account_id' => $chequeInHandId, 'debit' => $amount, 'credit' => 0.00, 'description' => $desc],
                    ['account_id' => $arAccountId, 'debit' => 0.00, 'credit' => $amount, 'description' => $desc]
                ];
                $this->insertJournalEntry($bankingDate, $ref, $desc, $lines, $uid);
            } elseif (($chk->vendor_id || $chk->service_provider_id) && $chk->bank_account_id) {
                // Issued Supplier Cheque: Debit AP, Credit Outstanding Cheques (Issued) [2050]
                // (Bank balance is NOT deducted until Cheque Clears!)
                $ref = 'CQ-ISS-' . $chequeNum;
                $desc = "Issued Supplier Cheque #" . $chequeNum;
                $lines = [
                    ['account_id' => $apAccountId, 'debit' => $amount, 'credit' => 0.00, 'description' => $desc],
                    ['account_id' => $outstandingChequesId, 'debit' => 0.00, 'credit' => $amount, 'description' => $desc]
                ];
                $this->insertJournalEntry($bankingDate, $ref, $desc, $lines, $uid);
            }
        }
        
        // Status Transition: -> Cleared (direct manual clear)
        if ($newStatus === 'Cleared' && $oldStatus !== 'Cleared') {
            if ($chk->customer_id && $chk->bank_account_id) {
                // Manually clear customer cheque directly to a bank account
                $ref = 'CQ-CLR-' . $chequeNum;
                $desc = "Cleared Customer Cheque #" . $chequeNum;
                $creditAccount = ($oldStatus === 'Deposited' || $oldStatus === 'Sent to Bank') 
                    ? ($this->getAccountIdByCode('1040') ?: $chequeInHandId) 
                    : $chequeInHandId;
                $lines = [
                    ['account_id' => $chk->bank_account_id, 'debit' => $amount, 'credit' => 0.00, 'description' => $desc],
                    ['account_id' => $creditAccount, 'debit' => 0.00, 'credit' => $amount, 'description' => $desc]
                ];
                $this->insertJournalEntry($bankingDate, $ref, $desc, $lines, $uid);
            } elseif (($chk->type === 'Issued' || $chk->vendor_id || $chk->service_provider_id || $chk->supplier_payment_id) && $chk->bank_account_id && $oldStatus !== 'Endorsed') {
                // Manually clear supplier cheque: Deduct from selected Bank Account!
                $ref = 'CQ-CLR-' . $chequeNum;
                $desc = "Cleared Supplier Cheque #" . $chequeNum;
                $lines = [
                    ['account_id' => $outstandingChequesId, 'debit' => $amount, 'credit' => 0.00, 'description' => $desc],
                    ['account_id' => $chk->bank_account_id, 'debit' => 0.00, 'credit' => $amount, 'description' => $desc]
                ];
                $this->insertJournalEntry($bankingDate, $ref, $desc, $lines, $uid);
            }
        }
        
        // Status Transition: -> Bounced / Returned / Cancelled
        if (in_array($newStatus, ['Bounced', 'Returned', 'Cancelled']) && $oldStatus !== $newStatus) {
            if ($chk->customer_id) {
                // Bounced customer cheque: Re-open Accounts Receivable
                $ref = 'CQ-BNC-' . $chequeNum;
                $desc = "Bounced Customer Cheque #" . $chequeNum;
                $creditAccount = ($oldStatus === 'Deposited' || $oldStatus === 'Sent to Bank') 
                    ? ($this->getAccountIdByCode('1040') ?: $chequeInHandId) 
                    : $chequeInHandId;
                $lines = [
                    ['account_id' => $arAccountId, 'debit' => $amount, 'credit' => 0.00, 'description' => $desc],
                    ['account_id' => $creditAccount, 'debit' => 0.00, 'credit' => $amount, 'description' => $desc]
                ];
                
                if (isset($chk->return_charge) && $chk->return_charge > 0 && !empty($chk->return_charge_account_id)) {
                    $chargeAmt = floatval($chk->return_charge);
                    $chargeAcc = intval($chk->return_charge_account_id);
                    // Record the return charge: Debit the selected expense account, Credit the Bank/Cheque in Hand
                    $lines[] = ['account_id' => $chargeAcc, 'debit' => $chargeAmt, 'credit' => 0.00, 'description' => "Return Charge for Cheque #" . $chequeNum];
                    $lines[] = ['account_id' => $creditAccount, 'debit' => 0.00, 'credit' => $chargeAmt, 'description' => "Return Charge for Cheque #" . $chequeNum];
                }
                
                $this->insertJournalEntry($bankingDate, $ref, $desc, $lines, $uid);

                // Reverse corresponding active customer payment and re-open invoices to Unpaid
                $this->db->query("SELECT p.* FROM customer_payments p
                                  WHERE p.customer_id = :cid AND p.amount = :amt AND p.payment_method = 'Cheque' AND p.status = 'Active' 
                                  ORDER BY p.id DESC LIMIT 1");
                $this->db->bind(':cid', $chk->customer_id);
                $this->db->bind(':amt', $amount);
                $payment = $this->db->single();

                if ($payment) {
                    $this->db->query("UPDATE customer_payments SET status = 'Bounced', reversed_by = :uid, reversed_at = CURRENT_TIMESTAMP() WHERE id = :pid");
                    $this->db->bind(':uid', intval($uid));
                    $this->db->bind(':pid', intval($payment->id));
                    $this->db->execute();

                    $this->db->query("SELECT invoice_id FROM customer_payment_allocations WHERE customer_payment_id = :pid AND is_reversed = 0");
                    $this->db->bind(':pid', intval($payment->id));
                    $allocs = $this->db->resultSet() ?: [];

                    $this->db->query("UPDATE customer_payment_allocations SET is_reversed = 1, reversed_by = :uid, reversed_at = CURRENT_TIMESTAMP() WHERE customer_payment_id = :pid");
                    $this->db->bind(':uid', intval($uid));
                    $this->db->bind(':pid', intval($payment->id));
                    $this->db->execute();

                    foreach ($allocs as $a) {
                        $this->db->query("UPDATE invoices SET status = 'Unpaid' WHERE id = :id");
                        $this->db->bind(':id', intval($a->invoice_id));
                        $this->db->execute();
                    }
                }
            } elseif ($chk->vendor_id || $chk->service_provider_id || $chk->supplier_payment_id) {
                // Bounced / Cancelled supplier cheque
                $ref = 'CQ-CAN-' . $chequeNum;
                $desc = "Cancelled Supplier Cheque #" . $chequeNum;

                if ($oldStatus === 'Cleared' && $chk->bank_account_id) {
                    // Was cleared, so bank balance was deducted: Restore bank balance and re-open AP
                    $lines = [
                        ['account_id' => $chk->bank_account_id, 'debit' => $amount, 'credit' => 0.00, 'description' => $desc],
                        ['account_id' => $apAccountId, 'debit' => 0.00, 'credit' => $amount, 'description' => $desc]
                    ];
                } else {
                    // Was pending: Clear Outstanding Cheques (2050) liability and re-open AP
                    $lines = [
                        ['account_id' => $outstandingChequesId, 'debit' => $amount, 'credit' => 0.00, 'description' => $desc],
                        ['account_id' => $apAccountId, 'debit' => 0.00, 'credit' => $amount, 'description' => $desc]
                    ];
                }
                $this->insertJournalEntry($bankingDate, $ref, $desc, $lines, $uid);

                // Reverse corresponding active supplier payment and re-open GRNs
                $paymentId = $chk->supplier_payment_id;
                if (!$paymentId) {
                    $this->db->query("SELECT id FROM supplier_payments WHERE (vendor_id = :vid OR service_provider_id = :spid) AND amount = :amt AND payment_method = 'Cheque' AND status = 'Active' ORDER BY id DESC LIMIT 1");
                    $this->db->bind(':vid', $chk->vendor_id);
                    $this->db->bind(':spid', $chk->service_provider_id);
                    $this->db->bind(':amt', $amount);
                    $pRow = $this->db->single();
                    if ($pRow) $paymentId = $pRow->id;
                }

                if ($paymentId) {
                    $this->db->query("UPDATE supplier_payments SET status = 'Reversed', reversed_by = :uid, reversed_at = CURRENT_TIMESTAMP() WHERE id = :pid");
                    $this->db->bind(':uid', intval($uid));
                    $this->db->bind(':pid', intval($paymentId));
                    $this->db->execute();

                    $this->db->query("SELECT grn_id FROM supplier_payment_allocations WHERE supplier_payment_id = :pid AND is_reversed = 0");
                    $this->db->bind(':pid', intval($paymentId));
                    $allocs = $this->db->resultSet() ?: [];

                    $this->db->query("UPDATE supplier_payment_allocations SET is_reversed = 1, reversed_by = :uid, reversed_at = CURRENT_TIMESTAMP() WHERE supplier_payment_id = :pid");
                    $this->db->bind(':uid', intval($uid));
                    $this->db->bind(':pid', intval($paymentId));
                    $this->db->execute();

                    foreach ($allocs as $a) {
                        $this->db->query("SELECT total_amount FROM goods_receipt_notes WHERE id = :id");
                        $this->db->bind(':id', $a->grn_id);
                        $grn = $this->db->single();
                        if ($grn) {
                            $this->db->query("SELECT COALESCE(SUM(amount), 0) as paid FROM supplier_payment_allocations WHERE grn_id = :id AND is_reversed = 0");
                            $this->db->bind(':id', $a->grn_id);
                            $allocRow = $this->db->single();
                            $paid = $allocRow ? floatval($allocRow->paid) : 0.0;
                            $tot = floatval($grn->total_amount);
                            $st = ($paid >= $tot - 0.01 && $tot > 0) ? 'Paid' : (($paid > 0.01) ? 'Partially Paid' : 'Unpaid');
                            $this->db->query("UPDATE goods_receipt_notes SET status = :st WHERE id = :id");
                            $this->db->bind(':st', $st);
                            $this->db->bind(':id', $a->grn_id);
                            $this->db->execute();
                        }
                    }
                }
            }
        }
    }
    
    private function getAccountIdByCode($code) {
        $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = :code LIMIT 1");
        $this->db->bind(':code', $code);
        $row = $this->db->single();
        return $row ? intval($row->id) : null;
    }

    private function getOutstandingChequesAccountId() {
        $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = '2050' LIMIT 1");
        $row = $this->db->single();
        if ($row) return intval($row->id);

        $this->db->query("INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, balance, is_active) 
                          VALUES ('2050', 'Outstanding Cheques (Issued)', 'Liability', 'Current Liability', 0.00, 1)");
        $this->db->execute();
        return intval($this->db->lastInsertId());
    }
    
    private function insertJournalEntry($date, $ref, $desc, $lines, $uid) {
        $this->db->query("INSERT INTO journal_entries (entry_date, reference, description, created_by, status) 
                          VALUES (:date, :ref, :desc, :uid, 'Posted')");
        $this->db->bind(':date', $date);
        $this->db->bind(':ref', $ref);
        $this->db->bind(':desc', $desc);
        $this->db->bind(':uid', $uid);
        $this->db->execute();
        $jeId = $this->db->lastInsertId();
        
        foreach ($lines as $line) {
            $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description, is_cleared) 
                              VALUES (:jid, :aid, :deb, :cred, :desc, 1)");
            $this->db->bind(':jid', $jeId);
            $this->db->bind(':aid', $line['account_id']);
            $this->db->bind(':deb', $line['debit']);
            $this->db->bind(':cred', $line['credit']);
            $this->db->bind(':desc', $line['description']);
            $this->db->execute();
            
            // Update ledger account balance
            $this->db->query("SELECT account_type FROM chart_of_accounts WHERE id = :id FOR UPDATE");
            $this->db->bind(':id', $line['account_id']);
            $account = $this->db->single();
            
            $balanceUpdateSql = "UPDATE chart_of_accounts SET balance = balance ";
            if ($account && in_array($account->account_type, ['Asset', 'Expense'])) {
                $balanceUpdateSql .= "+ :debit - :credit ";
            } else {
                $balanceUpdateSql .= "- :debit + :credit ";
            }
            $balanceUpdateSql .= "WHERE id = :id";
            
            $this->db->query($balanceUpdateSql);
            $this->db->bind(':debit', $line['debit']);
            $this->db->bind(':credit', $line['credit']);
            $this->db->bind(':id', $line['account_id']);
            $this->db->execute();
        }
    }
}
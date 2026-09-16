<?php
declare(strict_types=1);

require_once __DIR__ . '/AuditLog.php';

class JournalEntry {
    private Database $db;
    private AuditLog $auditLog;

    public function __construct() {
        $this->db = new Database();
        $this->auditLog = new AuditLog();
    }

    public function getAllEntries(?int $limit = null, ?int $offset = null, array $filters = []): array {
        $sql = "SELECT je.*, u.username 
                FROM journal_entries je 
                LEFT JOIN users u ON je.created_by = u.id 
                WHERE je.is_manual = 1";
        
        $params = [];
        if (!empty($filters['search'])) {
            $sql .= " AND (je.reference LIKE :search OR je.description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status']) && $filters['status'] !== 'All') {
            $sql .= " AND je.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND je.entry_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND je.entry_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }
        
        $sql .= " ORDER BY je.entry_date DESC, je.id DESC";
        
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $this->db->query($sql);
        
        foreach ($params as $key => $val) {
            $this->db->bind($key, $val);
        }
        
        if ($limit !== null && $offset !== null) {
            $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
            $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        }
        
        return $this->db->resultSet() ?: [];
    }

    public function getEntriesCount(array $filters = []): int {
        $sql = "SELECT COUNT(*) as total FROM journal_entries je WHERE je.is_manual = 1";
        
        $params = [];
        if (!empty($filters['search'])) {
            $sql .= " AND (je.reference LIKE :search OR je.description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status']) && $filters['status'] !== 'All') {
            $sql .= " AND je.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND je.entry_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND je.entry_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }
        
        $this->db->query($sql);
        foreach ($params as $key => $val) {
            $this->db->bind($key, $val);
        }
        
        $row = $this->db->single();
        return $row ? (int)$row->total : 0;
    }

    public function postEntry(string $date, string $reference, string $description, array $lines, int $userId, bool $isManual = false): string|bool {
        try {
            // Check if period is closed/locked
            $this->db->query("SELECT COUNT(*) as cnt FROM financial_years WHERE :entry_date BETWEEN start_date AND end_date");
            $this->db->bind(':entry_date', $date);
            $res = $this->db->single();
            if ($res && $res->cnt > 0) {
                return 'Accounting Error: The period containing date ' . $date . ' is closed and locked.';
            }

            $inTransaction = $this->db->inTransaction();
            if (!$inTransaction) {
                $this->db->beginTransaction();
            }

            // Duplicate reference check under transaction lock
            if (!empty($reference)) {
                $this->db->query("SELECT COUNT(*) as cnt FROM journal_entries WHERE reference = :ref FOR UPDATE");
                $this->db->bind(':ref', $reference);
                $dupRes = $this->db->single();
                if ($dupRes && $dupRes->cnt > 0) {
                    if (!$inTransaction) {
                        $this->db->rollBack();
                    }
                    return 'Accounting Error: Journal Entry Reference "' . htmlspecialchars($reference) . '" already exists.';
                }
            }

            $this->db->query("INSERT INTO journal_entries (entry_date, reference, description, created_by, is_manual) 
                              VALUES (:entry_date, :reference, :description, :created_by, :is_manual)");
            $this->db->bind(':entry_date', $date);
            $this->db->bind(':reference', $reference);
            $this->db->bind(':description', $description);
            $this->db->bind(':created_by', $userId);
            $this->db->bind(':is_manual', $isManual ? 1 : 0);
            $this->db->execute();
            
            $journalId = $this->db->lastInsertId();

            foreach ($lines as $line) {
                $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description, entity_type, entity_id) 
                                  VALUES (:journal_id, :account_id, :debit, :credit, :description, :entity_type, :entity_id)");
                $this->db->bind(':journal_id', $journalId);
                $this->db->bind(':account_id', $line['account_id']);
                $this->db->bind(':debit', $line['debit']);
                $this->db->bind(':credit', $line['credit']);
                $this->db->bind(':description', !empty($line['description']) ? $line['description'] : null);
                $this->db->bind(':entity_type', !empty($line['entity_type']) ? $line['entity_type'] : null);
                $this->db->bind(':entity_id', !empty($line['entity_id']) ? $line['entity_id'] : null);
                $this->db->execute();

                $this->db->query("SELECT account_type FROM chart_of_accounts WHERE id = :id FOR UPDATE");
                $this->db->bind(':id', $line['account_id']);
                $account = $this->db->single();

                $balanceUpdateSql = "UPDATE chart_of_accounts SET balance = balance ";
                if (in_array($account->account_type, [COA_TYPE_ASSET, COA_TYPE_EXPENSE])) {
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

            // Sync first non-null entity_type and entity_id to journal_entries header
            $firstEntityType = null;
            $firstEntityId = null;
            foreach ($lines as $l) {
                if (!empty($l['entity_type']) && !empty($l['entity_id'])) {
                    $firstEntityType = $l['entity_type'];
                    $firstEntityId = intval($l['entity_id']);
                    break;
                }
            }
            if ($firstEntityType && $firstEntityId) {
                $this->db->query("UPDATE journal_entries SET entity_type = :et, entity_id = :eid WHERE id = :id");
                $this->db->bind(':et', $firstEntityType);
                $this->db->bind(':eid', $firstEntityId);
                $this->db->bind(':id', $journalId);
                $this->db->execute();
            }

            if (!$inTransaction) {
                $this->db->commit();
            }

            try {
                $totalAmount = 0;
                foreach ($lines as $l) {
                    $totalAmount += floatval($l['debit'] ?? 0);
                }
                $refText = !empty($reference) ? "Ref: {$reference}" : "ID: #{$journalId}";
                $auditDesc = "Posted Journal Entry ({$refText}, Date: {$date}, Lines: " . count($lines) . ", Total Amount: Rs. " . number_format($totalAmount, 2) . ") - {$description}";
                $newSnapshot = [
                    'id' => $journalId,
                    'entry_date' => $date,
                    'reference' => $reference,
                    'description' => $description,
                    'is_manual' => $isManual ? 1 : 0,
                    'lines' => $lines
                ];
                $this->auditLog->logAction($userId, 'POST', 'Journal Entry', $auditDesc, (int)$journalId, null, $newSnapshot);
            } catch (Throwable $t) {}

            return true;
        } catch (Exception $e) {
            if (!$inTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return 'Database Error: Failed to post entry. ' . $e->getMessage();
        }
    }

    public function closeFinancialYear(string $startDate, string $endDate, int $userId, int $retainedEarningsAccId): string|bool {
        try {
            $this->db->beginTransaction();

            // 1. Calculate P&L balances for unclosed entries up to $endDate
            $this->db->query("SELECT t.account_id, c.account_type, SUM(t.debit) as total_debit, SUM(t.credit) as total_credit 
                              FROM transactions t
                              JOIN journal_entries je ON t.journal_entry_id = je.id
                              JOIN chart_of_accounts c ON t.account_id = c.id
                              WHERE je.is_closed = 0 AND je.entry_date <= :end_date 
                                AND je.status != 'Draft' AND je.status != 'Voided'
                                AND c.account_type IN ('Revenue', 'Expense')
                              GROUP BY t.account_id");
            $this->db->bind(':end_date', $endDate);
            $plAccounts = $this->db->resultSet();

            if(empty($plAccounts)) {
                $this->db->rollBack();
                return "No unclosed P&L transactions found for this period.";
            }

            // 2. Create Closing Journal Entry Header
            $desc = "Financial Year Closing Entry as of " . $endDate;
            $ref = "YE-CLOSE-" . date('Y', strtotime($endDate));
            $this->db->query("INSERT INTO journal_entries (entry_date, reference, description, created_by, status, is_closed) 
                              VALUES (:date, :ref, :desc, :user, 'Posted', 1)"); // Closing entry is automatically locked
            $this->db->bind(':date', $endDate);
            $this->db->bind(':ref', $ref);
            $this->db->bind(':desc', $desc);
            $this->db->bind(':user', $userId);
            $this->db->execute();
            $journalId = $this->db->lastInsertId();

            $netIncome = 0;
            
            // 3. Post zeroing lines for Revenue & Expense
            foreach($plAccounts as $acc) {
                if ($acc->account_type === COA_TYPE_REVENUE) {
                    // Net Credit Balance
                    $balance = floatval($acc->total_credit) - floatval($acc->total_debit);
                    if (round($balance, 2) == 0) continue;
                    
                    $netIncome += $balance; // Revenue adds to Net Income
                    
                    // To zero out Credit balance: Debit it
                    $debit = $balance > 0 ? $balance : 0;
                    $credit = $balance < 0 ? abs($balance) : 0;
                } else { // Expense
                    // Net Debit Balance
                    $balance = floatval($acc->total_debit) - floatval($acc->total_credit);
                    if (round($balance, 2) == 0) continue;
                    
                    $netIncome -= $balance; // Expense subtracts from Net Income
                    
                    // To zero out Debit balance: Credit it
                    $debit = $balance < 0 ? abs($balance) : 0;
                    $credit = $balance > 0 ? $balance : 0;
                }

                $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description) VALUES (:jid, :aid, :deb, :cred, :desc)");
                $this->db->bind(':jid', $journalId);
                $this->db->bind(':aid', $acc->account_id);
                $this->db->bind(':deb', $debit);
                $this->db->bind(':cred', $credit);
                $this->db->bind(':desc', "Year-End Closing - Zero out Account Balance");
                $this->db->execute();

                // Zero out the COA balance
                $this->db->query("UPDATE chart_of_accounts SET balance = 0 WHERE id = :id");
                $this->db->bind(':id', $acc->account_id);
                $this->db->execute();
            }

            // 4. Post offsetting entry to Retained Earnings
            if (round($netIncome, 2) != 0) {
                $reDebit = $netIncome < 0 ? abs($netIncome) : 0;
                $reCredit = $netIncome > 0 ? $netIncome : 0;

                $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description) VALUES (:jid, :aid, :deb, :cred, :desc)");
                $this->db->bind(':jid', $journalId);
                $this->db->bind(':aid', $retainedEarningsAccId);
                $this->db->bind(':deb', $reDebit);
                $this->db->bind(':cred', $reCredit);
                $this->db->bind(':desc', "Year-End Closing - Transfer Net Income to Retained Earnings");
                $this->db->execute();

                // ACCT-2 FIX: Use account-type-aware balance update for Retained Earnings
                $this->db->updateAccountBalance($retainedEarningsAccId, $reDebit, $reCredit);
            }

            // 5. Lock all old journal entries (excluding drafts)
            $this->db->query("UPDATE journal_entries SET is_closed = 1 WHERE entry_date <= :end_date AND is_closed = 0 AND status != 'Draft'");
            $this->db->bind(':end_date', $endDate);
            $this->db->execute();

            // 6. Record financial year in tracking table
            $this->db->query("INSERT INTO financial_years (year_name, start_date, end_date, closed_by) VALUES (:name, :start_date, :end_date, :uid)");
            $this->db->bind(':name', 'FY ' . date('Y', strtotime($startDate)) . '-' . date('Y', strtotime($endDate)));
            $this->db->bind(':start_date', $startDate);
            $this->db->bind(':end_date', $endDate);
            $this->db->bind(':uid', $userId);
            $this->db->execute();

            $this->db->commit();

            try {
                $auditDesc = "Closed Financial Year ({$startDate} to {$endDate}) and posted Year-End closing entry #{$journalId}";
                $this->auditLog->logAction($userId, 'CLOSE_YEAR', 'Journal Entry', $auditDesc, (int)$journalId, null, [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'retained_earnings_account_id' => $retainedEarningsAccId,
                    'closing_journal_id' => $journalId,
                    'net_income' => $netIncome
                ]);
            } catch (Throwable $t) {}

            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return "Database Error: " . $e->getMessage();
        }
    }

    public function voidEntry(int $id): bool {
        try {
            // 1. Fetch the journal entry
            $this->db->query("SELECT * FROM journal_entries WHERE id = :id");
            $this->db->bind(':id', $id);
            $entry = $this->db->single();
            if (!$entry) return false;
            if ($entry->status === 'Voided') return true; // Already voided
            if ($entry->is_closed) return false; // Cannot void a closed financial year entry

            $this->db->beginTransaction();

            // 2. Fetch all transaction lines for this journal entry
            $this->db->query("SELECT * FROM transactions WHERE journal_entry_id = :id");
            $this->db->bind(':id', $id);
            $lines = $this->db->resultSet();

            // 3. Revert impact on COA balances
            if ($entry->status === 'Posted') {
                foreach ($lines as $line) {
                    $this->db->query("SELECT account_type FROM chart_of_accounts WHERE id = :aid FOR UPDATE");
                    $this->db->bind(':aid', $line->account_id);
                    $account = $this->db->single();

                    $balanceUpdateSql = "UPDATE chart_of_accounts SET balance = balance ";
                    if (in_array($account->account_type, [COA_TYPE_ASSET, COA_TYPE_EXPENSE])) {
                        $balanceUpdateSql .= "- :debit + :credit ";
                    } else {
                        $balanceUpdateSql .= "+ :debit - :credit ";
                    }
                    $balanceUpdateSql .= "WHERE id = :aid";

                    $this->db->query($balanceUpdateSql);
                    $this->db->bind(':debit', $line->debit);
                    $this->db->bind(':credit', $line->credit);
                    $this->db->bind(':aid', $line->account_id);
                    $this->db->execute();
                }
            }

            // 4. Update journal entry status to Voided
            $this->db->query("UPDATE journal_entries SET status = 'Voided' WHERE id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            // 5. Reset transaction clearing flags so voided entries don't linger in reconciliation
            $this->db->query("UPDATE transactions SET is_cleared = 0 WHERE journal_entry_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            $userId = $_SESSION['user_id'] ?? null;

            // 6. Cascade void status to linked customer payments
            $this->db->query("SELECT id FROM customer_payments WHERE journal_entry_id = :id");
            $this->db->bind(':id', $id);
            $custPmts = $this->db->resultSet() ?: [];
            foreach ($custPmts as $cp) {
                $this->db->query("UPDATE customer_payments SET status = 'Reversed', reversed_by = :uid, reversed_at = CURRENT_TIMESTAMP() WHERE id = :cpid");
                $this->db->bind(':uid', $userId);
                $this->db->bind(':cpid', $cp->id);
                $this->db->execute();

                // Reverse allocations
                $this->db->query("SELECT invoice_id FROM customer_payment_allocations WHERE customer_payment_id = :cpid AND is_reversed = 0");
                $this->db->bind(':cpid', $cp->id);
                $allocs = $this->db->resultSet() ?: [];

                $this->db->query("UPDATE customer_payment_allocations SET is_reversed = 1, reversed_by = :uid, reversed_at = CURRENT_TIMESTAMP() WHERE customer_payment_id = :cpid AND is_reversed = 0");
                $this->db->bind(':uid', $userId);
                $this->db->bind(':cpid', $cp->id);
                $this->db->execute();

                foreach ($allocs as $al) {
                    $this->db->query("UPDATE invoices SET status = 'Unpaid' WHERE id = :iid AND status = 'Paid'");
                    $this->db->bind(':iid', $al->invoice_id);
                    $this->db->execute();
                }
            }

            // 7. Cascade void status to linked supplier payments
            $this->db->query("SELECT id FROM supplier_payments WHERE journal_entry_id = :id");
            $this->db->bind(':id', $id);
            $suppPmts = $this->db->resultSet() ?: [];
            foreach ($suppPmts as $sp) {
                $this->db->query("UPDATE supplier_payments SET status = 'Reversed', reversed_by = :uid, reversed_at = CURRENT_TIMESTAMP() WHERE id = :spid");
                $this->db->bind(':uid', $userId);
                $this->db->bind(':spid', $sp->id);
                $this->db->execute();

                // Reverse allocations
                $this->db->query("SELECT grn_id FROM supplier_payment_allocations WHERE supplier_payment_id = :spid AND is_reversed = 0");
                $this->db->bind(':spid', $sp->id);
                $sAllocs = $this->db->resultSet() ?: [];

                $this->db->query("UPDATE supplier_payment_allocations SET is_reversed = 1, reversed_by = :uid, reversed_at = CURRENT_TIMESTAMP() WHERE supplier_payment_id = :spid AND is_reversed = 0");
                $this->db->bind(':uid', $userId);
                $this->db->bind(':spid', $sp->id);
                $this->db->execute();

                foreach ($sAllocs as $sal) {
                    $this->db->query("UPDATE goods_receipt_notes SET payment_status = 'Unpaid' WHERE id = :gid AND payment_status = 'Paid'");
                    $this->db->bind(':gid', $sal->grn_id);
                    $this->db->execute();
                }
            }

            // 8. Cascade void to petty cash transactions
            $this->db->query("UPDATE petty_cash_transactions SET status = 'Voided', updated_at = CURRENT_TIMESTAMP() WHERE journal_entry_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            // 10. Cascade void to cheques
            $this->db->query("UPDATE cheques SET status = 'Voided' WHERE supplier_payment_id IN (SELECT id FROM supplier_payments WHERE journal_entry_id = :id)");
            $this->db->bind(':id', $id);
            $this->db->execute();

            // 11. Cascade to loan repayments/disbursements if applicable
            if (!empty($entry->reference)) {
                if (strpos($entry->reference, 'LOAN-REP-') === 0) {
                    $repId = intval(substr($entry->reference, 9));
                    if ($repId > 0) {
                        $this->db->query("SELECT loan_id FROM loan_repayments WHERE id = :repid");
                        $this->db->bind(':repid', $repId);
                        $repRow = $this->db->single();

                        $this->db->query("DELETE FROM loan_repayments WHERE id = :repid");
                        $this->db->bind(':repid', $repId);
                        $this->db->execute();

                        if ($repRow && $repRow->loan_id) {
                            $this->db->query("UPDATE loans SET status = 'Active' WHERE id = :lid AND status = 'Closed'");
                            $this->db->bind(':lid', $repRow->loan_id);
                            $this->db->execute();
                        }
                    }
                } elseif (strpos($entry->reference, 'LOAN-DISB-') === 0) {
                    $loanId = intval(substr($entry->reference, 10));
                    if ($loanId > 0) {
                        $this->db->query("UPDATE loans SET status = 'Pending' WHERE id = :lid");
                        $this->db->bind(':lid', $loanId);
                        $this->db->execute();
                    }
                }
            }

            $this->db->commit();

            try {
                $userId = $_SESSION['user_id'] ?? null;
                $refText = !empty($entry->reference) ? "Ref: {$entry->reference}" : "ID: #{$id}";
                $auditDesc = "Voided Journal Entry #{$id} ({$refText}, Date: {$entry->entry_date}) - {$entry->description}";
                $oldSnapshot = [
                    'id' => $id,
                    'entry_date' => $entry->entry_date,
                    'reference' => $entry->reference,
                    'description' => $entry->description,
                    'status' => $entry->status,
                    'lines' => $lines
                ];
                $newSnapshot = ['status' => 'Voided'];
                $this->auditLog->logAction($userId, 'VOID', 'Journal Entry', $auditDesc, $id, $oldSnapshot, $newSnapshot);
            } catch (Throwable $t) {}

            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function revertFinancialYearClose(int $fyId): string|bool {
        try {
            // 1. Fetch the financial year record
            $this->db->query("SELECT * FROM financial_years WHERE id = :id");
            $this->db->bind(':id', $fyId);
            $fy = $this->db->single();
            if (!$fy) {
                return 'Financial Year record not found.';
            }

            $this->db->beginTransaction();

            // 2. Find the closing journal entry
            $ref = "YE-CLOSE-" . date('Y', strtotime($fy->end_date));
            $this->db->query("SELECT * FROM journal_entries WHERE entry_date = :end_date AND reference = :ref");
            $this->db->bind(':end_date', $fy->end_date);
            $this->db->bind(':ref', $ref);
            $closingEntry = $this->db->single();

            if ($closingEntry) {
                // Fetch closing entry lines
                $this->db->query("SELECT * FROM transactions WHERE journal_entry_id = :id");
                $this->db->bind(':id', $closingEntry->id);
                $lines = $this->db->resultSet();

                // Revert closing entry transactions impact on COA balances
                foreach ($lines as $line) {
                    $this->db->query("SELECT account_type FROM chart_of_accounts WHERE id = :aid FOR UPDATE");
                    $this->db->bind(':aid', $line->account_id);
                    $account = $this->db->single();

                    $balanceUpdateSql = "UPDATE chart_of_accounts SET balance = balance ";
                    if (in_array($account->account_type, [COA_TYPE_ASSET, COA_TYPE_EXPENSE])) {
                        $balanceUpdateSql .= "- :debit + :credit ";
                    } else {
                        $balanceUpdateSql .= "+ :debit - :credit ";
                    }
                    $balanceUpdateSql .= "WHERE id = :aid";

                    $this->db->query($balanceUpdateSql);
                    $this->db->bind(':debit', $line->debit);
                    $this->db->bind(':credit', $line->credit);
                    $this->db->bind(':aid', $line->account_id);
                    $this->db->execute();
                }

                // Mark closing entry as Voided and not closed
                $this->db->query("UPDATE journal_entries SET status = 'Voided', is_closed = 0 WHERE id = :id");
                $this->db->bind(':id', $closingEntry->id);
                $this->db->execute();
            }

            // 3. Unlock all journal entries in the reversed period
            $this->db->query("UPDATE journal_entries SET is_closed = 0 WHERE entry_date BETWEEN :start_date AND :end_date AND is_closed = 1");
            $this->db->bind(':start_date', $fy->start_date);
            $this->db->bind(':end_date', $fy->end_date);
            $this->db->execute();

            // 4. Delete the financial year record
            $this->db->query("DELETE FROM financial_years WHERE id = :id");
            $this->db->bind(':id', $fyId);
            $this->db->execute();

            $this->db->commit();

            try {
                $userId = $_SESSION['user_id'] ?? null;
                $auditDesc = "Reverted Financial Year Close #{$fyId} ({$fy->start_date} to {$fy->end_date})";
                $this->auditLog->logAction($userId, 'REVERT_YEAR_CLOSE', 'Journal Entry', $auditDesc, $fyId, (array)$fy, null);
            } catch (Throwable $t) {}

            return true;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return "Database Error: " . $e->getMessage();
        }
    }

    public function getClosedFinancialYears(): array {
        $this->db->query("SELECT fy.*, u.username as closed_by_name 
                          FROM financial_years fy 
                          LEFT JOIN users u ON fy.closed_by = u.id 
                          ORDER BY fy.end_date DESC");
        return $this->db->resultSet() ?: [];
    }

    public function getEntryDetails(int $id): array|bool {
        // 1. Fetch the journal entry details
        $this->db->query("SELECT je.*, u.username 
                          FROM journal_entries je 
                          LEFT JOIN users u ON je.created_by = u.id 
                          WHERE je.id = :id");
        $this->db->bind(':id', $id);
        $entry = $this->db->single();
        if (!$entry) return false;

        // 2. Fetch the transaction lines with account codes and names, and entity names
        $this->db->query("SELECT t.*, c.account_code, c.account_name, 
                                 cu.name as customer_name, v.name as vendor_name
                          FROM transactions t 
                          JOIN chart_of_accounts c ON t.account_id = c.id 
                          LEFT JOIN customers cu ON t.entity_type = 'Customer' AND t.entity_id = cu.id
                          LEFT JOIN vendors v ON t.entity_type = 'Vendor' AND t.entity_id = v.id
                          WHERE t.journal_entry_id = :id");
        $this->db->bind(':id', $id);
        $lines = $this->db->resultSet();

        return [
            'entry' => $entry,
            'lines' => $lines ?: []
        ];
    }
}
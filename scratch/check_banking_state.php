<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Database.php';

$db = new Database();

// Check bank accounts in chart_of_accounts
$db->query("SELECT id, account_code, account_name, account_type, balance FROM chart_of_accounts WHERE account_code LIKE '10%' OR account_code LIKE '16%' OR account_name LIKE '%Bank%'");
$banks = $db->resultSet();
echo "=== BANK ACCOUNTS ===\n";
foreach ($banks as $b) {
    echo "ID: {$b->id} | Code: {$b->account_code} | Name: {$b->account_name} | Type: {$b->account_type} | Bal: {$b->balance}\n";
}

// Check Outstanding Cheques account
$db->query("SELECT id, account_code, account_name, account_type, balance FROM chart_of_accounts WHERE account_code = '2050'");
$oc = $db->single();
echo "\n=== OUTSTANDING CHEQUES (2050) ===\n";
if ($oc) {
    echo "ID: {$oc->id} | Code: {$oc->account_code} | Name: {$oc->account_name} | Bal: {$oc->balance}\n";
} else {
    echo "NOT FOUND!\n";
}

// Check recent issued cheques
$db->query("SELECT ch.id, ch.cheque_number, ch.amount, ch.status, ch.bank_account_id, ch.bank_name, ch.vendor_id, v.name as vendor_name, ch.payee_name 
            FROM cheques ch 
            LEFT JOIN vendors v ON ch.vendor_id = v.id 
            WHERE ch.type = 'Issued' OR ch.vendor_id IS NOT NULL OR ch.bank_account_id IS NOT NULL 
            LIMIT 5");
$issued = $db->resultSet();
echo "\n=== ISSUED CHEQUES SAMPLE ===\n";
print_r($issued);

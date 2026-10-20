<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Database.php';

$db = new Database();
$db->query("DESCRIBE cheques");
$cols = $db->resultSet();
echo "=== CHEQUES TABLE COLUMNS ===\n";
foreach ($cols as $c) {
    echo "{$c->Field} ({$c->Type}) Null:{$c->Null} Default:{$c->Default}\n";
}

$db->query("SELECT id, type, payee_name, customer_id, vendor_id, service_provider_id, cheque_number, amount, status, bank_account_id, supplier_payment_id FROM cheques ORDER BY id DESC LIMIT 10");
$recent = $db->resultSet();
echo "\n=== RECENT CHEQUES ===\n";
print_r($recent);

$db->query("SELECT id, account_code, account_name, account_type, balance FROM chart_of_accounts WHERE account_code IN ('1010', '1040', '1200', '2000', '2050', '2100') OR account_name LIKE '%Cheque%'");
$accounts = $db->resultSet();
echo "\n=== CHEQUE / AP / AR ACCOUNTS ===\n";
print_r($accounts);

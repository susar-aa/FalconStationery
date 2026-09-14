<?php
$conn = new mysqli("localhost", "root", "", "temp_erp");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "SELECT invoice_number, total_amount, customer_name FROM deleted_invoices WHERE delete_reason LIKE '%Route Deletion%' AND deleted_at > '2026-09-13' ORDER BY id;";
$res = $conn->query($sql);
$invoices = [];
while ($row = $res->fetch_assoc()) {
    $invoices[] = $row;
}

$sql_out = "-- PERFECT RESTORE WITH CORRECT VARIATIONS\nSTART TRANSACTION;\n\n";

$inv_list = implode("','", array_column($invoices, "invoice_number"));
$sql_out .= "DELETE FROM `invoice_items` WHERE `invoice_id` IN (SELECT `id` FROM `invoices` WHERE `invoice_number` IN ('$inv_list'));\n";
$sql_out .= "DELETE FROM `invoices` WHERE `invoice_number` IN ('$inv_list');\n\n";

$invoice_id_start = 91000;
$item_id_start = 910000;

foreach ($invoices as $inv) {
    $inv_num = $inv["invoice_number"];
    $total_amount = $inv["total_amount"];
    $customer_name = $inv["customer_name"];
    
    $c_res = $conn->query("SELECT id FROM customers WHERE name = '" . $conn->real_escape_string($customer_name) . "' LIMIT 1");
    $customer_id = ($c_res && $c_res->num_rows > 0) ? $c_res->fetch_assoc()["id"] : 361006;
    
    $inv_id = $invoice_id_start++;
    $sql_out .= "-- Invoice $inv_num\n";
    
    $s_res = $conn->query("SELECT item_id, variation_option_id, quantity_in as quantity, unit_cost FROM stock_ledger WHERE transaction_type = 'Sales Invoice Deletion' AND reference_number = '$inv_num'");
    
    $items = [];
    $calculated_total = 0;
    while ($item = $s_res->fetch_assoc()) {
        $items[] = $item;
    }
    
    $item_sql_lines = "";
    foreach ($items as $item) {
        $i_id = $item["item_id"];
        if (!$i_id) continue;
        $var_id = $item["variation_option_id"];
        $qty = $item["quantity"];
        $cost = $item["unit_cost"];
        
        $name_res = $conn->query("SELECT name, price FROM items WHERE id = $i_id");
        if (!$name_res) die("Error with item_id $i_id: " . $conn->error);
        $name_row = $name_res->fetch_assoc();
        $desc = $name_row["name"];
        $unit_price = $name_row["price"];
        
        if ($var_id) {
            $v_res = $conn->query("SELECT variation_value_id, price FROM item_variation_options WHERE id = $var_id");
            if ($v_res && $v_res->num_rows > 0) {
                $v_row = $v_res->fetch_assoc();
                $v_val_id = $v_row["variation_value_id"];
                $unit_price = $v_row["price"];
                $vv_res = $conn->query("SELECT value_name FROM variation_values WHERE id = $v_val_id");
                if ($vv_res && $vv_res->num_rows > 0) {
                    $desc .= " - " . $vv_res->fetch_assoc()["value_name"];
                }
            }
        }
        
        $line_total = $qty * $unit_price;
        $calculated_total += $line_total;
        
        $var_sql = $var_id ? $var_id : "NULL";
        $desc = $conn->real_escape_string($desc);
        
        $it_id = $item_id_start++;
        $item_sql_lines .= "INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES ($it_id, $inv_id, $i_id, $var_sql, '$desc', $qty, $unit_price, $line_total, $cost);\n";
    }
    
    $discount = $calculated_total - $total_amount;
    if ($discount < 0) $discount = 0;
    
    $inv_date = substr($inv_num, 0, 4) . "-" . substr($inv_num, 4, 2) . "-" . substr($inv_num, 6, 2);
    
    $sql_out .= "INSERT INTO `invoices` (`id`, `invoice_number`, `customer_id`, `global_discount_type`, `global_discount_val`, `total_amount`, `status`, `invoice_date`, `due_date`, `created_by`, `rep_route_id`, `stock_status`) VALUES ($inv_id, '$inv_num', $customer_id, 'Rs', $discount, $total_amount, 'Unpaid', '$inv_date', '$inv_date', 2, 439, 'deducted');\n";
    $sql_out .= $item_sql_lines;
    $sql_out .= "\n";
}

$sql_out .= "COMMIT;\n";
file_put_contents("C:/xampp/htdocs/CURTISS/FalconStationery/perfect_restore.sql", $sql_out);
echo "Done.";
?>

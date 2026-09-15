<?php
$c = file_get_contents('c:\xampp\htdocs\CURTISS\FalconStationery\ERP\app\Views\petty_cash\index.php');
$i = strpos($c, 'id="allocateModal"');
if ($i !== false) {
    $sub = substr($c, $i-5, 30);
    echo $sub . "\n";
    for($j = $i-5; $j < $i+20; $j++) {
        echo ord($c[$j]) . " ";
    }
    echo "\n";
} else {
    echo "NOT FOUND\n";
}

<?php
$c = file_get_contents('c:\xampp\htdocs\CURTISS\FalconStationery\ERP\app\Views\petty_cash\index.php');
// Strip out all php code to pretend the conditions are fully passed
$c = preg_replace('/<\?php.*?\?>/s', '', $c);

libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($c);

$modal = $doc->getElementById('allocateModal');
if ($modal) {
    echo "allocateModal is FOUND in DOM.\n";
} else {
    echo "allocateModal is MISSING from DOM.\n";
    // Check if it exists as part of some text
    if (strpos($c, 'allocateModal') !== false) {
        echo "It is in the string, but DOM parser dropped it!\n";
    }
}

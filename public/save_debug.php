<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $html = file_get_contents('php://input');
    file_put_contents(__DIR__ . '/debug_dom.html', $html);
    echo "Saved";
}

<?php
// execute_restore.php

// 1. Database Connection Variables
// The script will try to read these from your .env file automatically.
// If it fails to read them, you can manually update the 4 lines below with your live server credentials:
$host = '127.0.0.1';
$dbname = 'curtiss_erp';
$user = 'root';
$pass = '';

// Attempt to read the .env file automatically if it exists in the same folder
if (file_exists('.env')) {
    $env = file_get_contents('.env');
    if (preg_match('/DB_HOST=(.*)/', $env, $m)) $host = trim($m[1]);
    if (preg_match('/DB_NAME=(.*)/', $env, $m)) $dbname = trim($m[1]);
    if (preg_match('/DB_USER=(.*)/', $env, $m)) $user = trim($m[1]);
    if (preg_match('/DB_PASS=(.*)/', $env, $m)) $pass = trim($m[1]);
}

echo "<h2>Starting Database Restore...</h2>";

try {
    // 2. Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database successfully.<br>";

    // 3. Read the SQL file
    $sql_file = 'restore_invoices.sql';
    if (!file_exists($sql_file)) {
        die("<b>Error:</b> Could not find <code>$sql_file</code>. Make sure it is uploaded in the exact same folder as this script.");
    }
    
    $sql = file_get_contents($sql_file);
    
    // 4. Execute the SQL queries
    $pdo->exec($sql);
    
    echo "<h1 style='color:green;'>Success!</h1>";
    echo "<p>The deleted route and invoices (including variations) have been successfully restored to the online database.</p>";
    echo "<p style='color:red;'><strong>SECURITY WARNING:</strong> Please immediately delete this file (<code>execute_restore.php</code>) and <code>restore_invoices.sql</code> from your server to prevent unauthorized database modifications.</p>";

} catch (PDOException $e) {
    echo "<h3 style='color:red;'>Database Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>If the connection failed, please edit this file and manually enter your live database Username, Password, and Database Name at the top of the script.</p>";
}
?>

<?php
/**
 * Standalone SQL Importer
 * Bypass Nginx 413 Request Entity Too Large error by importing a file already on the server.
 */

session_start();
set_time_limit(0); // Prevent timeout for large files
ini_set('memory_limit', '-1'); // Allow large files to be processed

$message = '';
$error = '';

// Default DB details (adjust if needed)
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'falcon_erp'; // Update with actual DB name if known

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = $_POST['db_host'] ?? 'localhost';
    $dbUser = $_POST['db_user'] ?? 'root';
    $dbPass = $_POST['db_pass'] ?? '';
    $dbName = $_POST['db_name'] ?? '';
    $sqlFile = $_POST['sql_file'] ?? '';

    if (empty($dbHost) || empty($dbUser) || empty($dbName) || empty($sqlFile)) {
        $error = "Please fill in all required fields and select a file.";
    } elseif (!file_exists($sqlFile)) {
        $error = "The selected file does not exist on the server. Please check the path.";
    } else {
        try {
            $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Read the SQL file
            $sql = file_get_contents($sqlFile);
            if ($sql === false) {
                throw new Exception("Could not read the SQL file.");
            }

            // Execute the SQL
            // For very large files, it's better to execute line by line, but PDO can handle large multi-queries if emulation is on.
            // A safer chunked approach for large SQL dumps:
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
            
            $queries = explode(";\n", $sql);
            $pdo->beginTransaction();
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    $pdo->exec($query);
                }
            }
            $pdo->commit();

            $message = "Database successfully imported from " . htmlspecialchars($sqlFile) . "!";
        } catch (PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "Database Error: " . $e->getMessage();
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Find all .sql files in the current directory and parent directory for easy selection
$availableFiles = [];
$scanDirs = [__DIR__, dirname(__DIR__)];
foreach ($scanDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*.sql');
        if ($files) {
            $availableFiles = array_merge($availableFiles, $files);
        }
    }
}
$availableFiles = array_unique($availableFiles);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Importer Tool</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f2f2f7; color: #1c1c1e; padding: 40px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #007aff; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; }
        .alert.success { background: #e6f9ec; color: #1e4620; border: 1px solid #34c759; }
        .alert.error { background: #fff0ef; color: #6e1a15; border: 1px solid #ff3b30; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; }
        input[type="text"], input[type="password"], select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        button { background: #007aff; color: #fff; border: none; padding: 12px 20px; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; width: 100%; transition: background 0.2s; }
        button:hover { background: #005bb5; }
        .instructions { background: #e5f2ff; padding: 15px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; line-height: 1.5; }
    </style>
</head>
<body>

<div class="container">
    <h2><span style="font-size: 24px;">🗄️</span> Large SQL Importer</h2>
    
    <div class="instructions">
        <strong>How to bypass the "413 Entity Too Large" error:</strong><br>
        1. Use FTP, SFTP, or your hosting File Manager to upload your <code>.sql</code> file directly into the <code>public/</code> folder on your server.<br>
        2. Refresh this page.<br>
        3. Select the file from the dropdown below and click Import.
    </div>

    <?php if ($message): ?>
        <div class="alert success"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Database Host</label>
            <input type="text" name="db_host" value="<?= htmlspecialchars($dbHost) ?>" required>
        </div>
        <div class="form-group">
            <label>Database Username</label>
            <input type="text" name="db_user" value="<?= htmlspecialchars($dbUser) ?>" required>
        </div>
        <div class="form-group">
            <label>Database Password</label>
            <input type="password" name="db_pass" value="<?= htmlspecialchars($dbPass) ?>">
        </div>
        <div class="form-group">
            <label>Database Name</label>
            <input type="text" name="db_name" value="<?= htmlspecialchars($dbName) ?>" required>
        </div>
        <div class="form-group">
            <label>Select SQL File on Server</label>
            <?php if (empty($availableFiles)): ?>
                <select name="sql_file" required>
                    <option value="">-- No .sql files found in public directory --</option>
                </select>
                <div style="font-size: 12px; color: #ff3b30; margin-top: 5px;">Please upload your .sql file to the <code><?= __DIR__ ?></code> directory first!</div>
            <?php else: ?>
                <select name="sql_file" required>
                    <option value="">-- Select File --</option>
                    <?php foreach ($availableFiles as $file): ?>
                        <option value="<?= htmlspecialchars($file) ?>"><?= htmlspecialchars(basename($file)) ?> (<?= round(filesize($file) / 1048576, 2) ?> MB)</option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>
        
        <button type="submit">Import Database</button>
    </form>
</div>

</body>
</html>

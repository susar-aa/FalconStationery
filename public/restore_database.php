<?php
/**
 * Falcon Stationery - Invoice Data Restoration Tool
 * Runs database restoration for missing product lines cleanly from any browser.
 */

if (file_exists(__DIR__ . '/../config/database.php')) {
    require_once __DIR__ . '/../config/database.php';
} elseif (file_exists(__DIR__ . '/ERP/config/database.php')) {
    require_once __DIR__ . '/ERP/config/database.php';
}

$host = defined('DB_HOST') ? DB_HOST : 'localhost';
$user = defined('DB_USER') ? DB_USER : 'root';
$pass = defined('DB_PASS') ? DB_PASS : '';
$db   = defined('DB_NAME') ? DB_NAME : 'curtiss_erp';

$executed = false;
$logs = [];
$errorMsg = null;
$itemsInserted = 0;
$invoicesUpdated = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'run_restore') {
    $executed = true;
    
    $host = !empty($_POST['db_host']) ? trim($_POST['db_host']) : $host;
    $user = !empty($_POST['db_user']) ? trim($_POST['db_user']) : $user;
    $pass = isset($_POST['db_pass']) ? $_POST['db_pass'] : $pass;
    $db   = !empty($_POST['db_name']) ? trim($_POST['db_name']) : $db;

    ini_set('memory_limit', '1024M');
    set_time_limit(600);

    $mysqli = new mysqli($host, $user, $pass, $db);
    if ($mysqli->connect_error) {
        $errorMsg = "Database Connection Failed: " . $mysqli->connect_error;
    } else {
        $sqlFile = dirname(__DIR__) . '/restore_all_missing_invoices.sql';
        if (!file_exists($sqlFile)) {
            $sqlFile = __DIR__ . '/restore_all_missing_invoices.sql';
        }
        if (!file_exists($sqlFile)) {
            $errorMsg = "SQL File not found at: " . $sqlFile;
        } else {
            $content = file_get_contents($sqlFile);
            $content = str_replace("\r\n", "\n", $content);
            $statements = explode(";\n\n", $content);

            $logs[] = "Connected to database [{$db}] on [{$host}].";
            $logs[] = "Loaded restoration script with " . count($statements) . " batch statements.";

            foreach ($statements as $idx => $stmt) {
                $stmt = trim($stmt);
                if (empty($stmt)) continue;

                $lines = explode("\n", $stmt);
                $cleanLines = [];
                foreach ($lines as $l) {
                    if (strpos(trim($l), '--') !== 0) {
                        $cleanLines[] = $l;
                    }
                }
                $query = trim(implode("\n", $cleanLines));
                if (empty($query)) continue;

                if (!$mysqli->query($query)) {
                    $logs[] = "⚠️ Error in statement #" . ($idx+1) . ": " . $mysqli->error;
                } else {
                    $affected = $mysqli->affected_rows;
                    if (strpos($query, 'INSERT INTO `invoice_items`') === 0) {
                        $itemsInserted += $affected;
                    } elseif (strpos($query, 'UPDATE invoices') === 0) {
                        $invoicesUpdated += $affected;
                    }
                }
            }
            $logs[] = "✅ Restoration process finished successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Restoration Tool - Falcon Stationery</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0F172A; color: #F8FAFC; font-family: 'Inter', sans-serif; min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 820px; margin: 0 auto; background: #1E293B; border: 1px solid #334155; border-radius: 16px; padding: 32px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3); }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #334155; padding-bottom: 20px; margin-bottom: 24px; }
        .title { font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 800; color: #38BDF8; }
        .subtitle { font-size: 13px; color: #94A3B8; margin-top: 4px; }
        .badge { background: #0284C7; color: #FFF; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 9999px; text-transform: uppercase; }
        .card { background: #0F172A; border: 1px solid #334155; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        label { font-size: 12px; font-weight: 600; color: #CBD5E1; }
        input[type="text"], input[type="password"] { background: #1E293B; border: 1px solid #475569; color: #F8FAFC; padding: 10px 14px; border-radius: 8px; font-size: 13px; font-family: inherit; }
        input[type="text"]:focus, input[type="password"]:focus { outline: none; border-color: #38BDF8; }
        .btn-submit { background: linear-gradient(135deg, #0284C7, #2563EB); color: white; border: none; padding: 14px 24px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; width: 100%; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4); }
        .btn-submit:hover { background: linear-gradient(135deg, #0369A1, #1D4ED8); transform: translateY(-1px); }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .stat-box { background: #1E293B; border: 1px solid #334155; border-radius: 10px; padding: 16px; text-align: center; }
        .stat-val { font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 800; color: #4ADE80; }
        .stat-lbl { font-size: 12px; color: #94A3B8; margin-top: 2px; }
        .log-box { background: #090D16; border: 1px solid #334155; border-radius: 10px; padding: 16px; font-family: monospace; font-size: 12px; max-height: 250px; overflow-y: auto; color: #A7F3D0; line-height: 1.6; }
        .alert-error { background: #7F1D1D; border: 1px solid #991B1B; color: #FCA5A5; padding: 14px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <div class="title">Falcon Stationery ERP</div>
                <div class="subtitle">Automatic Database Invoice Items Restoration Tool</div>
            </div>
            <span class="badge">v2.0 Safe Restore</span>
        </div>

        <?php if ($errorMsg): ?>
            <div class="alert-error">
                <strong>Error:</strong> <?= htmlspecialchars($errorMsg) ?>
            </div>
        <?php endif; ?>

        <?php if ($executed && !$errorMsg): ?>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-val"><?= number_format($itemsInserted) ?></div>
                    <div class="stat-lbl">Product Items Restored</div>
                </div>
                <div class="stat-box">
                    <div class="stat-val"><?= number_format($invoicesUpdated) ?></div>
                    <div class="stat-lbl">Invoices Updated & Re-synced</div>
                </div>
            </div>

            <div class="card">
                <h4 style="font-size: 14px; color: #38BDF8; margin-bottom: 12px;">Execution Log:</h4>
                <div class="log-box">
                    <?php foreach ($logs as $l): ?>
                        <div><?= htmlspecialchars($l) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="action" value="run_restore">
            <div class="card">
                <h4 style="font-size: 14px; color: #F8FAFC; margin-bottom: 16px;">Database Connection Settings:</h4>
                <div class="grid">
                    <div class="form-group">
                        <label>DB Host</label>
                        <input type="text" name="db_host" value="<?= htmlspecialchars($host) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>DB Name</label>
                        <input type="text" name="db_name" value="<?= htmlspecialchars($db) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>DB Username</label>
                        <input type="text" name="db_user" value="<?= htmlspecialchars($user) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>DB Password</label>
                        <input type="password" name="db_pass" value="<?= htmlspecialchars($pass) ?>">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit">🚀 Run Full Database Restoration (Restore 5,889 Items)</button>
        </form>
    </div>
</body>
</html>

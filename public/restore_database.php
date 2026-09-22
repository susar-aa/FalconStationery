<?php
/**
 * Falcon Stationery - Database Query & Restoration Tool
 * Allows executing restoration scripts or pasting custom SQL queries directly from any web browser.
 */

if (file_exists(__DIR__ . '/../config/database.php')) {
    require_once __DIR__ . '/../config/database.php';
} elseif (file_exists(__DIR__ . '/config/database.php')) {
    require_once __DIR__ . '/config/database.php';
}

$host = defined('DB_HOST') ? DB_HOST : 'localhost';
$user = defined('DB_USER') ? DB_USER : 'root';
$pass = defined('DB_PASS') ? DB_PASS : '';
$db   = defined('DB_NAME') ? DB_NAME : 'curtiss_erp';

$executed = false;
$logs = [];
$errorMsg = null;
$totalAffected = 0;
$queryCount = 0;
$executionTime = 0;

$defaultSqlFile = dirname(__DIR__) . '/restore_all_missing_invoices.sql';
if (!file_exists($defaultSqlFile) && file_exists(__DIR__ . '/restore_all_missing_invoices.sql')) {
    $defaultSqlFile = __DIR__ . '/restore_all_missing_invoices.sql';
}

$initialQueryText = "";
if (file_exists($defaultSqlFile)) {
    $initialQueryText = file_get_contents($defaultSqlFile);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'run_sql') {
    $executed = true;
    $startTime = microtime(true);

    $host = !empty($_POST['db_host']) ? trim($_POST['db_host']) : $host;
    $user = !empty($_POST['db_user']) ? trim($_POST['db_user']) : $user;
    $pass = isset($_POST['db_pass']) ? $_POST['db_pass'] : $pass;
    $db   = !empty($_POST['db_name']) ? trim($_POST['db_name']) : $db;

    $sqlQueryText = isset($_POST['sql_query']) ? trim($_POST['sql_query']) : '';
    $initialQueryText = $sqlQueryText;

    if (empty($sqlQueryText)) {
        $errorMsg = "Please enter or paste an SQL query to execute.";
    } else {
        ini_set('memory_limit', '1024M');
        set_time_limit(600);

        $mysqli = new mysqli($host, $user, $pass, $db);
        if ($mysqli->connect_error) {
            $errorMsg = "Database Connection Failed: " . $mysqli->connect_error;
        } else {
            $mysqli->set_charset("utf8mb4");

            $normalized = str_replace("\r\n", "\n", $sqlQueryText);
            $statements = preg_split('/;[ \t]*\n/', $normalized);

            $logs[] = "Connected to database [{$db}] on [{$host}].";
            $logs[] = "Processing " . count($statements) . " SQL statement blocks...";

            $mysqli->query("SET FOREIGN_KEY_CHECKS = 0");

            foreach ($statements as $idx => $stmt) {
                $stmt = trim($stmt);
                if (empty($stmt)) continue;

                $lines = explode("\n", $stmt);
                $cleanLines = [];
                foreach ($lines as $l) {
                    if (strpos(trim($l), '--') !== 0 && strpos(trim($l), '#') !== 0) {
                        $cleanLines[] = $l;
                    }
                }
                $query = trim(implode("\n", $cleanLines));
                if (empty($query)) continue;

                $queryCount++;
                if (!$mysqli->query($query)) {
                    $logs[] = "⚠️ Error in statement #{$queryCount}: " . $mysqli->error;
                } else {
                    $affected = $mysqli->affected_rows;
                    if ($affected > 0) {
                        $totalAffected += $affected;
                    }
                    $logs[] = "Query #{$queryCount} executed successfully (" . ($affected >= 0 ? "{$affected} rows affected" : "OK") . ").";
                }
            }

            $mysqli->query("SET FOREIGN_KEY_CHECKS = 1");
            $executionTime = round(microtime(true) - $startTime, 3);
            $logs[] = "✅ Execution completed in {$executionTime}s!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Query Runner & Database Restoration - Falcon Stationery</title>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0F172A; color: #F8FAFC; font-family: 'Inter', sans-serif; min-height: 100vh; padding: 30px 20px; }
        .container { max-width: 960px; margin: 0 auto; background: #1E293B; border: 1px solid #334155; border-radius: 16px; padding: 28px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #334155; padding-bottom: 18px; margin-bottom: 24px; }
        .title { font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 800; color: #38BDF8; display: flex; align-items: center; gap: 8px; }
        .subtitle { font-size: 13px; color: #94A3B8; margin-top: 4px; }
        .badge { background: #0284C7; color: #FFF; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 9999px; text-transform: uppercase; }
        .card { background: #0F172A; border: 1px solid #334155; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        label { font-size: 12px; font-weight: 600; color: #CBD5E1; }
        input[type="text"], input[type="password"] { background: #1E293B; border: 1px solid #475569; color: #F8FAFC; padding: 9px 12px; border-radius: 8px; font-size: 13px; font-family: inherit; }
        input[type="text"]:focus, input[type="password"]:focus, textarea:focus { outline: none; border-color: #38BDF8; }
        textarea { width: 100%; height: 340px; background: #090D16; border: 1px solid #334155; color: #38BDF8; font-family: 'Fira Code', monospace; font-size: 13px; line-height: 1.5; padding: 16px; border-radius: 10px; resize: vertical; margin-top: 8px; }
        .btn-container { display: flex; gap: 12px; margin-top: 16px; }
        .btn-submit { flex: 2; background: linear-gradient(135deg, #0284C7, #2563EB); color: white; border: none; padding: 14px 24px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4); }
        .btn-submit:hover { background: linear-gradient(135deg, #0369A1, #1D4ED8); transform: translateY(-1px); }
        .btn-secondary { flex: 1; background: #334155; color: white; border: 1px solid #475569; padding: 14px 18px; border-radius: 10px; font-weight: 600; font-size: 13px; cursor: pointer; text-align: center; text-decoration: none; display: inline-block; }
        .btn-secondary:hover { background: #475569; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
        .stat-box { background: #1E293B; border: 1px solid #334155; border-radius: 10px; padding: 16px; text-align: center; }
        .stat-val { font-family: 'Outfit', sans-serif; font-size: 26px; font-weight: 800; color: #4ADE80; }
        .stat-lbl { font-size: 12px; color: #94A3B8; margin-top: 2px; }
        .log-box { background: #090D16; border: 1px solid #334155; border-radius: 10px; padding: 16px; font-family: 'Fira Code', monospace; font-size: 12px; max-height: 220px; overflow-y: auto; color: #A7F3D0; line-height: 1.6; }
        .alert-error { background: #7F1D1D; border: 1px solid #991B1B; color: #FCA5A5; padding: 14px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <div class="title">⚡ Falcon Stationery SQL Runner</div>
                <div class="subtitle">Paste and execute SQL queries or run automated invoice restorations</div>
            </div>
            <span class="badge">Safe Web SQL Runner</span>
        </div>

        <?php if ($errorMsg): ?>
            <div class="alert-error">
                <strong>Error:</strong> <?= htmlspecialchars($errorMsg) ?>
            </div>
        <?php endif; ?>

        <?php if ($executed && !$errorMsg): ?>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-val"><?= number_format($queryCount) ?></div>
                    <div class="stat-lbl">Statements Executed</div>
                </div>
                <div class="stat-box">
                    <div class="stat-val"><?= number_format($totalAffected) ?></div>
                    <div class="stat-lbl">Total Rows Affected</div>
                </div>
                <div class="stat-box">
                    <div class="stat-val"><?= $executionTime ?>s</div>
                    <div class="stat-lbl">Execution Time</div>
                </div>
            </div>

            <div class="card">
                <h4 style="font-size: 13px; color: #38BDF8; margin-bottom: 12px;">Console Output:</h4>
                <div class="log-box">
                    <?php foreach ($logs as $l): ?>
                        <div><?= htmlspecialchars($l) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="action" value="run_sql">
            
            <div class="card">
                <h4 style="font-size: 13px; color: #F8FAFC; margin-bottom: 12px;">Database Connection Settings</h4>
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

            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label style="font-size: 13px; font-weight: 700; color: #F8FAFC;">Paste SQL Query / Restoration Statements Below:</label>
                    <button type="button" onclick="document.getElementById('sql_query').value='';" style="background: none; border: none; color: #94A3B8; font-size: 12px; cursor: pointer; text-decoration: underline;">Clear Box</button>
                </div>
                <textarea id="sql_query" name="sql_query" placeholder="Paste your SQL statements here..."><?= htmlspecialchars($initialQueryText) ?></textarea>
                
                <div class="btn-container">
                    <button type="submit" class="btn-submit">🚀 Run SQL Query / Execute Restoration</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>

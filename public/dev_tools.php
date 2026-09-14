<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$output_git = '';
$output_sql = '';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        
        // --- GIT PULL (FETCH & RESET) ---
        if ($_POST['action'] === 'git_pull') {
            $repo_dir = realpath(__DIR__ . '/../');
            $safe_dir = escapeshellarg(str_replace('\\', '/', $repo_dir));
            $cmd = "set GCM_INTERACTIVE=false && set GIT_TERMINAL_PROMPT=0 && cd " . escapeshellarg($repo_dir) . " && git config --global --add safe.directory $safe_dir 2>&1 && git fetch --all 2>&1 && git reset --hard origin/main 2>&1";
            $result = shell_exec($cmd);
            $output_git = "<div class='success-box'><pre>" . htmlspecialchars($result) . "</pre></div>";
        } 
        
        // --- SQL EXECUTION ---
        elseif ($_POST['action'] === 'run_sql' && !empty($_POST['sql_code'])) {
            try {
                $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Execute the raw SQL
                $pdo->exec($_POST['sql_code']);
                
                $output_sql = "<div class='success-box'><i class='fa-solid fa-check-circle'></i> SQL Executed Successfully!</div>";
            } catch (PDOException $e) {
                $output_sql = "<div class='error-box'><i class='fa-solid fa-triangle-exclamation'></i> <strong>SQL Error:</strong><br>" . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ERP Deployment & DB Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #334155; margin: 0; padding: 40px; }
        .container { max-width: 900px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #0f172a; font-size: 24px; }
        .logout-btn { color: #ef4444; text-decoration: none; font-weight: 600; font-size: 14px; border: 1px solid #fca5a5; padding: 6px 12px; border-radius: 6px; transition: 0.2s; }
        .logout-btn:hover { background: #fef2f2; }
        
        .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03); margin-bottom: 30px; border: 1px solid #e2e8f0; }
        .card h2 { margin-top: 0; font-size: 18px; color: #1e293b; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        
        textarea, input[type="text"] { width: 100%; padding: 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; box-sizing: border-box; margin-bottom: 15px; background: #f8fafc; }
        textarea { height: 150px; font-family: 'Courier New', Courier, monospace; resize: vertical; }
        textarea:focus, input[type="text"]:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        
        .btn { background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; transition: 0.2s; }
        .btn:hover { background: #2563eb; }
        .btn-green { background: #10b981; }
        .btn-green:hover { background: #059669; }
        .btn-purple { background: #8b5cf6; }
        .btn-purple:hover { background: #7c3aed; }
        
        .success-box { background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 14px; }
        .error-box { background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 14px; }
        pre { margin: 0; white-space: pre-wrap; font-family: 'Courier New', Courier, monospace; }
        
        .warning-text { font-size: 13px; color: #64748b; margin-top: 10px; display: block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fa-solid fa-code"></i> Developer Tools</h1>
        </div>

        <!-- Git Deploy Card -->
        <div class="card">
            <h2><i class="fa-brands fa-github"></i> Git Auto-Deploy</h2>
            <p style="font-size: 14px; color: #475569; margin-bottom: 20px;">
                Use this on the <strong>Live aaPanel Server</strong> to pull the latest changes from the main branch on GitHub. This will overwrite any uncommitted local changes on the server.
            </p>
            <form method="POST">
                <input type="hidden" name="action" value="git_pull">
                <button type="submit" class="btn"><i class="fa-solid fa-cloud-arrow-down"></i> Pull Latest Changes</button>
            </form>
            <?= $output_git ?>
        </div>

        <!-- SQL Execution Card -->
        <div class="card">
            <h2><i class="fa-solid fa-database"></i> Database SQL Executor</h2>
            <p style="font-size: 14px; color: #475569; margin-bottom: 15px;">
                Paste any database migration or ALTER TABLE queries here to run them directly on the connected database (<strong><?= DB_NAME ?></strong>).
            </p>
            <form method="POST">
                <input type="hidden" name="action" value="run_sql">
                <textarea name="sql_code" placeholder="ALTER TABLE `journal_entries` ADD COLUMN ...;" required></textarea>
                <button type="submit" class="btn btn-green"><i class="fa-solid fa-play"></i> Execute SQL</button>
                <span class="warning-text"><i class="fa-solid fa-circle-info"></i> Be careful! Queries run here are executed immediately and cannot be undone.</span>
            </form>
            <?= $output_sql ?>
        </div>
    </div>
</body>
</html>

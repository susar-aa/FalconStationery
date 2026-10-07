<?php
if (DIRECTORY_SEPARATOR === '\\' || 
    (isset($_GET['secret']) && $_GET['secret'] === 'curtiss_debug_123') || 
    (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false || strpos($_SERVER['HTTP_HOST'], '::1') !== false))) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Global Exception and Error Logger
function global_exception_handler(Throwable $exception) {
    $correlationId = 'ERR-SYS-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)) . '-' . time();
    $logFile = dirname(__DIR__) . '/app_errors.log';
    $errMessage = "[Correlation ID: {$correlationId}] Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    $logContent = "[" . date('Y-m-d H:i:s') . "] " . $errMessage . "\n" . $exception->getTraceAsString() . "\n\n";
    @file_put_contents($logFile, $logContent, FILE_APPEND);

    // If it's an API/AJAX request, return structured JSON without leaking internal traces
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || 
              (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
              isset($_GET['api_sync']);
              
    if ($isAjax) {
        header('Content-Type: application/json');
        if (!headers_sent()) {
            http_response_code(200);
        }
        echo json_encode([
            'success' => false,
            'status' => 'error',
            'correlation_id' => $correlationId,
            'message' => 'An internal server error occurred. (Correlation ID: ' . $correlationId . ')'
        ]);
        exit;
    } else {
        if (!headers_sent()) {
            http_response_code(500);
        }
        $showDetails = true; // Enabled for debugging and diagnostic reporting
        $errTextToCopy = "Correlation ID: " . $correlationId . "\n" .
                         "URL: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n" .
                         "Error Message: " . $exception->getMessage() . "\n" .
                         "File: " . $exception->getFile() . ":" . $exception->getLine() . "\n\n" .
                         "Stack Trace:\n" . $exception->getTraceAsString();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Application Error (HTTP 500)</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f4f6f8; color: #1f2937; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
                .card { background: white; padding: 32px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); max-width: 850px; width: 100%; border: 1px solid #e5e7eb; }
                h3 { color: #ef4444; margin-top: 0; font-size: 22px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
                p { font-size: 14px; color: #4b5563; line-height: 1.6; margin-bottom: 12px; }
                .correlation-badge { display: inline-block; background: #fef2f2; color: #991b1b; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; border: 1px solid #fecaca; margin-bottom: 12px; }
                pre { background: #1e293b; color: #e2e8f0; padding: 16px; border-radius: 10px; overflow-x: auto; font-size: 12px; line-height: 1.5; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; max-height: 320px; }
                .btn-group { display: flex; gap: 12px; margin-top: 24px; flex-wrap: wrap; }
                .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #2563eb; color: white !important; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; }
                .btn:hover { background: #1d4ed8; }
                .btn-secondary { background: #6b7280; }
                .btn-secondary:hover { background: #4b5563; }
                .btn-outline { background: transparent; border: 1px solid #d1d5db; color: #374151 !important; }
                .btn-outline:hover { background: #f3f4f6; }
            </style>
        </head>
        <body>
            <div class="card">
                <h3>Application Error (HTTP 500)</h3>
                <p>An unexpected error occurred while processing your request. This issue has been logged to <strong>app_errors.log</strong>.</p>
                <div class="correlation-badge">Correlation ID: <?= htmlspecialchars($correlationId) ?></div>
                
                <?php if ($showDetails): ?>
                    <p><strong>Error Message:</strong> <span style="color:#dc2626; word-break: break-all;"><?= htmlspecialchars($exception->getMessage()) ?></span></p>
                    <p><strong>File:</strong> <code><?= htmlspecialchars($exception->getFile()) ?>:<?= $exception->getLine() ?></code></p>
                    <pre><?= htmlspecialchars($exception->getTraceAsString()) ?></pre>
                <?php endif; ?>

                <textarea id="hiddenErrorText" style="position: absolute; left: -9999px;"><?= htmlspecialchars($errTextToCopy) ?></textarea>

                <div class="btn-group">
                    <a href="javascript:history.back()" class="btn">Go Back</a>
                    <button type="button" onclick="copyErrorDetails()" id="copyErrorBtn" class="btn btn-secondary">
                        Copy Error Details
                    </button>
                </div>
            </div>

            <script>
            function copyErrorDetails() {
                const text = document.getElementById('hiddenErrorText').value;
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(showCopied).catch(fallbackCopy);
                } else {
                    fallbackCopy();
                }
                function fallbackCopy() {
                    const el = document.getElementById('hiddenErrorText');
                    el.style.position = 'fixed';
                    el.style.left = '0';
                    el.style.top = '0';
                    el.select();
                    document.execCommand('copy');
                    showCopied();
                }
                function showCopied() {
                    const btn = document.getElementById('copyErrorBtn');
                    btn.innerText = 'Copied to Clipboard!';
                    btn.style.background = '#10b981';
                    setTimeout(() => {
                        btn.innerText = 'Copy Error Details';
                        btn.style.background = '#6b7280';
                    }, 2500);
                }
            }
            </script>
        </body>
        </html>
        <?php
    }
    exit;
}

function global_error_handler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

set_exception_handler('global_exception_handler');
set_error_handler('global_error_handler');

function global_shutdown_handler() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR])) {
        $logFile = dirname(__DIR__) . '/app_errors.log';
        $errMessage = "Fatal Error [{$error['type']}]: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line'];
        $logContent = "[" . date('Y-m-d H:i:s') . "] " . $errMessage . "\nShutdown handler captured fatal crash.\n\n";
        @file_put_contents($logFile, $logContent, FILE_APPEND);

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                  isset($_GET['api_sync']);
                  
        if ($isAjax) {
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode([
                'success' => false,
                'message' => $error['message'],
                'trace' => 'Fatal error in ' . $error['file'] . ' on line ' . $error['line']
            ]);
        }
    }
}
register_shutdown_function('global_shutdown_handler');


// Global CORS Headers
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Credentials: true');
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-User-ID');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'trycurtiss.com') !== false) {
    $isSecure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

session_start();

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$vendorAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/core/Cache.php';
require_once dirname(__DIR__) . '/core/Database.php';

// Auto-run schema_update.sql if pending, then clear file
$schemaFile = dirname(__DIR__) . '/schema_update.sql';
if (file_exists($schemaFile)) {
    $rawSql = trim(file_get_contents($schemaFile));
    // Remove comments and whitespace
    $cleanSql = preg_replace('/--.*$/m', '', $rawSql);
    $cleanSql = trim($cleanSql);
    
    if (!empty($cleanSql)) {
        try {
            $dbMigration = new Database();
            // Split into individual SQL statements
            $statements = array_filter(array_map('trim', explode(';', $cleanSql)));
            foreach ($statements as $stmt) {
                if (!empty($stmt)) {
                    $dbMigration->query($stmt);
                    $dbMigration->execute();
                }
            }
            // Successfully executed: Clear the schema file so it won't run again
            $defaultHeader = "-- Copy & paste SQL queries here before running deploy.bat\n-- After deployment, the server will execute these queries once and automatically wipe this file clean.\n";
            @file_put_contents($schemaFile, $defaultHeader);
        } catch (Exception $e) {
            @file_put_contents(dirname(__DIR__) . '/app_errors.log', "[" . date('Y-m-d H:i:s') . "] Migration Error: " . $e->getMessage() . "\n", FILE_APPEND);
        }
    }
}

require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/core/App.php';

$app = new App();


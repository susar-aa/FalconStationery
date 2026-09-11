<?php
/**
 * Standalone SQL Importer with Chunked Uploads
 * Bypasses Nginx 413 Request Entity Too Large and PHP upload limits
 */

session_start();
set_time_limit(0); 
ini_set('memory_limit', '-1'); 

header('Access-Control-Allow-Origin: *');

// Handle AJAX chunk uploads
if (isset($_POST['action']) && $_POST['action'] === 'upload_chunk') {
    if (!isset($_FILES['chunk'])) {
        echo json_encode(['status' => 'error', 'message' => 'No chunk received']);
        exit;
    }
    
    $chunk = $_FILES['chunk']['tmp_name'];
    $tempFile = __DIR__ . '/temp_import.sql';
    
    // If it's the first chunk, ensure we start fresh
    if (isset($_POST['chunk_index']) && $_POST['chunk_index'] == 0 && file_exists($tempFile)) {
        unlink($tempFile);
    }
    
    // Append chunk to temp file
    $out = fopen($tempFile, 'ab');
    $in = fopen($chunk, 'rb');
    while ($buff = fread($in, 4096)) {
        fwrite($out, $buff);
    }
    fclose($in);
    fclose($out);
    
    echo json_encode(['status' => 'success']);
    exit;
}

// Handle AJAX import trigger
if (isset($_POST['action']) && $_POST['action'] === 'import_temp') {
    $dbHost = $_POST['db_host'] ?? 'localhost';
    $dbUser = $_POST['db_user'] ?? 'root';
    $dbPass = $_POST['db_pass'] ?? '';
    $dbName = $_POST['db_name'] ?? '';
    $tempFile = __DIR__ . '/temp_import.sql';

    if (!file_exists($tempFile)) {
        echo json_encode(['status' => 'error', 'message' => 'SQL file was not uploaded completely.']);
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);

        // Read and execute in chunks to save memory
        $sql = file_get_contents($tempFile);
        if ($sql === false) {
            throw new Exception("Could not read the uploaded SQL file.");
        }

        $queries = explode(";\n", $sql);
        $pdo->beginTransaction();
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $pdo->exec($query);
            }
        }
        $pdo->commit();
        
        unlink($tempFile); // Clean up
        
        echo json_encode(['status' => 'success', 'message' => 'Database imported successfully!']);
    } catch (PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

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
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; display: none; }
        .alert.success { background: #e6f9ec; color: #1e4620; border: 1px solid #34c759; }
        .alert.error { background: #fff0ef; color: #6e1a15; border: 1px solid #ff3b30; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; }
        input[type="text"], input[type="password"], input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        button { background: #007aff; color: #fff; border: none; padding: 12px 20px; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; width: 100%; transition: background 0.2s; margin-top: 10px; }
        button:hover { background: #005bb5; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        .instructions { background: #e5f2ff; padding: 15px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; line-height: 1.5; color: #004085; }
        .progress-bar-container { width: 100%; background: #e0e0e0; border-radius: 8px; overflow: hidden; margin-top: 15px; display: none; }
        .progress-bar { height: 10px; background: #34c759; width: 0%; transition: width 0.2s; }
        #statusText { font-size: 13px; font-weight: 600; margin-top: 8px; color: #636366; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <h2><span style="font-size: 24px;">🗄️</span> Chunked SQL Importer</h2>
    
    <div class="instructions">
        <strong>Upload any size file right from your browser!</strong><br>
        This tool splits your `.sql` file into small pieces before sending it to the server. This completely bypasses the <b>"413 Request Entity Too Large"</b> limit of Nginx and PHP without needing FTP!
    </div>

    <div id="alertBox" class="alert"></div>

    <form id="importForm">
        <div class="form-group">
            <label>Database Host</label>
            <input type="text" id="db_host" value="localhost" required>
        </div>
        <div class="form-group">
            <label>Database Username</label>
            <input type="text" id="db_user" value="root" required>
        </div>
        <div class="form-group">
            <label>Database Password</label>
            <input type="password" id="db_pass" value="">
        </div>
        <div class="form-group">
            <label>Database Name</label>
            <input type="text" id="db_name" value="falcon_erp" required>
        </div>
        <div class="form-group">
            <label>Select Local SQL File</label>
            <input type="file" id="sql_file" accept=".sql" required>
        </div>
        
        <button type="submit" id="submitBtn">Start Import</button>
        
        <div class="progress-bar-container" id="progressContainer">
            <div class="progress-bar" id="progressBar"></div>
        </div>
        <div id="statusText"></div>
    </form>
</div>

<script>
document.getElementById('importForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const fileInput = document.getElementById('sql_file');
    const file = fileInput.files[0];
    if (!file) return;

    const btn = document.getElementById('submitBtn');
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    const statusText = document.getElementById('statusText');
    const alertBox = document.getElementById('alertBox');
    
    // Reset UI
    btn.disabled = true;
    progressContainer.style.display = 'block';
    progressBar.style.width = '0%';
    alertBox.style.display = 'none';
    
    const dbHost = document.getElementById('db_host').value;
    const dbUser = document.getElementById('db_user').value;
    const dbPass = document.getElementById('db_pass').value;
    const dbName = document.getElementById('db_name').value;
    
    // Chunking settings
    const chunkSize = 1024 * 1024; // 1MB chunks
    const totalChunks = Math.ceil(file.size / chunkSize);
    
    try {
        // Step 1: Upload chunks
        for (let i = 0; i < totalChunks; i++) {
            statusText.innerText = `Uploading chunk ${i + 1} of ${totalChunks}...`;
            
            const start = i * chunkSize;
            const end = Math.min(start + chunkSize, file.size);
            const chunk = file.slice(start, end);
            
            const formData = new FormData();
            formData.append('action', 'upload_chunk');
            formData.append('chunk_index', i);
            formData.append('chunk', chunk, 'chunk.part');
            
            const response = await fetch('', { method: 'POST', body: formData });
            const data = await response.json();
            
            if (data.status !== 'success') {
                throw new Error(data.message || 'Chunk upload failed');
            }
            
            // Update progress
            const progress = ((i + 1) / totalChunks) * 100;
            progressBar.style.width = `${progress}%`;
        }
        
        // Step 2: Trigger Import
        statusText.innerText = `File uploaded! Executing SQL queries on the database... this may take a while depending on file size.`;
        
        const importFormData = new FormData();
        importFormData.append('action', 'import_temp');
        importFormData.append('db_host', dbHost);
        importFormData.append('db_user', dbUser);
        importFormData.append('db_pass', dbPass);
        importFormData.append('db_name', dbName);
        
        const importResponse = await fetch('', { method: 'POST', body: importFormData });
        const importData = await importResponse.json();
        
        if (importData.status === 'success') {
            showAlert('success', importData.message);
            statusText.innerText = 'Import Complete!';
        } else {
            throw new Error(importData.message);
        }
        
    } catch (error) {
        showAlert('error', error.message);
        statusText.innerText = 'Import Failed.';
    } finally {
        btn.disabled = false;
        fileInput.value = ''; // Reset file input
    }
});

function showAlert(type, message) {
    const alertBox = document.getElementById('alertBox');
    alertBox.className = `alert ${type}`;
    alertBox.innerHTML = message;
    alertBox.style.display = 'block';
}
</script>

</body>
</html>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$latest = new stdClass();
$latest->version_name = '1.18.0';
$latest->version = '1.18.0';
$latest->major = 1;
$latest->minor = 18;
$latest->patch = 0;
$latest->build_version = 18;
$latest->force_update = 1;
$latest->release_notes = '';
$latest->apk_path = 'public/releases/app-v1.18.0.apk';
$latest->package_name = 'com.example.curtiss';

$notes = [];
if (!empty($latest->release_notes)) {
    $rawNotes = explode("\n", $latest->release_notes);
    foreach ($rawNotes as $line) {
        $trimmed = trim($line);
        if ($trimmed !== '') {
            $notes[] = $trimmed;
        }
    }
}

$apkMd5 = '';
$versionedApkPath = '../' . $latest->apk_path;
if (file_exists($versionedApkPath)) {
    $apkMd5 = md5_file($versionedApkPath);
} else {
    $latestApkPath = '../public/releases/latest.apk';
    if (file_exists($latestApkPath)) {
        $apkMd5 = md5_file($latestApkPath);
    }
}

$major = intval($latest->major);
$minor = intval($latest->minor);
$patch = intval($latest->patch);
$latestVersionCode = intval($latest->build_version ?: ($major * 100 + $minor * 10 + $patch));
$latestVersionCodeNormalized = intval($latest->build_version ?: ($major * 10000 + $minor * 100 + $patch));

define('APP_URL', 'http://localhost');

$json = json_encode([
    'latestVersion' => $latest->version_name ?: $latest->version,
    'latestVersionCode' => $latestVersionCode,
    'latestVersionCodeNormalized' => $latestVersionCodeNormalized,
    'apkUrl' => APP_URL . '/releases/latest.apk?v=' . ($latest->version_name ?: $latest->version) . '&t=' . time(),
    'forceUpdate' => (bool)$latest->force_update,
    'releaseNotes' => $notes,
    'apkMd5' => $apkMd5,
    'packageName' => $latest->package_name ?: 'com.example.curtiss'
]);

echo $json;

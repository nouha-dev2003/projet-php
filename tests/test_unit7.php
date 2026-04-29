<?php
// tests/test_unit7.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../utils/Uploader.php';

echo "=== STABILIZATION AUDIT: UNIT 7 (Uploader) ===\n";

$targetDir = __DIR__ . '/uploads_test/';
@mkdir($targetDir, 0755, true);

// 1. Test invalid parameters
try {
    $badFile = [];
    Uploader::upload($badFile, $targetDir);
    echo "❌ Failed to catch invalid arguments.\n";
} catch (\RuntimeException $e) {
    echo "✅ Caught missing superglobal args correctly: " . $e->getMessage() . "\n";
}

// 2. Test file too large
try {
    $largeFile = [
        'error' => UPLOAD_ERR_OK,
        'size' => 5000000, 
        'name' => 'test.jpg'
    ];
    Uploader::upload($largeFile, $targetDir, 100);
    echo "❌ Failed to catch oversize file.\n";
} catch (\RuntimeException $e) {
    echo "✅ Caught file size constraint cleanly: " . $e->getMessage() . "\n";
}

// 3. Test Invalid Extension
try {
    $exeFile = [
        'error' => UPLOAD_ERR_OK,
        'size' => 10,
        'name' => 'malware.exe'
    ];
    Uploader::upload($exeFile, $targetDir);
    echo "❌ Failed to catch bad extension.\n";
} catch (\RuntimeException $e) {
    echo "✅ Caught invalid extension cleanly: " . $e->getMessage() . "\n";
}

if (is_dir($targetDir)) rmdir($targetDir);
echo "✅ Unit 7 Isolation Tests Completed.\n";

<?php
// Simple test script to check image upload setup
// Run with: php test-image-setup.php

echo "Testing Image Upload Setup...\n\n";

// Check if storage directories exist
$storageDir = __DIR__ . '/storage/app/public/partners';
$publicStorageDir = __DIR__ . '/public/storage';

echo "1. Checking storage directory...\n";
if (is_dir($storageDir)) {
    echo "   ✓ Storage directory exists: $storageDir\n";
    echo "   ✓ Is writable: " . (is_writable($storageDir) ? 'YES' : 'NO') . "\n";
} else {
    echo "   ✗ Storage directory missing: $storageDir\n";
    echo "   Creating directory...\n";
    if (mkdir($storageDir, 0755, true)) {
        echo "   ✓ Created storage directory\n";
    } else {
        echo "   ✗ Failed to create storage directory\n";
    }
}

echo "\n2. Checking storage symlink...\n";
if (is_link($publicStorageDir)) {
    echo "   ✓ Storage symlink exists: $publicStorageDir\n";
    echo "   ✓ Points to: " . readlink($publicStorageDir) . "\n";
} else if (is_dir($publicStorageDir)) {
    echo "   ✓ Storage directory exists (not symlink): $publicStorageDir\n";
} else {
    echo "   ✗ Storage symlink missing: $publicStorageDir\n";
    echo "   Run: php artisan storage:link\n";
}

echo "\n3. Checking partner storage subdirectory...\n";
$partnersStorageDir = $storageDir;
$partnersPublicDir = $publicStorageDir . '/partners';

if (is_dir($partnersPublicDir)) {
    echo "   ✓ Partners public directory accessible: $partnersPublicDir\n";
} else {
    echo "   ✗ Partners public directory not accessible: $partnersPublicDir\n";
}

echo "\n4. Testing image access...\n";
$testFiles = glob($storageDir . '/*.*');
if (!empty($testFiles)) {
    $testFile = basename($testFiles[0]);
    $publicUrl = "/storage/partners/$testFile";
    echo "   Found test image: $testFile\n";
    echo "   Public URL would be: $publicUrl\n";
    echo "   Full path: " . $publicStorageDir . "/partners/$testFile\n";
    echo "   File exists at public path: " . (file_exists($publicStorageDir . "/partners/$testFile") ? 'YES' : 'NO') . "\n";
} else {
    echo "   No images found in storage directory\n";
}

echo "\n5. Checking PHP upload settings...\n";
echo "   upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "   post_max_size: " . ini_get('post_max_size') . "\n";
echo "   max_file_uploads: " . ini_get('max_file_uploads') . "\n";

echo "\n6. Checking Laravel storage structure...\n";
$storageAppDir = __DIR__ . '/storage/app';
$storagePublicDir = __DIR__ . '/storage/app/public';

echo "   storage/app exists: " . (is_dir($storageAppDir) ? 'YES' : 'NO') . "\n";
echo "   storage/app/public exists: " . (is_dir($storagePublicDir) ? 'YES' : 'NO') . "\n";
echo "   storage/app/public writable: " . (is_writable($storagePublicDir) ? 'YES' : 'NO') . "\n";

echo "\nTest completed!\n";
echo "\nNext steps if issues found:\n";
echo "1. Run: php artisan storage:link\n";
echo "2. Check file permissions on storage directories\n";
echo "3. Try uploading an image and check browser console + Laravel logs\n";
?>
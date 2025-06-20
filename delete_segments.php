<?php
$folder = __DIR__ . '/starsports1tamil';

if (!is_dir($folder)) {
    die("❌ Folder not found: $folder\n");
}

$files = glob($folder . '/*'); // Get all files in folder

foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
        echo "🗑 Deleted: " . basename($file) . "\n";
    }
}

echo "✅ All segments and playlist deleted from starsports1tamil\n";
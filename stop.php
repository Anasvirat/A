<?php
echo "🛑 Stopping stream.php...\n";

// Try killing all PHP processes running stream.php
$output = shell_exec("pkill -f stream.php 2>&1");

echo "✅ stream.php stopped (if running)\n";

<?php
echo "🛑 Stopping stream scripts...\n";
$output = shell_exec("pkill -f stream_ 2>&1");
echo "✅ stream processes stopped (if running)\n";
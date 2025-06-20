<?php
set_time_limit(0);

$channels = [
    'starsports1tamil' => 'https://TS-j8bh.onrender.com/Box.ts?id=4',
    'sonyyay' => 'https://TS-j8bh.onrender.com/Box.ts?id=3',
    'Starsports2tamilhd' => 'https://TS-j8bh.onrender.com/Box.ts?id=2',
];

$segmentDuration = 10;         // Each segment = 10 seconds
$baseDir = __DIR__;            // Save in same folder as this script

foreach ($channels as $name => $url) {
    $pid = pcntl_fork();

    if ($pid == -1) {
        echo "❌ Failed to fork for $name\n";
        continue;
    }

    if ($pid === 0) {
        echo "▶ Starting $name...\n";

        $outputDir = "$baseDir/$name";
        if (!file_exists($outputDir)) mkdir($outputDir, 0777, true);

        $segmentIndex = 0;

        while (true) {
            $segmentFile = "$outputDir/index$segmentIndex.ts";
            $liveUrl = $url . '&cache=' . time();

            $cmd = "ffmpeg -y -fflags +discardcorrupt -re -rw_timeout 5000000 -i \"$liveUrl\" -t $segmentDuration -c copy \"$segmentFile\" 2>&1";
            echo "[$name] Running: $cmd\n";
            $output = shell_exec($cmd);
            echo "[$name] FFmpeg Output:\n$output\n";

            if (file_exists($segmentFile)) {
                // Append to playlist
                $m3u8 = "#EXTINF:$segmentDuration,\nindex$segmentIndex.ts\n";
                file_put_contents("$outputDir/index.m3u8", $m3u8, FILE_APPEND);
                echo "[$name] ✅ Segment $segmentIndex written\n";
            } else {
                echo "[$name] ❌ Failed to save segment $segmentIndex\n";
            }

            $segmentIndex++;
            sleep($segmentDuration);
        }

        exit();
    }
}

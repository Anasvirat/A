<?php
set_time_limit(0);

// List of channels: 'folder_name' => 'stream_url'
$channels = [
    'starsports1tamil' => 'https://TS-j8bh.onrender.com/Box.ts?id=4',
    'sonyyay'          => 'https://TS-j8bh.onrender.com/Box.ts?id=3',
];

// Segment config
$segmentDuration = 10;
$baseDir = __DIR__;

foreach ($channels as $name => $url) {
    echo "▶ Starting stream for $name...\n";

    $outputDir = "$baseDir/$name";
    if (!file_exists($outputDir)) mkdir($outputDir, 0777, true);

    $segmentIndex = 0;
    $previousSegment = null;

    while (true) {
        $segmentFile = "$outputDir/index$segmentIndex.ts";
        $liveUrl = $url . '&cache=' . time();

        // FFmpeg command to capture 10s
        $cmd = "ffmpeg -y -fflags +discardcorrupt -re -rw_timeout 5000000 -i \"$liveUrl\" -t $segmentDuration -c copy \"$segmentFile\" 2>&1";
        echo "[$name] Running: $cmd\n";
        $output = shell_exec($cmd);
        echo "[$name] FFmpeg Output:\n$output\n";

        if (file_exists($segmentFile)) {
            // Write fresh playlist with only latest segment
            $m3u8 = "#EXTM3U\n";
            $m3u8 .= "#EXT-X-VERSION:3\n";
            $m3u8 .= "#EXT-X-TARGETDURATION:$segmentDuration\n";
            $m3u8 .= "#EXT-X-MEDIA-SEQUENCE:$segmentIndex\n";
            $m3u8 .= "#EXTINF:$segmentDuration,\nindex$segmentIndex.ts\n";

            file_put_contents("$outputDir/index.m3u8", $m3u8);
            echo "[$name] ✅ index$segmentIndex.ts saved, playlist updated\n";

            // Delete previous segment
            if ($previousSegment !== null) {
                $oldFile = "$outputDir/index$previousSegment.ts";
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                    echo "[$name] 🗑 Deleted: index$previousSegment.ts\n";
                }
            }

            $previousSegment = $segmentIndex;
        } else {
            echo "[$name] ❌ Failed to save segment $segmentIndex\n";
        }

        $segmentIndex++;
        sleep($segmentDuration);
    }
}

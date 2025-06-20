<?php
set_time_limit(0);

$name = 'starsports1tamil';
$url  = 'https://TS-j8bh.onrender.com/Box.ts?id=4';
$segmentDuration = 10;
$baseDir = __DIR__;

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
        $m3u8Entry = "#EXTINF:$segmentDuration,\nindex$segmentIndex.ts\n";
        file_put_contents("$outputDir/index.m3u8", $m3u8Entry, FILE_APPEND);
        echo "[$name] ✅ Segment $segmentIndex created\n";
    } else {
        echo "[$name] ❌ Segment failed: index$segmentIndex.ts\n";
    }

    $segmentIndex++;
    sleep($segmentDuration);
}
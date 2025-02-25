<?php
// Path to the video file
$videoFile = "./video/video.mp4";

if (!file_exists($videoFile)) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

// Get file size
$filesize = filesize($videoFile);
$start = 0;
$end = $filesize - 1;

// Handle range requests
if (isset($_SERVER["HTTP_RANGE"])) {
    preg_match("/bytes=(\d+)-(\d+)?/", $_SERVER["HTTP_RANGE"], $matches);
    $start = intval($matches[1]);
    $end = isset($matches[2]) ? intval($matches[2]) : $end;
}

// Set headers for video streaming
header("Content-Type: video/mp4");
header("Accept-Ranges: bytes");
header("Content-Length: " . ($end - $start + 1));
header("Content-Range: bytes $start-$end/$filesize");
header("HTTP/1.1 206 Partial Content");

// Open the file and seek to the requested byte range
$handle = fopen($videoFile, "rb");
fseek($handle, $start);
$bufferSize = 1024 * 1024; // 1MB buffer

while (!feof($handle) && ($start <= $end)) {
    $bytesToRead = min($bufferSize, ($end - $start + 1));
    echo fread($handle, $bytesToRead);
    flush(); // Send data to client
    $start += $bytesToRead;
}

fclose($handle);
exit;
?>
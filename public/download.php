<?php
/**
 * Download Handler
 * Serves generated images for download
 */
require_once '../config/database.php';

$filename = $_GET['file'] ?? '';

// Validate filename (alphanumeric only)
if (!preg_match('/^[a-f0-9]+\.jpg$/i', $filename)) {
    http_response_code(400);
    die('Invalid file request');
}

$filepath = '../storage/results/' . $filename;

if (!file_exists($filepath)) {
    http_response_code(404);
    die('File not found. The image may have been deleted or expired.');
}

// Log download
$logEntry = sprintf(
    "[%s] Download: %s\n",
    date('Y-m-d H:i:s'),
    $filename
);
file_put_contents('../logs/download.log', $logEntry, FILE_APPEND);

// Send file
header('Content-Type: image/jpeg');
header('Content-Disposition: attachment; filename="' . str_replace('.jpg', '-ucapin.jpg', $filename) . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Flush output buffers
if (ob_get_level()) {
    ob_end_clean();
}

readfile($filepath);
exit;

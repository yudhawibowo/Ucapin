<?php
/**
 * Upload Handler
 * Handles image uploads with security validation
 */
require_once '../config/database.php';

header('Content-Type: application/json');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    // Check if file was uploaded
    if (!isset($_FILES['image'])) {
        throw new Exception('No file uploaded');
    }
    
    $file = $_FILES['image'];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server limit',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form limit',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by extension'
        ];
        $errorMsg = $errorMessages[$file['error']] ?? 'Upload error occurred';
        throw new Exception($errorMsg);
    }
    
    // Validate file size (max 2MB)
    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) {
        throw new Exception('File size exceeds 2MB limit (' . round($file['size'] / 1024 / 1024, 2) . 'MB)');
    }
    
    // Validate MIME type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG and PNG allowed (detected: ' . $mimeType . ')');
    }
    
    // Validate image extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
        throw new Exception('Invalid file extension');
    }
    
    // Ensure upload directory exists
    $uploadDir = '../storage/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate secure filename
    $filename = bin2hex(random_bytes(16)) . '.' . $extension;
    $uploadPath = $uploadDir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to save uploaded file');
    }
    
    // Verify file was saved
    if (!file_exists($uploadPath)) {
        throw new Exception('File was not saved successfully');
    }
    
    // Log the upload
    $logEntry = sprintf(
        "[%s] Upload: %s (%s, %d bytes)\n",
        date('Y-m-d H:i:s'),
        $filename,
        $mimeType,
        $file['size']
    );
    file_put_contents('../logs/upload.log', $logEntry, FILE_APPEND);
    
    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'path' => 'storage/uploads/' . $filename,
        'url' => '../storage/uploads/' . $filename,
        'message' => 'Upload successful'
    ]);
    
} catch (Exception $e) {
    error_log("Upload Error: " . $e->getMessage());
    
    // Log error
    $logEntry = sprintf(
        "[%s] Upload Error: %s\n",
        date('Y-m-d H:i:s'),
        $e->getMessage()
    );
    file_put_contents('../logs/upload.log', $logEntry, FILE_APPEND);
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

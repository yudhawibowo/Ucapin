<?php
/**
 * Background Removal API
 * Removes background from uploaded images using GD library
 * Returns the processed image with transparent background
 */
require_once '../config/database.php';

header('Content-Type: application/json');

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
        throw new Exception('File upload error');
    }

    // Validate file size (max 5MB for background removal)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        throw new Exception('File size exceeds 5MB limit');
    }

    // Validate MIME type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG and PNG allowed');
    }

    // Ensure upload directory exists
    $uploadDir = '../storage/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate secure filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = bin2hex(random_bytes(16)) . '.' . $extension;
    $uploadPath = $uploadDir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to save uploaded file');
    }

    // Process background removal
    $resultPath = removeBackground($uploadPath, $filename);

    // Log the operation
    $logEntry = sprintf(
        "[%s] Background Removal: %s -> %s\n",
        date('Y-m-d H:i:s'),
        $filename,
        basename($resultPath)
    );
    file_put_contents('../logs/upload.log', $logEntry, FILE_APPEND);

    echo json_encode([
        'success' => true,
        'filename' => basename($resultPath),
        'path' => 'storage/uploads/' . basename($resultPath),
        'url' => '../storage/uploads/' . basename($resultPath),
        'message' => 'Background removed successfully'
    ]);

} catch (Exception $e) {
    error_log("Background Removal Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

/**
 * Remove background from image
 * Uses simple color-based segmentation (assumes light/white background)
 * For production, consider using AI-based services like remove.bg API
 */
function removeBackground($inputPath, $filename) {
    // Get image info
    $imageInfo = getimagesize($inputPath);
    $width = $imageInfo[0];
    $height = $imageInfo[1];
    $mimeType = $imageInfo['mime'];

    // Create image resource
    switch ($mimeType) {
        case 'image/jpeg':
        case 'image/jpg':
            $sourceImage = imagecreatefromjpeg($inputPath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($inputPath);
            break;
        default:
            throw new Exception('Unsupported image format');
    }

    // Create output image with alpha channel
    $outputImage = imagecreatetruecolor($width, $height);
    
    // Enable alpha blending
    imagealphablending($outputImage, false);
    imagesavealpha($outputImage, true);
    
    // Create transparent background
    $transparent = imagecolorallocatealpha($outputImage, 0, 0, 0, 127);
    imagefill($outputImage, 0, 0, $transparent);

    // Process each pixel
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $rgb = imagecolorat($sourceImage, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            // Simple algorithm: detect light/white background
            // Adjust threshold as needed
            $threshold = 200;
            $isBackground = ($r > $threshold && $g > $threshold && $b > $threshold);

            if (!$isBackground) {
                // Get alpha from source (for PNG)
                $alpha = ($rgb >> 24) & 0xFF;
                // Convert alpha (0-127) to output format
                $outputAlpha = (int)($alpha * 127 / 127);
                $color = imagecolorallocatealpha($outputImage, $r, $g, $b, $outputAlpha);
                imagesetpixel($outputImage, $x, $y, $color);
            }
        }
    }

    // Save as PNG with transparency
    $outputFilename = pathinfo($filename, PATHINFO_FILENAME) . '_nobg.png';
    $outputPath = dirname($inputPath) . '/' . $outputFilename;
    
    imagepng($outputImage, $outputPath, 9);
    
    // Free memory
    imagedestroy($sourceImage);
    imagedestroy($outputImage);

    return $outputPath;
}

/**
 * Alternative: More advanced background removal using edge detection
 * This is a simplified version - for production use AI services
 */
function removeBackgroundAdvanced($inputPath, $filename) {
    $imageInfo = getimagesize($inputPath);
    $width = $imageInfo[0];
    $height = $imageInfo[1];
    $mimeType = $imageInfo['mime'];

    switch ($mimeType) {
        case 'image/jpeg':
        case 'image/jpg':
            $sourceImage = imagecreatefromjpeg($inputPath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($inputPath);
            break;
        default:
            throw new Exception('Unsupported image format');
    }

    $outputImage = imagecreatetruecolor($width, $height);
    imagealphablending($outputImage, false);
    imagesavealpha($outputImage, true);
    $transparent = imagecolorallocatealpha($outputImage, 0, 0, 0, 127);
    imagefill($outputImage, 0, 0, $transparent);

    // Sample background color from corners (average)
    $cornerColors = [];
    $cornerSize = min(10, $width / 4, $height / 4);
    
    // Top-left corner
    for ($y = 0; $y < $cornerSize; $y++) {
        for ($x = 0; $x < $cornerSize; $x++) {
            $rgb = imagecolorat($sourceImage, $x, $y);
            $cornerColors[] = [
                'r' => ($rgb >> 16) & 0xFF,
                'g' => ($rgb >> 8) & 0xFF,
                'b' => $rgb & 0xFF
            ];
        }
    }
    
    // Calculate average background color
    $avgR = array_sum(array_column($cornerColors, 'r')) / count($cornerColors);
    $avgG = array_sum(array_column($cornerColors, 'g')) / count($cornerColors);
    $avgB = array_sum(array_column($cornerColors, 'b')) / count($cornerColors);

    // Process each pixel with color distance
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $rgb = imagecolorat($sourceImage, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            // Calculate color distance from background
            $distance = sqrt(
                pow($r - $avgR, 2) +
                pow($g - $avgG, 2) +
                pow($b - $avgB, 2)
            );

            // Threshold for foreground detection
            $threshold = 50;
            
            if ($distance > $threshold) {
                $alpha = ($rgb >> 24) & 0xFF;
                $outputAlpha = (int)($alpha * 127 / 127);
                $color = imagecolorallocatealpha($outputImage, $r, $g, $b, $outputAlpha);
                imagesetpixel($outputImage, $x, $y, $color);
            }
        }
    }

    $outputFilename = pathinfo($filename, PATHINFO_FILENAME) . '_nobg.png';
    $outputPath = dirname($inputPath) . '/' . $outputFilename;
    
    imagepng($outputImage, $outputPath, 9);
    
    imagedestroy($sourceImage);
    imagedestroy($outputImage);

    return $outputPath;
}

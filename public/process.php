<?php
/**
 * Image Processing & Generation
 * Generates final image with text overlay
 */
require_once '../config/database.php';

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method not allowed');
}

try {
    // Get POST data
    $canvasData = $_POST['canvas_data'] ?? '';
    $userId = $_POST['user_id'] ?? '';
    $customText = $_POST['custom_text'] ?? '';
    
    // Validate required data
    if (empty($canvasData)) {
        throw new Exception('No image data received');
    }
    
    if (empty($userId)) {
        throw new Exception('User ID is required');
    }
    
    // Remove data URL prefix and decode
    if (preg_match('/^data:image\/(jpeg|png);base64,(.*)$/', $canvasData, $matches)) {
        $imageData = base64_decode($matches[2]);
        $format = $matches[1];
    } else {
        throw new Exception('Invalid image data format');
    }
    
    if ($imageData === false || strlen($imageData) === 0) {
        throw new Exception('Failed to decode image data');
    }
    
    // Ensure storage directory exists
    $storageDir = '../storage/results/';
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0755, true);
    }
    
    // Generate unique filename for result
    $resultFilename = bin2hex(random_bytes(16)) . '.jpg';
    $resultPath = $storageDir . $resultFilename;
    
    // Save the generated image
    if (file_put_contents($resultPath, $imageData) === false) {
        throw new Exception('Failed to save generated image');
    }
    
    // Verify file was saved
    if (!file_exists($resultPath)) {
        throw new Exception('Image file was not created');
    }
    
    // Get image dimensions for database
    $imageInfo = @getimagesize($resultPath);
    $width = $imageInfo[0] ?? 0;
    $height = $imageInfo[1] ?? 0;
    
    // Save to database
    $db = (new Database())->connect();
    
    $stmt = $db->prepare("
        INSERT INTO images (user_id, result_image, custom_text) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$userId, 'storage/results/' . $resultFilename, $customText]);
    
    $imageId = $db->lastInsertId();
    
    // Log the generation
    $logEntry = sprintf(
        "[%s] Generated: Image ID %d, User %d, File: %s (%dx%d)\n",
        date('Y-m-d H:i:s'),
        $imageId,
        $userId,
        $resultFilename,
        $width,
        $height
    );
    file_put_contents('../logs/generation.log', $logEntry, FILE_APPEND);
    
    // Check if this is a fetch request (JSON expected) or form submit (HTML expected)
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || 
              !empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
    
    if ($isAjax) {
        // Return JSON for fetch requests
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'filename' => $resultFilename,
            'download_url' => 'download.php?file=' . urlencode($resultFilename),
            'message' => 'Image generated successfully'
        ]);
    } else {
        // Return HTML for form submissions (fallback)
        ?>
        <!DOCTYPE HTML>
        <html>
        <head>
            <title>Download - UCAPIN</title>
            <meta charset="utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1" />
            <meta http-equiv="refresh" content="5;url=index.php" />
            <style>
                body {
                    background: #1a1a2e;
                    color: #fff;
                    font-family: Arial, sans-serif;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    margin: 0;
                    flex-direction: column;
                }
                .success-container {
                    text-align: center;
                    padding: 40px;
                    max-width: 500px;
                }
                .success-icon {
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                    background: #2ed573;
                    color: #fff;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 2.5em;
                    margin: 0 auto 20px;
                    animation: popIn 0.5s ease;
                }
                @keyframes popIn {
                    0% { transform: scale(0); }
                    80% { transform: scale(1.1); }
                    100% { transform: scale(1); }
                }
                h1 {
                    color: #2ed573;
                    margin-bottom: 15px;
                }
                p {
                    color: #aaa;
                    margin-bottom: 20px;
                }
                .download-link {
                    display: inline-block;
                    padding: 15px 30px;
                    background: linear-gradient(135deg, #2ed573, #17c964);
                    color: #fff;
                    text-decoration: none;
                    border-radius: 8px;
                    font-weight: bold;
                    margin: 20px 10px;
                    transition: transform 0.2s;
                }
                .download-link:hover {
                    transform: translateY(-2px);
                }
                .back-link {
                    display: inline-block;
                    padding: 15px 30px;
                    background: #3a3a5a;
                    color: #fff;
                    text-decoration: none;
                    border-radius: 8px;
                    font-weight: bold;
                    margin: 20px 10px;
                    transition: transform 0.2s;
                }
                .back-link:hover {
                    transform: translateY(-2px);
                }
                .spinner {
                    width: 40px;
                    height: 40px;
                    border: 3px solid #3a3a5a;
                    border-top-color: #2ed573;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                    margin: 20px auto;
                }
                @keyframes spin {
                    to { transform: rotate(360deg); }
                }
                .redirect-text {
                    color: #666;
                    font-size: 0.9em;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class="success-container">
                <div class="success-icon">✓</div>
                <h1>Image Generated!</h1>
                <p>Your image has been created successfully and is ready for download.</p>
                
                <a href="download.php?file=<?= urlencode($resultFilename) ?>" class="download-link" download>
                    📥 Download Image
                </a>
                <br>
                <a href="index.php" class="back-link">
                    ← Create Another
                </a>
                
                <div class="spinner"></div>
                <p class="redirect-text">Redirecting to create new image in 5 seconds...</p>
            </div>
            
            <script>
                // Auto-trigger download after short delay
                setTimeout(function() {
                    window.location.href = 'download.php?file=<?= urlencode($resultFilename) ?>';
                }, 800);
            </script>
        </body>
        </html>
        <?php
    }
    exit;
    
} catch (Exception $e) {
    error_log("Generation Error: " . $e->getMessage());
    
    // Log error
    $logEntry = sprintf(
        "[%s] Error: %s\n",
        date('Y-m-d H:i:s'),
        $e->getMessage()
    );
    file_put_contents('../logs/generation.log', $logEntry, FILE_APPEND);
    
    // Check if this is a fetch request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || 
              !empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
    
    if ($isAjax) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    } else {
        // Show error page
        ?>
        <!DOCTYPE HTML>
        <html>
        <head>
            <title>Error - UCAPIN</title>
            <meta charset="utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1" />
            <style>
                body {
                    background: #1a1a2e;
                    color: #fff;
                    font-family: Arial, sans-serif;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    margin: 0;
                }
                .error-container {
                    text-align: center;
                    padding: 40px;
                    max-width: 500px;
                }
                .error-icon {
                    font-size: 4em;
                    margin-bottom: 20px;
                }
                h1 {
                    color: #ff6b6b;
                    margin-bottom: 15px;
                }
                p {
                    color: #aaa;
                    margin-bottom: 20px;
                }
                .btn {
                    display: inline-block;
                    padding: 12px 30px;
                    background: linear-gradient(135deg, #00d4ff, #0099cc);
                    color: #000;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;
                    transition: transform 0.2s;
                }
                .btn:hover {
                    transform: translateY(-2px);
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-icon">⚠️</div>
                <h1>Generation Failed</h1>
                <p><?= htmlspecialchars($e->getMessage()) ?></p>
                <a href="index.php" class="btn">Try Again</a>
            </div>
        </body>
        </html>
        <?php
    }
    exit;
}

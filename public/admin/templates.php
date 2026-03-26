<?php
/**
 * Admin - Template Management
 */
require_once __DIR__ . '/auth.php';
requireAdminAuth();
require_once __DIR__ . '/../../config/database.php';

$db = (new Database())->connect();

// Start output buffering to prevent header issues
ob_start();

// Get flash messages from session
$message = $_SESSION['success_message'] ?? '';
$error = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Handle add template
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    // Debug logging
    error_log('=== Template Upload Debug ===');
    error_log('POST data: ' . print_r($_POST, true));
    error_log('FILES data: ' . print_r($_FILES, true));

    $title = trim($_POST['title'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);

    error_log('Title: "' . $title . '"');
    error_log('Category ID: ' . $categoryId);

    // Validate inputs
    if (empty($title)) {
        $_SESSION['error_message'] = 'Template title is required.';
        error_log('Error: Title empty');
        header('Location: templates.php');
        exit;
    }
    
    if ($categoryId <= 0) {
        $_SESSION['error_message'] = 'Please select a category.';
        error_log('Error: Category invalid');
        header('Location: templates.php');
        exit;
    }
    
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server limit',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form limit',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by extension'
        ];
        $uploadErrorCode = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;
        $_SESSION['error_message'] = 'File upload error: ' . ($uploadErrors[$uploadErrorCode] ?? 'Unknown error (code: ' . $uploadErrorCode . ')');
        error_log('Error: ' . $_SESSION['error_message']);
        header('Location: templates.php');
        exit;
    }
    
    error_log('File uploaded: ' . $_FILES['image']['name']);
    error_log('File size: ' . $_FILES['image']['size']);
    error_log('File type: ' . $_FILES['image']['type']);
    error_log('Temp file: ' . $_FILES['image']['tmp_name']);

    // Validate image type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);

    error_log('Detected MIME type: ' . $mimeType);

    if (!in_array($mimeType, $allowedTypes)) {
        $_SESSION['error_message'] = 'Invalid image type. Only JPG and PNG allowed (detected: ' . $mimeType . ').';
        error_log('Error: Invalid type');
        header('Location: templates.php');
        exit;
    }
    
    if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        $_SESSION['error_message'] = 'Image size must be less than 2MB (current: ' . round($_FILES['image']['size'] / 1024 / 1024, 2) . 'MB).';
        error_log('Error: File too large');
        header('Location: templates.php');
        exit;
    }
    
    // Generate filename and path
    $filename = bin2hex(random_bytes(16)) . '.jpg';
    $uploadPath = '../../templates/' . $filename;

    error_log('Upload path: ' . $uploadPath);

    // Ensure templates directory exists and is writable
    $templatesDir = '../../templates/';
    if (!is_dir($templatesDir)) {
        if (!mkdir($templatesDir, 0755, true)) {
            $_SESSION['error_message'] = 'Failed to create templates directory. Please check permissions.';
            error_log('Error: Cannot create directory');
            header('Location: templates.php');
            exit;
        }
    } elseif (!is_writable($templatesDir)) {
        $_SESSION['error_message'] = 'Templates directory is not writable. Please check permissions.';
        error_log('Error: Directory not writable');
        header('Location: templates.php');
        exit;
    }

    // Move uploaded file
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        $_SESSION['error_message'] = 'Failed to upload image. Please check server permissions.';
        error_log('Error: Cannot move uploaded file');
        header('Location: templates.php');
        exit;
    }
    
    error_log('File moved successfully');

    // Create thumbnail
    $thumbFilename = 'thumb_' . $filename;
    $thumbPath = '../../templates/' . $thumbFilename;

    $sourceImage = @imagecreatefromstring(file_get_contents($uploadPath));
    if (!$sourceImage) {
        @unlink($uploadPath);
        $_SESSION['error_message'] = 'Failed to process image. GD library may not be available or image is corrupted.';
        error_log('Error: Cannot create image from file');
        header('Location: templates.php');
        exit;
    }
    
    $originalWidth = imagesx($sourceImage);
    $originalHeight = imagesy($sourceImage);
    $thumbWidth = 300;
    $thumbHeight = ($originalHeight / $originalWidth) * $thumbWidth;

    $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
    imagecopyresampled($thumbImage, $sourceImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $originalWidth, $originalHeight);

    if (!imagejpeg($thumbImage, $thumbPath, 85)) {
        @unlink($uploadPath);
        imagedestroy($sourceImage);
        imagedestroy($thumbImage);
        $_SESSION['error_message'] = 'Failed to create thumbnail. GD library may not be available.';
        error_log('Error: Cannot create thumbnail');
        header('Location: templates.php');
        exit;
    }
    
    imagedestroy($sourceImage);
    imagedestroy($thumbImage);
    error_log('Thumbnail created successfully');

    // Save to database
    $stmt = $db->prepare("INSERT INTO templates (title, file_path, thumbnail_path, category_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, 'templates/' . $filename, 'templates/' . $thumbFilename, $categoryId]);

    $_SESSION['success_message'] = 'Template added successfully!';
    error_log('Template saved to database with ID: ' . $db->lastInsertId());
    error_log('=== End Debug ===');
    header('Location: templates.php');
    exit;
}

// Handle delete
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['id'];
    $stmt = $db->prepare("SELECT file_path, thumbnail_path FROM templates WHERE id = ?");
    $stmt->execute([$id]);
    $template = $stmt->fetch();

    if ($template) {
        if (file_exists('../../' . $template['file_path'])) {
            unlink('../../' . $template['file_path']);
        }
        if (file_exists('../../' . $template['thumbnail_path'])) {
            unlink('../../' . $template['thumbnail_path']);
        }
        $db->prepare("DELETE FROM templates WHERE id = ?")->execute([$id]);
        $_SESSION['success_message'] = 'Template deleted successfully!';
        header('Location: templates.php');
        exit;
    }
}

// Flush output buffer and include header
ob_end_flush();
require_once __DIR__ . '/includes/header.php';

// Get template categories
$templateCategories = $db->query("SELECT * FROM template_categories ORDER BY name")->fetchAll();

// Get all templates
$templates = $db->query("
    SELECT t.*, tc.name as category_name 
    FROM templates t
    LEFT JOIN template_categories tc ON t.category_id = tc.id
    ORDER BY t.created_at DESC
")->fetchAll();
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>📐 Template Management</h1>
            <p>Manage image templates and categories for users</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('addTemplateModal')">
            ➕ Add Template
        </button>
    </div>
    
    <?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <!-- Template Categories Overview -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <h2>📁 Template Categories</h2>
            <a href="categories.php" class="btn-link">Manage Categories →</a>
        </div>
        <div class="card-body">
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php
                $templateCats = $db->query("
                    SELECT tc.*, COUNT(t.id) as template_count 
                    FROM template_categories tc
                    LEFT JOIN templates t ON tc.id = t.category_id
                    GROUP BY tc.id
                    ORDER BY tc.name
                ")->fetchAll();
                ?>
                <?php foreach ($templateCats as $cat): ?>
                <span style="background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%); padding: 10px 18px; border-radius: 20px; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9em;">
                    <span>📁</span>
                    <strong><?= htmlspecialchars($cat['name']) ?></strong>
                    <span style="color: #666; font-size: 0.85em;"><?= $cat['template_count'] ?> templates</span>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Templates Gallery -->
    <div class="card">
        <div class="card-header">
            <h2>📐 All Templates</h2>
        </div>
        <div class="card-body">
            <div class="gallery-grid">
                <?php foreach ($templates as $template): ?>
                <div class="gallery-item">
                    <img src="../../<?= htmlspecialchars($template['thumbnail_path'] ?? $template['file_path']) ?>" alt="<?= htmlspecialchars($template['title']) ?>" class="gallery-image">
                    <div class="gallery-info">
                        <h3><?= htmlspecialchars($template['title']) ?></h3>
                        <p><?= htmlspecialchars($template['category_name'] ?? 'Uncategorized') ?></p>
                        <div class="gallery-actions">
                            <a href="../../<?= htmlspecialchars($template['file_path']) ?>" target="_blank" class="btn btn-small btn-info">View</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this template?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $template['id'] ?>">
                                <button type="submit" class="btn btn-small btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($templates)): ?>
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 4em; margin-bottom: 20px;">📐</div>
                <h3 style="color: #1a1a2e; margin-bottom: 10px;">No Templates Yet</h3>
                <p style="color: #666;">Click "Add Template" to create your first template</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Template Modal -->
<div id="addTemplateModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>➕ Add New Template</h2>
            <button class="modal-close" onclick="closeModal('addTemplateModal')">×</button>
        </div>
        <form method="POST" enctype="multipart/form-data" id="addTemplateForm">
            <input type="hidden" name="action" value="add">

            <div class="modal-body">
                <div class="form-group">
                    <label>Template Title *</label>
                    <input type="text" name="title" required placeholder="e.g., Blue Gradient Background">
                </div>

                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($templateCategories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Template Image *</label>
                    <input type="file" name="image" id="templateImage" accept="image/jpeg,image/png" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 10px; background: #fff;">
                    <small style="color: #999; display: block; margin-top: 5px;">Max 2MB • JPG, PNG</small>
                    <div id="imagePreview" style="margin-top: 15px; display: none;">
                        <p style="margin-bottom: 8px; font-weight: 600; color: #1a1a2e;">Preview:</p>
                        <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 10px; border: 2px solid #e0e0e0; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addTemplateModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Add Template</button>
            </div>
        </form>
    </div>
</div>

<script>
// Simple modal functions
function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});

// Image preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('templateImage');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const addTemplateForm = document.getElementById('addTemplateForm');
    const submitBtn = document.getElementById('submitBtn');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    alert('Invalid image type. Only JPG and PNG are allowed.');
                    imageInput.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }

                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Image size must be less than 2MB (current: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB).');
                    imageInput.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImg.src = event.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
            }
        });
    }

    // Handle form submission
    if (addTemplateForm) {
        addTemplateForm.addEventListener('submit', function(e) {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '⏳ Uploading...';
            
            // Let the form submit normally (PHP will handle it)
            // After successful submit, page will reload and show success message
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>

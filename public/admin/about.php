<?php
/**
 * Admin - About Page Management
 */
require_once 'includes/header.php';

$message = '';

// Fetch current about content
$about = $db->query("SELECT * FROM about LIMIT 1")->fetch();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if (!empty($title) && !empty($content)) {
        if ($about) {
            $db->prepare("UPDATE about SET title = ?, content = ? WHERE id = ?")
              ->execute([$title, $content, $about['id']]);
        } else {
            $db->prepare("INSERT INTO about (title, content) VALUES (?, ?)")
              ->execute([$title, $content]);
        }
        $message = 'About page updated successfully.';
        $about = $db->query("SELECT * FROM about LIMIT 1")->fetch();
    }
}
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>ℹ️ About Page Management</h1>
            <p>Edit the about page content</p>
        </div>
    </div>
    
    <?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h2>ℹ️ Edit About Content</h2>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" required value="<?= htmlspecialchars($about['title'] ?? 'About UCAPIN') ?>">
                </div>
                
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" rows="10" required><?= htmlspecialchars($about['content'] ?? '') ?></textarea>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                    <a href="../about.php" target="_blank" class="btn btn-secondary">👁️ Preview</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

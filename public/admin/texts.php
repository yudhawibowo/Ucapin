<?php
/**
 * Admin - Text References Management
 */
require_once 'includes/header.php';

$message = '';
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $content = trim($_POST['content'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    
    if (!empty($content) && $categoryId > 0) {
        $db->prepare("INSERT INTO text_references (category_id, content) VALUES (?, ?)")
          ->execute([$categoryId, $content]);
        $message = 'Text reference added successfully.';
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->prepare("DELETE FROM text_references WHERE id = ?")->execute([$id]);
    $message = 'Text reference deleted successfully.';
}

// Fetch all texts with categories
$texts = $db->query("
    SELECT t.*, c.name as category_name 
    FROM text_references t
    LEFT JOIN categories c ON t.category_id = c.id
    ORDER BY t.created_at DESC
")->fetchAll();
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>📝 Text References</h1>
            <p>Manage text reference library for users</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('addTextModal')">
            ➕ Add Text
        </button>
    </div>
    
    <?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <!-- Category Overview -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <h2>📁 Categories</h2>
            <a href="categories.php" class="btn-link">Manage Categories →</a>
        </div>
        <div class="card-body">
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php
                $catStats = $db->query("
                    SELECT c.*, COUNT(t.id) as text_count 
                    FROM categories c
                    LEFT JOIN text_references t ON c.id = t.category_id
                    GROUP BY c.id
                    ORDER BY c.name
                ")->fetchAll();
                ?>
                <?php foreach ($catStats as $cat): ?>
                <span style="background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%); padding: 10px 18px; border-radius: 20px; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9em;">
                    <span>📝</span>
                    <strong><?= htmlspecialchars($cat['name']) ?></strong>
                    <span style="color: #666; font-size: 0.85em;"><?= $cat['text_count'] ?> texts</span>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Text References Table -->
    <div class="card">
        <div class="card-header">
            <h2>📝 All Text References</h2>
        </div>
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Content</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($texts as $text): ?>
                    <tr>
                        <td>#<?= $text['id'] ?></td>
                        <td><span style="background: #eef2f7; padding: 4px 12px; border-radius: 15px; font-size: 0.85em;"><?= htmlspecialchars($text['category_name'] ?? 'N/A') ?></span></td>
                        <td><?= htmlspecialchars($text['content']) ?></td>
                        <td><?= date('M d, Y', strtotime($text['created_at'])) ?></td>
                        <td>
                            <a href="?delete=<?= $text['id'] ?>" class="btn btn-small btn-danger" onclick="return confirm('Delete this text?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Text Modal -->
<div id="addTextModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>➕ Add New Text Reference</h2>
            <button class="modal-close" onclick="closeModal('addTextModal')">×</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            
            <div class="modal-body">
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Text Content *</label>
                    <textarea name="content" rows="4" required placeholder="Enter text reference..."></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addTextModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Text</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

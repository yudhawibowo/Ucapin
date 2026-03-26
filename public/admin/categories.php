<?php
/**
 * Admin - Category Management (Text & Template Categories)
 */
require_once 'includes/header.php';

$message = '';
$error = '';
$categoryType = $_GET['type'] ?? 'text';

// Handle add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name'] ?? '');
    $type = $_POST['type'] ?? 'text';
    $description = trim($_POST['description'] ?? '');
    
    if (!empty($name)) {
        if ($type === 'template') {
            $stmt = $db->prepare("INSERT INTO template_categories (name, description) VALUES (?, ?)");
            $stmt->execute([$name, $description]);
        } else {
            $stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
        }
        $message = 'Category added successfully!';
    } else {
        $error = 'Category name is required.';
    }
}

// Handle delete
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['id'];
    $type = $_POST['type'];
    
    if ($type === 'template') {
        $count = $db->prepare("SELECT COUNT(*) FROM templates WHERE category_id = ?");
        $count->execute([$id]);
        if ($count->fetchColumn() > 0) {
            $error = 'Cannot delete category with existing templates.';
        } else {
            $db->prepare("DELETE FROM template_categories WHERE id = ?")->execute([$id]);
            $message = 'Category deleted successfully!';
        }
    } else {
        $count = $db->prepare("SELECT COUNT(*) FROM text_references WHERE category_id = ?");
        $count->execute([$id]);
        if ($count->fetchColumn() > 0) {
            $error = 'Cannot delete category with existing text references.';
        } else {
            $db->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
            $message = 'Category deleted successfully!';
        }
    }
}

// Get categories based on type
if ($categoryType === 'template') {
    $categories = $db->query("
        SELECT c.*, COUNT(t.id) as item_count 
        FROM template_categories c
        LEFT JOIN templates t ON c.id = t.category_id
        GROUP BY c.id
        ORDER BY c.name
    ")->fetchAll();
} else {
    $categories = $db->query("
        SELECT c.*, COUNT(t.id) as item_count 
        FROM categories c
        LEFT JOIN text_references t ON c.id = t.category_id
        GROUP BY c.id
        ORDER BY c.name
    ")->fetchAll();
}
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>📁 Category Management</h1>
            <p>Manage categories for text references and templates</p>
        </div>
    </div>
    
    <?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <!-- Category Type Tabs -->
    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <a href="?type=text" style="padding: 12px 24px; background: <?= $categoryType === 'text' ? 'linear-gradient(135deg, #00d4ff 0%, #0099cc 100%)' : '#fff' ?>; color: <?= $categoryType === 'text' ? '#fff' : '#666' ?>; text-decoration: none; border-radius: 10px; font-weight: 600; transition: all 0.3s;">
            📝 Text Reference Categories
        </a>
        <a href="?type=template" style="padding: 12px 24px; background: <?= $categoryType === 'template' ? 'linear-gradient(135deg, #00d4ff 0%, #0099cc 100%)' : '#fff' ?>; color: <?= $categoryType === 'template' ? '#fff' : '#666' ?>; text-decoration: none; border-radius: 10px; font-weight: 600; transition: all 0.3s;">
            📐 Template Categories
        </a>
    </div>
    
    <!-- Add Category Card -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <h2>➕ Add New Category</h2>
        </div>
        <div class="card-body">
            <form method="POST" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="type" value="<?= $categoryType ?>">
                
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Category Name *</label>
                    <input type="text" name="name" required placeholder="<?= $categoryType === 'template' ? 'e.g., Eid Mubarak' : 'e.g., Motivation' ?>" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 10px;">
                </div>
                
                <?php if ($categoryType === 'template'): ?>
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Description</label>
                    <input type="text" name="description" placeholder="Brief description" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 10px;">
                </div>
                <?php endif; ?>
                
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">&nbsp;</label>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Categories Table -->
    <div class="card">
        <div class="card-header">
            <h2>📋 <?= $categoryType === 'text' ? 'Text Reference' : 'Template' ?> Categories</h2>
        </div>
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <?php if ($categoryType === 'template'): ?>
                        <th>Description</th>
                        <?php endif; ?>
                        <th>Items</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td>#<?= $cat['id'] ?></td>
                        <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                        <?php if ($categoryType === 'template'): ?>
                        <td><?= htmlspecialchars($cat['description'] ?? '-') ?></td>
                        <?php endif; ?>
                        <td><span style="background: #eef2f7; padding: 4px 12px; border-radius: 15px; font-size: 0.85em;"><?= $cat['item_count'] ?> items</span></td>
                        <td><?= date('M d, Y', strtotime($cat['created_at'])) ?></td>
                        <td>
                            <?php if ($cat['item_count'] == 0): ?>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this category?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                <input type="hidden" name="type" value="<?= $categoryType ?>">
                                <button type="submit" class="btn btn-small btn-danger">Delete</button>
                            </form>
                            <?php else: ?>
                            <span style="color: #999;">Has items</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

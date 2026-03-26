<?php
/**
 * Admin - User Management
 */
require_once 'includes/header.php';

$message = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    $message = 'User deleted successfully.';
}

// Search
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM users";
$params = [];

if ($search) {
    $query .= " WHERE name LIKE ? OR email LIKE ?";
    $params = ["%$search%", "%$search%"];
}

$query .= " ORDER BY created_at DESC";
$users = $db->prepare($query);
$users->execute($params);
$users = $users->fetchAll();
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>👥 User Management</h1>
            <p>Manage user accounts and view activity</p>
        </div>
    </div>
    
    <?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h2>👥 All Users (<?= count($users) ?>)</h2>
        </div>
        <div class="card-body">
            <div style="margin-bottom: 20px;">
                <form method="GET" style="display: flex; gap: 10px; max-width: 500px;">
                    <input type="text" name="search" placeholder="🔍 Search by name or email..." value="<?= htmlspecialchars($search) ?>" style="flex: 1; padding: 12px; border: 2px solid #e0e0e0; border-radius: 10px;">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search): ?>
                    <a href="users.php" class="btn btn-secondary">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>#<?= $user['id'] ?></td>
                        <td><strong><?= htmlspecialchars($user['name']) ?></strong></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                        <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <a href="images.php?user_id=<?= $user['id'] ?>" class="btn btn-small btn-info">View Images</a>
                            <a href="?delete=<?= $user['id'] ?>" class="btn btn-small btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

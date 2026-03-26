<?php
/**
 * Admin Dashboard - UCAPIN
 */
require_once 'includes/header.php';

// Get statistics
$stats = [];
$stats['users'] = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$stats['images'] = $db->query("SELECT COUNT(*) FROM images")->fetchColumn();
$stats['templates'] = $db->query("SELECT COUNT(*) FROM templates")->fetchColumn();
$stats['texts'] = $db->query("SELECT COUNT(*) FROM text_references")->fetchColumn();
$stats['categories'] = $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();

// Recent users
$recentUsers = $db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Recent images
$recentImages = $db->query("
    SELECT i.*, u.name as user_name 
    FROM images i 
    JOIN users u ON i.user_id = u.id 
    ORDER BY i.created_at DESC LIMIT 5
")->fetchAll();
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>📊 Dashboard</h1>
            <p>Welcome back, <?= htmlspecialchars(getAdminUsername()) ?>! Here's what's happening today.</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-users">
            <div class="stat-icon">👥</div>
            <div class="stat-details">
                <h3><?= number_format($stats['users']) ?></h3>
                <p>Total Users</p>
            </div>
        </div>
        
        <div class="stat-card stat-images">
            <div class="stat-icon">🖼️</div>
            <div class="stat-details">
                <h3><?= number_format($stats['images']) ?></h3>
                <p>Images Generated</p>
            </div>
        </div>
        
        <div class="stat-card stat-templates">
            <div class="stat-icon">📐</div>
            <div class="stat-details">
                <h3><?= number_format($stats['templates']) ?></h3>
                <p>Templates</p>
            </div>
        </div>
        
        <div class="stat-card stat-texts">
            <div class="stat-icon">📝</div>
            <div class="stat-details">
                <h3><?= number_format($stats['texts']) ?></h3>
                <p>Text References</p>
            </div>
        </div>
        
        <div class="stat-card stat-categories">
            <div class="stat-icon">📁</div>
            <div class="stat-details">
                <h3><?= number_format($stats['categories']) ?></h3>
                <p>Categories</p>
            </div>
        </div>
    </div>

    <!-- Recent Data -->
    <div class="card">
        <div class="card-header">
            <h2>👥 Recent Users</h2>
            <a href="users.php" class="btn-link">View All →</a>
        </div>
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentUsers as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                        <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Images Gallery -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h2>🖼️ Recent Generated Images</h2>
            <a href="images.php" class="btn-link">View All →</a>
        </div>
        <div class="card-body">
            <div class="gallery-grid">
                <?php foreach ($recentImages as $image): ?>
                <div class="gallery-item">
                    <img src="../../<?= htmlspecialchars($image['result_image']) ?>" alt="Result" class="gallery-image">
                    <div class="gallery-info">
                        <h3><?= htmlspecialchars($image['user_name']) ?></h3>
                        <p><?= htmlspecialchars(substr($image['custom_text'] ?? 'No text', 0, 40)) ?>...</p>
                        <div class="gallery-actions">
                            <a href="../../<?= htmlspecialchars($image['result_image']) ?>" target="_blank" class="btn btn-small btn-info">View</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h2>⚡ Quick Actions</h2>
        </div>
        <div class="card-body">
            <div class="gallery-grid">
                <a href="templates.php" class="gallery-item quick-action">
                    <div class="gallery-info" style="text-align: center; padding: 30px;">
                        <div style="font-size: 3em; margin-bottom: 15px;">📐</div>
                        <h3>Manage Templates</h3>
                        <p>Add or edit image templates</p>
                    </div>
                </a>
                <a href="texts.php" class="gallery-item quick-action">
                    <div class="gallery-info" style="text-align: center; padding: 30px;">
                        <div style="font-size: 3em; margin-bottom: 15px;">📝</div>
                        <h3>Text References</h3>
                        <p>Manage text library</p>
                    </div>
                </a>
                <a href="categories.php" class="gallery-item quick-action">
                    <div class="gallery-info" style="text-align: center; padding: 30px;">
                        <div style="font-size: 3em; margin-bottom: 15px;">📁</div>
                        <h3>Categories</h3>
                        <p>Organize content</p>
                    </div>
                </a>
                <a href="users.php" class="gallery-item quick-action">
                    <div class="gallery-info" style="text-align: center; padding: 30px;">
                        <div style="font-size: 3em; margin-bottom: 15px;">👥</div>
                        <h3>Users</h3>
                        <p>Manage user accounts</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.stat-users .stat-icon { color: #667eea; }
.stat-images .stat-icon { color: #f093fb; }
.stat-templates .stat-icon { color: #4facfe; }
.stat-texts .stat-icon { color: #43e97b; }
.stat-categories .stat-icon { color: #fa709a; }

.quick-action {
    text-decoration: none;
    color: inherit;
}
.quick-action:hover {
    transform: translateY(-5px);
}
</style>

<?php require_once 'includes/footer.php'; ?>

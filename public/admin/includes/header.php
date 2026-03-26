<?php
/**
 * Admin Header - UCAPIN
 * Modern sidebar navigation with DataTables
 */
require_once 'auth.php';
requireAdminAuth();
require_once '../../config/database.php';

$db = (new Database())->connect();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

$navItems = [
    ['id' => 'dashboard', 'icon' => '📊', 'label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['id' => 'templates', 'icon' => '📐', 'label' => 'Templates', 'url' => 'templates.php'],
    ['id' => 'categories', 'icon' => '📁', 'label' => 'Categories', 'url' => 'categories.php'],
    ['id' => 'texts', 'icon' => '📝', 'label' => 'Text References', 'url' => 'texts.php'],
    ['id' => 'users', 'icon' => '👥', 'label' => 'Users', 'url' => 'users.php'],
    ['id' => 'images', 'icon' => '🖼️', 'label' => 'Generated Images', 'url' => 'images.php'],
    ['id' => 'about', 'icon' => 'ℹ️', 'label' => 'About Page', 'url' => 'about.php'],
];
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Admin Panel - UCAPIN</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
        
        .admin-layout { display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s;
            z-index: 1000;
        }
        
        .sidebar-header { padding: 30px 25px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .sidebar-logo { display: flex; align-items: center; gap: 15px; margin-bottom: 10px; }
        .sidebar-logo .icon {
            width: 50px; height: 50px;
            background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5em;
        }
        .sidebar-logo h1 { font-size: 1.5em; font-weight: 700; }
        .sidebar-logo p { color: rgba(255, 255, 255, 0.6); font-size: 0.85em; }
        
        .sidebar-nav { padding: 20px 15px; }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 18px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: all 0.3s;
        }
        .nav-item:hover { background: rgba(255, 255, 255, 0.1); color: #fff; }
        .nav-item.active {
            background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
            color: #fff;
        }
        .nav-item .icon { font-size: 1.3em; width: 25px; text-align: center; }
        .nav-item .label { font-weight: 500; font-size: 0.95em; }
        
        .sidebar-footer {
            padding: 20px 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 20px;
        }
        .user-info {
            display: flex; align-items: center; gap: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .user-avatar {
            width: 45px; height: 45px;
            background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2em;
        }
        .user-details { flex: 1; }
        .user-name { font-weight: 600; font-size: 0.95em; }
        .user-role { color: rgba(255, 255, 255, 0.6); font-size: 0.8em; }
        .logout-btn {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 12px;
            background: rgba(255, 71, 87, 0.2);
            color: #ff4757;
            border: 1px solid #ff4757;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            font-weight: 600;
        }
        .logout-btn:hover { background: #ff4757; color: #fff; }
        
        /* Main Content */
        .main-wrapper { flex: 1; margin-left: 280px; transition: margin-left 0.3s; }
        .main-content { padding: 30px; }
        
        .topbar {
            background: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eef2f7;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .menu-toggle {
            display: none;
            background: none; border: none;
            font-size: 1.5em; cursor: pointer; color: #1a1a2e;
        }
        .topbar-actions { display: flex; align-items: center; gap: 15px; }
        .topbar-link {
            color: #666; text-decoration: none;
            padding: 8px 16px; background: #f5f7fa;
            border-radius: 8px; transition: all 0.3s;
        }
        .topbar-link:hover { background: #00d4ff; color: #fff; }
        
        /* Page Header */
        .page-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; flex-wrap: wrap; gap: 15px;
        }
        .page-header h1 { color: #1a1a2e; font-size: 2em; margin-bottom: 5px; }
        .page-header p { color: #666; font-size: 1em; }
        
        /* Buttons */
        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-align: center;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
            color: #fff;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 212, 255, 0.3);
        }
        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }
        .btn-danger {
            background: #ff4757;
            color: #fff;
        }
        .btn-success {
            background: #2ed573;
            color: #fff;
        }
        .btn-info {
            background: #4facfe;
            color: #fff;
        }
        .btn-small {
            padding: 6px 12px;
            font-size: 0.85em;
        }
        .btn-link {
            color: #00d4ff;
            text-decoration: none;
            font-weight: 600;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px 16px;
        }
        .btn-link:hover { color: #0099cc; }
        
        /* Cards */
        .card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid #eef2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-header h2 { font-size: 1.3em; color: #1a1a2e; }
        .card-body { padding: 25px; }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .stat-icon { font-size: 3em; line-height: 1; }
        .stat-details h3 { font-size: 2em; color: #1a1a2e; margin-bottom: 5px; }
        .stat-details p { color: #666; font-size: 0.9em; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* DataTables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table thead th {
            background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
            color: #fff;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85em;
        }
        .data-table tbody td {
            padding: 15px;
            border-bottom: 1px solid #eef2f7;
            color: #333;
        }
        .data-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        /* Gallery Grid */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        .gallery-item {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        .gallery-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .gallery-info {
            padding: 15px;
        }
        .gallery-info h3 {
            font-size: 0.95em;
            color: #1a1a2e;
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .gallery-info p {
            color: #666;
            font-size: 0.85em;
            margin-bottom: 12px;
        }
        .gallery-actions {
            display: flex;
            gap: 8px;
        }
        .gallery-actions .btn {
            flex: 1;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalSlideIn 0.3s ease;
        }
        @keyframes modalSlideIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .modal-header {
            padding: 25px;
            border-bottom: 1px solid #eef2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h2 { font-size: 1.3em; color: #1a1a2e; }
        .modal-close {
            background: none; border: none;
            font-size: 2em; cursor: pointer;
            color: #999; line-height: 1;
        }
        .modal-close:hover { color: #333; }
        .modal-body { padding: 25px; }
        .modal-footer {
            padding: 20px 25px;
            border-top: 1px solid #eef2f7;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        /* Forms */
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #00d4ff;
        }
        
        /* Alerts */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .menu-toggle { display: block; }
            .page-header { flex-direction: column; align-items: flex-start; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .gallery-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
            .gallery-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <div class="icon">🎨</div>
                    <div>
                        <h1>UCAPIN</h1>
                        <p>Admin Panel</p>
                    </div>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <?php foreach ($navItems as $item): ?>
                <a href="<?= $item['url'] ?>" class="nav-item <?= $currentPage === $item['id'] ? 'active' : '' ?>">
                    <span class="icon"><?= $item['icon'] ?></span>
                    <span class="label"><?= $item['label'] ?></span>
                </a>
                <?php endforeach; ?>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">👤</div>
                    <div class="user-details">
                        <div class="user-name"><?= htmlspecialchars(getAdminUsername()) ?></div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
                <a href="logout.php" class="logout-btn">
                    <span>🚪</span>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        
        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <!-- Topbar -->
            <div class="topbar">
                <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
                <div class="topbar-actions">
                    <a href="../index.php" class="topbar-link" target="_blank">🌐 View Site</a>
                </div>
            </div>

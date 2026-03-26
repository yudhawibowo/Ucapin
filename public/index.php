<?php
/**
 * UCAPIN - Main User Interface (Step-by-Step Wizard v1.3.2)
 * Image Text Generator Platform
 */
session_start();
require_once '../config/database.php';

$db = (new Database())->connect();

// Restore userData from session if exists (for pagination/filter)
$userData = $_SESSION['user_data'] ?? null;

// Fetch categories
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Fetch templates with pagination and optional category filter
$templatesPerPage = 12;
$page = isset($_GET['template_page']) ? (int)$_GET['template_page'] : 1;
$offset = ($page - 1) * $templatesPerPage;
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Build WHERE clause for category filter (using prepared statement pattern)
$categoryCondition = $categoryFilter > 0 ? "WHERE t.category_id = :category_id" : "";
$countQuery = "SELECT COUNT(*) as count FROM templates " . ($categoryFilter > 0 ? "WHERE category_id = :category_id" : "");

// Get total count
$countStmt = $db->prepare($countQuery);
if ($categoryFilter > 0) {
    $countStmt->bindValue(':category_id', $categoryFilter, PDO::PARAM_INT);
}
$countStmt->execute();
$totalTemplates = $countStmt->fetch()['count'];
$totalPages = ceil($totalTemplates / $templatesPerPage);

// Fetch templates with category and pagination
$templateQuery = "
    SELECT t.*, tc.name as category_name 
    FROM templates t 
    LEFT JOIN template_categories tc ON t.category_id = tc.id 
    $categoryCondition
    ORDER BY t.created_at DESC 
    LIMIT $templatesPerPage OFFSET $offset
";
$stmt = $db->prepare($templateQuery);
if ($categoryFilter > 0) {
    $stmt->bindValue(':category_id', $categoryFilter, PDO::PARAM_INT);
}
$stmt->execute();
$templates = $stmt->fetchAll();

// Fetch template categories for filter
$templateCategories = $db->query("SELECT * FROM template_categories ORDER BY name")->fetchAll();

// Handle form submission
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));

    // Validation
    if (empty($name) || empty($email)) {
        $error = 'Name and Email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (!empty($phone) && !preg_match('/^[0-9+\-\s()]+$/', $phone)) {
        $error = 'Invalid phone number format.';
    } else {
        // Save or get user
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $stmt = $db->prepare("INSERT INTO users (name, email, phone) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $phone]);
            $userId = $db->lastInsertId();
        } else {
            $userId = $user['id'];
        }

        $userData = [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ];
        
        // Save to session for pagination/filter persistence
        $_SESSION['user_data'] = $userData;
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>UCAPIN - Image Text Generator</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" href="../assets/css/main.css" />
    <link rel="stylesheet" href="../assets/css/ucapin.css" />
    <noscript><link rel="stylesheet" href="../assets/css/noscript.css" /></noscript>
    <style>
        /* Centered Form Container */
        .form-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 30px;
            background: rgba(30, 30, 50, 0.6);
            border-radius: 10px;
            border: 1px solid #4a4a6a;
        }
        
        .form-container h2 {
            color: #fff;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .form-container > p {
            text-align: center;
            color: #aaa;
            margin-bottom: 25px;
        }
        
        .form-container .fields {
            margin-bottom: 20px;
        }
        
        .form-container .field {
            margin-bottom: 15px;
        }
        
        .form-container label {
            color: #00d4ff;
            font-size: 0.9em;
            margin-bottom: 5px;
            display: block;
        }
        
        .form-container input[type="text"],
        .form-container input[type="email"] {
            width: 100%;
            padding: 12px;
            background: #2a2a4a;
            border: 1px solid #4a4a6a;
            color: #fff;
            border-radius: 5px;
            transition: border-color 0.2s;
        }
        
        .form-container input:focus {
            border-color: #00d4ff;
            outline: none;
        }
        
        .form-container .button.primary {
            width: 100%;
            background: linear-gradient(135deg, #00d4ff, #0099cc);
            color: #000;
            font-weight: bold;
            border: none;
            padding: 14px 28px;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            font-size: 1em;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .form-container .button.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 212, 255, 0.4);
        }
        
        /* Wizard Container - Fixed Width */
        .wizard-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Wizard Steps */
        .wizard-steps {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            position: relative;
            gap: 20px;
        }
        
        .wizard-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: #3a3a5a;
            z-index: 0;
        }
        
        .step {
            text-align: center;
            position: relative;
            z-index: 1;
            flex: 1;
            max-width: 150px;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #3a3a5a;
            color: #aaa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .step.active .step-number {
            background: #00d4ff;
            color: #000;
        }
        
        .step.completed .step-number {
            background: #2ed573;
            color: #fff;
        }
        
        .step-label {
            color: #aaa;
            font-size: 0.85em;
        }
        
        .step.active .step-label {
            color: #00d4ff;
            font-weight: bold;
        }
        
        /* Wizard Panels - Fixed Height */
        .wizard-panel {
            display: none;
            background: rgba(30, 30, 50, 0.6);
            border-radius: 10px;
            border: 1px solid #4a4a6a;
            padding: 30px;
            margin-bottom: 20px;
            min-height: 550px;
        }
        
        .wizard-panel.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .wizard-panel h3 {
            color: #fff;
            margin-bottom: 20px;
            text-align: center;
        }
        
        /* Image Selection */
        .image-selection {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .image-option {
            cursor: pointer;
            border: 3px solid transparent;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.2s;
            position: relative;
        }
        
        .image-option:hover {
            border-color: #00d4ff;
            transform: scale(1.05);
        }
        
        .image-option.selected {
            border-color: #2ed573;
        }
        
        .image-option img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            display: block;
        }
        
        .image-option .label {
            padding: 8px;
            background: #2a2a4a;
            color: #fff;
            text-align: center;
            font-size: 0.85em;
        }
        
        /* Upload Area */
        .upload-area {
            border: 3px dashed #4a4a6a;
            border-radius: 10px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 20px;
        }
        
        .upload-area:hover {
            border-color: #00d4ff;
            background: rgba(0, 212, 255, 0.05);
        }
        
        .upload-area.dragover {
            border-color: #2ed573;
            background: rgba(46, 213, 115, 0.1);
        }
        
        .upload-area .icon {
            font-size: 3em;
            margin-bottom: 15px;
        }
        
        .upload-area p {
            color: #aaa;
            margin-bottom: 5px;
        }
        
        .upload-area .small {
            color: #666;
            font-size: 0.85em;
        }
        
        /* Canvas Area */
        .canvas-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .canvas-container-wrapper {
            border: 2px solid #4a4a6a;
            border-radius: 8px;
            overflow: hidden;
            background: #1a1a2e;
            position: relative;
            max-width: 100%;
        }
        
        .canvas-container {
            position: relative;
        }

        #imageCanvas {
            display: block;
            max-width: 100%;
        }

        /* Template Gallery with Categories */
        .template-gallery-wrapper {
            background: rgba(30, 30, 50, 0.4);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .template-category-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: center;
        }

        .template-filter-btn {
            padding: 8px 16px;
            background: #2a2a4a;
            border: 1px solid #4a4a6a;
            color: #aaa;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.85em;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .template-filter-btn:hover,
        .template-filter-btn.active {
            background: #00d4ff;
            color: #000;
            border-color: #00d4ff;
        }

        .template-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .template-card {
            background: #2a2a4a;
            border-radius: 10px;
            overflow: hidden;
            border: 3px solid transparent;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .template-card:hover {
            border-color: #00d4ff;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.2);
        }

        .template-card.selected {
            border-color: #2ed573;
            box-shadow: 0 10px 30px rgba(46, 213, 115, 0.3);
        }

        .template-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            display: block;
        }

        .template-card-info {
            padding: 12px;
        }

        .template-card-title {
            color: #fff;
            font-size: 0.9em;
            font-weight: 600;
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .template-card-category {
            color: #00d4ff;
            font-size: 0.75em;
            display: inline-block;
            background: rgba(0, 212, 255, 0.15);
            padding: 3px 8px;
            border-radius: 10px;
        }

        /* Template Pagination */
        .template-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .template-pagination a,
        .template-pagination span {
            padding: 8px 14px;
            background: #2a2a4a;
            border: 1px solid #4a4a6a;
            color: #aaa;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9em;
            transition: all 0.2s;
        }

        .template-pagination a:hover {
            background: #00d4ff;
            color: #000;
            border-color: #00d4ff;
        }

        .template-pagination .current {
            background: #00d4ff;
            color: #000;
            border-color: #00d4ff;
            font-weight: bold;
        }

        .template-pagination .disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Image Size Control */
        .size-control {
            background: #2a2a4a;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .size-control label {
            color: #00d4ff;
            font-size: 0.9em;
        }
        
        .size-control input[type="range"] {
            width: 150px;
        }
        
        .size-control .value-display {
            color: #fff;
            font-weight: bold;
            min-width: 50px;
        }
        
        /* Text Options */
        .text-options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .text-option-card {
            background: #2a2a4a;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #4a4a6a;
        }
        
        .text-option-card label {
            color: #00d4ff;
            font-size: 0.85em;
            display: block;
            margin-bottom: 8px;
        }
        
        .text-option-card input[type="range"] {
            width: 100%;
        }
        
        .text-option-card input[type="color"] {
            width: 100%;
            height: 40px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .text-option-card .value-display {
            color: #aaa;
            font-size: 0.85em;
            margin-top: 5px;
        }
        
        /* Category Tabs - Centered */
        .category-tabs-wrapper {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .category-tabs {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            align-items: center;
        }
        
        .category-tab {
            padding: 10px 18px;
            background: #2a2a4a;
            border: 1px solid #4a4a6a;
            color: #aaa;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.85em;
            transition: all 0.2s;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
        }
        
        .category-tab:hover, .category-tab.active {
            background: #00d4ff;
            color: #000;
            border-color: #00d4ff;
        }
        
        /* Search Box */
        .text-search-box {
            width: 100%;
            max-width: 400px;
            margin: 15px auto;
            position: relative;
        }
        
        .text-search-box input {
            width: 100%;
            padding: 12px 40px 12px 15px;
            background: #2a2a4a;
            border: 1px solid #4a4a6a;
            color: #fff;
            border-radius: 25px;
            font-size: 0.9em;
            transition: border-color 0.2s;
        }
        
        .text-search-box input:focus {
            border-color: #00d4ff;
            outline: none;
        }
        
        .text-search-box .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 1.2em;
        }
        
        .text-list {
            max-height: 200px;
            overflow-y: auto;
            background: #1a1a2e;
            border-radius: 8px;
            padding: 10px;
        }
        
        .text-item {
            padding: 12px;
            background: #3a3a5a;
            margin-bottom: 8px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            color: #fff;
            font-size: 0.9em;
        }
        
        .text-item:hover {
            background: #4a4a6a;
            border-left: 3px solid #00d4ff;
        }
        
        /* Wizard Navigation */
        .wizard-nav {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .wizard-nav .btn {
            padding: 12px 28px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            font-weight: bold;
            transition: all 0.2s;
            font-size: 0.95em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-align: center;
        }
        
        .wizard-nav .btn-prev {
            background: #3a3a5a;
            color: #fff;
        }
        
        .wizard-nav .btn-prev:hover {
            background: #4a4a6a;
        }
        
        .wizard-nav .btn-next,
        .wizard-nav .btn-add {
            background: linear-gradient(135deg, #00d4ff, #0099cc);
            color: #000;
        }
        
        .wizard-nav .btn-next:hover,
        .wizard-nav .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 212, 255, 0.4);
        }
        
        .wizard-nav .btn-generate {
            background: linear-gradient(135deg, #2ed573, #17c964);
            color: #fff;
        }
        
        .wizard-nav .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(46, 213, 115, 0.4);
        }
        
        .wizard-nav .btn-undo,
        .wizard-nav .btn-redo {
            background: #4a4a6a;
            color: #fff;
            padding: 10px 20px;
            font-size: 0.9em;
        }
        
        .wizard-nav .btn-undo:hover,
        .wizard-nav .btn-redo:hover {
            background: #5a5a7a;
        }
        
        .wizard-nav .btn-delete {
            background: linear-gradient(135deg, #ff4757, #ff3838);
            color: #fff;
        }
        
        .wizard-nav .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 71, 87, 0.4);
        }
        
        .toolbar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 15px;
            padding: 10px;
            background: #2a2a4a;
            border-radius: 8px;
        }
        
        /* WYSIWYG Text Controls */
        .wysiwyg-controls {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 15px;
            padding: 12px;
            background: #2a2a4a;
            border-radius: 8px;
        }
        
        .wysiwyg-btn {
            padding: 10px 16px;
            background: #3a3a5a;
            border: 1px solid #4a4a6a;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
        }
        
        .wysiwyg-btn:hover, .wysiwyg-btn.active {
            background: #00d4ff;
            color: #000;
            border-color: #00d4ff;
        }
        
        /* Loading Overlay */
        #loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 99999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        
        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #3a3a5a;
            border-top-color: #00d4ff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        #loading-overlay p {
            color: #fff;
            margin-top: 20px;
            font-size: 1.1em;
        }
        
        /* Success Message */
        .success-message {
            display: none;
            text-align: center;
            padding: 40px;
        }
        
        .success-message .checkmark {
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
        }
        
        .success-message h3 {
            color: #fff;
            margin-bottom: 10px;
        }
        
        .success-message p {
            color: #aaa;
            margin-bottom: 20px;
        }
        
        /* Toast Notification */
        .toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #2ed573;
            color: #fff;
            padding: 15px 30px;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            z-index: 100000;
            opacity: 0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: bold;
        }
        
        .toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
        
        .toast.error {
            background: #ff4757;
        }
        
        .toast.info {
            background: #00d4ff;
            color: #000;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .form-container {
                margin: 20px;
                padding: 20px;
            }
            
            .wizard-container {
                padding: 10px;
            }
            
            .wizard-steps {
                gap: 10px;
            }
            
            .wizard-steps::before {
                display: none;
            }
            
            .step {
                max-width: 80px;
            }
            
            .step-label {
                font-size: 0.7em;
            }
            
            .step-number {
                width: 30px;
                height: 30px;
                font-size: 0.85em;
            }
            
            .image-selection {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .wizard-nav {
                flex-direction: column;
            }
            
            .wizard-nav .btn {
                width: 100%;
            }
            
            .toolbar, .wysiwyg-controls {
                justify-content: center;
            }
            
            .size-control {
                flex-direction: column;
                align-items: stretch;
            }
            
            .size-control input[type="range"] {
                width: 100%;
            }
            
            .wizard-panel {
                min-height: auto;
            }
        }
        
        .hidden { display: none; }
        .error-msg {
            background: rgba(255, 107, 107, 0.2);
            border: 1px solid #ff6b6b;
            color: #ff6b6b;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .btn:disabled:hover {
            transform: none;
            box-shadow: none;
        }
    </style>
</head>
<body class="is-preload">

    <!-- Loading Overlay -->
    <div id="loading-overlay">
        <div class="spinner"></div>
        <p id="loading-text">Generating your image...</p>
        <p class="loading-progress" id="loading-progress">Please wait</p>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <span id="toast-icon">✓</span>
        <span id="toast-message">Success!</span>
    </div>

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Header -->
        <header id="header">
            <h1><a href="index.php"><strong>UCAPIN</strong> Image Generator</a></h1>
            <nav>
                <ul>
                    <li><a href="#footer" class="icon solid fa-info-circle">About</a></li>
                    <li><a href="admin/login.php" class="icon solid fa-lock">Admin</a></li>
                </ul>
            </nav>
        </header>

        <!-- Main -->
        <div id="main">
            
            <?php if ($error): ?>
            <div class="thumb error">
                <div class="error-msg"><?= $error ?></div>
            </div>
            <?php endif; ?>
            
            <?php if ($userData && empty($error)): ?>
            <!-- Wizard Interface -->
            <div class="wizard-container">
                
                <!-- Progress Steps -->
                <div class="wizard-steps">
                    <div class="step active" id="step1-indicator">
                        <div class="step-number">1</div>
                        <div class="step-label">Choose Image</div>
                    </div>
                    <div class="step" id="step2-indicator">
                        <div class="step-number">2</div>
                        <div class="step-label">Edit & Text</div>
                    </div>
                    <div class="step" id="step3-indicator">
                        <div class="step-number">3</div>
                        <div class="step-label">Generate</div>
                    </div>
                </div>

                <!-- Step 1: Choose Image -->
                <div class="wizard-panel active" id="step1">
                    <h3>📸 Step 1: Choose Your Image</h3>

                    <!-- Upload Area -->
                    <div class="upload-area" id="uploadArea" onclick="document.getElementById('imageUpload').click()">
                        <div class="icon">📁</div>
                        <p><strong>Click to upload</strong> or drag and drop</p>
                        <p class="small">Max 2MB • JPG, PNG format</p>
                        <input type="file" id="imageUpload" accept="image/jpeg,image/png" style="display: none;" onchange="handleImageUpload(event)">
                    </div>

                    <!-- Or Select Template -->
                    <p style="color: #aaa; text-align: center; margin: 20px 0;">— OR SELECT TEMPLATE —</p>

                    <!-- Template Gallery with Categories -->
                    <div class="template-gallery-wrapper">
                        <!-- Category Filter -->
                        <div class="template-category-filter">
                            <a href="?template_page=<?= $page ?>" class="template-filter-btn <?= $categoryFilter == 0 ? 'active' : '' ?>">
                                📁 All Templates (<?= $totalTemplates ?>)
                            </a>
                            <?php 
                            // Get count for each category
                            foreach ($templateCategories as $cat): 
                                $catCountStmt = $db->prepare("SELECT COUNT(*) as count FROM templates WHERE category_id = :category_id");
                                $catCountStmt->bindValue(':category_id', $cat['id'], PDO::PARAM_INT);
                                $catCountStmt->execute();
                                $catCount = $catCountStmt->fetch()['count'];
                            ?>
                            <a href="?template_page=<?= $page ?>&category=<?= $cat['id'] ?>" class="template-filter-btn <?= $categoryFilter == $cat['id'] ? 'active' : '' ?>">
                                📁 <?= htmlspecialchars($cat['name']) ?> (<?= $catCount ?>)
                            </a>
                            <?php endforeach; ?>
                        </div>

                        <!-- Template Grid -->
                        <div class="template-gallery">
                            <?php if (!empty($templates)): ?>
                                <?php foreach ($templates as $template): 
                                    $filePath = '../' . $template['file_path'];
                                    $thumbPath = '../' . ($template['thumbnail_path'] ?: $template['file_path']);
                                    $debugInfo = "ID:{$template['id']} | {$template['title']} | Path:{$template['file_path']}";
                                ?>
                                <div class="template-card" onclick="selectTemplate('<?= htmlspecialchars($filePath) ?>', this)" title="<?= htmlspecialchars($debugInfo) ?>">
                                    <img src="<?= htmlspecialchars($thumbPath . '?t=' . time()) ?>" alt="<?= htmlspecialchars($template['title']) ?>" onerror="console.error('Image load failed:', this.src); this.src='<?= htmlspecialchars($filePath) ?>'">
                                    <div class="template-card-info">
                                        <div class="template-card-title"><?= htmlspecialchars($template['title']) ?></div>
                                        <?php if ($template['category_name']): ?>
                                        <div class="template-card-category">📁 <?= htmlspecialchars($template['category_name']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Default template placeholders -->
                                <div class="template-card" onclick="selectTemplate('../images/fulls/01.jpg', this)">
                                    <img src="../images/thumbs/01.jpg" alt="Template 1">
                                    <div class="template-card-info">
                                        <div class="template-card-title">Template 1</div>
                                        <div class="template-card-category">📁 Default</div>
                                    </div>
                                </div>
                                <div class="template-card" onclick="selectTemplate('../images/fulls/02.jpg', this)">
                                    <img src="../images/thumbs/02.jpg" alt="Template 2">
                                    <div class="template-card-info">
                                        <div class="template-card-title">Template 2</div>
                                        <div class="template-card-category">📁 Default</div>
                                    </div>
                                </div>
                                <div class="template-card" onclick="selectTemplate('../images/fulls/03.jpg', this)">
                                    <img src="../images/thumbs/03.jpg" alt="Template 3">
                                    <div class="template-card-info">
                                        <div class="template-card-title">Template 3</div>
                                        <div class="template-card-category">📁 Default</div>
                                    </div>
                                </div>
                                <div class="template-card" onclick="selectTemplate('../images/fulls/04.jpg', this)">
                                    <img src="../images/thumbs/04.jpg" alt="Template 4">
                                    <div class="template-card-info">
                                        <div class="template-card-title">Template 4</div>
                                        <div class="template-card-category">📁 Default</div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                        <div class="template-pagination">
                            <!-- Previous -->
                            <?php if ($page > 1): ?>
                            <a href="?template_page=<?= $page - 1 ?><?= isset($_GET['category']) ? '&category=' . $_GET['category'] : '' ?>">← Prev</a>
                            <?php else: ?>
                            <span class="disabled">← Prev</span>
                            <?php endif; ?>

                            <!-- Page Numbers -->
                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                            <a href="?template_page=<?= $i ?><?= isset($_GET['category']) ? '&category=' . $_GET['category'] : '' ?>" class="<?= $i == $page ? 'current' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>

                            <!-- Next -->
                            <?php if ($page < $totalPages): ?>
                            <a href="?template_page=<?= $page + 1 ?><?= isset($_GET['category']) ? '&category=' . $_GET['category'] : '' ?>">Next →</a>
                            <?php else: ?>
                            <span class="disabled">Next →</span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="wizard-nav" style="justify-content: flex-end;">
                        <button class="btn btn-next" onclick="nextStep(2)" id="step1-next" disabled>
                            Next →
                        </button>
                    </div>
                </div>

                <!-- Step 2: Edit & Add Text (Merged) -->
                <div class="wizard-panel" id="step2">
                    <h3>✏️ Step 2: Edit & Add Text</h3>

                    <!-- Photo Upload as Layer -->
                    <div class="upload-area" id="step2UploadArea" onclick="document.getElementById('step2ImageUpload').click()" style="margin-bottom: 20px;">
                        <div class="icon">🖼️</div>
                        <p><strong>Upload Photo as Layer</strong></p>
                        <p class="small">Add photo overlay • Auto background removal • Max 5MB • JPG, PNG</p>
                        <input type="file" id="step2ImageUpload" accept="image/jpeg,image/png" style="display: none;" onchange="handleStep2Upload(event)">
                    </div>
                    <div id="step2UploadStatus" style="text-align: center; margin-bottom: 15px; display: none;">
                        <span id="step2Filename" style="color: #00d4ff; font-size: 0.9em;"></span>
                        <div style="margin-top: 10px;">
                            <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" id="removeBackground" checked style="width: auto;">
                                <span style="color: #aaa; font-size: 0.9em;">🪄 Auto-remove background</span>
                            </label>
                        </div>
                        <p style="color: #666; font-size: 0.85em; margin-top: 8px;">💡 Photo will be added as movable layer on canvas</p>
                    </div>

                    <!-- Image Size Control -->
                    <div class="size-control">
                        <label>📐 Template Size:</label>
                        <input type="range" id="imageSizeSlider" min="50" max="150" value="100" step="10" oninput="updateImageSize()">
                        <span class="value-display" id="imageSizeValue">100%</span>
                    </div>

                    <!-- Canvas Preview -->
                    <div class="canvas-wrapper">
                        <div class="canvas-container-wrapper">
                            <div class="canvas-container">
                                <canvas id="imageCanvas"></canvas>
                            </div>
                        </div>
                        <p style="color: #aaa; margin-top: 10px; font-size: 0.9em;">💡 Drag uploaded photo to reposition • Pinch/drag to resize • Drag text to reposition</p>
                    </div>
                    
                    <!-- Undo/Redo Toolbar -->
                    <div class="toolbar">
                        <button class="btn btn-undo" onclick="undo()" id="undoBtn" disabled>↶ Undo</button>
                        <button class="btn btn-redo" onclick="redo()" id="redoBtn" disabled>↷ Redo</button>
                        <button class="btn btn-delete" onclick="deleteSelectedText()" id="deleteBtn">🗑️ Delete Selected</button>
                    </div>
                    
                    <!-- WYSIWYG Text Controls -->
                    <div class="wysiwyg-controls">
                        <button class="wysiwyg-btn" onclick="toggleTextStyle('bold')" id="boldBtn" title="Bold"><b>B</b></button>
                        <button class="wysiwyg-btn" onclick="toggleTextStyle('italic')" id="italicBtn" title="Italic"><i>I</i></button>
                        <button class="wysiwyg-btn" onclick="toggleTextStyle('underline')" id="underlineBtn" title="Underline"><u>U</u></button>
                        <button class="wysiwyg-btn" onclick="toggleTextStyle('linethrough')" id="strikeBtn" title="Strikethrough"><s>S</s></button>
                        <button class="wysiwyg-btn" onclick="changeTextAlign('left')" id="alignLeft" title="Align Left">⬅</button>
                        <button class="wysiwyg-btn" onclick="changeTextAlign('center')" id="alignCenter" title="Align Center">⬌</button>
                        <button class="wysiwyg-btn" onclick="changeTextAlign('right')" id="alignRight" title="Align Right">➡</button>
                    </div>
                    
                    <!-- Custom Text Input -->
                    <div class="text-option-card" style="margin-bottom: 20px;">
                        <label>Enter Your Custom Text</label>
                        <textarea id="customText" rows="2" placeholder="Type your text here..." style="width: 100%; background: #1a1a2e; border: 1px solid #4a4a6a; color: #fff; padding: 10px; border-radius: 5px; resize: vertical; font-size: 1em;"></textarea>
                        <div style="display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap;">
                            <button onclick="addTextToCanvas()" class="btn btn-add" style="flex: 1; min-width: 150px;">
                                ➕ Add Text to Canvas
                            </button>
                        </div>
                    </div>
                    
                    <!-- Style Options -->
                    <div class="text-options-grid">
                        <div class="text-option-card">
                            <label>Font Size</label>
                            <input type="range" id="textSize" min="12" max="120" value="32" oninput="updateTextStyle()">
                            <div class="value-display" id="sizeValue">32px</div>
                        </div>
                        
                        <div class="text-option-card">
                            <label>Text Color</label>
                            <input type="color" id="textColor" value="#ffffff" onchange="updateTextStyle()">
                        </div>
                    </div>
                    
                    <!-- Or Choose Reference - Centered -->
                    <p style="color: #aaa; text-align: center; margin: 20px 0;">— OR CHOOSE FROM REFERENCES —</p>
                    
                    <!-- Search Box -->
                    <div class="text-search-box">
                        <input type="text" id="textSearch" placeholder="Search text references..." oninput="searchTexts()">
                        <span class="search-icon">🔍</span>
                    </div>
                    
                    <!-- Category Tabs - Centered -->
                    <div class="category-tabs-wrapper">
                        <div class="category-tabs">
                            <button class="category-tab active" onclick="filterTexts('all', this)">All</button>
                            <?php foreach ($categories as $cat): ?>
                            <button class="category-tab" onclick="filterTexts(<?= $cat['id'] ?>, this)"><?= htmlspecialchars($cat['name']) ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div id="textList" class="text-list">
                        <p style="color: #666; text-align: center;">Loading text references...</p>
                    </div>
                    
                    <div class="wizard-nav">
                        <button class="btn btn-prev" onclick="prevStep(1)">← Back</button>
                        <button class="btn btn-generate" onclick="generateImage()">📥 Generate & Download</button>
                    </div>
                </div>

                <!-- Step 3: Success -->
                <div class="wizard-panel" id="step3">
                    <div class="success-message" style="display: block;">
                        <div class="checkmark">✓</div>
                        <h3>Image Generated Successfully!</h3>
                        <p>Your image has been downloaded</p>
                        <button onclick="startNew()" class="btn btn-next" style="margin-top: 20px;">
                            🔄 Create New Image
                        </button>
                    </div>
                </div>

            </div>
            
            <?php else: ?>
            <!-- User Form (Centered) -->
            <div class="form-container">
                <h2>Welcome to UCAPIN</h2>
                <p>Enter your details to start creating images</p>
                
                <form method="POST" action="">
                    <div class="fields">
                        <div class="field">
                            <label for="name">Name *</label>
                            <input type="text" name="name" id="name" required placeholder="Your Name" />
                        </div>
                        <div class="field">
                            <label for="email">Email *</label>
                            <input type="email" name="email" id="email" required placeholder="your@email.com" />
                        </div>
                        <div class="field">
                            <label for="phone">Phone (optional)</label>
                            <input type="text" name="phone" id="phone" placeholder="+1 234 567 8900" />
                        </div>
                    </div>
                    <button type="submit" class="button primary">Continue →</button>
                </form>
            </div>
            <?php endif; ?>

        </div>

        <!-- Footer / About -->
        <footer id="footer" class="panel">
            <div class="inner split">
                <div>
                    <section>
                        <h2>About UCAPIN</h2>
                        <p>UCAPIN is a web-based image text generator platform. Create beautiful images with custom text overlays using our easy-to-use editor.</p>
                    </section>
                    <p class="copyright">
                        &copy; UCAPIN. Design: <a href="http://html5up.net">HTML5 UP</a>.
                    </p>
                </div>
                <div>
                    <section>
                        <h2>Quick Start</h2>
                        <ol style="color: #aaa;">
                            <li>Enter your details</li>
                            <li>Choose or upload an image</li>
                            <li>Add and customize text</li>
                            <li>Download your creation</li>
                        </ol>
                    </section>
                </div>
            </div>
        </footer>

    </div>

    <!-- Scripts -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/jquery.poptrox.min.js"></script>
    <script src="../assets/js/browser.min.js"></script>
    <script src="../assets/js/breakpoints.min.js"></script>
    <script src="../assets/js/util.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script>
        let canvas;
        let currentImage = null;
        let currentStep = 1;
        let selectedImage = null;
        let originalImageWidth = 500;
        let originalImageHeight = 400;
        let currentImageScale = 1;
        let imageLoaded = false;
        
        // History for undo/redo
        let historyStack = [];
        let historyIndex = -1;
        let historyProcessing = false;
        
        // Text style state
        let currentTextStyles = {
            bold: false,
            italic: false,
            underline: false,
            linethrough: false,
            textAlign: 'center'
        };
        
        // All texts for search
        let allTexts = [];
        
        // Initialize canvas
        function initCanvas() {
            if (!canvas) {
                canvas = new fabric.Canvas('imageCanvas', {
                    width: 500,
                    height: 400,
                    backgroundColor: '#1a1a2e'
                });
                
                setupCanvasEvents();
            }
        }
        
        // Setup canvas events
        function setupCanvasEvents() {
            canvas.on('selection:created', updateStyleFromSelection);
            canvas.on('selection:updated', updateStyleFromSelection);
            canvas.on('object:modified', function(e) {
                if (!historyProcessing) {
                    saveHistory();
                }
            });
            
            canvas.on('object:added', function(e) {
                if (!historyProcessing && e.target && e.target.type !== 'background') {
                    saveHistory();
                }
            });
            
            canvas.on('object:removed', function(e) {
                if (!historyProcessing) {
                    saveHistory();
                }
            });
        }
        
        // Save state to history
        function saveHistory() {
            if (historyProcessing) return;
            
            historyStack = historyStack.slice(0, historyIndex + 1);
            const json = canvas.toJSON();
            historyStack.push(json);
            historyIndex++;
            
            if (historyStack.length > 20) {
                historyStack.shift();
                historyIndex--;
            }
            
            updateUndoRedoButtons();
        }
        
        // Undo
        function undo() {
            if (historyIndex > 0) {
                historyProcessing = true;
                historyIndex--;
                canvas.loadFromJSON(historyStack[historyIndex], function() {
                    canvas.renderAll();
                    historyProcessing = false;
                    updateUndoRedoButtons();
                });
            }
        }
        
        // Redo
        function redo() {
            if (historyIndex < historyStack.length - 1) {
                historyProcessing = true;
                historyIndex++;
                canvas.loadFromJSON(historyStack[historyIndex], function() {
                    canvas.renderAll();
                    historyProcessing = false;
                    updateUndoRedoButtons();
                });
            }
        }
        
        // Update undo/redo button states
        function updateUndoRedoButtons() {
            document.getElementById('undoBtn').disabled = historyIndex <= 0;
            document.getElementById('redoBtn').disabled = historyIndex >= historyStack.length - 1;
        }
        
        // Update style inputs from selected object
        function updateStyleFromSelection() {
            const activeObject = canvas.getActiveObject();
            if (activeObject && (activeObject.type === 'text' || activeObject.type === 'i-text')) {
                document.getElementById('textSize').value = activeObject.fontSize || 32;
                document.getElementById('textColor').value = activeObject.fill || '#ffffff';
                document.getElementById('sizeValue').textContent = (activeObject.fontSize || 32) + 'px';
                
                // Update WYSIWYG buttons
                currentTextStyles.bold = activeObject.fontWeight === 'bold';
                currentTextStyles.italic = activeObject.fontStyle === 'italic';
                currentTextStyles.underline = activeObject.underline || false;
                currentTextStyles.linethrough = activeObject.linethrough || false;
                currentTextStyles.textAlign = activeObject.textAlign || 'left';
                
                updateWysiwygButtons();
            }
        }
        
        // Update WYSIWYG button states
        function updateWysiwygButtons() {
            document.getElementById('boldBtn').classList.toggle('active', currentTextStyles.bold);
            document.getElementById('italicBtn').classList.toggle('active', currentTextStyles.italic);
            document.getElementById('underlineBtn').classList.toggle('active', currentTextStyles.underline);
            document.getElementById('strikeBtn').classList.toggle('active', currentTextStyles.linethrough);
            document.getElementById('alignLeft').classList.toggle('active', currentTextStyles.textAlign === 'left');
            document.getElementById('alignCenter').classList.toggle('active', currentTextStyles.textAlign === 'center');
            document.getElementById('alignRight').classList.toggle('active', currentTextStyles.textAlign === 'right');
        }
        
        // Toggle text style
        function toggleTextStyle(style) {
            const activeObject = canvas.getActiveObject();
            if (!activeObject || (activeObject.type !== 'text' && activeObject.type !== 'i-text')) {
                showToast('Please select a text object first', 'error');
                return;
            }
            
            if (style === 'bold') {
                currentTextStyles.bold = !currentTextStyles.bold;
                activeObject.set('fontWeight', currentTextStyles.bold ? 'bold' : 'normal');
            } else if (style === 'italic') {
                currentTextStyles.italic = !currentTextStyles.italic;
                activeObject.set('fontStyle', currentTextStyles.italic ? 'italic' : 'normal');
            } else if (style === 'underline') {
                currentTextStyles.underline = !currentTextStyles.underline;
                activeObject.set('underline', currentTextStyles.underline);
            } else if (style === 'linethrough') {
                currentTextStyles.linethrough = !currentTextStyles.linethrough;
                activeObject.set('linethrough', currentTextStyles.linethrough);
            }
            
            canvas.renderAll();
            updateWysiwygButtons();
            saveHistory();
        }
        
        // Change text alignment
        function changeTextAlign(align) {
            const activeObject = canvas.getActiveObject();
            if (!activeObject || (activeObject.type !== 'text' && activeObject.type !== 'i-text')) {
                showToast('Please select a text object first', 'error');
                return;
            }
            
            currentTextStyles.textAlign = align;
            activeObject.set('textAlign', align);
            canvas.renderAll();
            updateWysiwygButtons();
            saveHistory();
        }
        
        // Step navigation
        function nextStep(step) {
            if (step === 2 && !imageLoaded) {
                showToast('Please select or upload an image first', 'error');
                return;
            }
            
            if (step === 2) {
                initCanvas();
            }
            
            document.querySelectorAll('.wizard-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');
            
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            document.getElementById('step' + step + '-indicator').classList.add('active');
            
            for (let i = 1; i < step; i++) {
                document.getElementById('step' + i + '-indicator').classList.add('completed');
            }
            
            currentStep = step;
        }
        
        function prevStep(step) {
            document.querySelectorAll('.wizard-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');
            
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            document.getElementById('step' + step + '-indicator').classList.add('active');
            
            for (let i = step + 1; i <= 3; i++) {
                document.getElementById('step' + i + '-indicator').classList.remove('completed');
            }
            
            currentStep = step;
        }
        
        // Handle image upload
        function handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            if (file.size > 2 * 1024 * 1024) {
                showToast('File size must be less than 2MB', 'error');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                loadImageToCanvas(e.target.result);
            };
            reader.onerror = function() {
                showToast('Failed to read file', 'error');
            };
            reader.readAsDataURL(file);
        }
        
        // Drag and drop
        const uploadArea = document.getElementById('uploadArea');
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file && (file.type === 'image/jpeg' || file.type === 'image/png')) {
                if (file.size > 2 * 1024 * 1024) {
                    showToast('File size must be less than 2MB', 'error');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    loadImageToCanvas(e.target.result);
                };
                reader.onerror = function() {
                    showToast('Failed to read file', 'error');
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Select template
        function selectTemplate(path, element) {
            // Deselect all template cards
            document.querySelectorAll('.template-card').forEach(el => el.classList.remove('selected'));
            document.querySelectorAll('.image-option').forEach(el => el.classList.remove('selected'));
            
            // Select clicked element
            if (element) {
                element.classList.add('selected');
            }
            
            selectedImage = path;
            console.log('Selecting template:', path);

            const img = new Image();
            // Remove crossOrigin for same-domain images
            img.onload = function() {
                console.log('Image loaded successfully:', img.width, 'x', img.height);
                loadImageToCanvas(path, img);
            };
            img.onerror = function() {
                console.error('Failed to load image:', path);
                showToast('Failed to load template image', 'error');
            };
            img.src = path;
        }
        
        // Load image to canvas
        function loadImageToCanvas(src, imgElement = null) {
            initCanvas();

            const img = imgElement || new Image();
            // Remove crossOrigin for same-domain images
            img.onload = function() {
                try {
                    originalImageWidth = img.width;
                    originalImageHeight = img.height;

                    const maxWidth = 600;
                    const maxHeight = 500;
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth || height > maxHeight) {
                        const ratio = Math.min(maxWidth / width, maxHeight / height);
                        width = width * ratio;
                        height = height * ratio;
                    }

                    canvas.setWidth(width);
                    canvas.setHeight(height);
                    canvas.setBackgroundColor('#1a1a2e', canvas.renderAll.bind(canvas));

                    const fabricImg = new fabric.Image(img);
                    fabricImg.scaleToWidth(width);
                    canvas.setBackgroundImage(fabricImg, function() {
                        canvas.renderAll();
                        imageLoaded = true;
                        document.getElementById('step1-next').disabled = false;

                        showToast('✓ Image loaded successfully!', 'success');

                        // Auto-advance to step 2 after short delay
                        setTimeout(function() {
                            nextStep(2);
                        }, 1000);
                    });
                    currentImage = fabricImg;
                    currentImageScale = 1;
                } catch (err) {
                    console.error('Error loading image:', err);
                    showToast('Failed to load image to canvas', 'error');
                }
            };
            img.onerror = function() {
                console.error('Failed to load image:', src);
                showToast('Failed to load image', 'error');
            };
            img.src = src;
        }
        
        // Update image size
        function updateImageSize() {
            const scalePercent = parseInt(document.getElementById('imageSizeSlider').value);
            document.getElementById('imageSizeValue').textContent = scalePercent + '%';
            
            if (!currentImage) return;
            
            currentImageScale = scalePercent / 100;
            
            const newWidth = originalImageWidth * currentImageScale;
            const newHeight = originalImageHeight * currentImageScale;
            
            canvas.setWidth(newWidth);
            canvas.setHeight(newHeight);
            
            currentImage.scaleToWidth(newWidth);
            canvas.renderAll();
            
            saveHistory();
        }
        
        // Add text to canvas
        function addTextToCanvas() {
            const text = document.getElementById('customText').value;
            if (!text.trim()) {
                showToast('Please enter some text', 'error');
                return;
            }
            
            initCanvas();
            
            const fontSize = parseInt(document.getElementById('textSize').value);
            const fillColor = document.getElementById('textColor').value;
            
            const fabricText = new fabric.IText(text, {
                left: canvas.width / 2,
                top: canvas.height / 2,
                fontSize: fontSize,
                fill: fillColor,
                originX: 'center',
                originY: 'center',
                fontWeight: currentTextStyles.bold ? 'bold' : 'normal',
                fontStyle: currentTextStyles.italic ? 'italic' : 'normal',
                underline: currentTextStyles.underline,
                linethrough: currentTextStyles.linethrough,
                textAlign: currentTextStyles.textAlign
            });
            
            canvas.add(fabricText);
            canvas.setActiveObject(fabricText);
            
            document.getElementById('customText').value = '';
            showToast('✓ Text added to image!', 'success');
        }
        
        // Update text style
        function updateTextStyle() {
            if (!canvas) return;
            
            const activeObject = canvas.getActiveObject();
            if (activeObject && (activeObject.type === 'text' || activeObject.type === 'i-text')) {
                activeObject.set({
                    fontSize: parseInt(document.getElementById('textSize').value),
                    fill: document.getElementById('textColor').value
                });
                canvas.renderAll();
            }
            document.getElementById('sizeValue').textContent = document.getElementById('textSize').value + 'px';
        }
        
        // Delete selected text
        function deleteSelectedText() {
            if (!canvas) return;
            
            const activeObject = canvas.getActiveObject();
            if (activeObject && (activeObject.type === 'text' || activeObject.type === 'i-text')) {
                canvas.remove(activeObject);
                canvas.discardActiveObject();
                canvas.renderAll();
                showToast('🗑️ Text deleted', 'info');
            } else {
                showToast('Please select a text object to delete', 'error');
            }
        }
        
        // Filter texts
        function filterTexts(categoryId, element) {
            document.querySelectorAll('.category-tab').forEach(tab => tab.classList.remove('active'));
            if (element) element.classList.add('active');
            
            // Clear search when filtering by category
            document.getElementById('textSearch').value = '';
            
            fetch('get_texts.php?category_id=' + categoryId)
                .then(response => response.json())
                .then(data => {
                    allTexts = data; // Store for search
                    displayTexts(data);
                })
                .catch(err => {
                    console.error('Error loading texts:', err);
                    document.getElementById('textList').innerHTML = '<p style="color: #ff6b6b; text-align: center;">Error loading texts</p>';
                });
        }
        
        // Search texts
        function searchTexts() {
            const searchTerm = document.getElementById('textSearch').value.toLowerCase().trim();
            
            if (!searchTerm) {
                // If search is empty, show all texts from current category
                const activeCategory = document.querySelector('.category-tab.active');
                if (activeCategory && activeCategory.onclick) {
                    // Re-trigger category filter
                    return;
                }
                displayTexts(allTexts);
                return;
            }
            
            // Filter texts by search term
            const filtered = allTexts.filter(text => 
                text.content.toLowerCase().includes(searchTerm)
            );
            displayTexts(filtered);
        }
        
        // Display texts
        function displayTexts(texts) {
            const container = document.getElementById('textList');
            container.innerHTML = '';
            
            if (texts.length === 0) {
                container.innerHTML = '<p style="color: #666; text-align: center;">No texts found</p>';
                return;
            }
            
            texts.forEach(item => {
                const div = document.createElement('div');
                div.className = 'text-item';
                div.textContent = item.content.substring(0, 80) + (item.content.length > 80 ? '...' : '');
                div.onclick = function() {
                    document.getElementById('customText').value = item.content;
                    addTextToCanvas();
                };
                container.appendChild(div);
            });
        }
        
        // Generate image - same tab
        function generateImage() {
            if (!canvas) {
                showToast('Canvas not initialized', 'error');
                return;
            }
            
            const objects = canvas.getObjects();
            if (objects.length === 0 || !currentImage) {
                showToast('Please add some text to the image', 'error');
                return;
            }
            
            document.getElementById('loading-overlay').style.display = 'flex';
            document.getElementById('loading-text').textContent = 'Generating your image...';
            document.getElementById('loading-progress').textContent = 'Please wait';
            
            const canvasData = canvas.toDataURL('image/jpeg', 0.95);
            const customText = document.getElementById('customText').value;
            
            // Use fetch to generate in background
            const formData = new FormData();
            formData.append('canvas_data', canvasData);
            formData.append('user_id', '<?= $userData['id'] ?? '' ?>');
            formData.append('custom_text', customText);
            
            fetch('process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                // Parse the response to extract filename
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Look for download link in response
                const downloadLink = doc.querySelector('.download-link');
                if (downloadLink) {
                    const downloadUrl = downloadLink.href;
                    
                    // Trigger download
                    const link = document.createElement('a');
                    link.href = downloadUrl;
                    link.download = '';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // Wait for download to start, then show success
                    setTimeout(() => {
                        document.getElementById('loading-overlay').style.display = 'none';
                        nextStep(3);
                    }, 2000);
                } else {
                    // Fallback: submit form normally
                    document.getElementById('canvasData').value = canvasData;
                    document.getElementById('formCustomText').value = customText;
                    document.getElementById('generateForm').submit();
                }
            })
            .catch(err => {
                console.error('Error:', err);
                showToast('Failed to generate image', 'error');
                document.getElementById('loading-overlay').style.display = 'none';
            });
        }
        
        // Start new image - go to Step 1, not registration
        function startNew() {
            historyStack = [];
            historyIndex = -1;
            selectedImage = null;
            currentImageScale = 1;
            imageLoaded = false;
            
            if (canvas) {
                canvas.clear();
                canvas.setBackgroundColor('#1a1a2e', canvas.renderAll.bind(canvas));
            }
            
            document.getElementById('customText').value = '';
            document.getElementById('imageSizeSlider').value = 100;
            document.getElementById('imageSizeValue').textContent = '100%';
            document.getElementById('textSearch').value = '';
            
            // Reset text styles
            currentTextStyles = {
                bold: false,
                italic: false,
                underline: false,
                linethrough: false,
                textAlign: 'center'
            };
            updateWysiwygButtons();
            
            // Go to Step 1
            document.querySelectorAll('.wizard-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('step1').classList.add('active');
            
            document.querySelectorAll('.step').forEach(s => {
                s.classList.remove('active', 'completed');
            });
            document.getElementById('step1-indicator').classList.add('active');
            
            document.getElementById('step1-next').disabled = true;
            
            document.querySelectorAll('.image-option').forEach(el => el.classList.remove('selected'));
            
            // Reset category tabs
            document.querySelectorAll('.category-tab').forEach(tab => tab.classList.remove('active'));
            document.querySelector('.category-tab:first-child').classList.add('active');
            
            // Reload texts
            filterTexts('all', document.querySelector('.category-tab:first-child'));
            
            showToast('ℹ️ Ready to create a new image!', 'info');
        }
        
        // Toast notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            const toastIcon = document.getElementById('toast-icon');
            
            toast.className = 'toast ' + type;
            toastMessage.textContent = message;
            
            if (type === 'success') {
                toastIcon.textContent = '✓';
            } else if (type === 'error') {
                toastIcon.textContent = '⚠';
            } else {
                toastIcon.textContent = 'ℹ';
            }
            
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
        
        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            filterTexts('all', document.querySelector('.category-tab.active'));
            updateWysiwygButtons();
        });

        // Step 2: Handle photo upload as layer with background removal
        function handleStep2Upload(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                showToast('File size must be less than 5MB', 'error');
                return;
            }

            // Show filename and background removal option
            document.getElementById('step2Filename').textContent = '📄 ' + file.name;
            document.getElementById('step2UploadStatus').style.display = 'block';

            const removeBg = document.getElementById('removeBackground').checked;

            if (removeBg) {
                // Upload with background removal
                uploadWithBackgroundRemoval(file);
            } else {
                // Upload without background removal
                uploadStep2Image(file);
            }
        }

        // Upload image with background removal and add as layer
        function uploadWithBackgroundRemoval(file) {
            showToast('🪄 Removing background...', 'info');
            document.getElementById('loading-overlay').style.display = 'flex';
            document.getElementById('loading-text').textContent = 'Removing background...';
            document.getElementById('loading-progress').textContent = 'Please wait';

            const formData = new FormData();
            formData.append('image', file);

            fetch('remove_background.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('✓ Background removed!', 'success');
                    // Add the processed image as a layer on canvas
                    addImageLayer('../' + data.url);
                    document.getElementById('loading-overlay').style.display = 'none';
                } else {
                    throw new Error(data.error || 'Background removal failed');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                showToast('⚠️ Background removal failed. Loading original...', 'error');
                document.getElementById('loading-overlay').style.display = 'none';
                // Fallback: add original image as layer
                addImageLayerFromFile(file);
            });
        }

        // Upload Step 2 image and add as layer (without background removal)
        function uploadStep2Image(file) {
            addImageLayerFromFile(file);
        }

        // Add image as layer from URL
        function addImageLayer(url) {
            if (!canvas) {
                showToast('Please load a template first', 'error');
                return;
            }

            const imgObj = new Image();
            imgObj.crossOrigin = 'anonymous';
            imgObj.src = url;
            
            imgObj.onload = function() {
                // Create fabric image object
                const fabricImg = new fabric.Image(imgObj);
                
                // Scale down if too large
                const maxWidth = canvas.width * 0.6;
                const maxHeight = canvas.height * 0.6;
                
                const scale = Math.min(
                    maxWidth / fabricImg.width,
                    maxHeight / fabricImg.height,
                    1 // Don't scale up
                );
                
                fabricImg.set({
                    left: canvas.width / 2,
                    top: canvas.height / 2,
                    originX: 'center',
                    originY: 'center',
                    scaleX: scale,
                    scaleY: scale,
                    cornerColor: '#00d4ff',
                    cornerStrokeColor: '#00d4ff',
                    borderOpacityWhenMoving: 0.8,
                    transparentCorners: false
                });
                
                // Add to canvas
                canvas.add(fabricImg);
                canvas.setActiveObject(fabricImg);
                canvas.renderAll();
                
                showToast('✓ Photo added as layer! Drag to reposition, use corners to resize', 'success');
                saveHistory();
            };
            
            imgObj.onerror = function() {
                showToast('Failed to load image', 'error');
            };
        }

        // Add image as layer from file
        function addImageLayerFromFile(file) {
            if (!canvas) {
                showToast('Please load a template first', 'error');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                addImageLayer(e.target.result);
            };
            reader.onerror = function() {
                showToast('Failed to read file', 'error');
            };
            reader.readAsDataURL(file);
        }

        // Drag and drop for Step 2
        const step2UploadArea = document.getElementById('step2UploadArea');
        if (step2UploadArea) {
            step2UploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                step2UploadArea.classList.add('dragover');
            });

            step2UploadArea.addEventListener('dragleave', () => {
                step2UploadArea.classList.remove('dragover');
            });

            step2UploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                step2UploadArea.classList.remove('dragover');
                const file = e.dataTransfer.files[0];
                if (file && (file.type === 'image/jpeg' || file.type === 'image/png')) {
                    handleStep2Upload({ target: { files: [file] } });
                }
            });
        }
    </script>
</body>
</html>

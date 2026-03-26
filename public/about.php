<?php
/**
 * About Page
 * Displays about information from database
 */
require_once '../config/database.php';

$db = (new Database())->connect();

// Fetch about content
$about = $db->query("SELECT * FROM about LIMIT 1")->fetch();

?>
<!DOCTYPE HTML>
<html>
<head>
    <title>About - UCAPIN</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="../assets/css/main.css" />
    <noscript><link rel="stylesheet" href="../assets/css/noscript.css" /></noscript>
</head>
<body class="is-preload">

    <div id="wrapper">

        <!-- Header -->
        <header id="header">
            <h1><a href="index.php"><strong>UCAPIN</strong> About Us</a></h1>
            <nav>
                <ul>
                    <li><a href="index.php" class="icon solid fa-home">Home</a></li>
                    <li><a href="admin/login.php" class="icon solid fa-lock">Admin</a></li>
                </ul>
            </nav>
        </header>

        <!-- Main -->
        <div id="main">
            <article class="thumb" style="grid-column: span 2;">
                <h2><?= htmlspecialchars($about['title'] ?? 'About UCAPIN') ?></h2>
                <p><?= nl2br(htmlspecialchars($about['content'] ?? 'UCAPIN is a web-based image text generator platform.')) ?></p>
                
                <h3 style="margin-top: 30px;">Our Features</h3>
                <ul>
                    <li>Easy-to-use image editor with drag & drop text</li>
                    <li>Multiple templates to choose from</li>
                    <li>Text reference library with categories</li>
                    <li>Custom text styling (size, color, position)</li>
                    <li>Instant download of generated images</li>
                    <li>User history tracking</li>
                </ul>
                
                <h3 style="margin-top: 30px;">How It Works</h3>
                <ol>
                    <li>Enter your details to get started</li>
                    <li>Choose a template or upload your own image</li>
                    <li>Add text - either custom or from our reference library</li>
                    <li>Drag and position text anywhere on the image</li>
                    <li>Customize font size and color</li>
                    <li>Download your creation instantly</li>
                </ol>
                
                <div style="margin-top: 30px;">
                    <a href="index.php" class="button primary">Start Creating Now</a>
                </div>
            </article>
        </div>

        <!-- Footer -->
        <footer id="footer" class="panel">
            <div class="inner">
                <p class="copyright">
                    &copy; UCAPIN. Design: <a href="http://html5up.net">HTML5 UP</a>.
                </p>
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

</body>
</html>

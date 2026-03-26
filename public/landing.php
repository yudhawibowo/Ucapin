<?php
/**
 * UCAPIN - Landing Page
 * Professional landing page with SEO optimization, image gallery, and trial CTA
 */
session_start();
require_once '../config/database.php';

$db = (new Database())->connect();

// Get statistics for counter
$stats = [];
try {
    $stats['images'] = $db->query("SELECT COUNT(*) FROM images")->fetchColumn();
    $stats['users'] = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $stats['templates'] = $db->query("SELECT COUNT(*) FROM templates")->fetchColumn();
} catch (Exception $e) {
    $stats = ['images' => 0, 'users' => 0, 'templates' => 0];
}

// Get recent generated images for gallery
$recentImages = $db->query("
    SELECT i.*, u.name as user_name
    FROM images i
    JOIN users u ON i.user_id = u.id
    ORDER BY i.created_at DESC
    LIMIT 12
")->fetchAll();

// Get about content
try {
    $aboutContent = $db->query("SELECT * FROM about LIMIT 1")->fetch();
} catch (Exception $e) {
    $aboutContent = ['title' => 'About UCAPIN', 'content' => 'UCAPIN is a web-based image text generator platform.'];
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>UCAPIN - Free Online Image Text Generator | Create Custom Quotes & Graphics</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <meta name="description" content="Create beautiful images with custom text overlays for free. Perfect for social media, motivational quotes, greetings, and more. No signup required - start creating now!" />
    <meta name="keywords" content="image generator, text overlay, quote maker, social media graphics, free image editor, motivational quotes, greeting cards, custom images" />
    <meta name="author" content="UCAPIN" />
    <meta name="robots" content="index, follow" />
    
    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website" />
    <meta property="og:title" content="UCAPIN - Free Online Image Text Generator" />
    <meta property="og:description" content="Create beautiful images with custom text overlays. Perfect for social media, quotes, and greetings." />
    <meta property="og:image" content="assets/images/og-image.jpg" />
    <meta property="og:url" content="https://ucapin.com" />
    
    <!-- Canonical URL -->
    <link rel="canonical" href="index.php" />
    
    <link rel="stylesheet" href="../assets/css/main.css" />
    <link rel="stylesheet" href="../assets/css/ucapin.css" />
    <noscript><link rel="stylesheet" href="../assets/css/noscript.css" /></noscript>
    
    <style>
        /* Landing Page Specific Styles - Mobile First with Proper Container */
        * {
            box-sizing: border-box;
        }
        
        /* Main Container - Mobile Width Constrained */
        body {
            padding-bottom: 80px; /* Space for bottom nav */
            margin: 0;
            padding: 0;
        }
        
        #wrapper {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        /* Bottom Navigation Bar */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #1e1e32, #2a2a4a);
            border-top: 2px solid #00d4ff;
            padding: 10px 0;
            z-index: 9999;
            box-shadow: 0 -5px 20px rgba(0, 212, 255, 0.2);
        }

        .bottom-nav-content {
            display: flex;
            justify-content: space-around;
            align-items: center;
            max-width: 600px;
            margin: 0 auto;
            padding: 0 10px;
        }

        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #aaa;
            font-size: 0.7em;
            padding: 6px 10px;
            border-radius: 8px;
            transition: all 0.3s;
            gap: 5px;
            min-width: 65px;
        }

        .bottom-nav-item:hover,
        .bottom-nav-item.active {
            color: #00d4ff;
            background: rgba(0, 212, 255, 0.1);
        }

        .bottom-nav-item i {
            font-size: 1.3em;
        }

        .bottom-nav-item span {
            display: block;
            font-size: 0.85em;
            white-space: nowrap;
        }

        .bottom-nav-more {
            position: relative;
        }

        .bottom-nav-dropdown {
            display: none;
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #1e1e32;
            border: 1px solid #00d4ff;
            border-radius: 10px;
            padding: 10px 0;
            min-width: 180px;
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.5);
            margin-bottom: 10px;
        }

        .bottom-nav-dropdown.show {
            display: block;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateX(-50%) translateY(10px); }
            to { opacity: 1; transform: translateX(-50%) translateY(0); }
        }

        .bottom-nav-dropdown a {
            display: block;
            padding: 12px 20px;
            color: #aaa;
            text-decoration: none;
            transition: all 0.2s;
            text-align: left;
        }

        .bottom-nav-dropdown a:hover {
            background: rgba(0, 212, 255, 0.1);
            color: #00d4ff;
        }

        /* Hide top header */
        #header {
            display: none;
        }

        /* Landing Hero - Centered */
        .landing-hero {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.15), rgba(46, 213, 115, 0.15));
            padding: 50px 20px 40px;
            text-align: center;
            border-radius: 15px;
            margin: 0 15px 25px 15px;
            position: relative;
            overflow: hidden;
        }
        
        .landing-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(0, 212, 255, 0.1) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        .landing-hero h1 {
            font-size: 3em;
            color: #fff;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
            text-shadow: 0 0 30px rgba(0, 212, 255, 0.5);
        }
        
        .landing-hero .highlight {
            background: linear-gradient(135deg, #00d4ff, #2ed573);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
        }
        
        .landing-hero p {
            font-size: 1.3em;
            color: #aaa;
            max-width: 700px;
            margin: 0 auto 30px;
            position: relative;
            z-index: 1;
        }
        
        .hero-cta {
            display: inline-flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
            position: relative;
            z-index: 1;
        }
        
        .btn-cta-primary {
            background: linear-gradient(135deg, #00d4ff, #0099cc);
            color: #000;
            padding: 18px 40px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1.1em;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
        }
        
        .btn-cta-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 212, 255, 0.5);
        }
        
        .btn-cta-secondary {
            background: rgba(46, 213, 115, 0.2);
            color: #2ed573;
            padding: 18px 40px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1.1em;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 2px solid #2ed573;
        }
        
        .btn-cta-secondary:hover {
            background: #2ed573;
            color: #000;
            transform: translateY(-5px);
        }

        /* Stats Counter - Centered */
        .stats-counter {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 25px 15px;
            padding: 25px 20px;
            background: rgba(30, 30, 50, 0.5);
            border-radius: 15px;
            border: 1px solid #4a4a6a;
        }

        .stat-item {
            text-align: center;
            padding: 15px 10px;
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            background: linear-gradient(135deg, #00d4ff, #2ed573);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: block;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .stat-label {
            color: #aaa;
            font-size: 0.85em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
        }

        /* Features Section */
        .features-section {
            padding: 50px 20px;
            margin: 30px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h2 {
            font-size: 2em;
            color: #fff;
            margin-bottom: 12px;
        }

        .section-title p {
            color: #aaa;
            font-size: 1em;
            max-width: 500px;
            margin: 0 auto;
            padding: 0 10px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            max-width: 550px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .feature-card {
            background: rgba(30, 30, 50, 0.6);
            padding: 25px 20px;
            border-radius: 15px;
            border: 1px solid #4a4a6a;
            transition: all 0.3s;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: #00d4ff;
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.2);
        }

        .feature-icon {
            font-size: 2.5em;
            margin-bottom: 15px;
            display: block;
        }

        .feature-card h3 {
            color: #fff;
            font-size: 1.2em;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: #aaa;
            line-height: 1.6;
            font-size: 0.95em;
        }
            box-shadow: 0 15px 40px rgba(0, 212, 255, 0.2);
        }
        
        .feature-icon {
            font-size: 3em;
            margin-bottom: 20px;
            display: block;
        }
        
        .feature-card h3 {
            color: #fff;
            font-size: 1.3em;
            margin-bottom: 15px;
        }
        
        .feature-card p {
            color: #aaa;
            line-height: 1.6;
        }

        /* Gallery Section - Attractive Grid */
        .gallery-section {
            padding: 50px 15px;
            background: rgba(20, 20, 40, 0.5);
            border-radius: 15px;
            margin: 30px 0;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            max-width: 570px;
            margin: 0 auto;
            padding: 0;
        }

        .gallery-item-landing {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .gallery-item-landing:hover {
            transform: translateY(-5px);
            border-color: #00d4ff;
            box-shadow: 0 8px 25px rgba(0, 212, 255, 0.3);
        }

        .gallery-item-landing img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.3s;
        }

        .gallery-item-landing:hover img {
            transform: scale(1.1);
        }

        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.95), transparent);
            padding: 15px 12px;
            opacity: 0;
            transition: opacity 0.3s;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .gallery-item-landing:hover .gallery-overlay {
            opacity: 1;
        }

        .gallery-overlay-text {
            color: #fff;
            font-size: 0.85em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 500;
        }

        .gallery-overlay-user {
            color: #00d4ff;
            font-size: 0.75em;
            margin-top: 2px;
            font-weight: 600;
        }

        /* How It Works */
        .how-it-works {
            padding: 50px 20px;
            margin: 30px 0;
        }

        .steps-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 500px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .step-card {
            background: rgba(30, 30, 50, 0.4);
            padding: 25px 20px;
            border-radius: 15px;
            border: 1px solid #4a4a6a;
            text-align: center;
            transition: all 0.3s;
        }

        .step-card:hover {
            border-color: #00d4ff;
            transform: translateY(-3px);
        }

        .step-number-badge {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00d4ff, #2ed573);
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2em;
            margin: 0 auto 15px;
        }

        .step-card h3 {
            color: #fff;
            font-size: 1.1em;
            margin-bottom: 10px;
        }

        .step-card p {
            color: #aaa;
            font-size: 0.9em;
            line-height: 1.5;
        }
        }
        
        .step-card {
            flex: 1;
            min-width: 200px;
            text-align: center;
            padding: 30px 20px;
            background: rgba(30, 30, 50, 0.4);
            border-radius: 15px;
            border: 1px solid #4a4a6a;
            position: relative;
        }
        
        .step-number-badge {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00d4ff, #2ed573);
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.3em;
            margin: 0 auto 20px;
        }
        
        .step-card h3 {
            color: #fff;
            margin-bottom: 10px;
        }
        
        .step-card p {
            color: #aaa;
            font-size: 0.95em;
        }

        /* CTA Section - Centered */
        .cta-section {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(46, 213, 115, 0.2));
            padding: 60px 20px;
            text-align: center;
            border-radius: 15px;
            margin: 30px 0;
            border: 1px solid #4a4a6a;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-section h2 {
            font-size: 2em;
            color: #fff;
            margin-bottom: 15px;
        }

        .cta-section p {
            color: #aaa;
            font-size: 1em;
            margin-bottom: 25px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 10px;
        }
        
        /* Modal/Trial Popup */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 100000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .modal-overlay.active {
            display: flex;
        }
        
        .modal-content {
            background: linear-gradient(135deg, #1e1e32, #2a2a4a);
            padding: 40px;
            border-radius: 20px;
            max-width: 500px;
            width: 100%;
            border: 2px solid #00d4ff;
            position: relative;
            animation: modalSlideIn 0.3s ease;
        }
        
        @keyframes modalSlideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #00d4ff, #0099cc);
            border: none;
            color: #000;
            font-size: 1.3em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
            z-index: 10;
        }

        .modal-close:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 212, 255, 0.5);
        }

        .modal-close:active {
            transform: translateY(0);
        }
        
        .modal-content h2 {
            color: #fff;
            font-size: 2em;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .modal-content p {
            color: #aaa;
            text-align: center;
            margin-bottom: 25px;
        }
        
        .trial-form .field {
            margin-bottom: 20px;
        }
        
        .trial-form label {
            color: #00d4ff;
            font-size: 0.9em;
            display: block;
            margin-bottom: 8px;
        }
        
        .trial-form input {
            width: 100%;
            padding: 14px;
            background: #2a2a4a;
            border: 1px solid #4a4a6a;
            color: #fff;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.2s;
        }
        
        .trial-form input:focus {
            border-color: #00d4ff;
            outline: none;
        }
        
        .btn-trial-submit {
            width: 100%;
            background: linear-gradient(135deg, #00d4ff, #0099cc);
            color: #000;
            padding: 16px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.1em;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-trial-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.4);
        }
        
        /* Modal Responsive */
        @media (max-width: 768px) {
            .modal-overlay {
                padding: 15px;
                align-items: center;
            }
            
            .modal-content {
                max-width: 100%;
                margin: 0 auto;
            }
        }
        
        /* Testimonials - Centered */
        .testimonials-section {
            padding: 50px 20px;
            margin: 30px 0;
            text-align: center;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            max-width: 550px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .testimonial-card {
            background: rgba(30, 30, 50, 0.6);
            padding: 25px 20px;
            border-radius: 15px;
            border: 1px solid #4a4a6a;
            text-align: center;
        }

        .testimonial-text {
            color: #aaa;
            font-style: italic;
            margin-bottom: 20px;
            line-height: 1.6;
            text-align: center;
        }

        .testimonial-author {
            color: #00d4ff;
            font-weight: bold;
            display: block;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .landing-hero {
                padding: 40px 15px;
                margin: 0 10px 20px 10px;
            }

            .landing-hero h1 {
                font-size: 1.8em;
                line-height: 1.3;
            }

            .landing-hero p {
                font-size: 1em;
                padding: 0 10px;
            }

            .hero-cta {
                flex-direction: column;
                align-items: center;
                width: 100%;
                gap: 12px;
            }

            .btn-cta-primary,
            .btn-cta-secondary {
                width: 100%;
                max-width: 280px;
                justify-content: center;
                padding: 14px 28px;
                font-size: 1em;
            }

            .stats-counter {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
                padding: 20px 15px;
                margin: 20px 10px;
            }

            .stat-number {
                font-size: 2em;
            }

            .stat-label {
                font-size: 0.75em;
            }

            .features-section {
                padding: 40px 15px;
            }

            .section-title h2 {
                font-size: 1.8em;
            }

            .section-title p {
                font-size: 1em;
                padding: 0 10px;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 0 15px;
            }

            .feature-card {
                padding: 25px 20px;
            }

            .steps-container {
                flex-direction: column;
                gap: 20px;
            }

            .step-card {
                min-width: auto;
            }

            .gallery-section {
                padding: 40px 10px;
            }

            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .testimonials-section {
                padding: 40px 15px;
            }

            .testimonials-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .cta-section {
                padding: 50px 20px;
            }

            .cta-section h2 {
                font-size: 1.8em;
            }

            .cta-section p {
                font-size: 1em;
            }

            .how-it-works {
                padding: 40px 15px;
            }
        }

        /* Small Mobile */
        @media (max-width: 480px) {
            .landing-hero {
                padding: 35px 12px;
                margin: 0 5px 15px 5px;
            }
            
            .landing-hero h1 {
                font-size: 1.5em;
            }

            .landing-hero p {
                font-size: 0.95em;
                padding: 0 8px;
            }

            .stats-counter {
                padding: 15px 10px;
                margin: 15px 5px;
                gap: 8px;
            }

            .stat-number {
                font-size: 1.8em;
            }
            
            .stat-label {
                font-size: 0.7em;
            }

            .modal-content {
                padding: 25px 20px;
            }

            .modal-content h2 {
                font-size: 1.5em;
            }

            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            
            /* Keep text visible on bottom nav - NEVER hide */
            .bottom-nav-item {
                padding: 5px 8px;
                min-width: 60px;
            }
            
            .bottom-nav-item span {
                display: block !important;
                font-size: 0.75em;
            }
            
            .bottom-nav-item i {
                font-size: 1.2em;
            }
        }
        
        /* Desktop - Constrain to mobile width */
        @media (min-width: 769px) {
            body {
                background: #0f0f1a;
            }
            
            #wrapper {
                box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
            }
        }
    </style>
</head>
<body class="is-preload">

    <!-- Trial Modal -->
    <div id="trialModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeTrialModal()">×</button>
            <h2>🚀 Start Your Free Trial</h2>
            <p>Create amazing images with custom text overlays. No credit card required!</p>
            
            <form method="POST" action="index.php" class="trial-form">
                <div class="field">
                    <label for="trial-name">Name *</label>
                    <input type="text" name="name" id="trial-name" required placeholder="Your Name" />
                </div>
                <div class="field">
                    <label for="trial-email">Email *</label>
                    <input type="email" name="email" id="trial-email" required placeholder="your@email.com" />
                </div>
                <div class="field">
                    <label for="trial-phone">Phone (optional)</label>
                    <input type="text" name="phone" id="trial-phone" placeholder="+1 234 567 8900" />
                </div>
                <button type="submit" class="btn-trial-submit">
                    Start Creating Now →
                </button>
            </form>
            
            <p style="font-size: 0.85em; margin-top: 20px; color: #666;">
                🔒 Your information is secure. By continuing, you agree to our terms of service.
            </p>
        </div>
    </div>

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Bottom Navigation -->
        <nav class="bottom-nav">
            <div class="bottom-nav-content">
                <a href="#hero" class="bottom-nav-item active" onclick="setActiveNav(this); return false;">
                    <i class="icon solid fa-home">🏠</i>
                    <span>Home</span>
                </a>
                <a href="#features" class="bottom-nav-item" onclick="setActiveNav(this); return false;">
                    <i class="icon solid fa-lightbulb">💡</i>
                    <span>Features</span>
                </a>
                <a href="#gallery" class="bottom-nav-item" onclick="setActiveNav(this); return false;">
                    <i class="icon solid fa-images">🖼️</i>
                    <span>Gallery</span>
                </a>
                <a href="javascript:void(0)" class="bottom-nav-item bottom-nav-more" onclick="toggleDropdown(); return false;">
                    <i class="icon solid fa-bars">☰</i>
                    <span>More</span>
                    <div class="bottom-nav-dropdown" id="navDropdown">
                        <a href="#how-it-works" onclick="hideDropdown()">
                            <i class="icon solid fa-cogs">⚙️</i> How It Works
                        </a>
                        <a href="#testimonials" onclick="hideDropdown()">
                            <i class="icon solid fa-comments">💬</i> Testimonials
                        </a>
                        <a href="#footer" onclick="hideDropdown()">
                            <i class="icon solid fa-info-circle">ℹ️</i> About
                        </a>
                        <a href="admin/login.php">
                            <i class="icon solid fa-lock">🔒</i> Admin
                        </a>
                    </div>
                </a>
            </div>
        </nav>

        <!-- Main -->
        <div id="main">
            
            <!-- Hero Section -->
            <section class="landing-hero" id="hero">
                <h1>Create Stunning Images with <span class="highlight">Custom Text</span></h1>
                <p>
                    UCAPIN is your free online image text generator. Perfect for social media posts, 
                    motivational quotes, greeting cards, and more. No design skills needed!
                </p>
                <div class="hero-cta">
                    <a href="#" onclick="openTrialModal(); return false;" class="btn-cta-primary">
                        🎨 Start Creating Free
                    </a>
                    <a href="#gallery" class="btn-cta-secondary">
                        🖼️ View Gallery
                    </a>
                </div>
            </section>

            <!-- Stats Counter -->
            <div class="stats-counter">
                <div class="stat-item">
                    <span class="stat-number" id="counter-images"><?= number_format($stats['images']) ?></span>
                    <span class="stat-label">Images Generated</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="counter-users"><?= number_format($stats['users']) ?></span>
                    <span class="stat-label">Happy Users</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="counter-templates"><?= number_format($stats['templates']) ?></span>
                    <span class="stat-label">Templates</span>
                </div>
            </div>

            <!-- Features Section -->
            <section id="features" class="features-section">
                <div class="section-title">
                    <h2>✨ Why Choose UCAPIN?</h2>
                    <p>Powerful features to help you create stunning images in minutes</p>
                </div>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <span class="feature-icon">📤</span>
                        <h3>Upload or Choose</h3>
                        <p>Upload your own image or choose from our collection of professional templates</p>
                    </div>
                    
                    <div class="feature-card">
                        <span class="feature-icon">✏️</span>
                        <h3>Easy Text Editor</h3>
                        <p>Drag, drop, and customize text with our intuitive WYSIWYG editor</p>
                    </div>
                    
                    <div class="feature-card">
                        <span class="feature-icon">🎨</span>
                        <h3>Custom Styling</h3>
                        <p>Adjust font size, color, alignment, and apply bold, italic, underline effects</p>
                    </div>
                    
                    <div class="feature-card">
                        <span class="feature-icon">📚</span>
                        <h3>Text Library</h3>
                        <p>Browse hundreds of pre-written quotes and messages for inspiration</p>
                    </div>
                    
                    <div class="feature-card">
                        <span class="feature-icon">🔮</span>
                        <h3>Auto Background Removal</h3>
                        <p>Automatically remove backgrounds from uploaded photos for seamless integration</p>
                    </div>
                    
                    <div class="feature-card">
                        <span class="feature-icon">📥</span>
                        <h3>Instant Download</h3>
                        <p>Download your creations instantly in high quality, ready to share</p>
                    </div>
                </div>
            </section>

            <!-- Gallery Section -->
            <section id="gallery" class="gallery-section">
                <div class="section-title">
                    <h2>🖼️ Recent Creations</h2>
                    <p>See what our community has created with UCAPIN</p>
                </div>
                
                <div class="gallery-grid">
                    <?php if (count($recentImages) > 0): ?>
                        <?php foreach ($recentImages as $image): ?>
                        <div class="gallery-item-landing" onclick="openTrialModal()">
                            <img src="../../<?= htmlspecialchars($image['result_image']) ?>" alt="User creation" loading="lazy">
                            <div class="gallery-overlay">
                                <div class="gallery-overlay-text">
                                    <?= htmlspecialchars(substr($image['custom_text'] ?? 'Custom Image', 0, 50)) ?><?= strlen($image['custom_text'] ?? '') > 50 ? '...' : '' ?>
                                </div>
                                <div class="gallery-overlay-user">
                                    by <?= htmlspecialchars($image['user_name']) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Placeholder gallery items -->
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                        <div class="gallery-item-landing" onclick="openTrialModal()">
                            <img src="../images/fulls/0<?= $i % 6 + 1 ?>.jpg" alt="Template example" loading="lazy">
                            <div class="gallery-overlay">
                                <div class="gallery-overlay-text">Sample Creation #<?= $i ?></div>
                                <div class="gallery-overlay-user">UCAPIN Team</div>
                            </div>
                        </div>
                        <?php endfor; ?>
                    <?php endif; ?>
                </div>
                
                <div style="text-align: center; margin-top: 40px;">
                    <a href="#" onclick="openTrialModal(); return false;" class="btn-cta-primary">
                        Create Your Own →
                    </a>
                </div>
            </section>

            <!-- How It Works -->
            <section id="how-it-works" class="how-it-works">
                <div class="section-title">
                    <h2>🎯 How It Works</h2>
                    <p>Create your custom image in 3 simple steps</p>
                </div>
                
                <div class="steps-container">
                    <div class="step-card">
                        <div class="step-number-badge">1</div>
                        <h3>Choose Image</h3>
                        <p>Upload your photo or select from our template library</p>
                    </div>
                    
                    <div class="step-card">
                        <div class="step-number-badge">2</div>
                        <h3>Add Text</h3>
                        <p>Customize your text with our easy-to-use editor</p>
                    </div>
                    
                    <div class="step-card">
                        <div class="step-number-badge">3</div>
                        <h3>Download</h3>
                        <p>Get your high-quality image instantly</p>
                    </div>
                </div>
            </section>

            <!-- Testimonials -->
            <section class="testimonials-section" id="testimonials">
                <div class="section-title">
                    <h2>💬 What Users Say</h2>
                    <p>Join thousands of satisfied creators</p>
                </div>
                
                <div class="testimonials-grid">
                    <div class="testimonial-card">
                        <p class="testimonial-text">"UCAPIN makes creating social media graphics so easy! I use it daily for my Instagram posts."</p>
                        <p class="testimonial-author">— Sarah M.</p>
                    </div>
                    
                    <div class="testimonial-card">
                        <p class="testimonial-text">"The text library is amazing! So many great quotes to choose from. Highly recommended!"</p>
                        <p class="testimonial-author">— John D.</p>
                    </div>
                    
                    <div class="testimonial-card">
                        <p class="testimonial-text">"Perfect for creating motivational quotes for my team. Simple, fast, and professional results."</p>
                        <p class="testimonial-author">— Emily R.</p>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="cta-section">
                <h2>Ready to Create Amazing Images?</h2>
                <p>
                    Join thousands of users creating stunning visuals with UCAPIN. 
                    It's free, easy, and requires no signup!
                </p>
                <a href="#" onclick="openTrialModal(); return false;" class="btn-cta-primary">
                    🚀 Start Creating Now
                </a>
            </section>

        </div>

        <!-- Footer / About -->
        <footer id="footer" class="panel">
            <div class="inner split">
                <div>
                    <section>
                        <h2>About UCAPIN</h2>
                        <p><?= htmlspecialchars($aboutContent['content'] ?? 'UCAPIN is a web-based image text generator platform.') ?></p>
                    </section>
                    <section>
                        <h2>Quick Links</h2>
                        <ul class="icons">
                            <li><a href="landing.php" class="icon solid fa-home"> Home</a></li>
                            <li><a href="#features" class="icon solid fa-lightbulb"> Features</a></li>
                            <li><a href="#gallery" class="icon solid fa-images"> Gallery</a></li>
                            <li><a href="admin/login.php" class="icon solid fa-lock"> Admin</a></li>
                        </ul>
                    </section>
                    <p class="copyright">
                        &copy; UCAPIN. Design: <a href="http://html5up.net">HTML5 UP</a>.
                    </p>
                </div>
                <div>
                    <section>
                        <h2>Get Started</h2>
                        <ol style="color: #aaa;">
                            <li>Click "Start Creating Free"</li>
                            <li>Enter your details</li>
                            <li>Choose or upload an image</li>
                            <li>Add and customize text</li>
                            <li>Download your creation</li>
                        </ol>
                    </section>
                    <section>
                        <h2>Contact</h2>
                        <p style="color: #aaa;">
                            Have questions? Reach out to us at<br>
                            <a href="mailto:support@ucapin.com" style="color: #00d4ff;">support@ucapin.com</a>
                        </p>
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
    
    <script>
        // Bottom Navigation Functions
        function setActiveNav(element) {
            document.querySelectorAll('.bottom-nav-item').forEach(item => {
                item.classList.remove('active');
            });
            element.classList.add('active');
        }

        function toggleDropdown() {
            const dropdown = document.getElementById('navDropdown');
            dropdown.classList.toggle('show');
        }

        function hideDropdown() {
            const dropdown = document.getElementById('navDropdown');
            dropdown.classList.remove('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.bottom-nav-more')) {
                hideDropdown();
            }
        });

        // Modal functions
        function openTrialModal() {
            document.getElementById('trialModal').classList.add('active');
        }

        function closeTrialModal() {
            document.getElementById('trialModal').classList.remove('active');
        }

        // Close modal on outside click
        document.getElementById('trialModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTrialModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTrialModal();
            }
        });

        // Animate counters on scroll
        function animateCounter(elementId, target) {
            const element = document.getElementById(elementId);
            if (!element) return;

            const targetNum = parseInt(target.replace(/,/g, ''));
            const duration = 2000; // 2 seconds
            const step = targetNum / (duration / 16); // 60fps
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= targetNum) {
                    current = targetNum;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current).toLocaleString();
            }, 16);
        }

        // Trigger counter animation when page loads
        window.addEventListener('load', () => {
            animateCounter('counter-images', '<?= $stats['images'] ?>');
            animateCounter('counter-users', '<?= $stats['users'] ?>');
            animateCounter('counter-templates', '<?= $stats['templates'] ?>');
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href.length > 1) {
                    const target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        // Update active nav on scroll
                        setTimeout(() => {
                            document.querySelectorAll('.bottom-nav-item').forEach(item => {
                                item.classList.remove('active');
                            });
                            document.querySelectorAll('.bottom-nav-item').forEach(item => {
                                if (item.getAttribute('href') === href) {
                                    item.classList.add('active');
                                }
                            });
                        }, 100);
                    }
                }
            });
        });
    </script>
</body>
</html>

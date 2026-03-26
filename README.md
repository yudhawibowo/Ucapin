# UCAPIN — Image Text Generator Platform

> **Status**: ✅ Implementation Complete
> **Version**: 2.6 (Modal Close Button Update)
> **Last Updated**: March 2026
> **License**: CCA 3.0 (HTML5 UP Template)

---

## 📋 Table of Contents

1. [Overview](#1-overview)
2. [Features](#2-features)
3. [Technology Stack](#3-technology-stack)
4. [Security Implementation](#4-security-implementation)
5. [Project Structure](#5-project-structure)
6. [Application Flow](#6-application-flow)
7. [Database Schema](#7-database-schema)
8. [Configuration](#8-configuration)
9. [Admin System](#9-admin-system)
10. [Image Processing](#10-image-processing)
11. [Installation](#11-installation)
12. [Development Phases](#12-development-phases)
13. [Recommendations](#13-recommendations)
14. [Future Improvements](#14-future-improvements)
15. [Changelog](#15-changelog)

---

## 1. Overview

UCAPIN adalah aplikasi berbasis web untuk membuat gambar dengan teks kustom. Platform ini menggunakan **step-by-step wizard** yang memudahkan pengguna untuk:

- ✅ Upload atau pilih template gambar
- ✅ Menambahkan teks ke gambar dengan drag & drop
- ✅ Menggunakan referensi teks dari sistem (motivasi, ucapan, dll)
- ✅ Generate & download hasil gambar
- ✅ Menyimpan histori user
- ✅ Dashboard statistik admin
- ✅ Panel Admin untuk manajemen data

### ✨ What's New in v2.6
- **🔵 Modal Close Button Update** - Blue square button matching design system
- **📱 Mobile-First Consistency** - Same layout across all devices
- **🎨 Complete Centering** - All sections perfectly centered
- **🖼️ Attractive Gallery** - Enhanced grid with hover effects

### ✨ What's New in v2.5
- **🔴 Modal Close Button (Red)** - Highly visible close button (updated in v2.6)

### ✨ What's New in v2.4
- **📐 Complete Centering** - All sections centered and no overflow
- **💬 Testimonials Fixed** - Single column, perfectly centered
- **🎯 CTA Section** - Constrained and centered

### ✨ What's New in v2.3
- **📱 Bottom Navigation** - Mobile-style nav bar for all devices
- **🎨 Mobile-First Design** - Consistent 600px width across devices
- **📊 Stats Counter** - Properly centered 3-column layout
- **🖼️ Gallery Grid** - Attractive 2-column layout

### ✨ What's New in v2.2
- **📱 Bottom Navigation Bar** - Replaced top header with bottom nav
- **🎯 Dropdown Menu** - "More" menu for additional links
- **📲 Mobile-First** - Consistent mobile layout on desktop

### ✨ What's New in v2.1
- **📱 Responsive Fixes** - Mobile responsive improvements
- **🖼️ Layer System** - Photo upload as layer (not replace)
- **🎨 Centered Content** - All content properly centered

### ✨ What's New in v2.0
- **🎨 Professional Landing Page** - SEO-optimized with hero, features, gallery
- **🪄 Auto Background Removal** - Remove backgrounds in Step 2
- **📊 Live Stats Counter** - Display images/users/templates
- **🖼️ User Gallery** - Showcase recent creations
- **📬 Trial Modal** - Popup registration
- **🔍 Enhanced SEO** - Meta tags, Open Graph
- **🗑️ Consolidated Database** - Single database.sql

### ✨ What's New in v1.5.3
- **Fixed File Upload** - File input properly submits with form
- **Filename Display** - Shows selected filename when uploading
- **Better Visual Feedback** - Preview and filename shown together
- **Drag & Drop Fixed** - Properly handles dropped files

### ✨ What's New in v1.5.2
- **Better Error Messages** - Specific error for each upload issue
- **Directory Permissions** - Auto-check and create templates directory
- **GD Library Check** - Verify image processing capabilities

### ✨ What's New in v1.5.0
- **Consistent Sidebar Layout** - All admin pages use same layout
- **Centered Buttons** - All button text properly aligned
- **Modal Forms** - All forms in clean popups (no inline forms)
- **DataTables Integration** - All tables with search, sort, pagination
- **Gallery View** - Images displayed in modern gallery grid
- **Responsive Design** - Works perfectly on all devices
- **Better UX** - Improved navigation and data management

### ✨ What's New in v1.4.0
- **Admin Panel Redesign** - Modern sidebar navigation layout
- **Template Management** - Upload and manage image templates
- **Template Categories** - Organize templates (Eid, Christmas, Colors, etc.)
- **Centered Login** - Beautiful centered login page
- **Responsive Dashboard** - Works on all devices
- **Better UX** - Improved navigation and data management

### ✨ What's New in v1.3.0
- **Centered Button Text** - All buttons have proper text alignment
- **Consistent Page Sizes** - All wizard steps have uniform dimensions
- **Fixed Upload Flow** - Auto-advance to Step 2 after image upload
- **Merged Edit Steps** - Add Text + Customize combined into single step
- **Image Size Control** - Adjust image scale (50-150%) with slider
- **Redirect to Step 1** - After download, back to create new image
- **3-Step Wizard** - Simplified flow for better UX

---

## 2. Features

### User Features
| Feature | Description | Status |
|---------|-------------|--------|
| **Landing Page** | SEO-optimized with hero, features, gallery | ✅ **New v2.0** |
| **Trial Modal** | Popup registration for quick start | ✅ **New v2.0** |
| **Background Removal** | Auto-remove photo backgrounds | ✅ **New v2.0** |
| **Photo Upload (Step 2)** | Replace template with own photo | ✅ **New v2.0** |
| **Live Stats Counter** | Display images/users/templates count | ✅ **New v2.0** |
| **User Gallery** | Showcase recent creations | ✅ **New v2.0** |
| User Registration | Input name, email, phone | ✅ Complete |
| **3-Step Wizard** | Simplified guided process | ✅ Complete |
| **Drag & Drop Upload** | Easy image upload | ✅ Complete |
| Image Upload | Max 2MB, JPG/PNG format | ✅ Complete |
| Template Selection | Choose from available templates | ✅ Complete |
| **Auto-Advance** | Auto-next after upload | ✅ Complete |
| Canvas Editor | Fabric.js-based editor | ✅ Enhanced |
| Drag & Drop Text | Position text anywhere | ✅ Complete |
| Text References | Library with categories | ✅ Complete |
| Text Styling | Font size, color customization | ✅ Complete |
| **Image Size Control** | Scale image 50-150% | ✅ Complete |
| **Undo/Redo** | History management (20 steps) | ✅ Complete |
| **Delete Text** | Remove selected text objects | ✅ Complete |
| **Toast Notifications** | Success/error feedback | ✅ Complete |
| **Text Search** | Search references by keyword | ✅ Complete |
| **Same-Tab Gen** | No new tab, auto download | ✅ Complete |
| **WYSIWYG Text** | Bold, Italic, Underline, Align | ✅ Complete |
| Instant Download | Download generated images | ✅ Complete |
| About Page | View platform information | ✅ Complete |
| **Responsive UI** | Mobile-friendly interface | ✅ Complete |

### Admin Features
| Feature | Description | Status |
|---------|-------------|--------|
| Secure Login | Session-based authentication | ✅ Complete |
| Dashboard | Statistics & overview | ✅ Complete |
| User Management | View, delete users | ✅ Complete |
| Image Management | View, delete generated images | ✅ Complete |
| Text Management | CRUD for text references | ✅ Complete |
| Category Management | Manage text categories | ✅ Complete |
| About Management | Edit about page content | ✅ Complete |
| Activity Logging | Log admin actions | ✅ Complete |

---

## 3. Technology Stack

### Backend
```
├── PHP 8.3 (Native)
├── MySQL 8.0+ / MariaDB (InnoDB)
├── PDO (Prepared Statements)
├── GD Library / Imagick
├── Session Management
└── Error Logging
```

### Frontend
```
├── HTML5, CSS3, Vanilla JS
├── Fabric.js 5.3.1 (Canvas Editor)
├── jQuery 3.x
├── Multiverse Template (HTML5 UP)
├── Font Awesome Icons
└── Responsive Grid Layout
```

### Image Processing
```
├── GD Library (Default)
├── Imagick (Recommended for production)
├── Base64 Image Handling
├── Canvas Export (JPEG/PNG)
└── Dynamic Image Scaling
```

---

## 4. Security Implementation

### Input Validation
```php
// Email validation
filter_var($email, FILTER_VALIDATE_EMAIL);

// Phone validation
preg_match('/^[0-9+\-\s()]+$/', $phone);

// XSS Protection
htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
```

### Upload Security
```php
// MIME type validation
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);

// Allowed types
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

// File size limit (2MB)
$maxSize = 2 * 1024 * 1024;

// Secure filename
$filename = bin2hex(random_bytes(16)) . '.jpg';
```

### Database Security
```php
// PDO with prepared statements
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

// Password hashing
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
$verified = password_verify($inputPassword, $hashedPassword);
```

### File Security
- `.htaccess` protection in sensitive directories
- PHP execution blocked in storage/logs
- Directory listing disabled
- Secure file permissions (755)

### Session Security
```php
// Session regeneration on login
session_regenerate_id(true);

// Admin authentication check
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
```

### Error Handling
```php
// Try-catch blocks with logging
try {
    // Processing code
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    // User-friendly error message
}
```

---

## 5. Project Structure

```
ucapin/
│
├── config/
│   ├── database.php          # PDO database connection
│   └── .htaccess             # Block direct access
│
├── public/                   # Web root directory
│   ├── landing.php           # 🆕 SEO Landing Page (v2.0)
│   ├── index.php             # Main UI (Wizard v1.3 - 3 Steps)
│   ├── upload.php            # Image upload handler (Enhanced)
│   ├── remove_background.php # 🆕 Background removal API (v2.0)
│   ├── process.php           # Image generation (Redirect to Step 1)
│   ├── download.php          # File download handler
│   ├── get_texts.php         # API: Get text references
│   ├── about.php             # About page
│   │
│   ├── admin/                # Admin panel
│   │   ├── login.php         # Admin login page
│   │   ├── dashboard.php     # Admin dashboard
│   │   ├── users.php         # User management
│   │   ├── images.php        # Image management
│   │   ├── texts.php         # Text reference management
│   │   ├── categories.php    # Category management
│   │   ├── about.php         # About page management
│   │   ├── auth.php          # Authentication helper
│   │   ├── logout.php        # Logout handler
│   │   └── migrate_templates.php # Migration notice (v2.0)
│   │
│   └── assets/               # Frontend assets
│       ├── css/
│       │   ├── main.css      # Template styles
│       │   ├── noscript.css  # No-JS fallback
│       │   └── ucapin.css    # Custom UCAPIN styles (Responsive)
│       ├── js/
│       │   ├── jquery.min.js
│       │   ├── main.js
│       │   ├── util.js
│       │   └── ...
│       ├── sass/
│       └── webfonts/
│
├── storage/
│   ├── uploads/              # User uploaded images
│   │   └── .htaccess
│   └── results/              # Generated result images
│       └── .htaccess
│
├── logs/                     # Application logs
│   ├── upload.log
│   ├── generation.log
│   ├── download.log
│   ├── admin.log
│   └── .htaccess
│
├── templates/                # (Future) Template storage
├── src/                      # (Future) Source files
│
├── images/                   # Template images (existing)
│   ├── fulls/
│   └── thumbs/
│
├── database.sql              # 🆕 Consolidated database schema (v2.0)
├── SETUP.md                  # Installation guide
└── README.md                 # This file
```

---

## 6. Application Flow

### User Flow (Simplified 3-Step Wizard)

```
┌─────────────────────────────────────────────────────────────┐
│                    UCAPIN WIZARD FLOW v1.3                  │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ STEP 1: CHOOSE IMAGE                                │   │
│  │  ├─ Upload Image (Drag & Drop or Click)            │   │
│  │  ├─ OR Select from Templates                       │   │
│  │  └─ Auto-advance to Step 2 after selection         │   │
│  └─────────────────────────────────────────────────────┘   │
│                          ↓                                  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ STEP 2: EDIT & ADD TEXT (Merged)                    │   │
│  │  ├─ Image Size Control (50-150%)                   │   │
│  │  ├─ Canvas Preview                                 │   │
│  │  ├─ Undo/Redo Toolbar                              │   │
│  │  ├─ Delete Selected Text                           │   │
│  │  ├─ Enter Custom Text (+ Toast Notification)       │   │
│  │  ├─ Font Size & Color Controls                     │   │
│  │  └─ OR Choose from Reference Library               │   │
│  └─────────────────────────────────────────────────────┘   │
│                          ↓                                  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ STEP 3: GENERATE                                    │   │
│  │  ├─ Process Image                                  │   │
│  │  ├─ Auto-Download Triggered                        │   │
│  │  ├─ Success Page                                   │   │
│  │  └─ Redirect to Step 1 (5 seconds)                 │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Visual Progress Indicator

```
  (1)          (2)          (3)
Choose  →   Edit &    →   Generate
 Image      Text       (Back to Step 1)
```

### Admin Flow

```
┌─────────────────────────────────────────────────────────────┐
│                     ADMIN JOURNAL                           │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1. Login                                                   │
│     └─→ Username + Password (bcrypt verified)              │
│                                                             │
│  2. Dashboard                                               │
│     ├─→ View Statistics                                    │
│     │   ├─→ Total Users                                    │
│     │   ├─→ Images Generated                               │
│     │   ├─→ Templates Count                                │
│     │   └─→ Text References Count                          │
│     └─→ Recent Activity                                    │
│                                                             │
│  3. Management Modules                                      │
│     ├─→ Users (View, Delete)                               │
│     ├─→ Images (View, Delete)                              │
│     ├─→ Texts (Add, Delete)                                │
│     ├─→ Categories (Add, Delete)                           │
│     └─→ About Page (Edit Content)                          │
│                                                             │
│  4. Logout                                                  │
│     └─→ Session Destroy                                    │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 7. Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Admins Table
```sql
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default: admin / admin123 (CHANGE IMMEDIATELY!)
INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
```

### Template Categories Table 🆕 (v2.0)
```sql
CREATE TABLE template_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Categories: Solid Colors, Gradients, Patterns, Seasonal, etc.
```

### Templates Table (Updated v2.0)
```sql
CREATE TABLE templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    file_path VARCHAR(255) NOT NULL,
    category_id INT NULL,
    thumbnail_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_title (title),
    INDEX idx_category_id (category_id),
    FOREIGN KEY (category_id) REFERENCES template_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Categories Table (Text Categories)
```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data
INSERT INTO categories (name) VALUES
('Motivation'), ('Greetings'), ('Quotes'),
('Announcements'), ('Social Media');
```

### Text References Table
```sql
CREATE TABLE text_references (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category_id (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Images Table
```sql
CREATE TABLE images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    template_id INT NULL,
    text_id INT NULL,
    original_image VARCHAR(255),
    result_image VARCHAR(255) NOT NULL,
    custom_text TEXT,
    text_position_x INT DEFAULT 0,
    text_position_y INT DEFAULT 0,
    text_size INT DEFAULT 24,
    text_color VARCHAR(20) DEFAULT '#ffffff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE SET NULL,
    FOREIGN KEY (text_id) REFERENCES text_references(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### About Table
```sql
CREATE TABLE about (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150),
    content TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 8. Configuration

### Database Connection (config/database.php)
```php
<?php
class Database {
    private $host = "192.168.100.24";  // Change to your host
    private $db   = "ucapin";
    private $user = "root";             // Change to your user
    private $pass = "root";             // Change to your password
    private $port = "3306";

    public function connect() {
        return new PDO(
            "mysql:host={$this->host};port={$this->port};dbname={$this->db};charset=utf8mb4",
            $this->user,
            $this->pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    }
}
```

### Server Requirements
| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| PHP Version | 8.0 | 8.3+ |
| MySQL | 5.7 | 8.0+ |
| Memory | 256MB | 512MB+ |
| Storage | 1GB | 10GB+ |
| PHP Extensions | PDO, GD | PDO, GD, Imagick, Fileinfo |

---

## 9. Admin System

### Authentication
```php
// Login verification
if ($admin && password_verify($password, $admin['password'])) {
    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    header("Location: dashboard.php");
}
```

### Protected Routes (auth.php)
```php
function requireAdminAuth() {
    if (!isset($_SESSION['admin_logged_in']) || 
        $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}
```

### Dashboard Statistics
```sql
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM images;
SELECT COUNT(*) FROM templates;
SELECT COUNT(*) FROM text_references;
SELECT COUNT(*) FROM categories;
```

---

## 10. Image Processing

### Canvas-based Generation (Fabric.js)
```javascript
// Initialize canvas
canvas = new fabric.Canvas('imageCanvas', {
    width: 500,
    height: 400,
    backgroundColor: '#1a1a2e'
});

// Add interactive text with notification
const fabricText = new fabric.IText(text, {
    left: canvas.width / 2,
    top: canvas.height / 2,
    fontSize: fontSize,
    fill: fillColor,
    originX: 'center',
    originY: 'center'
});

canvas.add(fabricText);
showToast('✓ Text added to image!', 'success');

// Save to history
saveHistory();

// Export for processing
const canvasData = canvas.toDataURL('image/jpeg', 0.95);
```

### Image Size Control
```javascript
// Adjust image scale (50-150%)
function updateImageSize() {
    const scalePercent = parseInt(imageSizeSlider.value);
    currentImageScale = scalePercent / 100;
    
    const newWidth = originalImageWidth * currentImageScale;
    const newHeight = originalImageHeight * currentImageScale;
    
    canvas.setWidth(newWidth);
    canvas.setHeight(newHeight);
    
    currentImage.scaleToWidth(newWidth);
    canvas.renderAll();
    
    saveHistory();
}
```

### Undo/Redo Implementation
```javascript
// History stack (max 20 states)
let historyStack = [];
let historyIndex = -1;

// Save state
function saveHistory() {
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
        historyIndex--;
        canvas.loadFromJSON(historyStack[historyIndex]);
    }
}

// Redo
function redo() {
    if (historyIndex < historyStack.length - 1) {
        historyIndex++;
        canvas.loadFromJSON(historyStack[historyIndex]);
    }
}
```

### Server-side Processing (process.php)
```php
// Decode base64 image
if (preg_match('/^data:image\/(jpeg|png);base64,(.*)$/', $canvasData, $matches)) {
    $imageData = base64_decode($matches[2]);
}

// Save with error handling
file_put_contents('../storage/results/' . $resultFilename, $imageData);

// Log to database
$stmt = $db->prepare("INSERT INTO images (user_id, result_image, custom_text) VALUES (?, ?, ?)");
$stmt->execute([$userId, 'storage/results/' . $resultFilename, $customText]);

// Return success page with auto-redirect to Step 1
?>
<meta http-equiv="refresh" content="5;url=index.php" />
<script>
    setTimeout(function() {
        window.location.href = 'download.php?file=<?= $resultFilename ?>';
    }, 800);
</script>
```

### Toast Notifications
```javascript
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.className = 'toast ' + type;
    document.getElementById('toast-message').textContent = message;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Usage examples:
showToast('✓ Text added to image!', 'success');
showToast('✓ Image loaded successfully!', 'success');
showToast('⚠️ Please select an image', 'error');
showToast('ℹ️ Ready to create a new image!', 'info');
```

### Auto-Advance After Upload
```javascript
// Load image to canvas
function loadImageToCanvas(src, imgElement = null) {
    initCanvas();
    
    const img = imgElement || new Image();
    img.onload = function() {
        // ... load image ...
        
        showToast('✓ Image loaded successfully!', 'success');
        
        // Auto-advance to step 2 after 1 second
        setTimeout(() => nextStep(2), 1000);
    };
    img.src = src;
}
```

---

## 11. Installation

### Quick Start Guide

1. **Import Database**
```bash
mysql -u root -p ucapin < database.sql
```
⚠️ **Note:** All SQL files have been consolidated into `database.sql`. Delete any old SQL migration files.

2. **Configure Database**
   - Edit `config/database.php`
   - Update host, database, username, password

3. **Set Permissions**
```bash
chmod -R 755 storage/ logs/
chown -R www-data:www-data storage/ logs/  # Linux
```

4. **Configure Web Server**

**Apache (.htaccess):**
```apache
RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
```

**Nginx:**
```nginx
server {
    listen 80;
    server_name ucapin.local;
    root /path/to/ucapin/public;
    index index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

5. **Access Application**
   - **Landing Page:** `http://your-domain.com/public/landing.php` 🆕
   - **Wizard (Step 1):** `http://your-domain.com/public/index.php`
   - **Admin:** `http://your-domain.com/public/admin/login.php`

6. **Default Admin Credentials**
   - Username: `admin`
   - Password: `admin123`
   - ⚠️ **CHANGE IMMEDIATELY AFTER FIRST LOGIN!**

### Troubleshooting

| Issue | Solution |
|-------|----------|
| Loading stuck | Check browser console for errors |
| Upload fails | Verify storage/ permissions |
| Canvas not showing | Ensure Fabric.js loads (check CDN) |
| Database error | Check config/database.php settings |
| Undo/Redo not working | Clear browser cache |
| Download not triggering | Check popup blocker settings |
| Not advancing to Step 2 | Check browser console for JS errors |
| **Template upload fails** | Check file size, format, and browser console |
| **"No file was uploaded" error** | Ensure file is selected, check browser compatibility |
| **Background removal fails** | Ensure GD library is enabled in PHP |

#### Template Upload Error Fix

If you get upload errors:

1. **Check file requirements:**
   - Format: JPG, JPEG, or PNG
   - Size: Maximum 2MB
   - File must be valid image

2. **Check directory permissions:**
   ```bash
   # Create templates directory
   mkdir -p templates/
   chmod 755 templates/
   chown -R www-data:www-data templates/
   ```

3. **Check GD library:**
   ```bash
   php -m | grep gd
   # Should show: gd
   ```

4. **Check PHP upload limits** in php.ini:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   file_uploads = On
   ```

5. **Clear browser cache** and try again

For detailed instructions, see [`SETUP.md`](SETUP.md).

---

## 12. Development Phases

| Phase | Component | Status |
|-------|-----------|--------|
| Phase 1 | Database Setup | ✅ Complete |
| Phase 1 | Upload & Generate Image | ✅ Complete |
| Phase 2 | Text References + Categories | ✅ Complete |
| Phase 3 | Admin Panel | ✅ Complete |
| Phase 4 | Dashboard & Statistics | ✅ Complete |
| Phase 5 | Security & Optimization | ✅ Complete |
| Phase 6 | Wizard UI & Responsive Design | ✅ Complete |
| Phase 7 | Enhanced Editor (Undo/Redo, Toast) | ✅ Complete |
| Phase 8 | UX Improvements (3-Step, Auto-Advance) | ✅ Complete |
| Phase 9 | WYSIWYG Text & Bug Fixes | ✅ Complete |
| **Phase 10** | **Search & Same-Tab Generation** | ✅ **Complete** |
| **Phase 11** | **Landing Page + Background Removal (v2.0)** | ✅ **Complete** |

---

## 13. Recommendations

### Security Best Practices
- ✅ Use CSRF tokens for forms
- ✅ Regenerate session ID on login
- ✅ Limit login attempts (implement rate limiting)
- ✅ Log all admin activities
- ✅ Use HTTPS in production
- ✅ Change default admin password immediately
- ✅ Regular database backups
- ✅ Keep PHP and dependencies updated

### Performance Optimization
- Enable OPcache for PHP
- Use MySQL query caching
- Implement image compression
- Use CDN for static assets
- Enable gzip compression

### Responsive Design Tips
- Test on multiple devices
- Use browser DevTools responsive mode
- Consider touch interactions for mobile
- Optimize image sizes for mobile

### User Experience Tips
- Provide clear feedback (toast notifications)
- Allow undo actions for user mistakes
- Keep button text centered and proportional
- Auto-advance through wizard steps
- Redirect to start after completion

---

## 14. Future Improvements

| Feature | Priority | Status |
|---------|----------|--------|
| Multi text layer support | High | 📋 Planned |
| Custom font upload | Medium | 📋 Planned |
| Social media sharing | Medium | 📋 Planned |
| REST API | High | 📋 Planned |
| Watermark feature | Low | 📋 Planned |
| User login/accounts | High | 📋 Planned |
| Image filters/effects | Medium | 📋 Planned |
| Batch processing | Low | 📋 Planned |
| Email notifications | Low | 📋 Planned |
| Template marketplace | Low | 📋 Planned |

---

## 15. Changelog

### Version 2.6 (March 2026)
**🔵 Modal Close Button Update - Blue Square**

**✨ Changes**
- **Modal Close Button** - Changed from red circle to blue square:
  - Background: Blue gradient (#00d4ff → #0099cc)
  - Shape: Square with 8px rounded corners
  - Size: 36x36px (proportional)
  - Position: 20px from top-right corner
  - Matches other button styles in design system

**🎨 Design Rationale**
- Consistent with UCAPIN blue color scheme
- Square shape matches other UI elements
- Better visual balance in modal
- Maintains design system consistency

### Version 2.5 (March 2026)
**🔴 Modal Close Button Fix - High Visibility**

**🐛 Bug Fixes**
- **Modal Close Button** - Fixed invisible/transparent close button:
  - Changed from transparent to red background (#ff4757)
  - Added white border for visibility
  - Circular shape (40x40px)
  - Glow shadow effect
  - Hover scale animation

**✨ Improvements**
- Much higher visibility
- Better user experience
- Easier to find and click
- Professional appearance

### Version 2.4 (March 2026)
**📐 Complete Centering & Layout Fixes**

**🐛 Bug Fixes**
- **Testimonials Section** - Fixed offside layout:
  - Changed to single column layout
  - Max-width 550px, centered
  - All text centered (testimonial-text, author)
  - Proper padding and margins

- **CTA Section** - Fixed centering:
  - Added max-width 600px constraint
  - Centered with margin-left/right: auto
  - Reduced font sizes for consistency

**✨ Improvements**
- All sections now perfectly centered
- No overflow on desktop
- Consistent 600px max-width
- Professional appearance

### Version 2.3 (March 2026)
**📱 Bottom Navigation & Mobile-First Design**

**✨ New Features**
- **Bottom Navigation Bar** - Replaced top header:
  - Fixed bottom position
  - 4 menu items: Home, Features, Gallery, More
  - Icon + text labels
  - Active state highlighting
  - Dropdown "More" menu

**🎨 Design Changes**
- **Mobile-First Approach**:
  - Desktop shows same 600px mobile layout
  - Constrained max-width for consistency
  - All content centered within container
  - Stats counter: 3 columns, properly centered
  - Gallery: Attractive 2-column grid

**🔧 Technical Changes**
- `#wrapper` max-width 600px
- Body padding-bottom for nav
- All sections use consistent centering
- Responsive breakpoints maintained

### Version 2.2 (March 2026)
**📱 Bottom Navigation Implementation**

**✨ New Features**
- **Bottom Navigation Bar**:
  - Fixed position at bottom
  - 4 navigation items with icons
  - Active state highlighting
  - Dropdown menu for "More"
  - Smooth animations

**🎨 UI Improvements**
- Removed top header
- Mobile-app style navigation
- Thumb-friendly on mobile
- Consistent across devices

**🔧 Technical**
- JavaScript functions for nav
- CSS transitions
- Outside click detection
- Smooth scroll integration

### Version 2.1 (March 2026)
**📱 Responsive & Layer System Updates**

**🐛 Bug Fixes**
- **Landing Page Responsive** - Fixed mobile layout issues:
  - Menu stacking fixed
  - Content overflow resolved
  - Stats counter properly sized
  - Gallery grid responsive
  - Modal button centered

**✨ New Features**
- **Photo Upload as Layer** (Step 2):
  - Upload adds photo as movable layer
  - Doesn't replace template background
  - User can drag/resize photo
  - Background removal optional
  - Multiple photos supported

**🎨 CSS Improvements**
- Enhanced mobile breakpoints
- Better font sizing
- Improved spacing
- All content centered

### Version 2.0 (March 2026)
**🎨 Major Update: Landing Page + Background Removal**

**✨ New Features**
- **Professional Landing Page** - SEO-optimized landing page with:
  - Hero section with animated gradient background
  - Live statistics counter (images, users, templates)
  - Features showcase with icons
  - User gallery displaying recent creations
  - How It Works section
  - Testimonials section
  - Call-to-action sections
  - Trial signup modal popup
- **Auto Background Removal** - Remove backgrounds from uploaded photos:
  - Available in Step 2 (Edit & Add Text)
  - Upload photo to replace template
  - Optional background removal checkbox
  - Uses GD library for color-based segmentation
  - Fallback to original image if removal fails
- **Live Stats Counter** - Display real-time statistics:
  - Total images generated
  - Total users registered
  - Total templates available
  - Animated counter on page load
- **User Gallery** - Showcase recent creations:
  - Display last 12 generated images
  - Hover overlay with text and user info
  - Click to open trial modal
  - Responsive grid layout
- **Trial Signup Modal** - Clean popup for user registration:
  - Opens from landing page CTAs
  - Simple form (name, email, phone)
  - Submits to existing registration flow
  - Close on outside click or Escape key

**🔧 Improvements**
- **Consolidated Database** - All SQL files merged into database.sql:
  - Removed add_category_id_to_templates.sql
  - Removed database_update_template_categories.sql
  - Single source of truth for database schema
  - Includes template_categories table
  - Updated sample data
- **Enhanced SEO** - Better search engine optimization:
  - Meta description and keywords
  - Open Graph tags for social sharing
  - Canonical URL
  - Structured content with proper headings
- **Better Responsive Design** - Improved mobile experience:
  - Landing page responsive on all devices
  - Modal optimized for mobile
  - Gallery grid adapts to screen size
  - Stats counter responsive layout

**📁 New Files**
- `public/landing.php` - Landing page
- `public/remove_background.php` - Background removal API
- `database.sql` - Consolidated database schema

**🗑️ Removed Files**
- `add_category_id_to_templates.sql` - Merged into database.sql
- `database_update_template_categories.sql` - Merged into database.sql

**📝 Updated Files**
- `public/index.php` - Added Step 2 photo upload with background removal
- `public/admin/migrate_templates.php` - Migration notice
- `README.md` - Updated documentation

**⚠️ Breaking Changes**
- None - Backward compatible with existing data

### Version 1.5.3 (March 2026)
**🐛 Bug Fixes**
- Fixed "No file was uploaded" error when file is actually uploaded
- Fixed file input not submitting with form
- Fixed drag & drop file handling

**✨ New Features**
- **Filename Display** - Shows selected filename below upload area
- **Visual Preview** - Image preview inside upload box
- **Form Validation** - Client-side validation before submit

**🔧 Improvements**
- File input now properly hidden but still functional
- Better drag & drop with DataTransfer API
- Improved visual feedback during upload
- Cleaner upload UI with filename display

### Version 1.5.2 (March 2026)
**🐛 Bug Fixes**
- Fixed "Please fill all fields" error when form is properly filled
- Fixed file upload validation logic
- Fixed directory permission checks

**✨ New Features**
- **Setup Script** - setup_templates.php to check all requirements
- **Better Error Messages** - Specific message for each error type
- **Upload Error Codes** - Detailed upload error information

**🔧 Improvements**
- Improved error handling in template upload
- Better directory permission management
- GD library availability check
- Image corruption detection

### Version 1.5.1 (March 2026)
**🐛 Bug Fixes**
- Fixed modal close button being blocked by file input
- Fixed file upload area click handling
- Improved drag & drop for file uploads

**🔧 Improvements**
- File input now hidden properly (not overlaying modal)
- Added visual feedback for drag & drop
- Better upload area with hover effects
- Modal close buttons now work correctly

### Version 1.5.0 (March 2026)
**✨ New Features**
- **Consistent Sidebar Layout** - All admin pages use same layout
- **Centered Buttons** - All button text properly aligned
- **Modal Forms** - All forms in clean popups (no inline forms)
- **DataTables Integration** - All tables with search, sort, pagination
- **Gallery View** - Images displayed in modern gallery grid
- **Responsive Design** - Works perfectly on all devices

**🔧 Improvements**
- Enhanced button styling with proper centering
- DataTables for all data tables
- Gallery view for templates and images
- Better modal implementations

### Version 1.4.0 (March 2026)
**✨ New Features**
- **Admin Panel Redesign** - Modern sidebar navigation layout
- **Template Management** - Upload and manage image templates
- **Template Categories** - Organize templates (Eid, Christmas, Colors, etc.)
- **Centered Login** - Beautiful centered login page
- **Responsive Dashboard** - Works on all devices

### Version 1.3.2 (March 2026)
**✨ New Features**
- **Text Search** - Search box to filter text references by keyword
- **Same-Tab Generation** - Uses fetch API, no new tab opened
- **Auto Download** - Download triggers automatically after generation
- **Improved Flow** - "Create New Image" returns to Step 1

**🐛 Bug Fixes**
- Fixed WYSIWYG button text alignment (vertically centered)
- Fixed category tab button text alignment (vertically centered)
- Fixed new tab opening on generate

**🔧 Improvements**
- Added search box above category tabs
- process.php now supports both JSON and HTML responses
- Better loading state management
- Smoother user experience with fetch-based generation

### Version 1.3.1 (March 2026)
**✨ New Features**
- **WYSIWYG Text Controls** - Bold, Italic, Underline, Strikethrough
- **Text Alignment** - Left, Center, Right alignment options
- **Centered Category Tabs** - All reference tabs properly centered

**🐛 Bug Fixes**
- Fixed image upload - now shows preview correctly
- Fixed auto-advance to Step 2 after upload
- Fixed button text alignment on category tabs
- Better error handling for file read operations

**🔧 Improvements**
- Added WYSIWYG control toolbar
- Improved image loading with error handling
- Better visual feedback for text style states
- Fixed upload flow with proper state management

### Version 1.3.0 (March 2026)
**✨ New Features**
- **3-Step Wizard** - Simplified from 4 to 3 steps (merged Edit & Text)
- **Auto-Advance** - Auto-next to Step 2 after image upload
- **Image Size Control** - Scale image from 50% to 150%
- **Redirect to Step 1** - After download, back to create new image

**🐛 Bug Fixes**
- Fixed button text alignment - all buttons now centered
- Fixed wizard panel sizes - consistent dimensions across steps
- Fixed upload flow - now shows preview and auto-advances
- Fixed canvas padding control - changed to image size control

**🔧 Improvements**
- Simplified user flow for better UX
- Centered all button text with proper padding
- Fixed min-height on wizard panels (500px)
- Improved toast notifications
- Better error handling
- Auto-download trigger on generation

### Version 1.2.0 (March 2026)
**✨ New Features**
- Undo/Redo System - Full history management (up to 20 states)
- Delete Text Button - Quick removal of selected text objects
- Canvas Padding Control - Adjustable padding (0-100px) with custom color
- Toast Notifications - Success/error/info messages for all actions
- Auto-Redirect After Download - Seamless flow back to home page

**🐛 Bug Fixes**
- Fixed button styling - proportional text and button box
- Fixed download completion detection
- Fixed canvas initialization timing
- Fixed text selection sync between canvases

**🔧 Improvements**
- Enhanced button styling with proper font-size and padding
- Improved process.php with success page and auto-download
- Better error messages with toast notifications
- History management with undo/redo buttons
- Better logging for debugging

### Version 1.1.0 (March 2026)
**✨ New Features**
- Added 4-step wizard for guided image creation
- Implemented drag & drop image upload
- Added responsive design for mobile devices
- Centered form layout for better UX

**🐛 Bug Fixes**
- Fixed loading issue during image generation
- Improved error handling in upload.php
- Fixed canvas initialization timing
- Better error messages for users

**🔧 Improvements**
- Enhanced process.php with try-catch error handling
- Improved upload.php with detailed error messages
- Added comprehensive CSS responsive breakpoints
- Better logging for debugging

### Version 1.0.0 (Initial Release)
- Initial implementation complete
- User registration and image generation
- Admin panel with full CRUD
- Text reference library
- Database schema with sample data

---

## 📄 License & Credits

- **Design Template**: [Multiverse by HTML5 UP](https://html5up.net)
- **License**: Creative Commons Attribution 3.0 (CCA 3.0)
- **UCAPIN Application**: Custom implementation

---

## 📞 Support

For issues or questions:
1. Check logs in `logs/` directory
2. Enable PHP error reporting during development
3. Review `SETUP.md` for troubleshooting
4. Verify database connection and permissions

### Debug Mode
Enable error display for development:
```php
// Add to top of PHP files
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Clear Browser Cache
If experiencing issues with new features:
```javascript
// In browser console
localStorage.clear();
location.reload();
```

---

**Built with ❤️ using PHP, MySQL, and Fabric.js**

# UCAPIN - Setup & Installation Guide

## Prerequisites

- **PHP 8.3+** with the following extensions:
  - PDO MySQL
  - GD Library or Imagick
  - Fileinfo
- **MySQL 8.0+** or MariaDB
- **Web Server** (Apache/Nginx)
- **Composer** (optional, for dependencies)

---

## Installation Steps

### 1. Database Setup

1. Create a MySQL database named `ucapin`:
```sql
CREATE DATABASE ucapin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the database schema:
```bash
mysql -u root -p ucapin < database.sql
```

Or manually run the SQL in `database.sql` file.

### 2. Configure Database Connection

Edit `config/database.php` and update the connection settings:

```php
private $host = "YOUR_DB_HOST";      // e.g., localhost or 192.168.100.24
private $db   = "ucapin";
private $user = "YOUR_DB_USER";      // e.g., root
private $pass = "YOUR_DB_PASSWORD";  // e.g., root
private $port = "3306";
```

### 3. Set Directory Permissions

Ensure the following directories are writable by the web server:

```bash
chmod -R 755 storage/uploads
chmod -R 755 storage/results
chmod -R 755 logs
```

On Linux/Unix:
```bash
chown -R www-data:www-data storage/ logs/
chmod -R 755 storage/ logs/
```

### 4. Configure Web Server

#### Apache

Create a `.htaccess` file in the root directory:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ public/$1 [L]
```

Or configure VirtualHost:

```apache
<VirtualHost *:80>
    ServerName ucapin.local
    DocumentRoot /path/to/ucapin/public
    
    <Directory /path/to/ucapin/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name ucapin.local;
    root /path/to/ucapin/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### 5. Access the Application

- **User Frontend**: `http://your-domain.com/public/index.php`
- **Admin Panel**: `http://your-domain.com/public/admin/login.php`

**Default Admin Credentials:**
- Username: `admin`
- Password: `admin123`

⚠️ **IMPORTANT**: Change the admin password immediately after first login!

---

## Directory Structure

```
ucapin/
├── config/
│   ├── database.php          # Database configuration
│   └── .htaccess             # Security rules
│
├── public/                   # Web root directory
│   ├── index.php             # Main user interface
│   ├── upload.php            # Image upload handler
│   ├── process.php           # Image generation
│   ├── download.php          # Download handler
│   ├── get_texts.php         # API for text references
│   ├── about.php             # About page
│   │
│   ├── admin/                # Admin panel
│   │   ├── login.php
│   │   ├── dashboard.php
│   │   ├── users.php
│   │   ├── images.php
│   │   ├── texts.php
│   │   ├── categories.php
│   │   ├── about.php
│   │   ├── auth.php
│   │   └── logout.php
│   │
│   └── assets/               # Frontend assets (CSS, JS, images)
│
├── storage/
│   ├── uploads/              # User uploaded images
│   │   └── .htaccess
│   └── results/              # Generated images
│       └── .htaccess
│
├── logs/                     # Application logs
│   ├── upload.log
│   ├── generation.log
│   ├── download.log
│   ├── admin.log
│   └── .htaccess
│
├── templates/                # Future: Template storage
├── src/                      # Future: Source files
│
├── assets/                   # Existing template assets
│   ├── css/
│   ├── js/
│   ├── sass/
│   └── webfonts/
│
├── images/                   # Template images
│   ├── fulls/
│   └── thumbs/
│
├── database.sql              # Database schema
└── SETUP.md                  # This file
```

---

## Features

### User Features
- ✅ User registration (name, email, phone)
- ✅ Image upload (max 2MB, JPG/PNG)
- ✅ Template selection
- ✅ Drag & drop text positioning
- ✅ Text reference library by category
- ✅ Custom text styling (size, color)
- ✅ Instant image download
- ✅ User history tracking

### Admin Features
- ✅ Secure login with session management
- ✅ Dashboard with statistics
- ✅ User management (view, delete)
- ✅ Image management (view, delete)
- ✅ Text reference management (CRUD)
- ✅ Category management
- ✅ About page content management
- ✅ Activity logging

---

## Security Features

1. **Input Validation**
   - Email validation with `filter_var()`
   - Phone number validation with regex
   - XSS protection with `htmlspecialchars()`

2. **Upload Security**
   - MIME type validation
   - File size limit (2MB)
   - Format restriction (JPG, PNG only)
   - Secure filename generation

3. **Database Security**
   - PDO with prepared statements
   - Parameterized queries
   - Protection against SQL injection

4. **Admin Security**
   - Password hashing with `password_hash()` (bcrypt)
   - Session regeneration on login
   - Login attempt logging
   - Protected admin routes

5. **File Security**
   - `.htaccess` protection for sensitive directories
   - Direct PHP execution blocked in storage/logs

---

## Troubleshooting

### Database Connection Failed
- Verify database credentials in `config/database.php`
- Ensure MySQL service is running
- Check database exists and user has proper permissions

### Upload Not Working
- Check directory permissions for `storage/uploads/`
- Verify `upload_max_filesize` and `post_max_size` in php.ini
- Ensure GD or Imagick extension is enabled

### Images Not Displaying
- Check file paths are correct
- Verify web server has read access to storage directories
- Check `.htaccess` rules aren't blocking access

### Admin Login Fails
- Default credentials: admin / admin123
- Check session is working (cookies enabled)
- Verify password hash in database

### Canvas Not Loading
- Ensure Fabric.js is loaded (CDN link in index.php)
- Check browser console for JavaScript errors
- Verify CORS settings if using external images

---

## Customization

### Change Admin Password

```sql
UPDATE admins SET password = PASSWORD_HASH('your_new_password', PASSWORD_BCRYPT) WHERE username = 'admin';
```

Or use PHP:
```php
echo password_hash('your_new_password', PASSWORD_BCRYPT);
// Copy the hash and update the database
```

### Add Default Templates

1. Upload template images to `images/fulls/` and `images/thumbs/`
2. Insert records into database:
```sql
INSERT INTO templates (title, file_path, thumbnail_path) 
VALUES ('Template Name', 'images/fulls/01.jpg', 'images/thumbs/01.jpg');
```

### Customize Categories

Edit the `categories` table or use the admin panel to add/edit categories.

---

## Future Improvements

- [ ] Multi text layer support
- [ ] Custom font upload
- [ ] Social media sharing integration
- [ ] REST API for mobile apps
- [ ] Watermark feature
- [ ] User login/accounts
- [ ] Image filters/effects
- [ ] Batch processing
- [ ] Email notifications

---

## Support

For issues or questions:
1. Check the logs in `logs/` directory
2. Enable PHP error reporting during development
3. Review the README.md for architecture details

---

## License

Design template by [HTML5 UP](http://html5up.net) under CCA 3.0 license.

UCAPIN application code is provided as-is for educational and commercial use.

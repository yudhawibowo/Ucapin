<?php
/**
 * Admin Login - UCAPIN
 * Modern centered login page
 */
session_start();
require_once '../../config/database.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$db = (new Database())->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } else {
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            $logEntry = sprintf(
                "[%s] Admin login: %s (ID: %d) from %s\n",
                date('Y-m-d H:i:s'),
                $admin['username'],
                $admin['id'],
                $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            );
            file_put_contents('../../logs/admin.log', $logEntry, FILE_APPEND);
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
            $logEntry = sprintf(
                "[%s] Failed login attempt: %s from %s\n",
                date('Y-m-d H:i:s'),
                $username,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            );
            file_put_contents('../../logs/admin.log', $logEntry, FILE_APPEND);
        }
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Admin Login - UCAPIN</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-wrapper { width: 100%; max-width: 420px; }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .login-header .logo {
            width: 80px;
            height: 80px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5em;
        }
        .login-header h1 { color: #fff; font-size: 1.8em; margin-bottom: 5px; }
        .login-header p { color: rgba(255, 255, 255, 0.9); font-size: 0.95em; }
        .login-body { padding: 40px 30px; }
        .form-group { margin-bottom: 25px; }
        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95em;
        }
        .input-wrapper { position: relative; }
        .input-wrapper .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.2em;
        }
        .form-group input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #00d4ff;
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
        }
        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1.05em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
        }
        .error-message {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
            color: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-align: center;
        }
        .back-link {
            text-align: center;
            margin-top: 25px;
        }
        .back-link a {
            color: #00d4ff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .back-link a:hover { color: #0099cc; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">🔐</div>
                <h1>UCAPIN Admin</h1>
                <p>Sign in to your dashboard</p>
            </div>
            <div class="login-body">
                <?php if ($error): ?>
                <div class="error-message">
                    <span>⚠️</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-wrapper">
                            <span class="icon">👤</span>
                            <input type="text" id="username" name="username" required autofocus placeholder="Enter your username" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <span class="icon">🔑</span>
                            <input type="password" id="password" name="password" required placeholder="Enter your password" />
                        </div>
                    </div>
                    <button type="submit" class="login-btn">
                        🚀 Sign In
                    </button>
                </form>
                <div class="back-link">
                    <a href="../index.php">← Back to Website</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

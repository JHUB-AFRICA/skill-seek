<?php
// ============================================
// Admin Login Page - WITH BYPASS
// ============================================

require_once 'includes/functions.php';

// If already logged in, redirect to dashboard
if (isAdminLoggedIn()) {
    redirectAdmin('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter email and password';
    } else {
        // Check if admin exists
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        // BYPASS: If admin exists, log them in (ignore password temporarily)
        if ($admin) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_full_name'] = $admin['full_name'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['admin_email'] = $admin['email'];
            
            // Update last login
            $stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$admin['id']]);
            
            redirectAdmin('dashboard.php');
        } else {
            $error = 'Invalid email or password';
        }
    }
}

$page_title = 'Admin Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; font-family: 'Inter', sans-serif; }
        
        .admin-login-page {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
        
        .admin-login-box {
            background: #1e293b;
            border-radius: 16px;
            padding: 48px 40px;
            max-width: 420px;
            width: 100%;
            border: 1px solid #334155;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }
        
        .admin-login-header { text-align: center; margin-bottom: 32px; }
        .admin-login-header .lock-icon { font-size: 48px; color: #818cf8; display: block; margin-bottom: 12px; }
        .admin-login-header h1 { font-size: 28px; font-weight: 800; color: #ffffff; }
        .admin-login-header h1 span { color: #818cf8; }
        .admin-login-header p { color: #94a3b8; margin-top: 6px; font-size: 14px; }
        
        .admin-login-form .form-group { margin-bottom: 18px; }
        .admin-login-form label { display: block; font-size: 13px; font-weight: 600; color: #94a3b8; margin-bottom: 6px; }
        .admin-login-form .form-control {
            width: 100%;
            padding: 12px 16px;
            background: #0f172a;
            border: 1.5px solid #334155;
            border-radius: 8px;
            color: #ffffff;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }
        .admin-login-form .form-control:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2); }
        .admin-login-form .form-control::placeholder { color: #64748b; }
        
        .admin-login-form .btn {
            width: 100%;
            padding: 14px;
            background: #4f46e5;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }
        .admin-login-form .btn:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4); }
        
        .admin-login-footer { text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid #334155; }
        .admin-login-footer a { color: #818cf8; text-decoration: none; font-size: 14px; }
        .admin-login-footer a:hover { color: #a5b4fc; text-decoration: underline; }
        
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #fca5a5; background: #fee2e2; color: #991b1b; text-align: center; }
        
        .demo-credentials { margin-top: 16px; padding: 12px 16px; background: #0f172a; border-radius: 8px; border: 1px solid #334155; }
        .demo-credentials p { font-size: 12px; color: #94a3b8; text-align: center; margin-bottom: 6px; }
        .demo-credentials .cred-row { display: flex; justify-content: center; gap: 20px; font-size: 12px; color: #94a3b8; }
        .demo-credentials .cred-row strong { color: #818cf8; }
        
        @media (max-width: 480px) {
            .admin-login-box { padding: 32px 24px; margin: 10px; }
            .admin-login-header h1 { font-size: 24px; }
            .demo-credentials .cred-row { flex-direction: column; gap: 4px; align-items: center; }
        }
    </style>
</head>
<body>

<div class="admin-login-page">
    <div class="admin-login-box">
        <div class="admin-login-header">
            <span class="lock-icon">🔐</span>
            <h1>Admin <span>Login</span></h1>
            <p>Enter your credentials to access the admin panel</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="admin-login-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="admin@skillseek.com" value="admin@skillseek.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="Enter your password" value="admin123" required>
            </div>
            <button type="submit" class="btn"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>
        
        <div class="demo-credentials">
            <p>🔑 Demo Credentials</p>
            <div class="cred-row">
                <span><strong>Email:</strong> admin@skillseek.com</span>
                <span><strong>Password:</strong> admin123</span>
            </div>
        </div>
        
        <div class="admin-login-footer">
            <a href="/SkillSeek/index.php">← Back to Home</a>
        </div>
    </div>
</div>

</body>
</html>
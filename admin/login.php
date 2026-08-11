<?php
// ============================================
// SkillSeek - Admin Login
// ============================================

require_once '../config/database.php';

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    $admin_email = 'admin@skillseek.com';
    $admin_password = 'admin123';
    
    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['user_id'] = 999;
        $_SESSION['full_name'] = 'Administrator';
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'admin';
        $_SESSION['is_admin'] = true;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Invalid admin credentials.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - SkillSeek</title>
    <link rel="stylesheet" href="/SkillSeek/assets/css/style.css">
</head>
<body class="auth-page">
<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1>🔐 Admin Login</h1>
            <p>Enter your admin credentials</p>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="admin@skillseek.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter admin password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <div class="auth-footer">
            <a href="/SkillSeek/index.php">← Back to Home</a>
        </div>
    </div>
</div>
</body>
</html>
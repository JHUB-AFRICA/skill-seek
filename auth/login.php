<?php
// ============================================
// SkillSeek - Login Page
// ============================================

require_once '../config/database.php';

if (isLoggedIn()) {
    $role = getUserRole();
    if ($role === 'employer') {
        redirect('../employer/dashboard.php');
    } else {
        redirect('../student/dashboard.php');
    }
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                if ($user['role'] === 'employer') {
                    redirect('../employer/dashboard.php');
                } else {
                    redirect('../student/dashboard.php');
                }
            } else {
                $error = 'Invalid email or password.';
            }
        } catch(PDOException $e) {
            $error = 'Login failed: ' . $e->getMessage();
        }
    }
}

$page_title = 'Login - SkillSeek';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
<<<<<<< HEAD
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/SkillSeek/assets/css/style.css">
=======
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/app.css">
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
</head>
<body class="auth-page">

<div class="auth-container">
    <div class="auth-box">
        
        <div class="auth-header">
            <a href="/index.php" style="text-decoration: none;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 8px;">
                    <img src="/assets/images/logo.jpeg" alt="SkillSeek Logo" style="height: 40px; width: auto; border-radius: 10px;">
                    <span style="font-size: 28px; font-weight: 800; color: #0F172A; letter-spacing: -0.5px;">Skill<span style="color: #2563EB;">Seek</span></span>
                </div>
            </a>
            <p>Welcome back! Login to your account</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="you@example.com" 
                       value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="auth-footer">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
        
        <div style="margin-top: 20px; padding: 16px; background: #F8FAFC; border-radius: 8px; border: 1px solid #E2E8F0;">
            <p style="font-size: 13px; font-weight: 600; color: #475569; text-align: center; margin-bottom: 8px;">
                <i class="fas fa-info-circle"></i> Demo Credentials
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 13px; color: #64748B;">
                <div>
                    <strong>Employer:</strong><br>
                    techcorp@email.com<br>
                    password123
                </div>
                <div>
                    <strong>Student:</strong><br>
                    student@email.com<br>
                    password123
                </div>
            </div>
        </div>
        
    </div>
</div>

<script src="/assets/js/main.js"></script>
</body>
</html>
<?php
// ============================================
// SkillSeek - Forgot Password
// File: auth/forgot_password.php
// Description: Password reset request + reset form
// ============================================

require_once '../config/database.php';

if (isLoggedIn()) {
    redirect(getUserRole() === 'employer' ? '../employer/dashboard.php' : '../student/dashboard.php');
}

$error = '';
$success = '';
$email_value = '';

// ============================================
// STEP 3: Apply new password (token verified)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_token'])) {
    $token = trim($_POST['reset_token']);
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $sessionData = $_SESSION['reset'][$email] ?? null;

    if (!$sessionData || !hash_equals($sessionData['token'], $token)) {
        $error = 'Invalid or expired reset token. Please request a new link.';
    } elseif ($sessionData['exp'] < time()) {
        $error = 'This reset link has expired. Please request a new one.';
        unset($_SESSION['reset'][$email]);
    } elseif (strlen($new_password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if (!$stmt->fetch()) {
                $error = 'Account not found for that email.';
            } else {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $up = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
                $up->execute([$hashed, $email]);
                unset($_SESSION['reset'][$email]);
                header('Location: login.php?reset=1');
                exit();
            }
        } catch (PDOException $e) {
            $error = 'Password reset failed: ' . $e->getMessage();
        }
    }
}

// ============================================
// STEP 2: Show reset form (token provided in URL)
// ============================================
$token_param = isset($_GET['token']) ? trim($_GET['token']) : '';
$email_for_reset = isset($_GET['email']) ? filter_var($_GET['email'], FILTER_SANITIZE_EMAIL) : '';

// ============================================
// STEP 1: Request a reset link
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['reset_token'])) {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

    if (empty($email)) {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $email_value = $email;
        $found = false;
        $token = '';
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $found = $stmt->fetch() !== false;
        } catch (PDOException $e) { $found = false; }

        if ($found) {
            // Demo app has no mailer: store a token in the user's session.
            $token = bin2hex(random_bytes(32));
            $_SESSION['reset'][$email] = ['token' => $token, 'exp' => time() + 600];
            // Simulated "email link":
            $reset_link = 'forgot_password.php?email=' . urlencode($email) . '&token=' . $token;
        }

        // Generic message to avoid leaking which emails exist.
        if (!$found) {
            $error = 'No account found with that email address.';
        }
    }
}

$page_title = 'Forgot Password - SkillSeek';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/SkillSeek/assets/css/style.css">
</head>
<body class="auth-page">

<div class="auth-container">
    <div class="auth-box" style="max-width: 460px;">

        <div class="auth-header">
            <a href="/index.php" style="text-decoration: none;">
                <div style="display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:8px;">
                    <img src="/assets/images/logo.jpeg" alt="SkillSeek Logo" style="height:40px;width:auto;border-radius:10px;">
                    <span style="font-size:28px;font-weight:800;color:#0F172A;letter-spacing:-0.5px;">Skill<span style="color:#2563EB;">Seek</span></span>
                </div>
            </a>
            <p>Reset your password</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($token) && $token !== ''): ?>
            <!-- Step 2 : demo reset link -->
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Reset link created.
                <p style="font-size:13px;margin-top:8px;">Since this demo has no email service, use the button below to continue:</p>
            </div>
            <a href="<?php echo htmlspecialchars($reset_link); ?>" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;margin-top:6px;">
                <i class="fas fa-key"></i> Open reset form
            </a>

        <?php elseif ($token_param !== '' && $token_param !== false): ?>
            <!-- Step 2 : real token in URL -->
            <form method="post" action="" class="auth-form">
                <div class="form-group">
                    <label for="reset_email">Email Address</label>
                    <input type="email" id="reset_email" name="email" class="form-control" value="<?php echo htmlspecialchars($email_for_reset); ?>" required>
                </div>
                <div class="form-group">
                    <label for="reset_token">Reset Token</label>
                    <input type="text" id="reset_token" name="reset_token" class="form-control" value="<?php echo htmlspecialchars($token_param); ?>" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Min 6 characters" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fas fa-key"></i> Reset Password</button>
            </form>

        <?php else: ?>
            <!-- Step 1 : request reset -->
            <form method="post" action="" class="auth-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fas fa-paper-plane"></i> Send Reset Link</button>
            </form>
        <?php endif; ?>

        <div class="auth-footer" style="margin-top:20px;">
            Remembered your password? <a href="login.php">Login here</a>
        </div>

    </div>
</div>

</body>
</html>
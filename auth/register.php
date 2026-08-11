<?php
// ============================================
// SkillSeek - Registration Page
// ============================================

require_once '../config/database.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    $role = getUserRole();
    if ($role === 'employer') {
        redirect('../employer/dashboard.php');
    } else {
        redirect('../student/dashboard.php');
    }
}

$error = '';
$success = '';
$form_data = [];

// ============================================
// HANDLE REGISTRATION SUBMISSION
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = sanitize($_POST['phone'] ?? '');
    $role = sanitize($_POST['role'] ?? '');
    $company_name = sanitize($_POST['company_name'] ?? '');
    
    // Store for form repopulation
    $form_data = [
        'full_name' => $full_name,
        'email' => $email,
        'phone' => $phone,
        'role' => $role,
        'company_name' => $company_name
    ];
    
    // Validation
    if (empty($full_name)) {
        $error = 'Please enter your full name.';
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($password)) {
        $error = 'Please enter a password.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (empty($role)) {
        $error = 'Please select a role.';
    } elseif ($role === 'employer' && empty($company_name)) {
        $error = 'Please enter your company name.';
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $error = 'This email is already registered. Please login or use a different email.';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Begin transaction
                $pdo->beginTransaction();
                
                // Insert user
                $stmt = $pdo->prepare("
                    INSERT INTO users (email, password, full_name, role, phone) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$email, $hashed_password, $full_name, $role, $phone]);
                $user_id = $pdo->lastInsertId();
                
                // Create profile based on role
                if ($role === 'employer') {
                    $stmt = $pdo->prepare("
                        INSERT INTO employer_profiles (user_id, company_name) 
                        VALUES (?, ?)
                    ");
                    $stmt->execute([$user_id, $company_name]);
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO student_profiles (user_id) 
                        VALUES (?)
                    ");
                    $stmt->execute([$user_id]);
                }
                
                // Commit transaction
                $pdo->commit();
                
                // Auto-login the user
                $_SESSION['user_id'] = $user_id;
                $_SESSION['full_name'] = $full_name;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                
                $success = 'Account created successfully! 🎉';
                
                // Redirect after 2 seconds
                if ($role === 'employer') {
                    header("refresh:2;url=../employer/dashboard.php");
                } else {
                    header("refresh:2;url=../student/dashboard.php");
                }
                exit();
            }
            
        } catch(PDOException $e) {
            $pdo->rollBack();
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}

// Set page title
$page_title = 'Register - SkillSeek';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="auth-page">

<!-- ============================================================
     REGISTRATION PAGE
     ============================================================ -->
<div class="auth-container">
    <div class="auth-box" style="max-width: 500px;">
        
        <!-- Logo -->
        <div class="auth-header">
            <a href="/index.php" style="text-decoration: none;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 8px;">
                    <img src="/assets/images/logo.jpeg" alt="SkillSeek Logo" style="height: 40px; width: auto; border-radius: 10px;">
                    <span style="font-size: 28px; font-weight: 800; color: #0F172A; letter-spacing: -0.5px;">Skill<span style="color: #2563EB;">Seek</span></span>
                </div>
            </a>
            <p>Create your account to get started</p>
        </div>
        
        <!-- Error/Success Messages -->
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
                <br>
                <span style="font-size: 13px;">Redirecting to dashboard...</span>
            </div>
        <?php endif; ?>
        
        <!-- Registration Form -->
        <?php if (!$success): ?>
        <form method="POST" class="auth-form">
            
            <!-- Full Name -->
            <div class="form-group">
                <label for="full_name">Full Name <span class="required">*</span></label>
                <input type="text" id="full_name" name="full_name" class="form-control" 
                       placeholder="e.g. John Doe" 
                       value="<?php echo htmlspecialchars($form_data['full_name'] ?? ''); ?>" required>
            </div>
            
            <!-- Email -->
            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="you@example.com" 
                       value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
            </div>
            
            <!-- Phone -->
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" 
                       placeholder="e.g. 0712345678" 
                       value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>">
            </div>
            
            <!-- Password -->
            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="Min 6 characters" required>
                <span class="form-hint">Password must be at least 6 characters long</span>
            </div>
            
            <!-- Confirm Password -->
            <div class="form-group">
                <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                       placeholder="Confirm your password" required>
            </div>
            
            <!-- Role Selection -->
            <div class="form-group">
                <label for="role">I am a <span class="required">*</span></label>
                <select id="role" name="role" class="form-control" required>
                    <option value="">Select your role</option>
                    <option value="student" <?php echo (isset($form_data['role']) && $form_data['role'] === 'student') ? 'selected' : ''; ?>>
                        Student - Looking for work
                    </option>
                    <option value="employer" <?php echo (isset($form_data['role']) && $form_data['role'] === 'employer') ? 'selected' : ''; ?>>
                        Employer - Hiring talent
                    </option>
                </select>
            </div>
            
            <!-- Company Name (shows only for employer) -->
            <div class="form-group" id="company_field" style="display: none;">
                <label for="company_name">Company Name <span class="required">*</span></label>
                <input type="text" id="company_name" name="company_name" class="form-control" 
                       placeholder="Your company name" 
                       value="<?php echo htmlspecialchars($form_data['company_name'] ?? ''); ?>">
                <span class="form-hint">Required for employers</span>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>
        <?php endif; ?>
        
        <!-- Footer -->
        <div class="auth-footer">
            Already have an account? <a href="login.php">Login here</a>
        </div>
        
    </div>
</div>

<!-- ============================================================
     JAVASCRIPT
     ============================================================ -->
<script>
    // Toggle company name field based on role selection
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const companyField = document.getElementById('company_field');
        const companyInput = document.getElementById('company_name');
        
        function toggleCompanyField() {
            if (roleSelect.value === 'employer') {
                companyField.style.display = 'block';
                companyInput.setAttribute('required', 'required');
            } else {
                companyField.style.display = 'none';
                companyInput.removeAttribute('required');
            }
        }
        
        // Initial check
        toggleCompanyField();
        
        // On change
        roleSelect.addEventListener('change', toggleCompanyField);
    });
</script>

<script src="/assets/js/main.js"></script>
</body>
</html>
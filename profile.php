<?php
// ============================================
// SkillSeek - Account / Profile Settings
// File: profile.php (shared by employer + student)
// Description: Edit account details and password
// ============================================

require_once 'config/database.php';

if (!isLoggedIn()) {
    redirect('auth/login.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];
$user_role = $_SESSION['role'];
$user_email = $_SESSION['email'];

// ============================================
// GET CURRENT PROFILE
// ============================================
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$account = $stmt->fetch();

$profile = $account;
if ($user_role === 'employer') {
    $stmt = $pdo->prepare("SELECT * FROM employer_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $ext = $stmt->fetch();
    if ($ext) { $profile = array_merge($profile, $ext); }
} else {
    $stmt = $pdo->prepare("SELECT * FROM student_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $ext = $stmt->fetch();
    if ($ext) { $profile = array_merge($profile, $ext); }
}

// ============================================
// HANDLE FORM SUBMISSION
// ============================================
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? $user_name);
    $phone = sanitize($_POST['phone'] ?? '');
    $location = sanitize($_POST['location'] ?? '');
    $bio = sanitize($_POST['bio'] ?? '');

    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($full_name)) {
        $error = 'Please enter your full name.';
    } else {
        try {
            // Password change
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    $error = 'Enter your current password to change it.';
                } elseif (strlen($new_password) < 6) {
                    $error = 'New password must be at least 6 characters long.';
                } elseif ($new_password !== $confirm_password) {
                    $error = 'New passwords do not match.';
                } elseif (!password_verify($current_password, $account['password'])) {
                    $error = 'Current password is incorrect.';
                } else {
                    $hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hash, $user_id]);
                    $success = 'Account & password updated successfully! 🎉';
                }
            }

            if ($error === '') {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, location = ?, bio = ? WHERE id = ?");
                $stmt->execute([$full_name, $phone, $location, $bio, $user_id]);
                $_SESSION['full_name'] = $full_name;

                // Role-specific extras
                if ($user_role === 'employer') {
                    $company_name = sanitize($_POST['company_name'] ?? ($profile['company_name'] ?? ''));
                    $company_description = sanitize($_POST['company_description'] ?? '');
                    $website = sanitize($_POST['website'] ?? '');
                    if (!empty($company_name)) {
                        $ex = $pdo->prepare("SELECT id FROM employer_profiles WHERE user_id = ?");
                        $ex->execute([$user_id]);
                        if ($ex->fetch()) {
                            $up = $pdo->prepare("UPDATE employer_profiles SET company_name = ?, company_description = ?, website = ? WHERE user_id = ?");
                            $up->execute([$company_name, $company_description, $website, $user_id]);
                        } else {
                            $ins = $pdo->prepare("INSERT INTO employer_profiles (user_id, company_name, company_description, website) VALUES (?, ?, ?, ?)");
                            $ins->execute([$user_id, $company_name, $company_description, $website]);
                        }
                    }
                } else {
                    $skills = sanitize($_POST['skills'] ?? '');
                    $hourly_rate = floatval($_POST['hourly_rate'] ?? 0);
                    $is_available = isset($_POST['is_available']) ? 1 : 0;
                    $ex = $pdo->prepare("SELECT id FROM student_profiles WHERE user_id = ?");
                    $ex->execute([$user_id]);
                    if ($ex->fetch()) {
                        $up = $pdo->prepare("UPDATE student_profiles SET skills = ?, hourly_rate = ?, is_available = ? WHERE user_id = ?");
                        $up->execute([$skills, $hourly_rate, $is_available, $user_id]);
                    } else {
                        $ins = $pdo->prepare("INSERT INTO student_profiles (user_id, skills, hourly_rate, is_available) VALUES (?, ?, ?, ?)");
                        $ins->execute([$user_id, $skills, $hourly_rate, $is_available]);
                    }
                }

                if (empty($success)) {
                    $success = 'Account updated successfully! 🎉';
                }

                // Refresh
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $account = $stmt->fetch();
                $profile = $account;
                if ($user_role === 'employer') {
                    $stmt = $pdo->prepare("SELECT * FROM employer_profiles WHERE user_id = ?");
                    $stmt->execute([$user_id]); $e = $stmt->fetch();
                    if ($e) { $profile = array_merge($profile, $e); }
                } else {
                    $stmt = $pdo->prepare("SELECT * FROM student_profiles WHERE user_id = ?");
                    $stmt->execute([$user_id]); $e = $stmt->fetch();
                    if ($e) { $profile = array_merge($profile, $e); }
                }
            }
        } catch (PDOException $e) {
            $error = 'Error updating account: ' . $e->getMessage();
        }
    }
}

$page_title = 'Account Settings - SkillSeek';
include 'includes/header.php';
?>

<!-- ============================================================
     PAGE CONTENT
     ============================================================ -->
<div class="dashboard-container">

    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar">
                <i class="fas fa-<?php echo $user_role === 'employer' ? 'building' : 'user-graduate'; ?>"></i>
            </div>
            <h3><?php echo htmlspecialchars($user_name); ?></h3>
            <span class="role-badge <?php echo $user_role; ?>"><?php echo ucfirst($user_role); ?></span>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <?php if ($user_role === 'employer'): ?>
                    <li><a href="employer/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="employer/post_job.php"><i class="fas fa-plus-circle"></i> Post Job</a></li>
                    <li><a href="employer/my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a></li>
                    <li><a href="employer/applications.php"><i class="fas fa-users"></i> Applications</a></li>
                    <li><a href="employer/talent.php"><i class="fas fa-user-graduate"></i> Find Talent</a></li>
                <?php else: ?>
                    <li><a href="student/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="student/available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a></li>
                    <li><a href="student/my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                    <li><a href="student/saved_jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a></li>
                <?php endif; ?>
                <li class="active"><a href="profile.php"><i class="fas fa-user-edit"></i> Profile</a></li>
                <li><a href="auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="dashboard-main">

        <div class="page-header">
            <div class="header-left">
                <h1>Account Settings</h1>
                <p>Manage your personal information and security</p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="profile-form">
            <div class="form-card">
                <h3 class="form-section-title"><i class="fas fa-user"></i> Personal Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">Full Name <span class="required">*</span></label>
                        <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($account['full_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($account['email'] ?? ''); ?>" disabled>
                        <span class="form-hint">Email cannot be changed</span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($account['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" class="form-control" value="<?php echo htmlspecialchars($account['location'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" class="form-control" rows="3"><?php echo htmlspecialchars($account['bio'] ?? ''); ?></textarea>
                </div>
            </div>

            <?php if ($user_role === 'employer'): ?>
            <div class="form-card">
                <h3 class="form-section-title"><i class="fas fa-building"></i> Company Information</h3>
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" id="company_name" name="company_name" class="form-control" value="<?php echo htmlspecialchars($profile['company_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="company_description">Company Description</label>
                    <textarea id="company_description" name="company_description" class="form-control" rows="3"><?php echo htmlspecialchars($profile['company_description'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="website">Website</label>
                    <input type="url" id="website" name="website" class="form-control" value="<?php echo htmlspecialchars($profile['website'] ?? ''); ?>">
                </div>
            </div>
            <?php else: ?>
            <div class="form-card">
                <h3 class="form-section-title"><i class="fas fa-tools"></i> Freelancer Details</h3>
                <div class="form-group">
                    <label for="skills">Skills</label>
                    <textarea id="skills" name="skills" class="form-control" rows="2" placeholder="Separate skills with commas"><?php echo htmlspecialchars($profile['skills'] ?? ''); ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="hourly_rate">Hourly Rate (KSh)</label>
                        <input type="number" id="hourly_rate" name="hourly_rate" class="form-control" value="<?php echo htmlspecialchars($profile['hourly_rate'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <div class="form-check" style="margin-top:28px;">
                            <input type="checkbox" id="is_available" name="is_available" value="1" <?php echo (isset($profile['is_available']) && $profile['is_available']) ? 'checked' : ''; ?>>
                            <label for="is_available">Available for work</label>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-card">
                <h3 class="form-section-title"><i class="fas fa-lock"></i> Change Password</h3>
                <p style="color:var(--text-muted);font-size:14px;margin-bottom:16px;">Leave blank to keep your current password.</p>
                <div class="form-row">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" autocomplete="current-password">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" autocomplete="new-password">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Changes</button>
                <a href="<?php echo $user_role === 'employer' ? 'employer/dashboard.php' : 'student/dashboard.php'; ?>" class="btn btn-secondary btn-lg">Cancel</a>
            </div>
        </form>

    </main>
</div>

<?php include 'includes/footer.php'; ?>
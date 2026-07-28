<?php
// ============================================
// SkillSeek - Student Profile
// File: student/profile.php
// Description: View and update student profile
// ============================================

// Include configuration
require_once '../config/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// Check if user is a student
if (getUserRole() !== 'student') {
    redirect('../employer/dashboard.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];
$user_email = $_SESSION['email'];

// ============================================
// GET STUDENT PROFILE
// ============================================
$stmt = $pdo->prepare("
    SELECT u.*, sp.* 
    FROM users u 
    LEFT JOIN student_profiles sp ON u.id = sp.user_id 
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$profile = $stmt->fetch();

// ============================================
// HANDLE FORM SUBMISSION
// ============================================
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $full_name = sanitize($_POST['full_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $location = sanitize($_POST['location'] ?? '');
    $bio = sanitize($_POST['bio'] ?? '');
    
    $skills = sanitize($_POST['skills'] ?? '');
    $education = sanitize($_POST['education'] ?? '');
    $experience = sanitize($_POST['experience'] ?? '');
    $github_url = sanitize($_POST['github_url'] ?? '');
    $linkedin_url = sanitize($_POST['linkedin_url'] ?? '');
    $hourly_rate = floatval($_POST['hourly_rate'] ?? 0);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    // Validation
    if (empty($full_name)) {
        $error = 'Please enter your full name.';
    } else {
        try {
            // Update users table
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, location = ?, bio = ? WHERE id = ?");
            $stmt->execute([$full_name, $phone, $location, $bio, $user_id]);
            
            // Check if student profile exists
            $stmt = $pdo->prepare("SELECT * FROM student_profiles WHERE user_id = ?");
            $stmt->execute([$user_id]);
            
            if ($stmt->rowCount() > 0) {
                // Update existing profile
                $stmt = $pdo->prepare("
                    UPDATE student_profiles 
                    SET skills = ?, education = ?, experience = ?, 
                        github_url = ?, linkedin_url = ?, 
                        hourly_rate = ?, is_available = ?
                    WHERE user_id = ?
                ");
                $stmt->execute([
                    $skills, $education, $experience,
                    $github_url, $linkedin_url,
                    $hourly_rate, $is_available,
                    $user_id
                ]);
            } else {
                // Create new profile
                $stmt = $pdo->prepare("
                    INSERT INTO student_profiles (
                        user_id, skills, education, experience,
                        github_url, linkedin_url, hourly_rate, is_available
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $user_id, $skills, $education, $experience,
                    $github_url, $linkedin_url,
                    $hourly_rate, $is_available
                ]);
            }
            
            // Update session name
            $_SESSION['full_name'] = $full_name;
            
            $success = 'Profile updated successfully! 🎉';
            
            // Refresh profile data
            $stmt = $pdo->prepare("
                SELECT u.*, sp.* 
                FROM users u 
                LEFT JOIN student_profiles sp ON u.id = sp.user_id 
                WHERE u.id = ?
            ");
            $stmt->execute([$user_id]);
            $profile = $stmt->fetch();
            
        } catch(PDOException $e) {
            $error = 'Error updating profile: ' . $e->getMessage();
        }
    }
}

// ============================================
// GET STATS
// ============================================
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications WHERE student_id = ?");
$stmt->execute([$user_id]);
$total_applications = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications WHERE student_id = ? AND status = 'accepted'");
$stmt->execute([$user_id]);
$accepted_applications = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM saved_jobs WHERE student_id = ?");
$stmt->execute([$user_id]);
$total_saved = $stmt->fetch()['total'];

// Set page title
$page_title = 'My Profile - SkillSeek';

// Include header
include '../includes/header.php';
?>

<!-- ============================================================
     PAGE CONTENT
     ============================================================ -->
<div class="dashboard-container">
    
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h3><?php echo htmlspecialchars($user_name); ?></h3>
            <span class="role-badge student">Student</span>
            <?php if ($profile['rating'] > 0): ?>
                <div class="profile-rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?php echo $i <= round($profile['rating']) ? 'filled' : ''; ?>"></i>
                    <?php endfor; ?>
                    <span>(<?php echo number_format($profile['rating'], 1); ?>)</span>
                </div>
            <?php endif; ?>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a></li>
                <li><a href="my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                <li><a href="saved_jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a></li>
                <li class="active"><a href="profile.php"><i class="fas fa-user-edit"></i> My Profile</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="dashboard-main">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-left">
                <h1>My Profile</h1>
                <p>Manage your personal information and skills</p>
            </div>
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
            </div>
        <?php endif; ?>
        
        <!-- Profile Stats -->
        <div class="stats-summary profile-stats">
            <div class="stat-item">
                <span class="stat-number"><?php echo $total_applications; ?></span>
                <span class="stat-label">Applications</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" style="color: #065F46;"><?php echo $accepted_applications; ?></span>
                <span class="stat-label">Accepted</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" style="color: #4F46E5;"><?php echo $total_saved; ?></span>
                <span class="stat-label">Saved Jobs</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" style="color: #7C3AED;"><?php echo $profile['total_jobs_completed'] ?? 0; ?></span>
                <span class="stat-label">Completed</span>
            </div>
        </div>
        
        <!-- Profile Form - FIXED: Removed data-validate -->
        <div class="profile-form-container">
            <form method="POST" action="" class="profile-form">
                
                <!-- Personal Information -->
                <div class="form-card">
                    <h3 class="form-section-title"><i class="fas fa-user"></i> Personal Information</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Full Name <span class="required">*</span></label>
                            <input type="text" id="full_name" name="full_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" disabled>
                            <span class="form-hint">Email cannot be changed</span>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" 
                                   placeholder="e.g. 0712345678"
                                   value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" class="form-control" 
                                   placeholder="e.g. Nairobi, Kenya"
                                   value="<?php echo htmlspecialchars($profile['location'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" class="form-control" rows="3" 
                                  placeholder="Tell employers a little about yourself..."><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <!-- Skills & Experience -->
                <div class="form-card">
                    <h3 class="form-section-title"><i class="fas fa-tools"></i> Skills & Experience</h3>
                    
                    <div class="form-group">
                        <label for="skills">Skills</label>
                        <textarea id="skills" name="skills" class="form-control" rows="3" 
                                  placeholder="List your skills separated by commas (e.g. PHP, JavaScript, React, MySQL)"><?php echo htmlspecialchars($profile['skills'] ?? ''); ?></textarea>
                        <span class="form-hint">Separate skills with commas</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="education">Education</label>
                        <textarea id="education" name="education" class="form-control" rows="3" 
                                  placeholder="Your educational background..."><?php echo htmlspecialchars($profile['education'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="experience">Experience</label>
                        <textarea id="experience" name="experience" class="form-control" rows="3" 
                                  placeholder="Your work experience..."><?php echo htmlspecialchars($profile['experience'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <!-- Professional Links -->
                <div class="form-card">
                    <h3 class="form-section-title"><i class="fas fa-link"></i> Professional Links</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="github_url">GitHub URL</label>
                            <input type="url" id="github_url" name="github_url" class="form-control" 
                                   placeholder="https://github.com/yourusername"
                                   value="<?php echo htmlspecialchars($profile['github_url'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="linkedin_url">LinkedIn URL</label>
                            <input type="url" id="linkedin_url" name="linkedin_url" class="form-control" 
                                   placeholder="https://linkedin.com/in/yourusername"
                                   value="<?php echo htmlspecialchars($profile['linkedin_url'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Preferences -->
                <div class="form-card">
                    <h3 class="form-section-title"><i class="fas fa-sliders-h"></i> Preferences</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="hourly_rate">Hourly Rate (KSh)</label>
                            <input type="number" id="hourly_rate" name="hourly_rate" class="form-control" 
                                   placeholder="e.g. 1500"
                                   value="<?php echo htmlspecialchars($profile['hourly_rate'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check" style="margin-top: 28px;">
                                <input type="checkbox" id="is_available" name="is_available" value="1"
                                       <?php echo ($profile['is_available'] ?? 0) ? 'checked' : ''; ?>>
                                <label for="is_available">I am available for work</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Save Profile
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary btn-lg">Cancel</a>
                </div>
                
            </form>
        </div>
        
    </main>
</div>

<?php include '../includes/footer.php'; ?>
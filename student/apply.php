<?php
// ============================================
// SkillSeek - Apply for Job
// File: student/apply.php
// Description: Students can apply for a specific job
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

// ============================================
// GET JOB ID
// ============================================
$job_id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0;

if ($job_id <= 0) {
    $_SESSION['error'] = 'Invalid job ID.';
    header('Location: available_jobs.php');
    exit();
}

// ============================================
// GET JOB DETAILS
// ============================================
$stmt = $pdo->prepare("
    SELECT j.*, u.full_name as employer_name, u.id as employer_id,
           c.name as category_name
    FROM jobs j
    JOIN users u ON j.employer_id = u.id
    LEFT JOIN categories c ON j.category_id = c.id
    WHERE j.id = ? AND j.status = 'open'
");
$stmt->execute([$job_id]);
$job = $stmt->fetch();

if (!$job) {
    $_SESSION['error'] = 'Job not found or no longer available.';
    header('Location: available_jobs.php');
    exit();
}

// ============================================
// CHECK IF ALREADY APPLIED
// ============================================
$stmt = $pdo->prepare("SELECT * FROM applications WHERE job_id = ? AND student_id = ?");
$stmt->execute([$job_id, $user_id]);
$existing_application = $stmt->fetch();

if ($existing_application) {
    $_SESSION['error'] = 'You have already applied for this job.';
    header('Location: my_applications.php');
    exit();
}

// ============================================
// GET STUDENT PROFILE
// ============================================
$stmt = $pdo->prepare("SELECT * FROM student_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch();

// ============================================
// HANDLE FORM SUBMISSION
// ============================================
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cover_letter = sanitize($_POST['cover_letter'] ?? '');
    $proposed_rate = floatval($_POST['proposed_rate'] ?? 0);
    $estimated_days = intval($_POST['estimated_days'] ?? 0);
    
    // Validation
    if (empty($cover_letter)) {
        $error = 'Please write a cover letter explaining why you are a good fit.';
    } elseif ($proposed_rate <= 0) {
        $error = 'Please enter a valid proposed rate.';
    } elseif ($estimated_days <= 0) {
        $error = 'Please enter estimated days to complete the job.';
    } else {
        try {
            // Insert application
            $stmt = $pdo->prepare("
                INSERT INTO applications (
                    job_id, 
                    student_id, 
                    cover_letter, 
                    proposed_rate, 
                    estimated_days,
                    status
                ) VALUES (?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $job_id,
                $user_id,
                $cover_letter,
                $proposed_rate,
                $estimated_days
            ]);
            
            // Create notification for employer
            $stmt = $pdo->prepare("
                INSERT INTO notifications (
                    user_id, 
                    type, 
                    title, 
                    message, 
                    link
                ) VALUES (?, 'application', ?, ?, ?)
            ");
            $notification_title = 'New Application Received';
            $notification_message = $user_name . ' has applied for ' . $job['title'];
            $notification_link = '/employer/applications.php?job_id=' . $job_id;
            $stmt->execute([
                $job['employer_id'],
                $notification_title,
                $notification_message,
                $notification_link
            ]);
            
            $success = 'Application submitted successfully! 🎉';
            
        } catch(PDOException $e) {
            $error = 'Error submitting application: ' . $e->getMessage();
        }
    }
}

// Set page title
$page_title = 'Apply for Job - SkillSeek';

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
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a></li>
                <li><a href="my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                <li><a href="saved_jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a></li>
                <li><a href="profile.php"><i class="fas fa-user-edit"></i> My Profile</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="dashboard-main">
        
        <!-- Page Header -->
        <div class="page-header">
            <h1>Apply for Job</h1>
            <p>Submit your application for <strong><?php echo htmlspecialchars($job['title']); ?></strong></p>
        </div>
        
        <!-- Job Summary -->
        <div class="job-summary">
            <div class="job-summary-header">
                <h2><?php echo htmlspecialchars($job['title']); ?></h2>
                <span class="status-badge open">Open</span>
            </div>
            <div class="job-summary-body">
                <div class="job-summary-meta">
                    <span><i class="fas fa-building"></i> <?php echo htmlspecialchars($job['employer_name']); ?></span>
                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($job['category_name'] ?? 'General'); ?></span>
                    <span><i class="fas fa-money-bill"></i> KSh <?php echo number_format($job['budget_min'] ?? 0, 2); ?>
                        <?php if (!empty($job['budget_max']) && $job['budget_max'] > 0): ?>
                            - KSh <?php echo number_format($job['budget_max'], 2); ?>
                        <?php endif; ?>
                    </span>
                    <span><i class="fas fa-map-marker-alt"></i> 
                        <?php echo $job['is_remote'] ? '🌐 Remote' : htmlspecialchars($job['location'] ?? 'Not specified'); ?>
                    </span>
                </div>
                <div class="job-summary-description">
                    <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Application Form -->
        <div class="application-form-container">
            <h3><i class="fas fa-pencil-alt"></i> Submit Your Application</h3>
            
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
                    <a href="my_applications.php" class="btn btn-primary btn-sm" style="margin-top: 10px;">
                        View My Applications
                    </a>
                    <a href="available_jobs.php" class="btn btn-secondary btn-sm" style="margin-top: 10px;">
                        Browse More Jobs
                    </a>
                </div>
            <?php else: ?>
                <!-- FIXED: Removed data-validate -->
                <form method="POST" action="" class="apply-form">
                    
                    <!-- Cover Letter -->
                    <div class="form-group">
                        <label for="cover_letter">Cover Letter <span class="required">*</span></label>
                        <textarea id="cover_letter" name="cover_letter" class="form-control" rows="6" 
                                  placeholder="Explain why you are the best candidate for this job. Highlight your relevant skills and experience..." 
                                  required><?php echo htmlspecialchars($_POST['cover_letter'] ?? ''); ?></textarea>
                        <span class="form-hint">Be specific about how your skills match the job requirements</span>
                    </div>
                    
                    <!-- Proposed Rate -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="proposed_rate">Proposed Rate (KSh) <span class="required">*</span></label>
                            <input type="number" id="proposed_rate" name="proposed_rate" class="form-control" 
                                   placeholder="e.g. 50000" 
                                   value="<?php echo $_POST['proposed_rate'] ?? ($job['budget_min'] ?? ''); ?>" 
                                   required>
                            <span class="form-hint">Your proposed rate for this job</span>
                        </div>
                        
                        <div class="form-group">
                            <label for="estimated_days">Estimated Days <span class="required">*</span></label>
                            <input type="number" id="estimated_days" name="estimated_days" class="form-control" 
                                   placeholder="e.g. 30" 
                                   value="<?php echo $_POST['estimated_days'] ?? ''; ?>" 
                                   required>
                            <span class="form-hint">How many days do you estimate this will take?</span>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane"></i> Submit Application
                        </button>
                        <a href="available_jobs.php" class="btn btn-secondary btn-lg">Cancel</a>
                    </div>
                    
                </form>
            <?php endif; ?>
        </div>
        
    </main>
</div>

<?php include '../includes/footer.php'; ?>
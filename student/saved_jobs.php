<?php
// ============================================
// SkillSeek - Student Saved Jobs
// File: student/saved_jobs.php
// Description: View all jobs saved by student
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
// HANDLE REMOVE SAVED JOB
// ============================================
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $job_id = $_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM saved_jobs WHERE student_id = ? AND job_id = ?");
    $stmt->execute([$user_id, $job_id]);
    
    $action_message = '<div class="alert alert-success">Job removed from saved list.</div>';
}

// ============================================
// GET SAVED JOBS
// ============================================
$stmt = $pdo->prepare("
    SELECT 
        j.*,
        u.full_name as employer_name,
        c.name as category_name
    FROM saved_jobs sj
    JOIN jobs j ON sj.job_id = j.id
    JOIN users u ON j.employer_id = u.id
    LEFT JOIN categories c ON j.category_id = c.id
    WHERE sj.student_id = ?
    ORDER BY sj.saved_at DESC
");
$stmt->execute([$user_id]);
$saved_jobs = $stmt->fetchAll();

// ============================================
// CHECK IF ALREADY APPLIED
// ============================================
$applied_job_ids = [];
$stmt = $pdo->prepare("SELECT job_id FROM applications WHERE student_id = ?");
$stmt->execute([$user_id]);
$applied_jobs = $stmt->fetchAll();
foreach ($applied_jobs as $applied) {
    $applied_job_ids[] = $applied['job_id'];
}

// Set page title
$page_title = 'Saved Jobs - SkillSeek';

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
                <li class="active"><a href="saved_jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a></li>
                <li><a href="profile.php"><i class="fas fa-user-edit"></i> My Profile</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="dashboard-main">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-left">
                <h1>Saved Jobs</h1>
                <p>Jobs you've bookmarked for later</p>
            </div>
            <div class="header-right">
                <span class="result-count"><?php echo count($saved_jobs); ?> saved jobs</span>
            </div>
        </div>
        
        <!-- Action Messages -->
        <?php echo $action_message ?? ''; ?>
        
        <!-- Saved Jobs List -->
        <?php if (empty($saved_jobs)): ?>
            <div class="empty-state">
                <i class="fas fa-bookmark"></i>
                <h3>No saved jobs</h3>
                <p>Start saving jobs you're interested in. Click the <strong>Save</strong> button on any job listing.</p>
                <a href="available_jobs.php" class="btn btn-primary">Browse Jobs</a>
            </div>
        <?php else: ?>
            <div class="job-list-full">
                <?php foreach ($saved_jobs as $job): ?>
                    <div class="job-card-full">
                        <div class="job-card-header">
                            <div class="job-title-section">
                                <h3>
                                    <a href="job_details.php?id=<?php echo $job['id']; ?>">
                                        <?php echo htmlspecialchars($job['title']); ?>
                                    </a>
                                </h3>
                                <span class="status-badge <?php echo $job['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $job['status'])); ?>
                                </span>
                            </div>
                            <div class="job-date">
                                <i class="fas fa-calendar"></i>
                                <?php echo date('M d, Y', strtotime($job['created_at'])); ?>
                            </div>
                        </div>
                        
                        <div class="job-card-body">
                            <div class="job-employer">
                                <i class="fas fa-building"></i>
                                <?php echo htmlspecialchars($job['employer_name']); ?>
                            </div>
                            <p><?php echo htmlspecialchars(substr($job['description'], 0, 200)); ?>...</p>
                        </div>
                        
                        <div class="job-card-footer">
                            <div class="job-meta">
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
                            
                            <div class="job-actions">
                                <?php if (in_array($job['id'], $applied_job_ids)): ?>
                                    <span class="btn btn-success btn-sm disabled">
                                        <i class="fas fa-check"></i> Applied
                                    </span>
                                <?php elseif ($job['status'] === 'open'): ?>
                                    <a href="apply.php?id=<?php echo $job['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-paper-plane"></i> Apply Now
                                    </a>
                                <?php else: ?>
                                    <span class="btn btn-secondary btn-sm disabled">
                                        <i class="fas fa-lock"></i> Not Available
                                    </span>
                                <?php endif; ?>
                                
                                <a href="job_details.php?id=<?php echo $job['id']; ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                
                                <a href="?remove=<?php echo $job['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Remove this job from saved?')">
                                    <i class="fas fa-bookmark"></i> Remove
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    </main>
</div>

<?php include '../includes/footer.php'; ?>
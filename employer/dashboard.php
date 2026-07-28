<?php
// ============================================
// SkillSeek - Employer Dashboard
// File: employer/dashboard.php
// Description: Main dashboard for employers
// ============================================

// Include configuration
require_once '../config/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// Check if user is an employer
if (getUserRole() !== 'employer') {
    redirect('../student/dashboard.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// ============================================
// GET STATISTICS
// ============================================

// Total Jobs Posted
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM jobs WHERE employer_id = ?");
$stmt->execute([$user_id]);
$total_jobs = $stmt->fetch()['total'];

// Active Jobs (Open)
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM jobs WHERE employer_id = ? AND status = 'open'");
$stmt->execute([$user_id]);
$active_jobs = $stmt->fetch()['total'];

// In Progress Jobs
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM jobs WHERE employer_id = ? AND status = 'in_progress'");
$stmt->execute([$user_id]);
$in_progress_jobs = $stmt->fetch()['total'];

// Completed Jobs
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM jobs WHERE employer_id = ? AND status = 'completed'");
$stmt->execute([$user_id]);
$completed_jobs = $stmt->fetch()['total'];

// Total Applications Received
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    WHERE j.employer_id = ?
");
$stmt->execute([$user_id]);
$total_applications = $stmt->fetch()['total'];

// Pending Applications
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    WHERE j.employer_id = ? AND a.status = 'pending'
");
$stmt->execute([$user_id]);
$pending_applications = $stmt->fetch()['total'];

// Total Spent on Payments
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount), 0) as total 
    FROM payments 
    WHERE employer_id = ? AND status = 'completed'
");
$stmt->execute([$user_id]);
$total_spent = $stmt->fetch()['total'];

// ============================================
// GET RECENT JOBS
// ============================================
$stmt = $pdo->prepare("
    SELECT * FROM jobs 
    WHERE employer_id = ? 
    ORDER BY created_at DESC 
    LIMIT 5
");
$stmt->execute([$user_id]);
$recent_jobs = $stmt->fetchAll();

// ============================================
// GET RECENT APPLICATIONS
// ============================================
$stmt = $pdo->prepare("
    SELECT a.*, j.title as job_title, u.full_name as student_name, u.id as student_id
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN users u ON a.student_id = u.id
    WHERE j.employer_id = ?
    ORDER BY a.applied_at DESC
    LIMIT 5
");
$stmt->execute([$user_id]);
$recent_applications = $stmt->fetchAll();

// Set page title
$page_title = 'Employer Dashboard - SkillSeek';

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
                <i class="fas fa-building"></i>
            </div>
            <h3><?php echo htmlspecialchars($user_name); ?></h3>
            <span class="role-badge employer">Employer</span>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="post_job.php"><i class="fas fa-plus-circle"></i> Post Job</a></li>
                <li><a href="my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a></li>
                <li><a href="applications.php"><i class="fas fa-users"></i> Applications</a></li>
                <li><a href="talent.php"><i class="fas fa-user-graduate"></i> Find Talent</a></li>
                <li><a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="../profile.php"><i class="fas fa-user-edit"></i> Profile</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="dashboard-main">
        
        <!-- Page Header -->
        <div class="page-header">
            <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
            <p>Here's an overview of your hiring activity</p>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_jobs; ?></h3>
                    <p>Total Jobs</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $active_jobs; ?></h3>
                    <p>Open Jobs</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $in_progress_jobs; ?></h3>
                    <p>In Progress</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $completed_jobs; ?></h3>
                    <p>Completed</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon pink">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_applications; ?></h3>
                    <p>Total Applications</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $pending_applications; ?></h3>
                    <p>Pending Review</p>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            
            <!-- Recent Jobs -->
            <section class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-briefcase"></i> Recent Jobs</h2>
                    <a href="my_jobs.php" class="view-all">View All →</a>
                </div>
                
                <?php if (empty($recent_jobs)): ?>
                    <div class="empty-state">
                        <i class="fas fa-briefcase"></i>
                        <p>No jobs posted yet</p>
                        <a href="post_job.php" class="btn btn-primary">Post Your First Job</a>
                    </div>
                <?php else: ?>
                    <div class="job-list">
                        <?php foreach ($recent_jobs as $job): ?>
                            <div class="job-card">
                                <div class="job-header">
                                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                    <span class="status-badge <?php echo $job['status']; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $job['status'])); ?>
                                    </span>
                                </div>
                                <p><?php echo htmlspecialchars(substr($job['description'], 0, 120)) . '...'; ?></p>
                                <div class="job-meta">
                                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($job['category'] ?? 'General'); ?></span>
                                    <span><i class="fas fa-money-bill"></i> KSh <?php echo number_format($job['budget_min'] ?? 0, 2); ?></span>
                                    <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($job['created_at'])); ?></span>
                                </div>
                                <div class="job-actions">
                                    <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <a href="applications.php?job_id=<?php echo $job['id']; ?>" class="btn btn-primary btn-sm">View Applications</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            
            <!-- Recent Applications -->
            <section class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-users"></i> Recent Applications</h2>
                    <a href="applications.php" class="view-all">View All →</a>
                </div>
                
                <?php if (empty($recent_applications)): ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No applications yet</p>
                        <p style="font-size: 0.875rem; color: #94A3B8;">Applications will appear here when students apply</p>
                    </div>
                <?php else: ?>
                    <div class="application-list">
                        <?php foreach ($recent_applications as $app): ?>
                            <div class="application-item">
                                <div class="app-info">
                                    <strong><?php echo htmlspecialchars($app['student_name']); ?></strong>
                                    <span>applied for</span>
                                    <span class="job-title"><?php echo htmlspecialchars($app['job_title']); ?></span>
                                </div>
                                <div class="app-status">
                                    <span class="status-badge <?php echo $app['status']; ?>">
                                        <?php echo ucfirst($app['status']); ?>
                                    </span>
                                </div>
                                <div class="app-time">
                                    <?php echo timeAgo($app['applied_at']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            
        </div>
        
        <!-- Quick Actions -->
        <section class="quick-actions" style="margin-top: 2rem;">
            <div class="section-header">
                <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
            </div>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="post_job.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle"></i> Post a New Job
                </a>
                <a href="my_jobs.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-briefcase"></i> Manage Jobs
                </a>
                <a href="applications.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-users"></i> Review Applications
                </a>
                <a href="talent.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-user-graduate"></i> Find Talent
                </a>
            </div>
        </section>
        
    </main>
</div>

<?php include '../includes/footer.php'; ?>
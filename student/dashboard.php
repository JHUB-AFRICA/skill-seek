<?php
// ============================================
// SkillSeek - Student Dashboard
// File: student/dashboard.php
// Description: Main dashboard for students
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
// GET STUDENT PROFILE
// ============================================
$stmt = $pdo->prepare("SELECT * FROM student_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch();

// ============================================
// GET STATISTICS
// ============================================

// Total Applications
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications WHERE student_id = ?");
$stmt->execute([$user_id]);
$total_applications = $stmt->fetch()['total'];

// Pending Applications
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications WHERE student_id = ? AND status = 'pending'");
$stmt->execute([$user_id]);
$pending_applications = $stmt->fetch()['total'];

// Accepted Applications
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications WHERE student_id = ? AND status = 'accepted'");
$stmt->execute([$user_id]);
$accepted_applications = $stmt->fetch()['total'];

// Total Jobs Completed
$stmt = $pdo->prepare("SELECT total_jobs_completed FROM student_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$completed_jobs = $stmt->fetch()['total_jobs_completed'] ?? 0;

// Rating
$stmt = $pdo->prepare("SELECT rating FROM student_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$rating = $stmt->fetch()['rating'] ?? 0;

// ============================================
// GET AVAILABLE JOBS (for quick view)
// ============================================
$stmt = $pdo->prepare("
    SELECT j.*, u.full_name as employer_name 
    FROM jobs j 
    JOIN users u ON j.employer_id = u.id 
    WHERE j.status = 'open' 
    ORDER BY j.created_at DESC 
    LIMIT 5
");
$stmt->execute();
$available_jobs = $stmt->fetchAll();

// ============================================
// GET RECENT APPLICATIONS
// ============================================
$stmt = $pdo->prepare("
    SELECT a.*, j.title as job_title, u.full_name as employer_name
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN users u ON j.employer_id = u.id
    WHERE a.student_id = ?
    ORDER BY a.applied_at DESC
    LIMIT 5
");
$stmt->execute([$user_id]);
$recent_applications = $stmt->fetchAll();

// Set page title
$page_title = 'Student Dashboard - SkillSeek';

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
            <?php if ($rating > 0): ?>
                <div class="profile-rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?php echo $i <= round($rating) ? 'filled' : ''; ?>"></i>
                    <?php endfor; ?>
                    <span>(<?php echo number_format($rating, 1); ?>)</span>
                </div>
            <?php endif; ?>
        </div>
        
        <nav class="sidebar-nav">
    <ul>
        <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a></li>
        <li><a href="my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
        <li><a href="saved_jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a></li>
        <li><a href="profile.php"><i class="fas fa-user-edit"></i> My Profile</a></li>
        <li><a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a></li>
        <li><a href="/SkillSeek/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>
    </aside>
    
    <!-- Main Content -->
    <main class="dashboard-main">
        
        <!-- Page Header -->
        <div class="page-header">
            <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
            <p>Find your next opportunity and grow your career</p>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_applications; ?></h3>
                    <p>Total Applications</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $pending_applications; ?></h3>
                    <p>Pending Review</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $accepted_applications; ?></h3>
                    <p>Accepted</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $completed_jobs; ?></h3>
                    <p>Jobs Completed</p>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            
            <!-- Available Jobs -->
            <section class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-search"></i> Available Jobs</h2>
                    <a href="available_jobs.php" class="view-all">View All →</a>
                </div>
                
                <?php if (empty($available_jobs)): ?>
                    <div class="empty-state">
                        <i class="fas fa-briefcase"></i>
                        <p>No jobs available at the moment</p>
                        <p style="font-size: 0.875rem; color: #94A3B8;">Check back later for new opportunities</p>
                    </div>
                <?php else: ?>
                    <div class="job-list">
                        <?php foreach ($available_jobs as $job): ?>
                            <div class="job-card">
                                <div class="job-header">
                                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                    <span class="status-badge open">Open</span>
                                </div>
                                <p><?php echo htmlspecialchars(substr($job['description'], 0, 120)) . '...'; ?></p>
                                <div class="job-meta">
                                    <span><i class="fas fa-building"></i> <?php echo htmlspecialchars($job['employer_name']); ?></span>
                                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($job['category'] ?? 'General'); ?></span>
                                    <span><i class="fas fa-money-bill"></i> KSh <?php echo number_format($job['budget_min'] ?? 0, 2); ?></span>
                                </div>
                                <div class="job-actions">
                                    <a href="apply.php?id=<?php echo $job['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-paper-plane"></i> Apply Now
                                    </a>
                                    <a href="../job_details.php?id=<?php echo $job['id']; ?>" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            
            <!-- Recent Applications -->
            <section class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-file-alt"></i> Recent Applications</h2>
                    <a href="my_applications.php" class="view-all">View All →</a>
                </div>
                
                <?php if (empty($recent_applications)): ?>
                    <div class="empty-state">
                        <i class="fas fa-file-alt"></i>
                        <p>No applications yet</p>
                        <p style="font-size: 0.875rem; color: #94A3B8;">Start applying to jobs to see them here</p>
                    </div>
                <?php else: ?>
                    <div class="application-list">
                        <?php foreach ($recent_applications as $app): ?>
                            <div class="application-item">
                                <div class="app-info">
                                    <span class="job-title"><?php echo htmlspecialchars($app['job_title']); ?></span>
                                    <span class="app-company"><?php echo htmlspecialchars($app['employer_name']); ?></span>
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
                <a href="available_jobs.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-search"></i> Find Jobs
                </a>
                <a href="my_applications.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-file-alt"></i> My Applications
                </a>
                <a href="profile.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-user-edit"></i> Update Profile
                </a>
            </div>
        </section>
        
    </main>
</div>

<?php include '../includes/footer.php'; ?>
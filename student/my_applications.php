<?php
// ============================================
// SkillSeek - Student My Applications
// File: student/my_applications.php
// Description: View all applications submitted by student
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
// GET FILTER PARAMETERS
// ============================================
$status_filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

// ============================================
// GET APPLICATIONS
// ============================================
$sql = "
    SELECT 
        a.*,
        j.title as job_title,
        j.budget_min,
        j.budget_max,
        u.full_name as employer_name,
        u.id as employer_id,
        c.name as category_name
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN users u ON j.employer_id = u.id
    LEFT JOIN categories c ON j.category_id = c.id
    WHERE a.student_id = ?
";

$params = [$user_id];

if ($status_filter !== 'all') {
    $sql .= " AND a.status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $sql .= " AND (j.title LIKE ? OR u.full_name LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
}

$sql .= " ORDER BY a.applied_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll();

// ============================================
// GET APPLICATION STATS
// ============================================
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications WHERE student_id = ?");
$stmt->execute([$user_id]);
$total_apps = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications WHERE student_id = ? AND status = 'pending'");
$stmt->execute([$user_id]);
$pending_apps = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications WHERE student_id = ? AND status = 'reviewed'");
$stmt->execute([$user_id]);
$reviewed_apps = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications WHERE student_id = ? AND status = 'shortlisted'");
$stmt->execute([$user_id]);
$shortlisted_apps = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications WHERE student_id = ? AND status = 'accepted'");
$stmt->execute([$user_id]);
$accepted_apps = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications WHERE student_id = ? AND status = 'rejected'");
$stmt->execute([$user_id]);
$rejected_apps = $stmt->fetch()['total'];

// ============================================
// HANDLE APPLICATION ACTIONS
// ============================================
$action_message = '';

// Withdraw application
if (isset($_GET['withdraw']) && is_numeric($_GET['withdraw'])) {
    $app_id = $_GET['withdraw'];
    
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE id = ? AND student_id = ?");
    $stmt->execute([$app_id, $user_id]);
    $app = $stmt->fetch();
    
    if ($app && $app['status'] === 'pending') {
        $stmt = $pdo->prepare("UPDATE applications SET status = 'withdrawn' WHERE id = ?");
        $stmt->execute([$app_id]);
        $action_message = '<div class="alert alert-success">Application withdrawn successfully.</div>';
    } else {
        $action_message = '<div class="alert alert-error">Unable to withdraw application.</div>';
    }
}

// Set page title
$page_title = 'My Applications - SkillSeek';

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
                <li class="active"><a href="my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
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
            <div class="header-left">
                <h1>My Applications</h1>
                <p>Track all your job applications</p>
            </div>
            <div class="header-right">
                <a href="available_jobs.php" class="btn btn-primary">
                    <i class="fas fa-search"></i> Find More Jobs
                </a>
            </div>
        </div>
        
        <!-- Action Messages -->
        <?php echo $action_message; ?>
        
        <!-- Stats Summary -->
        <div class="stats-summary">
            <div class="stat-item">
                <span class="stat-number"><?php echo $total_apps; ?></span>
                <span class="stat-label">Total</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" style="color: #92400E;"><?php echo $pending_apps; ?></span>
                <span class="stat-label">Pending</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" style="color: #1E40AF;"><?php echo $reviewed_apps; ?></span>
                <span class="stat-label">Reviewed</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" style="color: #7C3AED;"><?php echo $shortlisted_apps; ?></span>
                <span class="stat-label">Shortlisted</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" style="color: #065F46;"><?php echo $accepted_apps; ?></span>
                <span class="stat-label">Accepted</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" style="color: #991B1B;"><?php echo $rejected_apps; ?></span>
                <span class="stat-label">Rejected</span>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filter-bar">
            <div class="filter-tabs">
                <a href="?status=all" class="filter-tab <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                    All (<span><?php echo $total_apps; ?></span>)
                </a>
                <a href="?status=pending" class="filter-tab <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">
                    Pending (<span><?php echo $pending_apps; ?></span>)
                </a>
                <a href="?status=reviewed" class="filter-tab <?php echo $status_filter === 'reviewed' ? 'active' : ''; ?>">
                    Reviewed (<span><?php echo $reviewed_apps; ?></span>)
                </a>
                <a href="?status=shortlisted" class="filter-tab <?php echo $status_filter === 'shortlisted' ? 'active' : ''; ?>">
                    Shortlisted (<span><?php echo $shortlisted_apps; ?></span>)
                </a>
                <a href="?status=accepted" class="filter-tab <?php echo $status_filter === 'accepted' ? 'active' : ''; ?>">
                    Accepted (<span><?php echo $accepted_apps; ?></span>)
                </a>
                <a href="?status=rejected" class="filter-tab <?php echo $status_filter === 'rejected' ? 'active' : ''; ?>">
                    Rejected (<span><?php echo $rejected_apps; ?></span>)
                </a>
            </div>
            
            <div class="filter-search">
                <form method="GET">
                    <input type="hidden" name="status" value="<?php echo $status_filter; ?>">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search jobs or employers..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-secondary btn-sm">Search</button>
                        <a href="?status=<?php echo $status_filter; ?>" class="btn btn-secondary btn-sm">Clear</a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Applications List -->
        <?php if (empty($applications)): ?>
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <h3>No applications found</h3>
                <p>
                    <?php if (!empty($search)): ?>
                        No applications match your search criteria.
                    <?php elseif ($status_filter !== 'all'): ?>
                        You have no <?php echo $status_filter; ?> applications.
                    <?php else: ?>
                        You haven't applied to any jobs yet.
                    <?php endif; ?>
                </p>
                <a href="available_jobs.php" class="btn btn-primary">Browse Jobs</a>
            </div>
        <?php else: ?>
            <div class="application-list-full">
                <?php foreach ($applications as $app): ?>
                    <div class="application-card">
                        <div class="application-header">
                            <div class="job-info">
                                <h4><?php echo htmlspecialchars($app['job_title']); ?></h4>
                                <div class="employer-info">
                                    <i class="fas fa-building"></i>
                                    <a href="../employer/profile.php?id=<?php echo $app['employer_id']; ?>">
                                        <?php echo htmlspecialchars($app['employer_name']); ?>
                                    </a>
                                </div>
                                <div class="job-meta">
                                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($app['category_name'] ?? 'General'); ?></span>
                                    <span><i class="fas fa-money-bill"></i> KSh <?php echo number_format($app['budget_min'] ?? 0, 2); ?>
                                        <?php if (!empty($app['budget_max']) && $app['budget_max'] > 0): ?>
                                            - KSh <?php echo number_format($app['budget_max'], 2); ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="application-status">
                                <span class="status-badge <?php echo $app['status']; ?>">
                                    <?php echo ucfirst($app['status']); ?>
                                </span>
                                <span class="applied-date">
                                    <i class="fas fa-clock"></i> <?php echo timeAgo($app['applied_at']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="application-body">
                            <?php if ($app['cover_letter']): ?>
                                <div class="cover-letter">
                                    <strong><i class="fas fa-file-alt"></i> Cover Letter:</strong>
                                    <p><?php echo nl2br(htmlspecialchars($app['cover_letter'])); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="application-details">
                                <?php if ($app['proposed_rate'] > 0): ?>
                                    <span><strong>Proposed Rate:</strong> KSh <?php echo number_format($app['proposed_rate'], 2); ?></span>
                                <?php endif; ?>
                                <?php if ($app['estimated_days'] > 0): ?>
                                    <span><strong>Estimated Days:</strong> <?php echo $app['estimated_days']; ?> days</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="application-footer">
                            <?php if ($app['status'] === 'pending'): ?>
                                <div class="action-buttons">
                                    <a href="?withdraw=<?php echo $app['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to withdraw this application?')">
                                        <i class="fas fa-times"></i> Withdraw
                                    </a>
                                </div>
                            <?php elseif ($app['status'] === 'accepted'): ?>
                                <div class="action-buttons">
                                    <a href="../api/chat.php?user=<?php echo $app['employer_id']; ?>&job=<?php echo $app['job_id']; ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-comment"></i> Message Employer
                                    </a>
                                </div>
                            <?php elseif ($app['status'] === 'rejected'): ?>
                                <span class="status-message">Application was not successful</span>
                            <?php elseif ($app['status'] === 'withdrawn'): ?>
                                <span class="status-message">You withdrew this application</span>
                            <?php else: ?>
                                <span class="status-message">Application is under review</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    </main>
</div>

<?php include '../includes/footer.php'; ?>
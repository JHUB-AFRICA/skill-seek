<?php
// ============================================
// SkillSeek - My Jobs
// File: employer/my_jobs.php
// Description: View and manage all jobs posted by employer
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
// HANDLE JOB ACTIONS (Delete, Close, etc.)
// ============================================
$action_message = '';

// Delete job
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $job_id = $_GET['delete'];
    
    // Verify job belongs to this employer
    $stmt = $pdo->prepare("SELECT id FROM jobs WHERE id = ? AND employer_id = ?");
    $stmt->execute([$job_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
        $stmt->execute([$job_id]);
        $action_message = '<div class="alert alert-success">Job deleted successfully!</div>';
    } else {
        $action_message = '<div class="alert alert-error">You do not have permission to delete this job.</div>';
    }
}

// Close job (change status to closed)
if (isset($_GET['close']) && is_numeric($_GET['close'])) {
    $job_id = $_GET['close'];
    
    $stmt = $pdo->prepare("UPDATE jobs SET status = 'completed' WHERE id = ? AND employer_id = ?");
    $stmt->execute([$job_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        $action_message = '<div class="alert alert-success">Job marked as completed!</div>';
    } else {
        $action_message = '<div class="alert alert-error">Error closing job.</div>';
    }
}

// Reopen job
if (isset($_GET['reopen']) && is_numeric($_GET['reopen'])) {
    $job_id = $_GET['reopen'];
    
    $stmt = $pdo->prepare("UPDATE jobs SET status = 'open' WHERE id = ? AND employer_id = ?");
    $stmt->execute([$job_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        $action_message = '<div class="alert alert-success">Job reopened successfully!</div>';
    } else {
        $action_message = '<div class="alert alert-error">Error reopening job.</div>';
    }
}

// ============================================
// GET FILTER PARAMETERS
// ============================================
$status_filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

// ============================================
// GET JOBS WITH FILTERS
// ============================================
$sql = "SELECT * FROM jobs WHERE employer_id = ?";
$params = [$user_id];

if ($status_filter !== 'all') {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll();

// ============================================
// GET JOB STATS
// ============================================
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM jobs WHERE employer_id = ?");
$stmt->execute([$user_id]);
$total_jobs = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM jobs WHERE employer_id = ? AND status = 'open'");
$stmt->execute([$user_id]);
$open_jobs = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM jobs WHERE employer_id = ? AND status = 'in_progress'");
$stmt->execute([$user_id]);
$in_progress = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM jobs WHERE employer_id = ? AND status = 'completed'");
$stmt->execute([$user_id]);
$completed_jobs = $stmt->fetch()['total'];

// Set page title
$page_title = 'My Jobs - SkillSeek';

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
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="post_job.php"><i class="fas fa-plus-circle"></i> Post Job</a></li>
                <li class="active"><a href="my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a></li>
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
            <div class="header-left">
                <h1>My Jobs</h1>
                <p>Manage all your job postings</p>
            </div>
            <div class="header-right">
                <a href="post_job.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Post New Job
                </a>
            </div>
        </div>
        
        <!-- Action Messages -->
        <?php echo $action_message; ?>
        
        <!-- Stats Summary -->
        <div class="stats-summary">
            <div class="stat-item">
                <span class="stat-number"><?php echo $total_jobs; ?></span>
                <span class="stat-label">Total Jobs</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" style="color: #1E40AF;"><?php echo $open_jobs; ?></span>
                <span class="stat-label">Open</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" style="color: #92400E;"><?php echo $in_progress; ?></span>
                <span class="stat-label">In Progress</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" style="color: #065F46;"><?php echo $completed_jobs; ?></span>
                <span class="stat-label">Completed</span>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filter-bar">
            <div class="filter-tabs">
                <a href="?status=all" class="filter-tab <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                    All (<span><?php echo $total_jobs; ?></span>)
                </a>
                <a href="?status=open" class="filter-tab <?php echo $status_filter === 'open' ? 'active' : ''; ?>">
                    Open (<span><?php echo $open_jobs; ?></span>)
                </a>
                <a href="?status=in_progress" class="filter-tab <?php echo $status_filter === 'in_progress' ? 'active' : ''; ?>">
                    In Progress (<span><?php echo $in_progress; ?></span>)
                </a>
                <a href="?status=completed" class="filter-tab <?php echo $status_filter === 'completed' ? 'active' : ''; ?>">
                    Completed (<span><?php echo $completed_jobs; ?></span>)
                </a>
            </div>
            
            <div class="filter-search">
                <form method="GET">
                    <input type="hidden" name="status" value="<?php echo $status_filter; ?>">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search jobs..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-secondary btn-sm">Search</button>
                        <?php if (!empty($search)): ?>
                            <a href="?status=<?php echo $status_filter; ?>" class="btn btn-secondary btn-sm">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Job List -->
        <?php if (empty($jobs)): ?>
            <div class="empty-state">
                <i class="fas fa-briefcase"></i>
                <h3>No jobs found</h3>
                <p>
                    <?php if (!empty($search)): ?>
                        No jobs match your search criteria.
                    <?php elseif ($status_filter !== 'all'): ?>
                        You have no <?php echo $status_filter; ?> jobs.
                    <?php else: ?>
                        You haven't posted any jobs yet.
                    <?php endif; ?>
                </p>
                <a href="post_job.php" class="btn btn-primary">Post Your First Job</a>
            </div>
        <?php else: ?>
            <div class="job-list-full">
                <?php foreach ($jobs as $job): ?>
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
                            <p><?php echo htmlspecialchars(substr($job['description'], 0, 200)); ?>...</p>
                        </div>
                        
                        <div class="job-card-footer">
                            <div class="job-meta">
                                <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($job['category'] ?? 'General'); ?></span>
                                <span><i class="fas fa-money-bill"></i> KSh <?php echo number_format($job['budget_min'] ?? 0, 2); ?>
                                    <?php if (!empty($job['budget_max']) && $job['budget_max'] > 0): ?>
                                        - KSh <?php echo number_format($job['budget_max'], 2); ?>
                                    <?php endif; ?>
                                </span>
                                <span><i class="fas fa-map-marker-alt"></i> 
                                    <?php echo $job['is_remote'] ? 'Remote' : htmlspecialchars($job['location'] ?? 'Not specified'); ?>
                                </span>
                                <?php if ($job['expires_at']): ?>
                                    <span><i class="fas fa-clock"></i> Expires: <?php echo date('M d, Y', strtotime($job['expires_at'])); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="job-actions">
                                <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="applications.php?job_id=<?php echo $job['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-users"></i> Applications
                                </a>
                                <?php if ($job['status'] === 'open'): ?>
                                    <a href="?close=<?php echo $job['id']; ?>" class="btn btn-success btn-sm" 
                                       onclick="return confirm('Mark this job as completed?')">
                                        <i class="fas fa-check"></i> Complete
                                    </a>
                                <?php elseif ($job['status'] === 'completed'): ?>
                                    <a href="?reopen=<?php echo $job['id']; ?>" class="btn btn-warning btn-sm"
                                       onclick="return confirm('Reopen this job?')">
                                        <i class="fas fa-undo"></i> Reopen
                                    </a>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $job['id']; ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this job? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i> Delete
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
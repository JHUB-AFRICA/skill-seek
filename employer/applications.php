<?php
// ============================================
// SkillSeek - Employer Applications
// File: employer/applications.php
// Description: View and manage all applications received
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
// HANDLE APPLICATION ACTIONS
// ============================================
$action_message = '';

// Update application status (accept/reject)
if (isset($_GET['action']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $application_id = $_GET['id'];
    $action = $_GET['action'];
    
    // Verify application belongs to employer's job
    $stmt = $pdo->prepare("
        SELECT a.*, j.employer_id 
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        WHERE a.id = ? AND j.employer_id = ?
    ");
    $stmt->execute([$application_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        $valid_actions = ['accepted', 'rejected', 'shortlisted', 'reviewed'];
        if (in_array($action, $valid_actions)) {
            $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
            $stmt->execute([$action, $application_id]);
            $action_message = '<div class="alert alert-success">Application ' . ucfirst($action) . ' successfully!</div>';
            
            // If accepted, also update job status if needed
            if ($action === 'accepted') {
                // Get job_id from application
                $stmt = $pdo->prepare("SELECT job_id FROM applications WHERE id = ?");
                $stmt->execute([$application_id]);
                $job = $stmt->fetch();
                
                // Update job status to in_progress
                $stmt = $pdo->prepare("UPDATE jobs SET status = 'in_progress' WHERE id = ?");
                $stmt->execute([$job['job_id']]);
            }
        } else {
            $action_message = '<div class="alert alert-error">Invalid action.</div>';
        }
    } else {
        $action_message = '<div class="alert alert-error">You do not have permission to modify this application.</div>';
    }
}

// ============================================
// GET FILTER PARAMETERS
// ============================================
$status_filter = $_GET['status'] ?? 'all';
$job_filter = isset($_GET['job_id']) && is_numeric($_GET['job_id']) ? $_GET['job_id'] : 'all';
$search = $_GET['search'] ?? '';

// ============================================
// GET EMPLOYER'S JOBS FOR FILTER DROPDOWN
// ============================================
$stmt = $pdo->prepare("SELECT id, title FROM jobs WHERE employer_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$employer_jobs = $stmt->fetchAll();

// ============================================
// GET APPLICATIONS WITH FILTERS
// ============================================
$sql = "
    SELECT 
        a.*,
        j.title as job_title,
        u.full_name as student_name,
        u.email as student_email,
        u.phone as student_phone,
        sp.skills,
        sp.rating
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN users u ON a.student_id = u.id
    LEFT JOIN student_profiles sp ON u.id = sp.user_id
    WHERE j.employer_id = ?
";

$params = [$user_id];

if ($status_filter !== 'all') {
    $sql .= " AND a.status = ?";
    $params[] = $status_filter;
}

if ($job_filter !== 'all') {
    $sql .= " AND a.job_id = ?";
    $params[] = $job_filter;
}

if (!empty($search)) {
    $sql .= " AND (u.full_name LIKE ? OR j.title LIKE ?)";
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
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    WHERE j.employer_id = ?
");
$stmt->execute([$user_id]);
$total_apps = $stmt->fetch()['total'];

$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    WHERE j.employer_id = ? AND a.status = 'pending'
");
$stmt->execute([$user_id]);
$pending_apps = $stmt->fetch()['total'];

$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    WHERE j.employer_id = ? AND a.status = 'reviewed'
");
$stmt->execute([$user_id]);
$reviewed_apps = $stmt->fetch()['total'];

$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    WHERE j.employer_id = ? AND a.status = 'shortlisted'
");
$stmt->execute([$user_id]);
$shortlisted_apps = $stmt->fetch()['total'];

$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    WHERE j.employer_id = ? AND a.status = 'accepted'
");
$stmt->execute([$user_id]);
$accepted_apps = $stmt->fetch()['total'];

$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    WHERE j.employer_id = ? AND a.status = 'rejected'
");
$stmt->execute([$user_id]);
$rejected_apps = $stmt->fetch()['total'];

// Set page title
$page_title = 'Applications - SkillSeek';

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
                <li><a href="my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a></li>
                <li class="active"><a href="applications.php"><i class="fas fa-users"></i> Applications</a></li>
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
                <h1>Applications</h1>
                <p>Review and manage all student applications</p>
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
                <a href="?status=all&job_id=<?php echo $job_filter; ?>" 
                   class="filter-tab <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                    All (<span><?php echo $total_apps; ?></span>)
                </a>
                <a href="?status=pending&job_id=<?php echo $job_filter; ?>" 
                   class="filter-tab <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">
                    Pending (<span><?php echo $pending_apps; ?></span>)
                </a>
                <a href="?status=reviewed&job_id=<?php echo $job_filter; ?>" 
                   class="filter-tab <?php echo $status_filter === 'reviewed' ? 'active' : ''; ?>">
                    Reviewed (<span><?php echo $reviewed_apps; ?></span>)
                </a>
                <a href="?status=shortlisted&job_id=<?php echo $job_filter; ?>" 
                   class="filter-tab <?php echo $status_filter === 'shortlisted' ? 'active' : ''; ?>">
                    Shortlisted (<span><?php echo $shortlisted_apps; ?></span>)
                </a>
                <a href="?status=accepted&job_id=<?php echo $job_filter; ?>" 
                   class="filter-tab <?php echo $status_filter === 'accepted' ? 'active' : ''; ?>">
                    Accepted (<span><?php echo $accepted_apps; ?></span>)
                </a>
                <a href="?status=rejected&job_id=<?php echo $job_filter; ?>" 
                   class="filter-tab <?php echo $status_filter === 'rejected' ? 'active' : ''; ?>">
                    Rejected (<span><?php echo $rejected_apps; ?></span>)
                </a>
            </div>
            
            <div class="filter-search">
                <form method="GET">
                    <input type="hidden" name="status" value="<?php echo $status_filter; ?>">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <select name="job_id" class="filter-select">
                            <option value="all">All Jobs</option>
                            <?php foreach ($employer_jobs as $job): ?>
                                <option value="<?php echo $job['id']; ?>" 
                                    <?php echo ($job_filter == $job['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($job['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="search" placeholder="Search student or job..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                        <a href="?status=<?php echo $status_filter; ?>" class="btn btn-secondary btn-sm">Clear</a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Applications List -->
        <?php if (empty($applications)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>No applications found</h3>
                <p>
                    <?php if (!empty($search)): ?>
                        No applications match your search criteria.
                    <?php elseif ($status_filter !== 'all'): ?>
                        You have no <?php echo $status_filter; ?> applications.
                    <?php else: ?>
                        You haven't received any applications yet.
                    <?php endif; ?>
                </p>
            </div>
        <?php else: ?>
            <div class="application-list-full">
                <?php foreach ($applications as $app): ?>
                    <div class="application-card">
                        <div class="application-header">
                            <div class="applicant-info">
                                <div class="applicant-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div>
                                    <h4><?php echo htmlspecialchars($app['student_name']); ?></h4>
                                    <div class="applicant-details">
                                        <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($app['student_email']); ?></span>
                                        <?php if ($app['student_phone']): ?>
                                            <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($app['student_phone']); ?></span>
                                        <?php endif; ?>
                                        <?php if ($app['rating'] > 0): ?>
                                            <span><i class="fas fa-star" style="color: #F59E0B;"></i> <?php echo number_format($app['rating'], 1); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="application-meta">
                                <span class="status-badge <?php echo $app['status']; ?>">
                                    <?php echo ucfirst($app['status']); ?>
                                </span>
                                <span class="applied-date">
                                    <i class="fas fa-clock"></i> <?php echo timeAgo($app['applied_at']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="application-body">
                            <div class="job-info">
                                <strong><i class="fas fa-briefcase"></i> Applied for:</strong>
                                <a href="../job_details.php?id=<?php echo $app['job_id']; ?>">
                                    <?php echo htmlspecialchars($app['job_title']); ?>
                                </a>
                            </div>
                            
                            <?php if ($app['cover_letter']): ?>
                                <div class="cover-letter">
                                    <strong><i class="fas fa-file-alt"></i> Cover Letter:</strong>
                                    <p><?php echo nl2br(htmlspecialchars($app['cover_letter'])); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($app['proposed_rate'] > 0): ?>
                                <div class="proposed-rate">
                                    <strong><i class="fas fa-money-bill"></i> Proposed Rate:</strong>
                                    KSh <?php echo number_format($app['proposed_rate'], 2); ?>
                                    <?php if ($app['estimated_days'] > 0): ?>
                                        <span>| Estimated: <?php echo $app['estimated_days']; ?> days</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($app['skills']): ?>
                                <div class="applicant-skills">
                                    <strong><i class="fas fa-tags"></i> Skills:</strong>
                                    <?php 
                                        $skills = explode(',', $app['skills']);
                                        foreach ($skills as $skill): 
                                    ?>
                                        <span class="skill-tag"><?php echo htmlspecialchars(trim($skill)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="application-footer">
                            <?php if ($app['status'] === 'pending'): ?>
                                <div class="action-buttons">
                                    <a href="?action=reviewed&id=<?php echo $app['id']; ?>" 
                                       class="btn btn-secondary btn-sm">
                                        <i class="fas fa-check"></i> Mark Reviewed
                                    </a>
                                    <a href="?action=shortlisted&id=<?php echo $app['id']; ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-star"></i> Shortlist
                                    </a>
                                    <a href="?action=accepted&id=<?php echo $app['id']; ?>" 
                                       class="btn btn-success btn-sm"
                                       onclick="return confirm('Accept this application? This will start the job.')">
                                        <i class="fas fa-check-circle"></i> Accept
                                    </a>
                                    <a href="?action=rejected&id=<?php echo $app['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Reject this application?')">
                                        <i class="fas fa-times"></i> Reject
                                    </a>
                                </div>
                            <?php elseif ($app['status'] === 'reviewed'): ?>
                                <div class="action-buttons">
                                    <a href="?action=shortlisted&id=<?php echo $app['id']; ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-star"></i> Shortlist
                                    </a>
                                    <a href="?action=accepted&id=<?php echo $app['id']; ?>" 
                                       class="btn btn-success btn-sm"
                                       onclick="return confirm('Accept this application?')">
                                        <i class="fas fa-check-circle"></i> Accept
                                    </a>
                                    <a href="?action=rejected&id=<?php echo $app['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Reject this application?')">
                                        <i class="fas fa-times"></i> Reject
                                    </a>
                                </div>
                            <?php elseif ($app['status'] === 'shortlisted'): ?>
                                <div class="action-buttons">
                                    <a href="?action=accepted&id=<?php echo $app['id']; ?>" 
                                       class="btn btn-success btn-sm"
                                       onclick="return confirm('Accept this application?')">
                                        <i class="fas fa-check-circle"></i> Accept
                                    </a>
                                    <a href="?action=rejected&id=<?php echo $app['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Reject this application?')">
                                        <i class="fas fa-times"></i> Reject
                                    </a>
                                </div>
                            <?php else: ?>
                                <span class="status-message">Application <?php echo $app['status']; ?></span>
                                <?php if ($app['status'] === 'accepted'): ?>
                                    <a href="../api/chat.php?user=<?php echo $app['student_id']; ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-comment"></i> Message Student
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    </main>
</div>

<?php include '../includes/footer.php'; ?>
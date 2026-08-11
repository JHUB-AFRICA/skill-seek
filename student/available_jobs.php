<?php
// ============================================
// SkillSeek - Student Available Jobs
// File: student/available_jobs.php
// Description: Browse and search available jobs
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
// HANDLE SAVE/UNSAVE JOB
// ============================================
$action_message = '';
// Surface flash error from apply.php redirects
if (isset($_SESSION['error'])) {
    $action_message = '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}
if (isset($_GET['action']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $job_id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'save') {
        // Check if already saved
        $stmt = $pdo->prepare("SELECT * FROM saved_jobs WHERE student_id = ? AND job_id = ?");
        $stmt->execute([$user_id, $job_id]);
        if ($stmt->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT INTO saved_jobs (student_id, job_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $job_id]);
            $action_message = '<div class="alert alert-success">Job saved successfully!</div>';
        } else {
            $action_message = '<div class="alert alert-info">Job already saved.</div>';
        }
    } elseif ($action === 'remove') {
        $stmt = $pdo->prepare("DELETE FROM saved_jobs WHERE student_id = ? AND job_id = ?");
        $stmt->execute([$user_id, $job_id]);
        $action_message = '<div class="alert alert-success">Job removed from saved.</div>';
    }
}

// ============================================
// GET FILTER PARAMETERS
// ============================================
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$min_budget = $_GET['min_budget'] ?? '';
$max_budget = $_GET['max_budget'] ?? '';
$is_remote = isset($_GET['is_remote']) ? 1 : 0;
$sort_by = $_GET['sort'] ?? 'newest';

// ============================================
// GET CATEGORIES FOR FILTER
// ============================================
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

// ============================================
// GET JOBS WITH FILTERS
// ============================================
$sql = "
    SELECT 
        j.*,
        u.full_name as employer_name,
        u.phone as employer_phone,
        c.name as category_name
    FROM jobs j
    JOIN users u ON j.employer_id = u.id
    LEFT JOIN categories c ON j.category_id = c.id
    WHERE j.status = 'open'
";

$params = [];

if (!empty($search)) {
    $sql .= " AND (j.title LIKE ? OR j.description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
}

if (!empty($category)) {
    $sql .= " AND c.name = ?";
    $params[] = $category;
}

if (!empty($min_budget) && is_numeric($min_budget)) {
    $sql .= " AND j.budget_min >= ?";
    $params[] = $min_budget;
}

if (!empty($max_budget) && is_numeric($max_budget)) {
    $sql .= " AND j.budget_max <= ?";
    $params[] = $max_budget;
}

if ($is_remote) {
    $sql .= " AND j.is_remote = 1";
}

// Sorting
switch ($sort_by) {
    case 'newest':
        $sql .= " ORDER BY j.created_at DESC";
        break;
    case 'oldest':
        $sql .= " ORDER BY j.created_at ASC";
        break;
    case 'budget_high':
        $sql .= " ORDER BY j.budget_max DESC";
        break;
    case 'budget_low':
        $sql .= " ORDER BY j.budget_min ASC";
        break;
    default:
        $sql .= " ORDER BY j.created_at DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll();

// ============================================
// CHECK SAVED JOBS
// ============================================
$saved_job_ids = [];
$stmt = $pdo->prepare("SELECT job_id FROM saved_jobs WHERE student_id = ?");
$stmt->execute([$user_id]);
$saved_jobs = $stmt->fetchAll();
foreach ($saved_jobs as $saved) {
    $saved_job_ids[] = $saved['job_id'];
}

// ============================================
// CHECK IF STUDENT HAS ALREADY APPLIED
// ============================================
$applied_job_ids = [];
$stmt = $pdo->prepare("SELECT job_id FROM applications WHERE student_id = ?");
$stmt->execute([$user_id]);
$applied_jobs = $stmt->fetchAll();
foreach ($applied_jobs as $applied) {
    $applied_job_ids[] = $applied['job_id'];
}

// Set page title
$page_title = 'Available Jobs - SkillSeek';

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
                <li class="active"><a href="available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a></li>
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
            <div class="header-left">
                <h1>Available Jobs</h1>
                <p>Find the perfect opportunity for your skills</p>
            </div>
            <div class="header-right">
                <span class="result-count"><?php echo count($jobs); ?> jobs found</span>
            </div>
        </div>
        
        <!-- Action Messages -->
        <?php echo $action_message; ?>
        
        <!-- Filters -->
        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="search"><i class="fas fa-search"></i></label>
                        <input type="text" id="search" name="search" 
                               placeholder="Search jobs by title or description..."
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="category">Category</label>
                        <select id="category" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['name']); ?>" 
                                    <?php echo ($category == $cat['name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="min_budget">Min Budget</label>
                        <input type="number" id="min_budget" name="min_budget" 
                               placeholder="5000"
                               value="<?php echo htmlspecialchars($min_budget); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="max_budget">Max Budget</label>
                        <input type="number" id="max_budget" name="max_budget" 
                               placeholder="100000"
                               value="<?php echo htmlspecialchars($max_budget); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="sort">Sort By</label>
                        <select id="sort" name="sort">
                            <option value="newest" <?php echo $sort_by === 'newest' ? 'selected' : ''; ?>>Newest</option>
                            <option value="oldest" <?php echo $sort_by === 'oldest' ? 'selected' : ''; ?>>Oldest</option>
                            <option value="budget_high" <?php echo $sort_by === 'budget_high' ? 'selected' : ''; ?>>Budget: High to Low</option>
                            <option value="budget_low" <?php echo $sort_by === 'budget_low' ? 'selected' : ''; ?>>Budget: Low to High</option>
                        </select>
                    </div>
                    
                    <div class="filter-group checkbox-group">
                        <input type="checkbox" id="is_remote" name="is_remote" value="1"
                               <?php echo $is_remote ? 'checked' : ''; ?>>
                        <label for="is_remote">Remote Only</label>
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="available_jobs.php" class="btn btn-secondary">Clear All</a>
                </div>
            </form>
        </div>
        
        <!-- Jobs List -->
        <?php if (empty($jobs)): ?>
            <div class="empty-state">
                <i class="fas fa-briefcase"></i>
                <h3>No jobs found</h3>
                <p>
                    <?php if (!empty($search) || !empty($category) || !empty($min_budget) || !empty($max_budget)): ?>
                        No jobs match your search criteria. Try adjusting your filters.
                    <?php else: ?>
                        There are currently no open jobs available. Check back later!
                    <?php endif; ?>
                </p>
                <a href="available_jobs.php" class="btn btn-secondary">Clear Filters</a>
            </div>
        <?php else: ?>
            <div class="job-list-full">
                <?php foreach ($jobs as $job): ?>
                    <div class="job-card-full">
                        <div class="job-card-header">
                            <div class="job-title-section">
                                <h3>
                                    <a href="../job_details.php?id=<?php echo $job['id']; ?>">
                                        <?php echo htmlspecialchars($job['title']); ?>
                                    </a>
                                </h3>
                                <span class="status-badge open">Open</span>
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
                                <?php if ($job['expires_at']): ?>
                                    <span><i class="fas fa-clock"></i> Expires: <?php echo date('M d, Y', strtotime($job['expires_at'])); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="job-actions">
                                <?php if (in_array($job['id'], $applied_job_ids)): ?>
                                    <span class="btn btn-success btn-sm disabled">
                                        <i class="fas fa-check"></i> Applied
                                    </span>
                                <?php else: ?>
                                    <a href="apply.php?id=<?php echo $job['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-paper-plane"></i> Apply Now
                                    </a>
                                <?php endif; ?>
                                
                                <a href="../job_details.php?id=<?php echo $job['id']; ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                
                                <?php if (in_array($job['id'], $saved_job_ids)): ?>
                                    <a href="?action=remove&id=<?php echo $job['id']; ?>" 
                                       class="btn btn-warning btn-sm"
                                       onclick="return confirm('Remove this job from saved?')">
                                        <i class="fas fa-bookmark"></i> Saved
                                    </a>
                                <?php else: ?>
                                    <a href="?action=save&id=<?php echo $job['id']; ?>" 
                                       class="btn btn-secondary btn-sm">
                                        <i class="fas fa-bookmark"></i> Save
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    </main>
</div>

<?php include '../includes/footer.php'; ?>
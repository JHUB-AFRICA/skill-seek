<?php
// ============================================
// SkillSeek - Admin Dashboard
// ============================================

require_once '../config/database.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

// Get Statistics
$stmt = $pdo->query("SELECT COUNT(*) as total, 
                     SUM(CASE WHEN role = 'employer' THEN 1 ELSE 0 END) as employers,
                     SUM(CASE WHEN role = 'student' THEN 1 ELSE 0 END) as students
                     FROM users");
$user_stats = $stmt->fetch();

$stmt = $pdo->query("SELECT COUNT(*) as total,
                     SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open,
                     SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                     SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                     FROM jobs");
$job_stats = $stmt->fetch();

$stmt = $pdo->query("SELECT COUNT(*) as total FROM applications");
$total_applications = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total, SUM(amount) as total_amount 
                     FROM payments WHERE status = 'completed'");
$payment_stats = $stmt->fetch();

$page_title = 'Admin Dashboard - SkillSeek';
include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<div class="admin-main">
    <div class="admin-header">
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo $_SESSION['full_name']; ?></p>
    </div>

    <!-- Stats Cards -->
    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon blue">👥</div>
            <div class="admin-stat-info">
                <h3><?php echo $user_stats['total']; ?></h3>
                <p>Total Users</p>
                <small><?php echo $user_stats['employers']; ?> Employers | <?php echo $user_stats['students']; ?> Students</small>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon green">💼</div>
            <div class="admin-stat-info">
                <h3><?php echo $job_stats['total']; ?></h3>
                <p>Total Jobs</p>
                <small><?php echo $job_stats['open']; ?> Open | <?php echo $job_stats['in_progress']; ?> In Progress</small>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon orange">📄</div>
            <div class="admin-stat-info">
                <h3><?php echo $total_applications; ?></h3>
                <p>Total Applications</p>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon purple">💰</div>
            <div class="admin-stat-info">
                <h3>KSh <?php echo number_format($payment_stats['total_amount'] ?? 0, 2); ?></h3>
                <p>Total Payments</p>
                <small><?php echo $payment_stats['total'] ?? 0; ?> transactions</small>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="admin-grid">
        <div class="admin-section">
            <h3>Recent Users</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
                    $recent_users = $stmt->fetchAll();
                    foreach ($recent_users as $user):
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><span class="role-badge <?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-section">
            <h3>Recent Jobs</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Employer</th>
                        <th>Status</th>
                        <th>Posted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT j.*, u.full_name as employer_name 
                                         FROM jobs j 
                                         JOIN users u ON j.employer_id = u.id 
                                         ORDER BY j.created_at DESC LIMIT 5");
                    $recent_jobs = $stmt->fetchAll();
                    foreach ($recent_jobs as $job):
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                        <td><?php echo htmlspecialchars($job['employer_name']); ?></td>
                        <td><span class="status-badge <?php echo $job['status']; ?>"><?php echo ucfirst($job['status']); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($job['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
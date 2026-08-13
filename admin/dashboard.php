<?php
require_once 'includes/functions.php';
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Get real stats from database
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$total_users = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM jobs");
$total_jobs = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM applications");
$total_applications = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total, SUM(amount) as total_amount FROM payments WHERE status = 'completed'");
$payments = $stmt->fetch();
$total_payments = $payments['total'] ?? 0;
$total_amount = $payments['total_amount'] ?? 0;

// Get recent users
$recent_users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Get recent jobs
$recent_jobs = $pdo->query("
    SELECT j.*, u.full_name as employer_name 
    FROM jobs j 
    JOIN users u ON j.employer_id = u.id 
    ORDER BY j.created_at DESC LIMIT 5
")->fetchAll();

$page_title = 'Dashboard';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<main class="admin-main">
    <div class="admin-page-header">
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo $_SESSION['admin_full_name'] ?? 'Admin'; ?></p>
    </div>

    <!-- Stats Cards -->
    <div class="admin-stats">
        <div class="admin-stat-card">
            <div class="admin-stat-icon blue"><i class="fas fa-users"></i></div>
            <div class="admin-stat-info">
                <h3><?php echo $total_users; ?></h3>
                <p>Total Users</p>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon green"><i class="fas fa-briefcase"></i></div>
            <div class="admin-stat-info">
                <h3><?php echo $total_jobs; ?></h3>
                <p>Total Jobs</p>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon orange"><i class="fas fa-file-alt"></i></div>
            <div class="admin-stat-info">
                <h3><?php echo $total_applications; ?></h3>
                <p>Applications</p>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon purple"><i class="fas fa-credit-card"></i></div>
            <div class="admin-stat-info">
                <h3>KSh <?php echo number_format($total_amount, 2); ?></h3>
                <p>Total Payments</p>
                <small><?php echo $total_payments; ?> transactions</small>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
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
                <?php foreach ($recent_users as $user): ?>
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

    <!-- Recent Jobs -->
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
                <?php foreach ($recent_jobs as $job): ?>
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
</main>
<?php include 'includes/footer.php'; ?>
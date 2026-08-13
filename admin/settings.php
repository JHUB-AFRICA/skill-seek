<?php
require_once 'includes/functions.php';
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Get platform stats
$users_count = $pdo->query("SELECT COUNT(*) as total FROM users")->fetch()['total'];
$jobs_count = $pdo->query("SELECT COUNT(*) as total FROM jobs")->fetch()['total'];
$apps_count = $pdo->query("SELECT COUNT(*) as total FROM applications")->fetch()['total'];
$payments_count = $pdo->query("SELECT COUNT(*) as total FROM payments WHERE status = 'completed'")->fetch()['total'];

$page_title = 'Settings';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<main class="admin-main">
    <div class="admin-page-header">
        <h1>Settings</h1>
        <p>Platform settings</p>
    </div>

    <div class="admin-section">
        <h3>Platform Overview</h3>
        <div class="settings-grid">
            <div class="settings-item">
                <span class="settings-label">Total Users</span>
                <span class="settings-value"><?php echo $users_count; ?></span>
            </div>
            <div class="settings-item">
                <span class="settings-label">Total Jobs</span>
                <span class="settings-value"><?php echo $jobs_count; ?></span>
            </div>
            <div class="settings-item">
                <span class="settings-label">Total Applications</span>
                <span class="settings-value"><?php echo $apps_count; ?></span>
            </div>
            <div class="settings-item">
                <span class="settings-label">Total Payments</span>
                <span class="settings-value"><?php echo $payments_count; ?></span>
            </div>
        </div>
    </div>

    <div class="admin-section">
        <h3>Settings Features Coming Soon</h3>
        <ul>
            <li>Site name and branding</li>
            <li>Email configuration</li>
            <li>Payment settings</li>
            <li>User permissions</li>
        </ul>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
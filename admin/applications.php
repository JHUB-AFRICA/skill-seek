<?php
require_once 'includes/functions.php';
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Get all applications
$apps = $pdo->query("
    SELECT a.*, j.title as job_title, u.full_name as student_name 
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    JOIN users u ON a.student_id = u.id 
    ORDER BY a.applied_at DESC
")->fetchAll();

$page_title = 'Applications';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<main class="admin-main">
    <div class="admin-page-header">
        <h1>Applications</h1>
        <p>Manage all applications (<?php echo count($apps); ?> total)</p>
    </div>
    <div class="admin-section">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Job</th>
                    <th>Student</th>
                    <th>Rate</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Applied</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($apps as $app): ?>
                <tr>
                    <td><?php echo $app['id']; ?></td>
                    <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                    <td><?php echo htmlspecialchars($app['student_name']); ?></td>
                    <td>KSh <?php echo number_format($app['proposed_rate'] ?? 0, 2); ?></td>
                    <td><?php echo $app['estimated_days'] ?? 'N/A'; ?></td>
                    <td><span class="status-badge <?php echo $app['status']; ?>"><?php echo ucfirst($app['status']); ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
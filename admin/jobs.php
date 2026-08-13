<?php
require_once 'includes/functions.php';
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Get all jobs with employer names
$jobs = $pdo->query("
    SELECT j.*, u.full_name as employer_name 
    FROM jobs j 
    JOIN users u ON j.employer_id = u.id 
    ORDER BY j.created_at DESC
")->fetchAll();

$page_title = 'Jobs';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<main class="admin-main">
    <div class="admin-page-header">
        <h1>Jobs</h1>
        <p>Manage all job listings (<?php echo count($jobs); ?> total)</p>
    </div>
    <div class="admin-section">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Employer</th>
                    <th>Budget</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Posted</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobs as $job): ?>
                <tr>
                    <td><?php echo $job['id']; ?></td>
                    <td><?php echo htmlspecialchars($job['title']); ?></td>
                    <td><?php echo htmlspecialchars($job['employer_name']); ?></td>
                    <td>KSh <?php echo number_format($job['budget_min'] ?? 0, 2); ?></td>
                    <td><?php echo htmlspecialchars($job['category'] ?? 'General'); ?></td>
                    <td><span class="status-badge <?php echo $job['status']; ?>"><?php echo ucfirst($job['status']); ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($job['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
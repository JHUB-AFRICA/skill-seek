<?php
// ============================================
// SkillSeek - Admin Jobs Management
// ============================================

require_once '../config/database.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = 'Job deleted successfully.';
    } elseif ($action === 'close') {
        $stmt = $pdo->prepare("UPDATE jobs SET status = 'completed' WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = 'Job closed successfully.';
    }
    header('Location: jobs.php');
    exit();
}

// Get all jobs
$stmt = $pdo->query("SELECT j.*, u.full_name as employer_name 
                     FROM jobs j 
                     JOIN users u ON j.employer_id = u.id 
                     ORDER BY j.created_at DESC");
$jobs = $stmt->fetchAll();

$page_title = 'Jobs - Admin';
include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<div class="admin-main">
    <div class="admin-header">
        <h1>Jobs</h1>
        <p>Manage all job listings</p>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <div class="admin-section">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Employer</th>
                    <th>Budget</th>
                    <th>Status</th>
                    <th>Posted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($jobs)): ?>
                    <tr><td colspan="7" style="text-align: center; padding: 20px; color: #94A3B8;">No jobs found</td></tr>
                <?php else: ?>
                    <?php foreach ($jobs as $job): ?>
                    <tr>
                        <td><?php echo $job['id']; ?></td>
                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                        <td><?php echo htmlspecialchars($job['employer_name']); ?></td>
                        <td>KSh <?php echo number_format($job['budget_min'] ?? 0, 2); ?></td>
                        <td>
                            <span class="status-badge <?php echo $job['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $job['status'])); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($job['created_at'])); ?></td>
                        <td>
                            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                <?php if ($job['status'] === 'open'): ?>
                                    <a href="?action=close&id=<?php echo $job['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Close this job?')">Close</a>
                                <?php endif; ?>
                                <a href="?action=delete&id=<?php echo $job['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this job?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
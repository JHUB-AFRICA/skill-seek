<?php
// ============================================
// SkillSeek - Admin Applications Management
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
        $stmt = $pdo->prepare("DELETE FROM applications WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = 'Application deleted successfully.';
    }
    header('Location: applications.php');
    exit();
}

// Get all applications
$stmt = $pdo->query("SELECT a.*, j.title as job_title, 
                     u.full_name as student_name, e.full_name as employer_name
                     FROM applications a 
                     JOIN jobs j ON a.job_id = j.id 
                     JOIN users u ON a.student_id = u.id 
                     JOIN users e ON j.employer_id = e.id 
                     ORDER BY a.applied_at DESC");
$applications = $stmt->fetchAll();

$page_title = 'Applications - Admin';
include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<div class="admin-main">
    <div class="admin-header">
        <h1>Applications</h1>
        <p>Manage all student applications</p>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <div class="admin-section">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Job</th>
                    <th>Student</th>
                    <th>Employer</th>
                    <th>Rate</th>
                    <th>Status</th>
                    <th>Applied</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($applications)): ?>
                    <tr><td colspan="8" style="text-align: center; padding: 20px; color: #94A3B8;">No applications found</td></tr>
                <?php else: ?>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><?php echo $app['id']; ?></td>
                        <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                        <td><?php echo htmlspecialchars($app['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($app['employer_name']); ?></td>
                        <td>KSh <?php echo number_format($app['proposed_rate'] ?? 0, 2); ?></td>
                        <td>
                            <span class="status-badge <?php echo $app['status']; ?>">
                                <?php echo ucfirst($app['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                        <td>
                            <a href="?action=delete&id=<?php echo $app['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this application?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
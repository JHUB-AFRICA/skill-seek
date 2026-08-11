<?php
// ============================================
// SkillSeek - Admin Payments Management
// ============================================

require_once '../config/database.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

// Get all payments
$stmt = $pdo->query("SELECT p.*, j.title as job_title, 
                     u.full_name as student_name, e.full_name as employer_name
                     FROM payments p 
                     JOIN jobs j ON p.job_id = j.id 
                     JOIN users u ON p.student_id = u.id 
                     JOIN users e ON p.employer_id = e.id 
                     ORDER BY p.created_at DESC");
$payments = $stmt->fetchAll();

// Get stats
$stmt = $pdo->query("SELECT COUNT(*) as total, SUM(amount) as total_amount 
                     FROM payments WHERE status = 'completed'");
$stats = $stmt->fetch();

$page_title = 'Payments - Admin';
include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<div class="admin-main">
    <div class="admin-header">
        <h1>Payments</h1>
        <p>Monitor all payment transactions</p>
    </div>

    <div class="admin-stats-grid" style="margin-bottom: 20px;">
        <div class="admin-stat-card">
            <div class="admin-stat-icon purple">💰</div>
            <div class="admin-stat-info">
                <h3>KSh <?php echo number_format($stats['total_amount'] ?? 0, 2); ?></h3>
                <p>Total Payments</p>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon blue">📊</div>
            <div class="admin-stat-info">
                <h3><?php echo $stats['total'] ?? 0; ?></h3>
                <p>Total Transactions</p>
            </div>
        </div>
    </div>

    <div class="admin-section">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Job</th>
                    <th>Employer</th>
                    <th>Student</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>MPESA Code</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr><td colspan="8" style="text-align: center; padding: 20px; color: #94A3B8;">No payments found</td></tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo $payment['id']; ?></td>
                        <td><?php echo htmlspecialchars($payment['job_title']); ?></td>
                        <td><?php echo htmlspecialchars($payment['employer_name']); ?></td>
                        <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                        <td><strong>KSh <?php echo number_format($payment['amount'], 2); ?></strong></td>
                        <td>
                            <span class="status-badge <?php echo $payment['status']; ?>">
                                <?php echo ucfirst($payment['status']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($payment['mpesa_code'] ?? 'N/A'); ?></td>
                        <td><?php echo date('M d, Y', strtotime($payment['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
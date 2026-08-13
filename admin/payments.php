<?php
require_once 'includes/functions.php';
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Get all payments
$payments = $pdo->query("
    SELECT p.*, j.title as job_title, u.full_name as student_name 
    FROM payments p 
    JOIN jobs j ON p.job_id = j.id 
    JOIN users u ON p.student_id = u.id 
    ORDER BY p.created_at DESC
")->fetchAll();

// Get stats
$stats = $pdo->query("SELECT COUNT(*) as total, SUM(amount) as total_amount FROM payments WHERE status = 'completed'")->fetch();
$total_payments = $stats['total'] ?? 0;
$total_amount = $stats['total_amount'] ?? 0;

$page_title = 'Payments';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<main class="admin-main">
    <div class="admin-page-header">
        <h1>Payments</h1>
        <p>Monitor all payments</p>
    </div>

    <!-- Stats -->
    <div class="admin-stats" style="margin-bottom: 20px;">
        <div class="admin-stat-card">
            <div class="admin-stat-icon purple"><i class="fas fa-credit-card"></i></div>
            <div class="admin-stat-info">
                <h3>KSh <?php echo number_format($total_amount, 2); ?></h3>
                <p>Total Payments</p>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon blue"><i class="fas fa-receipt"></i></div>
            <div class="admin-stat-info">
                <h3><?php echo $total_payments; ?></h3>
                <p>Transactions</p>
            </div>
        </div>
    </div>

    <div class="admin-section">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Job</th>
                    <th>Student</th>
                    <th>Amount</th>
                    <th>MPESA Code</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?php echo $payment['id']; ?></td>
                    <td><?php echo htmlspecialchars($payment['job_title']); ?></td>
                    <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                    <td><strong>KSh <?php echo number_format($payment['amount'], 2); ?></strong></td>
                    <td><?php echo htmlspecialchars($payment['mpesa_code'] ?? 'N/A'); ?></td>
                    <td><span class="status-badge <?php echo $payment['status']; ?>"><?php echo ucfirst($payment['status']); ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($payment['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
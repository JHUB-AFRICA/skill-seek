<?php
// ============================================
// SkillSeek - Payments
// File: employer/payments.php
// Description: View payment history for employer
// ============================================

require_once '../config/database.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

if (getUserRole() !== 'employer') {
    redirect('../student/dashboard.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// ============================================
// GET PAYMENT STATS
// ============================================
$stmt = $pdo->prepare("SELECT COUNT(*) AS total, COALESCE(SUM(amount),0) AS sum FROM payments WHERE employer_id = ?");
$stmt->execute([$user_id]);
$payout = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(*) AS total, COALESCE(SUM(amount),0) AS sum FROM payments WHERE employer_id = ? AND status = 'completed'");
$stmt->execute([$user_id]);
$completed = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM payments WHERE employer_id = ? AND status = 'pending'");
$stmt->execute([$user_id]);
$pending = (int)$stmt->fetch()['total'];

// ============================================
// GET PAYMENTS
// ============================================
$stmt = $pdo->prepare("
    SELECT p.*, j.title AS job_title, u.full_name AS student_name
    FROM payments p
    LEFT JOIN jobs j ON p.job_id = j.id
    LEFT JOIN users u ON p.student_id = u.id
    WHERE p.employer_id = ?
    ORDER BY p.created_at DESC
    LIMIT 100
");
$stmt->execute([$user_id]);
$payments = $stmt->fetchAll();

$page_title = 'Payments - SkillSeek';
include '../includes/header.php';
?>

<!-- ============================================================
     PAGE CONTENT
     ============================================================ -->
<div class="dashboard-container">

    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar"><i class="fas fa-building"></i></div>
            <h3><?php echo htmlspecialchars($user_name); ?></h3>
            <span class="role-badge employer">Employer</span>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="post_job.php"><i class="fas fa-plus-circle"></i> Post Job</a></li>
                <li><a href="my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a></li>
                <li><a href="applications.php"><i class="fas fa-users"></i> Applications</a></li>
                <li><a href="talent.php"><i class="fas fa-user-graduate"></i> Find Talent</a></li>
                <li class="active"><a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="../profile.php"><i class="fas fa-user-edit"></i> Profile</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="dashboard-main">

        <div class="page-header">
            <div class="header-left">
                <h1>Payments</h1>
                <p>Payment history for jobs you've posted</p>
            </div>
        </div>

        <div class="stats-summary">
            <div class="stat-item">
                <span class="stat-number"><?php echo number_format((float)$payout['total'], 0); ?></span>
                <span class="stat-label">All Payments</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" style="color:#065F46;">KSh <?php echo number_format((float)$completed['sum'], 2); ?></span>
                <span class="stat-label">Completed Spend</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" style="color:#92400E;"><?php echo number_format((float)$pending, 0); ?></span>
                <span class="stat-label">Pending</span>
            </div>
        </div>

        <?php if (empty($payments)): ?>
            <div class="empty-state">
                <i class="fas fa-credit-card"></i>
                <h3>No payments yet</h3>
                <p>Payments will appear here once you hire and pay freelancers on a project.</p>
                <a href="post_job.php" class="btn btn-primary">Post a Job</a>
            </div>
        <?php else: ?>
            <div class="job-list-full">
                <?php foreach ($payments as $p): ?>
                    <div class="job-card-full" style="padding:20px 24px;">
                        <div class="job-card-header">
                            <div class="job-title-section">
                                <h3><?php echo htmlspecialchars($p['job_title'] ?? 'Payment'); ?></h3>
                                <span class="status-badge <?php echo $p['status']; ?>">
                                    <?php echo ucfirst($p['status']); ?>
                                </span>
                            </div>
                            <div class="job-date">
                                <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($p['created_at'])); ?>
                            </div>
                        </div>
                        <div class="job-card-body">
                            <p>
                                <strong><i class="fas fa-user"></i> <?php echo htmlspecialchars($p['student_name'] ?? 'Freelancer'); ?></strong><br>
                                <i class="fas fa-money-bill"></i> KSh <?php echo number_format((float)$p['amount'], 2); ?>
                                <span style="margin:0 8px;">|</span> <?php echo ucfirst(str_replace('_', ' ', $p['payment_type'])); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

<?php include '../includes/footer.php'; ?>
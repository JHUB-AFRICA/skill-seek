<?php
// ============================================
// SkillSeek - Linked Employers (Student View)
// ============================================

require_once '../config/database.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

if (getUserRole() !== 'student') {
    redirect('../employer/dashboard.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// ============================================
// GET LINKED EMPLOYERS
// ============================================
$stmt = $pdo->prepare("
    SELECT u.id, u.full_name, u.email, u.phone, 
           ep.company_name,
           esl.created_at as linked_date
    FROM employer_student_links esl
    JOIN users u ON esl.employer_id = u.id
    LEFT JOIN employer_profiles ep ON u.id = ep.user_id
    WHERE esl.student_id = ?
    ORDER BY esl.created_at DESC
");
$stmt->execute([$user_id]);
$linked_employers = $stmt->fetchAll();

$page_title = 'Linked Employers - SkillSeek';
include '../includes/header.php';
?>

<div class="dashboard-container">
    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h3><?php echo htmlspecialchars($user_name); ?></h3>
            <span class="role-badge student">Student</span>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a></li>
                <li><a href="my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                <li><a href="saved_jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a></li>
                <li><a href="profile.php"><i class="fas fa-user-edit"></i> My Profile</a></li>
                <li class="active"><a href="linked_employers.php"><i class="fas fa-link"></i> Linked Employers</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>
    
    <main class="dashboard-main">
        <div class="page-header">
            <h1>🔗 Linked Employers</h1>
            <p>Employers who have connected with you</p>
        </div>

        <?php if (empty($linked_employers)): ?>
            <div class="empty-state">
                <i class="fas fa-link"></i>
                <h3>No linked employers</h3>
                <p>Employers will appear here when they link with you.</p>
            </div>
        <?php else: ?>
            <div class="employer-list">
                <?php foreach ($linked_employers as $employer): ?>
                    <div class="employer-card">
                        <div class="employer-header">
                            <div class="employer-info">
                                <div class="employer-avatar">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <h3><?php echo htmlspecialchars($employer['full_name']); ?></h3>
                                    <div class="employer-company">
                                        <i class="fas fa-building"></i>
                                        <?php echo htmlspecialchars($employer['company_name'] ?? 'Company not specified'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="employer-status">
                                <span class="status-badge linked">🔗 Linked</span>
                            </div>
                        </div>
                        
                        <div class="employer-body">
                            <div class="employer-meta">
                                <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($employer['email']); ?></span>
                                <?php if ($employer['phone']): ?>
                                    <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($employer['phone']); ?></span>
                                <?php endif; ?>
                                <span><i class="fas fa-calendar"></i> Linked: <?php echo date('M d, Y', strtotime($employer['linked_date'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="employer-footer">
                            <div class="employer-actions">
                                <a href="../api/chat.php?user=<?php echo $employer['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-comment"></i> Message
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<style>
.employer-list { display: grid; gap: 16px; }
.employer-card { background: #FFFFFF; border-radius: 12px; padding: 20px 24px; border: 1px solid #E2E8F0; transition: all 0.2s ease; }
.employer-card:hover { border-color: #4F46E5; }
.employer-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #E2E8F0; flex-wrap: wrap; gap: 10px; }
.employer-info { display: flex; align-items: center; gap: 12px; }
.employer-avatar { width: 48px; height: 48px; border-radius: 50%; background: #EEF2FF; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #4F46E5; }
.employer-info h3 { font-size: 16px; font-weight: 700; color: #0F172A; margin: 0; }
.employer-company { font-size: 13px; color: #64748B; }
.employer-status { display: flex; gap: 8px; flex-wrap: wrap; }
.employer-body { margin-bottom: 12px; }
.employer-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 13px; color: #64748B; }
.employer-meta span { display: flex; align-items: center; gap: 4px; }
.employer-footer { padding-top: 12px; border-top: 1px solid #E2E8F0; }
.employer-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.status-badge.linked { background: #EDE9FE; color: #5B21B6; }
@media (max-width: 768px) { .employer-header { flex-direction: column; align-items: flex-start; } .employer-actions { flex-direction: column; width: 100%; } .employer-actions .btn { width: 100%; justify-content: center; } }
</style>

<?php include '../includes/footer.php'; ?>
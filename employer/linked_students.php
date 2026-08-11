<?php
// ============================================
// SkillSeek - Linked Students
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
// HANDLE UNLINK
// ============================================
if (isset($_GET['unlink']) && is_numeric($_GET['unlink'])) {
    $student_id = intval($_GET['unlink']);
    $stmt = $pdo->prepare("DELETE FROM employer_student_links WHERE employer_id = ? AND student_id = ?");
    $stmt->execute([$user_id, $student_id]);
    $_SESSION['message'] = 'Student unlinked successfully.';
    header('Location: linked_students.php');
    exit();
}

// ============================================
// GET LINKED STUDENTS
// ============================================
$stmt = $pdo->prepare("
    SELECT u.id, u.full_name, u.email, u.phone, u.location, 
           sp.skills, sp.rating, sp.total_jobs_completed, sp.is_available,
           esl.created_at as linked_date
    FROM employer_student_links esl
    JOIN users u ON esl.student_id = u.id
    JOIN student_profiles sp ON u.id = sp.user_id
    WHERE esl.employer_id = ?
    ORDER BY esl.created_at DESC
");
$stmt->execute([$user_id]);
$linked_students = $stmt->fetchAll();

$page_title = 'Linked Students - SkillSeek';
include '../includes/header.php';
?>

<div class="dashboard-container">
    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar">
                <i class="fas fa-building"></i>
            </div>
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
                <li class="active"><a href="linked_students.php"><i class="fas fa-link"></i> Linked Students</a></li>
                <li><a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>
    
    <main class="dashboard-main">
        <div class="page-header">
            <h1>🔗 Linked Students</h1>
            <p>Students you have connected with</p>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <?php if (empty($linked_students)): ?>
            <div class="empty-state">
                <i class="fas fa-link"></i>
                <h3>No linked students</h3>
                <p>Go to <a href="talent.php">Find Talent</a> to start linking with students.</p>
            </div>
        <?php else: ?>
            <div class="student-list">
                <?php foreach ($linked_students as $student): ?>
                    <div class="student-card">
                        <div class="student-header">
                            <div class="student-info">
                                <div class="student-avatar">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div>
                                    <h3><?php echo htmlspecialchars($student['full_name']); ?></h3>
                                    <div class="student-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($student['location'] ?? 'Location not specified'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="student-status">
                                <?php if ($student['is_available']): ?>
                                    <span class="status-badge available">Available</span>
                                <?php else: ?>
                                    <span class="status-badge unavailable">Not Available</span>
                                <?php endif; ?>
                                <span class="status-badge linked">🔗 Linked</span>
                            </div>
                        </div>
                        
                        <div class="student-body">
                            <?php if ($student['skills']): ?>
                                <div class="student-skills">
                                    <strong>Skills:</strong>
                                    <?php 
                                        $skills = explode(',', $student['skills']);
                                        foreach ($skills as $skill): 
                                            $skill = trim($skill);
                                    ?>
                                        <span class="skill-tag"><?php echo htmlspecialchars($skill); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="student-meta">
                                <?php if ($student['rating'] > 0): ?>
                                    <span><i class="fas fa-star" style="color: #F59E0B;"></i> <?php echo number_format($student['rating'], 1); ?></span>
                                <?php endif; ?>
                                <?php if ($student['total_jobs_completed'] > 0): ?>
                                    <span><i class="fas fa-check-circle" style="color: #10B981;"></i> <?php echo $student['total_jobs_completed']; ?> jobs</span>
                                <?php endif; ?>
                                <span><i class="fas fa-calendar"></i> Linked: <?php echo date('M d, Y', strtotime($student['linked_date'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="student-footer">
                            <div class="student-actions">
                                <a href="?unlink=<?php echo $student['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Unlink this student?')">
                                    <i class="fas fa-unlink"></i> Unlink
                                </a>
                                <a href="../api/chat.php?user=<?php echo $student['id']; ?>" class="btn btn-primary btn-sm">
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
.student-list { display: grid; gap: 16px; }
.student-card { background: #FFFFFF; border-radius: 12px; padding: 20px 24px; border: 1px solid #E2E8F0; transition: all 0.2s ease; }
.student-card:hover { border-color: #4F46E5; }
.student-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #E2E8F0; flex-wrap: wrap; gap: 10px; }
.student-info { display: flex; align-items: center; gap: 12px; }
.student-avatar { width: 48px; height: 48px; border-radius: 50%; background: #EEF2FF; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #4F46E5; }
.student-info h3 { font-size: 16px; font-weight: 700; color: #0F172A; margin: 0; }
.student-location { font-size: 13px; color: #64748B; }
.student-status { display: flex; gap: 8px; flex-wrap: wrap; }
.student-body { margin-bottom: 12px; }
.student-skills { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 8px; align-items: center; }
.student-skills strong { font-size: 13px; color: #475569; margin-right: 4px; }
.skill-tag { display: inline-block; padding: 2px 10px; background: #EEF2FF; color: #4F46E5; font-size: 12px; font-weight: 500; border-radius: 9999px; }
.student-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 13px; color: #64748B; }
.student-meta span { display: flex; align-items: center; gap: 4px; }
.student-footer { padding-top: 12px; border-top: 1px solid #E2E8F0; }
.student-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.status-badge.linked { background: #EDE9FE; color: #5B21B6; }
@media (max-width: 768px) { .student-header { flex-direction: column; align-items: flex-start; } .student-actions { flex-direction: column; width: 100%; } .student-actions .btn { width: 100%; justify-content: center; } }
</style>

<?php include '../includes/footer.php'; ?>
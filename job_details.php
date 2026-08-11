<?php
// ============================================
// SkillSeek - Job Details (shared view)
// File: job_details.php
// Description: View a single job listing with
//              role-aware Apply / Save actions
// ============================================

require_once 'config/database.php';

$job_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if ($job_id <= 0) {
    redirect('jobs.php');
}

// Fetch job with employer + category
$stmt = $pdo->prepare("
    SELECT j.*, u.full_name AS employer_name, u.id AS employer_id, u.email AS employer_email,
           c.name AS category_name
    FROM jobs j
    JOIN users u ON j.employer_id = u.id
    LEFT JOIN categories c ON j.category_id = c.id
    WHERE j.id = ?
");
$stmt->execute([$job_id]);
$job = $stmt->fetch();

if (!$job) {
    $page_title = 'Job Not Found - SkillSeek';
    include 'includes/header.php';
    echo '<section class="section" style="padding-top:60px;min-height:50vh;"><div class="container-wide"><div class="empty-state reveal"><i class="fas fa-circle-exclamation"></i><h3>Job not found</h3><p>This opportunity may have been removed or is no longer available.</p><a href="jobs.php" class="btn btn-primary">Browse Jobs</a></div></div></section>';
    include 'includes/footer.php';
    return;
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// ============================================
// HANDLE SAVE / REMOVE (logged-in students)
// ============================================
$action_message = '';
if (isset($_GET['action']) && in_array($_GET['action'], ['save', 'remove'], true) && $user_id && $user_role === 'student') {
    if ($_GET['action'] === 'save') {
        $chk = $pdo->prepare("SELECT id FROM saved_jobs WHERE student_id = ? AND job_id = ?");
        $chk->execute([$user_id, $job_id]);
        if (!$chk->fetch()) {
            $ins = $pdo->prepare("INSERT INTO saved_jobs (student_id, job_id) VALUES (?, ?)");
            $ins->execute([$user_id, $job_id]);
            $action_message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Job saved successfully!</div>';
        } else {
            $action_message = '<div class="alert alert-info"><i class="fas fa-circle-info"></i> Job already saved.</div>';
        }
    } else {
        $del = $pdo->prepare("DELETE FROM saved_jobs WHERE student_id = ? AND job_id = ?");
        $del->execute([$user_id, $job_id]);
        $action_message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Job removed from saved.</div>';
    }
}

// ============================================
// STUDENT STATE (applied / saved)
// ============================================
$already_applied = false;
$is_saved = false;

if ($user_id && $user_role === 'student') {
    $stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND student_id = ?");
    $stmt->execute([$job_id, $user_id]);
    $already_applied = (bool)$stmt->fetch();

    $stmt = $pdo->prepare("SELECT id FROM saved_jobs WHERE job_id = ? AND student_id = ?");
    $stmt->execute([$job_id, $user_id]);
    $is_saved = (bool)$stmt->fetch();
}

$page_title = htmlspecialchars($job['title']) . ' - SkillSeek';
include 'includes/header.php';
?>

<!-- ============================================================
     JOB DETAILS
     ============================================================ -->
<section class="section" style="padding-top:60px;">
    <div class="container-wide" style="max-width:920px;">

        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="jobs.php">Jobs</a>
            <i class="fas fa-chevron-right"></i>
            <span><?php echo htmlspecialchars($job['title']); ?></span>
        </nav>

        <?php echo $action_message; ?>

        <article class="job-detail-card reveal">
            <div class="job-detail-head">
                <div class="job-detail-logo">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="job-detail-title">
                    <h1><?php echo htmlspecialchars($job['title']); ?></h1>
                    <p>
                        <i class="fas fa-building"></i> <?php echo htmlspecialchars($job['employer_name']); ?>
                        <span class="status-badge <?php echo htmlspecialchars($job['status']); ?>"><?php echo ucfirst(str_replace('_', ' ', $job['status'])); ?></span>
                    </p>
                </div>
            </div>

            <div class="job-detail-meta">
                <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($job['category_name'] ?? 'General'); ?></span>
                <span><i class="fas fa-money-bill"></i> KSh <?php echo number_format($job['budget_min'] ?? 0, 2); ?>
                    <?php if (!empty($job['budget_max']) && $job['budget_max'] > 0): ?>
                        - KSh <?php echo number_format($job['budget_max'], 2); ?>
                    <?php endif; ?>
                </span>
                <span><i class="fas fa-map-marker-alt"></i>
                    <?php echo $job['is_remote'] ? 'Remote' : htmlspecialchars($job['location'] ?? 'Not specified'); ?>
                </span>
                <span><i class="fas fa-calendar"></i> Posted <?php echo date('M d, Y', strtotime($job['created_at'])); ?></span>
                <?php if ($job['expires_at']): ?>
                    <span><i class="fas fa-clock"></i> Expires <?php echo date('M d, Y', strtotime($job['expires_at'])); ?></span>
                <?php endif; ?>
            </div>

            <div class="job-detail-body">
                <h3><i class="fas fa-align-left"></i> Description</h3>
                <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>

                <?php if (!empty($job['requirements'])): ?>
                    <h3><i class="fas fa-list-check"></i> Requirements</h3>
                    <p><?php echo nl2br(htmlspecialchars($job['requirements'])); ?></p>
                <?php endif; ?>

                <?php if (!empty($job['responsibilities'])): ?>
                    <h3><i class="fas fa-tasks"></i> Responsibilities</h3>
                    <p><?php echo nl2br(htmlspecialchars($job['responsibilities'])); ?></p>
                <?php endif; ?>
            </div>

            <div class="job-detail-actions">
                <?php if (!$user_id): ?>
                    <a href="auth/register.php" class="btn btn-primary btn-lg"><i class="fas fa-user-plus"></i> Register to Apply</a>
                    <a href="auth/login.php" class="btn btn-outline btn-lg"><i class="fas fa-sign-in-alt"></i> Login</a>
                <?php elseif ($user_role === 'student'): ?>
                    <?php if ($job['status'] === 'open'): ?>
                        <?php if ($already_applied): ?>
                            <span class="btn btn-success btn-lg disabled"><i class="fas fa-check"></i> Applied</span>
                            <a href="student/my_applications.php" class="btn btn-outline btn-lg">View My Applications</a>
                        <?php else: ?>
                            <a href="student/apply.php?id=<?php echo $job['id']; ?>" class="btn btn-primary btn-lg"><i class="fas fa-paper-plane"></i> Apply Now</a>
                        <?php endif; ?>
                        <?php if ($is_saved): ?>
                            <a href="?id=<?php echo $job['id']; ?>&action=remove" class="btn btn-outline btn-lg" onclick="return confirm('Remove this job from saved?')"><i class="fas fa-bookmark"></i> Saved</a>
                        <?php else: ?>
                            <a href="?id=<?php echo $job['id']; ?>&action=save" class="btn btn-outline btn-lg"><i class="fas fa-bookmark"></i> Save Job</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="btn btn-secondary btn-lg disabled"><i class="fas fa-lock"></i> Not Accepting Applications</span>
                    <?php endif; ?>
                <?php elseif ($user_role === 'employer'): ?>
                    <a href="employer/applications.php?job_id=<?php echo $job['id']; ?>" class="btn btn-primary btn-lg"><i class="fas fa-users"></i> View Applications</a>
                    <?php if ($job['employer_id'] == $user_id): ?>
                        <a href="employer/edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-outline btn-lg"><i class="fas fa-edit"></i> Edit Job</a>
                    <?php endif; ?>
                <?php endif; ?>

                <a href="javascript:history.back()" class="btn btn-secondary btn-lg"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </article>

    </div>
</section>

<?php include 'includes/footer.php'; ?>

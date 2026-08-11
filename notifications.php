<?php
// ============================================
// SkillSeek - Notifications
// File: notifications.php
// Description: View and manage user notifications
// ============================================

require_once 'config/database.php';

if (!isLoggedIn()) {
    redirect('auth/login.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];
$user_role = $_SESSION['role'];

// ============================================
// HANDLE MARK-ALL-READ
// ============================================
$action_message = '';
if (isset($_GET['action']) && $_GET['action'] === 'mark_all_read') {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $action_message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> All notifications marked as read.</div>';
}

// ============================================
// HANDLE MARK-ONE-READ
// ============================================
if (isset($_GET['mark']) && is_numeric($_GET['mark'])) {
    $nid = (int)$_GET['mark'];
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$nid, $user_id]);
}

// ============================================
// GET NOTIFICATIONS
// ============================================
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 100");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->execute([$user_id]);
$unread = (int)$stmt->fetch()['total'];

$page_title = 'Notifications - SkillSeek';
include 'includes/header.php';
?>

<!-- ============================================================
     PAGE CONTENT
     ============================================================ -->
<div class="dashboard-container">

    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar"><i class="fas fa-bell"></i></div>
            <h3><?php echo htmlspecialchars($user_name); ?></h3>
            <span class="role-badge <?php echo $user_role; ?>"><?php echo ucfirst($user_role); ?></span>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <?php if ($user_role === 'employer'): ?>
                    <li><a href="employer/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="employer/post_job.php"><i class="fas fa-plus-circle"></i> Post Job</a></li>
                    <li><a href="employer/my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a></li>
                    <li><a href="employer/applications.php"><i class="fas fa-users"></i> Applications</a></li>
                    <li><a href="employer/talent.php"><i class="fas fa-user-graduate"></i> Find Talent</a></li>
                <?php else: ?>
                    <li><a href="student/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="student/available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a></li>
                    <li><a href="student/my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                    <li><a href="student/saved_jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a></li>
                <?php endif; ?>
                <li class="active"><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                <li><a href="profile.php"><i class="fas fa-user-edit"></i> Profile</a></li>
                <li><a href="auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="dashboard-main">

        <div class="page-header">
            <div class="header-left">
                <h1>Notifications</h1>
                <p>Stay up to date with your activity</p>
            </div>
            <div class="header-right">
                <?php if ($unread > 0): ?>
                    <a href="?action=mark_all_read" class="btn btn-primary">
                        <i class="fas fa-check-double"></i> Mark All Read
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php echo $action_message; ?>

        <div class="stats-summary" style="margin-bottom:24px;">
            <div class="stat-item">
                <span class="stat-number"><?php echo count($notifications); ?></span>
                <span class="stat-label">Total</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" style="color:#2563EB;"><?php echo $unread; ?></span>
                <span class="stat-label">Unread</span>
            </div>
        </div>

        <?php if (empty($notifications)): ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>No notifications</h3>
                <p>You have no notifications yet. Activity will appear here.</p>
            </div>
        <?php else: ?>
            <div class="notifications-list">
                <?php foreach ($notifications as $n): ?>
                    <?php
                        $icon = 'fa-circle-info';
                        switch ($n['type']) {
                            case 'application': $icon = 'fa-file-alt'; break;
                            case 'message':     $icon = 'fa-comment-dots'; break;
                            case 'payment':     $icon = 'fa-money-bill'; break;
                            case 'job':         $icon = 'fa-briefcase'; break;
                            case 'acceptance':  $icon = 'fa-check-circle'; break;
                            case 'rejection':   $icon = 'fa-times-circle'; break;
                            default:            $icon = 'fa-circle-info';
                        }
                    ?>
                    <a href="<?php echo !empty($n['link']) ? htmlspecialchars($n['link']) : 'notifications.php'; ?>"
                       class="notification-item <?php echo $n['is_read'] ? '' : 'unread'; ?>"
                       <?php if (!$n['is_read']): ?>onclick="markRead(<?php echo (int)$n['id']; ?>, event);"<?php endif; ?>>
                        <div class="notification-icon"><i class="fas <?php echo $icon; ?>"></i></div>
                        <div class="notification-body">
                            <strong><?php echo htmlspecialchars($n['title']); ?></strong>
                            <p><?php echo htmlspecialchars($n['message']); ?></p>
                            <span class="notification-time"><i class="fas fa-clock"></i> <?php echo timeAgo($n['created_at']); ?></span>
                        </div>
                        <?php if (!$n['is_read']): ?>
                            <span class="notification-dot" aria-label="Unread"></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

<script>
    function markRead(id, event) {
        if (event) { event.preventDefault(); }
        fetch('notifications.php?mark=' + id, { method: 'GET' })
            .then(function () {
                if (event) {
                    var link = event.target.closest ? event.target.closest('a') : null;
                    if (link) {
                        var href = link.getAttribute('href');
                        link.classList.remove('unread');
                        var dot = link.querySelector('.notification-dot');
                        if (dot) { dot.remove(); }
                        if (href && href !== 'notifications.php') {
                            window.location.href = href;
                        }
                    }
                }
            })
            .catch(function () { if (event) { window.location.href = 'notifications.php'; } });
    }
</script>

<?php include 'includes/footer.php'; ?>

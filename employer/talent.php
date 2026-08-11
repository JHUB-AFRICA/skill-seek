<?php
// ============================================
// SkillSeek - Find Talent
// File: employer/talent.php
// Description: Browse available students/freelancers
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
// GET FILTERS
// ============================================
$search = $_GET['search'] ?? '';
$skill_filter = $_GET['skill'] ?? '';

// ============================================
// GET TALENT
// ============================================
$sql = "
    SELECT u.id, u.full_name, u.email, u.phone, u.location, u.bio, u.profile_pic,
           sp.skills, sp.education, sp.experience, sp.hourly_rate, sp.rating, sp.total_jobs_completed
    FROM student_profiles sp
    JOIN users u ON u.id = sp.user_id
    WHERE sp.is_available = 1
";

$params = [];

if (!empty($search)) {
    $sql .= " AND (u.full_name LIKE ? OR u.location LIKE ? OR sp.skills LIKE ?)";
    $term = "%$search%";
    $params[] = $term; $params[] = $term; $params[] = $term;
}

if (!empty($skill_filter)) {
    $sql .= " AND sp.skills LIKE ?";
    $params[] = "%$skill_filter%";
}

$sql .= " ORDER BY sp.rating DESC, sp.total_jobs_completed DESC LIMIT 60";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$talent = $stmt->fetchAll();

$page_title = 'Find Talent - SkillSeek';
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
                <li class="active"><a href="talent.php"><i class="fas fa-user-graduate"></i> Find Talent</a></li>
                <li><a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="../profile.php"><i class="fas fa-user-edit"></i> Profile</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="dashboard-main">

        <div class="page-header">
            <div class="header-left">
                <h1>Find Talent</h1>
                <p>Browse available students and freelancers</p>
            </div>
            <div class="header-right">
                <span class="result-count"><?php echo count($talent); ?> available</span>
            </div>
        </div>

        <div class="filter-bar" style="margin-bottom:24px;">
            <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;width:100%;">
                <div class="search-box" style="flex:1;min-width:220px;">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search by name, location, or skill..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                    <?php if (!empty($search) || !empty($skill_filter)): ?>
                        <a href="talent.php" class="btn btn-secondary btn-sm">Clear</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php if (empty($talent)): ?>
            <div class="empty-state">
                <i class="fas fa-user-graduate"></i>
                <h3>No talent found</h3>
                <p>No available freelancers match your search. Try different keywords.</p>
                <a href="talent.php" class="btn btn-secondary">Clear Filters</a>
            </div>
        <?php else: ?>
            <div class="talent-grid">
                <?php foreach ($talent as $t): ?>
                    <?php
                        $initials = '';
                        foreach (preg_split('/\s+/', trim($t['full_name'])) as $part) {
                            if (isset($part[0])) { $initials .= strtoupper($part[0]); }
                        }
                        $skills = array_slice(array_filter(array_map('trim', explode(',', $t['skills'] ?? ''))), 0, 4);
                    ?>
                    <div class="talent-card">
                        <div class="talent-head">
                            <div class="talent-avatar"><?php echo $initials !== '' ? $initials : '<i class="fas fa-user"></i>'; ?></div>
                            <div>
                                <h3><?php echo htmlspecialchars($t['full_name']); ?></h3>
                                <p><i class="fas fa-location-dot"></i> <?php echo htmlspecialchars($t['location'] ?? 'Remote'); ?></p>
                            </div>
                            <span class="talent-rate">$<?php echo number_format((float)$t['hourly_rate'], 0); ?>/hr</span>
                        </div>
                        <div class="talent-rating">
                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                <i class="fas fa-star <?php echo $s <= round((float)$t['rating']) ? 'filled' : ''; ?>"></i>
                            <?php endfor; ?>
                            <span><?php echo number_format((float)$t['rating'], 1); ?></span>
                            <span class="talent-jobs"><?php echo (int)$t['total_jobs_completed']; ?> jobs done</span>
                        </div>
                        <?php if (!empty($t['bio'])): ?>
                            <p class="talent-bio"><?php echo htmlspecialchars(substr($t['bio'] ?? '', 0, 120)); ?><?php echo isset($t['bio']) && strlen($t['bio']) > 120 ? '...' : ''; ?></p>
                        <?php endif; ?>
                        <?php if (!empty($skills)): ?>
                            <div class="talent-skills">
                                <?php foreach ($skills as $skill): ?>
                                    <span><?php echo htmlspecialchars($skill); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="talent-foot">
                            <a href="../api/chat.php?user=<?php echo (int)$t['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-comment"></i> Contact</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

<?php include '../includes/footer.php'; ?>

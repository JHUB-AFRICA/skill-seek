<?php
// ============================================
<<<<<<< HEAD
// SkillSeek - Find Talent (with Linking)
=======
// SkillSeek - Find Talent
// File: employer/talent.php
// Description: Browse available students/freelancers
>>>>>>> 28517cf9d50c245056bdb5ef81c9337e20c1b477
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
<<<<<<< HEAD
// HANDLE LINK ACTIONS
// ============================================
if (isset($_GET['action']) && isset($_GET['id'])) {
    $student_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action === 'link') {
        // Check if already linked
        $stmt = $pdo->prepare("SELECT * FROM employer_student_links WHERE employer_id = ? AND student_id = ?");
        $stmt->execute([$user_id, $student_id]);
        
        if ($stmt->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT INTO employer_student_links (employer_id, student_id, status) VALUES (?, ?, 'accepted')");
            $stmt->execute([$user_id, $student_id]);
            $_SESSION['message'] = 'Student linked successfully!';
            
            // Create notification for student
            $stmt = $pdo->prepare("
                INSERT INTO notifications (user_id, type, title, message, link) 
                VALUES (?, 'link', 'New Connection', ?, '/student/dashboard.php')
            ");
            $stmt->execute([$student_id, $user_name . ' has linked with you!']);
        } else {
            $_SESSION['message'] = 'Student already linked.';
        }
    } elseif ($action === 'unlink') {
        $stmt = $pdo->prepare("DELETE FROM employer_student_links WHERE employer_id = ? AND student_id = ?");
        $stmt->execute([$user_id, $student_id]);
        $_SESSION['message'] = 'Student unlinked successfully.';
    }
    header('Location: talent.php');
    exit();
}

// ============================================
=======
>>>>>>> 28517cf9d50c245056bdb5ef81c9337e20c1b477
// GET FILTERS
// ============================================
$search = $_GET['search'] ?? '';
$skill_filter = $_GET['skill'] ?? '';

// ============================================
<<<<<<< HEAD
// GET STUDENTS
// ============================================
$sql = "
    SELECT 
        u.id,
        u.full_name,
        u.email,
        u.phone,
        u.location,
        u.bio,
        sp.skills,
        sp.education,
        sp.experience,
        sp.github_url,
        sp.linkedin_url,
        sp.hourly_rate,
        sp.is_available,
        sp.rating,
        sp.total_jobs_completed,
        (SELECT COUNT(*) FROM applications WHERE student_id = u.id) as total_applications,
        (SELECT COUNT(*) FROM employer_student_links WHERE employer_id = ? AND student_id = u.id) as is_linked
    FROM users u
    JOIN student_profiles sp ON u.id = sp.user_id
    WHERE u.role = 'student'
=======
// GET TALENT
// ============================================
$sql = "
    SELECT u.id, u.full_name, u.email, u.phone, u.location, u.bio, u.profile_pic,
           sp.skills, sp.education, sp.experience, sp.hourly_rate, sp.rating, sp.total_jobs_completed
    FROM student_profiles sp
    JOIN users u ON u.id = sp.user_id
    WHERE sp.is_available = 1
>>>>>>> 28517cf9d50c245056bdb5ef81c9337e20c1b477
";

$params = [$user_id];

if (!empty($search)) {
<<<<<<< HEAD
    $sql .= " AND (u.full_name LIKE ? OR u.location LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
=======
    $sql .= " AND (u.full_name LIKE ? OR u.location LIKE ? OR sp.skills LIKE ?)";
    $term = "%$search%";
    $params[] = $term; $params[] = $term; $params[] = $term;
>>>>>>> 28517cf9d50c245056bdb5ef81c9337e20c1b477
}

if (!empty($skill_filter)) {
    $sql .= " AND sp.skills LIKE ?";
    $params[] = "%$skill_filter%";
}

<<<<<<< HEAD
$sql .= " ORDER BY sp.rating DESC, sp.total_jobs_completed DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

// ============================================
// GET LINKED STUDENTS COUNT
// ============================================
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM employer_student_links WHERE employer_id = ?");
$stmt->execute([$user_id]);
$linked_count = $stmt->fetch()['total'];
=======
$sql .= " ORDER BY sp.rating DESC, sp.total_jobs_completed DESC LIMIT 60";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$talent = $stmt->fetchAll();
>>>>>>> 28517cf9d50c245056bdb5ef81c9337e20c1b477

$page_title = 'Find Talent - SkillSeek';
include '../includes/header.php';
?>

<div class="dashboard-container">
<<<<<<< HEAD
    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar">
                <i class="fas fa-building"></i>
            </div>
=======

    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar"><i class="fas fa-building"></i></div>
>>>>>>> 28517cf9d50c245056bdb5ef81c9337e20c1b477
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
                <li><a href="linked_students.php"><i class="fas fa-link"></i> Linked Students (<?php echo $linked_count; ?>)</a></li>
                <li><a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>
<<<<<<< HEAD
    
    <main class="dashboard-main">
        <div class="page-header">
            <div class="header-left">
                <h1>Find Talent</h1>
                <p>Browse and link with skilled students</p>
            </div>
            <div class="header-right">
                <span class="result-count"><?php echo count($students); ?> students found</span>
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label><i class="fas fa-search"></i></label>
                        <input type="text" name="search" placeholder="Search by name or location..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="filter-group">
                        <label>Skill</label>
                        <input type="text" name="skill" placeholder="e.g. PHP, React, Design" value="<?php echo htmlspecialchars($skill_filter); ?>">
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="talent.php" class="btn btn-secondary">Clear All</a>
=======

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
>>>>>>> 28517cf9d50c245056bdb5ef81c9337e20c1b477
                </div>
            </form>
        </div>

<<<<<<< HEAD
        <!-- Students List -->
        <?php if (empty($students)): ?>
            <div class="empty-state">
                <i class="fas fa-user-graduate"></i>
                <h3>No students found</h3>
                <p>Try adjusting your search criteria.</p>
            </div>
        <?php else: ?>
            <div class="student-list">
                <?php foreach ($students as $student): ?>
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
                                <?php if ($student['is_linked'] > 0): ?>
                                    <span class="status-badge linked">🔗 Linked</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="student-body">
                            <?php if ($student['bio']): ?>
                                <p class="student-bio"><?php echo htmlspecialchars(substr($student['bio'], 0, 150)); ?>...</p>
                            <?php endif; ?>
                            
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
                                <?php if ($student['hourly_rate'] > 0): ?>
                                    <span><i class="fas fa-money-bill"></i> KSh <?php echo number_format($student['hourly_rate'], 2); ?>/hr</span>
                                <?php endif; ?>
                                <?php if ($student['rating'] > 0): ?>
                                    <span><i class="fas fa-star" style="color: #F59E0B;"></i> <?php echo number_format($student['rating'], 1); ?></span>
                                <?php endif; ?>
                                <?php if ($student['total_jobs_completed'] > 0): ?>
                                    <span><i class="fas fa-check-circle" style="color: #10B981;"></i> <?php echo $student['total_jobs_completed']; ?> jobs</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="student-footer">
                            <div class="student-actions">
                                <?php if ($student['is_linked'] > 0): ?>
                                    <a href="?action=unlink&id=<?php echo $student['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Unlink this student?')">
                                        <i class="fas fa-unlink"></i> Unlink
                                    </a>
                                <?php else: ?>
                                    <a href="?action=link&id=<?php echo $student['id']; ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-link"></i> Link Student
                                    </a>
                                <?php endif; ?>
                                <a href="../api/chat.php?user=<?php echo $student['id']; ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-comment"></i> Message
                                </a>
                                <?php if ($student['github_url']): ?>
                                    <a href="<?php echo $student['github_url']; ?>" target="_blank" class="btn btn-secondary btn-sm">
                                        <i class="fab fa-github"></i> GitHub
                                    </a>
                                <?php endif; ?>
                            </div>
=======
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
>>>>>>> 28517cf9d50c245056bdb5ef81c9337e20c1b477
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
<<<<<<< HEAD
    </main>
</div>

<style>
.student-list { display: grid; gap: 16px; }
.student-card { background: #FFFFFF; border-radius: 12px; padding: 20px 24px; border: 1px solid #E2E8F0; transition: all 0.2s ease; }
.student-card:hover { border-color: #4F46E5; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
.student-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #E2E8F0; flex-wrap: wrap; gap: 10px; }
.student-info { display: flex; align-items: center; gap: 12px; }
.student-avatar { width: 48px; height: 48px; border-radius: 50%; background: #EEF2FF; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #4F46E5; }
.student-info h3 { font-size: 16px; font-weight: 700; color: #0F172A; margin: 0; }
.student-location { font-size: 13px; color: #64748B; }
.student-status { display: flex; gap: 8px; flex-wrap: wrap; }
.student-body { margin-bottom: 12px; }
.student-bio { color: #475569; font-size: 14px; margin-bottom: 8px; }
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
=======

    </main>
</div>

<?php include '../includes/footer.php'; ?>
>>>>>>> 28517cf9d50c245056bdb5ef81c9337e20c1b477

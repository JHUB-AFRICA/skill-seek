<?php
// ============================================
// SkillSeek - Find Talent
// File: employer/talent.php
<<<<<<< HEAD
// Description: Employers can browse and search for students
// ============================================

// Include configuration
require_once '../config/database.php';

// Check if user is logged in
=======
// Description: Browse available students/freelancers
// ============================================

require_once '../config/database.php';

>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

<<<<<<< HEAD
// Check if user is an employer
=======
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
if (getUserRole() !== 'employer') {
    redirect('../student/dashboard.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// ============================================
<<<<<<< HEAD
// GET FILTER PARAMETERS
// ============================================
$search = $_GET['search'] ?? '';
$skill_filter = $_GET['skill'] ?? '';
$availability = $_GET['availability'] ?? '';

// ============================================
// GET STUDENTS WITH FILTERS
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
        (SELECT COUNT(*) FROM applications WHERE student_id = u.id) as total_applications
    FROM users u
    JOIN student_profiles sp ON u.id = sp.user_id
    WHERE u.role = 'student'
=======
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
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
";

$params = [];

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
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
}

if (!empty($skill_filter)) {
    $sql .= " AND sp.skills LIKE ?";
    $params[] = "%$skill_filter%";
}

<<<<<<< HEAD
if ($availability === 'available') {
    $sql .= " AND sp.is_available = 1";
} elseif ($availability === 'unavailable') {
    $sql .= " AND sp.is_available = 0";
}

$sql .= " ORDER BY sp.rating DESC, sp.total_jobs_completed DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

// Set page title
$page_title = 'Find Talent - SkillSeek';

// Include header
=======
$sql .= " ORDER BY sp.rating DESC, sp.total_jobs_completed DESC LIMIT 60";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$talent = $stmt->fetchAll();

$page_title = 'Find Talent - SkillSeek';
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
include '../includes/header.php';
?>

<!-- ============================================================
     PAGE CONTENT
     ============================================================ -->
<div class="dashboard-container">
<<<<<<< HEAD
    
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar">
                <i class="fas fa-building"></i>
            </div>
            <h3><?php echo htmlspecialchars($user_name); ?></h3>
            <span class="role-badge employer">Employer</span>
        </div>
        
=======

    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar"><i class="fas fa-building"></i></div>
            <h3><?php echo htmlspecialchars($user_name); ?></h3>
            <span class="role-badge employer">Employer</span>
        </div>
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
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
<<<<<<< HEAD
    
    <!-- Main Content -->
    <main class="dashboard-main">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-left">
                <h1>Find Talent</h1>
                <p>Browse and search for skilled students</p>
            </div>
            <div class="header-right">
                <span class="result-count"><?php echo count($students); ?> students found</span>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="search"><i class="fas fa-search"></i></label>
                        <input type="text" id="search" name="search" 
                               placeholder="Search by name or location..."
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="skill">Skill</label>
                        <input type="text" id="skill" name="skill" 
                               placeholder="e.g. PHP, React, Design"
                               value="<?php echo htmlspecialchars($skill_filter); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="availability">Availability</label>
                        <select id="availability" name="availability">
                            <option value="">All Students</option>
                            <option value="available" <?php echo $availability === 'available' ? 'selected' : ''; ?>>Available</option>
                            <option value="unavailable" <?php echo $availability === 'unavailable' ? 'selected' : ''; ?>>Not Available</option>
                        </select>
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="talent.php" class="btn btn-secondary">Clear All</a>
                </div>
            </form>
        </div>
        
        <!-- Students List -->
        <?php if (empty($students)): ?>
            <div class="empty-state">
                <i class="fas fa-user-graduate"></i>
                <h3>No students found</h3>
                <p>
                    <?php if (!empty($search) || !empty($skill_filter) || !empty($availability)): ?>
                        No students match your search criteria. Try adjusting your filters.
                    <?php else: ?>
                        There are currently no students registered. Check back later!
                    <?php endif; ?>
                </p>
                <a href="talent.php" class="btn btn-secondary">Clear Filters</a>
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
                                <?php if ($student['education']): ?>
                                    <span><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars(substr($student['education'], 0, 60)); ?></span>
                                <?php endif; ?>
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
                                <a href="../api/chat.php?user=<?php echo $student['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-comment"></i> Message
                                </a>
                                <a href="mailto:<?php echo $student['email']; ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-envelope"></i> Email
                                </a>
                                <?php if ($student['github_url']): ?>
                                    <a href="<?php echo $student['github_url']; ?>" target="_blank" class="btn btn-secondary btn-sm">
                                        <i class="fab fa-github"></i> GitHub
                                    </a>
                                <?php endif; ?>
                                <?php if ($student['linkedin_url']): ?>
                                    <a href="<?php echo $student['linkedin_url']; ?>" target="_blank" class="btn btn-secondary btn-sm">
                                        <i class="fab fa-linkedin"></i> LinkedIn
                                    </a>
                                <?php endif; ?>
                            </div>
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
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
<<<<<<< HEAD
        
    </main>
</div>

<style>
/* ============================================================
   FIND TALENT PAGE STYLES
   ============================================================ */

.student-list {
    display: grid;
    gap: 20px;
}

.student-card {
    background: #FFFFFF;
    border-radius: 12px;
    padding: 20px 24px;
    border: 1px solid #E2E8F0;
    transition: all 0.2s ease;
}

.student-card:hover {
    border-color: #4F46E5;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.student-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #E2E8F0;
    flex-wrap: wrap;
    gap: 10px;
}

.student-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.student-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #EEF2FF;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #4F46E5;
}

.student-info h3 {
    font-size: 16px;
    font-weight: 700;
    color: #0F172A;
    margin: 0;
}

.student-location {
    font-size: 13px;
    color: #64748B;
}

.student-location i {
    margin-right: 4px;
}

.student-status .status-badge {
    font-size: 12px;
    padding: 3px 12px;
}

.status-badge.available {
    background: #D1FAE5;
    color: #065F46;
}

.status-badge.unavailable {
    background: #FEE2E2;
    color: #991B1B;
}

.student-body {
    margin-bottom: 12px;
}

.student-bio {
    color: #475569;
    font-size: 14px;
    margin-bottom: 8px;
}

.student-skills {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 8px;
    align-items: center;
}

.student-skills strong {
    font-size: 13px;
    color: #475569;
    margin-right: 4px;
}

.skill-tag {
    display: inline-block;
    padding: 2px 10px;
    background: #EEF2FF;
    color: #4F46E5;
    font-size: 12px;
    font-weight: 500;
    border-radius: 9999px;
}

.student-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 13px;
    color: #64748B;
}

.student-meta span {
    display: flex;
    align-items: center;
    gap: 4px;
}

.student-footer {
    padding-top: 12px;
    border-top: 1px solid #E2E8F0;
}

.student-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

/* Empty state */
.empty-state .btn-secondary {
    margin-top: 8px;
}

@media (max-width: 768px) {
    .student-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .student-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .student-actions .btn {
        width: 100%;
        justify-content: center;
    }
    
    .student-meta {
        flex-direction: column;
        gap: 4px;
    }
}
</style>

<?php include '../includes/footer.php'; ?>
=======

    </main>
</div>

<?php include '../includes/footer.php'; ?>
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1

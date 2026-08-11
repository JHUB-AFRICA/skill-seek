<?php
// ============================================
// SkillSeek - Header
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['full_name'] ?? 'Guest';
$user_role = $_SESSION['role'] ?? null;
$base_path = $base_path ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'SkillSeek'; ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/SkillSeek/assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        
      <!-- Logo -->
<div class="header-logo">
    <a href="/SkillSeek/index.php">
        <img src="/SkillSeek/assets/images/logo.jpeg" alt="SkillSeek" style="height: 40px; width: auto; display: block;">
        <span style="font-weight: 900; font-size: 1.6rem; color: #0F172A; margin-left: 8px;">Skill<span style="color: #4F46E5;">Seek</span></span>
    </a>
</div>
        
        <!-- Navigation -->
        <nav class="header-nav">
            <ul class="nav-list">
                <?php if ($user_id && $user_role === 'employer'): ?>
                    <li><a href="<?php echo $base_path; ?>employer/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="<?php echo $base_path; ?>employer/post_job.php"><i class="fas fa-plus-circle"></i> Post Job</a></li>
                    <li><a href="<?php echo $base_path; ?>employer/my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a></li>
                    <li><a href="<?php echo $base_path; ?>employer/applications.php"><i class="fas fa-users"></i> Applications</a></li>
                    <li><a href="<?php echo $base_path; ?>employer/talent.php"><i class="fas fa-user-graduate"></i> Find Talent</a></li>
                    
                <?php elseif ($user_id && $user_role === 'student'): ?>
                    <li><a href="<?php echo $base_path; ?>student/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="<?php echo $base_path; ?>student/available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a></li>
                    <li><a href="<?php echo $base_path; ?>student/my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                    <li><a href="<?php echo $base_path; ?>student/saved_jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a></li>
                    <li><a href="<?php echo $base_path; ?>student/profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                    
                <?php else: ?>
                    <li><a href="<?php echo $base_path; ?>index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?php echo $base_path; ?>jobs.php"><i class="fas fa-briefcase"></i> Browse Jobs</a></li>
                    <li><a href="<?php echo $base_path; ?>about.php"><i class="fas fa-info-circle"></i> About</a></li>
                    <li><a href="<?php echo $base_path; ?>support.php"><i class="fas fa-headset"></i> Support</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <!-- Actions -->
        <div class="header-actions">
            <?php if ($user_id): ?>
                <a href="<?php echo $base_path; ?>api/chat.php" class="action-btn" title="Messages">
                    <i class="fas fa-comment-dots"></i>
                    <span class="badge">0</span>
                </a>
                
                <div class="user-dropdown">
                    <button class="user-btn" onclick="toggleUserMenu()">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo htmlspecialchars($user_name); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" id="userMenu">
                        <?php if ($user_role === 'employer'): ?>
                            <a href="<?php echo $base_path; ?>employer/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <a href="<?php echo $base_path; ?>employer/my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a>
                        <?php else: ?>
                            <a href="<?php echo $base_path; ?>student/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <a href="<?php echo $base_path; ?>student/available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a>
                        <?php endif; ?>
                        <hr>
                        <a href="<?php echo $base_path; ?>profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="<?php echo $base_path; ?>auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>auth/login.php" class="btn btn-outline">Log In</a>
                <a href="<?php echo $base_path; ?>auth/register.php" class="btn btn-primary">Register</a>
            <?php endif; ?>
            
            <button class="mobile-toggle" onclick="toggleMobileMenu()" aria-label="Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
    </div>
    
    <div class="mobile-menu" id="mobileMenu">
        <ul>
            <?php if ($user_id && $user_role === 'employer'): ?>
                <li><a href="<?php echo $base_path; ?>employer/dashboard.php">Dashboard</a></li>
                <li><a href="<?php echo $base_path; ?>employer/post_job.php">Post Job</a></li>
                <li><a href="<?php echo $base_path; ?>employer/my_jobs.php">My Jobs</a></li>
                <li><a href="<?php echo $base_path; ?>employer/applications.php">Applications</a></li>
                <li><a href="/SkillSeek/auth/logout.php">Logout</a></li>
            <?php elseif ($user_id && $user_role === 'student'): ?>
                <li><a href="<?php echo $base_path; ?>student/dashboard.php">Dashboard</a></li>
                <li><a href="<?php echo $base_path; ?>student/available_jobs.php">Find Jobs</a></li>
                <li><a href="<?php echo $base_path; ?>student/my_applications.php">My Applications</a></li>
                <li><a href="<?php echo $base_path; ?>student/profile.php">Profile</a></li>
                <li><a href="<?php echo $base_path; ?>auth/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="<?php echo $base_path; ?>index.php">Home</a></li>
                <li><a href="<?php echo $base_path; ?>jobs.php">Browse Jobs</a></li>
                <li><a href="<?php echo $base_path; ?>auth/login.php">Login</a></li>
                <li><a href="<?php echo $base_path; ?>auth/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</header>

<script>
function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    menu.classList.toggle('open');
}

function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('open');
}

document.addEventListener('click', function(e) {
    const menu = document.getElementById('userMenu');
    const btn = document.querySelector('.user-btn');
    if (menu && btn && !btn.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.remove('open');
    }
});
</script>

<main>
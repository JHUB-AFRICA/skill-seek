<?php
// ============================================
// SkillSeek - Header (With Logo Image)
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['full_name'] ?? 'Guest';
$user_role = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'SkillSeek'; ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/SkillSeek/assets/css/style.css">
</head>
<body>

<!-- ============================================================
     HEADER - Job Platform
     ============================================================ -->
<header class="site-header">
    <div class="header-inner">
        
       <!-- LEFT: Logo with Image -->
<div class="header-logo">
    <a href="/SkillSeek/index.php">
        <img src="/SkillSeek/assets/images/logo.jpeg" alt="SkillSeek Logo" class="logo-image">
        <span class="logo-name">Skill<span>Seek</span></span>
    </a>
</div>
        
        <!-- CENTER: Navigation -->
        <nav class="header-nav">
            <ul class="nav-list">
                <?php if ($user_id && $user_role === 'employer'): ?>
                    <!-- EMPLOYER NAVIGATION -->
                    <li><a href="/SkillSeek/employer/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="/SkillSeek/employer/post_job.php"><i class="fas fa-plus-circle"></i> Post Job</a></li>
                    <li><a href="/SkillSeek/employer/my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a></li>
                    <li><a href="/SkillSeek/employer/applications.php"><i class="fas fa-users"></i> Applications</a></li>
                    <li><a href="/SkillSeek/employer/talent.php"><i class="fas fa-user-graduate"></i> Find Talent</a></li>
                    
                <?php elseif ($user_id && $user_role === 'student'): ?>
                    <!-- STUDENT NAVIGATION -->
                    <li><a href="/SkillSeek/student/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="/SkillSeek/student/available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a></li>
                    <li><a href="/SkillSeek/student/my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                    <li><a href="/SkillSeek/student/saved_jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a></li>
                    <li><a href="/SkillSeek/student/profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                    
                <?php else: ?>
                    <!-- GUEST NAVIGATION -->
                    <li><a href="/SkillSeek/index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="/SkillSeek/jobs.php"><i class="fas fa-briefcase"></i> Browse Jobs</a></li>
                    <li><a href="/SkillSeek/about.php"><i class="fas fa-info-circle"></i> About</a></li>
                    <li><a href="/SkillSeek/support.php"><i class="fas fa-headset"></i> Support</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <!-- RIGHT: Actions -->
        <div class="header-actions">
            <?php if ($user_id): ?>
                <!-- Messages -->
                <a href="/SkillSeek/api/chat.php" class="action-btn" title="Messages">
                    <i class="fas fa-comment-dots"></i>
                    <span class="badge">3</span>
                </a>
                
                <!-- Notifications -->
                <a href="/SkillSeek/notifications.php" class="action-btn" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="badge">5</span>
                </a>
                
                <!-- User Dropdown -->
                <div class="user-dropdown">
                    <button class="user-btn" onclick="toggleUserMenu()">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo htmlspecialchars($user_name); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" id="userMenu">
                        <?php if ($user_role === 'employer'): ?>
                            <a href="/SkillSeek/employer/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <a href="/SkillSeek/employer/my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a>
                            <a href="/SkillSeek/employer/applications.php"><i class="fas fa-users"></i> Applications</a>
                        <?php else: ?>
                            <a href="/SkillSeek/student/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <a href="/SkillSeek/student/available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a>
                            <a href="/SkillSeek/student/my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a>
                        <?php endif; ?>
                        <hr>
                        <a href="/SkillSeek/profile.php"><i class="fas fa-user"></i> Profile Settings</a>
                        <a href="/SkillSeek/support.php"><i class="fas fa-headset"></i> Support</a>
                        <a href="/SkillSeek/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/SkillSeek/auth/login.php" class="btn btn-outline">Log In</a>
                <a href="/SkillSeek/auth/register.php" class="btn btn-primary">Register</a>
            <?php endif; ?>
            
            <!-- Mobile Toggle -->
            <button class="mobile-toggle" onclick="toggleMobileMenu()" aria-label="Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
    </div>
    
    <!-- MOBILE MENU -->
    <div class="mobile-menu" id="mobileMenu">
        <ul>
            <?php if ($user_id && $user_role === 'employer'): ?>
                <li><a href="/SkillSeek/employer/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="/SkillSeek/employer/post_job.php"><i class="fas fa-plus-circle"></i> Post Job</a></li>
                <li><a href="/SkillSeek/employer/my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a></li>
                <li><a href="/SkillSeek/employer/applications.php"><i class="fas fa-users"></i> Applications</a></li>
                <li><a href="/SkillSeek/employer/talent.php"><i class="fas fa-user-graduate"></i> Find Talent</a></li>
                <hr>
                <li><a href="/SkillSeek/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="/SkillSeek/support.php"><i class="fas fa-headset"></i> Support</a></li>
                <li><a href="/SkillSeek/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                
            <?php elseif ($user_id && $user_role === 'student'): ?>
                <li><a href="/SkillSeek/student/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="/SkillSeek/student/available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a></li>
                <li><a href="/SkillSeek/student/my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                <li><a href="/SkillSeek/student/saved_jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a></li>
                <li><a href="/SkillSeek/student/profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                <hr>
                <li><a href="/SkillSeek/support.php"><i class="fas fa-headset"></i> Support</a></li>
                <li><a href="/SkillSeek/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                
            <?php else: ?>
                <li><a href="/SkillSeek/index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="/SkillSeek/jobs.php"><i class="fas fa-briefcase"></i> Browse Jobs</a></li>
                <li><a href="/SkillSeek/about.php"><i class="fas fa-info-circle"></i> About</a></li>
                <li><a href="/SkillSeek/support.php"><i class="fas fa-headset"></i> Support</a></li>
                <hr>
                <li><a href="/SkillSeek/auth/login.php"><i class="fas fa-sign-in-alt"></i> Log In</a></li>
                <li><a href="/SkillSeek/auth/register.php"><i class="fas fa-user-plus"></i> Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</header>

<!-- ============================================================
     JAVASCRIPT
     ============================================================ -->
<script>
    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        menu.classList.toggle('open');
    }
    
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('open');
        const toggle = document.querySelector('.mobile-toggle');
        toggle.classList.toggle('active');
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const userMenu = document.getElementById('userMenu');
        const userBtn = document.querySelector('.user-btn');
        if (userMenu && userBtn && !userBtn.contains(e.target) && !userMenu.contains(e.target)) {
            userMenu.classList.remove('open');
        }
    });
    
    // Close mobile menu on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const mobileMenu = document.getElementById('mobileMenu');
            const toggle = document.querySelector('.mobile-toggle');
            if (mobileMenu) {
                mobileMenu.classList.remove('open');
                if (toggle) toggle.classList.remove('active');
            }
        }
    });
</script>

<!-- MAIN CONTENT STARTS HERE -->
<main>
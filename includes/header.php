<?php
// ============================================
// SkillSeek - Header (Premium Redesign)
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['full_name'] ?? 'Guest';
$user_role = $_SESSION['role'] ?? null;
$base_path = $base_path ?? '';
$assets = $base_path . 'assets/';

// Determine current page for active nav highlighting + transparent header
$current_page = basename($_SERVER['PHP_SELF'] ?? 'index.php');

// Dashboard detection: pages that render the dashboard shell (sidebar layout).
// Used to hide the global center-nav and mobile menu so the sidebar is the
// single source of navigation (no duplicate menus).
$script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$is_dashboard = (bool)preg_match('#/(student|employer)/#', $script_name)
    || in_array($current_page, ['profile.php', 'notifications.php'], true);

$hs = function ($key) use ($current_page) {
    $map = [
        'home'           => ['index.php'],
        'browse'         => ['jobs.php', 'available_jobs.php'],
        'categories'     => ['categories.php'],
        'freelancers'    => ['index.php', 'freelancers.php', 'talent.php'],
        'companies'      => ['index.php', 'companies.php'],
        'about'          => ['about.php'],
        'support'        => ['support.php', 'contact.php', 'faq.php', 'help.php'],
        'dashboard'      => ['dashboard.php'],
        'postjob'        => ['post_job.php'],
        'myjobs'         => ['my_jobs.php'],
        'applications'   => ['applications.php'],
        'talent'         => ['talent.php'],
        'findjobs'       => ['available_jobs.php'],
        'myapps'         => ['my_applications.php'],
        'savedjobs'      => ['saved_jobs.php'],
        'profile'        => ['profile.php'],
    ];
    return in_array($current_page, $map[$key] ?? [], true);
};

$page_title = $page_title ?? 'SkillSeek';

// Body classes
$body_class = $is_dashboard ? 'dashboard-page' : '';
if (in_array($current_page, ['index.php'], true)) {
    $body_class = trim($body_class . ' header-overlay');
}
$body_attrs = 'data-page="' . htmlspecialchars($current_page, ENT_QUOTES) . '"'
    . ' data-role="' . htmlspecialchars($user_role ?? '', ENT_QUOTES) . '"'
    . ' data-user="' . htmlspecialchars($user_name ?? '', ENT_QUOTES) . '"';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="SkillSeek - A modern freelance marketplace connecting students, freelancers, and employers.">

    <!-- Favicon -->
    <link rel="icon" href="<?php echo $assets; ?>images/logo.jpeg" type="image/jpeg">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo $assets; ?>css/style.css">
    <link rel="stylesheet" href="<?php echo $assets; ?>css/app.css">
</head>
<body class="<?php echo trim($body_class); ?>" <?php echo $body_attrs; ?>>

<!-- ============================================================
     HEADER / NAVBAR
     ============================================================ -->
<header class="site-header" id="siteHeader">
    <div class="header-inner">

        <!-- LOGO -->
        <div class="header-logo">
            <a href="<?php echo $base_path; ?>/index.php" aria-label="SkillSeek Home">
                <img src="<?php echo $assets; ?>images/logo.jpeg" alt="SkillSeek Logo" class="logo-image">
                <span class="logo-name">Skill<span>Seek</span></span>
            </a>
        </div>

        <!-- CENTER NAV -->
        <nav class="header-nav" aria-label="Main navigation">
            <ul class="nav-list">
                <?php if ($user_id && $user_role === 'employer'): ?>
                    <!-- EMPLOYER NAV -->
                    <li><a class="<?php echo $hs('dashboard') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/employer/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a class="<?php echo $hs('postjob') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/employer/post_job.php"><i class="fas fa-plus-circle"></i> Post Job</a></li>
                    <li><a class="<?php echo $hs('myjobs') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/employer/my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a></li>
                    <li><a class="<?php echo $hs('applications') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/employer/applications.php"><i class="fas fa-users"></i> Applications</a></li>
                    <li><a class="<?php echo $hs('talent') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/employer/talent.php"><i class="fas fa-user-graduate"></i> Find Talent</a></li>

                <?php elseif ($user_id && $user_role === 'student'): ?>
                    <!-- STUDENT NAV -->
                    <li><a class="<?php echo $hs('dashboard') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/student/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a class="<?php echo $hs('findjobs') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/student/available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a></li>
                    <li><a class="<?php echo $hs('myapps') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/student/my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                    <li><a class="<?php echo $hs('savedjobs') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/student/saved_jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a></li>
                    <li><a class="<?php echo $hs('profile') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/student/profile.php"><i class="fas fa-user"></i> My Profile</a></li>

                <?php else: ?>
                    <!-- GUEST NAV -->
                    <li><a class="<?php echo $hs('home') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a class="<?php echo $hs('browse') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/jobs.php"><i class="fas fa-briefcase"></i> Browse Jobs</a></li>
                    <li><a class="<?php echo $hs('freelancers') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/index.php#freelancers"><i class="fas fa-user-tie"></i> Freelancers</a></li>
                    <li><a class="<?php echo $hs('categories') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/index.php#categories"><i class="fas fa-th-large"></i> Categories</a></li>
                    <li><a class="<?php echo $hs('companies') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/index.php#companies"><i class="fas fa-building"></i> Companies</a></li>
                    <li><a class="<?php echo $hs('about') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/about.php"><i class="fas fa-info-circle"></i> About</a></li>
                    <li><a class="<?php echo $hs('support') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>/support.php"><i class="fas fa-headset"></i> Support</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- RIGHT ACTIONS -->
        <div class="header-actions">
            <?php if ($user_id): ?>
                <!-- Search -->
                <form class="header-search" action="<?php echo $base_path; ?>/jobs.php" method="get" role="search" aria-label="Search jobs">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <input type="text" name="q" placeholder="Search jobs, skills..." aria-label="Search jobs">
                </form>

                <!-- Messages -->
                <a href="<?php echo $base_path; ?>/api/chat.php" class="action-btn" title="Messages" aria-label="Messages">
                    <i class="fas fa-comment-dots"></i>
                    <?php if (function_exists('getUnreadCount') && getUnreadCount($user_id) > 0): ?>
                        <span class="badge"><?php echo getUnreadCount($user_id); ?></span>
                    <?php endif; ?>
                </a>

                <!-- Notifications -->
                <a href="<?php echo $base_path; ?>/notifications.php" class="action-btn" title="Notifications" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <?php if (function_exists('getNotificationCount') && getNotificationCount($user_id) > 0): ?>
                        <span class="badge"><?php echo getNotificationCount($user_id); ?></span>
                    <?php endif; ?>
                </a>

                <!-- User Dropdown -->
                <div class="user-dropdown">
                    <button class="user-btn" onclick="toggleUserMenu()" aria-haspopup="true" aria-expanded="false" aria-label="Account menu">
                        <span class="avatar"><?php echo isset($user_name[0]) ? strtoupper($user_name[0]) : 'U'; ?></span>
                        <span><?php echo htmlspecialchars($user_name); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" id="userMenu">
                        <?php if ($user_role === 'employer'): ?>
                            <a href="<?php echo $base_path; ?>/employer/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <a href="<?php echo $base_path; ?>/employer/my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a>
                            <a href="<?php echo $base_path; ?>/employer/applications.php"><i class="fas fa-users"></i> Applications</a>
                        <?php else: ?>
                            <a href="<?php echo $base_path; ?>/student/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <a href="<?php echo $base_path; ?>/student/available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a>
                            <a href="<?php echo $base_path; ?>/student/my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a>
                        <?php endif; ?>
                        <hr>
                        <a href="<?php echo $base_path; ?>/profile.php"><i class="fas fa-user"></i> Profile Settings</a>
                        <a href="<?php echo $base_path; ?>/support.php"><i class="fas fa-headset"></i> Support</a>
                        <a href="<?php echo $base_path; ?>/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Search -->
                <form class="header-search" action="<?php echo $base_path; ?>/jobs.php" method="get" role="search" aria-label="Search jobs">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <input type="text" name="q" placeholder="Search jobs, skills..." aria-label="Search jobs">
                </form>

                <a href="<?php echo $base_path; ?>/auth/login.php" class="btn btn-outline">Log In</a>
                <a href="<?php echo $base_path; ?>/auth/register.php" class="btn btn-primary">Get Started</a>
            <?php endif; ?>

            <!-- Mobile Toggle -->
            <button class="mobile-toggle" onclick="toggleMobileMenu()" aria-label="Open menu" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
        </div>

    </div>

    <!-- MOBILE MENU -->
    <div class="mobile-menu" id="mobileMenu">
        <ul>
            <?php if ($user_id && $user_role === 'employer'): ?>
                <li><a href="<?php echo $base_path; ?>/employer/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="<?php echo $base_path; ?>/employer/post_job.php"><i class="fas fa-plus-circle"></i> Post Job</a></li>
                <li><a href="<?php echo $base_path; ?>/employer/my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a></li>
                <li><a href="<?php echo $base_path; ?>/employer/applications.php"><i class="fas fa-users"></i> Applications</a></li>
                <li><a href="<?php echo $base_path; ?>/employer/talent.php"><i class="fas fa-user-graduate"></i> Find Talent</a></li>
                <hr>
                <li><a href="<?php echo $base_path; ?>/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="<?php echo $base_path; ?>/support.php"><i class="fas fa-headset"></i> Support</a></li>
                <li><a href="<?php echo $base_path; ?>/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <?php elseif ($user_id && $user_role === 'student'): ?>
                <li><a href="<?php echo $base_path; ?>/student/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="<?php echo $base_path; ?>/student/available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a></li>
                <li><a href="<?php echo $base_path; ?>/student/my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                <li><a href="<?php echo $base_path; ?>/student/saved_jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a></li>
                <li><a href="<?php echo $base_path; ?>/student/profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                <hr>
                <li><a href="<?php echo $base_path; ?>/support.php"><i class="fas fa-headset"></i> Support</a></li>
                <li><a href="<?php echo $base_path; ?>/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <?php else: ?>
                <li><a href="<?php echo $base_path; ?>/index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?php echo $base_path; ?>/jobs.php"><i class="fas fa-briefcase"></i> Browse Jobs</a></li>
                <li><a href="<?php echo $base_path; ?>/index.php#freelancers"><i class="fas fa-user-tie"></i> Freelancers</a></li>
                <li><a href="<?php echo $base_path; ?>/index.php#categories"><i class="fas fa-th-large"></i> Categories</a></li>
                <li><a href="<?php echo $base_path; ?>/index.php#companies"><i class="fas fa-building"></i> Companies</a></li>
                <li><a href="<?php echo $base_path; ?>/about.php"><i class="fas fa-info-circle"></i> About</a></li>
                <li><a href="<?php echo $base_path; ?>/support.php"><i class="fas fa-headset"></i> Support</a></li>
                <hr>
                <li><a href="<?php echo $base_path; ?>/auth/login.php"><i class="fas fa-sign-in-alt"></i> Log In</a></li>
                <li><a href="<?php echo $base_path; ?>/auth/register.php"><i class="fas fa-user-plus"></i> Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</header>

<!-- ============================================================
     HEADER JAVASCRIPT
     ============================================================ -->
<script>
    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        const btn = document.querySelector('.user-btn');
        if (menu) {
            const open = menu.classList.toggle('open');
            if (btn) btn.setAttribute('aria-expanded', open);
        }
    }

    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const toggle = document.querySelector('.mobile-toggle');
        if (menu) {
            const open = menu.classList.toggle('open');
            if (toggle) {
                toggle.classList.toggle('active');
                toggle.setAttribute('aria-expanded', open);
            }
        }
    }

    // Navbar solid on scroll
    (function initHeaderScroll() {
        const header = document.getElementById('siteHeader');
        if (header) {
            const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 40);
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });
        }
    })();

    // Close dropdowns when clicking outside
    document.addEventListener('click', function (e) {
        const userMenu = document.getElementById('userMenu');
        const userBtn = document.querySelector('.user-btn');
        if (userMenu && userBtn && !userBtn.contains(e.target) && !userMenu.contains(e.target)) {
            userMenu.classList.remove('open');
            userBtn.setAttribute('aria-expanded', 'false');
        }
    });

    // Close mobile menu on ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const mobileMenu = document.getElementById('mobileMenu');
            const toggle = document.querySelector('.mobile-toggle');
            if (mobileMenu) {
                mobileMenu.classList.remove('open');
                if (toggle) { toggle.classList.remove('active'); toggle.setAttribute('aria-expanded', 'false'); }
            }
            const userMenu = document.getElementById('userMenu');
            const userBtn = document.querySelector('.user-btn');
            if (userMenu) { userMenu.classList.remove('open'); if (userBtn) userBtn.setAttribute('aria-expanded', 'false'); }
        }
    });
</script>

<!-- MAIN CONTENT STARTS HERE -->
<main>
<!-- Admin Sidebar -->
<aside class="admin-sidebar">
    <div class="admin-sidebar-brand">
        <a href="dashboard.php">
            <span class="brand-icon">🔐</span>
            <span class="brand-text">Admin<span>Panel</span></span>
        </a>
    </div>
    
    <nav class="admin-nav">
        <ul>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                <a href="users.php"><i class="fas fa-users"></i> <span>Users</span></a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'jobs.php' ? 'active' : ''; ?>">
                <a href="jobs.php"><i class="fas fa-briefcase"></i> <span>Jobs</span></a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'applications.php' ? 'active' : ''; ?>">
                <a href="applications.php"><i class="fas fa-file-alt"></i> <span>Applications</span></a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active' : ''; ?>">
                <a href="payments.php"><i class="fas fa-credit-card"></i> <span>Payments</span></a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="/SkillSeek/index.php" target="_blank"><i class="fas fa-external-link-alt"></i> <span>View Site</span></a>
            </li>
            <li>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
            </li>
        </ul>
    </nav>
    
    <div class="admin-sidebar-footer">
        <div class="admin-user">
            <div class="admin-user-avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="admin-user-info">
                <span class="admin-user-name"><?php echo $_SESSION['admin_full_name'] ?? 'Admin'; ?></span>
                <span class="admin-user-role"><?php echo $_SESSION['admin_role'] ?? 'Administrator'; ?></span>
            </div>
        </div>
    </div>
</aside>
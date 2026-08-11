<div class="admin-sidebar">
    <div class="admin-sidebar-header">
        <h2>🔐 Admin</h2>
        <p><?php echo $_SESSION['full_name']; ?></p>
    </div>
    <nav class="admin-sidebar-nav">
        <ul>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                <a href="users.php"><i class="fas fa-users"></i> Users</a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'jobs.php' ? 'active' : ''; ?>">
                <a href="jobs.php"><i class="fas fa-briefcase"></i> Jobs</a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'applications.php' ? 'active' : ''; ?>">
                <a href="applications.php"><i class="fas fa-file-alt"></i> Applications</a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active' : ''; ?>">
                <a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            </li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
</div>
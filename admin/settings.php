<?php
// ============================================
// SkillSeek - Admin Settings
// ============================================

require_once '../config/database.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

$page_title = 'Settings - Admin';
include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<div class="admin-main">
    <div class="admin-header">
        <h1>Settings</h1>
        <p>Manage platform settings</p>
    </div>

    <div class="admin-section">
        <h3>Platform Settings</h3>
        <div style="background: #F8FAFC; padding: 16px; border-radius: 8px; margin-bottom: 12px;">
            <p><strong>Site Name:</strong> SkillSeek</p>
            <p><strong>Version:</strong> 1.0</p>
            <p><strong>Total Users:</strong> <?php 
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
                echo $stmt->fetch()['total']; 
            ?></p>
            <p><strong>Total Jobs:</strong> <?php 
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM jobs");
                echo $stmt->fetch()['total']; 
            ?></p>
        </div>
        
        <div style="background: #FEF3C7; padding: 16px; border-radius: 8px; border-left: 4px solid #F59E0B;">
            <strong>🔧 Admin Features Coming Soon:</strong>
            <ul style="margin-top: 8px; color: #92400E;">
                <li>Site name and branding settings</li>
                <li>Email configuration</li>
                <li>Payment settings</li>
                <li>User management permissions</li>
            </ul>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
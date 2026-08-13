<?php
require_once 'includes/functions.php';
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Get all users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

$page_title = 'Users';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<main class="admin-main">
    <div class="admin-page-header">
        <h1>Users</h1>
        <p>Manage all platform users (<?php echo count($users); ?> total)</p>
    </div>
    <div class="admin-section">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><span class="role-badge <?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                    <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                    <td>
                        <?php if ($user['is_active']): ?>
                            <span class="status-badge active">Active</span>
                        <?php else: ?>
                            <span class="status-badge inactive">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
<?php
require_once '../config/database.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = 'User deleted successfully.';
    } elseif ($action === 'activate') {
        $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = 'User activated successfully.';
    } elseif ($action === 'deactivate') {
        $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = 'User deactivated successfully.';
    }
    header('Location: users.php');
    exit();
}

$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';

$sql = "SELECT * FROM users WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (full_name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($role_filter)) {
    $sql .= " AND role = ?";
    $params[] = $role_filter;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

$page_title = 'Users - Admin';
include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<div class="admin-main">
    <div class="admin-header">
        <h1>Users</h1>
        <p>Manage all platform users</p>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <div class="filter-bar" style="margin-bottom: 20px;">
        <form method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            <input type="text" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>" class="form-control" style="width: 200px;">
            <select name="role" class="form-control" style="width: 150px;">
                <option value="">All Roles</option>
                <option value="employer" <?php echo $role_filter === 'employer' ? 'selected' : ''; ?>>Employer</option>
                <option value="student" <?php echo $role_filter === 'student' ? 'selected' : ''; ?>>Student</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="users.php" class="btn btn-secondary">Clear</a>
        </form>
    </div>

    <div class="admin-section">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><span class="role-badge <?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                    <td>
                        <?php if ($user['is_active']): ?>
                            <span class="status-badge available">Active</span>
                        <?php else: ?>
                            <span class="status-badge unavailable">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                            <?php if ($user['is_active']): ?>
                                <a href="?action=deactivate&id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm" onclick="return confirm('Deactivate this user?')">Deactivate</a>
                            <?php else: ?>
                                <a href="?action=activate&id=<?php echo $user['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Activate this user?')">Activate</a>
                            <?php endif; ?>
                            <a href="?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?')">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
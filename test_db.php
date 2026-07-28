<?php
// test_db.php - Test database connection
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; background: #f5f7fa; }
        .success { background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; border: 1px solid #c3e6cb; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 8px; margin: 15px 0; border: 1px solid #bee5eb; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th { background: #e9ecef; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #dee2e6; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-employer { background: #dbeafe; color: #1e40af; }
        .badge-student { background: #d1fae5; color: #065f46; }
        h2 { color: #1e293b; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="success">
        <h2>✅ Database Connection Successful!</h2>
        <p>Connected to database: <strong><?php echo $dbname; ?></strong></p>
        <p>Host: <strong><?php echo $host; ?></strong></p>
    </div>

    <?php
    try {
        // Get total users
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
        $total_users = $stmt->fetch();
        
        // Get total jobs
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM jobs");
        $total_jobs = $stmt->fetch();
        
        // Get total categories
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
        $total_categories = $stmt->fetch();
    ?>
    
    <div class="info">
        <h3>📊 Database Statistics</h3>
        <ul>
            <li><strong>Total Users:</strong> <?php echo $total_users['total']; ?></li>
            <li><strong>Total Jobs:</strong> <?php echo $total_jobs['total']; ?></li>
            <li><strong>Total Categories:</strong> <?php echo $total_categories['total']; ?></li>
        </ul>
    </div>
    
    <h2>👥 Users in Database</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Full Name</th>
            <th>Role</th>
            <th>Phone</th>
        </tr>
        <?php
        $stmt = $pdo->query("SELECT id, email, full_name, role, phone FROM users");
        while ($row = $stmt->fetch()) {
            $badge_class = $row['role'] === 'employer' ? 'badge-employer' : 'badge-student';
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td><strong>{$row['full_name']}</strong></td>";
            echo "<td><span class='badge $badge_class'>{$row['role']}</span></td>";
            echo "<td>{$row['phone']}</td>";
            echo "</tr>";
        }
        ?>
    </table>
    
    <h2>📂 Categories</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
        </tr>
        <?php
        $stmt = $pdo->query("SELECT * FROM categories LIMIT 5");
        while ($row = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td><strong>{$row['name']}</strong></td>";
            echo "<td>{$row['description']}</td>";
            echo "</tr>";
        }
        ?>
    </table>
    
    <div class="info" style="margin-top: 20px;">
        <h3>✅ All Systems Ready!</h3>
        <p>You can now proceed to create the next file.</p>
        <p><strong>Test Login Credentials:</strong></p>
        <ul>
            <li><strong>Employer:</strong> techcorp@email.com / password123</li>
            <li><strong>Student:</strong> student@email.com / password123</li>
        </ul>
    </div>
    
    <?php
    } catch(PDOException $e) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px;'>";
        echo "❌ Error: " . $e->getMessage();
        echo "</div>";
    }
    ?>
</body>
</html>
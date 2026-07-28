<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Test basic PHP
echo "1. PHP is working!<br>";

// Try to include the config file
echo "2. Attempting to load config...<br>";

if (file_exists('config/database.php')) {
    echo "3. Config file found!<br>";
    require_once 'config/database.php';
    echo "4. Config loaded successfully!<br>";
} else {
    die("❌ Config file not found at: " . realpath('config/database.php'));
}

// Test database connection
echo "5. Testing database connection...<br>";

try {
    $stmt = $pdo->query("SELECT 1");
    echo "6. Database query successful!<br>";
    
    // Get some data
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total = $stmt->fetch();
    echo "7. Total users: " . $total['total'] . "<br>";
    
} catch(PDOException $e) {
    die("❌ Database error: " . $e->getMessage());
}

echo "✅ All tests passed!";
?>
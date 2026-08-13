<?php
// ============================================
// Admin Functions
// ============================================

// Database connection
require_once __DIR__ . '/../../config/database.php';

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_role']);
}

// Get admin data
function getAdminData($admin_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
    $stmt->execute([$admin_id]);
    return $stmt->fetch();
}

// Redirect
function redirectAdmin($url) {
    header("Location: $url");
    exit();
}

// Sanitize input
function sanitizeAdmin($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Get platform stats
function getPlatformStats() {
    global $pdo;
    
    $stats = [];
    
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $stats['total_users'] = $stmt->fetch()['total'];
    
    // Total jobs
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM jobs");
    $stats['total_jobs'] = $stmt->fetch()['total'];
    
    // Total applications
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM applications");
    $stats['total_applications'] = $stmt->fetch()['total'];
    
    // Total payments
    $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(amount) as total_amount FROM payments WHERE status = 'completed'");
    $payments = $stmt->fetch();
    $stats['total_payments'] = $payments['total'] ?? 0;
    $stats['total_amount'] = $payments['total_amount'] ?? 0;
    
    return $stats;
}

// Get recent users
function getRecentUsers($limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT " . intval($limit));
    $stmt->execute();
    return $stmt->fetchAll();
}
// Get recent jobs
function getRecentJobs($limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT j.*, u.full_name as employer_name 
        FROM jobs j 
        JOIN users u ON j.employer_id = u.id 
        ORDER BY j.created_at DESC LIMIT " . intval($limit)
    );
    $stmt->execute();
    return $stmt->fetchAll();
}
// Get recent applications
function getRecentApplications($limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT a.*, j.title as job_title, u.full_name as student_name
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        JOIN users u ON a.student_id = u.id
        ORDER BY a.applied_at DESC LIMIT " . intval($limit)
    );
    $stmt->execute();
    return $stmt->fetchAll();
}
?>
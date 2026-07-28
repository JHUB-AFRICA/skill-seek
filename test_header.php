<?php
// test_header.php - Test the header
$page_title = 'Test Header - SkillSeek';
$base_path = '';
include 'includes/header.php';
?>

<div class="container" style="padding: 40px; min-height: 60vh;">
    <h1>🧪 Header Test Page</h1>
    <p>If you can see this page with a styled header, the header is working!</p>
    
    <div class="card" style="max-width: 500px; margin-top: 30px;">
        <div class="card-header">
            <h3 class="card-title">Test Card</h3>
        </div>
        <div class="card-body">
            <p>This is a test card inside the page content.</p>
            <p><strong>User Status:</strong> 
                <?php echo isset($_SESSION['user_id']) ? 'Logged In ✅' : 'Guest 👤'; ?>
            </p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <p><strong>User:</strong> <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['role']); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php
$page_title = 'Test Header - SkillSeek';
$base_path = '';
include 'includes/header.php';
?>

<div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <h1 style="font-size: 2rem; font-weight: 700; color: #0F172A;">🧪 Header Test</h1>
    <p style="color: #64748B; margin-top: 8px;">If this looks professional, the header is fixed!</p>
    
    <div style="background: #FFFFFF; border-radius: 12px; padding: 24px; margin-top: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <h3 style="font-weight: 600;">User Status</h3>
        <p><strong>Logged In:</strong> <?php echo isset($_SESSION['user_id']) ? '✅ Yes' : '❌ No'; ?></p>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['role']); ?></p>
        <?php endif; ?>
    </div>
</div>

</main>
</body>
</html>
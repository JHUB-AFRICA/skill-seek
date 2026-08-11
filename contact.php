<?php
<<<<<<< HEAD
$page_title = 'Contact Us - SkillSeek';
include 'includes/header.php';
?>

<div class="container" style="padding: 40px 0; min-height: 60vh;">
    <h1>Contact Us</h1>
    <p>Email: support@skillseek.com</p>
    <p>Phone: +254 700 000 000</p>
</div>

=======
// ============================================
// SkillSeek - Contact Us
// ============================================
$page_title = 'Contact Us - SkillSeek';

// Contact form handler
$contact_error = '';
$contact_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';
    $name = sanitize($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $contact_error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $contact_error = 'Please enter a valid email address.';
    } else {
        $contact_success = 'Thank you! Your message has been received. We\'ll reply within 24 hours.';
    }
}

include 'includes/header.php';
?>
<section class="section" style="padding-top:60px;">
    <div class="container-wide" style="max-width:760px;">
        <div class="section-head reveal">
            <span class="section-eyebrow"><i class="fas fa-envelope"></i> Contact</span>
            <h2>Get in touch</h2>
            <p>We would love to hear from you. Reach out any time.</p>
        </div>
        <div class="reveal" style="background:#fff;border:1px solid var(--border);border-radius:20px;padding:36px;box-shadow:var(--shadow);">
            <?php if ($contact_success): ?>
                <div class="alert alert-success" style="display:flex;align-items:center;gap:10px;margin-bottom:20px;"><i class="fas fa-check-circle"></i> <?php echo $contact_success; ?></div>
            <?php elseif ($contact_error): ?>
                <div class="alert alert-error" style="display:flex;align-items:center;gap:10px;margin-bottom:20px;"><i class="fas fa-exclamation-circle"></i> <?php echo $contact_error; ?></div>
            <?php endif; ?>
            <form method="post" action="contact.php" data-validate>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
                    <div class="form-group"><label for="c-name">Full Name</label><input type="text" id="c-name" name="name" class="form-control" required></div>
                    <div class="form-group"><label for="c-email">Email</label><input type="email" id="c-email" name="email" class="form-control" required></div>
                </div>
                <div class="form-group"><label for="c-subject">Subject</label><input type="text" id="c-subject" name="subject" class="form-control" required></div>
                <div class="form-group"><label for="c-message">Message</label><textarea id="c-message" name="message" class="form-control" rows="5" required></textarea></div>
                <button type="submit" class="btn btn-primary btn-lg btn-ripple"><i class="fas fa-paper-plane"></i> Send Message</button>
            </form>
            <div style="display:flex;gap:22px;flex-wrap:wrap;margin-top:28px;padding-top:24px;border-top:1px solid var(--border);color:var(--text-muted);font-size:14px;">
                <span><i class="fas fa-envelope-open-text" style="color:var(--primary);"></i> support@skillseek.com</span>
                <span><i class="fas fa-phone" style="color:var(--primary);"></i> +254 700 000 000</span>
                <span><i class="fas fa-location-dot" style="color:var(--primary);"></i> Nairobi, Kenya</span>
            </div>
        </div>
    </div>
</section>
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
<?php include 'includes/footer.php'; ?>
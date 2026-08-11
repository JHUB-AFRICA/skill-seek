<?php
<<<<<<< HEAD
$page_title = 'Help Center - SkillSeek';
include 'includes/header.php';
?>

<div class="container" style="padding: 40px 0; min-height: 60vh;">
    <h1>Help Center</h1>
    <p>How can we help you?</p>
    <p>Coming soon...</p>
</div>

=======
// ============================================
// SkillSeek - Support
// ============================================
$page_title = 'Support Center - SkillSeek';

// Support form handler
$support_error = '';
$support_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';
    $name = sanitize($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $support_error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $support_error = 'Please enter a valid email address.';
    } else {
        $support_success = 'Thank you! Your ticket has been received. Our support team will respond within 24 hours.';
    }
}

include 'includes/header.php';
?>
<section class="section" style="padding-top:60px;">
    <div class="container-wide">
        <div class="section-head reveal">
            <span class="section-eyebrow"><i class="fas fa-headset"></i> Support Center</span>
            <h2>How can we help?</h2>
            <p>Find answers in our FAQ or reach out to our friendly support team.</p>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:start;">
            <!-- Contact card -->
            <div class="reveal">
                <div style="background:#fff;border:1px solid var(--border);border-radius:20px;padding:32px;box-shadow:var(--shadow);">
                    <h3 style="font-size:22px;font-weight:800;color:var(--text);margin-bottom:6px;"><i class="fas fa-envelope" style="color:var(--primary);margin-right:8px;"></i>Contact Us</h3>
                    <p style="color:var(--text-muted);margin-bottom:22px;">Send us a message and we'll reply within 24 hours.</p>
                    <?php if ($support_success): ?>
                        <div class="alert alert-success" style="display:flex;align-items:center;gap:10px;margin-bottom:20px;"><i class="fas fa-check-circle"></i> <?php echo $support_success; ?></div>
                    <?php elseif ($support_error): ?>
                        <div class="alert alert-error" style="display:flex;align-items:center;gap:10px;margin-bottom:20px;"><i class="fas fa-exclamation-circle"></i> <?php echo $support_error; ?></div>
                    <?php endif; ?>
                    <form method="post" action="support.php" data-validate>
                        <div class="form-group">
                            <label for="sup-name">Your Name</label>
                            <input type="text" id="sup-name" name="name" class="form-control" placeholder="John Doe" required>
                        </div>
                        <div class="form-group">
                            <label for="sup-email">Email Address</label>
                            <input type="email" id="sup-email" name="email" class="form-control" placeholder="you@example.com" required>
                        </div>
                        <div class="form-group">
                            <label for="sup-subject">Subject</label>
                            <input type="text" id="sup-subject" name="subject" class="form-control" placeholder="How can we help?" required>
                        </div>
                        <div class="form-group">
                            <label for="sup-message">Message</label>
                            <textarea id="sup-message" name="message" class="form-control" rows="4" placeholder="Tell us more..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-ripple"><i class="fas fa-paper-plane"></i> Send Message</button>
                    </form>
                    <p style="font-size:13px;color:var(--text-light);margin-top:14px;">
                        <i class="fas fa-envelope-open-text"></i> support@skillseek.com &nbsp;|&nbsp; <i class="fas fa-phone"></i> +254 700 000 000
                    </p>
                </div>
            </div>

            <!-- FAQ list -->
            <div class="reveal reveal-delay-1">
                <div style="background:#fff;border:1px solid var(--border);border-radius:20px;padding:32px;box-shadow:var(--shadow);">
                    <h3 style="font-size:22px;font-weight:800;color:var(--text);margin-bottom:20px;"><i class="fas fa-circle-question" style="color:var(--accent);margin-right:8px;"></i>Frequently Asked Questions</h3>
                    <?php $faqs = [
                        ['How do I create an account?', 'Click "Get Started", choose your role (Student or Employer), fill in your details, and you are ready to go.'],
                        ['Is SkillSeek free to use?', 'Yes. Creating a profile, browsing jobs, and applying are completely free.'],
                        ['How do students get paid?', 'Payments are agreed between the student and employer based on the project budget. Secure payment options are coming soon.'],
                        ['Can I hire students for short projects?', 'Absolutely. Post a job with your budget and timeline, review applications, and pick the best fit.'],
                        ['How do I reset my password?', 'Use the "Forgot password" link on the login page and follow the instructions sent to your email.'],
                    ]; ?>
                    <div style="display:grid;gap:12px;">
                        <?php foreach ($faqs as $i => $faq): ?>
                            <details style="border:1px solid var(--border);border-radius:12px;padding:16px 18px;transition:var(--transition);">
                                <summary style="cursor:pointer;font-weight:600;color:var(--text);font-size:15px;list-style:none;display:flex;justify-content:space-between;align-items:center;">
                                    <?php echo $faq[0]; ?><i class="fas fa-chevron-down" style="color:var(--primary);font-size:12px;transition:var(--transition);"></i>
                                </summary>
                                <p style="margin:12px 0 0;color:var(--text-muted);font-size:14.5px;line-height:1.7;"><?php echo $faq[1]; ?></p>
                            </details>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.querySelectorAll('details').forEach(function(d) {
    d.querySelector('summary').addEventListener('click', function () {
        const icon = d.querySelector('i');
        if (icon) icon.style.transform = d.open ? 'rotate(0deg)' : 'rotate(180deg)';
        setTimeout(() => { if (icon) icon.style.transform = d.open ? 'rotate(180deg)' : 'rotate(0deg)'; }, 10);
    });
});
</script>
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
<?php include 'includes/footer.php'; ?>
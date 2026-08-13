<?php
// ============================================
// SkillSeek - FAQ
// ============================================
$page_title = 'FAQ - SkillSeek';
include 'includes/header.php';
?>
<section class="section" style="padding-top:60px;">
    <div class="container-wide" style="max-width:820px;">
        <div class="section-head reveal">
            <span class="section-eyebrow"><i class="fas fa-circle-question"></i> FAQ</span>
            <h2>Frequently Asked Questions</h2>
            <p>Quick answers to common questions about SkillSeek.</p>
        </div>
        <div style="display:grid;gap:14px;">
            <?php $faqs = [
                ['What is SkillSeek?', 'SkillSeek is a freelance marketplace that connects students and freelancers with employers for projects, gigs, and long-term work.'],
                ['How do I get started as a student?', 'Create a free account, complete your profile with your skills and portfolio, then browse and apply to jobs that match you.'],
                ['How do I hire talent as an employer?', 'Post a job with a clear description and budget. Review applications, shortlist candidates, and accept the best fit for your project.'],
                ['Is there a fee to join?', 'No. Registration, browsing, and applying are completely free for everyone.'],
                ['How are payments handled?', 'Payment terms are agreed between student and employer per project. Secure in-app payment features are being rolled out.'],
                ['Can I update my profile later?', 'Yes. Log in and visit "My Profile" to update your skills, education, experience, portfolio, and contact details any time.'],
                ['What if I need help?', 'Visit the Support page to contact our team, or check the FAQ for quick answers.'],
            ]; ?>
            <?php foreach ($faqs as $faq): ?>
                <details style="background:#fff;border:1px solid var(--border);border-radius:14px;padding:18px 22px;box-shadow:var(--shadow-sm);">
                    <summary style="cursor:pointer;font-weight:600;color:var(--text);font-size:16px;list-style:none;display:flex;justify-content:space-between;align-items:center;">
                        <?php echo $faq[0]; ?><i class="fas fa-chevron-down" style="color:var(--primary);font-size:12px;transition:var(--transition);"></i>
                    </summary>
                    <p style="margin:14px 0 0;color:var(--text-muted);line-height:1.7;"><?php echo $faq[1]; ?></p>
                </details>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:36px;">
            <p style="color:var(--text-muted);margin-bottom:14px;">Still have questions?</p>
            <a href="support.php" class="btn btn-primary btn-ripple"><i class="fas fa-headset"></i> Contact Support</a>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
<?php
// ============================================
// SkillSeek - About
// ============================================
require_once 'config/database.php';

$stmt = $pdo->query("SELECT COUNT(*) as t FROM users WHERE role='student'");
$students = $stmt->fetch()['t'];
$stmt = $pdo->query("SELECT COUNT(*) as t FROM users WHERE role='employer'");
$employers = $stmt->fetch()['t'];
$stmt = $pdo->query("SELECT COUNT(*) as t FROM jobs WHERE status='open'");
$jobs = $stmt->fetch()['t'];

$page_title = 'About Us - SkillSeek';
include 'includes/header.php';
?>
<section class="section" style="padding-top:60px;">
    <div class="container-wide">
        <div class="section-head reveal">
            <span class="section-eyebrow"><i class="fas fa-rocket"></i> About SkillSeek</span>
            <h2>Building the future of work for students</h2>
            <p>We connect talented students and freelancers with employers who need great work done — quickly and securely.</p>
        </div>

        <div class="reveal" style="max-width:820px;margin:0 auto 60px;">
            <div style="background:#fff;border:1px solid var(--border);border-radius:20px;padding:36px;box-shadow:var(--shadow);">
                <h3 style="font-size:24px;font-weight:800;color:var(--text);margin-bottom:16px;">Our Mission</h3>
                <p style="font-size:17px;line-height:1.8;color:var(--text-muted);margin-bottom:20px;">
                    SkillSeek was built to remove the gap between classroom learning and real-world opportunity.
                    Whether you are a student building your first portfolio or an employer hunting for fresh talent,
                    our marketplace makes it easy to connect, collaborate, and grow.
                </p>
                <p style="font-size:17px;line-height:1.8;color:var(--text-muted);">
                    We believe in fair pay, transparent communication, and work that helps everyone level up. That is why
                    every job posted is reviewed, applications are simple, and payments are handled securely.
                </p>
            </div>
        </div>

        <div class="hero-stats reveal" style="margin-bottom:64px;box-shadow:var(--shadow);">
            <div class="hero-stat">
                <div class="stat-icon hi-blue"><i class="fas fa-briefcase"></i></div>
                <span class="stat-number"><?php echo number_format($jobs); ?><span class="kplus">+</span></span>
                <span class="stat-label">Open Jobs</span>
            </div>
            <div class="hero-stat">
                <div class="stat-icon hi-teal"><i class="fas fa-user-graduate"></i></div>
                <span class="stat-number"><?php echo number_format($students); ?><span class="kplus">+</span></span>
                <span class="stat-label">Students</span>
            </div>
            <div class="hero-stat">
                <div class="stat-icon hi-amber"><i class="fas fa-building"></i></div>
                <span class="stat-number"><?php echo number_format($employers); ?><span class="kplus">+</span></span>
                <span class="stat-label">Employers</span>
            </div>
        </div>

        <div class="features-grid">
            <article class="feature-card reveal">
                <span class="step-num">01</span>
                <div class="feat-icon"><i class="fas fa-eye"></i></div>
                <h3>Transparency</h3>
                <p>Clear job details, budgets, and honest reviews so you can decide with confidence.</p>
            </article>
            <article class="feature-card reveal reveal-delay-1">
                <span class="step-num">02</span>
                <div class="feat-icon"><i class="fas fa-shield-halved"></i></div>
                <h3>Security</h3>
                <p>Protected accounts, secure payments, and safe communication at every step.</p>
            </article>
            <article class="feature-card reveal reveal-delay-2">
                <span class="step-num">03</span>
                <div class="feat-icon"><i class="fas fa-globe"></i></div>
                <h3>Community</h3>
                <p>A growing network of ambitious students and forward-thinking employers across Kenya.</p>
            </article>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
<?php
// ============================================
// SkillSeek - Landing Page
// File: index.php
// Description: Main landing page for visitors
// ============================================

// Include configuration
require_once 'config/database.php';

// Get some stats for the homepage
$stmt = $pdo->query("SELECT COUNT(*) as total FROM jobs WHERE status = 'open'");
$open_jobs = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'student'");
$total_students = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'employer'");
$total_employers = $stmt->fetch()['total'];

// Get featured jobs (latest 6 open jobs)
$stmt = $pdo->prepare("
    SELECT j.*, u.full_name as employer_name, c.name as category_name
    FROM jobs j
    JOIN users u ON j.employer_id = u.id
    LEFT JOIN categories c ON j.category_id = c.id
    WHERE j.status = 'open'
    ORDER BY j.created_at DESC
    LIMIT 6
");
$stmt->execute();
$featured_jobs = $stmt->fetchAll();

// Set page title
$page_title = 'SkillSeek - Find Freelance Opportunities';

// Include header
include 'includes/header.php';
?>

<!-- ============================================================
     HERO SECTION
     ============================================================ -->
<section class="hero-section">
    <div class="hero-container">
        <div class="hero-content">
            <h1 class="hero-title">
                Find the Perfect <br>
                <span class="hero-highlight">Freelance Opportunity</span>
            </h1>
            <p class="hero-subtitle">
                Connect with talented students and employers. Whether you're looking for work or hiring, 
                SkillSeek makes it easy to find the perfect match.
            </p>
            <div class="hero-buttons">
                <?php if (isLoggedIn()): ?>
                    <?php if (getUserRole() === 'employer'): ?>
                        <a href="employer/dashboard.php" class="btn btn-primary btn-hero">
                            <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                        </a>
                    <?php else: ?>
                        <a href="student/dashboard.php" class="btn btn-primary btn-hero">
                            <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="auth/register.php" class="btn btn-primary btn-hero">
                        <i class="fas fa-user-plus"></i> Get Started
                    </a>
                    <a href="auth/login.php" class="btn btn-outline btn-hero">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <span class="stat-number"><?php echo $open_jobs; ?></span>
                <span class="stat-label">Open Jobs</span>
            </div>
            <div class="hero-stat">
                <span class="stat-number"><?php echo $total_students; ?></span>
                <span class="stat-label">Students</span>
            </div>
            <div class="hero-stat">
                <span class="stat-number"><?php echo $total_employers; ?></span>
                <span class="stat-label">Employers</span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     HOW IT WORKS
     ============================================================ -->
<section class="how-it-works">
    <div class="container">
        <div class="section-header text-center">
            <h2>How It Works</h2>
            <p>Three simple steps to get started</p>
        </div>
        
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3>Create an Account</h3>
                <p>Sign up as a student or employer. It's free and takes just a few minutes.</p>
            </div>
            
            <div class="step-card">
                <div class="step-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Find or Post Jobs</h3>
                <p>Students can browse available jobs. Employers can post new opportunities.</p>
            </div>
            
            <div class="step-card">
                <div class="step-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>Connect &amp; Work</h3>
                <p>Apply for jobs, review applications, and start working together.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     FEATURED JOBS
     ============================================================ -->
<section class="featured-jobs">
    <div class="container">
        <div class="section-header">
            <div>
                <h2>Featured Jobs</h2>
                <p>Latest opportunities from employers</p>
            </div>
            <a href="jobs.php" class="view-all">View All Jobs →</a>
        </div>
        
        <?php if (empty($featured_jobs)): ?>
            <div class="empty-state">
                <i class="fas fa-briefcase"></i>
                <p>No jobs available at the moment. Check back later!</p>
            </div>
        <?php else: ?>
            <div class="jobs-grid">
                <?php foreach ($featured_jobs as $job): ?>
                    <div class="job-card-featured">
                        <div class="job-card-header">
                            <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                            <span class="status-badge open">Open</span>
                        </div>
                        <div class="job-card-body">
                            <div class="job-employer">
                                <i class="fas fa-building"></i>
                                <?php echo htmlspecialchars($job['employer_name']); ?>
                            </div>
                            <p><?php echo htmlspecialchars(substr($job['description'], 0, 120)); ?>...</p>
                        </div>
                        <div class="job-card-footer">
                            <div class="job-meta">
                                <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($job['category_name'] ?? 'General'); ?></span>
                                <span><i class="fas fa-money-bill"></i> KSh <?php echo number_format($job['budget_min'] ?? 0, 2); ?></span>
                                <span><i class="fas fa-map-marker-alt"></i> 
                                    <?php echo $job['is_remote'] ? '🌐 Remote' : htmlspecialchars($job['location'] ?? 'N/A'); ?>
                                </span>
                            </div>
                            <?php if (isLoggedIn() && getUserRole() === 'student'): ?>
                                <a href="student/apply.php?id=<?php echo $job['id']; ?>" class="btn btn-primary btn-sm btn-block">
                                    Apply Now
                                </a>
                            <?php elseif (isLoggedIn() && getUserRole() === 'employer'): ?>
                                <a href="employer/job_details.php?id=<?php echo $job['id']; ?>" class="btn btn-secondary btn-sm btn-block">
                                    View Details
                                </a>
                            <?php else: ?>
                                <a href="auth/login.php" class="btn btn-secondary btn-sm btn-block">
                                    Login to Apply
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================================
     CTA SECTION
     ============================================================ -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of students and employers already using SkillSeek</p>
            <div class="cta-buttons">
                <?php if (!isLoggedIn()): ?>
                    <a href="auth/register.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus"></i> Create Free Account
                    </a>
                <?php else: ?>
                    <?php if (getUserRole() === 'employer'): ?>
                        <a href="employer/post_job.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus-circle"></i> Post a Job
                        </a>
                        <a href="employer/dashboard.php" class="btn btn-secondary btn-lg">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    <?php else: ?>
                        <a href="student/available_jobs.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-search"></i> Find Jobs
                        </a>
                        <a href="student/dashboard.php" class="btn btn-secondary btn-lg">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
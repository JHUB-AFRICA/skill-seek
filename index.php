<?php
// ============================================
// SkillSeek - Landing Page (Premium Redesign)
// ============================================

require_once 'config/database.php';

// Homepage statistics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM jobs WHERE status = 'open'");
$open_jobs = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'student'");
$total_students = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'employer'");
$total_employers = $stmt->fetch()['total'];

// Featured jobs (latest 6 open jobs)
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

// Popular categories with live job counts (top 8)
$stmt = $pdo->query("
    SELECT c.id, c.name, c.icon, c.color,
           COUNT(j.id) as job_count
    FROM categories c
    LEFT JOIN jobs j ON j.category_id = c.id AND j.status = 'open'
    GROUP BY c.id
    ORDER BY job_count DESC, c.name ASC
    LIMIT 8
");
$categories = $stmt->fetchAll();
if (empty($categories)) {
    $categories = [
        ['name' => 'Web Development',  'icon' => 'fa-code'],
        ['name' => 'Graphic Design',   'icon' => 'fa-palette'],
        ['name' => 'Writing',          'icon' => 'fa-pen-nib'],
        ['name' => 'Data Science',     'icon' => 'fa-chart-line'],
        ['name' => 'Mobile Development','icon' => 'fa-mobile-screen'],
        ['name' => 'Marketing',        'icon' => 'fa-bullhorn'],
        ['name' => 'Video Editing',    'icon' => 'fa-video'],
        ['name' => 'Cyber Security',   'icon' => 'fa-shield-halved'],
    ];
}

// Top freelancers (available, best-rated students)
$freelancers = [];
try {
    $flStmt = $pdo->query("
        SELECT u.id, u.full_name, u.location, u.profile_pic,
               sp.skills, sp.hourly_rate, sp.rating, sp.total_jobs_completed, sp.experience
        FROM student_profiles sp
        JOIN users u ON u.id = sp.user_id
        WHERE sp.is_available = TRUE
        ORDER BY sp.rating DESC
        LIMIT 6
    ");
    $freelancers = $flStmt->fetchAll();
} catch (Exception $e) { $freelancers = []; }

if (empty($freelancers)) {
    $freelancers = [
        ['full_name' => 'Jane Doe',       'location' => 'Nairobi',    'skills' => 'PHP, Laravel, MySQL, React', 'rating' => '4.9', 'hourly_rate' => '15',   'total_jobs_completed' => 42, 'experience' => 'Full Stack Developer'],
        ['full_name' => 'Kelvin Otieno',  'location' => 'Mombasa',   'skills' => 'UI/UX, Figma, Sketch',        'rating' => '4.8', 'hourly_rate' => '12',   'total_jobs_completed' => 35, 'experience' => 'Product Designer'],
        ['full_name' => 'Amina Yusuf',    'location' => 'Kisumu',    'skills' => 'Python, Pandas, ML',          'rating' => '4.9', 'hourly_rate' => '18',   'total_jobs_completed' => 28, 'experience' => 'Data Scientist'],
        ['full_name' => 'Brian Mwangi',   'location' => 'Nakuru',    'skills' => 'SEO, Copywriting, Content',    'rating' => '4.7', 'hourly_rate' => '10',   'total_jobs_completed' => 51, 'experience' => 'Digital Marketer'],
    ];
}

$page_title = 'SkillSeek - Find Freelance Opportunities That Match Your Skills';

include 'includes/header.php';
?>

<!-- ============================================================
     HERO SECTION
     ============================================================ -->
<section class="home-hero" id="hero">
    <div class="hero-inner">

        <div class="hero-copy">
            <span class="hero-chip reveal"><span class="dot"></span> Trusted freelance marketplace for students &amp; employers</span>
            <h1 class="hero-title reveal">
                Find Freelance Opportunities That <span class="grad">Match Your Skills</span>
            </h1>
            <p class="hero-subtitle reveal">
                Connect talented students, freelancers, and employers on one platform.
                Post jobs, apply in minutes, and grow your career.
            </p>

            <!-- Hero search (Indeed-inspired) -->
            <form class="hero-search reveal" action="jobs.php" method="get" role="search" aria-label="Search jobs by skill, company, or location">
                <div class="hero-search-field">
                    <i class="fas fa-magnifying-glass" aria-hidden="true"></i>
                    <input type="text" name="q" placeholder="Job title, skill, or company" aria-label="Job title, skill, or company">
                </div>
                <div class="hero-search-field">
                    <i class="fas fa-location-dot" aria-hidden="true"></i>
                    <input type="text" name="loc" placeholder="City or Remote" aria-label="City or Remote">
                </div>
                <button type="submit" class="btn btn-primary btn-ripple"><i class="fas fa-search"></i> Search</button>
            </form>

            <div class="hero-buttons reveal">
                <?php if (isLoggedIn()): ?>
                    <?php if (getUserRole() === 'employer'): ?>
                        <a href="employer/dashboard.php" class="btn btn-primary btn-lg btn-ripple"><i class="fas fa-tachometer-alt"></i> Go to Dashboard</a>
                        <a href="employer/post_job.php" class="btn btn-white-ghost btn-lg"><i class="fas fa-plus-circle"></i> Post a Job</a>
                    <?php else: ?>
                        <a href="student/dashboard.php" class="btn btn-primary btn-lg btn-ripple"><i class="fas fa-tachometer-alt"></i> Go to Dashboard</a>
                        <a href="student/available_jobs.php" class="btn btn-white-ghost btn-lg"><i class="fas fa-search"></i> Browse Jobs</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="auth/register.php" class="btn btn-primary btn-lg btn-ripple"><i class="fas fa-user-plus"></i> Get Started</a>
                    <a href="jobs.php" class="btn btn-white-ghost btn-lg"><i class="fas fa-briefcase"></i> Browse Jobs</a>
                    <a href="auth/register.php" class="btn btn-accent btn-lg"><i class="fas fa-plus-circle"></i> Post a Job</a>
                <?php endif; ?>
            </div>
            <div class="hero-trust reveal reveal-delay-2">
                <div class="avatars" aria-hidden="true">
                    <span class="a1">JD</span>
                    <span class="a2">JW</span>
                    <span class="a3">MK</span>
                    <span class="a4">SA</span>
                </div>
                <p><strong>Trusted by 10,000+ freelancers</strong> and 2,000+ employers across Kenya</p>
            </div>
        </div>

        <!-- Hero Visual -->
        <div class="hero-visual reveal reveal-delay-1">
            <div class="hero-card">
                <div class="hero-card-head">
                    <h4>Latest Opportunities</h4>
                    <span class="tag"><i class="fas fa-circle" style="font-size:6px;"></i> Live</span>
                </div>
                <?php if (!empty($featured_jobs)): $heroJob = $featured_jobs[0]; ?>
                    <div class="hero-mock-row">
                        <div class="hero-mock-ico" style="background:linear-gradient(135deg,#2563EB,#3B82F6);"><i class="fas fa-code"></i></div>
                        <div>
                            <h5><?php echo htmlspecialchars($heroJob['title']); ?></h5>
                            <p><?php echo htmlspecialchars($heroJob['employer_name']); ?></p>
                        </div>
                        <span class="pay">KSh <?php echo number_format($heroJob['budget_min'] ?? 0); ?>+</span>
                    </div>
                <?php else: ?>
                    <div class="hero-mock-row">
                        <div class="hero-mock-ico" style="background:linear-gradient(135deg,#2563EB,#3B82F6);"><i class="fas fa-code"></i></div>
                        <div><h5>Full Stack Developer</h5><p>TechCorp Solutions</p></div>
                        <span class="pay">KSh 80K+</span>
                    </div>
                <?php endif; ?>
                <div class="hero-mock-row">
                    <div class="hero-mock-ico" style="background:linear-gradient(135deg,#14B8A6,#2DD4BF);"><i class="fas fa-pen-nib"></i></div>
                    <div><h5>Content Writer</h5><p>BrandMinds Agency</p></div>
                    <span class="pay">KSh 25K+</span>
                </div>
                <div class="hero-mock-row">
                    <div class="hero-mock-ico" style="background:linear-gradient(135deg,#1E293B,#475569);"><i class="fas fa-palette"></i></div>
                    <div><h5>UI/UX Designer</h5><p>PixelForge Studio</p></div>
                    <span class="pay">KSh 60K+</span>
                </div>
            </div>

            <!-- Floating chips -->
            <div class="float-chip fc-1"><i class="fas fa-badge-check"></i><span><b>Hired</b>In 48 hours</span></div>
            <div class="float-chip fc-2"><i class="fas fa-hand-holding-usd"></i><span><b>KSh 2.4M+</b>Paid to talent</span></div>
            <div class="float-chip fc-3"><i class="fas fa-star" style="color:#F59E0B;"></i><span><b>4.9/5</b>User rating</span></div>
        </div>

    </div>

    <!-- Stats bar -->
    <div class="hero-statsbar">
        <div class="hero-stats reveal">
            <div class="hero-stat">
                <div class="stat-icon hi-blue"><i class="fas fa-user-graduate"></i></div>
                <span class="stat-number"><span class="counter" data-target="10000">0</span><span class="kplus">+</span></span>
                <span class="stat-label">Freelancers</span>
            </div>
            <div class="hero-stat">
                <div class="stat-icon hi-teal"><i class="fas fa-briefcase"></i></div>
                <span class="stat-number"><span class="counter" data-target="5000">0</span><span class="kplus">+</span></span>
                <span class="stat-label">Jobs Posted</span>
            </div>
            <div class="hero-stat">
                <div class="stat-icon hi-amber"><i class="fas fa-building"></i></div>
                <span class="stat-number"><span class="counter" data-target="2000">0</span><span class="kplus">+</span></span>
                <span class="stat-label">Employers</span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     TRUSTED COMPANIES
     ============================================================ -->
<section class="companies-strip" id="companies" aria-label="Companies hiring on SkillSeek">
    <div class="container-wide">
        <p class="companies-title reveal">Trusted by leading companies and growing teams</p>
        <div class="companies-marquee reveal" aria-hidden="true">
            <div class="companies-track">
                <?php $companyNames = ['TechCorp', 'BrandMinds', 'PixelForge', 'DataWorks', 'FinServe', 'GreenLabs', 'CloudNine', 'UrbanEdge']; ?>
                <?php for ($r = 0; $r < 2; $r++): ?>
                    <?php foreach ($companyNames as $cn): ?>
                        <span class="company-chip"><i class="fas fa-building"></i> <?php echo $cn; ?></span>
                    <?php endforeach; ?>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     ADVANCED SEARCH (Indeed-inspired)
     ============================================================ -->
<section class="section search-section" id="search">
    <div class="container-wide">
        <div class="section-head reveal">
            <span class="section-eyebrow"><i class="fas fa-magnifying-glass"></i> Find your match</span>
            <h2>Search jobs the smart way</h2>
            <p>Filter by skill, company, location, job type, and more — in seconds.</p>
        </div>

        <form class="search-panel reveal" action="jobs.php" method="get" role="search" aria-label="Advanced job search">
            <div class="search-panel-grid">
                <div class="search-field">
                    <label for="hp-q"><i class="fas fa-magnifying-glass"></i> Skill or company</label>
                    <input type="text" id="hp-q" name="q" placeholder="e.g. React, Web development" aria-label="Skill or company">
                </div>
                <div class="search-field">
                    <label for="hp-loc"><i class="fas fa-location-dot"></i> Location</label>
                    <input type="text" id="hp-loc" name="loc" placeholder="City or Remote" aria-label="Location">
                </div>
                <div class="search-field">
                    <label for="hp-cat"><i class="fas fa-th-large"></i> Category</label>
                    <select id="hp-cat" name="category">
                        <option value="">All categories</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="search-field">
                    <label for="hp-type"><i class="fas fa-list-check"></i> Job type</label>
                    <select id="hp-type" name="type">
                        <option value="">Any type</option>
                        <option value="fixed">Fixed price</option>
                        <option value="hourly">Hourly</option>
                        <option value="negotiable">Negotiable</option>
                    </select>
                </div>
            </div>
            <div class="search-panel-foot">
                <label class="search-remote"><input type="checkbox" name="remote" value="1"> <span>Remote only</span></label>
                <div class="search-actions">
                    <button type="submit" class="btn btn-primary btn-ripple"><i class="fas fa-search"></i> Search</button>
                    <a href="jobs.php" class="btn btn-outline"><i class="fas fa-rotate-left"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- ============================================================
     HOW IT WORKS / FEATURE CARDS
     ============================================================ -->
<section class="section features" id="how-it-works">
    <div class="container-wide">
        <div class="section-head reveal">
            <span class="section-eyebrow"><i class="fas fa-route"></i> How it works</span>
            <h2>Four simple steps to succeed</h2>
            <p>From creating your account to getting hired — we make it effortless.</p>
        </div>
        <div class="features-grid steps-4">
            <article class="feature-card reveal">
                <span class="step-num">01</span>
                <div class="feat-icon"><i class="fas fa-user-plus"></i></div>
                <h3>Create an account</h3>
                <p>Sign up as a student or employer in under a minute. It's free and takes just a few clicks.</p>
            </article>
            <article class="feature-card reveal reveal-delay-1">
                <span class="step-num">02</span>
                <div class="feat-icon"><i class="fas fa-id-badge"></i></div>
                <h3>Build your profile</h3>
                <p>Add your skills, portfolio, experience, and rates so employers can find the perfect match.</p>
            </article>
            <article class="feature-card reveal reveal-delay-2">
                <span class="step-num">03</span>
                <div class="feat-icon"><i class="fas fa-paper-plane"></i></div>
                <h3>Apply for jobs</h3>
                <p>Browse opportunities, shortlist favorites, and apply in one click with a clear cover letter.</p>
            </article>
            <article class="feature-card reveal reveal-delay-3">
                <span class="step-num">04</span>
                <div class="feat-icon"><i class="fas fa-award"></i></div>
                <h3>Get hired</h3>
                <p>Collaborate, deliver great work, get paid securely, and build a stellar reputation.</p>
            </article>
        </div>
    </div>
</section>

<!-- ============================================================
     POPULAR CATEGORIES
     ============================================================ -->
<section class="section categories" id="categories">
    <div class="container-wide">
        <div class="section-head reveal">
            <span class="section-eyebrow"><i class="fas fa-th-large"></i> Categories</span>
            <h2>Explore popular categories</h2>
            <p>Find work in the categories that matter most to your career.</p>
        </div>
        <div class="cat-grid">
            <?php $catColors = ['#2563EB', '#14B8A6', '#F59E0B', '#DB2777', '#2563EB', '#14B8A6', '#F59E0B', '#DB2777']; ?>
            <?php foreach ($categories as $i => $cat): $cIcon = $cat['icon'] ?? 'fa-tag'; ?>
                <a href="jobs.php?category=<?php echo (int)$cat['id']; ?>" class="cat-card reveal">
                    <div class="cat-ico"><i class="fas <?php echo htmlspecialchars($cIcon); ?>"></i></div>
                    <h4><?php echo htmlspecialchars($cat['name'] ?? 'Category'); ?></h4>
                    <p><?php echo (int)($cat['job_count'] ?? 0); ?> open jobs</p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     FEATURED JOBS
     ============================================================ -->
<section class="section jobs-section" id="jobs">
    <div class="container-wide">
        <div class="section-head reveal">
            <span class="section-eyebrow"><i class="fas fa-briefcase"></i> Featured Jobs</span>
            <h2>Latest opportunities from employers</h2>
            <p>Handpicked roles updated daily across all industries.</p>
        </div>

        <?php if (empty($featured_jobs)): ?>
            <div class="empty-state reveal">
                <i class="fas fa-briefcase"></i>
                <p>No jobs available at the moment. Check back later!</p>
                <a href="auth/login.php" class="btn btn-primary">Notify Me</a>
            </div>
        <?php else: ?>
            <div class="jobs-grid" id="jobsGrid">
                <?php foreach ($featured_jobs as $job): ?>
                    <article class="job-card reveal">
                        <div class="job-card-top">
                            <div class="job-logo"><i class="fas <?php echo !empty($job['category_name']) ? 'fa-code' : 'fa-code'; ?>"></i></div>
                            <div>
                                <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                <span class="company"><i class="fas fa-building"></i> <?php echo htmlspecialchars($job['employer_name']); ?></span>
                            </div>
                            <?php if (isLoggedIn() && getUserRole() === 'student'): ?>
                                <a href="student/saved_jobs.php" class="bookmark-btn" aria-label="Save job" title="Save job"><i class="fas fa-bookmark"></i></a>
                            <?php else: ?>
                                <a href="auth/login.php" class="bookmark-btn" aria-label="Save job" title="Login to save"><i class="fas fa-bookmark"></i></a>
                            <?php endif; ?>
                        </div>

                        <div class="job-tags">
                            <span><?php echo htmlspecialchars($job['category_name'] ?? 'General'); ?></span>
                            <span><?php echo $job['is_remote'] ? 'Remote' : 'On-site'; ?></span>
                            <span><?php echo ucfirst(str_replace('_', ' ', $job['status'] ?? 'open')); ?></span>
                        </div>

                        <p style="font-size:14.5px;color:#64748B;line-height:1.65;margin-bottom:16px;">
                            <?php echo htmlspecialchars(substr(strip_tags($job['description'] ?? ''), 0, 110)); ?>...
                        </p>

                        <div class="job-meta-row">
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo $job['is_remote'] ? 'Remote' : htmlspecialchars($job['location'] ?? 'Nairobi, Kenya'); ?></span>
                            <span><i class="fas fa-money-bill-wave"></i> KSh <?php echo number_format($job['budget_min'] ?? 0); ?><?php echo !empty($job['budget_max']) ? ' - ' . number_format($job['budget_max']) : ''; ?></span>
                        </div>

                        <div class="job-card-foot">
                            <span class="salary">KSh <?php echo number_format($job['budget_min'] ?? 0); ?> <small>/ <?php echo htmlspecialchars($job['budget_type'] ?? 'fixed'); ?></small></span>
                            <?php if (isLoggedIn() && getUserRole() === 'student'): ?>
                                <a href="student/apply.php?id=<?php echo $job['id']; ?>" class="btn btn-primary btn-sm btn-ripple">Apply Now</a>
                            <?php elseif (isLoggedIn() && getUserRole() === 'employer'): ?>
                                <a href="employer/my_jobs.php" class="btn btn-outline btn-sm">View</a>
                            <?php else: ?>
                                <a href="auth/login.php" class="btn btn-primary btn-sm">Login to Apply</a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="viewall-wrap reveal">
            <a href="student/available_jobs.php" class="btn btn-outline btn-lg"><i class="fas fa-arrow-right"></i> View All Jobs</a>
        </div>
    </div>
</section>

<!-- ============================================================
     TOP FREELANCERS (Upwork-inspired)
     ============================================================ -->
<section class="section freelancers-section" id="freelancers">
    <div class="container-wide">
        <div class="section-head reveal">
            <span class="section-eyebrow"><i class="fas fa-user-tie"></i> Top Freelancers</span>
            <h2>Meet our top-rated talent</h2>
            <p>Skilled, vetted, and ready to bring your project to life.</p>
        </div>

        <div class="fl-grid">
            <?php foreach ($freelancers as $i => $fl):
                $flName = $fl['full_name'] ?? 'Freelancer';
                $flRole = $fl['experience'] ?? 'Freelancer';
                $flSkills = array_slice(array_filter(array_map('trim', explode(',', ($fl['skills'] ?? '')))), 0, 3);
                $flRate = number_format((float)($fl['hourly_rate'] ?? 0), 0);
                $flRating = (float)($fl['rating'] ?? 4.5);
                $flJobs = (int)($fl['total_jobs_completed'] ?? 0);
                $initials = implode('', array_map(fn($p) => strtoupper($p[0] ?? ''), preg_split('/\s+/', trim($flName))));
            ?>
                <article class="fl-card reveal">
                    <div class="fl-head">
                        <div class="fl-avatar"><?php echo $initials; ?></div>
                        <span class="fl-badge"><i class="fas fa-circle-check"></i> Top Rated</span>
                    </div>
                    <h3 class="fl-name"><?php echo htmlspecialchars($flName); ?></h3>
                    <p class="fl-role"><?php echo htmlspecialchars($flRole); ?></p>
                    <div class="fl-rating" aria-label="Rating <?php echo $flRating; ?> out of 5">
                        <?php for ($s = 1; $s <= 5; $s++): ?>
                            <i class="fas fa-star <?php echo $s <= round($flRating) ? 'filled' : ''; ?>"></i>
                        <?php endfor; ?>
                        <span><?php echo number_format($flRating, 1); ?></span>
                    </div>
                    <div class="fl-location"><i class="fas fa-location-dot"></i> <?php echo htmlspecialchars($fl['location'] ?? 'Remote'); ?></div>
                    <?php if (!empty($flSkills)): ?>
                        <div class="fl-skills">
                            <?php foreach ($flSkills as $skill): ?>
                                <span><?php echo htmlspecialchars($skill); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="fl-foot">
                        <?php if ($flRate > 0): ?>
                            <div class="fl-rate"><strong>$<?php echo $flRate; ?></strong><span>/hr</span></div>
                        <?php else: ?>
                            <div class="fl-rate"><strong>Rate</strong><span>on request</span></div>
                        <?php endif; ?>
                        <?php
                            // Role-aware Hire Me: guests → login; logged-in → direct chat with the freelancer
                            $hire_url = 'auth/login.php?redirect=index.php';
                            if (isLoggedIn()) {
                                $hire_url = 'api/chat.php?user=' . (int)$fl['id'];
                            }
                        ?>
                        <a href="<?php echo $hire_url; ?>" class="btn btn-outline btn-sm">Hire Me</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     TESTIMONIALS
     ============================================================ -->
<section class="section testimonials" id="testimonials">
    <div class="container-wide">
        <div class="section-head reveal">
            <span class="section-eyebrow"><i class="fas fa-comment-dots"></i> Testimonials</span>
            <h2>Loved by students &amp; employers</h2>
            <p>Real stories from people building careers and teams on SkillSeek.</p>
        </div>

        <div class="testi-track reveal" id="testiTrack">
            <div class="testi-card">
                <div class="testi-stars" aria-label="5 out of 5 stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                <p class="testi-text">SkillSeek connected me with my first real freelance clients. I landed a full-stack project within a week of creating my profile!</p>
                <div class="testi-user">
                    <div class="testi-ava" style="background:linear-gradient(135deg,#2563EB,#3B82F6);">JD</div>
                    <div><h5>Jane Doe</h5><span>Full Stack Developer, Student</span></div>
                </div>
            </div>
            <div class="testi-card">
                <div class="testi-stars" aria-label="5 out of 5 stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                <p class="testi-text">As an employer, I've hired three talented students through SkillSeek. The quality of candidates and communication is outstanding.</p>
                <div class="testi-user">
                    <div class="testi-ava" style="background:linear-gradient(135deg,#14B8A6,#2DD4BF);">JS</div>
                    <div><h5>John Smith</h5><span>CEO, TechCorp Solutions</span></div>
                </div>
            </div>
            <div class="testi-card">
                <div class="testi-stars" aria-label="5 out of 5 stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                <p class="testi-text">The platform is intuitive and professional. I found exactly the skills I needed and the hiring process was smooth end to end.</p>
                <div class="testi-user">
                    <div class="testi-ava" style="background:linear-gradient(135deg,#F59E0B,#FBBF24);">MK</div>
                    <div><h5>Mary Kiptoo</h5><span>Marketing Lead, BrandMinds</span></div>
                </div>
            </div>
        </div>

        <div class="carousel-nav" role="group" aria-label="Testimonial carousel">
            <button class="carousel-btn" id="testiPrev" aria-label="Previous testimonial"><i class="fas fa-arrow-left"></i></button>
            <button class="carousel-btn" id="testiNext" aria-label="Next testimonial"><i class="fas fa-arrow-right"></i></button>
        </div>
        <div class="car-dots" id="testiDots" role="tablist" aria-label="Select testimonial"></div>
    </div>
</section>

<!-- ============================================================
     CALL TO ACTION
     ============================================================ -->
<section class="cta-section" id="cta">
    <div class="container-wide">
        <div class="cta-box reveal">
            <h2>Ready to launch your career?</h2>
            <p>Join a growing community of freelancers and employers. Find your next opportunity or project today.</p>
            <div class="cta-buttons">
                <?php if (!isLoggedIn()): ?>
                    <a href="auth/register.php" class="btn btn-light btn-lg btn-ripple"><i class="fas fa-user-plus"></i> Join SkillSeek</a>
                    <a href="auth/register.php" class="btn btn-white-ghost btn-lg"><i class="fas fa-briefcase"></i> Post a Job</a>
                    <a href="jobs.php" class="btn btn-white-ghost btn-lg"><i class="fas fa-eye"></i> Browse Opportunities</a>
                <?php else: ?>
                    <?php if (getUserRole() === 'employer'): ?>
                        <a href="employer/post_job.php" class="btn btn-light btn-lg btn-ripple"><i class="fas fa-plus-circle"></i> Post a Job</a>
                        <a href="employer/dashboard.php" class="btn btn-white-ghost btn-lg"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <?php else: ?>
                        <a href="student/available_jobs.php" class="btn btn-light btn-lg btn-ripple"><i class="fas fa-search"></i> Browse Opportunities</a>
                        <a href="student/dashboard.php" class="btn btn-white-ghost btn-lg"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

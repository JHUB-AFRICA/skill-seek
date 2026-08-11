<?php
// ============================================
// SkillSeek - Browse Jobs (Public listing)
// ============================================
require_once 'config/database.php';

$search = trim($_GET['q'] ?? '');
$loc = trim($_GET['loc'] ?? '');
$category = (int)($_GET['category'] ?? 0);
$type = trim($_GET['type'] ?? '');
$remote = isset($_GET['remote']) ? 1 : 0;

$sql = "
    SELECT j.*, u.full_name as employer_name, c.name as category_name
    FROM jobs j
    JOIN users u ON j.employer_id = u.id
    LEFT JOIN categories c ON j.category_id = c.id
    WHERE j.status = 'open'
";
$params = [];

if ($search !== '') {
    $sql .= " AND (j.title LIKE :q1 OR j.description LIKE :q2)";
    $params[':q1'] = '%' . $search . '%';
    $params[':q2'] = '%' . $search . '%';
}
if ($loc !== '') {
    $sql .= " AND (j.location LIKE :loc)";
    $params[':loc'] = '%' . $loc . '%';
}
if ($category) {
    $sql .= " AND j.category_id = :cat";
    $params[':cat'] = $category;
}
if ($type !== '') {
    $sql .= " AND j.budget_type = :type";
    $params[':type'] = $type;
}
if ($remote) {
    $sql .= " AND j.is_remote = 1";
}

$sql .= " ORDER BY j.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll();

$categories = [];
$catStmt = $pdo->query("SELECT * FROM categories ORDER BY name");
if ($catStmt) { $categories = $catStmt->fetchAll(); }

$total = count($jobs);
$page_title = 'Browse Jobs - SkillSeek';

include 'includes/header.php';
?>

<section class="section" style="padding-top:60px;" id="browse">
    <div class="container-wide">
        <div class="section-head reveal">
            <span class="section-eyebrow"><i class="fas fa-briefcase"></i> Browse Jobs</span>
            <h2>Explore open opportunities</h2>
            <p><?php echo $total; ?> job<?php echo $total !== 1 ? 's' : ''; ?> currently available.</p>
        </div>

        <form method="get" action="jobs.php" class="search-panel reveal" role="search" aria-label="Advanced job search">
            <div class="search-panel-grid">
                <div class="search-field">
                    <label for="sp-q"><i class="fas fa-magnifying-glass"></i> Skill or keyword</label>
                    <input type="text" id="sp-q" name="q" placeholder="e.g. PHP developer" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="search-field">
                    <label for="sp-loc"><i class="fas fa-location-dot"></i> Location</label>
                    <input type="text" id="sp-loc" name="loc" placeholder="City or Remote" value="<?php echo htmlspecialchars($loc); ?>">
                </div>
                <div class="search-field">
                    <label for="sp-cat"><i class="fas fa-th-large"></i> Category</label>
                    <select id="sp-cat" name="category">
                        <option value="">All categories</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo $category === (int)$c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="search-field">
                    <label for="sp-type"><i class="fas fa-list-check"></i> Job type</label>
                    <select id="sp-type" name="type">
                        <option value="">Any type</option>
                        <?php foreach (['fixed' => 'Fixed price', 'hourly' => 'Hourly', 'negotiable' => 'Negotiable'] as $val => $lbl): ?>
                            <option value="<?php echo $val; ?>" <?php echo $type === $val ? 'selected' : ''; ?>><?php echo $lbl; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="search-panel-foot">
                <label class="search-remote"><input type="checkbox" name="remote" value="1" <?php echo $remote ? 'checked' : ''; ?>> <span>Remote only</span></label>
                <div class="search-actions">
                    <button type="submit" class="btn btn-primary btn-ripple"><i class="fas fa-search"></i> Search</button>
                    <a href="jobs.php" class="btn btn-outline"><i class="fas fa-rotate-left"></i> Reset</a>
                </div>
            </div>
        </form>

        <?php if (empty($jobs)): ?>
            <div class="empty-state reveal">
                <i class="fas fa-briefcase"></i>
                <h3>No jobs found</h3>
                <p><?php echo $search !== '' ? 'No results match your search.' : 'No open jobs at the moment.'; ?></p>
                <a href="jobs.php" class="btn btn-primary">View all jobs</a>
            </div>
        <?php else: ?>
            <div class="jobs-grid">
                <?php foreach ($jobs as $job): ?>
                    <article class="job-card reveal">
                        <div class="job-card-top">
                            <div class="job-logo"><i class="fas fa-code"></i></div>
                            <div>
                                <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                <span class="company"><i class="fas fa-building"></i> <?php echo htmlspecialchars($job['employer_name']); ?></span>
                            </div>
                            <?php if (isLoggedIn() && getUserRole() === 'student'): ?>
                                <a href="student/saved_jobs.php" class="bookmark-btn" aria-label="Save job"><i class="fas fa-bookmark"></i></a>
                            <?php else: ?>
                                <a href="auth/login.php" class="bookmark-btn" aria-label="Login to save"><i class="fas fa-bookmark"></i></a>
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
    </div>
</section>

<?php include 'includes/footer.php'; ?>
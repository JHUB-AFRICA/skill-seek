<?php
// ============================================
// SkillSeek - Edit Job
// File: employer/edit_job.php
// Description: Employers can edit their existing jobs
// ============================================

require_once '../config/database.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

if (getUserRole() !== 'employer') {
    redirect('../student/dashboard.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// ============================================
// GET JOB TO EDIT
// ============================================
$job_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if ($job_id <= 0) {
    redirect('my_jobs.php');
}

$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ? AND employer_id = ?");
$stmt->execute([$job_id, $user_id]);
$job = $stmt->fetch();

if (!$job) {
    redirect('my_jobs.php');
}

// ============================================
// HANDLE FORM SUBMISSION
// ============================================
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $requirements = sanitize($_POST['requirements'] ?? '');
    $responsibilities = sanitize($_POST['responsibilities'] ?? '');
    $budget_min = floatval($_POST['budget_min'] ?? 0);
    $budget_max = floatval($_POST['budget_max'] ?? 0);
    $budget_type = sanitize($_POST['budget_type'] ?? 'fixed');
    $location = sanitize($_POST['location'] ?? '');
    $is_remote = isset($_POST['is_remote']) ? 1 : 0;
    $status = sanitize($_POST['status'] ?? 'open');
    $expires_at = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;

    if (empty($title)) {
        $error = 'Please enter a job title';
    } elseif (empty($description)) {
        $error = 'Please enter a job description';
    } elseif (empty($category)) {
        $error = 'Please select a category';
    } elseif ($budget_min <= 0 && $budget_max <= 0) {
        $error = 'Please enter a valid budget';
    } elseif ($budget_max > 0 && $budget_max < $budget_min) {
        $error = 'Maximum budget must be greater than minimum budget';
    } else {
        try {
            $cat_stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
            $cat_stmt->execute([$category]);
            $category_id = $cat_stmt->fetchColumn() ?: null;

            $stmt = $pdo->prepare("
                UPDATE jobs SET
                    category_id = ?, title = ?, description = ?, requirements = ?,
                    responsibilities = ?, budget_min = ?, budget_max = ?, budget_type = ?,
                    location = ?, is_remote = ?, status = ?, expires_at = ?
                WHERE id = ? AND employer_id = ?
            ");
            $stmt->execute([
                $category_id, $title, $description, $requirements,
                $responsibilities, $budget_min, $budget_max, $budget_type,
                $location, $is_remote, $status, $expires_at,
                $job_id, $user_id
            ]);

            $success = 'Job updated successfully! 🎉';

            // Refresh job data
            $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ? AND employer_id = ?");
            $stmt->execute([$job_id, $user_id]);
            $job = $stmt->fetch();
        } catch (PDOException $e) {
            $error = 'Error updating job: ' . $e->getMessage();
        }
    }
}

// ============================================
// GET CATEGORIES FOR DROPDOWN
// ============================================
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

$page_title = 'Edit Job - SkillSeek';
include '../includes/header.php';
?>

<!-- ============================================================
     PAGE CONTENT
     ============================================================ -->
<div class="dashboard-container">

    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar"><i class="fas fa-building"></i></div>
            <h3><?php echo htmlspecialchars($user_name); ?></h3>
            <span class="role-badge employer">Employer</span>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="post_job.php"><i class="fas fa-plus-circle"></i> Post Job</a></li>
                <li class="active"><a href="my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a></li>
                <li><a href="applications.php"><i class="fas fa-users"></i> Applications</a></li>
                <li><a href="talent.php"><i class="fas fa-user-graduate"></i> Find Talent</a></li>
                <li><a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="../profile.php"><i class="fas fa-user-edit"></i> Profile</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="dashboard-main">

        <div class="page-header">
            <div class="header-left">
                <h1>Edit Job</h1>
                <p>Update the details of your job listing</p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <br>
                <a href="my_jobs.php" class="btn btn-primary btn-sm" style="margin-top:10px;">View My Jobs</a>
                <a href="dashboard.php" class="btn btn-secondary btn-sm" style="margin-top:10px;">Dashboard</a>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST" action="" class="job-form">

            <div class="form-card">
                <h3 class="form-section-title"><i class="fas fa-info-circle"></i> Basic Information</h3>
                <div class="form-group">
                    <label for="title">Job Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($job['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="category">Category <span class="required">*</span></label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['name']); ?>"
                                <?php echo (isset($job['category_id']) && $cat['id'] == $job['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">Job Description <span class="required">*</span></label>
                    <textarea id="description" name="description" class="form-control" rows="6" required><?php echo htmlspecialchars($job['description']); ?></textarea>
                </div>
            </div>

            <div class="form-card">
                <h3 class="form-section-title"><i class="fas fa-tasks"></i> Requirements & Responsibilities</h3>
                <div class="form-group">
                    <label for="requirements">Requirements</label>
                    <textarea id="requirements" name="requirements" class="form-control" rows="4"><?php echo htmlspecialchars($job['requirements'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="responsibilities">Responsibilities</label>
                    <textarea id="responsibilities" name="responsibilities" class="form-control" rows="4"><?php echo htmlspecialchars($job['responsibilities'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="form-card">
                <h3 class="form-section-title"><i class="fas fa-money-bill"></i> Budget & Location</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="budget_min">Minimum Budget (KSh) <span class="required">*</span></label>
                        <input type="number" id="budget_min" name="budget_min" class="form-control" value="<?php echo htmlspecialchars($job['budget_min'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="budget_max">Maximum Budget (KSh)</label>
                        <input type="number" id="budget_max" name="budget_max" class="form-control" value="<?php echo htmlspecialchars($job['budget_max'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="budget_type">Budget Type</label>
                    <select id="budget_type" name="budget_type" class="form-control">
                        <option value="fixed" <?php echo ($job['budget_type'] === 'fixed') ? 'selected' : ''; ?>>Fixed Price</option>
                        <option value="hourly" <?php echo ($job['budget_type'] === 'hourly') ? 'selected' : ''; ?>>Hourly Rate</option>
                        <option value="negotiable" <?php echo ($job['budget_type'] === 'negotiable') ? 'selected' : ''; ?>>Negotiable</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-control" value="<?php echo htmlspecialchars($job['location'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="is_remote" name="is_remote" value="1" <?php echo $job['is_remote'] ? 'checked' : ''; ?>>
                        <label for="is_remote">This is a remote position</label>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <h3 class="form-section-title"><i class="fas fa-cog"></i> Job Settings</h3>
                <div class="form-group">
                    <label for="status">Job Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="open" <?php echo ($job['status'] === 'open') ? 'selected' : ''; ?>>Open - Accepting Applications</option>
                        <option value="draft" <?php echo ($job['status'] === 'draft') ? 'selected' : ''; ?>>Draft - Save for Later</option>
                        <option value="in_progress" <?php echo ($job['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                        <option value="completed" <?php echo ($job['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="expires_at">Expiry Date</label>
                    <input type="date" id="expires_at" name="expires_at" class="form-control" value="<?php echo htmlspecialchars($job['expires_at'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Changes</button>
                <a href="my_jobs.php" class="btn btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
        <?php endif; ?>

    </main>
</div>

<?php include '../includes/footer.php'; ?>

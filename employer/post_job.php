<?php
// ============================================
// SkillSeek - Post Job
// File: employer/post_job.php
// Description: Employers can post new jobs
// ============================================

// Include configuration
require_once '../config/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// Check if user is an employer
if (getUserRole() !== 'employer') {
    redirect('../student/dashboard.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// ============================================
// HANDLE FORM SUBMISSION
// ============================================
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
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
    
    // Validation
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
            // Insert job into database
            $stmt = $pdo->prepare("
                INSERT INTO jobs (
                    employer_id, 
                    category_id, 
                    title, 
                    description, 
                    requirements, 
                    responsibilities,
                    budget_min, 
                    budget_max, 
                    budget_type, 
                    location, 
                    is_remote, 
                    status,
                    expires_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            // Get category ID from name
            $cat_stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
            $cat_stmt->execute([$category]);
            $category_id = $cat_stmt->fetchColumn();
            
            if (!$category_id) {
                // If category doesn't exist, use NULL
                $category_id = null;
            }
            
            $stmt->execute([
                $user_id,
                $category_id,
                $title,
                $description,
                $requirements,
                $responsibilities,
                $budget_min,
                $budget_max,
                $budget_type,
                $location,
                $is_remote,
                $status,
                $expires_at
            ]);
            
            $job_id = $pdo->lastInsertId();
            
            $success = 'Job posted successfully! 🎉';
            
        } catch(PDOException $e) {
            $error = 'Error posting job: ' . $e->getMessage();
        }
    }
}

// ============================================
// GET CATEGORIES FOR DROPDOWN
// ============================================
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

// Set page title
$page_title = 'Post a Job - SkillSeek';

// Include header
include '../includes/header.php';
?>

<!-- ============================================================
     PAGE CONTENT
     ============================================================ -->
<div class="dashboard-container">
    
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar">
                <i class="fas fa-building"></i>
            </div>
            <h3><?php echo htmlspecialchars($user_name); ?></h3>
            <span class="role-badge employer">Employer</span>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li class="active"><a href="post_job.php"><i class="fas fa-plus-circle"></i> Post Job</a></li>
                <li><a href="my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a></li>
                <li><a href="applications.php"><i class="fas fa-users"></i> Applications</a></li>
                <li><a href="talent.php"><i class="fas fa-user-graduate"></i> Find Talent</a></li>
                <li><a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="../profile.php"><i class="fas fa-user-edit"></i> Profile</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="dashboard-main">
        
        <!-- Page Header -->
        <div class="page-header">
            <h1>Post a New Job</h1>
            <p>Fill in the details below to create a new job listing</p>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
                <br>
                <a href="my_jobs.php" class="btn btn-primary btn-sm" style="margin-top: 10px;">View My Jobs</a>
                <a href="post_job.php" class="btn btn-secondary btn-sm" style="margin-top: 10px;">Post Another Job</a>
            </div>
        <?php endif; ?>
        
        <!-- Job Form - FIXED: Removed data-validate -->
        <?php if (!$success): ?>
        <form method="POST" action="" class="job-form">
            
            <div class="form-card">
                <h3 class="form-section-title"><i class="fas fa-info-circle"></i> Basic Information</h3>
                
                <!-- Job Title -->
                <div class="form-group">
                    <label for="title">Job Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" class="form-control" 
                           placeholder="e.g. Senior Web Developer Needed" 
                           value="<?php echo $_POST['title'] ?? ''; ?>" required>
                    <span class="form-hint">A clear, descriptive title for your job</span>
                </div>
                
                <!-- Category -->
                <div class="form-group">
                    <label for="category">Category <span class="required">*</span></label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['name']); ?>" 
                                <?php echo (isset($_POST['category']) && $_POST['category'] == $cat['name']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Job Description -->
                <div class="form-group">
                    <label for="description">Job Description <span class="required">*</span></label>
                    <textarea id="description" name="description" class="form-control" rows="6" 
                              placeholder="Describe the job, project scope, and what you're looking for..." required><?php echo $_POST['description'] ?? ''; ?></textarea>
                    <span class="form-hint">Be detailed about the work involved</span>
                </div>
            </div>
            
            <div class="form-card">
                <h3 class="form-section-title"><i class="fas fa-tasks"></i> Requirements & Responsibilities</h3>
                
                <!-- Requirements -->
                <div class="form-group">
                    <label for="requirements">Requirements</label>
                    <textarea id="requirements" name="requirements" class="form-control" rows="4" 
                              placeholder="List the skills, experience, and qualifications needed..."><?php echo $_POST['requirements'] ?? ''; ?></textarea>
                    <span class="form-hint">What skills and experience are required?</span>
                </div>
                
                <!-- Responsibilities -->
                <div class="form-group">
                    <label for="responsibilities">Responsibilities</label>
                    <textarea id="responsibilities" name="responsibilities" class="form-control" rows="4" 
                              placeholder="List the day-to-day responsibilities..."><?php echo $_POST['responsibilities'] ?? ''; ?></textarea>
                    <span class="form-hint">What will the hired person be doing?</span>
                </div>
            </div>
            
            <div class="form-card">
                <h3 class="form-section-title"><i class="fas fa-money-bill"></i> Budget & Location</h3>
                
                <!-- Budget -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="budget_min">Minimum Budget (KSh) <span class="required">*</span></label>
                        <input type="number" id="budget_min" name="budget_min" class="form-control" 
                               placeholder="e.g. 50000" 
                               value="<?php echo $_POST['budget_min'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="budget_max">Maximum Budget (KSh)</label>
                        <input type="number" id="budget_max" name="budget_max" class="form-control" 
                               placeholder="e.g. 80000" 
                               value="<?php echo $_POST['budget_max'] ?? ''; ?>">
                        <span class="form-hint">Leave empty if fixed price</span>
                    </div>
                </div>
                
                <!-- Budget Type -->
                <div class="form-group">
                    <label for="budget_type">Budget Type</label>
                    <select id="budget_type" name="budget_type" class="form-control">
                        <option value="fixed" <?php echo (isset($_POST['budget_type']) && $_POST['budget_type'] == 'fixed') ? 'selected' : ''; ?>>Fixed Price</option>
                        <option value="hourly" <?php echo (isset($_POST['budget_type']) && $_POST['budget_type'] == 'hourly') ? 'selected' : ''; ?>>Hourly Rate</option>
                        <option value="negotiable" <?php echo (isset($_POST['budget_type']) && $_POST['budget_type'] == 'negotiable') ? 'selected' : ''; ?>>Negotiable</option>
                    </select>
                </div>
                
                <!-- Location -->
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-control" 
                           placeholder="e.g. Nairobi, Kenya" 
                           value="<?php echo $_POST['location'] ?? ''; ?>">
                </div>
                
                <!-- Remote Work -->
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="is_remote" name="is_remote" value="1" 
                               <?php echo (isset($_POST['is_remote']) && $_POST['is_remote'] == 1) ? 'checked' : ''; ?>>
                        <label for="is_remote">This is a remote position</label>
                    </div>
                </div>
            </div>
            
            <div class="form-card">
                <h3 class="form-section-title"><i class="fas fa-cog"></i> Job Settings</h3>
                
                <!-- Status -->
                <div class="form-group">
                    <label for="status">Job Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="open" <?php echo (isset($_POST['status']) && $_POST['status'] == 'open') ? 'selected' : ''; ?>>Open - Accepting Applications</option>
                        <option value="draft" <?php echo (isset($_POST['status']) && $_POST['status'] == 'draft') ? 'selected' : ''; ?>>Draft - Save for Later</option>
                    </select>
                </div>
                
                <!-- Expiry Date -->
                <div class="form-group">
                    <label for="expires_at">Expiry Date</label>
                    <input type="date" id="expires_at" name="expires_at" class="form-control" 
                           value="<?php echo $_POST['expires_at'] ?? date('Y-m-d', strtotime('+30 days')); ?>">
                    <span class="form-hint">When should this job listing expire?</span>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane"></i> Post Job
                </button>
                <a href="dashboard.php" class="btn btn-secondary btn-lg">Cancel</a>
            </div>
            
        </form>
        <?php endif; ?>
        
    </main>
</div>

<?php include '../includes/footer.php'; ?>
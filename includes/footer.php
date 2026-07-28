<?php
// ============================================
// SkillSeek - Footer Template
// File: includes/footer.php
// Description: Main footer for all pages
// ============================================

// Get base path if not set
$base_path = $base_path ?? '';
?>
    </main>
    <!-- End Main Content -->

    <!-- ============================================================
         FOOTER
         ============================================================ -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-grid">
                
                <!-- Brand Column -->
                <div class="footer-column">
                    <div class="footer-logo">
                        <span class="logo-icon">🚀</span>
                        <span class="logo-text">Skill<span>Seek</span></span>
                    </div>
                    <p class="footer-description">
                        Connecting employers with talented students. 
                        Find the perfect match for your projects.
                    </p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <!-- For Employers -->
                <div class="footer-column">
                    <h4>For Employers</h4>
                    <ul>
                        <li><a href="<?php echo $base_path; ?>employer/post_job.php">Post a Job</a></li>
                        <li><a href="<?php echo $base_path; ?>employer/my_jobs.php">Manage Jobs</a></li>
                        <li><a href="<?php echo $base_path; ?>employer/applications.php">View Applications</a></li>
                        <li><a href="<?php echo $base_path; ?>employer/talent.php">Find Talent</a></li>
                    </ul>
                </div>
                
                <!-- For Students -->
                <div class="footer-column">
                    <h4>For Students</h4>
                    <ul>
                        <li><a href="<?php echo $base_path; ?>student/available_jobs.php">Find Jobs</a></li>
                        <li><a href="<?php echo $base_path; ?>student/my_applications.php">My Applications</a></li>
                        <li><a href="<?php echo $base_path; ?>student/saved_jobs.php">Saved Jobs</a></li>
                        <li><a href="<?php echo $base_path; ?>student/profile.php">Update Profile</a></li>
                    </ul>
                </div>
                
                <!-- Support -->
                <div class="footer-column">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="<?php echo $base_path; ?>support.php">Help Center</a></li>
                        <li><a href="<?php echo $base_path; ?>contact.php">Contact Us</a></li>
                        <li><a href="<?php echo $base_path; ?>faq.php">FAQ</a></li>
                        <li><a href="<?php echo $base_path; ?>terms.php">Terms & Conditions</a></li>
                        <li><a href="<?php echo $base_path; ?>privacy.php">Privacy Policy</a></li>
                    </ul>
                </div>
                
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> SkillSeek. All rights reserved.</p>
                <p class="footer-version">Version 1.0</p>
            </div>
        </div>
    </footer>

    <!-- ============================================================
     BACK TO TOP BUTTON
     ============================================================ -->
    <button id="backToTop" class="back-to-top" onclick="scrollToTop()" aria-label="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- ============================================================
     TOAST CONTAINER
     ============================================================ -->
    <div id="toastContainer"></div>

    <!-- ============================================================
     JAVASCRIPT
     ============================================================ -->
    <script src="<?php echo $base_path; ?>assets/js/main.js"></script>
    
    <script>
        // Scroll to top function
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
        
        // Back to top button visibility
        window.addEventListener('scroll', function() {
            const button = document.getElementById('backToTop');
            if (button) {
                if (window.scrollY > 300) {
                    button.style.display = 'flex';
                } else {
                    button.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>
<?php
// ============================================
// SkillSeek - Footer
// ============================================

$base_path = $base_path ?? '';
?>
    </main>

    <!-- ============================================================
         FOOTER
         ============================================================ -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-grid">
                
                <!-- Brand Column -->
                <div class="footer-column">
                   <div class="footer-logo">
    <img src="/SkillSeek/assets/images/logo.jpeg" alt="SkillSeek" style="height: 35px; width: auto; display: block;">
    <span style="font-weight: 900; font-size: 1.4rem; color: #FFFFFF; margin-left: 8px;">Skill<span style="color: #818CF8;">Seek</span></span>
</div>
                    <p class="footer-description">
                        Connecting employers with talented students. 
                        Find the perfect match for your projects.
                    </p>
                </div>
                
                <!-- For Employers -->
                <div class="footer-column">
                    <h4>For Employers</h4>
                    <ul>
                        <li><a href="/SkillSeek/employer/dashboard.php">Dashboard</a></li>
                        <li><a href="/SkillSeek/employer/post_job.php">Post a Job</a></li>
                        <li><a href="/SkillSeek/employer/my_jobs.php">Manage Jobs</a></li>
                        <li><a href="/SkillSeek/employer/applications.php">View Applications</a></li>
                        <li><a href="/SkillSeek/employer/talent.php">Find Talent</a></li>
                        <li><a href="/SkillSeek/employer/payments.php">Payments</a></li>
                    </ul>
                </div>
                
                <!-- For Students -->
                <div class="footer-column">
                    <h4>For Students</h4>
                    <ul>
                        <li><a href="/SkillSeek/student/dashboard.php">Dashboard</a></li>
                        <li><a href="/SkillSeek/student/available_jobs.php">Find Jobs</a></li>
                        <li><a href="/SkillSeek/student/my_applications.php">My Applications</a></li>
                        <li><a href="/SkillSeek/student/saved_jobs.php">Saved Jobs</a></li>
                        <li><a href="/SkillSeek/student/profile.php">Update Profile</a></li>
                    </ul>
                </div>
                
                <!-- Support -->
                <div class="footer-column">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="/SkillSeek/support.php">Help Center</a></li>
                        <li><a href="/SkillSeek/contact.php">Contact Us</a></li>
                        <li><a href="/SkillSeek/faq.php">FAQ</a></li>
                        <li><a href="/SkillSeek/terms.php">Terms &amp; Conditions</a></li>
                        <li><a href="/SkillSeek/privacy.php">Privacy Policy</a></li>
                    </ul>
                </div>
                
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> SkillSeek. All rights reserved.</p>
                <p class="footer-version">Version 1.0</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top" onclick="scrollToTop()">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Toast Container -->
    <div id="toastContainer"></div>

    <!-- Main JavaScript -->
    <script src="/SkillSeek/assets/js/main.js"></script>
    
    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        window.addEventListener('scroll', function() {
            const button = document.getElementById('backToTop');
            if (button) {
                button.style.display = window.scrollY > 300 ? 'flex' : 'none';
            }
        });
    </script>
</body>
</html>
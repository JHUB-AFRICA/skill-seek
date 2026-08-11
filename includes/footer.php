<?php
// ============================================
<<<<<<< HEAD
// SkillSeek - Footer
// ============================================

=======
// SkillSeek - Footer (Premium Redesign)
// ============================================
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
$base_path = $base_path ?? '';
$assets = $base_path . 'assets/';
?>
    </main>

    <!-- ============================================================
         FOOTER
         ============================================================ -->
    <footer class="main-footer">
        <div class="container-wide">

            <div class="footer-grid">
<<<<<<< HEAD
                
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
=======

                <!-- Brand / Logo -->
                <div class="footer-col footer-brand">
                    <div class="header-logo">
                        <a href="<?php echo $base_path; ?>/index.php" aria-label="SkillSeek Home">
                            <img src="<?php echo $assets; ?>images/logo.jpeg" alt="SkillSeek Logo" class="logo-image">
                            <span class="logo-name">Skill<span>Seek</span></span>
                        </a>
                    </div>
                    <p>
                        A modern freelance marketplace connecting talented students,
                        skilled freelancers, and ambitious employers in one professional
                        platform to get work done faster.
                    </p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="X / Twitter"><i class="fab fa-twitter"></i></a>
                    </div>
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
                </div>

                <!-- Company -->
                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
<<<<<<< HEAD
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
                
=======
                        <li><a href="<?php echo $base_path; ?>/index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li><a href="<?php echo $base_path; ?>/jobs.php"><i class="fas fa-chevron-right"></i> Jobs</a></li>
                        <li><a href="<?php echo $base_path; ?>/index.php#categories"><i class="fas fa-chevron-right"></i> Categories</a></li>
                        <li><a href="<?php echo $base_path; ?>/index.php#freelancers"><i class="fas fa-chevron-right"></i> Freelancers</a></li>
                        <li><a href="<?php echo $base_path; ?>/about.php"><i class="fas fa-chevron-right"></i> About</a></li>
                    </ul>
                </div>

>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
                <!-- Support -->
                <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
<<<<<<< HEAD
                        <li><a href="/SkillSeek/support.php">Help Center</a></li>
                        <li><a href="/SkillSeek/contact.php">Contact Us</a></li>
                        <li><a href="/SkillSeek/faq.php">FAQ</a></li>
                        <li><a href="/SkillSeek/terms.php">Terms &amp; Conditions</a></li>
                        <li><a href="/SkillSeek/privacy.php">Privacy Policy</a></li>
=======
                        <li><a href="<?php echo $base_path; ?>/support.php"><i class="fas fa-chevron-right"></i> Support</a></li>
                        <li><a href="<?php echo $base_path; ?>/faq.php"><i class="fas fa-chevron-right"></i> FAQ</a></li>
                        <li><a href="<?php echo $base_path; ?>/contact.php"><i class="fas fa-chevron-right"></i> Contact</a></li>
                        <li><a href="<?php echo $base_path; ?>/support.php"><i class="fas fa-chevron-right"></i> Help Center</a></li>
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
                    </ul>
                </div>

                <!-- Newsletter + Social -->
                <div class="footer-col footer-newsletter">
                    <h4>Stay Updated</h4>
                    <p>Get the latest jobs and opportunities delivered to your inbox.</p>
                    <form class="newsletter-form" action="<?php echo $base_path; ?>/index.php" method="get" onsubmit="event.preventDefault(); showToast('Subscribed! We\u2019ll keep you posted.', 'success'); return false;">
                        <label for="newsletter-email" class="sr-only">Email address</label>
                        <input type="email" id="newsletter-email" name="email" placeholder="Enter your email" required aria-label="Email address">
                        <button type="submit" class="btn btn-primary btn-ripple" aria-label="Subscribe"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>

            </div>

            <div class="footer-bottom">
                <p>&copy; 2026 SkillSeek. All rights reserved.</p>
                <div class="legal">
                    <a href="<?php echo $base_path; ?>/privacy.php">Privacy</a>
                    <a href="<?php echo $base_path; ?>/terms.php">Terms</a>
                    <a href="<?php echo $base_path; ?>/support.php">Support</a>
                </div>
            </div>

        </div>
    </footer>

<<<<<<< HEAD
    <!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top" onclick="scrollToTop()">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Toast Container -->
    <div id="toastContainer"></div>

    <!-- Main JavaScript -->
    <script src="/SkillSeek/assets/js/main.js"></script>
    
=======
    <!-- ============================================================
         BACK TO TOP
         ============================================================ -->
    <button id="backToTop" class="back-to-top" onclick="scrollToTop()" aria-label="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- ============================================================
         TOAST CONTAINER
         ============================================================ -->
    <div id="toastContainer"></div>

    <!-- ============================================================
         GLOBAL JAVASCRIPT
         ============================================================ -->
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
<<<<<<< HEAD
        
        window.addEventListener('scroll', function() {
            const button = document.getElementById('backToTop');
            if (button) {
                button.style.display = window.scrollY > 300 ? 'flex' : 'none';
            }
        });
=======
>>>>>>> edea4d189acbfbdcfa9d92d3b8d426a2dfd2ceb1
    </script>
    <script src="<?php echo $base_path; ?>assets/js/main.js"></script>

</body>
</html>
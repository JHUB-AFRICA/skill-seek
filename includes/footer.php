<?php
// ============================================
// SkillSeek - Footer (Premium Redesign)
// ============================================
$base_path = $base_path ?? '';
$assets = $base_path . 'assets/';
?>
    </main>
    <!-- End Main Content -->

    <!-- ============================================================
         FOOTER
         ============================================================ -->
    <footer class="main-footer">
        <div class="container-wide">

            <div class="footer-grid">

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
                </div>

                <!-- Company -->
                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="<?php echo $base_path; ?>/index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li><a href="<?php echo $base_path; ?>/jobs.php"><i class="fas fa-chevron-right"></i> Jobs</a></li>
                        <li><a href="<?php echo $base_path; ?>/index.php#categories"><i class="fas fa-chevron-right"></i> Categories</a></li>
                        <li><a href="<?php echo $base_path; ?>/index.php#freelancers"><i class="fas fa-chevron-right"></i> Freelancers</a></li>
                        <li><a href="<?php echo $base_path; ?>/about.php"><i class="fas fa-chevron-right"></i> About</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="<?php echo $base_path; ?>/support.php"><i class="fas fa-chevron-right"></i> Support</a></li>
                        <li><a href="<?php echo $base_path; ?>/faq.php"><i class="fas fa-chevron-right"></i> FAQ</a></li>
                        <li><a href="<?php echo $base_path; ?>/contact.php"><i class="fas fa-chevron-right"></i> Contact</a></li>
                        <li><a href="<?php echo $base_path; ?>/support.php"><i class="fas fa-chevron-right"></i> Help Center</a></li>
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
    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
    <script src="<?php echo $base_path; ?>assets/js/main.js"></script>

</body>
</html>
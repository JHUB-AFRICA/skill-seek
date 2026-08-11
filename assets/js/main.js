// ============================================
// SkillSeek - Main JavaScript
// File: assets/js/main.js
// Version: 1.0
// Description: Global JavaScript functionality
// ============================================

// ============================================
// DOCUMENT READY
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 SkillSeek loaded successfully!');
    
    // Initialize all components
    initFormValidation();
    initPasswordToggle();
    initFileUpload();
    initSearchFilter();
    initMobileMenu();
    initBackToTop();
    initAutoDismissAlerts();
});

// ============================================
// FORM VALIDATION
// ============================================
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = this.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    showFieldError(field, 'This field is required');
                } else {
                    clearFieldError(field);
                }
                
                // Email validation
                if (field.type === 'email' && field.value.trim()) {
                    if (!isValidEmail(field.value)) {
                        isValid = false;
                        showFieldError(field, 'Please enter a valid email address');
                    }
                }
                
                // Password validation
                if (field.type === 'password' && field.value.trim()) {
                    if (field.value.length < 6) {
                        isValid = false;
                        showFieldError(field, 'Password must be at least 6 characters');
                    }
                }
            });
            
            // Confirm password
            const password = form.querySelector('input[type="password"][name="password"]');
            const confirm = form.querySelector('input[type="password"][name="confirm_password"]');
            if (password && confirm && password.value !== confirm.value) {
                isValid = false;
                showFieldError(confirm, 'Passwords do not match');
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
}

function showFieldError(field, message) {
    field.classList.add('error');
    let errorDiv = field.parentElement.querySelector('.form-error');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'form-error';
        field.parentElement.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
}

function clearFieldError(field) {
    field.classList.remove('error');
    const errorDiv = field.parentElement.querySelector('.form-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// ============================================
// PASSWORD TOGGLE
// ============================================
function initPasswordToggle() {
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            if (input) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            }
        });
    });
}

// ============================================
// FILE UPLOAD PREVIEW
// ============================================
function initFileUpload() {
    document.querySelectorAll('.file-upload').forEach(upload => {
        const input = upload.querySelector('input[type="file"]');
        const preview = upload.querySelector('.file-preview');
        const name = upload.querySelector('.file-name');
        
        if (input) {
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    if (name) name.textContent = file.name;
                    
                    if (preview && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; margin-top: 10px;">`;
                            preview.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });
        }
    });
}

// ============================================
// SEARCH & FILTER
// ============================================
function initSearchFilter() {
    const searchInput = document.getElementById('searchInput');
    const filterSelect = document.getElementById('filterSelect');
    const items = document.querySelectorAll('.filter-item');
    
    function filterItems() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const filterValue = filterSelect ? filterSelect.value : '';
        
        items.forEach(item => {
            let show = true;
            
            if (searchTerm) {
                const text = item.textContent.toLowerCase();
                if (!text.includes(searchTerm)) {
                    show = false;
                }
            }
            
            if (filterValue && show) {
                const category = item.dataset.category;
                if (category && category !== filterValue) {
                    show = false;
                }
            }
            
            item.style.display = show ? '' : 'none';
        });
    }
    
    if (searchInput) searchInput.addEventListener('input', filterItems);
    if (filterSelect) filterSelect.addEventListener('change', filterItems);
}

// ============================================
// MOBILE MENU
// ============================================
function initMobileMenu() {
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('open');
            this.classList.toggle('active');
        });
    }
}

// ============================================
// BACK TO TOP BUTTON
// ============================================
function initBackToTop() {
    const button = document.getElementById('backToTop');
    if (button) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                button.style.display = 'flex';
            } else {
                button.style.display = 'none';
            }
        });
        
        button.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

// ============================================
// AUTO DISMISS ALERTS
// ============================================
function initAutoDismissAlerts() {
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
}

// ============================================
// TOAST NOTIFICATIONS (class-based, matches design system)
// ============================================
function showToast(message, type = 'success') {
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        document.body.appendChild(toastContainer);
    }

    const icons = {
        success: '<i class="fas fa-check-circle"></i>',
        error: '<i class="fas fa-exclamation-circle"></i>',
        warning: '<i class="fas fa-triangle-exclamation"></i>',
        info: '<i class="fas fa-circle-info"></i>'
    };

    const toast = document.createElement('div');
    toast.className = 'toast ' + (type || 'info');
    toast.setAttribute('role', 'status');
    toast.innerHTML = '<span class="toast-ico">' + (icons[type] || icons.info) + '</span><span>' + message + '</span>';

    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('leaving');
        setTimeout(() => toast.remove(), 320);
    }, 3200);
}

// ============================================
// MODAL SYSTEM
// ============================================
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Close on backdrop click
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(modalId);
            }
        });
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(modal => {
            if (modal.style.display === 'flex') {
                closeModal(modal.id);
            }
        });
    }
});

// ============================================
// LOADING STATES
// ============================================
function showLoading(button) {
    const originalText = button.textContent;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    button.dataset.originalText = originalText;
}

function hideLoading(button) {
    button.disabled = false;
    button.textContent = button.dataset.originalText || button.textContent;
}

// ============================================
// CONFIRM DIALOG
// ============================================
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// ============================================
// DATE FORMATTING
// ============================================
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function timeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const seconds = Math.floor((now - date) / 1000);
    
    if (seconds < 60) return 'Just now';
    
    const intervals = [
        { label: 'minute', seconds: 60 },
        { label: 'hour', seconds: 3600 },
        { label: 'day', seconds: 86400 },
        { label: 'week', seconds: 604800 },
        { label: 'month', seconds: 2592000 },
        { label: 'year', seconds: 31536000 }
    ];
    
    for (const interval of intervals) {
        const count = Math.floor(seconds / interval.seconds);
        if (count > 0 && count < (interval.seconds === 60 ? 60 : Infinity)) {
            return count + ' ' + interval.label + (count > 1 ? 's' : '') + ' ago';
        }
    }
    
    return 'Long ago';
}

// ============================================
// AJAX FORM SUBMISSION
// ============================================
function submitFormAjax(form, url, method = 'POST') {
    const formData = new FormData(form);
    
    return fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Success!', 'success');
        } else {
            showToast(data.message || 'Error occurred', 'error');
        }
        return data;
    })
    .catch(error => {
        showToast('An error occurred. Please try again.', 'error');
        console.error('Error:', error);
    });
}

// ============================================
// COUNTER ANIMATION
// ============================================
function animateCounter(element, target, duration = 2000) {
    const start = 0;
    const startTime = performance.now();
    
    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const current = Math.floor(progress * target);
        
        element.textContent = current;
        
        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target;
        }
    }
    
    requestAnimationFrame(updateCounter);
}

// ============================================
// EXPOSE FUNCTIONS TO GLOBAL SCOPE
// ============================================
window.openModal = openModal;
window.closeModal = closeModal;
window.showToast = showToast;
window.confirmAction = confirmAction;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.formatDate = formatDate;
window.formatTime = formatTime;
window.timeAgo = timeAgo;
window.submitFormAjax = submitFormAjax;
window.animateCounter = animateCounter;

// ============================================
// ADD CSS ANIMATIONS
// ============================================
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .fa-spin {
        animation: spin 1s linear infinite;
    }
    
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.3s ease;
    }
    
    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
    }
`;
document.head.appendChild(styleSheet);

console.log('✅ SkillSeek JavaScript initialized successfully!');

// ============================================
// PREMIUM UI (app.css / redesigned homepage)
// ============================================
(function () {
    // --- Scroll reveal ---
    function initReveal() {
        const els = document.querySelectorAll('.reveal');
        if (!('IntersectionObserver' in window)) {
            els.forEach(el => el.classList.add('in-view'));
            return;
        }
        const io = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        els.forEach(el => io.observe(el));
    }

    // Animated counters
    function initCounters() {
        const counters = document.querySelectorAll('.counter');
        if (!counters.length) return;
        const animate = (el) => {
            const target = parseInt(el.dataset.target || '0', 10);
            const duration = 1600;
            const start = performance.now();
            const step = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                const ease = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.floor(ease * target).toLocaleString();
                if (progress < 1) requestAnimationFrame(step);
                else el.textContent = target.toLocaleString();
            };
            requestAnimationFrame(step);
        };
        const io = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) { animate(entry.target); io.unobserve(entry.target); }
            });
        }, { threshold: 0.6 });
        counters.forEach(c => io.observe(c));
    }

    // Button ripple
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-ripple');
        if (!btn) return;
        const rect = btn.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height) * 2;
        const ripple = document.createElement('span');
        ripple.className = 'ripple';
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
        ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
        btn.appendChild(ripple);
        setTimeout(() => ripple.remove(), 700);
    });

    // Back-to-top visibility
    function initBackToTop() {
        const btn = document.getElementById('backToTop');
        if (!btn) return;
        const onScroll = () => btn.classList.toggle('show', window.scrollY > 320);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    // Testimonial carousel
    function initCarousel() {
        const track = document.getElementById('testiTrack');
        if (!track) return;
        const cards = track.querySelectorAll('.testi-card');
        const prev = document.getElementById('testiPrev');
        const next = document.getElementById('testiNext');
        const dotsWrap = document.getElementById('testiDots');
        let index = 0;

        // build dots
        cards.forEach((_, i) => {
            const d = document.createElement('button');
            d.className = 'car-dot';
            d.setAttribute('role', 'tab');
            d.setAttribute('aria-label', 'Testimonial ' + (i + 1));
            d.addEventListener('click', () => goTo(i));
            dotsWrap.appendChild(d);
        });
        const dotEls = dotsWrap.querySelectorAll('.car-dot');

        const goTo = (i) => {
            index = (i + cards.length) % cards.length;
            track.style.setProperty('--slide', index);
            track.style.transform = 'translateX(calc(-1 * var(--slide) * (100% + 26px)))';
            dotEls.forEach((d, j) => d.classList.toggle('active', j === index));
        };
        if (next) next.addEventListener('click', () => goTo(index + 1));
        if (prev) prev.addEventListener('click', () => goTo(index - 1));
        goTo(0);

        // auto-advance (pause on hover)
        let timer = setInterval(() => goTo(index + 1), 6000);
        track.addEventListener('mouseenter', () => clearInterval(timer));
        track.addEventListener('mouseleave', () => timer = setInterval(() => goTo(index + 1), 6000));
    }

    // Smooth scroll for in-page anchors
    function initSmoothAnchors() {
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', function (e) {
                const id = this.getAttribute('href');
                if (id.length > 1) {
                    const target = document.querySelector(id);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initReveal();
        initCounters();
        initBackToTop();
        initCarousel();
        initSmoothAnchors();
        initUnifiedMicroInteractions();
    });
})();

// ============================================================
// DASHBOARD SHELL  (sidebar drawer, brand, bottom nav)
// Enhances non-dashboard + dashboard pages without touching backend.
// ============================================================
// ============================================================
// DASHBOARD SHELL  (sidebar drawer, brand, bottom nav)
// Frontend-only. Enhances dashboards without touching backend.
// ============================================================
(function initDashboard() {
    const role = (document.body.getAttribute('data-role') || '').toLowerCase();
    const page = (document.body.getAttribute('data-page') || '').toLowerCase();
    const isDashboard = document.body.classList.contains('dashboard-page');

    if (isDashboard) injectSidebarBrand(role);

    // Mobile slide-out drawer + bottom nav only on dashboard pages
    if (isDashboard) {
        buildMobileBackdrop();
        wireSidebarToggle();
        buildMobileNav(role, page);
    }
})();

// Inject the brand / logo at the top of the dashboard sidebar
function injectSidebarBrand(role) {
    const side = document.querySelector('.dashboard-sidebar');
    if (!side || side.querySelector('.sidebar-brand')) return;
    const logoEl = document.querySelector('.logo-image');
    const logo = logoEl ? logoEl.getAttribute('src') : '/assets/images/logo.jpeg';
    const brand = document.createElement('div');
    brand.className = 'sidebar-brand';
    brand.innerHTML =
        '<img class="sb-logo" src="' + logo + '" alt="SkillSeek logo" onerror="this.style.display=\'none\'">' +
        '<span class="sb-name">Skill<span>Seek</span></span>';
    side.insertBefore(brand, side.firstChild);

    // Separate the dashboard-hamburger-bearing header: mark it so the toggle works
    // even though the .header-nav is hidden on dashboard pages.
    const toggle = document.querySelector('.mobile-toggle');
    if (toggle) toggle.classList.add('dashboard-toggle');
}

// Build the full-screen backdrop used by the slide-out sidebar
function buildMobileBackdrop() {
    if (document.getElementById('mobileBackdrop')) return;
    const b = document.createElement('div');
    b.id = 'mobileBackdrop';
    document.body.appendChild(b);
}

// Wire the header hamburger to open/close the slide-out sidebar (mobile/tablet)
function wireSidebarToggle() {
    const closeDrawer = function () {
        document.body.classList.remove('sidebar-open');
        const backdrop = document.getElementById('mobileBackdrop');
        if (backdrop) backdrop.classList.remove('active');
        const t = document.querySelector('.mobile-toggle');
        if (t) { t.classList.remove('active'); t.setAttribute('aria-expanded', 'false'); }
    };

    const toggle = document.querySelector('.mobile-toggle');
    if (toggle) {
        toggle.addEventListener('click', function () {
            const open = document.body.classList.toggle('sidebar-open');
            this.classList.toggle('active', open);
            this.setAttribute('aria-expanded', open ? 'true' : 'false');
            const backdrop = document.getElementById('mobileBackdrop');
            if (backdrop) backdrop.classList.toggle('active', open);
        });
    }

    const backdrop = document.getElementById('mobileBackdrop');
    if (backdrop) backdrop.addEventListener('click', closeDrawer);

    // Close drawer after choosing a destination on small screens
    document.addEventListener('click', function (e) {
        if (e.target.closest('.dashboard-sidebar a') && window.innerWidth <= 900) closeDrawer();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDrawer();
    });
}

// Build the mobile bottom navigation (role-aware quick links)
function buildMobileNav(role, page, isDashboard) {
    if (document.querySelector('.mob-nav')) return;

    const links = role === 'employer'
        ? [
            { href: '/employer/dashboard.php', icon: 'fa-tachometer-alt', label: 'Home' },
            { href: '/employer/post_job.php', icon: 'fa-plus-circle', label: 'Post' },
            { href: '/employer/my_jobs.php', icon: 'fa-briefcase', label: 'Jobs' },
            { href: '/employer/applications.php', icon: 'fa-users', label: 'Apps' },
            { href: '/employer/talent.php', icon: 'fa-user-graduate', label: 'Talent' }
        ]
        : [
            { href: '/student/dashboard.php', icon: 'fa-tachometer-alt', label: 'Home' },
            { href: '/student/available_jobs.php', icon: 'fa-search', label: 'Jobs' },
            { href: '/student/my_applications.php', icon: 'fa-file-alt', label: 'Apps' },
            { href: '/student/saved_jobs.php', icon: 'fa-bookmark', label: 'Saved' },
            { href: profileHref(page), icon: 'fa-user', label: 'Profile' }
        ];

    const nav = document.createElement('nav');
    nav.className = 'mob-nav';
    nav.setAttribute('aria-label', 'Mobile navigation');
    const inner = document.createElement('div');
    inner.className = 'mn-inner';

    links.forEach(function (l) {
        const a = document.createElement('a');
        a.href = l.href;
        a.innerHTML = '<span class="m-ico"><i class="fas ' + l.icon + '"></i></span><span>' + l.label + '</span>';
        const file = l.href.split('/').pop();
        if (isMatch(page, file)) a.classList.add('active');
        inner.appendChild(a);
    });

    nav.appendChild(inner);
    document.body.appendChild(nav);
}

// Profile link depends on role (student has a dedicated profile page)
function profileHref(page) {
    // The shared settings page lives at the root; simplest is to keep it consistent.
    return '/profile.php';
}

// Determine if the mob-nav item matches the current page
function isMatch(page, file) {
    if (!page) return false;
    if (file === 'dashboard.php' || file === 'index.php') {
        return page === 'dashboard.php' || page === 'index.php';
    }
    // Compare by filename (e.g. my_applications.php, available_jobs.php ...)
    return page === file;
}
function initUnifiedMicroInteractions() {

    // --- 1) Ripple on any button / nav / action element ---
    const RIPPLEABLE = '.btn, .action-btn, .sidebar-nav a, .nav-list a, .carousel-btn, .car-dot, .mobile-toggle, .user-btn, .bookmark-btn, .talent-card a.btn';
    document.addEventListener('click', function (e) {
        const el = e.target.closest(RIPPLEABLE);
        if (!el || el.disabled || el.classList.contains('disabled') || el.classList.contains('is-loading')) return;
        if (el.classList.contains('car-dot')) return; // dots have own active animation
        const rect = el.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height) * 2;
        const ripple = document.createElement('span');
        ripple.className = 'ripple';
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
        ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
        el.appendChild(ripple);
        setTimeout(() => ripple.remove(), 650);
    }, { capture: true });

    // --- 2) Auto LOADING state on real submit buttons ---
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        // Respect native + custom validation
        if (form.dataset.noLoading === 'true') return;
        const submitter = e.submitter ||
            form.querySelector('button[type="submit"], input[type="submit"]');
        if (!submitter) return;
        if (form.checkValidity && !form.checkValidity()) return;
        // Skip if something else already loading (prevents double-fire)
        if (form.dataset.loading === 'true') return;
        form.dataset.loading = 'true';
        setTimeout(() => {
            if (form.dataset.loading === 'true') {
                addButtonLoading(submitter);
            }
        }, 120);
    });

    // --- 3) Progress feedback helper (drag-on-page load) ---
    window.addButtonLoading = function (btn) {
        if (!btn || btn.classList.contains('is-loading')) return;
        if (btn.dataset.skipLoading === 'true') return;
        btn.classList.add('is-loading');
        btn.setAttribute('aria-busy', 'true');
        btn.disabled = true;
        return {
            done: function (success) {
                btn.classList.remove('is-loading');
                btn.disabled = false;
                btn.removeAttribute('aria-busy');
                if (success) {
                    btn.classList.remove('is-success', 'is-error');
                    void btn.offsetWidth;
                    btn.classList.add('is-success');
                    setTimeout(() => btn.classList.remove('is-success'), 1600);
                }
            },
            error: function () {
                btn.classList.remove('is-loading');
                btn.disabled = false;
                btn.removeAttribute('aria-busy');
                btn.classList.remove('is-success', 'is-error');
                void btn.offsetWidth;
                btn.classList.add('is-error');
                setTimeout(() => btn.classList.remove('is-error'), 1800);
            }
        };
    };

    // --- 4) Success / error helpers on any element ---
    window.flashSuccess = function (btn, message) {
        if (btn) { const h = window.addButtonLoading(btn); h && h.done(true); }
        window.showToast(message, 'success');
    };
    window.flashError = function (btn, message) {
        if (btn) { const h = window.addButtonLoading(btn); h && h.error(); }
        window.showToast(message, 'error');
    };

    // --- 5) Skeleton placeholder toggles (for dynamic content) ---
    window.showSkeleton = function (container, cards) {
        if (!container) return;
        cards = cards || 3;
        container.classList.add('sk-placeholder');
        container.innerHTML = '';
        for (let i = 0; i < cards; i++) {
            const c = document.createElement('div');
            c.className = 'sk-card';
            c.innerHTML = '<div class="sk-line" style="width:38%;height:18px;margin-bottom:14px;"></div>' +
                          '<div class="sk-line" style="width:92%;"></div>' +
                          '<div class="sk-line" style="width:78%;margin-top:8px;"></div>';
            container.appendChild(c);
        }
    };
    window.hideSkeleton = function (container, html) {
        if (!container) return;
        container.classList.remove('sk-placeholder');
        if (html !== undefined) container.innerHTML = html;
    };
}

// ---- Convenience: page-load top progress bar ----
(function () {
    let bar = document.getElementById('topBar');
    if (!bar) {
        bar = document.createElement('div');
        bar.id = 'topBar';
        document.body.appendChild(bar);
    }
    bar.classList.add('loading');
    let w = 0;
    const step = () => {
        w = Math.min(w + Math.random() * 28 + 8, 92);
        bar.style.width = w + '%';
    };
    const timer = setInterval(step, 240);
    window.addEventListener('load', () => {
        clearInterval(timer);
        bar.style.width = '100%';
        setTimeout(() => {
            bar.classList.add('done');
            setTimeout(() => { bar.classList.remove('loading', 'done'); bar.style.width = '0%'; }, 450);
        }, 150);
    });
    // Safety fallback in case load already fired (e.g. run late)
    setTimeout(() => { if (document.readyState === 'complete' && w > 0) { clearInterval(timer); bar.style.width = '100%'; setTimeout(() => bar.classList.add('done'), 150); } }, 4000);
})();
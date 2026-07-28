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
// TOAST NOTIFICATIONS
// ============================================
function showToast(message, type = 'success') {
    // Create container if it doesn't exist
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            max-width: 350px;
        `;
        document.body.appendChild(toastContainer);
    }
    
    // Create toast
    const toast = document.createElement('div');
    const colors = {
        success: '#10B981',
        error: '#EF4444',
        warning: '#F59E0B',
        info: '#3B82F6'
    };
    
    toast.style.cssText = `
        background: ${colors[type] || colors.success};
        color: white;
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        animation: slideIn 0.3s ease;
        font-size: 0.875rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    `;
    
    // Add icon
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    toast.innerHTML = `<span>${icons[type] || 'ℹ️'}</span> ${message}`;
    
    toastContainer.appendChild(toast);
    
    // Auto remove
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
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
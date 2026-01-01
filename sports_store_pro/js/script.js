
// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', function () {
    // Mobile menu toggle
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navMenu = document.querySelector('.nav-menu');

    if (mobileToggle) {
        mobileToggle.addEventListener('click', function () {
            navMenu.classList.toggle('active');
        });
    }

    // User menu dropdown
    const userMenu = document.querySelector('.user-menu');
    if (userMenu) {
        userMenu.addEventListener('click', function (e) {
            e.stopPropagation();
            const dropdown = this.querySelector('.dropdown');
            dropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function () {
            const dropdown = userMenu.querySelector('.dropdown');
            if (dropdown) {
                dropdown.classList.remove('active');
            }
        });
    }

    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Product image zoom effect
    const productImages = document.querySelectorAll('.product-card img');
    productImages.forEach(img => {
        img.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.05)';
        });

        img.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1)';
        });
    });

    // Form validation and submission handling
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const submitButton = this.querySelector('button[type="submit"]');
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            // Validate required fields
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';
                    field.classList.add('error');
                } else {
                    field.style.borderColor = '#ddd';
                    field.classList.remove('error');
                }
            });

            // Special validation for login form
            if (this.action && this.action.includes('login.php')) {
                const username = this.querySelector('input[name="username"]');
                const password = this.querySelector('input[name="password"]');

                if (username && !username.value.trim()) {
                    isValid = false;
                    showError(username, 'يرجى إدخال اسم المستخدم');
                }

                if (password && !password.value.trim()) {
                    isValid = false;
                    showError(password, 'يرجى إدخال كلمة المرور');
                }
            }

            if (!isValid) {
                e.preventDefault();
                showToast('يرجى ملء جميع الحقول المطلوبة', 'error');
                return false;
            }

            // Show loading state for valid forms
            if (submitButton && isValid) {
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحميل...';
                submitButton.disabled = true;

                // Re-enable button after 10 seconds as fallback
                setTimeout(() => {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }, 10000);
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });

    // Quantity input validation
    const quantityInputs = document.querySelectorAll('input[type="number"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function () {
            if (this.value < 1) {
                this.value = 1;
            }
        });
    });

    // Search functionality
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        const searchInput = searchForm.querySelector('input[name="search"]');

        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    console.log('Searching for:', this.value);
                }, 300);
            });
        }
    }

    // Image lazy loading
    const images = document.querySelectorAll('img[data-src]');
    if (images.length > 0) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        });

        images.forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Shopping cart animation
    const addToCartButtons = document.querySelectorAll('.add-to-cart-form button');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });

    // Scroll to top button
    const scrollTopBtn = document.createElement('button');
    scrollTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    scrollTopBtn.className = 'scroll-top-btn';
    scrollTopBtn.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #e74c3c;
        color: white;
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        cursor: pointer;
        display: none;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        transition: all 0.3s;
    `;

    document.body.appendChild(scrollTopBtn);

    window.addEventListener('scroll', function () {
        if (window.pageYOffset > 300) {
            scrollTopBtn.style.display = 'block';
        } else {
            scrollTopBtn.style.display = 'none';
        }
    });

    scrollTopBtn.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Remove loading state on page load (in case of errors)
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        if (button.disabled) {
            button.disabled = false;
            const buttonText = button.getAttribute('data-original-text');
            if (buttonText) {
                button.innerHTML = buttonText;
            } else if (button.innerHTML.includes('جاري التحميل')) {
                button.innerHTML = 'تسجيل الدخول';
            }
        }
    });
});

// Utility functions
function formatPrice(price) {
    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: 'SAR'
    }).format(price);
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${type === 'success' ? '#27ae60' : '#e74c3c'};
        color: white;
        padding: 1rem 2rem;
        border-radius: 5px;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        box-shadow: 0 3px 10px rgba(0,0,0,0.3);
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

function showError(field, message) {
    field.style.borderColor = '#e74c3c';
    field.classList.add('error');

    // Remove existing error message
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }

    // Add new error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    errorDiv.style.cssText = `
        color: #e74c3c;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    `;

    field.parentNode.appendChild(errorDiv);

    // Remove error styling when user starts typing
    field.addEventListener('input', function () {
        this.style.borderColor = '#ddd';
        this.classList.remove('error');
        const errorMsg = this.parentNode.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.remove();
        }
    }, { once: true });
}

// Add CSS animations and styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }

    .lazy {
        opacity: 0;
        transition: opacity 0.3s;
    }

    .lazy.loaded {
        opacity: 1;
    }

    .user-menu .dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        display: none;
        min-width: 150px;
        z-index: 1000;
    }

    .user-menu .dropdown.active {
        display: block;
    }

    .user-menu .dropdown a {
        display: block;
        padding: 0.75rem 1rem;
        color: #333;
        text-decoration: none;
        border-bottom: 1px solid #eee;
        transition: background 0.3s;
    }

    .user-menu .dropdown a:last-child {
        border-bottom: none;
    }

    .user-menu .dropdown a:hover {
        background: #f8f9fa;
        color: #e74c3c;
    }

    .form-group input.error {
        border-color: #e74c3c !important;
        box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.1);
    }

    .error-message {
        color: #e74c3c;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }

    button[type="submit"]:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .scroll-top-btn:hover {
        background: #c0392b !important;
        transform: translateY(-2px);
    }

    .fa-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;

document.head.appendChild(style);

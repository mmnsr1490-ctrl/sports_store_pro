
// Professional Animations and Interactions
document.addEventListener('DOMContentLoaded', function () {
    // Initialize all animations and interactions
    initScrollAnimations();
    initParallaxEffects();
    initInteractiveElements();
    initLoadingAnimations();
    initFormEnhancements();
    initHeaderScroll();
    initProductHovers();
    initSmoothScrolling();
});

// Scroll-triggered animations
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    // Observe elements for animation
    document.querySelectorAll('.product-card, .category-card, .stat-card, .form-container').forEach(el => {
        el.classList.add('fade-in');
        observer.observe(el);
    });
}

// Parallax effects
function initParallaxEffects() {
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;

        const hero = document.querySelector('.hero');
        if (hero) {
            hero.style.transform = `translateY(${rate}px)`;
        }
    });
}

// Interactive hover effects
function initInteractiveElements() {
    // Product cards interactive effects
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Button ripple effect
    document.querySelectorAll('.btn, .cta-button').forEach(button => {
        button.addEventListener('click', function (e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
}

// Loading animations
function initLoadingAnimations() {
    // Form submission loading
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            const submitBtn = this.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;

                // Re-enable after 3 seconds (adjust as needed)
                setTimeout(() => {
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                }, 3000);
            }
        });
    });

    // AJAX loading states
    const addToCartForms = document.querySelectorAll('form[action*="add_to_cart"]');
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('[type="submit"]');

            if (submitBtn) {
                submitBtn.classList.add('loading');
                submitBtn.innerHTML = '<span class="loading-spinner"></span> جاري الإضافة...';
            }

            // Simulate AJAX request (replace with actual AJAX)
            setTimeout(() => {
                this.submit(); // Submit the form normally
            }, 1000);
        });
    });
}

// Form enhancements
function initFormEnhancements() {
    // Floating labels
    document.querySelectorAll('.form-group input, .form-group textarea').forEach(input => {
        input.addEventListener('focus', function () {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function () {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });

        // Check if already has value
        if (input.value) {
            input.parentElement.classList.add('focused');
        }
    });

    // Real-time validation feedback
    document.querySelectorAll('input[type="email"]').forEach(input => {
        input.addEventListener('blur', function () {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('error');
                showValidationMessage(this, 'يرجى إدخال بريد إلكتروني صحيح');
            } else {
                this.classList.remove('error');
                hideValidationMessage(this);
            }
        });
    });
}

// Header scroll effects
function initHeaderScroll() {
    const header = document.querySelector('header');
    let lastScrollTop = 0;

    window.addEventListener('scroll', () => {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // Add scrolled class for styling
        if (scrollTop > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        // Hide/show header based on scroll direction
        if (scrollTop > lastScrollTop && scrollTop > 200) {
            header.style.transform = 'translateY(-100%)';
        } else {
            header.style.transform = 'translateY(0)';
        }

        lastScrollTop = scrollTop;
    });
}

// Product hover effects
function initProductHovers() {
    document.querySelectorAll('.product-card img').forEach(img => {
        const card = img.closest('.product-card');

        card.addEventListener('mouseenter', function () {
            img.style.transform = 'scale(1.1)';
            img.style.transition = 'transform 0.5s ease';
        });

        card.addEventListener('mouseleave', function () {
            img.style.transform = 'scale(1)';
        });
    });
}

// Smooth scrolling for anchors
function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
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
}

// Utility functions
function showValidationMessage(input, message) {
    let errorDiv = input.parentElement.querySelector('.validation-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'validation-message error';
        input.parentElement.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

function hideValidationMessage(input) {
    const errorDiv = input.parentElement.querySelector('.validation-message');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
}

// Toast notifications
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="toast-close">&times;</button>
    `;

    document.body.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);

    // Auto remove
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 5000);

    // Manual close
    toast.querySelector('.toast-close').addEventListener('click', () => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    });
}

// Shopping cart animations
function animateCartIcon() {
    const cartIcon = document.querySelector('.cart-icon');
    if (cartIcon) {
        cartIcon.classList.add('bounce');
        setTimeout(() => {
            cartIcon.classList.remove('bounce');
        }, 500);
    }
}

// Initialize cart count animation
function updateCartCount(count) {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = count;
        cartCount.classList.add('pulse');
        setTimeout(() => {
            cartCount.classList.remove('pulse');
        }, 1000);
    }
}

// Search functionality enhancement
function initSearchEnhancements() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Implement live search here
                console.log('Searching for:', this.value);
            }, 500);
        });
    }
}

// Add CSS for animations
const animationStyles = `
<style>
.fade-in {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-in.animate-in {
    opacity: 1;
    transform: translateY(0);
}

.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.4);
    pointer-events: none;
    animation: ripple-animation 0.6s ease-out;
}

@keyframes ripple-animation {
    to {
        transform: scale(2);
        opacity: 0;
    }
}

.loading-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

.bounce {
    animation: bounce 0.5s ease-in-out;
}

@keyframes bounce {
    0%, 20%, 60%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    80% { transform: translateY(-5px); }
}

.pulse {
    animation: pulse 1s ease-in-out;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}

.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    padding: 1rem;
    transform: translateX(400px);
    transition: transform 0.3s ease;
    z-index: 10000;
    min-width: 300px;
}

.toast.show {
    transform: translateX(0);
}

.toast-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.toast-success {
    border-left: 4px solid #27ae60;
}

.toast-error {
    border-left: 4px solid #e74c3c;
}

.toast-close {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: #999;
}

.validation-message {
    font-size: 0.8rem;
    margin-top: 0.25rem;
    padding: 0.25rem 0;
}

.validation-message.error {
    color: #e74c3c;
}

.form-group.focused label {
    color: #3498db;
    transform: translateY(-20px) scale(0.9);
}

.form-group input.error,
.form-group textarea.error {
    border-color: #e74c3c;
    background-color: #fff5f5;
}
</style>
`;

document.head.insertAdjacentHTML('beforeend', animationStyles);

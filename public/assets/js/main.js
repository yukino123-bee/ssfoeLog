/**
 * SSFO eLog - Main JavaScript File
 * Enhanced UI/UX interactions and functionality
 */

(function () {
    'use strict';

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        initFormValidation();
        initSmoothScrolling();
        initTableEnhancements();
        initFileInputEnhancements();
        initButtonAnimations();
        initSearchFunctionality();
        initAccessibility();
        initFormSubmission();
        initScrollAnimations();
        initAdminSidebar();
    }

    /**
     * Form Validation Enhancement
     */
    function initFormValidation() {
        const forms = document.querySelectorAll('form');

        forms.forEach(form => {
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

            inputs.forEach(input => {
                // Add validation on blur
                input.addEventListener('blur', function () {
                    validateField(this);
                });

                // Remove error state on input
                input.addEventListener('input', function () {
                    if (this.classList.contains('error')) {
                        this.classList.remove('error');
                        const errorMsg = this.parentElement.querySelector('.error-message');
                        if (errorMsg) {
                            errorMsg.remove();
                        }
                    }
                });
            });

            // Form submission validation
            form.addEventListener('submit', function (e) {
                let isValid = true;

                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    // Focus on first error field
                    const firstError = form.querySelector('.error');
                    if (firstError) {
                        firstError.focus();
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        });
    }

    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Required field validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'This field is required';
        }

        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address';
            }
        }

        // Phone validation (basic)
        if (field.type === 'tel' && value) {
            const phoneRegex = /^09\d{9}$/;
            if (!phoneRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid Philippine mobile number (e.g. 09171234567)';
            }
        }

        // Number validation
        if (field.type === 'number' && value) {
            const num = parseFloat(value);
            if (isNaN(num)) {
                isValid = false;
                errorMessage = 'Please enter a valid number';
            }
            if (field.hasAttribute('min') && num < parseFloat(field.getAttribute('min'))) {
                isValid = false;
                errorMessage = `Value must be at least ${field.getAttribute('min')}`;
            }
            if (field.hasAttribute('max') && num > parseFloat(field.getAttribute('max'))) {
                isValid = false;
                errorMessage = `Value must be at most ${field.getAttribute('max')}`;
            }
        }

        // File validation
        if (field.type === 'file' && field.hasAttribute('required')) {
            if (!field.files || field.files.length === 0) {
                isValid = false;
                errorMessage = 'Please select a file';
            } else {
                // Check file type
                const accept = field.getAttribute('accept');
                if (accept) {
                    const allowedTypes = accept.split(',').map(type => type.trim().toLowerCase());
                    const file = field.files[0];
                    const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                    const fileType = file.type.toLowerCase();
                    
                    const isValidType = allowedTypes.some(type => {
                        if (type === fileExtension || type === fileType) {
                            return true;
                        }
                        // Handle wildcards like image/*, video/*, audio/*
                        if (type.endsWith('/*')) {
                            const baseType = type.split('/')[0];
                            return fileType.startsWith(baseType + '/');
                        }
                        return false;
                    });
                    
                    if (!isValidType) {
                        isValid = false;
                        errorMessage = `File type not allowed. Accepted types: ${accept}`;
                    }
                }
            }
        }

        // Update UI
        if (!isValid) {
            field.classList.add('error');
            showFieldError(field, errorMessage);
        } else {
            field.classList.remove('error');
            removeFieldError(field);
        }

        return isValid;
    }

    function showFieldError(field, message) {
        removeFieldError(field);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        field.parentElement.appendChild(errorDiv);
    }

    function removeFieldError(field) {
        const errorMsg = field.parentElement.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.remove();
        }
    }

    /**
     * Smooth Scrolling
     */
    function initSmoothScrolling() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href.length > 1) {
                    const target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    }

    /**
     * Table Enhancements
     */
    function initTableEnhancements() {
        const tables = document.querySelectorAll('table');

        tables.forEach(table => {
            // Add row hover effect
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseenter', function () {
                    this.style.transition = 'all 0.15s ease';
                });
            });

            // Make table responsive with horizontal scroll indicator
            const tableContainer = table.closest('.applications-table');
            if (tableContainer && table.scrollWidth > tableContainer.clientWidth) {
                tableContainer.style.position = 'relative';

                // Add scroll indicator
                const indicator = document.createElement('div');
                indicator.className = 'scroll-indicator';
                indicator.innerHTML = '← Scroll →';
                indicator.style.cssText = `
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    background: rgba(0, 0, 0, 0.7);
                    color: white;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 0.75rem;
                    pointer-events: none;
                    opacity: 0;
                    transition: opacity 0.3s;
                `;
                tableContainer.appendChild(indicator);

                let hideTimeout;
                tableContainer.addEventListener('scroll', function () {
                    clearTimeout(hideTimeout);
                    indicator.style.opacity = '1';
                    hideTimeout = setTimeout(() => {
                        indicator.style.opacity = '0';
                    }, 2000);
                });
            }
        });
    }

    /**
     * File Input Enhancements
     */
    function initFileInputEnhancements() {
        const fileInputs = document.querySelectorAll('input[type="file"]');

        fileInputs.forEach(input => {
            // Show selected file name
            input.addEventListener('change', function () {
                const files = this.files;
                if (files.length > 0) {
                    const fileName = files.length === 1
                        ? files[0].name
                        : `${files.length} files selected`;

                    // Remove existing file name display
                    const existingDisplay = this.parentElement.querySelector('.file-name-display');
                    if (existingDisplay) {
                        existingDisplay.remove();
                    }

                    // Add file name display
                    const display = document.createElement('div');
                    display.className = 'file-name-display';
                    display.textContent = fileName;
                    display.style.cssText = `
                        margin-top: 8px;
                        padding: 8px 12px;
                        background: #f3f4f6;
                        border-radius: 6px;
                        font-size: 0.875rem;
                        color: #374151;
                        display: inline-block;
                    `;
                    this.parentElement.appendChild(display);
                } else {
                    const existingDisplay = this.parentElement.querySelector('.file-name-display');
                    if (existingDisplay) {
                        existingDisplay.remove();
                    }
                }
            });
        });
    }

    /**
     * Button Animations
     */
    function initButtonAnimations() {
        const buttons = document.querySelectorAll('button, .btn, a.btn');

        buttons.forEach(button => {
            // Ripple effect on click
            button.addEventListener('click', function (e) {
                if (this.disabled) return;

                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.5);
                    left: ${x}px;
                    top: ${y}px;
                    transform: scale(0);
                    animation: ripple 0.6s ease-out;
                    pointer-events: none;
                `;

                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);

                setTimeout(() => ripple.remove(), 600);
            });
        });

        // Add ripple animation CSS
        if (!document.querySelector('#ripple-animation')) {
            const style = document.createElement('style');
            style.id = 'ripple-animation';
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }

    /**
     * Search Functionality Enhancement
     */
    function initSearchFunctionality() {
        const searchForms = document.querySelectorAll('.search-form');

        searchForms.forEach(form => {
            const input = form.querySelector('input[type="text"]');
            const table = form.closest('.admin-page')?.querySelector('table');

            if (input && table) {
                input.addEventListener('input', function () {
                    const searchTerm = this.value.toLowerCase().trim();
                    const rows = table.querySelectorAll('tbody tr');

                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Show "no results" message if needed
                    const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
                    let noResults = table.parentElement.querySelector('.no-results');

                    if (visibleRows.length === 0 && searchTerm) {
                        if (!noResults) {
                            noResults = document.createElement('div');
                            noResults.className = 'no-results';
                            noResults.textContent = 'No results found';
                            noResults.style.cssText = `
                                padding: 40px;
                                text-align: center;
                                color: #6b7280;
                                font-size: 1rem;
                            `;
                            table.parentElement.appendChild(noResults);
                        }
                    } else if (noResults) {
                        noResults.remove();
                    }
                });
            }
        });
    }

    /**
     * Accessibility Enhancements
     */
    function initAccessibility() {
        // Add skip to main content link
        if (!document.querySelector('.skip-link')) {
            const skipLink = document.createElement('a');
            skipLink.href = '#main-content';
            skipLink.className = 'skip-link';
            skipLink.textContent = 'Skip to main content';
            skipLink.style.cssText = `
                position: absolute;
                top: -40px;
                left: 0;
                background: #000;
                color: #fff;
                padding: 8px;
                text-decoration: none;
                z-index: 100;
            `;
            skipLink.addEventListener('focus', function () {
                this.style.top = '0';
            });
            skipLink.addEventListener('blur', function () {
                this.style.top = '-40px';
            });
            document.body.insertBefore(skipLink, document.body.firstChild);
        }

        // Add ARIA labels to buttons without text
        document.querySelectorAll('button:not([aria-label]):empty').forEach(button => {
            if (button.querySelector('img')) {
                button.setAttribute('aria-label', 'Button');
            }
        });

        // Enhance keyboard navigation for tables
        const tables = document.querySelectorAll('table');
        tables.forEach(table => {
            const cells = table.querySelectorAll('td, th');
            cells.forEach((cell, index) => {
                cell.setAttribute('tabindex', '0');
                cell.addEventListener('keydown', function (e) {
                    const totalCells = cells.length;
                    let nextIndex;

                    switch (e.key) {
                        case 'ArrowRight':
                            nextIndex = (index + 1) % totalCells;
                            break;
                        case 'ArrowLeft':
                            nextIndex = (index - 1 + totalCells) % totalCells;
                            break;
                        case 'ArrowDown':
                            const cols = table.querySelectorAll('thead th').length;
                            nextIndex = Math.min(index + cols, totalCells - 1);
                            break;
                        case 'ArrowUp':
                            const cols2 = table.querySelectorAll('thead th').length;
                            nextIndex = Math.max(index - cols2, 0);
                            break;
                        default:
                            return;
                    }

                    e.preventDefault();
                    cells[nextIndex].focus();
                });
            });
        });
    }

    // Add error field styling
    if (!document.querySelector('#form-error-styles')) {
        const style = document.createElement('style');
        style.id = 'form-error-styles';
        style.textContent = `
            input.error,
            select.error,
            textarea.error {
                border-color: #dc2626 !important;
                box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Form Submission Handler
     */
    function initFormSubmission() {
        const forms = document.querySelectorAll('form[action*="submit_request"]');

        forms.forEach(form => {
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.textContent;
                submitButton.disabled = true;
                submitButton.textContent = 'Submitting...';

                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Show success message
                        showNotification('Request submitted successfully! Request ID: ' + result.request_id, 'success');
                        form.reset();

                        // Redirect to track page after 2 seconds
                        setTimeout(() => {
                            window.location.href = 'track.php';
                        }, 2000);
                    } else {
                        showNotification(result.message || 'Failed to submit request. Please try again.', 'error');
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                    }
                } catch (error) {
                    showNotification('An error occurred. Please try again.', 'error');
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }
            });
        });
    }

    /**
     * Show notification message
     */
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existing = document.querySelector('.form-notification');
        if (existing) {
            existing.remove();
        }

        const notification = document.createElement('div');
        notification.className = `form-notification ${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            max-width: 400px;
            animation: slideIn 0.3s ease;
            background: ${type === 'success' ? '#d1fae5' : '#fee2e2'};
            color: ${type === 'success' ? '#065f46' : '#991b1b'};
            border: 1px solid ${type === 'success' ? '#a7f3d0' : '#fecaca'};
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    /**
     * Scroll Animations
     */
    function initScrollAnimations() {
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        const animatedElements = document.querySelectorAll('.animate-fade-up');
        animatedElements.forEach(el => observer.observe(el));

        // Add CSS for animations dynamically
        if (!document.querySelector('#scroll-animation-styles')) {
            const style = document.createElement('style');
            style.id = 'scroll-animation-styles';
            style.textContent = `
                .animate-fade-up {
                    opacity: 0;
                    transform: translateY(20px);
                    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
                }
                .animate-fade-up.is-visible {
                    opacity: 1;
                    transform: translateY(0);
                }
            `;
            document.head.appendChild(style);
        }
    }

    /**
     * Admin Sidebar Toggle
     */
    function initAdminSidebar() {
        const toggle = document.getElementById('sidebar-toggle');
        const sidebar = document.querySelector('.admin-sidebar');
        const overlay = document.querySelector('.sidebar-overlay');

        if (toggle && sidebar) {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                if (overlay) overlay.classList.toggle('active');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            });
        }
    }

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
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
    `;
    document.head.appendChild(style);

})();

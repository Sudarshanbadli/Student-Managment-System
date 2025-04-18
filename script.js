// File: assets/js/script.js
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar on mobile
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    // Role switcher on login/register pages
    const roleButtons = document.querySelectorAll('.role-btn');
    const studentFields = document.querySelector('.student-fields');
    const teacherFields = document.querySelector('.teacher-fields');
    
    if (roleButtons.length > 0) {
        roleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const role = this.getAttribute('data-role');
                
                // Update active button
                roleButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Show/hide fields based on role
                if (studentFields && teacherFields) {
                    if (role === 'student') {
                        studentFields.classList.add('active');
                        teacherFields.classList.remove('active');
                    } else if (role === 'teacher') {
                        studentFields.classList.remove('active');
                        teacherFields.classList.add('active');
                    } else {
                        studentFields.classList.remove('active');
                        teacherFields.classList.remove('active');
                    }
                }
            });
        });
    }
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'red';
                    
                    // Add error message if not exists
                    if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('error-message')) {
                        const errorMessage = document.createElement('span');
                        errorMessage.className = 'error-message';
                        errorMessage.style.color = 'red';
                        errorMessage.style.fontSize = '0.8rem';
                        errorMessage.textContent = 'This field is required';
                        field.parentNode.insertBefore(errorMessage, field.nextSibling);
                    }
                } else {
                    field.style.borderColor = '';
                    
                    // Remove error message if exists
                    if (field.nextElementSibling && field.nextElementSibling.classList.contains('error-message')) {
                        field.nextElementSibling.remove();
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                
                // Scroll to first error
                const firstError = form.querySelector('[required]:invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
    
    // Password confirmation validation
    const passwordFields = document.querySelectorAll('input[type="password"]');
    passwordFields.forEach(field => {
        field.addEventListener('input', function() {
            const confirmPassword = document.getElementById('confirm_password') || 
                                   document.getElementById('modal_confirm_password');
            
            if (confirmPassword && this.id === 'password') {
                if (confirmPassword.value && this.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Passwords do not match');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
            
            if (confirmPassword && this.id === 'confirm_password') {
                const password = document.getElementById('password') || 
                                 document.getElementById('modal_password');
                
                if (password.value !== this.value) {
                    this.setCustomValidity('Passwords do not match');
                } else {
                    this.setCustomValidity('');
                }
            }
        });
    });
    
    // Dark mode toggle (example - would need localStorage to persist)
    const darkModeToggle = document.createElement('div');
    darkModeToggle.className = 'dark-mode-toggle';
    darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    darkModeToggle.style.position = 'fixed';
    darkModeToggle.style.bottom = '20px';
    darkModeToggle.style.right = '20px';
    darkModeToggle.style.width = '50px';
    darkModeToggle.style.height = '50px';
    darkModeToggle.style.borderRadius = '50%';
    darkModeToggle.style.backgroundColor = 'var(--primary-color)';
    darkModeToggle.style.color = 'white';
    darkModeToggle.style.display = 'flex';
    darkModeToggle.style.justifyContent = 'center';
    darkModeToggle.style.alignItems = 'center';
    darkModeToggle.style.cursor = 'pointer';
    darkModeToggle.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
    darkModeToggle.style.zIndex = '999';
    
    darkModeToggle.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        
        if (document.body.classList.contains('dark-mode')) {
            this.innerHTML = '<i class="fas fa-sun"></i>';
            localStorage.setItem('darkMode', 'enabled');
        } else {
            this.innerHTML = '<i class="fas fa-moon"></i>';
            localStorage.setItem('darkMode', 'disabled');
        }
    });
    
    // Check for saved dark mode preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }
    
    document.body.appendChild(darkModeToggle);
    
    // Dark mode styles
    const darkModeStyles = document.createElement('style');
    darkModeStyles.textContent = `
        .dark-mode {
            --light-color: #212529;
            --dark-color: #f8f9fa;
            --gray-color: #adb5bd;
            
            background: #121212;
            color: #f8f9fa;
        }
        
        .dark-mode .sidebar {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        }
        
        .dark-mode .topbar,
        .dark-mode .footer,
        .dark-mode .welcome-section,
        .dark-mode .action-card,
        .dark-mode .profile-card,
        .dark-mode .data-table,
        .dark-mode .attendance-filter,
        .dark-mode .attendance-table,
        .dark-mode .timetable-card,
        .dark-mode .media-card,
        .dark-mode .modal-content {
            background-color: #343a40;
            color: #f8f9fa;
        }
        
        .dark-mode .sidebar-nav a {
            color: #f8f9fa;
        }
        
        .dark-mode .form-group input,
        .dark-mode .form-group select,
        .dark-mode .form-group textarea {
            background-color: #495057;
            border-color: #495057;
            color: #f8f9fa;
        }
        
        .dark-mode .form-group input:focus,
        .dark-mode .form-group select:focus,
        .dark-mode .form-group textarea:focus {
            background-color: #495057;
            border-color: var(--primary-color);
        }
        
        .dark-mode .alert {
            background-color: rgba(0, 0, 0, 0.3);
        }
    `;
    
    document.head.appendChild(darkModeStyles);
    
    // Initialize tooltips
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            
            const rect = this.getBoundingClientRect();
            tooltip.style.position = 'absolute';
            tooltip.style.left = `${rect.left + rect.width / 2}px`;
            tooltip.style.top = `${rect.top - 40}px`;
            tooltip.style.transform = 'translateX(-50%)';
            tooltip.style.backgroundColor = 'var(--dark-color)';
            tooltip.style.color = 'white';
            tooltip.style.padding = '5px 10px';
            tooltip.style.borderRadius = '5px';
            tooltip.style.fontSize = '0.8rem';
            tooltip.style.zIndex = '1000';
            tooltip.style.whiteSpace = 'nowrap';
            
            document.body.appendChild(tooltip);
            
            this.addEventListener('mouseleave', function() {
                tooltip.remove();
            });
        });
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});
/**
 * SmartHUB Core JavaScript Engine
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Dark Mode Toggle & Sync
    const body = document.body;
    const themeToggle = document.querySelector('.dark-mode-toggle');
    const savedTheme = localStorage.getItem('theme') || 'light';
    
    // Initial sync
    if (savedTheme === 'dark') {
        body.classList.add('dark');
        if (themeToggle) {
            themeToggle.innerHTML = '<i class="fas fa-sun text-warning"></i>';
        }
    } else {
        body.classList.remove('dark');
        if (themeToggle) {
            themeToggle.innerHTML = '<i class="fas fa-moon text-secondary"></i>';
        }
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark');
            const isDark = body.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            
            themeToggle.innerHTML = isDark 
                ? '<i class="fas fa-sun text-warning"></i>' 
                : '<i class="fas fa-moon text-secondary"></i>';
        });
    }

    // 2. Sidebar Mobile Toggle
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    }

    // Close sidebar on outer click for mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth < 992 && sidebar && sidebar.classList.contains('show')) {
            if (!sidebar.contains(e.target) && sidebarToggle && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        }
    });

    // 3. Dynamic Toast Alerts
    window.showToast = function(message, type = 'info') {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `custom-toast ${type} animate-fade-in`;
        
        let icon = 'fa-info-circle text-info';
        if (type === 'success') icon = 'fa-check-circle text-success';
        if (type === 'error') icon = 'fa-exclamation-circle text-danger';
        if (type === 'warning') icon = 'fa-exclamation-triangle text-warning';

        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            <div class="toast-body flex-grow-1">${message}</div>
            <button type="button" class="btn-close ms-2" onclick="this.parentElement.remove()" style="font-size: 0.75rem; background: none; border: none; color: var(--text-secondary);">&times;</button>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideIn 0.3s ease reverse forwards';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    };

    // Auto-display server-side flash notifications
    const serverFlashes = document.querySelectorAll('.server-flash');
    serverFlashes.forEach(flash => {
        const msg = flash.getAttribute('data-message');
        const type = flash.getAttribute('data-type');
        if (msg && type) {
            window.showToast(msg, type);
        }
    });
});

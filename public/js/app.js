/**
 * PASIGNAE - Diocese of Pasig Church Web Portal
 */

document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initAlerts();
    initFormLoading();
});

function initMobileMenu() {
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    const sidebar = document.getElementById('sidebar');

    btn?.addEventListener('click', () => {
        menu?.classList.toggle('hidden');
    });

    document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
        sidebar?.classList.toggle('-translate-x-full');
    });
}

function initAlerts() {
    document.querySelectorAll('[data-dismiss]').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('[role="alert"]')?.remove();
        });
    });

    setTimeout(() => {
        document.querySelectorAll('[role="alert"]').forEach(el => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 5000);
}

function initFormLoading() {
    document.querySelectorAll('form[data-loading]').forEach(form => {
        form.addEventListener('submit', () => {
            const btn = form.querySelector('[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner inline-block mr-2"></span> Processing...';
            }
        });
    });
}

function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
}

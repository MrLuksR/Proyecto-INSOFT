document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const modulesPanel = document.querySelector('.modules-panel');
    const modulesToggle = document.getElementById('modulesToggle');
    const contentLayout = document.querySelector('.content-layout');
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');
    const toast = document.getElementById('portalToast');
    let toastTimeout;

    function showToast(message) {
        if (!toast) return;
        toast.textContent = message;
        toast.classList.add('show');
        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => toast.classList.remove('show'), 3200);
    }

    sidebarToggle?.addEventListener('click', () => {
        sidebar?.classList.toggle('collapsed');
        const collapsed = sidebar?.classList.contains('collapsed');
        sidebarToggle.setAttribute('aria-label', collapsed ? 'Expandir menú' : 'Contraer menú');
    });

    modulesToggle?.addEventListener('click', () => {
        if (window.matchMedia('(max-width: 900px)').matches) {
            modulesPanel?.classList.toggle('mobile-open');
        } else {
            modulesPanel?.classList.toggle('collapsed');
            contentLayout?.classList.toggle('modules-hidden');
        }
    });

    profileButton?.addEventListener('click', (event) => {
        event.stopPropagation();
        profileDropdown?.classList.toggle('open');
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.user-menu')) profileDropdown?.classList.remove('open');
    });

    document.querySelectorAll('[data-not-configured]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const section = link.dataset.notConfigured || 'Esta función';
            showToast(`${section}: falta vincular esta opción con el módulo correspondiente del sistema.`);
        });
    });

    document.getElementById('languageButton')?.addEventListener('click', () => {
        showToast('El portal está configurado en español.');
    });

    document.getElementById('notificationButton')?.addEventListener('click', () => {
        showToast('No hay notificaciones nuevas para mostrar.');
    });

    window.addEventListener('resize', () => {
        if (!window.matchMedia('(max-width: 900px)').matches) {
            modulesPanel?.classList.remove('mobile-open');
        } else {
            modulesPanel?.classList.remove('collapsed');
            contentLayout?.classList.remove('modules-hidden');
        }
    });
});

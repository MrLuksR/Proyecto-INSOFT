document.addEventListener('DOMContentLoaded', function () {
    /* Menú lateral para pantallas pequeñas */
    const sidebar = document.getElementById('portalSidebar');
    const toggle = document.getElementById('portalMenuToggle');

    if (sidebar && toggle) {
        toggle.addEventListener('click', function () {
            const abierto = sidebar.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', abierto ? 'true' : 'false');
        });

        document.addEventListener('click', function (event) {
            if (
                window.innerWidth <= 850 &&
                sidebar.classList.contains('is-open') &&
                !sidebar.contains(event.target) &&
                !toggle.contains(event.target)
            ) {
                sidebar.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 850) {
                sidebar.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* Menú desplegable del usuario */
    const userMenu = document.getElementById('userMenu');
    const userTrigger = document.getElementById('userMenuTrigger');
    const userDropdown = document.getElementById('userMenuDropdown');

    if (userMenu && userTrigger && userDropdown) {
        function abrirMenuUsuario() {
            userDropdown.hidden = false;
            userTrigger.setAttribute('aria-expanded', 'true');
        }

        function cerrarMenuUsuario() {
            userDropdown.hidden = true;
            userTrigger.setAttribute('aria-expanded', 'false');
        }

        userTrigger.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (userDropdown.hidden) {
                abrirMenuUsuario();
            } else {
                cerrarMenuUsuario();
            }
        });

        userDropdown.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        document.addEventListener('click', function (event) {
            if (!userMenu.contains(event.target)) {
                cerrarMenuUsuario();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !userDropdown.hidden) {
                cerrarMenuUsuario();
                userTrigger.focus();
            }
        });
    }
});


document.addEventListener("DOMContentLoaded", function () {
    "use strict";

    // ELEMENTOS PRINCIPALES

    const sidebar = document.querySelector(".sidebar");
    const collapseButton = document.querySelector(".sidebar-collapse");

    const profileMenu = document.querySelector(".user-menu");
    const profileButton = document.querySelector(".profile-chevron");
    const profileDropdown = document.querySelector(".profile-dropdown");

    const navLinks = document.querySelectorAll(
        '.sidebar .nav-link[href^="#"]'
    );

    // NOTIFICACIONES

    let toastTimer;

    let toast = document.querySelector(".toast");

    if (!toast) {
        toast = document.createElement("div");
        toast.className = "toast";
        toast.setAttribute("role", "status");
        toast.setAttribute("aria-live", "polite");
        document.body.appendChild(toast);
    }

    function showToast(message) {
        if (!message) return;

        clearTimeout(toastTimer);

        toast.textContent = message;
        toast.classList.add("show");

        toastTimer = setTimeout(function () {
            toast.classList.remove("show");
        }, 3000);
    }

    // CONTRAER / EXPANDIR BARRA LATERAL

    if (sidebar && collapseButton) {
        collapseButton.setAttribute("aria-label", "Contraer menú");
        collapseButton.setAttribute("aria-expanded", "true");

        collapseButton.addEventListener("click", function () {
            const isCollapsed = sidebar.classList.toggle("collapsed");

            collapseButton.setAttribute(
                "aria-expanded",
                String(!isCollapsed)
            );

            collapseButton.setAttribute(
                "aria-label",
                isCollapsed ? "Expandir menú" : "Contraer menú"
            );

            const icon = collapseButton.querySelector("i");

            if (icon) {
                icon.classList.toggle("fa-angles-left", !isCollapsed);
                icon.classList.toggle("fa-angles-right", isCollapsed);
                icon.classList.toggle("fa-chevron-left", false);
                icon.classList.toggle("fa-chevron-right", false);
            }

            // Guardar el estado en la página actual.
            // No requiere base de datos ni almacenamiento externo.
        });
    }

    // NAVEGACIÓN ENTRE SECCIONES

    function activateLink(selectedLink) {
        navLinks.forEach(function (link) {
            const isActive = link === selectedLink;

            link.classList.toggle("active", isActive);

            if (isActive) {
                link.setAttribute("aria-current", "location");
            } else {
                link.removeAttribute("aria-current");
            }
        });
    }

    navLinks.forEach(function (link) {
        link.addEventListener("click", function (event) {
            const href = link.getAttribute("href");

            if (!href || href === "#") return;

            const target = document.querySelector(href);

            // Si el destino no existe, no impedir el comportamiento normal.
            if (!target) return;

            event.preventDefault();

            activateLink(link);

            target.scrollIntoView({
                behavior: window.matchMedia(
                    "(prefers-reduced-motion: reduce)"
                ).matches ? "auto" : "smooth",
                block: "start"
            });

            // Actualizar el fragmento de la URL.
            if (window.location.hash !== href) {
                history.replaceState(null, "", href);
            }
        });
    });

    // Marcar como activa la sección indicada en la URL.
    function activateFromHash() {
        const hash = window.location.hash;

        if (!hash) return;

        const matchingLink = Array.from(navLinks).find(function (link) {
            return link.getAttribute("href") === hash;
        });

        if (matchingLink) {
            activateLink(matchingLink);
        }
    }

    activateFromHash();

    // MENÚ DEL PERFIL

    function closeProfileMenu() {
        if (!profileDropdown) return;

        profileDropdown.classList.remove("open");

        if (profileButton) {
            profileButton.setAttribute("aria-expanded", "false");
        }
    }

    function toggleProfileMenu() {
        if (!profileDropdown) return;

        const willOpen = !profileDropdown.classList.contains("open");

        profileDropdown.classList.toggle("open", willOpen);

        if (profileButton) {
            profileButton.setAttribute(
                "aria-expanded",
                String(willOpen)
            );
        }
    }

    if (profileButton && profileDropdown) {
        profileButton.setAttribute("aria-expanded", "false");

        profileButton.addEventListener("click", function (event) {
            event.preventDefault();
            event.stopPropagation();

            toggleProfileMenu();
        });
    }

    // Cerrar el menú al hacer clic fuera.
    document.addEventListener("click", function (event) {
        if (
            profileMenu &&
            !profileMenu.contains(event.target)
        ) {
            closeProfileMenu();
        }
    });

    // NOTIFICACIONES CONFIGURABLES

    document.querySelectorAll("[data-toast]").forEach(function (element) {
        element.addEventListener("click", function () {
            const message = element.getAttribute("data-toast");

            showToast(message);
        });
    });

    // CERRAR MENÚS CON ESCAPE

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            closeProfileMenu();
            toast.classList.remove("show");
        }
    });

    
    // ACCESIBILIDAD DE LOS ENLACES INTERNOS

    navLinks.forEach(function (link) {
        const href = link.getAttribute("href");

        if (href && href.startsWith("#")) {
            const target = document.querySelector(href);

            if (target && !target.hasAttribute("tabindex")) {
                target.setAttribute("tabindex", "-1");
            }
        }
    });

});

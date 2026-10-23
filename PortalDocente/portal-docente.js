document.addEventListener("DOMContentLoaded", function () {
    "use strict";

    // 1. ELEMENTOS DEL PORTAL

    const sidebar = document.getElementById("sidebar");
    const sidebarToggle = document.getElementById("sidebarToggle");

    const userMenu = document.getElementById("userMenu");
    const profileToggle = document.getElementById("profileToggle");
    const profileDropdown = document.getElementById("profileDropdown");

    let toastTimer = null;

    // 2. NOTIFICACIONES

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

    // 3. MENÚ LATERAL

    function updateSidebarIcon() {
        if (!sidebarToggle || !sidebar) return;

        const icon = sidebarToggle.querySelector("i");

        if (icon) {
            icon.className = window.innerWidth <= 850
                ? "fa-solid fa-bars"
                : sidebar.classList.contains("collapsed")
                    ? "fa-solid fa-angles-right"
                    : "fa-solid fa-angles-left";
        }
    }

    function closeMobileSidebar() {
        if (!sidebar || !sidebarToggle) return;

        sidebar.classList.remove("is-open");

        if (window.innerWidth <= 850) {
            sidebarToggle.setAttribute("aria-expanded", "false");
        }

        updateSidebarIcon();
    }

    if (sidebar && sidebarToggle) {
        sidebarToggle.setAttribute(
            "aria-expanded",
            String(
                window.innerWidth <= 850
                    ? sidebar.classList.contains("is-open")
                    : !sidebar.classList.contains("collapsed")
            )
        );

        updateSidebarIcon();

        sidebarToggle.addEventListener("click", function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (window.innerWidth <= 850) {
                const isOpen = sidebar.classList.toggle("is-open");

                sidebarToggle.setAttribute(
                    "aria-expanded",
                    String(isOpen)
                );
            } else {
                const isCollapsed = sidebar.classList.toggle("collapsed");

                sidebarToggle.setAttribute(
                    "aria-expanded",
                    String(!isCollapsed)
                );

                sidebarToggle.setAttribute(
                    "aria-label",
                    isCollapsed ? "Expandir menú" : "Contraer menú"
                );
            }

            updateSidebarIcon();
        });

        // Cerrar el menú móvil al seleccionar una opción.
        sidebar.querySelectorAll(".nav-link").forEach(function (link) {
            link.addEventListener("click", function () {
                if (window.innerWidth <= 850) {
                    closeMobileSidebar();
                }
            });
        });

        // Cerrar al pulsar fuera del menú.
        document.addEventListener("click", function (event) {
            if (
                window.innerWidth <= 850 &&
                sidebar.classList.contains("is-open") &&
                !sidebar.contains(event.target) &&
                !sidebarToggle.contains(event.target)
            ) {
                closeMobileSidebar();
            }
        });
    }

    // 4. MENÚ DEL PERFIL

    function closeProfileMenu() {
        if (userMenu) {
            userMenu.classList.remove("dropdown-open");
        }

        if (profileDropdown) {
            profileDropdown.classList.remove("open");
            profileDropdown.hidden = true;
        }

        if (profileToggle) {
            profileToggle.setAttribute("aria-expanded", "false");
        }
    }

    function toggleProfileMenu() {
        if (!userMenu || !profileDropdown || !profileToggle) {
            return;
        }

        const isOpen = userMenu.classList.contains("dropdown-open");

        userMenu.classList.toggle("dropdown-open", !isOpen);
        profileDropdown.classList.toggle("open", !isOpen);
        profileDropdown.hidden = isOpen;

        profileToggle.setAttribute(
            "aria-expanded",
            String(!isOpen)
        );
    }

    if (userMenu && profileToggle && profileDropdown) {
        // Estado inicial cerrado.
        closeProfileMenu();

        profileToggle.addEventListener("click", function (event) {
            event.preventDefault();
            event.stopPropagation();

            toggleProfileMenu();
        });

        profileDropdown.addEventListener("click", function (event) {
            event.stopPropagation();
        });

        document.addEventListener("click", function (event) {
            if (!userMenu.contains(event.target)) {
                closeProfileMenu();
            }
        });
    }

    // 5. NOTIFICACIONES EN BOTONES Y ENLACES

    document.querySelectorAll("[data-toast]").forEach(function (element) {
        element.addEventListener("click", function () {
            showToast(element.getAttribute("data-toast"));
        });
    });

    
    // 6. CERRAR CON ESCAPE

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            closeProfileMenu();
            closeMobileSidebar();

            if (toast) {
                toast.classList.remove("show");
            }
        }
    });

    // 7. ADAPTACIÓN AL CAMBIAR EL TAMAÑO

    window.addEventListener("resize", function () {
        if (!sidebar || !sidebarToggle) return;

        if (window.innerWidth > 850) {
            sidebar.classList.remove("is-open");
        }

        updateSidebarIcon();

        sidebarToggle.setAttribute(
            "aria-expanded",
            String(
                window.innerWidth <= 850
                    ? sidebar.classList.contains("is-open")
                    : !sidebar.classList.contains("collapsed")
            )
        );
    });

    console.log("Portal Docente INADI: JavaScript cargado correctamente.");

});
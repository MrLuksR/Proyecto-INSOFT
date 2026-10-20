document.addEventListener("DOMContentLoaded", function () {
    "use strict";

    // 1. ELEMENTOS DEL DOM

    const sidebar = document.querySelector(".sidebar");
    const collapseButton = document.querySelector(".sidebar-collapse");

    const profileMenu = document.querySelector(".user-menu");
    const profileButton = document.querySelector(".profile-chevron");
    const profileDropdown = document.querySelector(".profile-dropdown");

    let toastTimer;
    let toast = document.querySelector(".toast");


    // 2. CREAR NOTIFICACIONES TOAST

    if (!toast) {
        toast = document.createElement("div");

        toast.className = "toast";
        toast.setAttribute("role", "status");
        toast.setAttribute("aria-live", "polite");

        document.body.appendChild(toast);
    }

    // Mostrar notificación temporal
    function showToast(message) {
        if (!message) {
            return;
        }

        clearTimeout(toastTimer);

        toast.textContent = message;
        toast.classList.add("show");

        toastTimer = setTimeout(function () {
            toast.classList.remove("show");
        }, 3000);
    }


    // 3. CONTRAER Y EXPANDIR EL MENÚ LATERAL

    if (sidebar && collapseButton) {
        collapseButton.setAttribute("aria-expanded", "true");

        collapseButton.addEventListener("click", function () {
            const collapsed = sidebar.classList.toggle("collapsed");

            collapseButton.setAttribute(
                "aria-expanded",
                String(!collapsed)
            );

            collapseButton.setAttribute(
                "aria-label",
                collapsed ? "Expandir menú" : "Contraer menú"
            );

            const icon = collapseButton.querySelector("i");

            if (icon) {
                icon.classList.toggle("fa-angles-left", !collapsed);
                icon.classList.toggle("fa-angles-right", collapsed);
            }
        });
    }


    // 4. CERRAR EL MENÚ DEL PERFIL

    function closeProfileMenu() {
        if (profileDropdown) {
            profileDropdown.classList.remove("open");
        }

        if (profileButton) {
            profileButton.setAttribute("aria-expanded", "false");
        }
    }


    // 5. ABRIR Y CERRAR EL MENÚ DEL PERFIL

    if (profileButton && profileDropdown) {
        profileButton.setAttribute("aria-expanded", "false");

        profileButton.addEventListener("click", function (event) {
            event.preventDefault();
            event.stopPropagation();

            const open = !profileDropdown.classList.contains("open");

            profileDropdown.classList.toggle("open", open);

            profileButton.setAttribute(
                "aria-expanded",
                String(open)
            );
        });
    }


    // 6. CERRAR EL PERFIL AL HACER CLIC AFUERA

    document.addEventListener("click", function (event) {
        if (profileMenu && !profileMenu.contains(event.target)) {
            closeProfileMenu();
        }
    });


    // 7. MOSTRAR NOTIFICACIONES AL HACER CLIC

    document.querySelectorAll("[data-toast]").forEach(function (el) {
        el.addEventListener("click", function () {
            showToast(el.getAttribute("data-toast"));
        });
    });


    // 8. CERRAR ELEMENTOS CON LA TECLA ESCAPE

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            closeProfileMenu();

            if (toast) {
                toast.classList.remove("show");
            }
        }
    });

});
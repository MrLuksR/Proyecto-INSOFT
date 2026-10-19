document.addEventListener("DOMContentLoaded", function () {
    "use strict";
    const sidebar = document.querySelector(".sidebar");
    const collapseButton = document.querySelector(".sidebar-collapse");
    const profileMenu = document.querySelector(".user-menu");
    const profileButton = document.querySelector(".profile-chevron");
    const profileDropdown = document.querySelector(".profile-dropdown");
    let toastTimer;
    let toast = document.querySelector(".toast");
    if (!toast) { toast = document.createElement("div"); toast.className = "toast"; toast.setAttribute("role", "status"); toast.setAttribute("aria-live", "polite"); document.body.appendChild(toast); }
    function showToast(message) { if (!message) return; clearTimeout(toastTimer); toast.textContent = message; toast.classList.add("show"); toastTimer = setTimeout(() => toast.classList.remove("show"), 3000); }
    if (sidebar && collapseButton) {
        collapseButton.setAttribute("aria-expanded", "true");
        collapseButton.addEventListener("click", function () {
            const collapsed = sidebar.classList.toggle("collapsed");
            collapseButton.setAttribute("aria-expanded", String(!collapsed));
            collapseButton.setAttribute("aria-label", collapsed ? "Expandir menú" : "Contraer menú");
            const icon = collapseButton.querySelector("i");
            if (icon) { icon.classList.toggle("fa-angles-left", !collapsed); icon.classList.toggle("fa-angles-right", collapsed); }
        });
    }
    function closeProfileMenu() { if (profileDropdown) profileDropdown.classList.remove("open"); if (profileButton) profileButton.setAttribute("aria-expanded", "false"); }
    if (profileButton && profileDropdown) {
        profileButton.setAttribute("aria-expanded", "false");
        profileButton.addEventListener("click", function (event) { event.preventDefault(); event.stopPropagation(); const open = !profileDropdown.classList.contains("open"); profileDropdown.classList.toggle("open", open); profileButton.setAttribute("aria-expanded", String(open)); });
    }
    document.addEventListener("click", function (event) { if (profileMenu && !profileMenu.contains(event.target)) closeProfileMenu(); });
    document.querySelectorAll("[data-toast]").forEach(el => el.addEventListener("click", () => showToast(el.getAttribute("data-toast"))));
    document.addEventListener("keydown", function (event) { if (event.key === "Escape") { closeProfileMenu(); if (toast) toast.classList.remove("show"); } });
});

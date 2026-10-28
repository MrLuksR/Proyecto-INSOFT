

document.addEventListener("DOMContentLoaded", function () {

    console.log("INADI: JavaScript de Robótica cargado correctamente.");


    // 1. MENÚ LATERAL CONTRAÍBLE

    const botonSidebar = document.getElementById("sidebarToggle");
    const sidebar = document.querySelector(".sidebar");
    const pagina = document.querySelector(".page");

    if (botonSidebar) {

        const iconoSidebar = botonSidebar.querySelector("i");

        botonSidebar.setAttribute("aria-expanded", "true");

        botonSidebar.addEventListener("click", function (evento) {

            evento.preventDefault();

            const contraido =
                !document.body.classList.contains("sidebar-collapsed");

            document.body.classList.toggle(
                "sidebar-collapsed",
                contraido
            );

            if (sidebar) {
                sidebar.classList.toggle(
                    "sidebar-collapsed",
                    contraido
                );
            }

            if (pagina) {
                pagina.classList.toggle(
                    "sidebar-collapsed",
                    contraido
                );
            }

            botonSidebar.setAttribute(
                "aria-expanded",
                String(!contraido)
            );

            botonSidebar.setAttribute(
                "aria-label",
                contraido ? "Expandir menú" : "Contraer menú"
            );

            botonSidebar.setAttribute(
                "title",
                contraido ? "Expandir menú" : "Contraer menú"
            );

            if (iconoSidebar) {

                iconoSidebar.classList.toggle(
                    "fa-angles-left",
                    !contraido
                );

                iconoSidebar.classList.toggle(
                    "fa-angles-right",
                    contraido
                );
            }

        });
    }


    // 2. ANIMACIONES PARA LAS TARJETAS DE ROBÓTICA

    const tarjetas = document.querySelectorAll(
        ".tarjeta, .tarjetas .card"
    );

    tarjetas.forEach(function (tarjeta) {

        tarjeta.addEventListener("mouseenter", function () {
            tarjeta.classList.add("tarjeta-hover");
        });

        tarjeta.addEventListener("mouseleave", function () {
            tarjeta.classList.remove("tarjeta-hover");
        });

    });


    // 3. ANIMACIÓN DE ENTRADA AL DESPLAZARSE POR LA PÁGINA

    const elementosAnimados = document.querySelectorAll(
        ".encabezado-servicio, " +
        ".bloque-servicio, " +
        ".tarjeta, " +
        ".lista-beneficios li"
    );

    if ("IntersectionObserver" in window) {

        const observador = new IntersectionObserver(
            function (entradas, observadorActual) {

                entradas.forEach(function (entrada) {

                    if (entrada.isIntersecting) {

                        entrada.target.classList.add("card-visible");

                        observadorActual.unobserve(entrada.target);
                    }

                });

            },
            {
                threshold: 0.12
            }
        );

        elementosAnimados.forEach(function (elemento) {
            observador.observe(elemento);
        });

    } else {

        elementosAnimados.forEach(function (elemento) {
            elemento.classList.add("card-visible");
        });

    }


    // 4. BOTONES DE CONTACTO

    const botonesContacto = document.querySelectorAll(
        ".boton-contacto"
    );

    botonesContacto.forEach(function (boton) {

        boton.addEventListener("mouseenter", function () {
            boton.classList.add("boton-contacto-hover");
        });

        boton.addEventListener("mouseleave", function () {
            boton.classList.remove("boton-contacto-hover");
        });

    });


    // 5. ACCESIBILIDAD PARA LOS ENLACES INTERNOS

    const enlacesInternos = document.querySelectorAll(
        'a[href^="#"]'
    );

    enlacesInternos.forEach(function (enlace) {

        enlace.addEventListener("click", function (evento) {

            const destino = enlace.getAttribute("href");

            if (!destino || destino === "#") {
                return;
            }

            const seccion = document.querySelector(destino);

            if (seccion) {

                evento.preventDefault();

                seccion.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });

            }

        });

    });


    // 6. EVITAR ANIMACIONES SI EL USUARIO LAS REDUCE

    const reducirMovimiento = window.matchMedia(
        "(prefers-reduced-motion: reduce)"
    );

    if (reducirMovimiento.matches) {

        elementosAnimados.forEach(function (elemento) {
            elemento.classList.add("card-visible");
        });

    }


    console.log("INADI: interacciones de Robótica inicializadas.");

});

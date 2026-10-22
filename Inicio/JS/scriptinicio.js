
/* INADI | JAVASCRIPT PRINCIPAL */

document.addEventListener("DOMContentLoaded", function () {

    console.log("INADI: JavaScript cargado correctamente.");

    // 1. CARRUSEL DE CURSOS

    const carrusel = document.querySelector(".course-carousel");

    if (carrusel) {

        // Buscar los banners generados por PHP.
        const banners = Array.from(
            carrusel.querySelectorAll(
                ".course-slide, .banner, .banner-card, " +
                ".course-banner, .curso-card, .slide, .carousel-item"
            )
        );

        // Evitar contar elementos que no sean banners reales.
        const bannersUnicos = [...new Set(banners)];

        const puntos = Array.from(
            carrusel.querySelectorAll(
                ".carousel-dot, .carousel-dots button"
            )
        );

        const anterior = carrusel.querySelector(".arrow-left");
        const siguiente = carrusel.querySelector(".arrow-right");

        let cursoActual = 0;
        let intervaloCarrusel = null;

        const tiempoCambio = 5000;
        // 5000 milisegundos = 5 segundos.

        // Identificar los banners visibles y sus indicadores.
        function obtenerBanners() {

            return Array.from(
                carrusel.querySelectorAll(
                    ".course-slide, .banner, .banner-card, " +
                    ".course-banner, .curso-card, .slide, .carousel-item"
                )
            ).filter(function (elemento, indice, lista) {
                return lista.indexOf(elemento) === indice;
            });
        }

        function mostrarCurso(indice) {

            const slides = obtenerBanners();

            if (slides.length === 0) {
                console.warn(
                    "INADI: no se encontraron banners de cursos. " +
                    "Revisá el HTML generado por mostrarBanners($pdo)."
                );
                return;
            }

            // Ajustar el índice para que el carrusel sea circular.
            cursoActual = (indice + slides.length) % slides.length;

            slides.forEach(function (slide, i) {

                const activo = i === cursoActual;

                slide.classList.toggle("active", activo);

                slide.setAttribute(
                    "aria-hidden",
                    activo ? "false" : "true"
                );

                // Si los banners se controlan con display,
                // se muestra únicamente el curso activo.
                slide.style.display = activo ? "" : "none";
            });

            // Actualizar los indicadores.
            puntos.forEach(function (punto, i) {

                const activo = i === cursoActual;

                punto.classList.toggle("active", activo);

                if (activo) {
                    punto.setAttribute("aria-current", "true");
                } else {
                    punto.removeAttribute("aria-current");
                }
            });
        }

        // Función global para los botones definidos en el PHP.
        window.cambiarCurso = function (direccion) {

            mostrarCurso(cursoActual + direccion);
            reiniciarCarrusel();
        };

        // Función global para seleccionar un curso con los puntos.
        window.irACurso = function (indice) {

            mostrarCurso(indice);
            reiniciarCarrusel();
        };

        // Iniciar el movimiento automático.
        function iniciarCarrusel() {

            detenerCarrusel();

            const slides = obtenerBanners();

            if (slides.length > 1) {

                intervaloCarrusel = setInterval(function () {
                    mostrarCurso(cursoActual + 1);
                }, tiempoCambio);
            }
        }

        // Detener el movimiento.
        function detenerCarrusel() {

            if (intervaloCarrusel !== null) {
                clearInterval(intervaloCarrusel);
                intervaloCarrusel = null;
            }
        }

        // Reiniciar el temporizador después de una interacción.
        function reiniciarCarrusel() {
            iniciarCarrusel();
        }

        // Botón anterior.
        if (anterior) {
            anterior.onclick = function (evento) {
                evento.preventDefault();
                window.cambiarCurso(-1);
            };
        }

        // Botón siguiente.
        if (siguiente) {
            siguiente.onclick = function (evento) {
                evento.preventDefault();
                window.cambiarCurso(1);
            };
        }

        // Los puntos permiten elegir un curso directamente.
        puntos.forEach(function (punto, indice) {

            punto.addEventListener("click", function () {
                window.irACurso(indice);
            });
        });

        // Pausar cuando el usuario pasa el mouse por el carrusel.
        carrusel.addEventListener("mouseenter", detenerCarrusel);

        carrusel.addEventListener("mouseleave", iniciarCarrusel);

        // Pausar cuando el usuario navega con el teclado.
        carrusel.addEventListener("focusin", detenerCarrusel);

        carrusel.addEventListener("focusout", function (evento) {

            if (!carrusel.contains(evento.relatedTarget)) {
                iniciarCarrusel();
            }
        });

        // Evitar movimiento automático si la pestaña está oculta.
        document.addEventListener("visibilitychange", function () {

            if (document.hidden) {
                detenerCarrusel();
            } else {
                iniciarCarrusel();
            }
        });

        // Mostrar el primer curso e iniciar el carrusel.
        mostrarCurso(0);
        iniciarCarrusel();

    } else {

        console.warn("INADI: no se encontró .course-carousel.");
    }


    // 2. ANIMACIONES PARA LAS TARJETAS DE SERVICIOS

    const tarjetasServicios = document.querySelectorAll(".service-card");

    tarjetasServicios.forEach(function (tarjeta) {

        tarjeta.addEventListener("mouseenter", function () {
            tarjeta.classList.add("service-hover");
        });

        tarjeta.addEventListener("mouseleave", function () {
            tarjeta.classList.remove("service-hover");
        });

    });


    // 3. ANIMACIONES PARA LAS TARJETAS DE ESTADÍSTICAS

    const tarjetasEstadisticas = document.querySelectorAll(".stat-card");

    tarjetasEstadisticas.forEach(function (tarjeta) {

        tarjeta.addEventListener("mouseenter", function () {
            tarjeta.classList.add("stat-hover");
        });

        tarjeta.addEventListener("mouseleave", function () {
            tarjeta.classList.remove("stat-hover");
        });

    });


    // 4. MENÚ LATERAL CONTRAÍBLE

    const botonSidebar = document.getElementById("sidebarToggle");

    if (botonSidebar) {

        const iconoSidebar = botonSidebar.querySelector("i");

        botonSidebar.addEventListener("click", function () {

            document.body.classList.toggle("sidebar-collapsed");

            const contraido = document.body.classList.contains(
                "sidebar-collapsed"
            );

            botonSidebar.setAttribute(
                "aria-expanded",
                contraido ? "false" : "true"
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


    // 5. EXPANDIR Y CONTRAER RESEÑAS

    const botonesResena = document.querySelectorAll(".review-more");

    botonesResena.forEach(function (boton) {

        const texto = boton.previousElementSibling;

        if (!texto || !texto.classList.contains("review-text")) {
            return;
        }

        // Guardar el contenido original.
        const contenidoCompleto = texto.textContent.trim();

        const limiteCaracteres = 150;

        if (contenidoCompleto.length <= limiteCaracteres) {
            boton.style.display = "none";
            return;
        }

        texto.textContent =
            contenidoCompleto.substring(0, limiteCaracteres).trimEnd() +
            "...";

        boton.textContent = "Ver más⌄";

        let expandida = false;

        boton.setAttribute("role", "button");
        boton.setAttribute("tabindex", "0");
        boton.setAttribute("aria-expanded", "false");

        function alternarResena() {

            expandida = !expandida;

            texto.textContent = expandida
                ? contenidoCompleto
                : contenidoCompleto
                    .substring(0, limiteCaracteres)
                    .trimEnd() + "...";

            boton.textContent = expandida
                ? "Ver menos⌃"
                : "Ver más⌄";

            boton.setAttribute(
                "aria-expanded",
                expandida ? "true" : "false"
            );
        }

        boton.addEventListener("click", alternarResena);

        boton.addEventListener("keydown", function (evento) {

            if (evento.key === "Enter" || evento.key === " ") {
                evento.preventDefault();
                alternarResena();
            }

        });

    });


    // 6. ANIMACIÓN DE ENTRADA PARA LAS TARJETAS

    const elementosAnimados = document.querySelectorAll(
        ".service-card, .stats .stat-card, .review-card, .company-card"
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

    console.log("INADI: interacciones inicializadas.");

});


document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector(".sidebar");
    const toggle = document.getElementById("sidebarToggle");
    const page = document.querySelector(".page");

    if (!sidebar || !toggle || !page) {
        console.error("No se encontró la barra lateral o su botón.");
        return;
    }

    toggle.addEventListener("click", function () {
        const estaContraida = sidebar.classList.toggle("sidebar-collapsed");

        page.classList.toggle("sidebar-collapsed", estaContraida);

        toggle.setAttribute("aria-expanded", String(!estaContraida));
        toggle.setAttribute(
            "aria-label",
            estaContraida ? "Mostrar menú" : "Contraer menú"
        );
        toggle.setAttribute(
            "title",
            estaContraida ? "Mostrar menú" : "Contraer menú"
        );

        const icono = toggle.querySelector("i");

        if (icono) {
            icono.classList.toggle("fa-angles-left", !estaContraida);
            icono.classList.toggle("fa-angles-right", estaContraida);
        }
    });
});


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

        const sidebar = document.querySelector(".sidebar");
        const pagina = document.querySelector(".page");

        botonSidebar.setAttribute("aria-expanded", "true");

        botonSidebar.addEventListener("click", function (evento) {
            evento.preventDefault();

            // Sincronizar las clases utilizadas por los estilos actuales.
            const contraido = !document.body.classList.contains("sidebar-collapsed");
            document.body.classList.toggle("sidebar-collapsed", contraido);

            if (sidebar) sidebar.classList.toggle("sidebar-collapsed", contraido);
            if (pagina) pagina.classList.toggle("sidebar-collapsed", contraido);

            botonSidebar.setAttribute("aria-expanded", String(!contraido));
            botonSidebar.setAttribute("aria-label", contraido ? "Expandir menú" : "Contraer menú");
            botonSidebar.setAttribute("title", contraido ? "Expandir menú" : "Contraer menú");

            if (iconoSidebar) {
                iconoSidebar.classList.toggle("fa-angles-left", !contraido);
                iconoSidebar.classList.toggle("fa-angles-right", contraido);
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

/* MARQUESINA DE EMPRESAS Y VISOR DE LOGOTIPO */
function inicializarMarquesinaEmpresas() {
    const marquesina = document.querySelector(".companies-marquee");
    const pista = marquesina ? marquesina.querySelector(".companies") : null;

    if (!marquesina || !pista || marquesina.dataset.inicializada === "true") return;
    marquesina.dataset.inicializada = "true";

    // Duplica una sola vez la lista para mantener el desplazamiento continuo.
    if (pista.dataset.marquesinaPreparada !== "true") {
        const tarjetasOriginales = Array.from(pista.children);
        tarjetasOriginales.forEach(function (tarjeta) {
            const copia = tarjeta.cloneNode(true);
            copia.setAttribute("aria-hidden", "true");
            copia.classList.add("card-visible");
            copia.removeAttribute("tabindex");
            copia.querySelectorAll("a, button, [tabindex]").forEach(function (elemento) {
                elemento.setAttribute("tabindex", "-1");
            });
            pista.appendChild(copia);
        });
        pista.dataset.marquesinaPreparada = "true";
    }

    // Forzar las propiedades esenciales desde JS para evitar que reglas antiguas
    // de .companies (grid) impidan el movimiento horizontal.
    pista.style.display = "flex";
    pista.style.flexWrap = "nowrap";
    pista.style.width = "max-content";
    pista.style.minWidth = "max-content";
    pista.style.animation = "inadiMarquesinaEmpresas 32s linear infinite";
    pista.style.willChange = "transform";

    // Crear el visor de imágenes una sola vez.
    let visor = document.querySelector(".company-lightbox");
    if (!visor) {
        visor = document.createElement("div");
        visor.className = "company-lightbox";
        visor.setAttribute("role", "dialog");
        visor.setAttribute("aria-modal", "true");
        visor.setAttribute("aria-label", "Logotipo ampliado");
        visor.innerHTML = '<button type="button" class="company-lightbox-close" aria-label="Cerrar imagen">&times;</button><img src="" alt="Logotipo ampliado">';
        document.body.appendChild(visor);
    }

    const imagenGrande = visor.querySelector("img");
    const botonCerrar = visor.querySelector(".company-lightbox-close");
    let elementoAnterior = null;

    function abrirImagen(tarjeta) {
        if (tarjeta.getAttribute("aria-hidden") === "true") return;
        const imagen = tarjeta.querySelector(".company-logo img");
        if (!imagen || !imagen.getAttribute("src")) return;
        elementoAnterior = tarjeta;
        imagenGrande.src = imagen.src;
        imagenGrande.alt = imagen.alt || tarjeta.querySelector(".company-name")?.textContent.trim() || "Logotipo de empresa";
        visor.classList.add("is-visible");
        document.body.style.overflow = "hidden";
        botonCerrar.focus();
    }

    function cerrarImagen() {
        visor.classList.remove("is-visible");
        imagenGrande.removeAttribute("src");
        document.body.style.overflow = "";
        if (elementoAnterior && typeof elementoAnterior.focus === "function") elementoAnterior.focus();
    }

    marquesina.addEventListener("click", function (evento) {
        const tarjeta = evento.target.closest(".company-card");
        if (tarjeta) abrirImagen(tarjeta);
    });

    marquesina.addEventListener("keydown", function (evento) {
        if ((evento.key === "Enter" || evento.key === " ") && evento.target.matches(".company-card")) {
            evento.preventDefault();
            abrirImagen(evento.target);
        }
    });

    botonCerrar.addEventListener("click", cerrarImagen);
    visor.addEventListener("click", function (evento) {
        if (evento.target === visor) cerrarImagen();
    });
    document.addEventListener("keydown", function (evento) {
        if (evento.key === "Escape" && visor.classList.contains("is-visible")) cerrarImagen();
    });
 }

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", inicializarMarquesinaEmpresas, { once: true });
} else {
    inicializarMarquesinaEmpresas();
}

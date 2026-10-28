/* INADI | JAVASCRIPT PRINCIPAL */

"use strict";

document.addEventListener("DOMContentLoaded", function () {
    console.log("INADI: JavaScript cargado correctamente.");

    // 1. MENÚ DESPLEGABLE DEL USUARIO

    const menusUsuario = document.querySelectorAll(
        ".account-dropdown details"
    );

    menusUsuario.forEach(function (menu) {
        const resumen = menu.querySelector("summary");

        if (!resumen) return;

        resumen.setAttribute("aria-haspopup", "true");
        resumen.setAttribute("aria-expanded", String(menu.open));

        // El navegador abre y cierra <details> al pulsar <summary>.
        // Este evento actualiza el estado accesible.
        menu.addEventListener("toggle", function () {
            resumen.setAttribute("aria-expanded", String(menu.open));

            // Cerrar los demás menús de usuario si se abre este.
            if (menu.open) {
                menusUsuario.forEach(function (otroMenu) {
                    if (otroMenu !== menu) {
                        otroMenu.open = false;

                        const otroResumen =
                            otroMenu.querySelector("summary");

                        if (otroResumen) {
                            otroResumen.setAttribute(
                                "aria-expanded",
                                "false"
                            );
                        }
                    }
                });
            }
        });

        // Permitir abrir el menú con el teclado.
        resumen.addEventListener("keydown", function (evento) {
            if (
                evento.key === "Enter" ||
                evento.key === " "
            ) {
                // El elemento summary ya gestiona estas teclas.
                // No se cancela el comportamiento nativo.
            }
        });
    });

    // Cerrar el menú al hacer clic fuera.
    document.addEventListener("click", function (evento) {
        menusUsuario.forEach(function (menu) {
            if (!menu.contains(evento.target) && menu.open) {
                menu.open = false;

                const resumen = menu.querySelector("summary");

                if (resumen) {
                    resumen.setAttribute("aria-expanded", "false");
                }
            }
        });
    });

    // Cerrar el menú con Escape.
    document.addEventListener("keydown", function (evento) {
        if (evento.key === "Escape") {
            menusUsuario.forEach(function (menu) {
                if (menu.open) {
                    menu.open = false;

                    const resumen = menu.querySelector("summary");

                    if (resumen) {
                        resumen.setAttribute("aria-expanded", "false");
                        resumen.focus();
                    }
                }
            });
        }
    });

    // 2. CARRUSEL DE CURSOS

    const carrusel = document.querySelector(".course-carousel");

    if (carrusel) {
        const selectoresSlides = [
            ".course-slide",
            ".banner",
            ".banner-card",
            ".course-banner",
            ".curso-card",
            ".slide",
            ".carousel-item"
        ].join(", ");

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

        function obtenerBanners() {
            return Array.from(
                carrusel.querySelectorAll(selectoresSlides)
            ).filter(function (elemento, indice, lista) {
                return lista.indexOf(elemento) === indice;
            }).filter(function (elemento) {
                return !elemento.closest(".carousel-dots") &&
                    !elemento.classList.contains("carousel-arrow");
            });
        }

        function mostrarCurso(indice) {
            const banners = obtenerBanners();

            if (banners.length === 0) {
                detenerCarrusel();

                console.warn(
                    "INADI: no se encontraron banners. " +
                    "Revisá el HTML generado por mostrarBanners($pdo)."
                );

                return;
            }

            cursoActual = (indice + banners.length) % banners.length;

            banners.forEach(function (banner, posicion) {
                const activo = posicion === cursoActual;

                banner.classList.toggle("active", activo);
                banner.setAttribute("aria-hidden", String(!activo));
                banner.style.display = activo ? "" : "none";
            });

            puntos.forEach(function (punto, posicion) {
                const activo = posicion === cursoActual;

                punto.classList.toggle("active", activo);

                if (activo) {
                    punto.setAttribute("aria-current", "true");
                } else {
                    punto.removeAttribute("aria-current");
                }
            });
        }

        function detenerCarrusel() {
            if (intervaloCarrusel !== null) {
                clearInterval(intervaloCarrusel);
                intervaloCarrusel = null;
            }
        }

        function iniciarCarrusel() {
            detenerCarrusel();

            if (document.hidden) return;

            if (obtenerBanners().length > 1) {
                intervaloCarrusel = setInterval(function () {
                    mostrarCurso(cursoActual + 1);
                }, tiempoCambio);
            }
        }

        function reiniciarCarrusel() {
            iniciarCarrusel();
        }

        // Funciones utilizadas por los botones del PHP.
        window.cambiarCurso = function (direccion) {
            mostrarCurso(cursoActual + Number(direccion || 0));
            reiniciarCarrusel();
        };

        window.irACurso = function (indice) {
            mostrarCurso(Number(indice) || 0);
            reiniciarCarrusel();
        };

        if (anterior) {
            anterior.addEventListener("click", function (evento) {
                evento.preventDefault();
                window.cambiarCurso(-1);
            });
        }

        if (siguiente) {
            siguiente.addEventListener("click", function (evento) {
                evento.preventDefault();
                window.cambiarCurso(1);
            });
        }

        puntos.forEach(function (punto, indice) {
            punto.addEventListener("click", function () {
                window.irACurso(indice);
            });
        });

        carrusel.addEventListener("mouseenter", detenerCarrusel);
        carrusel.addEventListener("mouseleave", iniciarCarrusel);
        carrusel.addEventListener("focusin", detenerCarrusel);

        carrusel.addEventListener("focusout", function (evento) {
            if (!carrusel.contains(evento.relatedTarget)) {
                iniciarCarrusel();
            }
        });

        document.addEventListener("visibilitychange", function () {
            if (document.hidden) {
                detenerCarrusel();
            } else {
                iniciarCarrusel();
            }
        });

        mostrarCurso(0);
        iniciarCarrusel();
    }

    // 3. MENÚ LATERAL CONTRAÍBLE

    const botonSidebar = document.getElementById("sidebarToggle");
    const sidebar = document.querySelector(".sidebar");
    const pagina = document.querySelector(".page");

    if (botonSidebar && sidebar) {
        const iconoSidebar = botonSidebar.querySelector("i");

        function actualizarSidebar(contraido) {
            document.body.classList.toggle(
                "sidebar-collapsed",
                contraido
            );

            sidebar.classList.toggle(
                "sidebar-collapsed",
                contraido
            );

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
        }

        const inicialmenteContraido =
            document.body.classList.contains("sidebar-collapsed") ||
            sidebar.classList.contains("sidebar-collapsed") ||
            Boolean(
                pagina &&
                pagina.classList.contains("sidebar-collapsed")
            );

        actualizarSidebar(inicialmenteContraido);

        botonSidebar.addEventListener("click", function (evento) {
            evento.preventDefault();

            const contraido =
                !document.body.classList.contains("sidebar-collapsed");

            actualizarSidebar(contraido);
        });
    }

    // 4. TARJETAS DE SERVICIOS Y ESTADÍSTICAS

    document.querySelectorAll(
        ".service-card, .stat-card"
    ).forEach(function (tarjeta) {
        tarjeta.addEventListener("mouseenter", function () {
            tarjeta.classList.add(
                tarjeta.classList.contains("service-card")
                    ? "service-hover"
                    : "stat-hover"
            );
        });

        tarjeta.addEventListener("mouseleave", function () {
            tarjeta.classList.remove("service-hover", "stat-hover");
        });
    });

    // 5. EXPANDIR Y CONTRAER RESEÑAS

    document.querySelectorAll(".review-more").forEach(function (boton) {
        const texto = boton.previousElementSibling;

        if (!texto || !texto.classList.contains("review-text")) {
            return;
        }

        const contenidoCompleto = texto.textContent.trim();
        const limiteCaracteres = 150;

        if (contenidoCompleto.length <= limiteCaracteres) {
            boton.hidden = true;
            return;
        }

        let expandida = false;

        function actualizarResena() {
            texto.textContent = expandida
                ? contenidoCompleto
                : contenidoCompleto.substring(0, limiteCaracteres).trimEnd() + "...";

            boton.textContent = expandida
                ? "Ver menos⌃"
                : "Ver más⌄";

            boton.setAttribute(
                "aria-expanded",
                String(expandida)
            );
        }

        boton.setAttribute("role", "button");
        boton.setAttribute("tabindex", "0");
        boton.setAttribute("aria-expanded", "false");

        function alternarResena() {
            expandida = !expandida;
            actualizarResena();
        }

        actualizarResena();

        boton.addEventListener("click", alternarResena);

        boton.addEventListener("keydown", function (evento) {
            if (evento.key === "Enter" || evento.key === " ") {
                evento.preventDefault();
                alternarResena();
            }
        });
    });

    // 6. ANIMACIONES DE ENTRADA

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
            { threshold: 0.12 }
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

    // 7. MARQUESINA Y VISOR DE LOGOTIPOS DE EMPRESAS

    inicializarMarquesinaEmpresas();
});

function inicializarMarquesinaEmpresas() {
    const marquesina = document.querySelector(".companies-marquee");
    const pista = marquesina
        ? marquesina.querySelector(".companies")
        : null;

    if (!marquesina || !pista) return;
    if (marquesina.dataset.inicializada === "true") return;

    marquesina.dataset.inicializada = "true";

    // Preparar las tarjetas duplicadas una sola vez.
    if (pista.dataset.marquesinaPreparada !== "true") {
        const tarjetasOriginales = Array.from(pista.children);

        tarjetasOriginales.forEach(function (tarjeta) {
            const copia = tarjeta.cloneNode(true);

            copia.setAttribute("aria-hidden", "true");
            copia.classList.add("card-visible");
            copia.removeAttribute("tabindex");

            copia.querySelectorAll(
                "a, button, [tabindex]"
            ).forEach(function (elemento) {
                elemento.setAttribute("tabindex", "-1");
            });

            pista.appendChild(copia);
        });

        pista.dataset.marquesinaPreparada = "true";
    }

    // Animación horizontal continua.
    pista.style.display = "flex";
    pista.style.flexWrap = "nowrap";
    pista.style.width = "max-content";
    pista.style.minWidth = "max-content";
    pista.style.willChange = "transform";
    pista.style.animation =
        "inadiMarquesinaEmpresas 32s linear infinite";

    // Crear el visor ampliado.
    let visor = document.querySelector(".company-lightbox");

    if (!visor) {
        visor = document.createElement("div");
        visor.className = "company-lightbox";
        visor.setAttribute("role", "dialog");
        visor.setAttribute("aria-modal", "true");
        visor.setAttribute("aria-label", "Logotipo ampliado");

        visor.innerHTML = `
            <button
                type="button"
                class="company-lightbox-close"
                aria-label="Cerrar imagen"
            >&times;</button>
            <img src="" alt="Logotipo ampliado">
        `;

        document.body.appendChild(visor);
    }

    const imagenGrande = visor.querySelector("img");
    const botonCerrar = visor.querySelector(".company-lightbox-close");

    if (!imagenGrande || !botonCerrar) return;

    let elementoAnterior = null;

    function abrirImagen(tarjeta) {
        if (tarjeta.getAttribute("aria-hidden") === "true") return;

        const imagen = tarjeta.querySelector(".company-logo img");

        if (!imagen || !imagen.getAttribute("src")) return;

        elementoAnterior = tarjeta;
        imagenGrande.src = imagen.src;

        const nombreEmpresa = tarjeta.querySelector(".company-name");

        imagenGrande.alt =
            imagen.alt ||
            (nombreEmpresa
                ? nombreEmpresa.textContent.trim()
                : "Logotipo de empresa");

        visor.classList.add("is-visible");
        document.body.style.overflow = "hidden";
        botonCerrar.focus();
    }

    function cerrarImagen() {
        visor.classList.remove("is-visible");
        imagenGrande.removeAttribute("src");
        document.body.style.overflow = "";

        if (
            elementoAnterior &&
            typeof elementoAnterior.focus === "function"
        ) {
            elementoAnterior.focus();
        }
    }

    marquesina.addEventListener("click", function (evento) {
        const tarjeta = evento.target.closest(".company-card");

        if (tarjeta && marquesina.contains(tarjeta)) {
            abrirImagen(tarjeta);
        }
    });

    marquesina.addEventListener("keydown", function (evento) {
        if (
            (evento.key === "Enter" || evento.key === " ") &&
            evento.target.matches(".company-card")
        ) {
            evento.preventDefault();
            abrirImagen(evento.target);
        }
    });

    botonCerrar.addEventListener("click", cerrarImagen);

    visor.addEventListener("click", function (evento) {
        if (evento.target === visor) {
            cerrarImagen();
        }
    });

    document.addEventListener("keydown", function (evento) {
        if (
            evento.key === "Escape" &&
            visor.classList.contains("is-visible")
        ) {
            cerrarImagen();
        }
    });
}
// SCRIPT PRINCIPAL INADI

console.log("SCRIPT JS FUNCIONANDO");


// CARRUSEL DE CURSOS

// Curso que se está mostrando actualmente
let cursoActual = 0;

// Obtenemos todos los cursos
const cursos = document.querySelectorAll(".course-slide");

// Obtenemos todos los puntos indicadores
const puntos = document.querySelectorAll(".carousel-dot");

// Intervalo del cambio automático
let intervaloCarrusel;


// MOSTRAR UN CURSO
function mostrarCurso(indice) {

    // Si no hay cursos, no hacemos nada
    if (cursos.length === 0) {
        return;
    }

    // Aseguramos que el índice esté dentro del rango
    indice = ((indice % cursos.length) + cursos.length) % cursos.length;

    // Quitamos active de todos los cursos
    cursos.forEach(function(curso) {
        curso.classList.remove("active");
    });

    // Quitamos active de todos los puntos
    puntos.forEach(function(punto) {
        punto.classList.remove("active");
        punto.setAttribute("aria-current", "false");
    });

    // Activamos el curso seleccionado
    cursos[indice].classList.add("active");

    // Activamos el punto correspondiente, si existe
    if (puntos[indice]) {
        puntos[indice].classList.add("active");
        puntos[indice].setAttribute("aria-current", "true");
    }

    // Guardamos el curso actual
    cursoActual = indice;
}


// CAMBIAR CURSO
function cambiarCurso(direccion) {

    if (cursos.length === 0) {
        return;
    }

    // Avanzamos o retrocedemos
    mostrarCurso(cursoActual + direccion);
}


// IR DIRECTAMENTE A UN CURSO
function irACurso(indice) {
    mostrarCurso(indice);
}


// CAMBIO AUTOMÁTICO
function iniciarCarruselAutomatico() {

    // Evitamos crear varios intervalos
    clearInterval(intervaloCarrusel);

    // Si hay más de un curso, cambiamos cada 5 segundos
    if (cursos.length > 1) {
        intervaloCarrusel = setInterval(function() {
            cambiarCurso(1);
        }, 5000);
    }
}


// INICIALIZAR CARRUSEL

document.addEventListener("DOMContentLoaded", function() {

    // Mostramos el primer curso
    mostrarCurso(0);

    // Iniciamos el cambio automático
    iniciarCarruselAutomatico();


    // PUNTOS INDICADORES DEL CARRUSEL

    puntos.forEach(function(punto, indice) {

        punto.addEventListener("click", function() {

            // Mostramos el curso seleccionado
            irACurso(indice);

            // Reiniciamos el temporizador
            iniciarCarruselAutomatico();

        });

    });


    // FLECHAS DEL CARRUSEL

    const flechaIzquierda = document.querySelector(".arrow-left");
    const flechaDerecha = document.querySelector(".arrow-right");

    if (flechaIzquierda) {
        flechaIzquierda.addEventListener("click", function() {
            cambiarCurso(-1);
            iniciarCarruselAutomatico();
        });
    }

    if (flechaDerecha) {
        flechaDerecha.addEventListener("click", function() {
            cambiarCurso(1);
            iniciarCarruselAutomatico();
        });
    }


    // MENÚ LATERAL: CONTRAER Y EXPANDIR

    const toggle = document.getElementById("sidebarToggle");

    if (toggle) {

        // Sincronizamos el estado inicial del botón
        const inicialmenteContraido =
            document.body.classList.contains("sidebar-collapsed");

        toggle.setAttribute(
            "aria-expanded",
            String(!inicialmenteContraido)
        );

        toggle.setAttribute(
            "aria-label",
            inicialmenteContraido
                ? "Expandir menú"
                : "Contraer menú"
        );

        toggle.title = inicialmenteContraido
            ? "Expandir menú"
            : "Contraer menú";

        const icono = toggle.querySelector("i");

        if (icono) {
            icono.classList.toggle(
                "fa-angles-left",
                !inicialmenteContraido
            );

            icono.classList.toggle(
                "fa-angles-right",
                inicialmenteContraido
            );
        }

        // Alternamos el menú al hacer clic
        toggle.addEventListener("click", function() {

            const contraido =
                document.body.classList.toggle("sidebar-collapsed");

            toggle.setAttribute(
                "aria-expanded",
                String(!contraido)
            );

            toggle.setAttribute(
                "aria-label",
                contraido
                    ? "Expandir menú"
                    : "Contraer menú"
            );

            toggle.title = contraido
                ? "Expandir menú"
                : "Contraer menú";

            const icono = toggle.querySelector("i");

            if (icono) {
                icono.classList.toggle(
                    "fa-angles-left",
                    !contraido
                );

                icono.classList.toggle(
                    "fa-angles-right",
                    contraido
                );
            }

        });

    }


    // RESEÑAS: VER MÁS Y VER MENOS

    const botonesResena = document.querySelectorAll(".review-more");

    botonesResena.forEach(function(boton) {

        boton.style.cursor = "pointer";

        boton.setAttribute("role", "button");
        boton.setAttribute("tabindex", "0");
        boton.setAttribute("aria-expanded", "false");

        function alternarResena() {

            const tarjeta = boton.closest(".review-card");

            if (!tarjeta) {
                return;
            }

            const texto = tarjeta.querySelector(".review-text");

            if (!texto) {
                return;
            }

            const expandida = tarjeta.classList.toggle("review-expanded");

            boton.textContent = expandida
                ? "Ver menos⌃"
                : "Ver más⌄";

            boton.setAttribute(
                "aria-expanded",
                String(expandida)
            );

        }

        boton.addEventListener("click", alternarResena);

        // Permite activar con Enter o barra espaciadora
        boton.addEventListener("keydown", function(evento) {

            if (
                evento.key === "Enter" ||
                evento.key === " "
            ) {
                evento.preventDefault();
                alternarResena();
            }

        });

    });

});


// HACEMOS ACCESIBLES LAS FUNCIONES DEL CARRUSEL

window.mostrarCurso = mostrarCurso;
window.cambiarCurso = cambiarCurso;
window.irACurso = irACurso;
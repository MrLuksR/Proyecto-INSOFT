// 
// CARRUSEL DE CURSOS
// 


// Curso que se está mostrando actualmente
let cursoActual = 0;


// Obtenemos todos los cursos
const cursos = document.querySelectorAll(".course-slide");


// Obtenemos todos los puntos
const puntos = document.querySelectorAll(".carousel-dot");


// 
// MOSTRAR UN CURSO
// 

function mostrarCurso(indice) {

    // Quitamos "active" de todos los cursos
    cursos.forEach(function(curso) {

        curso.classList.remove("active");

    });


    // Quitamos "active" de todos los puntos
    puntos.forEach(function(punto) {

        punto.classList.remove("active");

    });


    // Activamos el curso seleccionado
    cursos[indice].classList.add("active");


    // Activamos el punto correspondiente
    puntos[indice].classList.add("active");


    // Guardamos el número del curso actual
    cursoActual = indice;

}



// 
// CAMBIAR CURSO
// 

function cambiarCurso(direccion) {

    // Sumamos o restamos 1
    cursoActual = cursoActual + direccion;


    // Si llegamos después del último curso,
    // volvemos al primero
    if (cursoActual >= cursos.length) {

        cursoActual = 0;

    }


    // Si retrocedemos antes del primer curso,
    // vamos al último
    if (cursoActual < 0) {

        cursoActual = cursos.length - 1;

    }


    // Mostramos el nuevo curso
    mostrarCurso(cursoActual);

}



// 
// IR DIRECTAMENTE A UN CURSO
// 

function irACurso(indice) {

    mostrarCurso(indice);

}



// 
// CAMBIO AUTOMÁTICO
// 

// Cada 5 segundos cambia automáticamente

setInterval(function() {

    cambiarCurso(1);

}, 5000);

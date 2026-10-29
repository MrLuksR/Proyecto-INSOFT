// =====================================================
// MOSTRAR Y OCULTAR CONTRASEÑA
// =====================================================

document.addEventListener('DOMContentLoaded', function () {

    // OBTENER ELEMENTOS
    const password = document.getElementById('password');
    const boton = document.getElementById('togglePassword');
    const icono = document.getElementById('iconoPassword');

    // COMPROBAR ELEMENTOS
    if (!password || !boton || !icono) {
        return;
    }

    // RUTAS DE LAS IMÁGENES
    const ojoAbierto = '../Elementos Gráficos/Ojo abierto.png';
    const ojoCerrado = '../Elementos Gráficos/Ojo bloqueado.png';

    // EVENTO DEL BOTÓN
    boton.addEventListener('click', function () {

        // COMPROBAR ESTADO ACTUAL
        const mostrar = password.type === 'password';

        // MOSTRAR U OCULTAR CONTRASEÑA
        password.type = mostrar ? 'text' : 'password';

        // CAMBIAR IMAGEN
        icono.src = mostrar ? ojoAbierto : ojoCerrado;

        // ACTUALIZAR ACCESIBILIDAD
        boton.setAttribute(
            'aria-label',
            mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña'
        );

        boton.setAttribute(
            'aria-pressed',
            String(mostrar)
        );

    });

});
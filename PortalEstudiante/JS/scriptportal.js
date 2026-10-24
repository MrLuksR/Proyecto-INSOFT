document.addEventListener('DOMContentLoaded', function () {
    /* Menú lateral para pantallas pequeñas */
    const sidebar = document.getElementById('portalSidebar');
    const toggle = document.getElementById('portalMenuToggle');

    if (sidebar && toggle) {
        toggle.addEventListener('click', function () {
            const abierto = sidebar.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', abierto ? 'true' : 'false');
        });

        document.addEventListener('click', function (event) {
            if (
                window.innerWidth <= 850 &&
                sidebar.classList.contains('is-open') &&
                !sidebar.contains(event.target) &&
                !toggle.contains(event.target)
            ) {
                sidebar.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 850) {
                sidebar.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* Menú desplegable del usuario */
    const userMenu = document.getElementById('userMenu');
    const userTrigger = document.getElementById('userMenuTrigger');
    const userDropdown = document.getElementById('userMenuDropdown');

    if (userMenu && userTrigger && userDropdown) {
        function abrirMenuUsuario() {
            userDropdown.hidden = false;
            userTrigger.setAttribute('aria-expanded', 'true');
        }

        function cerrarMenuUsuario() {
            userDropdown.hidden = true;
            userTrigger.setAttribute('aria-expanded', 'false');
        }

        userTrigger.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (userDropdown.hidden) {
                abrirMenuUsuario();
            } else {
                cerrarMenuUsuario();
            }
        });

        userDropdown.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        document.addEventListener('click', function (event) {
            if (!userMenu.contains(event.target)) {
                cerrarMenuUsuario();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !userDropdown.hidden) {
                cerrarMenuUsuario();
                userTrigger.focus();
            }
        });
    }

    /* Edición del perfil: cambios en tiempo real y validación */
    const formulario = document.querySelector('.edit-profile-form');
    if (!formulario) return;

    const regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const campos = Array.from(formulario.querySelectorAll('.edit-form-group input:not(:disabled)'));
    const valoresOriginales = new Map(campos.map(campo => [campo, campo.value]));
    const foto = formulario.querySelector('#foto');
    const fotoContenedor = formulario.querySelector('.edit-avatar-container');
    const fotoInicial = fotoContenedor ? fotoContenedor.innerHTML : '';
    let enviando = false;

    // BOTÓN DE GUARDAR CAMBIOS
    const botonGuardar = formulario.querySelector('button[type="submit"]');

    // DESACTIVAR BOTÓN INICIALMENTE
    if (botonGuardar) {
        botonGuardar.disabled = true;
    }

    function validarCampo(campo) {
        const grupo = campo.closest('.edit-form-group');
        const modificado = campo.value !== valoresOriginales.get(campo);
        const correoInvalido = campo.id === 'correo' && campo.value !== '' && !regexCorreo.test(campo.value);
        grupo.classList.toggle('is-modified', modificado);
        grupo.classList.toggle('is-invalid', correoInvalido);
        campo.setCustomValidity(correoInvalido ? 'Ingresá un correo electrónico válido.' : '');
        campo.setAttribute('aria-invalid', correoInvalido ? 'true' : 'false');
    }

    campos.forEach(campo => {

        campo.addEventListener('input', () => {
            validarCampo(campo);
            actualizarBoton();
        });

        campo.addEventListener('change', () => {
            validarCampo(campo);
            actualizarBoton();
        });

        validarCampo(campo);

    });

    if (foto) {
        foto.addEventListener('change', function () {
            const archivo = foto.files[0];
            const tipos = ['image/jpeg', 'image/png', 'image/webp'];
            if (archivo && !tipos.includes(archivo.type)) {
                alert('Seleccioná una imagen JPG, PNG o WEBP.');
                foto.value = '';
            }
            const seleccionada = foto.files.length > 0;
            actualizarBoton();
            const seccionFoto = foto.closest('.edit-profile-photo');
            if (seccionFoto) seccionFoto.classList.toggle('is-modified', seleccionada);
            if (!fotoContenedor) return;
            if (!seleccionada) {
                fotoContenedor.innerHTML = fotoInicial;
                return;
            }
            const lector = new FileReader();
            lector.onload = function (evento) {
                fotoContenedor.innerHTML = '';
                const img = document.createElement('img');
                img.src = evento.target.result;
                img.alt = 'Vista previa';
                img.className = 'profile-avatar';
                fotoContenedor.appendChild(img);
            };
            lector.readAsDataURL(foto.files[0]);
        });
    }

    function hayCambios() {
        return campos.some(campo => campo.value !== valoresOriginales.get(campo)) ||
            Boolean(foto && foto.files.length);
    }

    // ACTIVAR O DESACTIVAR BOTÓN SEGÚN LOS CAMBIOS
    function actualizarBoton() {
        if (!botonGuardar) return;

        botonGuardar.disabled = !hayCambios();
    }

    // El navegador decide el texto del aviso.
    window.addEventListener('beforeunload', function (evento) {
        if (hayCambios() && !enviando) {
            evento.preventDefault();
            evento.returnValue = '';
        }
    });

    formulario.addEventListener('submit', function (evento) {
        campos.forEach(validarCampo);
        const correo = formulario.querySelector('#correo');
        if (correo && !correo.checkValidity()) {
            evento.preventDefault();
            correo.reportValidity();
            correo.focus();
            return;
        }
        // Evitar aviso al enviar correctamente; si el envío falla y no navega,
        // la página siguiente deberá gestionar la persistencia y los errores.
        enviando = true;
    });
});
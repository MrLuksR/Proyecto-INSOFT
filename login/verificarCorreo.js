document.addEventListener('DOMContentLoaded', function () {

    // OBTENER ELEMENTOS
    const formulario = document.getElementById('formularioVerificacion');

    if (!formulario) return;

    const campos = Array.from(
        document.querySelectorAll('.codigo-input')
    );

    const botonVerificar = document.getElementById('botonVerificar');


    // ACTUALIZAR ESTADO DEL FORMULARIO
    function actualizarEstado() {

        // COMPROBAR QUE TODOS LOS CAMPOS TENGAN UN DÍGITO
        const completo = campos.every(campo =>
            /^[0-9]$/.test(campo.value)
        );

        // HABILITAR BOTÓN
        botonVerificar.disabled = !completo;

        // MARCAR CASILLAS COMPLETADAS
        campos.forEach(campo => {

            campo.classList.toggle(
                'completado',
                campo.value !== ''
            );

        });

    }


    // DISTRIBUIR CÓDIGO PEGADO
    function distribuirCodigo(texto, inicio = 0) {

        const numeros = texto.replace(/\D/g, '');

        if (!numeros) return;

        let posicion = inicio;

        for (const numero of numeros) {

            if (posicion >= campos.length) break;

            campos[posicion].value = numero;

            posicion++;

        }

        actualizarEstado();

        campos[Math.min(posicion, campos.length - 1)].focus();

    }


    // CONFIGURAR CADA CASILLA
    campos.forEach((campo, indice) => {

        // INGRESO DE NÚMEROS
        campo.addEventListener('input', function () {

            const valor = campo.value.replace(/\D/g, '');

            if (valor.length > 1) {

                campo.value = '';

                distribuirCodigo(valor, indice);

                return;
            }

            campo.value = valor;

            actualizarEstado();

            // AVANZAR AUTOMÁTICAMENTE
            if (valor !== '' && indice < campos.length - 1) {
                campos[indice + 1].focus();
            }

        });


        // CONTROL DEL TECLADO
        campo.addEventListener('keydown', function (evento) {

            // RETROCEDER AL BORRAR
            if (evento.key === 'Backspace') {

                if (campo.value === '' && indice > 0) {

                    campos[indice - 1].value = '';
                    campos[indice - 1].focus();

                    actualizarEstado();

                }

            }

            // NAVEGAR CON FLECHAS
            if (evento.key === 'ArrowLeft' && indice > 0) {

                evento.preventDefault();
                campos[indice - 1].focus();

            }

            if (evento.key === 'ArrowRight' && indice < campos.length - 1) {

                evento.preventDefault();
                campos[indice + 1].focus();

            }

        });


        // PEGAR CÓDIGO COMPLETO
        campo.addEventListener('paste', function (evento) {

            evento.preventDefault();

            const texto = evento.clipboardData.getData('text');

            distribuirCodigo(texto, indice);

        });

    });


    // VALIDAR ANTES DE ENVIAR
    formulario.addEventListener('submit', function (evento) {

        actualizarEstado();

        const completo = campos.every(campo =>
            /^[0-9]$/.test(campo.value)
        );

        if (!completo) {

            evento.preventDefault();
            campos[0].focus();

            return;
        }

        // EVITAR ENVÍOS REPETIDOS
        botonVerificar.disabled = true;

    });


    // ESTADO INICIAL
    actualizarEstado();

});
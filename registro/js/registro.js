
document.addEventListener("DOMContentLoaded", function () {
    "use strict";

    // ELEMENTOS DEL FORMULARIO

    const formulario = document.getElementById("formularioRegistro");

    const nombre = document.getElementById("nombre");
    const apellido = document.getElementById("apellido");
    const cedula = document.getElementById("cedula");
    const fecha = document.getElementById("fecha_nacimiento");
    const telefono = document.getElementById("telefono");
    const email = document.getElementById("email");
    const username = document.getElementById("username");
    const password = document.getElementById("password");
    const mostrarPassword = document.getElementById("togglePassword");
    const curso = document.getElementById("curso");
    const foto = document.getElementById("foto");

    const datosTutor = document.getElementById("datosTutor");
    const nombreTutor = document.getElementById("nombre_tutor");
    const telefonoTutor = document.getElementById("telefono_tutor");

    const estadoCedula = document.getElementById("estadoCedula");

    // ELEMENTOS DE LA CREDENCIAL

    const previewNombre = document.getElementById("previewNombre");
    const previewApellido = document.getElementById("previewApellido");
    const previewCedula = document.getElementById("previewCedula");
    const previewFecha = document.getElementById("previewFecha");
    const previewCurso = document.getElementById("previewCurso");

    const previewFoto = document.getElementById("previewFoto");
    const textoFoto = document.getElementById("textoFoto");

    // FUNCIONES AUXILIARES

    function actualizarTexto(elemento, valor, predeterminado) {
        if (!elemento) return;

        const texto = String(valor ?? "").trim();

        elemento.textContent = texto || predeterminado || "—";
    }

    function obtenerValor(elemento) {
        return elemento ? elemento.value.trim() : "";
    }

    // NOMBRE Y APELLIDO

    function actualizarNombre() {
        actualizarTexto(
            previewNombre,
            obtenerValor(nombre),
            "Nombre"
        );
    }

    function actualizarApellido() {
        actualizarTexto(
            previewApellido,
            obtenerValor(apellido),
            "Apellido"
        );
    }

    if (nombre) {
        nombre.addEventListener("input", actualizarNombre);
        actualizarNombre();
    }

    if (apellido) {
        apellido.addEventListener("input", actualizarApellido);
        actualizarApellido();
    }

    // CÉDULA URUGUAYA

    function soloDigitos(valor) {
        return String(valor ?? "")
            .replace(/\D/g, "")
            .slice(0, 8);
    }

    function formatearCedula(valor) {
        const numeros = soloDigitos(valor);

        if (!numeros) return "";

        if (numeros.length === 1) {
            return numeros;
        }

        const cuerpo = numeros.slice(0, -1);
        const verificador = numeros.slice(-1);

        const cuerpoFormateado = cuerpo.replace(
            /\B(?=(\d{3})+(?!\d))/g,
            "."
        );

        return cuerpoFormateado + "-" + verificador;
    }

    function actualizarEstadoCedula() {
        if (!cedula || !estadoCedula) return;

        const cantidad = soloDigitos(cedula.value).length;

        estadoCedula.classList.remove("correcta", "incorrecta");

        if (cantidad === 0) {
            estadoCedula.textContent =
                "Ingresá tu cédula uruguaya.";
        } else if (cantidad === 8) {
            estadoCedula.classList.add("correcta");
            estadoCedula.textContent =
                "Cédula completa: verificá que los datos sean correctos.";
        } else {
            estadoCedula.classList.add("incorrecta");
            estadoCedula.textContent =
                "Ingresaste " + cantidad + " de 8 dígitos.";
        }
    }

    if (cedula) {
        cedula.addEventListener("input", function () {
            cedula.value = formatearCedula(cedula.value);

            actualizarTexto(
                previewCedula,
                cedula.value,
                "—"
            );

            actualizarEstadoCedula();
        });

        if (cedula.value.trim()) {
            cedula.value = formatearCedula(cedula.value);
        }

        actualizarTexto(previewCedula, cedula.value, "—");
        actualizarEstadoCedula();
    }

    // FECHA DE NACIMIENTO

    function esFechaValida(fechaTexto) {
        if (!fechaTexto) return false;

        const partes = fechaTexto.split("-");

        if (partes.length !== 3) return false;

        const anio = Number(partes[0]);
        const mes = Number(partes[1]);
        const dia = Number(partes[2]);

        const fechaComprobacion = new Date(
            anio,
            mes - 1,
            dia
        );

        return (
            fechaComprobacion.getFullYear() === anio &&
            fechaComprobacion.getMonth() === mes - 1 &&
            fechaComprobacion.getDate() === dia
        );
    }

    function actualizarFecha() {
        if (!fecha) return;

        if (!fecha.value || !esFechaValida(fecha.value)) {
            actualizarTexto(previewFecha, "", "—");
            actualizarTutor();
            return;
        }

        const partes = fecha.value.split("-");

        actualizarTexto(
            previewFecha,
            partes[2] + "/" + partes[1] + "/" + partes[0],
            "—"
        );

        actualizarTutor();
    }

    // No permitir fechas futuras.
    if (fecha) {
        const hoy = new Date();
        const anio = hoy.getFullYear();
        const mes = String(hoy.getMonth() + 1).padStart(2, "0");
        const dia = String(hoy.getDate()).padStart(2, "0");

        fecha.max = anio + "-" + mes + "-" + dia;

        fecha.addEventListener("input", actualizarFecha);
        fecha.addEventListener("change", actualizarFecha);

        actualizarFecha();
    }

    // =========================================
    // ADULTO RESPONSABLE
    // =========================================

    function esMenorDeEdad(fechaTexto) {
        if (!esFechaValida(fechaTexto)) return false;

        const partes = fechaTexto.split("-");

        const nacimiento = new Date(
            Number(partes[0]),
            Number(partes[1]) - 1,
            Number(partes[2])
        );

        const hoy = new Date();

        let edad = hoy.getFullYear() - nacimiento.getFullYear();

        const aunNoCumplio =
            hoy.getMonth() < nacimiento.getMonth() ||
            (
                hoy.getMonth() === nacimiento.getMonth() &&
                hoy.getDate() < nacimiento.getDate()
            );

        if (aunNoCumplio) {
            edad--;
        }

        return edad < 18;
    }

    function actualizarTutor() {
        if (!datosTutor) return;

        const requiereTutor = esMenorDeEdad(
            fecha ? fecha.value : ""
        );

        datosTutor.hidden = !requiereTutor;

        if (nombreTutor) {
            nombreTutor.required = requiereTutor;
        }

        if (telefonoTutor) {
            telefonoTutor.required = requiereTutor;
        }
    }

    // CURSO SELECCIONADO

    function actualizarCurso() {
        if (!curso) return;

        const opcion = curso.options[curso.selectedIndex];

        const texto = curso.value && opcion
            ? opcion.textContent.trim()
            : "";

        actualizarTexto(
            previewCurso,
            texto,
            "Sin seleccionar"
        );
    }

    if (curso) {
        curso.addEventListener("change", actualizarCurso);
        actualizarCurso();
    }

    // FOTOGRAFÍA DE PERFIL

    let urlFotoAnterior = null;

    function liberarFotoAnterior() {
        if (urlFotoAnterior) {
            URL.revokeObjectURL(urlFotoAnterior);
            urlFotoAnterior = null;
        }
    }

    function restaurarFoto() {
        if (previewFoto) {
            previewFoto.onload = null;
            previewFoto.onerror = null;
            previewFoto.removeAttribute("src");
            previewFoto.hidden = true;
        }

        if (textoFoto) {
            textoFoto.hidden = false;
        }

        liberarFotoAnterior();
    }

    if (foto) {
        foto.addEventListener("change", function () {
            const archivo = foto.files && foto.files[0];

            if (!archivo) {
                restaurarFoto();
                return;
            }

            const formatosPermitidos = [
                "image/jpeg",
                "image/png",
                "image/webp"
            ];

            if (!formatosPermitidos.includes(archivo.type)) {
                alert(
                    "Seleccioná una fotografía JPG, PNG o WEBP."
                );

                foto.value = "";
                restaurarFoto();
                return;
            }

            if (archivo.size > 5 * 1024 * 1024) {
                alert(
                    "La fotografía no puede superar los 5 MB."
                );

                foto.value = "";
                restaurarFoto();
                return;
            }

            if (!previewFoto) return;

            liberarFotoAnterior();

            const nuevaURL = URL.createObjectURL(archivo);
            urlFotoAnterior = nuevaURL;

            previewFoto.onload = function () {
                if (urlFotoAnterior !== nuevaURL) return;

                previewFoto.hidden = false;

                if (textoFoto) {
                    textoFoto.hidden = true;
                }
            };

            previewFoto.onerror = function () {
                if (urlFotoAnterior !== nuevaURL) return;

                restaurarFoto();
                foto.value = "";

                alert(
                    "No se pudo mostrar la fotografía seleccionada."
                );
            };

            previewFoto.src = nuevaURL;
        });
    }

    // MOSTRAR U OCULTAR CONTRASEÑA

    if (password && mostrarPassword) {
        mostrarPassword.addEventListener("click", function () {
            const mostrar = password.type === "password";

            password.type = mostrar ? "text" : "password";

            mostrarPassword.textContent = mostrar
                ? "Ocultar"
                : "Ver";

            mostrarPassword.setAttribute(
                "aria-pressed",
                String(mostrar)
            );

            mostrarPassword.setAttribute(
                "aria-label",
                mostrar
                    ? "Ocultar contraseña"
                    : "Mostrar contraseña"
            );
        });
    }

    // VALIDACIÓN DEL FORMULARIO

    if (formulario) {
        formulario.addEventListener("submit", function (evento) {
            // Validar la fecha.
            if (
                fecha &&
                (
                    !esFechaValida(fecha.value) ||
                    fecha.value > fecha.max
                )
            ) {
                evento.preventDefault();

                alert(
                    "Ingresá una fecha de nacimiento válida."
                );

                fecha.focus();
                return;
            }

            // Validar los ocho dígitos de la cédula.
            if (cedula && soloDigitos(cedula.value).length !== 8) {
                evento.preventDefault();

                alert(
                    "La cédula debe contener 8 dígitos. " +
                    "Ejemplo: 4.567.890-1."
                );

                cedula.focus();
                return;
            }

            // Validar la contraseña.
            if (password && password.value.length < 8) {
                evento.preventDefault();

                alert(
                    "La contraseña debe tener al menos 8 caracteres."
                );

                password.focus();
                return;
            }

            // Validar el curso.
            if (curso && !curso.value) {
                evento.preventDefault();

                alert("Seleccioná un curso de Informática.");

                curso.focus();
                return;
            }

            // Validar al adulto responsable si es menor.
            if (esMenorDeEdad(fecha ? fecha.value : "")) {
                const camposTutor = [
                    nombreTutor,
                    telefonoTutor
                ];

                for (const campo of camposTutor) {
                    if (campo && !campo.value.trim()) {
                        evento.preventDefault();

                        alert(
                            "Completá los datos del adulto responsable."
                        );

                        campo.focus();
                        return;
                    }
                }
            }
        });
    }

    // Liberar la URL temporal de la fotografía al salir.
    window.addEventListener("pagehide", liberarFotoAnterior);
});

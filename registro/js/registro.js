document.addEventListener("DOMContentLoaded", function () {
    "use strict";

    const formulario = document.getElementById("formularioRegistro");
    if (!formulario) return;

    const pasos = Array.from(formulario.querySelectorAll(".paso-formulario"));
    const indicadores = Array.from(document.querySelectorAll("[data-indicador]"));
    const lineas = Array.from(document.querySelectorAll(".paso-linea > span"));
    const nombre = document.getElementById("nombre");
    const apellido = document.getElementById("apellido");
    const cedula = document.getElementById("cedula");
    const fecha = document.getElementById("fecha_nacimiento");
    const telefono = document.getElementById("telefono");
    const email = document.getElementById("email");
    const username = document.getElementById("username");
    const password = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");
    const cursoSelect = document.getElementById("curso");
    const foto = document.getElementById("foto");
    const estadoCedula = document.getElementById("estadoCedula");
    const estadoCurso = document.getElementById("estadoCurso");
    const estadoFoto = document.getElementById("estadoFoto");
    const datosTutor = document.getElementById("datosTutor");
    const nombreTutor = document.getElementById("nombre_tutor");
    const telefonoTutor = document.getElementById("telefono_tutor");
    const botonCrearCuenta = document.getElementById("botonCrearCuenta");

    const previewNombre = document.getElementById("previewNombre");
    const previewApellido = document.getElementById("previewApellido");
    const previewCedula = document.getElementById("previewCedula");
    const previewFecha = document.getElementById("previewFecha");
    const previewCurso = document.getElementById("previewCurso");
    const previewFoto = document.getElementById("previewFoto");
    const textoFoto = document.getElementById("textoFoto");
    const fortaleza = document.querySelector(".fortaleza-password");
    const ayudaPassword = document.getElementById("ayudaPassword");

    let pasoActual = 0;
    let urlFotoAnterior = null;

    function valor(el) {
        return el ? el.value.trim() : "";
    }

    function texto(el, contenido, alternativo) {
        if (el) el.textContent = contenido || alternativo || "—";
    }

    function digitos(valorTexto) {
        return String(valorTexto || "").replace(/\D/g, "").slice(0, 8);
    }

    function formatearCedula(valorTexto) {
        const numeros = digitos(valorTexto);
        if (numeros.length < 2) return numeros;
        const cuerpo = numeros.slice(0, -1).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        return cuerpo + "-" + numeros.slice(-1);
    }

    function fechaValida(textoFecha) {
        if (!textoFecha) return false;
        const partes = textoFecha.split("-");
        if (partes.length !== 3) return false;
        const anio = Number(partes[0]), mes = Number(partes[1]), dia = Number(partes[2]);
        const fechaObj = new Date(anio, mes - 1, dia);
        return fechaObj.getFullYear() === anio &&
            fechaObj.getMonth() === mes - 1 &&
            fechaObj.getDate() === dia;
    }

    function esMenor(textoFecha) {
        if (!fechaValida(textoFecha)) return false;
        const [anio, mes, dia] = textoFecha.split("-").map(Number);
        const hoy = new Date();
        let edad = hoy.getFullYear() - anio;
        if (hoy.getMonth() + 1 < mes || (hoy.getMonth() + 1 === mes && hoy.getDate() < dia)) edad--;
        return edad < 18;
    }

    function actualizarTutor() {
        if (!datosTutor) return;
        const requiere = esMenor(fecha ? fecha.value : "");
        datosTutor.hidden = !requiere;
        if (nombreTutor) nombreTutor.required = requiere;
        if (telefonoTutor) telefonoTutor.required = requiere;
    }

    function actualizarCredencial() {
        texto(previewNombre, valor(nombre), "Tu nombre");
        texto(previewApellido, valor(apellido), "Tu apellido");
        texto(previewCedula, valor(cedula), "—");

        if (fecha && fechaValida(fecha.value)) {
            const [a, m, d] = fecha.value.split("-");
            texto(previewFecha, `${d}/${m}/${a}`, "—");
        } else {
            texto(previewFecha, "", "—");
        }

        texto(previewCurso, valor(cursoSelect), "Elegí tu curso");
        actualizarTutor();
    }

    function actualizarIndicadores() {
        indicadores.forEach((indicador, indice) => {
            indicador.classList.toggle("activo", indice === pasoActual);
            indicador.classList.toggle("completado", indice < pasoActual);
        });
        lineas.forEach((linea, indice) => {
            linea.style.width = indice < pasoActual ? "100%" : "0";
        });
    }

    function mostrarPaso(indice) {
        pasoActual = Math.max(0, Math.min(indice, pasos.length - 1));
        pasos.forEach((paso, i) => {
            const activo = i === pasoActual;
            paso.hidden = !activo;
            paso.classList.toggle("activo", activo);
        });
        actualizarIndicadores();
        if (pasoActual === 2) actualizarResumen();
        const encabezado = pasos[pasoActual].querySelector("h3");
        if (encabezado) encabezado.setAttribute("tabindex", "-1");
        if (encabezado) encabezado.focus({ preventScroll: true });
        formulario.scrollIntoView({ behavior: "smooth", block: "start" });
    }

    function validarCampo(campo) {

        if (!campo || campo.disabled) return true;

        const valorCampo = valor(campo);

        let valido = true;
        let mensaje = "";

        // VALIDAR CAMPOS OBLIGATORIOS
        if (campo.required && !valorCampo) {

            valido = false;
            mensaje = "Completá este campo para continuar.";

        } else if (campo.type === "email" && valorCampo && !campo.validity.valid) {

            valido = false;
            mensaje = "Ingresá un correo electrónico válido.";

        } else if (campo.id === "cedula" && digitos(valorCampo).length !== 8) {

            valido = false;
            mensaje = "La cédula debe contener 8 dígitos.";

        } else if (
            campo.id === "fecha_nacimiento" &&
            (!fechaValida(valorCampo) || valorCampo > campo.max)
        ) {

            valido = false;
            mensaje = "Ingresá una fecha de nacimiento válida.";

        } else if (campo.id === "password" && campo.value.length < 8) {

            valido = false;
            mensaje = "La contraseña debe tener al menos 8 caracteres.";

        }

        // BUSCAR CONTENEDOR DEL CAMPO
        const contenedor = campo.closest(".campo") || campo.parentElement;

        // BUSCAR MENSAJE EXISTENTE
        let elementoError = contenedor.querySelector(".mensaje-validacion");

        if (!valido) {

            // MARCAR CAMPO COMO INCORRECTO
            campo.classList.add("invalido");
            campo.setAttribute("aria-invalid", "true");
            campo.dataset.errorValidacion = mensaje;

            // CREAR MENSAJE SI NO EXISTE
            if (!elementoError) {

                elementoError = document.createElement("small");
                elementoError.className = "mensaje-validacion";

                contenedor.appendChild(elementoError);
            }

            // MOSTRAR ERROR
            elementoError.textContent = mensaje;
            elementoError.hidden = false;

        } else {

            // QUITAR ESTADO DE ERROR
            campo.classList.remove("invalido");
            campo.removeAttribute("aria-invalid");

            delete campo.dataset.errorValidacion;

            // ELIMINAR MENSAJE
            if (elementoError) {
                elementoError.remove();
            }
        }

        return valido;
    }

    function camposDelPaso(indice) {
        if (indice === 0) return [nombre, apellido, cedula, fecha];
        if (indice === 1) {
            const lista = [email, username, password];
            if (esMenor(fecha ? fecha.value : "")) lista.push(nombreTutor, telefonoTutor);
            return lista;
        }
        return [];
    }

    function validarPaso(indice) {
        let valido = true;
        for (const campo of camposDelPaso(indice)) {
            if (!validarCampo(campo)) {
                valido = false;
            }
        }

        if (indice === 1 && !valor(cursoSelect)) {
            if (cursoSelect) {
                cursoSelect.classList.add("invalido");
                cursoSelect.setAttribute("aria-invalid", "true");
            }
            valido = false;
            if (estadoCurso) {
                estadoCurso.textContent = "Seleccioná un curso para continuar.";
                estadoCurso.style.color = "#c43b4b";
            }
            if (cursoSelect) cursoSelect.scrollIntoView({ behavior: "smooth", block: "center" });
        } else if (indice === 1) {
            if (cursoSelect) {
                cursoSelect.classList.remove("invalido");
                cursoSelect.removeAttribute("aria-invalid");
            }
            if (estadoCurso) estadoCurso.textContent = "";
        }

        if (indice === 1 && foto && foto.files && foto.files[0]) {
            const archivo = foto.files[0];
            const tipos = ["image/jpeg", "image/png", "image/webp"];
            if (!tipos.includes(archivo.type) || archivo.size > 5 * 1024 * 1024) {
                valido = false;
                if (estadoFoto) {
                    estadoFoto.textContent = !tipos.includes(archivo.type)
                        ? "El formato debe ser JPG, PNG o WEBP."
                        : "La fotografía no puede superar los 5 MB.";
                    estadoFoto.style.color = "#c43b4b";
                }
            }
        }

        if (!valido) {
            const primerInvalido = pasos[indice].querySelector(".invalido");
            if (primerInvalido) {
                primerInvalido.focus();
                primerInvalido.scrollIntoView({ behavior: "smooth", block: "center" });
            }
        }
        return valido;
    }

    function actualizarResumen() {
        texto(document.getElementById("resumenNombre"), [valor(nombre), valor(apellido)].filter(Boolean).join(" "), "—");
        texto(document.getElementById("resumenCedula"), valor(cedula), "—");
        if (fecha && fechaValida(fecha.value)) {
            const [a, m, d] = fecha.value.split("-");
            texto(document.getElementById("resumenFecha"), `${d}/${m}/${a}`, "—");
        } else texto(document.getElementById("resumenFecha"), "", "—");
        texto(document.getElementById("resumenEmail"), valor(email), "—");
        texto(document.getElementById("resumenUsername"), valor(username), "—");
        texto(document.getElementById("resumenCurso"), valor(cursoSelect), "—");
        texto(document.getElementById("resumenFoto"), foto && foto.files && foto.files[0] ? foto.files[0].name : "", "Sin fotografía");
    }

    formulario.querySelectorAll(".boton-siguiente").forEach((boton) => {
        boton.addEventListener("click", function () {
            if (validarPaso(pasoActual)) mostrarPaso(pasoActual + 1);
        });
    });
    formulario.querySelectorAll(".boton-anterior").forEach((boton) => {
        boton.addEventListener("click", function () { mostrarPaso(pasoActual - 1); });
    });

    [nombre, apellido, telefono, email, username].forEach((campo) => {
        if (!campo) return;
        campo.addEventListener("input", function () {
            if (campo.classList.contains("invalido")) validarCampo(campo);
            actualizarCredencial();
        });
    });

    [nombreTutor, telefonoTutor].forEach((campo) => {
        if (!campo) return;
        campo.addEventListener("input", function () {
            if (campo.classList.contains("invalido")) validarCampo(campo);
        });
    });

    if (cedula) {
        cedula.addEventListener("input", function () {
            cedula.value = formatearCedula(cedula.value);
            actualizarCredencial();
            const cantidad = digitos(cedula.value).length;
            if (estadoCedula) {
                estadoCedula.classList.remove("correcta", "incorrecta");
                if (cantidad === 8) {
                    estadoCedula.classList.add("correcta");
                    estadoCedula.textContent = "Cédula completa. Revisá que los datos sean correctos.";
                } else if (cantidad > 0) {
                    estadoCedula.classList.add("incorrecta");
                    estadoCedula.textContent = `Ingresaste ${cantidad} de 8 dígitos.`;
                } else {
                    estadoCedula.textContent = "Ingresá tu cédula uruguaya de 8 dígitos.";
                }
            }
            if (cedula.classList.contains("invalido")) validarCampo(cedula);
        });
        if (cedula.value) cedula.value = formatearCedula(cedula.value);
    }

    if (fecha) {
        const hoy = new Date();
        fecha.max = `${hoy.getFullYear()}-${String(hoy.getMonth() + 1).padStart(2, "0")}-${String(hoy.getDate()).padStart(2, "0")}`;
        fecha.addEventListener("change", function () {
            if (fecha.classList.contains("invalido")) validarCampo(fecha);
            actualizarCredencial();
        });
        fecha.addEventListener("input", actualizarCredencial);
    }

    if (cursoSelect) {
        cursoSelect.addEventListener("change", function () {
            cursoSelect.classList.toggle("seleccionado", !!valor(cursoSelect));
            cursoSelect.classList.remove("invalido");
            cursoSelect.removeAttribute("aria-invalid");
            if (estadoCurso) estadoCurso.textContent = "";
            actualizarCredencial();
            actualizarResumen();
        });
        cursoSelect.classList.toggle("seleccionado", !!valor(cursoSelect));
    }

    if (togglePassword && password) {
        togglePassword.addEventListener("click", function () {
            const mostrar = password.type === "password";
            password.type = mostrar ? "text" : "password";
            togglePassword.textContent = mostrar ? "Ocultar" : "Ver";
            togglePassword.setAttribute("aria-pressed", String(mostrar));
            togglePassword.setAttribute("aria-label", mostrar ? "Ocultar contraseña" : "Mostrar contraseña");
        });
    }

    if (password && fortaleza) {
        password.addEventListener("input", function () {
            const valorPassword = password.value;
            let puntos = 0;
            if (valorPassword.length >= 8) puntos++;
            if (/[a-z]/.test(valorPassword) && /[A-Z]/.test(valorPassword)) puntos++;
            if (/\d/.test(valorPassword)) puntos++;
            if (/[^a-zA-Z0-9]/.test(valorPassword)) puntos++;
            fortaleza.className = "fortaleza-password" + (valorPassword.length ? ` nivel-${Math.max(1, puntos)}` : "");
            if (ayudaPassword) {
                ayudaPassword.textContent = valorPassword.length < 8
                    ? "Usá al menos 8 caracteres."
                    : puntos >= 3 ? "Buena contraseña. ¡Bien ahí!" : "Podés hacerla más segura combinando letras, números y símbolos.";
            }
            if (password.classList.contains("invalido")) validarCampo(password);
        });
    }

    function liberarFotoAnterior() {
        if (urlFotoAnterior) {
            URL.revokeObjectURL(urlFotoAnterior);
            urlFotoAnterior = null;
        }
    }

    function restaurarFoto() {
        liberarFotoAnterior();
        if (previewFoto) {
            previewFoto.onload = null;
            previewFoto.onerror = null;
            previewFoto.removeAttribute("src");
            previewFoto.hidden = true;
        }
        if (textoFoto) textoFoto.hidden = false;
    }

    if (foto) {
        foto.addEventListener("change", function () {
            const archivo = foto.files && foto.files[0];
            if (estadoFoto) {
                estadoFoto.textContent = "";
                estadoFoto.style.color = "";
            }
            if (!archivo) {
                restaurarFoto();
                actualizarResumen();
                return;
            }
            const tipos = ["image/jpeg", "image/png", "image/webp"];
            if (!tipos.includes(archivo.type)) {
                if (estadoFoto) {
                    estadoFoto.textContent = "Elegí una imagen JPG, PNG o WEBP.";
                    estadoFoto.style.color = "#c43b4b";
                }
                foto.value = "";
                restaurarFoto();
                return;
            }
            if (archivo.size > 5 * 1024 * 1024) {
                if (estadoFoto) {
                    estadoFoto.textContent = "La fotografía no puede superar los 5 MB.";
                    estadoFoto.style.color = "#c43b4b";
                }
                foto.value = "";
                restaurarFoto();
                return;
            }
            if (estadoFoto) {
                estadoFoto.textContent = archivo.name;
                estadoFoto.style.color = "#15845b";
            }
            if (previewFoto) {
                liberarFotoAnterior();
                const nuevaURL = URL.createObjectURL(archivo);
                urlFotoAnterior = nuevaURL;
                previewFoto.onload = function () {
                    if (urlFotoAnterior !== nuevaURL) return;
                    previewFoto.hidden = false;
                    if (textoFoto) textoFoto.hidden = true;
                };
                previewFoto.onerror = function () {
                    if (urlFotoAnterior !== nuevaURL) return;
                    restaurarFoto();
                    foto.value = "";
                    if (estadoFoto) {
                        estadoFoto.textContent = "No se pudo mostrar la imagen seleccionada.";
                        estadoFoto.style.color = "#c43b4b";
                    }
                };
                previewFoto.src = nuevaURL;
            }
            actualizarResumen();
        });
    }

    formulario.addEventListener("submit", function (evento) {
        if (!validarPaso(0)) {
            evento.preventDefault();
            mostrarPaso(0);
            return;
        }
        if (!validarPaso(1)) {
            evento.preventDefault();
            mostrarPaso(1);
            return;
        }
        if (esMenor(fecha ? fecha.value : "")) {
            if (!valor(nombreTutor) || !valor(telefonoTutor)) {
                evento.preventDefault();
                mostrarPaso(1);
                validarPaso(1);
                return;
            }
        }
        if (botonCrearCuenta) {
            botonCrearCuenta.disabled = true;
            botonCrearCuenta.innerHTML = 'Enviando registro <span aria-hidden="true">…</span>';
        }
    });

    window.addEventListener("pagehide", liberarFotoAnterior);
    actualizarCredencial();
    actualizarIndicadores();
});


// REGISTRO INADI
// VISTA PREVIA EN TIEMPO REAL

document.addEventListener("DOMContentLoaded", function () {


    
    // ELEMENTOS DEL FORMULARIO


    const nombre =
        document.getElementById("nombre");

    const apellido =
        document.getElementById("apellido");

    const cedula =
        document.getElementById("cedula");

    const fecha =
        document.getElementById("fecha");

    const tipoUsuario =
        document.getElementById("tipo_usuario");

    const foto =
        document.getElementById("foto");

    const datosTutor =
        document.getElementById("datosTutor");



    // ELEMENTOS DE LA CREDENCIAL
    

    const vistaNombre =
        document.getElementById("vistaNombre");

    const vistaApellido =
        document.getElementById("vistaApellido");

    const vistaCedula =
        document.getElementById("vistaCedula");

    const vistaFecha =
        document.getElementById("vistaFecha");

    const vistaRol =
        document.getElementById("vistaRol");

    const vistaFoto =
        document.getElementById("vistaFoto");

    const estadoCedula =
        document.getElementById("estadoCedula");



    
    // FUNCIÓN PARA ACTUALIZAR TEXTO
    

    function actualizarTexto(elemento, valor) {

        if (valor.trim() === "") {

            elemento.textContent = "";

        } else {

            elemento.textContent =
                valor.trim();
        }
    }



    
    // NOMBRE
    

    nombre.addEventListener("input", function () {

        actualizarTexto(
            vistaNombre,
            nombre.value
        );

    });



    
    // APELLIDO
    

    apellido.addEventListener("input", function () {

        actualizarTexto(
            vistaApellido,
            apellido.value
        );

    });



    
    // CÉDULA URUGUAYA
    

    function formatearCedula(valor) {

        // Dejar únicamente números

        let numeros =
            valor.replace(/\D/g, "");


        // Máximo 8 números
        // Ejemplo:
        // 41234567

        numeros =
            numeros.substring(0, 8);


        // Si todavía no hay números

        if (numeros.length === 0) {

            return "";
        }


        // Si tiene menos de 2 números

        if (numeros.length === 1) {

            return numeros;
        }


        // Separar dígito verificador

        let cuerpo =
            numeros.slice(0, -1);

        let verificador =
            numeros.slice(-1);


        // Agregar puntos cada tres números

        let partes = [];

        while (cuerpo.length > 3) {

            partes.unshift(
                cuerpo.slice(-3)
            );

            cuerpo =
                cuerpo.slice(0, -3);
        }

        partes.unshift(cuerpo);


        // Resultado final

        return (
            partes.join(".")
            + "-"
            + verificador
        );
    }



    
    // ACTUALIZAR CÉDULA
    

    cedula.addEventListener("input", function () {

        const posicion =
            cedula.selectionStart;

        const valorAnterior =
            cedula.value;


        const soloNumeros =
            valorAnterior.replace(/\D/g, "");


        const formato =
            formatearCedula(
                soloNumeros
            );


        cedula.value =
            formato;


        vistaCedula.textContent =
            formato;


        
        // INDICADOR VISUAL
        

        estadoCedula.classList.remove(
            "correcta",
            "incorrecta"
        );


        if (soloNumeros.length === 8) {

            estadoCedula.classList.add(
                "correcta"
            );

        } else if (
            soloNumeros.length > 0
        ) {

            estadoCedula.classList.add(
                "incorrecta"
            );
        }

    });



    
    // FECHA
    

    fecha.addEventListener("change", function () {

        if (fecha.value === "") {

            vistaFecha.textContent = "";

            return;
        }


        const partes =
            fecha.value.split("-");


        if (partes.length === 3) {

            const anio =
                partes[0];

            const mes =
                partes[1];

            const dia =
                partes[2];


            vistaFecha.textContent =
                `${dia}/${mes}/${anio}`;
        }

    });



    
    // TIPO DE USUARIO
    

    tipoUsuario.addEventListener(
        "change",
        function () {

            
            // MOSTRAR / OCULTAR DATOS DEL TUTOR
            

            if (
                tipoUsuario.value === "tutor"
            ) {

                datosTutor.classList.add(
                    "activo"
                );

            } else {

                datosTutor.classList.remove(
                    "activo"
                );
            }


            
            // CAMBIAR ROL EN CREDENCIAL
            

            const roles = {

                estudiante:
                    "Estudiante",

                docente:
                    "Docente",

                tutor:
                    "Tutor",

                director:
                    "Director"

            };


            vistaRol.textContent =
                roles[tipoUsuario.value] || "";

        }
    );



    
    // FOTOGRAFÍA
    

    foto.addEventListener(
        "change",
        function () {

            const archivo =
                foto.files[0];


            if (!archivo) {

                return;
            }


            // Verificar que sea una imagen

            if (
                !archivo.type.startsWith(
                    "image/"
                )
            ) {

                alert(
                    "Por favor seleccioná una imagen válida."
                );

                foto.value = "";

                return;
            }


            const lector =
                new FileReader();


            lector.onload =
                function (evento) {

                    vistaFoto.innerHTML = "";


                    const imagen =
                        document.createElement(
                            "img"
                        );


                    imagen.src =
                        evento.target.result;


                    imagen.alt =
                        "Vista previa de la fotografía";


                    vistaFoto.appendChild(
                        imagen
                    );

                };


            lector.readAsDataURL(
                archivo
            );

        }
    );



    
    // EVITAR INGRESAR LETRAS EN CÉDULA
    

    cedula.addEventListener(
        "keypress",
        function (evento) {

            if (
                !/[0-9]/.test(
                    evento.key
                )
            ) {

                evento.preventDefault();
            }

        }
    );



    
    // VALIDACIÓN ANTES DE ENVIAR
    

    const formulario =
        document.getElementById(
            "formRegistro"
        );


    formulario.addEventListener(
        "submit",
        function (evento) {

            const numeros =
                cedula.value.replace(
                    /\D/g,
                    ""
                );


            // La cédula debe tener 8 dígitos

            if (
                numeros.length !== 8
            ) {

                evento.preventDefault();


                alert(
                    "La cédula debe contener 8 dígitos. Ejemplo: 4.123.456-7"
                );


                cedula.focus();

                return;
            }

        }
    );



    
    // CÉDULA DEL ESTUDIANTE REPRESENTADO
    

    const cedulaRepresentado =
        document.getElementById(
            "cedula_representado"
        );


    if (cedulaRepresentado) {

        cedulaRepresentado.addEventListener(
            "input",
            function () {

                const numeros =
                    cedulaRepresentado.value
                        .replace(/\D/g, "")
                        .substring(0, 8);


                cedulaRepresentado.value =
                    formatearCedula(
                        numeros
                    );

            }
        );


        cedulaRepresentado.addEventListener(
            "keypress",
            function (evento) {

                if (
                    !/[0-9]/.test(
                        evento.key
                    )
                ) {

                    evento.preventDefault();
                }

            }
        );

    }

});

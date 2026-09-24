
<?php

// VARIABLES PARA LA CREDENCIAL


$nombre = "";
$apellido = "";
$cedula = "";
$fecha = "";
$rol = "";
$foto = "";



// VERIFICAR SI EL FORMULARIO FUE ENVIADO


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    
    // RECIBIR DATOS
    

    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $cedula = trim($_POST["cedula"] ?? "");
    $fecha = $_POST["fecha"] ?? "";
    $tipo_usuario = $_POST["tipo_usuario"] ?? "";


    
    // DETERMINAR EL ROL
    

    $roles = [
        "estudiante" => "Estudiante",
        "docente" => "Docente",
        "tutor" => "Tutor",
        "director" => "Director"
    ];

    $rol = $roles[$tipo_usuario] ?? "";


    
    // PROCESAR FOTOGRAFÍA
    

    if (
        isset($_FILES["foto"]) &&
        $_FILES["foto"]["error"] === UPLOAD_ERR_OK
    ) {

        $nombreFoto = basename($_FILES["foto"]["name"]);

        $carpeta = "uploads/";


        // Crear carpeta si no existe

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }


        // Crear un nombre único para evitar
        // que una fotografía reemplace otra

        $extension = pathinfo($nombreFoto, PATHINFO_EXTENSION);

        $nombreArchivo = uniqid("usuario_", true) . "." . $extension;

        $rutaFoto = $carpeta . $nombreArchivo;


        // Mover fotografía

        if (
            move_uploaded_file(
                $_FILES["foto"]["tmp_name"],
                $rutaFoto
            )
        ) {

            $foto = $rutaFoto;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>INADI | Registro</title>


    <!-- FAVICON -->

    <link
        rel="icon"
        href="../Elementos Gráficos/Logo Inadi sin Brillo.png"
    >


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="styles.css"
    >

</head>


<body>


<!-- ENCABEZADO-->

<header class="encabezado">

    <img
        src="../Elementos Gráficos/Logo Inadi con Brillo.png"
        alt="Instituto Nacional de Informática"
        class="logo"
    >

    <div class="encabezado-texto">

        <strong>INADI</strong>

        <p>
            Instituto Nacional de Informática
        </p>

    </div>

</header>



<!--CONTENEDOR PRINCIPAL -->

<main class="registro">


    <!--FORMULARIO -->

    <section class="formulario">


        <!-- TÍTULO -->

        <div class="titulo">

            <span class="etiqueta">
                INADI
            </span>

            <h1>
                Crear cuenta
            </h1>

            <p>
                Completá tus datos para registrarte
                en INADI.
            </p>

        </div>



        <!-- FORMULARIO -->

        <form
            action="registro.php"
            method="post"
            enctype="multipart/form-data"
            id="formRegistro"
        >


            <!--TIPO DE USUARIO -->

            <div class="campo">

                <label for="tipo_usuario">
                    Tipo de usuario
                </label>

                <select
                    id="tipo_usuario"
                    name="tipo_usuario"
                    required
                >

                    <option value="">
                        Seleccioná un tipo de usuario
                    </option>

                    <option value="estudiante">
                        Estudiante
                    </option>

                    <option value="docente">
                        Docente
                    </option>

                    <option value="tutor">
                        Tutor
                    </option>

                    <option value="director">
                        Director
                    </option>

                </select>

            </div>



            <!--DATOS DEL TUTOR -->

            <div
                class="datos-tutor"
                id="datosTutor"
            >

                <div class="campo">

                    <label for="rol_tutor">
                        Rol del tutor
                    </label>

                    <select
                        id="rol_tutor"
                        name="rol_tutor"
                    >

                        <option value="">
                            Seleccioná el vínculo
                        </option>

                        <option value="madre">
                            Madre
                        </option>

                        <option value="padre">
                            Padre
                        </option>

                        <option value="abuelo">
                            Abuelo/a
                        </option>

                        <option value="hermano">
                            Hermano/a
                        </option>

                        <option value="responsable">
                            Responsable legal
                        </option>

                        <option value="otro">
                            Otro
                        </option>

                    </select>

                </div>


                <div class="campo">

                    <label for="cedula_representado">
                        Cédula del estudiante representado
                    </label>

                    <input
                        type="text"
                        id="cedula_representado"
                        name="cedula_representado"
                        placeholder="Ej.: 4.123.456-7"
                        maxlength="11"
                    >

                    <small>
                        Ingresá la cédula del estudiante al que representás.
                    </small>

                </div>

            </div>



            <!--NOMBRE DE USUARIO -->

            <div class="campo">

                <label for="nombre_usuario">
                    Nombre de usuario
                </label>

                <input
                    type="text"
                    id="nombre_usuario"
                    name="nombre_usuario"
                    placeholder="Ingresá tu nombre de usuario"
                    required
                >

            </div>



            <!--NOMBRE Y APELLIDO -->

            <div class="fila">

                <div class="campo">

                    <label for="nombre">
                        Nombre
                    </label>

                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        placeholder="Ingresá tu nombre"
                        autocomplete="given-name"
                        required
                    >

                </div>


                <div class="campo">

                    <label for="apellido">
                        Apellido
                    </label>

                    <input
                        type="text"
                        id="apellido"
                        name="apellido"
                        placeholder="Ingresá tu apellido"
                        autocomplete="family-name"
                        required
                    >

                </div>

            </div>



            <!--CÉDULA -->

            <div class="campo">

                <label for="cedula">
                    Cédula de identidad
                </label>

                <div class="campo-cedula">

                    <input
                        type="text"
                        id="cedula"
                        name="cedula"
                        placeholder="Ej.: 4.123.456-7"
                        maxlength="11"
                        inputmode="numeric"
                        autocomplete="off"
                        required
                    >

                    <span
                        class="estado-cedula"
                        id="estadoCedula"
                    ></span>

                </div>

                <small>
                    Escribí los números de tu cédula. El formato se completa automáticamente.
                </small>

            </div>



            <!--FECHA Y CORREO -->

            <div class="fila">

                <div class="campo">

                    <label for="fecha">
                        Fecha de nacimiento
                    </label>

                    <input
                        type="date"
                        id="fecha"
                        name="fecha"
                        required
                    >

                </div>


                <div class="campo">

                    <label for="correo">
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        placeholder="correo@ejemplo.com"
                        autocomplete="email"
                        required
                    >

                </div>

            </div>



            <!--CONTRASEÑA -->

            <div class="campo">

                <label for="password">
                    Contraseña
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Ingresá una contraseña"
                    autocomplete="new-password"
                    required
                >

            </div>



            <!--FOTOGRAFÍA -->

            <div class="campo">

                <label for="foto">
                    Fotografía
                </label>

                <input
                    type="file"
                    id="foto"
                    name="foto"
                    accept="image/jpeg,image/png,image/webp"
                >

                <small>
                    Seleccioná una fotografía para tu credencial.
                </small>

            </div>



            <!--BOTÓN -->

            <button
                type="submit"
                class="boton"
            >

                REGISTRARME

                <span>
                    →
                </span>

            </button>

        </form>

    </section>



    <!--CREDENCIAL -->

    <section class="documento-contenedor">

        <div class="documento">


            <!-- ENCABEZADO -->

            <div class="documento-superior">

                <div>

                    <span class="pais">
                        REPÚBLICA ORIENTAL DEL URUGUAY
                    </span>

                    <h2>
                        INADI
                    </h2>

                    <p>
                        INSTITUTO NACIONAL DE INFORMÁTICA
                    </p>

                </div>


                <div class="chip">
                    INADI
                </div>

            </div>



            <div class="linea"></div>



            <!-- CUERPO -->

            <div class="documento-cuerpo">


                <!-- FOTO -->

                <div class="foto" id="vistaFoto">

                    <?php if ($foto !== ""): ?>

                        <img
                            src="<?php echo htmlspecialchars($foto); ?>"
                            alt="Fotografía del usuario"
                        >

                    <?php else: ?>

                        <div class="icono-foto">
                            👤
                        </div>

                        <span>
                            FOTOGRAFÍA
                        </span>

                    <?php endif; ?>

                </div>



                <!-- DATOS -->

                <div class="datos">


                    <div class="dato">

                        <span>
                            Nombre
                        </span>

                        <strong id="vistaNombre">
                            <?php echo htmlspecialchars($nombre); ?>
                        </strong>

                    </div>



                    <div class="dato">

                        <span>
                            Apellido
                        </span>

                        <strong id="vistaApellido">
                            <?php echo htmlspecialchars($apellido); ?>
                        </strong>

                    </div>



                    <div class="dato">

                        <span>
                            C.I.
                        </span>

                        <strong id="vistaCedula">
                            <?php echo htmlspecialchars($cedula); ?>
                        </strong>

                    </div>



                    <div class="dato">

                        <span>
                            Fecha de nacimiento
                        </span>

                        <strong id="vistaFecha">

                            <?php

                            if ($fecha !== "") {

                                echo date(
                                    "d/m/Y",
                                    strtotime($fecha)
                                );

                            }

                            ?>

                        </strong>

                    </div>



                    <div class="dato">

                        <span>
                            Rol
                        </span>

                        <strong id="vistaRol">
                            <?php echo htmlspecialchars($rol); ?>
                        </strong>

                    </div>

                </div>

            </div>



            <!-- PARTE INFERIOR -->

            <div class="documento-inferior">

                <div class="firma">
                    Firma del usuario
                </div>


                <div class="codigo">

                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>

                </div>

            </div>



            <!-- MARCA DE AGUA -->

            <div class="marca-agua">
                INADI
            </div>

        </div>


        <p class="ayuda">
            La credencial se actualizará automáticamente mientras completás el formulario.
        </p>

    </section>

</main>



<!-- JAVASCRIPT -->

<script src="registro.js"></script>

</body>

</html>

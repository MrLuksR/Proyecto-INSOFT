<?php

// =====================================================
// CONFIGURACIÓN
// =====================================================

$anioActual = date("Y");


// =====================================================
// DATOS DEL FORMULARIO
// =====================================================

$mensaje = "";
$tipoMensaje = "";

$nombre = "";
$documento = "";
$email = "";
$telefono = "";
$fecha = "";
$genero = "";


// =====================================================
// PROCESAR FORMULARIO
// =====================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $documento = trim($_POST["documento"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $fecha = trim($_POST["fecha"] ?? "");
    $genero = trim($_POST["genero"] ?? "");


    // Verificar campos obligatorios

    if (
        $nombre === "" ||
        $documento === "" ||
        $email === "" ||
        $telefono === "" ||
        $fecha === ""
    ) {

        $mensaje = "Por favor, completa todos los campos obligatorios.";
        $tipoMensaje = "error";

    } else {

        $mensaje = "Datos registrados correctamente. Ahora puedes continuar con la selección del curso.";
        $tipoMensaje = "success";
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

    <title>
        INADI | Proceso de Matrícula
    </title>


    <!-- =================================================
         FONT AWESOME
    ================================================== -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- =================================================
         CSS
    ================================================== -->

    <link
        rel="stylesheet"
        href="./matriculasyles.css?v=2"
    >

</head>


<body>


<!-- =====================================================
     CONTENEDOR GENERAL
====================================================== -->

<div class="page">


    <!-- =================================================
         BARRA SUPERIOR
    ================================================== -->

    <header class="topbar">


        <!-- PARTE IZQUIERDA -->

        <div class="topbar-left">


            <!-- LOGO PEQUEÑO -->

            <div class="top-logo">

                <img
                    src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                    alt="Logo INADI"
                >

                <span>
                    INADI
                </span>

            </div>


            <!-- SEPARADOR -->

            <div class="topbar-separator"></div>


            <!-- NOTICIAS -->

            <div class="news">

                <i class="fa-solid fa-triangle-exclamation"></i>

                <span>
                    NOTICIAS
                </span>

            </div>

        </div>



        <!-- =================================================
             PARTE DERECHA
        ================================================== -->

        <div class="topbar-right">


            <!-- IDIOMA -->

            <div class="language">

                <i class="fa-solid fa-globe"></i>

                <span>
                    ES
                </span>

            </div>


            <!-- USUARIO -->

            <a
                href="../Bienvenido/bienvenidoindex.html"
                class="user-icon"
                title="Iniciar sesión"
            >

                <i class="fa-regular fa-user"></i>

            </a>

        </div>

    </header>



    <!-- =================================================
         SIDEBAR
    ================================================== -->

    <aside class="sidebar">


        <!-- =================================================
             MENÚ
        ================================================== -->

        <nav class="sidebar-nav">

            <ul class="menu">


                <!-- NOSOTROS -->

                <li>

                    <a href="#">

                        <i class="fa-solid fa-building-columns"></i>

                        <span>
                            Nosotros
                        </span>

                    </a>

                </li>


                <!-- CURSOS -->

                <li>

                    <a href="../Cursos/cursosindex.html">

                        <i class="fa-solid fa-book"></i>

                        <span>
                            Cursos
                        </span>

                    </a>

                </li>


                <!-- MATRÍCULA -->

                <li class="active">

                    <a href="matricula.php">

                        <i class="fa-solid fa-file-signature"></i>

                        <span>
                            Matrícula
                        </span>

                    </a>

                </li>


                <!-- CONTACTO -->

                <li>

                    <a href="#">

                        <i class="fa-solid fa-envelope"></i>

                        <span>
                            Contacto
                        </span>

                    </a>

                </li>


                <!-- GALERÍA -->

                <li>

                    <a href="#">

                        <i class="fa-solid fa-image"></i>

                        <span>
                            Galería
                        </span>

                    </a>

                </li>


                <!-- CONVENIOS -->

                <li>

                    <a href="#">

                        <i class="fa-solid fa-handshake"></i>

                        <span>
                            Convenios
                        </span>

                    </a>

                </li>


                <!-- EMPRESAS -->

                <li>

                    <a href="#">

                        <i class="fa-solid fa-city"></i>

                        <span>
                            Empresas
                        </span>

                    </a>

                </li>

            </ul>

        </nav>



        <!-- =================================================
             PARTE INFERIOR DEL SIDEBAR
        ================================================== -->

        <div class="sidebar-bottom">

            <div>
                © INADI <?php echo $anioActual; ?>
            </div>

            <div>
                Instituto de Informática
            </div>

            <div>
                Salto - Uruguay
            </div>

        </div>

    </aside>



    <!-- =================================================
         CONTENIDO
    ================================================== -->

    <div class="content">


        <main class="main">


            <!-- =================================================
                 CABECERA DE MATRÍCULA
            ================================================== -->

            <section class="matricula-page">


                <div class="matricula-header">


                    <div class="welcome-title">

                        Bienvenido a INADI

                    </div>


                    <h1>

                        Proceso de Matrícula

                    </h1>


                    <p>

                        Inicia tu camino en la excelencia tecnológica.
                        Completa el siguiente asistente para asegurar
                        tu lugar en INADI.

                    </p>

                </div>



                <!-- =================================================
                     ÁREA DE REGISTRO
                ================================================== -->

                <div class="registration">


                    <!-- =================================================
                         PASOS
                    ================================================== -->

                    <div class="steps">


                        <!-- PASO 1 -->

                        <div class="step current">


                            <div class="step-number">

                                <i class="fa-solid fa-user"></i>

                            </div>


                            <div class="step-info">

                                <h3>
                                    Datos<br>
                                    Personales
                                </h3>

                                <p>
                                    Información básica<br>
                                    del estudiante
                                </p>

                            </div>

                        </div>



                        <!-- PASO 2 -->

                        <div class="step">


                            <div class="step-number">
                                2
                            </div>


                            <div class="step-info">

                                <h3>
                                    Selección de<br>
                                    Curso
                                </h3>

                                <p>
                                    Elige tu plan de<br>
                                    estudio
                                </p>

                            </div>

                        </div>



                        <!-- PASO 3 -->

                        <div class="step">


                            <div class="step-number">
                                3
                            </div>


                            <div class="step-info">

                                <h3>
                                    Confirmación
                                </h3>

                                <p>
                                    Revisión y pago de<br>
                                    matrícula
                                </p>

                            </div>

                        </div>

                    </div>



                    <!-- =================================================
                         TARJETA
                    ================================================== -->

                    <section class="form-card">


                        <!-- CABECERA -->

                        <div class="form-header">

                            <h2>
                                Información del Estudiante
                            </h2>

                        </div>



                        <!-- =================================================
                             MENSAJE PHP
                        ================================================== -->

                        <?php if ($mensaje !== ""): ?>

                            <div
                                class="form-message <?php echo $tipoMensaje; ?>"
                            >

                                <?php
                                    echo htmlspecialchars(
                                        $mensaje,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );
                                ?>

                            </div>

                        <?php endif; ?>



                        <!-- =================================================
                             FORMULARIO
                        ================================================== -->

                        <form
                            method="POST"
                            action="matricula.php"
                        >


                            <!-- =================================================
                                 FILA 1
                            ================================================== -->

                            <div class="form-row">


                                <!-- NOMBRE -->

                                <div class="form-group">

                                    <label for="nombre">
                                        Nombre Completo *
                                    </label>

                                    <input
                                        type="text"
                                        id="nombre"
                                        name="nombre"
                                        placeholder="Ej: Juan Pérez"
                                        value="<?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?>"
                                        required
                                    >

                                </div>



                                <!-- DOCUMENTO -->

                                <div class="form-group">

                                    <label for="documento">
                                        Documento de Identidad *
                                    </label>

                                    <input
                                        type="text"
                                        id="documento"
                                        name="documento"
                                        placeholder="Ej: 1.234.567-8"
                                        value="<?php echo htmlspecialchars($documento, ENT_QUOTES, 'UTF-8'); ?>"
                                        required
                                    >

                                </div>

                            </div>



                            <!-- =================================================
                                 FILA 2
                            ================================================== -->

                            <div class="form-row">


                                <!-- EMAIL -->

                                <div class="form-group">

                                    <label for="email">
                                        Correo Electrónico *
                                    </label>

                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        placeholder="juan.perez@email.com"
                                        value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                                        required
                                    >

                                </div>



                                <!-- TELÉFONO -->

                                <div class="form-group">

                                    <label for="telefono">
                                        Teléfono / Celular *
                                    </label>

                                    <input
                                        type="tel"
                                        id="telefono"
                                        name="telefono"
                                        placeholder="09X XXX XXX"
                                        value="<?php echo htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8'); ?>"
                                        required
                                    >

                                </div>

                            </div>



                            <!-- =================================================
                                 FILA 3
                            ================================================== -->

                            <div class="form-row last-row">


                                <!-- FECHA -->

                                <div class="form-group birth">

                                    <label for="fecha">
                                        Fecha de Nacimiento *
                                    </label>

                                    <input
                                        type="date"
                                        id="fecha"
                                        name="fecha"
                                        value="<?php echo htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8'); ?>"
                                        required
                                    >

                                </div>



                                <!-- GÉNERO -->

                                <div class="form-group gender">

                                    <label for="genero">
                                        Género
                                    </label>

                                    <select
                                        id="genero"
                                        name="genero"
                                    >

                                        <option value="">
                                            Seleccionar...
                                        </option>

                                        <option
                                            value="Femenino"
                                            <?php echo ($genero === "Femenino") ? "selected" : ""; ?>
                                        >
                                            Femenino
                                        </option>

                                        <option
                                            value="Masculino"
                                            <?php echo ($genero === "Masculino") ? "selected" : ""; ?>
                                        >
                                            Masculino
                                        </option>

                                        <option
                                            value="Otro"
                                            <?php echo ($genero === "Otro") ? "selected" : ""; ?>
                                        >
                                            Otro
                                        </option>

                                    </select>

                                </div>

                            </div>



                            <!-- =================================================
                                 BOTÓN
                            ================================================== -->

                            <div class="button-container">

                                <button
                                    type="submit"
                                    class="continue-button"
                                >

                                    <span>
                                        Continuar a Cursos
                                    </span>

                                    <i class="fa-solid fa-arrow-right"></i>

                                </button>

                            </div>


                        </form>

                    </section>

                </div>

            </section>

        </main>

    </div>

</div>


</body>

</html>
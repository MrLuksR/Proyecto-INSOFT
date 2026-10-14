<?php
    include('../auth/config.php');

    session_start();

    if (isset($_SESSION['usuario'], $_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'estudiante') {
        $usuario = $_SESSION['usuario'];
        $nombreCompleto = $usuario->getNombre() . " " . $usuario->getApellido();
    }else {
        header('Location: ../Bienvenido/bienvenidoindex.php');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI - Portal de Autogestión Estudiantil</title>

    <link rel="stylesheet" href="portalEstudiante.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600&family=Inter:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">
</head>

<body>

<header class="header">

    <div class="header-container">

        <div class="brand">

            <div class="logo">
                INADI
            </div>

            <div class="brand-text">
                <h1>Instituto Nacional de Informática</h1>
                <p>Portal de Autogestión Estudiantil</p>
            </div>

        </div>

        <nav class="nav">

            <a href="#">Mi Credencial</a>

            <a href="#" class="active">
                Mis Datos
            </a>

            <a href="#">Mis Cursos</a>

            <a href="#">Calificaciones</a>

            <a href="#">Trámites</a>

        </nav>

        <div class="user-profile">

            <div class="user-info">

                <strong>
                    <?= htmlspecialchars($nombreCompleto) ?>
                </strong>

                <span>
                    Estudiante Regular • 2026
                </span>

            </div>

            <div class="avatar">
                <?= substr($usuario->getNombre(), 0, 1) . substr($usuario->getApellido(), 0, 1);?>
                <span class="online"></span>
            </div>

        </div>

    </div>

</header>


<main class="container">

    <!-- BANNER -->

    <section class="welcome">

        <div class="welcome-content">

            <div>

                <div class="status-badges">

                    <span class="account-status">
                        <span class="status-dot"></span>
                        Cuenta Autónoma (Sin tutor requerido)
                    </span>

                    <span class="verified">
                        C.I. Verificada por DNIC
                    </span>

                </div>

                <h2>
                    Perfil de Estudiante:
                    <?= htmlspecialchars($nombreCompleto) ?>
                </h2>

                <p>
                    Gestioná tu legajo digital institucional.
                    La actualización de datos modificará en tiempo real
                    tu credencial digital homologada para acceso a aulas
                    y laboratorios.
                </p>

            </div>

            <div class="welcome-actions">

                <span class="campus">
                    Instituto Nacional de Informática - Salto
                </span>

                <button type="submit"
                        form="student-profile-form"
                        class="btn-white">

                    Guardar Cambios

                </button>

            </div>

        </div>

    </section>


    <div class="layout">

        <!-- FORMULARIO -->

        <section class="form-card">

            <div class="section-title">

                <div>

                    <h3>
                        Actualización de Datos Personales y de Cuenta
                    </h3>

                    <p>
                        Modificá tu información registrada.
                        Los cambios en tu nombre o documento actualizarán
                        automáticamente tu credencial oficial.
                    </p>

                </div>

            </div>


            <form id="student-profile-form"
                  class="student-form"
                  method="POST"
                  action="">

                <!-- NOMBRE Y APELLIDO -->

                <div class="form-grid">

                    <div class="form-group">

                        <label for="nombre">
                            Nombres
                        </label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            value="<?= htmlspecialchars($usuario->getNombre()) ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="apellido">
                            Apellidos
                        </label>

                        <input
                            type="text"
                            id="apellido"
                            name="apellido"
                            value="<?= htmlspecialchars($usuario->getApellido()) ?>"
                            required
                        >

                    </div>

                </div>


                <!-- CEDULA Y FECHA -->

                <div class="form-grid">

                    <div class="form-group">

                        <label for="cedula">
                            Cédula de Identidad
                        </label>

                        <div class="input-verified">

                            <input
                                type="text"
                                id="cedula"
                                name="cedula"
                                value="<?= htmlspecialchars($usuario->getCedula($key)) ?>"
                                required
                            >

                            <span>
                                Validada
                            </span>

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="fecha_nacimiento">
                            Fecha de Nacimiento
                        </label>

                        <input
                            type="date"
                            id="fecha_nacimiento"
                            name="fecha_nacimiento"
                            value="<?= htmlspecialchars($usuario->getFechaNacimiento($key)) ?>"
                            required
                        >

                    </div>

                </div>


                <!-- USUARIO Y EMAIL -->

                <div class="form-grid">

                    <div class="form-group">

                        <label for="username">
                            Nombre de Usuario (Campus)
                        </label>

                        <div class="username-input">

                            <span>@</span>

                            <input
                                type="text"
                                id="username"
                                name="username"
                                value="<?= htmlspecialchars($usuario->getNombreUsuario()) ?>"
                            >

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="email">
                            Correo Electrónico Institucional
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($usuario->getCorreo($key)) ?>"
                            required
                        >

                    </div>

                </div>


                <!-- TELEFONO -->

                <div class="form-group">

                    <label for="telefono">
                        Teléfono Celular de Contacto
                    </label>

                    <div class="phone-input">

                        <span>
                            UY (+598)
                        </span>

                        <input
                            type="tel"
                            id="telefono"
                            name="telefono"
                            value="<?= htmlspecialchars($usuario->getTelefono($key)) ?>"
                        >

                    </div>

                </div>


                <!-- FOTO -->

                <div class="photo-box">

                    <label>
                        Fotografía Oficial para la Credencial
                    </label>

                    <div class="photo-content">

                        <?php
                            setFotoUser($usuario, 1);
                        ?>
                        <div>

                            <div class="photo-buttons">

                                <button type="button">
                                    Cambiar imagen
                                </button>

                                <button type="button"
                                        class="delete">
                                    Eliminar
                                </button>

                            </div>

                            <p>
                                Formato JPG o PNG, fondo claro y rostro
                                centrado. Máx. 4 MB.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- BOTONES -->

                <div class="form-actions">

                    <button type="reset"
                            class="btn-cancel">

                        Cancelar / Restaurar valores

                    </button>

                    <button type="submit"
                            class="btn-primary">

                        Guardar y Actualizar Credencial

                    </button>

                </div>

            </form>

        </section>


        <!-- CREDENCIAL -->

        <section class="credential-section">

            <div class="credential-heading">

                <div>

                    <h3>
                        Credencial Oficial de Estudiante
                    </h3>

                    <p>
                        Documento habilitado para el año académico 2026
                    </p>

                </div>

                <span class="live">
                    <span></span>
                    Vista previa en tiempo real
                </span>

            </div>


            <!-- CREDENCIAL -->

            <div class="credential-card">

                <div class="credential-header">

                    <div>

                        <small>
                            REPÚBLICA ORIENTAL DEL URUGUAY
                        </small>

                        <div class="institution">

                            <strong>INADI</strong>

                            <span>
                                Instituto Nacional de Informática
                            </span>

                        </div>

                    </div>

                    <div class="credential-logo">
                        INADI
                    </div>

                </div>


                <div class="credential-body">

                    <div class="credential-photo-container">

                        <?php
                            setFotoUser($usuario, 2);
                        ?>

                        <div class="signature">
                            <?=$nombreCompleto?>.
                        </div>

                        <small>
                            Firma del estudiante
                        </small>

                    </div>


                    <div class="credential-data">

                        <div>

                            <span>
                                Nombre Completo
                            </span>

                            <strong>
                                <?= htmlspecialchars($nombreCompleto) ?>
                            </strong>

                        </div>


                        <div class="two-columns">

                            <div>

                                <span>
                                    Cédula de Identidad
                                </span>

                                <strong>
                                    <?= htmlspecialchars($usuario->getCedula($key)) ?>
                                </strong>

                            </div>

                            <div>

                                <span>
                                    Fecha Nacimiento
                                </span>

                                <strong>
                                    18 / 04 / 2005
                                </strong>

                            </div>

                        </div>


                        <div>

                            <span>
                                Curso Asignado
                            </span>

                            <strong class="course">
                                [Curso asignado]
                            </strong>

                        </div>


                        <div class="two-columns">

                            <div>

                                <span>
                                    Estado
                                </span>

                                <b class="regular">
                                    <?= htmlspecialchars($usuario->getEstado()) ?>
                                </b>

                            </div>

                            <div>

                                <span>
                                    Vencimiento
                                </span>

                                <strong>
                                    Saldrá de matrícula
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- FOOTER CREDENCIAL -->

                <div class="credential-footer">

                    <p>
                        ID Digital:
                        <strong>
                            <?= htmlspecialchars($usuario->getNombreUsuario()) ?>
                        </strong>
                    </p>

                    <div class="barcode">

                        <span></span>
                        <span></span>
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

                <div class="watermark">
                    INADI
                </div>

            </div>


            <!-- ACCIONES -->

            <div class="credential-actions">

                <button class="btn-download">
                     Descargar PDF / Wallet
                </button>

                <button class="btn-share">
                    Compartir
                </button>

            </div>


            <!-- SEGURIDAD -->

            <div class="security">

                <strong>
                    Seguridad y Privacidad
                </strong>

                <p>
                    Esta cuenta es gestionada exclusivamente por el titular
                    mayor de edad o estudiante emancipado. No requiere
                    autorización de tutor.
                </p>

            </div>

        </section>

    </div>

</main>


<footer>

    © 2026 INADI - Instituto Nacional de Informática
    • República Oriental del Uruguay
    • Todos los derechos reservados.

</footer>

</body>
</html>
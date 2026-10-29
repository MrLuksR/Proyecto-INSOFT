<?php
// Servicios/robotica/robotica.php

// Incluir archivos desde la raíz del proyecto.
include_once __DIR__ . '/../../auth/config.php';
include_once __DIR__ . '/../../database/Clases/Estudiante.php';

// Iniciar sesión solamente si todavía no está iniciada.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$anioActual = date("Y");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI | Robótica y automatización</title>

    <!-- Iconos -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- CSS compartido de INADI -->
    <link rel="stylesheet"
          href="../../Inicio/CSS/styles.css">

    <!-- CSS específico de Robótica -->
    <link rel="stylesheet"
          href="robotica.css?v=2">
    <link rel="stylesheet" href="/Proyecto-INSOFT/Estructura/topbar-controls.css?v=1">
</head>

<body>

<div class="page">

    <!-- BARRA SUPERIOR -->
    <header class="topbar">

        <div class="topbar-left">

            <div class="top-logo">
                <img
                    src="../../Elementos%20Gr%C3%A1ficos/Logo%20Inadi%20con%20Brillo.png"
                    alt="Logo INADI">

                <span>INADI</span>
            </div>

            <div class="news">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>NOTICIAS</span>
            </div>

        </div>

        <!-- BUSCADOR -->
        <div class="top-search">

            <form action="../../Cursos/cursosindex.php" method="GET">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="search"
                    name="buscar"
                    placeholder="Buscar información..."
                    aria-label="Buscar información">

                <button type="submit">Buscar</button>

            </form>

        </div>

        <!-- ACCESOS -->
        <div class="topbar-right">

            <?php
            if (function_exists('setPortalBtn')) {
                setPortalBtn();
            }
            ?>

            <?php inadiLanguageControl(); ?>

            <?php
            if (function_exists('setLogin')) {
                setLogin();
            }
            ?>

        </div>

    </header>


    <!-- MENÚ LATERAL -->
    <aside class="sidebar">

        <button
            class="sidebar-toggle"
            id="sidebarToggle"
            type="button"
            aria-label="Contraer menú"
            aria-expanded="true"
            title="Contraer menú">

            <i class="fa-solid fa-angles-left"
               aria-hidden="true"></i>

        </button>

        <ul class="menu">

            <li>
                <a href="../../Inicio/inicioindex.php">
                    <i class="fa-solid fa-house"></i>
                    <span>Inicio</span>
                </a>
            </li>

            <li>
                <a href="../../Nosotros/nosotros.php">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>Nosotros</span>
                </a>
            </li>

            <li>
                <a href="../../Cursos/cursosindex.php">
                    <i class="fa-solid fa-book"></i>
                    <span>Cursos</span>
                </a>
            </li>

            <!-- OPCIONES DEL ESTUDIANTE -->
            <?php if (isset($_SESSION['nombre_usuario'])): ?>

                <li class="student-menu-heading">

                    <span class="student-avatar">
                        <i class="fa-solid fa-user-graduate"></i>
                    </span>

                    <span class="student-welcome">

                        <strong>
                            <?php
                            $nombreCompleto = trim(
                                ($_SESSION['nombre'] ?? '') . ' ' .
                                ($_SESSION['apellido'] ?? '')
                            );

                            echo htmlspecialchars(
                                $nombreCompleto !== ''
                                    ? $nombreCompleto
                                    : $_SESSION['nombre_usuario'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </strong>

                        <small>Estudiante registrado</small>

                    </span>

                </li>

                <li>
                    <a href="../../Estudiante/portalEstudiante.php">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Mi portal</span>
                    </a>
                </li>

                <li>
                    <a href="../../Matricula/matricula.php">
                        <i class="fa-solid fa-file-signature"></i>
                        <span>Mis matrículas</span>
                    </a>
                </li>

            <?php endif; ?>

            <?php
            if (
                isset($_SESSION['nombre_usuario']) &&
                function_exists('setMatriculaBtn')
            ) {
                setMatriculaBtn();
            }
            ?>

            <li>
                <a href="../../Contacto/contacto.php">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Contacto</span>
                </a>
            </li>

            <li>
                <a href="../../Galeria/galeria.php">
                    <i class="fa-solid fa-image"></i>
                    <span>Galería</span>
                </a>
            </li>

            <li>
                <a href="../../Convenios/convenios.php">
                    <i class="fa-solid fa-handshake"></i>
                    <span>Convenios</span>
                </a>
            </li>

            <li>
                <a href="../../Empresas/empresas.php">
                    <i class="fa-solid fa-city"></i>
                    <span>Empresas</span>
                </a>
            </li>

        </ul>

        <!-- INFORMACIÓN DEL INSTITUTO -->
        <div class="sidebar-bottom">

            © INADI <?php echo $anioActual; ?><br>
            Instituto de Informática<br>
            Salto - Uruguay

        </div>

    </aside>


    <!-- CONTENIDO PRINCIPAL -->
    <div class="content">

        <main class="main">

            <!-- ENCABEZADO -->
            <section class="encabezado-servicio">

                <span class="etiqueta">
                    Tecnología aplicada
                </span>

                <h1>Robótica y automatización</h1>

                <p>
                    Aprende sobre programación, sensores y sistemas
                    automatizados mediante actividades prácticas que conectan
                    el pensamiento lógico con la construcción de soluciones.
                </p>

            </section>


            <!-- POSIBILIDADES -->
            <section class="bloque-servicio">

                <h2>Áreas de trabajo</h2>

                <div class="tarjetas">

                    <article class="tarjeta">

                        <span class="icono">🤖</span>

                        <h3>Robótica educativa</h3>

                        <p>
                            Construcción y programación de proyectos
                            para aprender haciendo.
                        </p>

                    </article>

                    <article class="tarjeta">

                        <span class="icono">⚙️</span>

                        <h3>Automatización</h3>

                        <p>
                            Diseño de sistemas que ejecutan tareas
                            a partir de instrucciones y condiciones.
                        </p>

                    </article>

                    <article class="tarjeta">

                        <span class="icono">📡</span>

                        <h3>Sensores y control</h3>

                        <p>
                            Exploración de sensores, actuadores y respuestas
                            ante diferentes estímulos.
                        </p>

                    </article>

                </div>

            </section>


            <!-- COMPETENCIAS -->
            <section class="bloque-servicio">

                <h2>Competencias que puedes desarrollar</h2>

                <ul class="lista-beneficios">

                    <li>Pensamiento computacional.</li>

                    <li>Resolución de problemas.</li>

                    <li>Programación y lógica.</li>

                    <li>
                        Diseño, experimentación y trabajo colaborativo.
                    </li>

                </ul>

                <a
                    class="boton-contacto"
                    href="https://wa.me/59847331609"
                    target="_blank"
                    rel="noopener noreferrer">

                    Consultar por robótica

                </a>

            </section>

        </main>

    </div>


    <!-- PIE DE PÁGINA -->
    <footer>

        <div class="footer-content">

            <div class="footer-brand">

                <div class="footer-brand-title">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>INADI</span>
                </div>

                <p class="footer-description">
                    Instituto de Informática de Salto.
                </p>

                <p class="footer-description">
                    Desde 1992.
                </p>

            </div>


            <div class="footer-column">

                <h3>CONTACTO</h3>

                <p>
                    <i class="fa-solid fa-location-dot"></i>
                    Artigas 827, Salto, Uruguay
                </p>

                <p>
                    <i class="fa-solid fa-phone"></i>
                    473 31609
                </p>

            </div>


            <div class="footer-column">

                <h3>HORARIO</h3>

                <p>Lunes - Viernes: 08:00 - 20:00</p>

                <p>Sábados: 08:00 - 13:00</p>

            </div>


            <div class="footer-column">

                <h3>REDES SOCIALES</h3>

                <p>
                    <i class="fa-brands fa-facebook"></i>
                    Facebook
                </p>

                <p>
                    <i class="fa-brands fa-instagram"></i>
                    Instagram
                </p>

            </div>

        </div>

    </footer>


    <!-- BOTÓN WHATSAPP -->
    <a
        class="whatsapp"
        href="https://wa.me/59847331609"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Contactar por WhatsApp">

        <i class="fa-brands fa-whatsapp"></i>

    </a>

</div>


<!-- JAVASCRIPT ESPECÍFICO DE ROBÓTICA -->
<script src="robotica.js?v=1"></script>

</body>
</html>

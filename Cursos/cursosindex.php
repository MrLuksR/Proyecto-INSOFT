<?php
include("../auth/config.php");
include("../database/Clases/Estudiante.php");

require_once("../database/consultas/cursos/getCursos.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$anioActual = date("Y");

$cursosActivos = obtenerCursos($pdo);
$totalCursos = is_array($cursosActivos) ? count($cursosActivos) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INADI | Cursos</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- CSS compartido con Nosotros -->
    <link rel="stylesheet" href="../Inicio/CSS/stylesinicio.css?v=8">

    <!-- CSS exclusivo de Cursos -->
    <link rel="stylesheet" href="cursostyles.css?v=5">
</head>

<body>
<div class="page">

    <!-- BARRA SUPERIOR: IGUAL A NOSOTROS -->
    <header class="topbar">
        <div class="topbar-left">
            <a href="../Inicio/inicioindex.php" class="top-logo">
                <img src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                     alt="Logo INADI">
                <span>INADI</span>
            </a>

            <div class="news">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>NOTICIAS</span>
            </div>
        </div>

        <!-- BUSCADOR -->
        <div class="top-search">
            <form action="cursosindex.php" method="GET">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    type="search"
                    name="buscar"
                    placeholder="Buscar información..."
                    aria-label="Buscar información">
                <button type="submit">Buscar</button>
            </form>
        </div>

        <!-- ACCESOS DEL LADO DERECHO -->
        <div class="topbar-right">
            <a class="portal-button portal-estudiante"
               href="../Estudiante/portalEstudiante.php">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Portal Estudiantes</span>
            </a>

            <span class="language">
                <i class="fa-solid fa-globe"></i>
                ES
            </span>

            <?php setLogin(); ?>
        </div>
    </header>

    <!-- BARRA LATERAL: IGUAL A NOSOTROS -->
    <aside class="sidebar" aria-label="Menú principal">

        <button
            class="sidebar-toggle"
            id="sidebarToggle"
            type="button"
            aria-label="Contraer menú"
            aria-expanded="true"
            title="Contraer menú">
            <i class="fa-solid fa-angles-left" aria-hidden="true"></i>
        </button>

        <ul class="menu">
            <li>
                <a href="../Inicio/inicioindex.php">
                    <i class="fa-solid fa-house"></i>
                    <span>Inicio</span>
                </a>
            </li>

            <li>
                <a href="../Nosotros/nosotros.php">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>Nosotros</span>
                </a>
            </li>

            <li class="active">
                <a href="cursosindex.php" aria-current="page">
                    <i class="fa-solid fa-book"></i>
                    <span>Cursos</span>
                </a>
            </li>

            <!-- MENÚ PERSONAL DEL ESTUDIANTE -->
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
                    <a href="../Estudiante/portalEstudiante.php">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Mi portal</span>
                    </a>
                </li>

                <li>
                    <a href="../Matricula/matricula.php">
                        <i class="fa-solid fa-file-signature"></i>
                        <span>Mis matrículas</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php
            if (isset($_SESSION['nombre_usuario'])) {
                setMatriculaBtn();
            }
            ?>

            <li>
                <a href="../Contacto/contacto.php">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Contacto</span>
                </a>
            </li>

            <li>
                <a href="../Galeria/galeria.php">
                    <i class="fa-solid fa-image"></i>
                    <span>Galería</span>
                </a>
            </li>

            <li>
                <a href="../Convenios/convenios.php">
                    <i class="fa-solid fa-handshake"></i>
                    <span>Convenios</span>
                </a>
            </li>

            <li>
                <a href="../Empresas/empresas.php">
                    <i class="fa-solid fa-city"></i>
                    <span>Empresas</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-bottom">
            © INADI <?php echo htmlspecialchars($anioActual, ENT_QUOTES, 'UTF-8'); ?><br>
            Instituto de Informática<br>
            Salto - Uruguay
        </div>
    </aside>

    <!-- CONTENIDO DE CURSOS -->
    <div class="content">
        <main class="main">

            <section class="courses-header">
                <span class="courses-label">INSTITUTO DE INFORMÁTICA</span>

                <h1>Nuestros <strong>Cursos</strong></h1>

                <p>
                    Descubrí nuestra propuesta educativa y elegí
                    la formación que mejor se adapte a tus objetivos.
                </p>
            </section>

            <!-- ESTADÍSTICAS -->
            <section class="statistics">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div class="stat-info">
                        <strong><?php setTray(); ?></strong>
                        <span>Años de<br>experiencia</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <div class="stat-info">
                        <strong><?php echo (int) $totalCursos; ?></strong>
                        <span>Cursos<br>activos</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div class="stat-info">
                        <strong>10K</strong>
                        <span>Certificados<br>emitidos</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <div class="stat-info">
                        <strong>50</strong>
                        <span>Empresas<br>capacitadas</span>
                    </div>
                </div>
            </section>

            <!-- CATÁLOGO -->
            <section class="courses-section" id="cursos">
                <div class="section-title">
                    <div>
                        <span class="section-label">FORMACIÓN</span>
                        <h2>Cursos destacados</h2>
                        <p>Explorá las opciones de formación disponibles en INADI.</p>
                    </div>
                </div>

                <div class="courses-grid">
                    <?php mostrarCursos($pdo); ?>
                </div>
            </section>

        </main>
    </div>

    <!-- FOOTER ORIGINAL: SE CONSERVA SIN CAMBIOS -->
    <footer>
        <div class="footer-content">

            <div class="footer-column">
                <div class="footer-brand-title">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>INADI</span>
                </div>

                <p class="footer-description">
                    Instituto de Informática comprometido con
                    la formación tecnológica y el desarrollo educativo.
                </p>

                <p class="footer-description">
                    © INADI
                    <?php echo htmlspecialchars($anioActual, ENT_QUOTES, 'UTF-8'); ?>.
                    Todos los derechos reservados.
                </p>
            </div>

            <div class="footer-column">
                <h3>CONTACTO</h3>
                <p>
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Artigas 827, Salto, Uruguay</span>
                </p>
                <p>
                    <i class="fa-solid fa-building"></i>
                    <span>Instituto de Informática</span>
                </p>
            </div>

            <div class="footer-column">
                <h3>FORMACIÓN</h3>
                <p>
                    <i class="fa-solid fa-book"></i>
                    <span>Cursos de informática</span>
                </p>
                <p>
                    <i class="fa-solid fa-laptop"></i>
                    <span>Formación tecnológica</span>
                </p>
            </div>

            <div class="footer-column">
                <h3>NAVEGACIÓN</h3>
                <p>
                    <i class="fa-solid fa-house"></i>
                    <a href="../Inicio/inicioindex.php">Inicio</a>
                </p>
                <p>
                    <i class="fa-solid fa-building-columns"></i>
                    <a href="../Nosotros/nosotros.php">Nosotros</a>
                </p>
                <p>
                    <i class="fa-solid fa-envelope"></i>
                    <a href="../Contacto/contacto.php">Contacto</a>
                </p>
            </div>

        </div>
    </footer>

    <!-- BOTÓN DE WHATSAPP -->
    <a href="https://wa.me/"
       class="whatsapp"
       title="Contactar por WhatsApp"
       aria-label="Contactar por WhatsApp"
       target="_blank"
       rel="noopener noreferrer">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

</div>

<!-- FUNCIONAMIENTO DEL MENÚ LATERAL -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector(".sidebar");
    const page = document.querySelector(".page");
    const toggle = document.getElementById("sidebarToggle");

    if (!sidebar || !page || !toggle) {
        return;
    }

    toggle.addEventListener("click", function () {
        const contraida = sidebar.classList.toggle("sidebar-collapsed");

        page.classList.toggle("sidebar-collapsed", contraida);

        toggle.setAttribute("aria-expanded", String(!contraida));
        toggle.setAttribute(
            "aria-label",
            contraida ? "Mostrar menú" : "Contraer menú"
        );
        toggle.title = contraida ? "Mostrar menú" : "Contraer menú";

        const icono = toggle.querySelector("i");

        if (icono) {
            icono.classList.toggle("fa-angles-left", !contraida);
            icono.classList.toggle("fa-angles-right", contraida);
        }
    });
});
</script>

</body>
</html>

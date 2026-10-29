<?php
include("../database/Clases/Estudiante.php");
include("../auth/config.php");

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$anioActual = date("Y");

$empresas = [
    [
        "nombre" => "TATA",
        "logo" => "../imagenes/tata.png",
        "icono" => "fa-store",
        "descripcion" => "Personal de TATA supermercados, sucursal centro."
    ],
    [
        "nombre" => "INIA",
        "logo" => "../imagenes/inia.jpg",
        "icono" => "fa-seedling",
        "descripcion" => "Instituto Nacional de Investigación Agropecuaria."
    ],
    [
        "nombre" => "BHU",
        "logo" => "../imagenes/bhu.jpg",
        "icono" => "fa-building-columns",
        "descripcion" => "Banco Hipotecario del Uruguay, Salto."
    ],
    [
        "nombre" => "CTM Salto Grande",
        "logo" => "../imagenes/ctm.jpg",
        "icono" => "fa-bolt",
        "descripcion" => "Comisión Técnico Mixta de Salto Grande."
    ],
    [
        "nombre" => "Centro Comercial e Industrial de Salto",
        "logo" => "../imagenes/CENTRO-COMERCIAL-DE-SALTO.jpg",
        "icono" => "fa-shop",
        "descripcion" => "Institución vinculada al sector comercial e industrial de Salto."
    ],
    [
        "nombre" => "Junta Departamental de Salto",
        "logo" => "../imagenes/juntadesalto.png",
        "icono" => "fa-landmark",
        "descripcion" => "Organismo departamental de Salto."
    ],
    [
        "nombre" => "MTOP",
        "logo" => "../imagenes/mtop.png",
        "icono" => "fa-road",
        "descripcion" => "Ministerio de Transporte y Obras Públicas."
    ],
    [
        "nombre" => "PRODENOR",
        "logo" => "",
        "icono" => "fa-people-group",
        "descripcion" => "Proyecto de desarrollo regional mencionado en la trayectoria institucional."
    ]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI | Empresas e instituciones</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="../Inicio/CSS/stylesinicio.css">
    <link rel="stylesheet" href="empresas.css?v=1">
    <link rel="stylesheet" href="../Estructura/topbar-controls.css">
    <link rel="stylesheet" href="../Estructura/noticias-dropdown.css?v=4">
</head>

<body>
<div class="page">

    <!-- BARRA SUPERIOR -->
    <header class="topbar">

        <div class="topbar-left">
            <div class="top-logo">
                <img src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                     alt="Logo INADI">
                <span>INADI</span>
            </div>

                        <!-- MENÚ DESPLEGABLE DE NOTICIAS Y NOVEDADES -->
            <details class="news news-dropdown">
                <summary aria-label="Abrir menú de noticias y novedades">
                    <i class="fa-solid fa-bell" aria-hidden="true"></i>
                    <span>NOTICIAS Y NOVEDADES</span>
                    <i class="fa-solid fa-chevron-down news-chevron" aria-hidden="true"></i>
                </summary>
                <div class="news-menu">
                    <div class="news-menu-title">
                        <strong>Noticias y novedades</strong>
                        <span>Información institucional y novedades de INADI.</span>
                    </div>
                    <a href="../Noticias/avisos-importantes.php"><i class="fa-solid fa-bullhorn" aria-hidden="true"></i><span><strong>Avisos importantes</strong><small>Cambios de horarios, comunicados institucionales y avisos generales.</small></span></a>
                    <a href="../Noticias/proximos-cursos.php"><i class="fa-solid fa-book-open" aria-hidden="true"></i><span><strong>Próximos cursos e inscripciones</strong><small>Nuevas propuestas de formación, períodos de inscripción y fechas relevantes.</small></span></a>
                    <a href="../Noticias/actividades-eventos.php"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i><span><strong>Actividades y eventos</strong><small>Talleres, jornadas, proyectos educativos y actividades del instituto.</small></span></a>
                    <a href="../Noticias/novedades-tecnologicas.php"><i class="fa-solid fa-microchip" aria-hidden="true"></i><span><strong>Novedades tecnológicas</strong><small>Informática, programación, inteligencia artificial y recursos digitales.</small></span></a>
                </div>
            </details>
        </div>

        <div class="top-search">
            <form action="../Cursos/cursosindex.php" method="GET">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search"
                       name="buscar"
                       placeholder="Buscar información..."
                       aria-label="Buscar información">
                <button type="submit">Buscar</button>
            </form>
        </div>

        <div class="topbar-right">

            <?php inadiLanguageControl(); ?>

            <?php
            if (function_exists("setLogin")) {
                setLogin();
            }
            ?>
        </div>

    </header>

    <!-- MENÚ LATERAL -->
    <aside class="sidebar">

        <button class="sidebar-toggle"
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

            <li>
                <a href="../Cursos/cursosindex.php">
                    <i class="fa-solid fa-book"></i>
                    <span>Cursos</span>
                </a>
            </li>

            <?php if (isset($_SESSION["nombre_usuario"])): ?>

                <li class="student-menu-heading">
                    <span class="student-avatar">
                        <i class="fa-solid fa-user-graduate"></i>
                    </span>

                    <span class="student-welcome">
                        <strong>
                            <?php
                            $nombreCompleto = trim(
                                ($_SESSION["nombre"] ?? "") . " " .
                                ($_SESSION["apellido"] ?? "")
                            );

                            echo htmlspecialchars(
                                $nombreCompleto !== ""
                                    ? $nombreCompleto
                                    : $_SESSION["nombre_usuario"],
                                ENT_QUOTES,
                                "UTF-8"
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

                <?php
                if (function_exists("setMatriculaBtn")) {
                    setMatriculaBtn();
                }
                ?>

            <?php endif; ?>

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

            <li class="active">
                <a href="empresas.php" aria-current="page">
                    <i class="fa-solid fa-city"></i>
                    <span>Empresas</span>
                </a>
            </li>

        </ul>

        <div class="sidebar-bottom">
            © INADI <?php echo $anioActual; ?><br>
            Instituto de Informática<br>
            Salto - Uruguay
        </div>

    </aside>

    <!-- CONTENIDO -->
    <div class="content">
        <main class="main empresas-main">

            <section class="about-header">
                <span class="about-label">VÍNCULOS INSTITUCIONALES</span>

                <h1>Empresas e <strong>instituciones</strong></h1>

                <p>
                    Conocé las organizaciones vinculadas a la trayectoria
                    de INADI y su trabajo de formación informática.
                </p>
            </section>

            <!-- PRESENTACIÓN -->
            <section class="empresas-intro">

                <div class="empresas-intro-icon">
                    <i class="fa-solid fa-handshake-angle"></i>
                </div>

                <div>
                    <span class="empresas-kicker">TRABAJO EN CONJUNTO</span>

                    <h2>Formación conectada con la comunidad</h2>

                    <p>
                        A lo largo de su trayectoria, INADI ha desarrollado
                        experiencias de capacitación dirigidas a personas,
                        empresas e instituciones de Salto. Estos vínculos
                        forman parte de la historia institucional.
                    </p>
                </div>

            </section>

            <!-- MARQUESINA -->
            <section class="empresas-marquee-section">

                <div class="empresas-section-heading">
                    <div>
                        <span class="empresas-kicker">NUESTROS VÍNCULOS</span>
                        <h2>Empresas e instituciones</h2>
                    </div>

                    <span class="empresas-count">
                        <?php echo count($empresas); ?> organizaciones
                    </span>
                </div>

                <div class="empresas-marquee"
                     aria-label="Marquesina de empresas e instituciones">

                    <div class="empresas-track">

                        <?php for ($vuelta = 0; $vuelta < 2; $vuelta++): ?>

                            <?php foreach ($empresas as $empresa): ?>

                                <article class="empresa-marquee-card"
                                    <?php echo $vuelta === 1
                                        ? 'aria-hidden="true"'
                                        : ''; ?>>

                                    <div class="empresa-logo">

                                        <?php if ($empresa["logo"] !== ""): ?>

                                            <img
                                                src="<?php echo htmlspecialchars(
                                                    $empresa["logo"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ); ?>"
                                                alt="Logo de <?php echo htmlspecialchars(
                                                    $empresa["nombre"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ); ?>"
                                                loading="lazy"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                            <span class="empresa-logo-fallback">
                                                <i class="fa-solid <?php echo htmlspecialchars(
                                                    $empresa["icono"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ); ?>"></i>
                                            </span>

                                        <?php else: ?>

                                            <span class="empresa-logo-fallback">
                                                <i class="fa-solid <?php echo htmlspecialchars(
                                                    $empresa["icono"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ); ?>"></i>
                                            </span>

                                        <?php endif; ?>

                                    </div>

                                    <h3>
                                        <?php echo htmlspecialchars(
                                            $empresa["nombre"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ); ?>
                                    </h3>

                                </article>

                            <?php endforeach; ?>

                        <?php endfor; ?>

                    </div>
                </div>

                <p class="marquee-hint">
                    <i class="fa-solid fa-arrows-left-right"></i>
                    La marquesina se desplaza automáticamente.
                    Colocá el cursor encima para pausarla.
                </p>

            </section>

            <!-- TARJETAS DE EMPRESAS -->
            <section class="empresas-list-section">

                <div class="empresas-section-heading">
                    <div>
                        <span class="empresas-kicker">TRAYECTORIA INSTITUCIONAL</span>
                        <h2>Organizaciones vinculadas</h2>
                    </div>
                </div>

                <div class="empresas-grid">

                    <?php foreach ($empresas as $empresa): ?>

                        <article class="empresa-card">

                            <div class="empresa-card-top">
                                <div class="empresa-card-icon">
                                    <i class="fa-solid <?php echo htmlspecialchars(
                                        $empresa["icono"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ); ?>"></i>
                                </div>

                                <span class="empresa-tag">
                                    Vínculo institucional
                                </span>
                            </div>

                            <h3>
                                <?php echo htmlspecialchars(
                                    $empresa["nombre"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ); ?>
                            </h3>

                            <p>
                                <?php echo htmlspecialchars(
                                    $empresa["descripcion"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ); ?>
                            </p>

                        </article>

                    <?php endforeach; ?>

                </div>

            </section>

            <!-- CIERRE -->
            <section class="about-final empresas-final">

                <div class="final-icon">
                    <i class="fa-solid fa-people-group"></i>
                </div>

                <div>
                    <span class="final-label">INADI · SALTO</span>

                    <h2>¿Tu organización necesita capacitación?</h2>

                    <p>
                        Contactanos para consultar sobre propuestas de
                        formación informática y capacitación para equipos
                        de trabajo.
                    </p>

                    <a class="empresas-contact-link"
                       href="../Contacto/contacto.php">
                        Consultar a INADI
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

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

                <p class="footer-description">Desde 1992.</p>
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
                <p><i class="fa-brands fa-facebook"></i> Facebook</p>
                <p><i class="fa-brands fa-instagram"></i> Instagram</p>
            </div>

        </div>
    </footer>

</div>
</body>
</html>
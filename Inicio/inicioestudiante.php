<?php
// 1. SESIÓN Y DEPENDENCIAS
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . "/../database/Clases/Estudiante.php";
require_once __DIR__ . "/../database/consultas/banner/getBannerInfo.php";
require_once __DIR__ . "/../database/consultas/cursos/getCursos.php";
require_once __DIR__ . "/../auth/config.php";

// 2. DATOS DEL USUARIO
$usuarioLogueado = !empty($_SESSION['nombre_usuario']);

$tipoUsuario = strtolower(
    trim((string) ($_SESSION['tipo_usuario'] ?? ''))
);

$esEstudiante = $usuarioLogueado && $tipoUsuario === 'estudiante';
$nombreEstudiante = trim(
    (string)($_SESSION['nombre'] ?? '') . ' ' .
    (string)($_SESSION['apellido'] ?? '')
);
if ($nombreEstudiante === '') {
    $nombreEstudiante = (string)($_SESSION['nombre_usuario'] ?? 'Estudiante');
}

// 3. CONTAR CURSOS
$cantidadCursos = 0;

if (
    isset($pdo) &&
    $pdo instanceof PDO &&
    function_exists('obtenerCursos')
) {
    try {
        $cursos = obtenerCursos($pdo);
        $cantidadCursos = is_array($cursos) ? count($cursos) : 0;
    } catch (Throwable $e) {
        error_log('Error al obtener cursos: ' . $e->getMessage());
    }
}

// 4. INICIALIZAR BANNERS
if (!isset($num) || !is_numeric($num)) {
    $num = 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#45459c">

    <title>INADI | Instituto de Informática</title>

    <!-- ICONOS -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- CSS DE INICIO -->
    <link
        rel="stylesheet"
        href="/Proyecto-INSOFT/Inicio/CSS/styleestudiante.css?v=11"
    >
    <link rel="stylesheet" href="/Proyecto-INSOFT/Estructura/topbar-controls.css?v=1">
    <link rel="stylesheet" href="/Proyecto-INSOFT/Estructura/noticias-dropdown.css?v=5">
    <link rel="stylesheet" href="/Proyecto-INSOFT/Estructura/paneles-theme.css?v=1">
</head>

<body>
<div class="page">

    <!-- BARRA SUPERIOR -->
    <header class="topbar">

        <div class="topbar-left">
            <div class="top-logo">
                <img
                    src="/Proyecto-INSOFT/Elementos%20Gr%C3%A1ficos/Logo%20Inadi%20con%20Brillo.png"
                    alt="Logo INADI"
                >
                <span>INADI</span>
            </div>

            <!-- MENÚ DESPLEGABLE COMPARTIDO DE NOTICIAS Y NOVEDADES -->
            <details class="news news-dropdown">
                <summary aria-label="Abrir menú de noticias y novedades">
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                    <span>NOTICIAS Y NOVEDADES</span>
                    <i class="fa-solid fa-chevron-down news-chevron" aria-hidden="true"></i>
                </summary>
                <div class="news-menu">
                    <div class="news-menu-title">
                        <strong>Noticias y novedades</strong>
                        <span>Información, actividades y propuestas del instituto</span>
                    </div>
                    <a href="/Proyecto-INSOFT/Noticias/avisos-importantes.php"><i class="fa-solid fa-bullhorn" aria-hidden="true"></i><span><strong>Avisos importantes</strong><small>Cambios de horarios, comunicados institucionales y avisos</small></span></a>
                    <a href="/Proyecto-INSOFT/Noticias/proximos-cursos.php"><i class="fa-solid fa-book-open" aria-hidden="true"></i><span><strong>Próximos cursos e inscripciones</strong><small>Nuevas propuestas de formación y períodos de inscripción</small></span></a>
                    <a href="/Proyecto-INSOFT/Noticias/actividades-eventos.php"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i><span><strong>Actividades y eventos</strong><small>Talleres, jornadas y proyectos educativos</small></span></a>
                    <a href="/Proyecto-INSOFT/Noticias/novedades-tecnologicas.php"><i class="fa-solid fa-microchip" aria-hidden="true"></i><span><strong>Novedades tecnológicas</strong><small>Informática, programación y tecnología</small></span></a>
                </div>
            </details>
        </div>

        <!-- BUSCADOR -->
        <div class="top-search">
            <form
                action="/Proyecto-INSOFT/Cursos/cursosindex.php"
                method="get"
            >
                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="search"
                    name="buscar"
                    placeholder="Buscar información..."
                    aria-label="Buscar información"
                >

                <button type="submit">Buscar</button>
            </form>
        </div>

        <!-- ACCESOS DE USUARIO -->
        <div class="topbar-right">

            <?php inadiLanguageControl(); ?>

            <?php if (!$usuarioLogueado): ?>

                <?php setLogin(); ?>

            <?php else: ?>

                <?php setLogin(); ?>

            <?php endif; ?>

        </div>
    </header>

    <!-- BARRA LATERAL -->
    <aside class="sidebar">

        <button
            class="sidebar-toggle"
            id="sidebarToggle"
            type="button"
            aria-label="Contraer menú"
            aria-expanded="true"
            title="Contraer menú"
        >
            <i class="fa-solid fa-angles-left" aria-hidden="true"></i>
        </button>

        <ul class="menu">

            <li class="active">
                <a href="/Proyecto-INSOFT/Inicio/inicioindex.php">
                    <i class="fa-solid fa-house"></i>
                    <span>Inicio</span>
                </a>
            </li>

            <li>
                <a href="/Proyecto-INSOFT/Nosotros/nosotros.php">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>Nosotros</span>
                </a>
            </li>

            <li>
                <a href="/Proyecto-INSOFT/Cursos/cursosindex.php">
                    <i class="fa-solid fa-book"></i>
                    <span>Cursos</span>
                </a>
            </li>

            <li>
                <a href="/Proyecto-INSOFT/Contacto/contacto.php">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Contacto</span>
                </a>
            </li>

            <li>
                <a href="/Proyecto-INSOFT/Galeria/galeria.php">
                    <i class="fa-solid fa-image"></i>
                    <span>Galería</span>
                </a>
            </li>

            <li>
                <a href="/Proyecto-INSOFT/Convenios/convenios.php">
                    <i class="fa-solid fa-handshake"></i>
                    <span>Convenios</span>
                </a>
            </li>

            <li>
                <a href="/Proyecto-INSOFT/Empresas/empresas.php">
                    <i class="fa-solid fa-city"></i>
                    <span>Empresas</span>
                </a>
            </li>

        </ul>

        <div class="sidebar-bottom">
            © INADI 2026<br>
            Instituto de Informática<br>
            Salto - Uruguay
        </div>

    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="content">
        <main class="main">

            <!-- BIENVENIDA INSTITUCIONAL -->
            <section class="welcome">
                <div>
                    <span class="welcome-label">INSTITUTO DE INFORMÁTICA</span>

                    <h1>
                        <?= $esEstudiante
                            ? '¡Hola, ' . htmlspecialchars($nombreEstudiante, ENT_QUOTES, 'UTF-8') . '!'
                            : 'Bienvenido a <strong>INADI</strong>' ?>
                    </h1>

                    <p>
                        <?= $esEstudiante
                            ? 'Tu espacio de formación en INADI. Ya iniciaste sesión como estudiante.'
                            : 'Formación y capacitación en informática' ?>
                        · Artigas 827, Salto, Uruguay
                        · desde 1992
                    </p>

                    <?php if ($esEstudiante): ?>
                        <div class="datos-sesion-estudiante">
                            <span><strong>Usuario:</strong>
                                <?= htmlspecialchars((string)($_SESSION['nombre_usuario'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span><strong>Perfil:</strong> Estudiante</span>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- CURSOS DESTACADOS -->
            <section class="section">

                <div class="section-title">
                    <div>
                        <span class="section-kicker">FORMACIÓN</span>
                        <h2>Cursos destacados</h2>
                    </div>

                    <a
                        href="/Proyecto-INSOFT/Cursos/cursosindex.php"
                        class="view-all"
                    >
                        Ver todos
                    </a>
                </div>

                <div class="course-carousel">

                    <button
                        class="carousel-arrow arrow-left"
                        onclick="cambiarCurso(-1)"
                        type="button"
                        aria-label="Curso anterior"
                    >
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <?php
                    if (
                        isset($pdo) &&
                        $pdo instanceof PDO &&
                        function_exists('mostrarBanners')
                    ) {
                        mostrarBanners($pdo);
                    }
                    ?>

                    <button
                        class="carousel-arrow arrow-right"
                        onclick="cambiarCurso(1)"
                        type="button"
                        aria-label="Siguiente curso"
                    >
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                    <div class="carousel-dots">
                        <?php
                        if (function_exists('setButtons')) {
                            setButtons((int) $num);
                        }
                        ?>
                    </div>

                </div>
            </section>

            <!-- ESTADÍSTICAS -->
            <section class="stats">

                <div class="stat-card stat-blue">
                    <div class="stat-icon">
                        <i class="fa-solid fa-award"></i>
                    </div>

                    <div class="stat-info">
                        <div class="stat-number">
                            <?php
                            if (function_exists('setTray')) {
                                setTray();
                            } else {
                                echo '34';
                            }
                            ?>
                        </div>
                        <div class="stat-label">Años de experiencia</div>
                    </div>

                    <div class="stat-decoration"></div>
                </div>

                <div class="stat-card stat-orange">
                    <div class="stat-icon">
                        <i class="fa-solid fa-book-open"></i>
                    </div>

                    <div class="stat-info">
                        <div class="stat-number">
                            <?php echo (int) $cantidadCursos; ?>
                        </div>
                        <div class="stat-label">Cursos activos</div>
                    </div>

                    <div class="stat-decoration"></div>
                </div>

                <div class="stat-card stat-yellow">
                    <div class="stat-icon">
                        <i class="fa-solid fa-certificate"></i>
                    </div>

                    <div class="stat-info">
                        <div class="stat-number">10K</div>
                        <div class="stat-label">Certificados emitidos</div>
                    </div>

                    <div class="stat-decoration"></div>
                </div>

                <div class="stat-card stat-purple">
                    <div class="stat-icon">
                        <i class="fa-solid fa-building"></i>
                    </div>

                    <div class="stat-info">
                        <div class="stat-number">50</div>
                        <div class="stat-label">Empresas capacitadas</div>
                    </div>

                    <div class="stat-decoration"></div>
                </div>

            </section>

            <!-- CATÁLOGO DE SERVICIOS -->
            <section class="section services-section" id="servicios">

                <div class="section-title">
                    <div>
                        <span class="section-kicker">SOLUCIONES INADI</span>
                        <h2>Catálogo de servicios</h2>

                        <p class="services-intro">
                            Formación, tecnología e innovación para personas,
                            empresas e instituciones.
                        </p>
                    </div>
                </div>

                <div class="services-grid">

                    <article class="service-card service-indigo">
                        <div class="service-icon">
                            <i class="fa-solid fa-laptop-code"></i>
                        </div>
                        <span class="service-number">01</span>
                        <h3>Cursos</h3>
                        <p>
                            Formación presencial, virtual o híbrida para
                            desarrollar nuevas competencias digitales.
                        </p>
                        <a
                            href="/Proyecto-INSOFT/Cursos/cursosindex.php"
                            class="service-link"
                        >
                            Explorar cursos
                        </a>
                    </article>

                    <article class="service-card service-orange">
                        <div class="service-icon">
                            <i class="fa-solid fa-lightbulb"></i>
                        </div>
                        <span class="service-number">02</span>
                        <h3>Proyectos educativos</h3>
                        <p>
                            Diseño y desarrollo de propuestas educativas
                            innovadoras con apoyo tecnológico.
                        </p>
                        <a
                            href="/Proyecto-INSOFT/Contacto/contacto.php"
                            class="service-link"
                        >
                            Consultar propuesta
                        </a>
                    </article>

                    <article class="service-card service-blue">
                        <div class="service-icon">
                            <i class="fa-solid fa-people-group"></i>
                        </div>
                        <span class="service-number">03</span>
                        <h3>Formación para empresas e instituciones</h3>
                        <p>
                            Capacitaciones a medida para organizaciones
                            y equipos de trabajo.
                        </p>
                        <a
                            href="/Proyecto-INSOFT/Contacto/contacto.php"
                            class="service-link"
                        >
                            Solicitar información
                        </a>
                    </article>

                    <article class="service-card service-purple">
                        <div class="service-icon">
                            <i class="fa-solid fa-cube"></i>
                        </div>
                        <span class="service-number">04</span>
                        <h3>Diseño e impresión 3D</h3>
                        <p>
                            Creación de modelos y prototipos tridimensionales
                            para proyectos y aprendizaje.
                        </p>
                        <a
                            href="/Proyecto-INSOFT/Contacto/contacto.php"
                            class="service-link"
                        >
                            Conocer el servicio
                        </a>
                    </article>

                    <article class="service-card service-green">
                        <div class="service-icon">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <span class="service-number">05</span>
                        <h3>Robótica y automatización</h3>
                        <p>
                            Experiencias prácticas para explorar sensores,
                            programación y sistemas automatizados.
                        </p>
                        <a
                            href="/Proyecto-INSOFT/Contacto/contacto.php"
                            class="service-link"
                        >
                            Explorar soluciones
                        </a>
                    </article>

                    <article class="service-card service-pink">
                        <div class="service-icon">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <span class="service-number">06</span>
                        <h3>Mentorías y acompañamiento</h3>
                        <p>
                            Acompañamiento especializado para orientar ideas,
                            proyectos y desafíos tecnológicos.
                        </p>
                        <a
                            href="/Proyecto-INSOFT/Contacto/contacto.php"
                            class="service-link"
                        >
                            Hablar con INADI
                        </a>
                    </article>

                </div>
            </section>

            <!-- RESEÑAS -->
            <section class="section">

                <div class="section-title">
                    <div>
                        <span class="section-kicker">EXPERIENCIAS</span>
                        <h2>Reseñas de estudiantes</h2>
                    </div>

                    <span class="section-count">4 reseñas</span>
                </div>

                <div class="reviews">

                    <article class="review-card">
                        <div class="review-header">
                            <div class="avatar">VT</div>
                            <div>
                                <div class="review-name">Valentina Torres</div>
                                <div class="review-course">Egresada · Programación Python</div>
                            </div>
                            <div class="stars">★★★★★</div>
                        </div>

                        <p class="review-text">
                            INADI cambió mi vida profesional. Los docentes son
                            excelentes y el ambiente de aprendizaje es único.
                            Hoy trabajo en desarrollo de software gracias a lo
                            que aprendí acá.
                        </p>

                        <div class="review-more">Ver más⌄</div>
                    </article>

                    <article class="review-card">
                        <div class="review-header">
                            <div class="avatar">MA</div>
                            <div>
                                <div class="review-name">Martín Aguirre</div>
                                <div class="review-course">Estudiante · Ciberseguridad</div>
                            </div>
                            <div class="stars">★★★★★</div>
                        </div>

                        <p class="review-text">
                            Los cursos son muy prácticos y actualizados.
                            Las instalaciones son modernas y siempre hay
                            alguien dispuesto a ayudarte.
                        </p>

                        <div class="review-more">Ver más⌄</div>
                    </article>

                    <article class="review-card">
                        <div class="review-header">
                            <div class="avatar">LF</div>
                            <div>
                                <div class="review-name">Lucía Fernández</div>
                                <div class="review-course">Egresada · Marketing Digital</div>
                            </div>
                            <div class="stars">★★★★☆</div>
                        </div>

                        <p class="review-text">
                            La metodología es muy efectiva. Aprendí herramientas
                            que utilizo todos los días. INADI es el mejor lugar
                            para empezar en el mundo digital en Salto.
                        </p>

                        <div class="review-more">Ver más⌄</div>
                    </article>

                    <article class="review-card">
                        <div class="review-header">
                            <div class="avatar">DS</div>
                            <div>
                                <div class="review-name">Diego Sosa</div>
                                <div class="review-course">CCIS · Empresa convenio</div>
                            </div>
                            <div class="stars">★★★★★</div>
                        </div>

                        <p class="review-text">
                            Nuestra empresa tiene convenio con INADI hace años.
                            La calidad de los egresados es excelente. Son una
                            institución de referencia en toda la región.
                        </p>

                        <div class="review-more">Ver más⌄</div>
                    </article>

                </div>
            </section>

            <!-- EMPRESAS CONVENIO -->
            <section class="section">

                <div class="section-title">
                    <div>
                        <span class="section-kicker">VÍNCULOS</span>
                        <h2>Empresas convenio</h2>
                    </div>

                    <a
                        href="/Proyecto-INSOFT/Empresas/empresas.php"
                        class="view-all"
                    >
                        Ver todas
                    </a>
                </div>

                <div
                    class="companies-marquee"
                    aria-label="Logotipos de empresas con convenio"
                >
                    <div class="companies">

                        <div class="company-card">
                            <div class="company-logo">
                                <img
                                    src="/Proyecto-INSOFT/imagenes/tata.png"
                                    alt="TATA"
                                >
                            </div>
                            <div class="company-name">TATA</div>
                        </div>

                        <div class="company-card">
                            <div class="company-logo">
                                <img
                                    src="/Proyecto-INSOFT/imagenes/inia.jpg"
                                    alt="INIA"
                                >
                            </div>
                            <div class="company-name">INIA</div>
                        </div>

                        <div class="company-card">
                            <div class="company-logo">
                                <img
                                    src="/Proyecto-INSOFT/imagenes/bhu.jpg"
                                    alt="BHU"
                                >
                            </div>
                            <div class="company-name">BHU</div>
                        </div>

                        <div class="company-card">
                            <div class="company-logo">
                                <img
                                    src="/Proyecto-INSOFT/imagenes/ctm.jpg"
                                    alt="CTM"
                                >
                            </div>
                            <div class="company-name">CTM</div>
                        </div>

                        <div class="company-card">
                            <div class="company-logo">
                                <img
                                    src="/Proyecto-INSOFT/imagenes/CENTRO-COMERCIAL-DE-SALTO.jpg"
                                    alt="CCIS"
                                >
                            </div>
                            <div class="company-name">CCIS</div>
                        </div>

                        <div class="company-card">
                            <div class="company-logo">
                                <img
                                    src="/Proyecto-INSOFT/imagenes/juntadesalto.png"
                                    alt="Junta Departamental de Salto"
                                >
                            </div>
                            <div class="company-name">JDS</div>
                        </div>

                        <div class="company-card">
                            <div class="company-logo">
                                <img
                                    src="/Proyecto-INSOFT/imagenes/mtop.png"
                                    alt="MTOP"
                                >
                            </div>
                            <div class="company-name">MTOP</div>
                        </div>

                        <div class="company-card">
                            <div class="company-logo">PR</div>
                            <div class="company-name">PRODENOR</div>
                        </div>

                    </div>
                </div>
            </section>

        </main>
    </div>

    <!-- FOOTER -->
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

    <!-- WHATSAPP -->
    <div class="whatsapp">
        <i class="fa-brands fa-whatsapp"></i>
    </div>

</div>

<!-- JAVASCRIPT -->
<script
    src="/Proyecto-INSOFT/Inicio/JS/scriptestudiante.js?v=2"
    defer
></script>

</body>
</html>

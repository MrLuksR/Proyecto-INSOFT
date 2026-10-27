<?php
// Iniciar la sesión antes de incluir dependencias que puedan emitir salida.
require_once __DIR__ . "/../auth/config.php";
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Cargar las dependencias del inicio.
require_once __DIR__ . "/../database/consultas/banner/getBannerInfo.php";
require_once __DIR__ . "/../database/consultas/cursos/getCursos.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI | Instituto de Informática</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link rel="stylesheet" href="CSS/stylesinicio.css">
    <link rel="stylesheet" href="../Estructura/topbar-controls.css">
</head>

<body>
<div class="page">

    <!-- BARRA SUPERIOR -->
    <header class="topbar">

        <div class="topbar-left">
            <div class="top-logo">
                <img
                    src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                    alt="Logo INADI"
                >
                <span>INADI</span>
            </div>

            <div class="news">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>NOTICIAS</span>
            </div>
        </div>

        <!-- BUSCADOR -->
        <div class="top-search">
            <form action="../Cursos/cursosindex.php" method="GET">
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

        <!-- ACCESOS -->
        <div class="topbar-right">

            <?php inadiLanguageControl(); ?>

            <?php setLogin(); ?>

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
            © INADI 2026<br>
            Instituto de Informática<br>
            Salto - Uruguay
        </div>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="content">
        <main class="main">

            <!-- BIENVENIDA -->
            <section class="welcome">
                <div>
                    <span class="welcome-label">INSTITUTO DE INFORMÁTICA</span>
                    <h1>Bienvenido a <strong>INADI</strong></h1>
                    <p>
                        Formación y capacitación en informática
                        · Artigas 827, Salto, Uruguay
                        · desde 1992
                    </p>
                </div>
            </section>

            <!-- CURSOS DESTACADOS -->
            <section class="section">
                <div class="section-title">
                    <div>
                        <span class="section-kicker">FORMACIÓN</span>
                        <h2>Cursos destacados</h2>
                    </div>

                    <a href="../Cursos/cursosindex.php" class="view-all">
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
                    if (isset($pdo) && function_exists('mostrarBanners')) {
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
                        if (isset($num) && function_exists('setButtons')) {
                            setButtons($num);
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
                            <?php
                            if (isset($pdo) && function_exists('obtenerCursos')) {
                                echo count(obtenerCursos($pdo));
                            } else {
                                echo '0';
                            }
                            ?>
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
                        <a href="../Cursos/cursosindex.php" class="service-link">
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
                        <a href="../Contacto/contacto.php" class="service-link">
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
                        <a href="../Contacto/contacto.php" class="service-link">
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
                        <a href="../Contacto/contacto.php" class="service-link">
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
                        <a href="../Contacto/contacto.php" class="service-link">
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
                        <a href="../Contacto/contacto.php" class="service-link">
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
                                <div class="review-course">
                                    Egresada · Programación Python
                                </div>
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
                                <div class="review-course">
                                    Estudiante · Ciberseguridad
                                </div>
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
                                <div class="review-course">
                                    Egresada · Marketing Digital
                                </div>
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
                                <div class="review-course">
                                    CCIS · Empresa convenio
                                </div>
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

                    <a href="../Empresas/empresas.php" class="view-all">
                        Ver todas
                    </a>
                </div>

                <div
                    class="companies-marquee"
                    aria-label="Logotipos de empresas con convenio"
                >
                    <div class="companies">

                        <div class="company-card" tabindex="0" role="button" aria-label="Ampliar logotipo de TATA">
                            <div class="company-logo">
                                <img src="../imagenes/tata.png" alt="TATA">
                            </div>
                            <div class="company-name">TATA</div>
                        </div>

                        <div class="company-card" tabindex="0" role="button" aria-label="Ampliar logotipo de INIA">
                            <div class="company-logo">
                                <img src="../imagenes/inia.jpg" alt="INIA">
                            </div>
                            <div class="company-name">INIA</div>
                        </div>

                        <div class="company-card" tabindex="0" role="button" aria-label="Ampliar logotipo de BHU">
                            <div class="company-logo">
                                <img src="../imagenes/bhu.jpg" alt="BHU">
                            </div>
                            <div class="company-name">BHU</div>
                        </div>

                        <div class="company-card" tabindex="0" role="button" aria-label="Ampliar logotipo de CTM">
                            <div class="company-logo">
                                <img src="../imagenes/ctm.jpg" alt="CTM">
                            </div>
                            <div class="company-name">CTM</div>
                        </div>

                        <div class="company-card" tabindex="0" role="button" aria-label="Ampliar logotipo de CCIS">
                            <div class="company-logo">
                                <img src="../imagenes/CENTRO-COMERCIAL-DE-SALTO.jpg" alt="CCIS">
                            </div>
                            <div class="company-name">CCIS</div>
                        </div>

                        <div class="company-card" tabindex="0" role="button" aria-label="Ampliar logotipo de JDS">
                            <div class="company-logo">
                                <img src="../imagenes/juntadesalto.png" alt="JDS">
                            </div>
                            <div class="company-name">JDS</div>
                        </div>

                        <div class="company-card" tabindex="0" role="button" aria-label="Ampliar logotipo de MTOP">
                            <div class="company-logo">
                                <img src="../imagenes/mtop.png" alt="MTOP">
                            </div>
                            <div class="company-name">MTOP</div>
                        </div>

                        <div class="company-card" tabindex="0" role="button" aria-label="Ampliar logotipo de PRODENOR">
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

<!-- JAVASCRIPT PRINCIPAL -->
<script src="js/scriptinicio.js" defer></script>

</body>
</html>

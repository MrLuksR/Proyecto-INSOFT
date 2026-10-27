<?php
include("../auth/config.php");
include("../database/Clases/Estudiante.php");
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$anioActual = date('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INADI | Actividades y eventos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../Inicio/CSS/stylesinicio.css?v=11">
    <link rel="stylesheet" href="/Proyecto-INSOFT/Estructura/topbar-controls.css?v=1">
    <style>
      .news-page-content{max-width:1100px;margin:36px auto 0;padding:0 24px 60px;}
      .news-page-hero{background:linear-gradient(135deg,#45459c,#292965);color:#fff;border-radius:22px;padding:clamp(28px,5vw,54px);box-shadow:0 16px 36px rgba(41,41,101,.18)}
      .news-page-hero .news-icon{width:58px;height:58px;display:grid;place-items:center;border-radius:16px;background:rgba(255,255,255,.15);font-size:25px;margin-bottom:22px}
      .news-page-hero h1{font-size:clamp(27px,4vw,40px);line-height:1.2;margin:0 0 14px}
      .news-page-hero p{font-size:16px;line-height:1.8;max-width:760px;margin:0;color:rgba(255,255,255,.9)}
      .news-page-note{margin-top:24px;background:#fff;border:1px solid #e8e8f2;border-radius:16px;padding:24px;color:#42425f;box-shadow:0 8px 24px rgba(30,30,80,.06);line-height:1.8}
      .news-page-note h2{font-size:20px;color:#292965;margin:0 0 10px}
      .news-page-links{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin-top:24px}
      .news-page-links a{display:flex;gap:12px;align-items:flex-start;background:#fff;border:1px solid #e8e8f2;border-radius:14px;padding:18px;color:#292965;text-decoration:none;box-shadow:0 5px 16px rgba(30,30,80,.04)}
      .news-page-links a:hover{background:#eeeeff;border-color:#cfcff4}
      .news-page-links i{color:#45459c;font-size:18px;margin-top:3px}
      .news-page-links span{display:block;font-weight:800} .news-page-links small{display:block;font-weight:400;color:#77778c;line-height:1.5;margin-top:5px}
      @media(max-width:760px){.news-page-content{margin-top:30px}}
    </style>
    <link rel="stylesheet" href="/Proyecto-INSOFT/Estructura/noticias-dropdown.css?v=4">
</head>
<body>
<div class="page">
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

        <!-- BUSCADOR SUPERIOR -->
        <div class="top-search">
            <form action="../Cursos/cursosindex.php" method="GET">
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
                setPortalBtn();
            ?>

            <?php inadiLanguageControl(); ?>

            <?php setLogin(); ?>

        </div>
    </header>

<aside class="sidebar">

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
            © INADI <?php echo $anioActual; ?><br>
            Instituto de Informática<br>
            Salto - Uruguay
        </div>

    </aside>
  <div class="content">
  <main class="news-page-content">
    <section class="news-page-hero">
      <div class="news-icon"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i></div>
      <h1>Actividades y eventos</h1>
      <p>Talleres, jornadas, proyectos educativos y actividades del instituto.</p>
    </section>
    <section class="news-page-note">
      <h2>Información de INADI</h2>
      <p>Aquí se anunciarán talleres, jornadas, encuentros, proyectos educativos y otras actividades organizadas por el instituto.</p>
      <p style="margin-top:12px">Las publicaciones y fechas se actualizarán en esta sección a medida que se anuncien novedades oficiales.</p>
    </section>
    <nav class="news-page-links" aria-label="Otras categorías de noticias">
      <a href="avisos-importantes.php"><i class="fa-solid fa-bullhorn" aria-hidden="true"></i><span>Avisos importantes<small>Cambios de horarios, comunicados institucionales y avisos generales.</small></span></a>
      <a href="proximos-cursos.php"><i class="fa-solid fa-book-open" aria-hidden="true"></i><span>Próximos cursos e inscripciones<small>Nuevas propuestas de formación, períodos de inscripción y fechas relevantes.</small></span></a>
      <a href="novedades-tecnologicas.php"><i class="fa-solid fa-microchip" aria-hidden="true"></i><span>Novedades tecnológicas<small>Informática, programación, inteligencia artificial y recursos digitales.</small></span></a>
    </nav>
  </main>
  </div>

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
    <a class="whatsapp"
       href="https://wa.me/"
       aria-label="Contactar por WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

</div>

<!-- CONTROL DEL MENÚ LATERAL -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector(".sidebar");
    const toggle = document.getElementById("sidebarToggle");
    const page = document.querySelector(".page");

    if (!sidebar || !toggle || !page) {
        return;
    }

    toggle.addEventListener("click", function () {
        const contraida =
            sidebar.classList.toggle("sidebar-collapsed");

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

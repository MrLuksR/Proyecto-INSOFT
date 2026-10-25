<?php
include("../auth/config.php");
include("../database/Clases/Estudiante.php");
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

    <title>INADI | Convenios</title>

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- CSS COMPARTIDO: BARRA SUPERIOR, MENÚ Y FOOTER -->
    <link rel="stylesheet"
          href="../Inicio/CSS/stylesinicio.css?v=10">

    <!-- CSS EXCLUSIVO DE CONVENIOS -->
    <link rel="stylesheet" href="convenios.css?v=6">
    <link rel="stylesheet" href="/Proyecto-INSOFT/Estructura/topbar-controls.css?v=1">
</head>

<body>
<div class="page">

    <!-- BARRA SUPERIOR: MISMA ESTRUCTURA QUE NOSOTROS -->
    <header class="topbar">

        <div class="topbar-left">
            <div class="top-logo">
                <img src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                     alt="Logo INADI">
                <span>INADI</span>
            </div>

            <div class="news">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>NOTICIAS</span>
            </div>
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

    <!-- MENÚ LATERAL -->
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

            <li class="active">
                <a href="../Convenios/convenios.php" aria-current="page">
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

    <!-- CONTENIDO PRINCIPAL -->
    <div class="content">
        <main class="main convenios-main">

            <!-- ENCABEZADO -->
            <section class="convenios-header">
                <span class="convenios-label">
                    VÍNCULOS INSTITUCIONALES
                </span>

                <h1>Nuestros <strong>convenios</strong></h1>

                <p>
                    Trabajamos junto a instituciones y organizaciones
                    para acercar oportunidades de formación informática
                    a nuestra comunidad.
                </p>
            </section>

            <!-- PRESENTACIÓN -->
            <section class="convenios-intro">
                <div class="convenios-intro-icon">
                    <i class="fa-solid fa-handshake-angle"></i>
                </div>

                <div>
                    <h2>Crecer a través de la colaboración</h2>
                    <p>
                        En INADI creemos que la educación se fortalece
                        mediante el trabajo conjunto. Los convenios
                        institucionales permiten generar oportunidades
                        de capacitación, acompañar a diferentes sectores
                        y contribuir al desarrollo de Salto.
                    </p>
                </div>
            </section>

            <!-- TARJETAS DE CONVENIOS -->
            <section class="convenios-section">
                <div class="convenios-section-heading">
                    <span>ALIANZAS INSTITUCIONALES</span>
                    <h2>Instituciones con las que nos vinculamos</h2>
                    <p>
                        Conocé algunas de las organizaciones vinculadas
                        a nuestra trayectoria educativa.
                    </p>
                </div>

                <div class="convenios-grid">

                    <!-- SUPU -->
                    <article class="convenio-card">
                        <div class="convenio-card-top">
                            <div class="convenio-icon">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>

                            <span class="convenio-tag">
                                Convenio institucional
                            </span>
                        </div>

                        <h3>SUPU</h3>

                        <p class="convenio-subtitle">
                            Sindicato Único de Policías del Uruguay
                        </p>

                        <p>
                            Vínculo institucional orientado a acercar
                            oportunidades de formación y capacitación
                            informática a integrantes de la organización.
                        </p>

                        <div class="convenio-card-footer">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Uruguay</span>
                        </div>
                    </article>

                    <!-- CENTRO COMERCIAL -->
                    <article class="convenio-card">
                        <div class="convenio-card-top">
                            <div class="convenio-icon">
                                <i class="fa-solid fa-store"></i>
                            </div>

                            <span class="convenio-tag">
                                Convenio institucional
                            </span>
                        </div>

                        <h3>Centro Comercial e Industrial de Salto</h3>

                        <p class="convenio-subtitle">
                            Centro Comercial e Industrial de Salto
                        </p>

                        <p>
                            Relación institucional vinculada al ámbito
                            comercial y empresarial de Salto, con
                            antecedentes de capacitación para personal
                            administrativo.
                        </p>

                        <div class="convenio-card-footer">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Salto, Uruguay</span>
                        </div>
                    </article>

                    <!-- INSTITUTO ARTIGAS WASHINGTON -->
                    <article class="convenio-card">
                        <div class="convenio-card-top">
                            <div class="convenio-icon">
                                <i class="fa-solid fa-school"></i>
                            </div>

                            <span class="convenio-tag">
                                Convenio educativo
                            </span>
                        </div>

                        <h3>Instituto Artigas Washington</h3>

                        <p class="convenio-subtitle">
                            Institución educativa de Salto
                        </p>

                        <p>
                            Forma parte de los vínculos institucionales
                            mencionados en la trayectoria de INADI.
                        </p>

                        <div class="convenio-card-footer">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Salto, Uruguay</span>
                        </div>
                    </article>

                </div>
            </section>

            <!-- OTRAS ORGANIZACIONES -->
            <section class="convenios-section organizaciones-section">
                <div class="convenios-section-heading">
                    <span>TRABAJO CONJUNTO</span>
                    <h2>Experiencia en capacitación institucional</h2>
                    <p>
                        A lo largo de su trayectoria, INADI también ha
                        desarrollado actividades de formación para
                        personal de distintas organizaciones.
                    </p>
                </div>

                <div class="organizaciones-grid">

                    <div class="organizacion-item">
                        <i class="fa-solid fa-building"></i>
                        <span>Centro Comercial e Industrial de Salto</span>
                    </div>

                    <div class="organizacion-item">
                        <i class="fa-solid fa-people-group"></i>
                        <span>Brigada de Infantería N.º 3</span>
                    </div>

                    <div class="organizacion-item">
                        <i class="fa-solid fa-landmark"></i>
                        <span>Funcionarios del M.T.O.P.</span>
                    </div>

                    <div class="organizacion-item">
                        <i class="fa-solid fa-building-columns"></i>
                        <span>Banco Hipotecario del Uruguay</span>
                    </div>

                    <div class="organizacion-item">
                        <i class="fa-solid fa-water"></i>
                        <span>Comisión Técnico Mixta de Salto Grande</span>
                    </div>

                    <div class="organizacion-item">
                        <i class="fa-solid fa-tractor"></i>
                        <span>Instituto Nacional de Investigación Agropecuaria</span>
                    </div>

                    <div class="organizacion-item">
                        <i class="fa-solid fa-people-roof"></i>
                        <span>Junta Departamental de Salto</span>
                    </div>

                    <div class="organizacion-item">
                        <i class="fa-solid fa-shop"></i>
                        <span>Empresas y comercios de la región</span>
                    </div>

                </div>
            </section>

            <!-- CIERRE -->
            <section class="convenios-final">
                <div class="convenios-final-icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>

                <div class="convenios-final-text">
                    <span>EDUCACIÓN Y DESARROLLO</span>
                    <h2>Construyamos nuevas oportunidades</h2>
                    <p>
                        Si representás una empresa u organización y querés
                        conocer las posibilidades de capacitación, ponete
                        en contacto con INADI.
                    </p>

                    <a href="../Contacto/contacto.php" class="convenios-cta">
                        Contactar con INADI
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </section>

        </main>
    </div>

    <!-- FOOTER COMPARTIDO -->
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

<?php
session_start();

include("../database/consultas/banner/getBannerInfo.php");
include("../database/consultas/cursos/getCursos.php");
include("../auth/config.php");
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI | Instituto de Informática</title>

    <!-- FONT AWESOME -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- CSS -->
    <link rel="stylesheet" href="CSS/stylesinicio.css">

</head>


<body>

<div class="page">


    <!-- =====================================================
         BARRA SUPERIOR
         ===================================================== -->

    <header class="topbar">


        <!-- IZQUIERDA -->

        <div class="topbar-left">


            <!-- LOGO -->

            <div class="top-logo">

                <img
                    src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                    alt="Logo INADI"
                >

                <span>INADI</span>

            </div>


            <!-- NOTICIAS -->

            <div class="news">

                <i class="fa-solid fa-triangle-exclamation"></i>

                <span>NOTICIAS</span>

            </div>


        </div>



        <!-- BUSCADOR -->

        <div class="top-search">

            <form
                action="../Cursos/cursosindex.php"
                method="GET"
            >

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="search"
                    name="buscar"
                    placeholder="Buscar información..."
                    aria-label="Buscar información"
                >

                <button type="submit">
                    Buscar
                </button>

            </form>

        </div>



        <!-- DERECHA -->

        <div class="topbar-right">


            <!-- IDIOMA -->

            <span class="language">

                <i class="fa-solid fa-globe"></i>

                ES

            </span>


            <!-- LOGIN -->

            <?php
                setLogin();
            ?>


        </div>

    </header>



    <!-- =====================================================
         SIDEBAR
         ===================================================== -->

    <aside class="sidebar">


        <ul class="menu">


            <!-- INICIO -->

            <li class="active">

                <a href="../Inicio/inicioindex.php">

                    <i class="fa-solid fa-house"></i>

                    <span>Inicio</span>

                </a>

            </li>



            <!-- NOSOTROS -->

            <li>

                <a href="../Nosotros/nosotros.php">

                    <i class="fa-solid fa-building-columns"></i>

                    <span>Nosotros</span>

                </a>

            </li>



            <!-- CURSOS -->

            <li>

                <a href="../Cursos/cursosindex.php">

                    <i class="fa-solid fa-book"></i>

                    <span>Cursos</span>

                </a>

            </li>



            <!-- MATRÍCULA -->

            <?php

                if (isset($_SESSION['nombre_usuario'])) {

                    setMatriculaBtn();

                }

            ?>



            <!-- CONTACTO -->

            <li>

                <a href="../Contacto/contacto.php">

                    <i class="fa-solid fa-envelope"></i>

                    <span>Contacto</span>

                </a>

            </li>



            <!-- GALERÍA -->

            <li>

                <a href="../Galeria/galeria.php">

                    <i class="fa-solid fa-image"></i>

                    <span>Galería</span>

                </a>

            </li>



            <!-- CONVENIOS -->

            <li>

                <a href="../Convenios/convenios.php">

                    <i class="fa-solid fa-handshake"></i>

                    <span>Convenios</span>

                </a>

            </li>



            <!-- EMPRESAS -->

            <li>

                <a href="#">

                    <i class="fa-solid fa-city"></i>

                    <span>Empresas</span>

                </a>

            </li>


        </ul>



        <!-- INFORMACIÓN INFERIOR -->

        <div class="sidebar-bottom">

            © INADI 2026<br>

            Instituto de Informática<br>

            Salto - Uruguay

        </div>


    </aside>



    <!-- =====================================================
         CONTENIDO PRINCIPAL
         ===================================================== -->

    <div class="content">


        <main class="main">


            <!-- =================================================
                 BIENVENIDA
                 ================================================= -->

            <section class="welcome">

                <div>

                    <span class="welcome-label">
                        INSTITUTO DE INFORMÁTICA
                    </span>

                    <h1>
                        Bienvenido a <strong>INADI</strong>
                    </h1>

                    <p>
                        Formación y capacitación en informática
                        · Artigas 827, Salto, Uruguay
                        · desde 1992
                    </p>

                </div>

            </section>



            <!-- =================================================
                 ESTADÍSTICAS
                 ================================================= -->

            <section class="stats">


                <!-- 34 AÑOS -->

                <div class="stat-card stat-blue">

                    <div class="stat-icon">

                        <i class="fa-solid fa-award"></i>

                    </div>

                    <div class="stat-info">

                        <div class="stat-number">

                            <?php
                                setTray();
                            ?>

                        </div>

                        <div class="stat-label">
                            Años de experiencia
                        </div>

                    </div>

                    <div class="stat-decoration"></div>

                </div>



                <!-- CURSOS -->

                <div class="stat-card stat-orange">

                    <div class="stat-icon">

                        <i class="fa-solid fa-book-open"></i>

                    </div>

                    <div class="stat-info">

                        <div class="stat-number">

                            <?php
                                echo count(obtenerCursos($pdo));
                            ?>

                        </div>

                        <div class="stat-label">
                            Cursos activos
                        </div>

                    </div>

                    <div class="stat-decoration"></div>

                </div>



                <!-- CERTIFICADOS -->

                <div class="stat-card stat-yellow">

                    <div class="stat-icon">

                        <i class="fa-solid fa-certificate"></i>

                    </div>

                    <div class="stat-info">

                        <div class="stat-number">
                            10K
                        </div>

                        <div class="stat-label">
                            Certificados emitidos
                        </div>

                    </div>

                    <div class="stat-decoration"></div>

                </div>



                <!-- EMPRESAS -->

                <div class="stat-card stat-purple">

                    <div class="stat-icon">

                        <i class="fa-solid fa-building"></i>

                    </div>

                    <div class="stat-info">

                        <div class="stat-number">
                            50
                        </div>

                        <div class="stat-label">
                            Empresas capacitadas
                        </div>

                    </div>

                    <div class="stat-decoration"></div>

                </div>


            </section>



            <!-- =================================================
                 CURSOS DESTACADOS
                 ================================================= -->

            <section class="section">


                <div class="section-title">

                    <div>

                        <span class="section-kicker">
                            FORMACIÓN
                        </span>

                        <h2>
                            Cursos destacados
                        </h2>

                    </div>


                    <a
                        href="../Cursos/cursosindex.php"
                        class="view-all"
                    >
                        Ver todos
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>



                <!-- CARRUSEL -->

                <div class="course-carousel">


                    <!-- FLECHA IZQUIERDA -->

                    <button
                        class="carousel-arrow arrow-left"
                        onclick="cambiarCurso(-1)"
                        type="button"
                        aria-label="Curso anterior"
                    >

                        <i class="fa-solid fa-chevron-left"></i>

                    </button>



                    <!-- BANNERS -->

                    <?php

                        mostrarBanners($pdo);

                    ?>



                    <!-- FLECHA DERECHA -->

                    <button
                        class="carousel-arrow arrow-right"
                        onclick="cambiarCurso(1)"
                        type="button"
                        aria-label="Siguiente curso"
                    >

                        <i class="fa-solid fa-chevron-right"></i>

                    </button>



                    <!-- INDICADORES -->

                    <div class="carousel-dots">

                        <?php

                            setButtons($num);

                        ?>

                    </div>


                </div>


            </section>



            <!-- =================================================
                 RESEÑAS
                 ================================================= -->

            <section class="section">


                <div class="section-title">

                    <div>

                        <span class="section-kicker">
                            EXPERIENCIAS
                        </span>

                        <h2>
                            Reseñas de estudiantes
                        </h2>

                    </div>


                    <span class="section-count">
                        4 reseñas
                    </span>

                </div>



                <div class="reviews">


                    <!-- RESEÑA 1 -->

                    <article class="review-card">

                        <div class="review-header">

                            <div class="avatar">
                                VT
                            </div>

                            <div>

                                <div class="review-name">
                                    Valentina Torres
                                </div>

                                <div class="review-course">
                                    Egresada · Programación Python
                                </div>

                            </div>

                            <div class="stars">
                                ★★★★★
                            </div>

                        </div>


                        <p class="review-text">

                            INADI cambió mi vida profesional.
                            Los docentes son excelentes y el ambiente
                            de aprendizaje es único.
                            Hoy trabajo en desarrollo de software gracias
                            a lo que aprendí acá.

                        </p>


                        <div class="review-more">
                            Ver más⌄
                        </div>

                    </article>



                    <!-- RESEÑA 2 -->

                    <article class="review-card">

                        <div class="review-header">

                            <div class="avatar">
                                MA
                            </div>

                            <div>

                                <div class="review-name">
                                    Martín Aguirre
                                </div>

                                <div class="review-course">
                                    Estudiante · Ciberseguridad
                                </div>

                            </div>

                            <div class="stars">
                                ★★★★★
                            </div>

                        </div>


                        <p class="review-text">

                            Los cursos son muy prácticos y actualizados.
                            Las instalaciones son modernas y siempre hay
                            alguien dispuesto a ayudarte.

                        </p>


                        <div class="review-more">
                            Ver más⌄
                        </div>

                    </article>



                    <!-- RESEÑA 3 -->

                    <article class="review-card">

                        <div class="review-header">

                            <div class="avatar">
                                LF
                            </div>

                            <div>

                                <div class="review-name">
                                    Lucía Fernández
                                </div>

                                <div class="review-course">
                                    Egresada · Marketing Digital
                                </div>

                            </div>

                            <div class="stars">
                                ★★★★☆
                            </div>

                        </div>


                        <p class="review-text">

                            La metodología es muy efectiva.
                            Aprendí herramientas que utilizo todos los días.
                            INADI es el mejor lugar para empezar
                            en el mundo digital en Salto.

                        </p>


                        <div class="review-more">
                            Ver más⌄
                        </div>

                    </article>



                    <!-- RESEÑA 4 -->

                    <article class="review-card">

                        <div class="review-header">

                            <div class="avatar">
                                DS
                            </div>

                            <div>

                                <div class="review-name">
                                    Diego Sosa
                                </div>

                                <div class="review-course">
                                    CCIS · Empresa convenio
                                </div>

                            </div>

                            <div class="stars">
                                ★★★★★
                            </div>

                        </div>


                        <p class="review-text">

                            Nuestra empresa tiene convenio con INADI hace años.
                            La calidad de los egresados es excelente.
                            Son una institución de referencia en toda la región.

                        </p>


                        <div class="review-more">
                            Ver más⌄
                        </div>

                    </article>


                </div>

            </section>



            <!-- =================================================
                 EMPRESAS
                 ================================================= -->

            <section class="section">


                <div class="section-title">

                    <div>

                        <span class="section-kicker">
                            VÍNCULOS
                        </span>

                        <h2>
                            Empresas convenio
                        </h2>

                    </div>


                    <a
                        href="#"
                        class="view-all"
                    >
                        Ver todas
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>



                <div class="companies">


                   <div class="company-card">
                    <div class="company-logo">
                     <img src="../imagenes/tata.png" alt="TATA">
                     </div>

                     <div class="company-name">TATA</div>
                        </div>


                    <div class="company-card">
                    <div class="company-logo">
                     <img src="../imagenes/inia.jpg" alt="INIA">
                     </div>

                     <div class="company-name">INIA</div>
                        </div>


                    <div class="company-card">
                    <div class="company-logo">
                     <img src="../imagenes/bhu.jpg" alt="BHU">
                     </div>

                     <div class="company-name">BHU</div>
                        </div>


                    <div class="company-card">
                    <div class="company-logo">
                     <img src="../imagenes/ctm.jpg" alt="CTM">
                     </div>

                     <div class="company-name">CTM</div>
                        </div>

 
                   <div class="company-card">
                    <div class="company-logo">
                     <img src="../imagenes/CENTRO-COMERCIAL-DE-SALTO.jpg" alt="CCIS">
                     </div>

                     <div class="company-name">CCIS</div>
                        </div>


                   <div class="company-card">
                    <div class="company-logo">
                     <img src="../imagenes/juntadesalto.png" alt="JDS">
                     </div>

                     <div class="company-name">JDS</div>
                        </div>

                  <div class="company-card">
                    <div class="company-logo">
                     <img src="../imagenes/mtop.png" alt="MTOP">
                     </div>

                     <div class="company-name">MTOP</div>
                        </div>


                    <div class="company-card">
                        <div class="company-logo">PR</div>
                        <div class="company-name">PRODENOR</div>
                    </div>


                </div>

            </section>


        </main>

    </div>



    <!-- =====================================================
         FOOTER
         ===================================================== -->

    <footer>


        <div class="footer-content">


            <!-- MARCA -->

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



            <!-- CONTACTO -->

            <div class="footer-column">

                <h3>
                    CONTACTO
                </h3>

                <p>

                    <i class="fa-solid fa-location-dot"></i>

                    Artigas 827, Salto, Uruguay

                </p>

                <p>

                    <i class="fa-solid fa-phone"></i>

                    473 31609

                </p>

            </div>



            <!-- HORARIO -->

            <div class="footer-column">

                <h3>
                    HORARIO
                </h3>

                <p>
                    Lunes - Viernes: 08:00 - 20:00
                </p>

                <p>
                    Sábados: 08:00 - 13:00
                </p>

            </div>



            <!-- REDES -->

            <div class="footer-column">

                <h3>
                    REDES SOCIALES
                </h3>

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



    <!-- =====================================================
         WHATSAPP
         ===================================================== -->

    <div class="whatsapp">

        <i class="fa-brands fa-whatsapp"></i>

    </div>


</div>



<!-- JAVASCRIPT -->

<script src="js/script.js"></script>


</body>

</html>
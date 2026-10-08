<?php
session_start();
include("../auth/config.php");

$nombreUsuario = "Usuario";
$anioActual = date("Y");
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI | Convenios</title>

    <!-- FONT AWESOME -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- CSS -->
    <link
        rel="stylesheet"
        href="convenios.css?v=3"
    >

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

                NOTICIAS

            </div>

        </div>

        <div class="topbar-right">

            <span>

                <i class="fa-solid fa-globe"></i>

                ES

            </span>

            <?php setLogin(); ?>

        </div>

    </header>


    <!-- BARRA LATERAL -->

    <aside class="sidebar">

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

            <li class="active">
                <a href="convenios.php">
                    <i class="fa-solid fa-handshake"></i>
                    <span>Convenios</span>
                </a>
            </li>

            <li>
                <a href="#">
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

        <main class="main">

            <!-- ENCABEZADO -->

            <section class="convenios-header">

                <span class="convenios-label">
                    VÍNCULOS INSTITUCIONALES
                </span>

                <h1>Convenios</h1>

                <p>
                    Conocé los convenios y beneficios que INADI
                    ofrece junto a diferentes instituciones.
                </p>

            </section>


            <!-- CONTENIDO -->

            <div class="convenios-layout">

                <!-- CONVENIOS -->

                <section class="convenios-main">

                    <div class="section-heading">

                        <div class="section-icon">
                            <i class="fa-solid fa-handshake"></i>
                        </div>

                        <div>

                            <span>BENEFICIOS</span>

                            <h2>Nuestros convenios</h2>

                        </div>

                    </div>


                    <!-- CONVENIO SUPU -->

                    <article class="convenio-card">

                        <div class="card-top">

                            <div class="institution-icon">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>

                            <div class="card-date">

                                <i class="fa-regular fa-calendar"></i>

                                13 Ago 2018

                            </div>

                        </div>

                        <div class="card-category">
                            NOTICIAS
                        </div>

                        <h3>
                            Convenio con SUPU.
                            25% de descuento en todos nuestros cursos.
                        </h3>

                        <p>
                            A partir del 8 de marzo de 2018 junto a la
                            Sociedad de Funcionarios Policiales en
                            Actividad y Retiro de la Administración
                            Central (SUPU), firmamos un nuevo convenio
                            prestando el servicio de capacitación
                            informática.
                        </p>

                        <p>
                            Todos los que estén comprendidos en este
                            convenio obtendrán el beneficio del
                            <strong>25% de descuento</strong> en todos
                            los cursos.
                        </p>

                        <div class="discount">

                            <div class="discount-icon">
                                <i class="fa-solid fa-percent"></i>
                            </div>

                            <div>

                                <span>BENEFICIO DEL CONVENIO</span>

                                <strong>25% de descuento</strong>

                            </div>

                        </div>

                        <div class="card-footer">

                            <span>
                                <i class="fa-solid fa-building"></i>
                                SUPU
                            </span>

                            <span>
                                <i class="fa-solid fa-location-dot"></i>
                                Salto, Uruguay
                            </span>

                        </div>

                    </article>


                    <!-- CONVENIO CENTRO COMERCIAL -->

                    <article class="convenio-card">

                        <div class="card-top">

                            <div class="institution-icon commercial">
                                <i class="fa-solid fa-store"></i>
                            </div>

                            <div class="card-date">

                                <i class="fa-regular fa-calendar"></i>

                                13 Ago 2018

                            </div>

                        </div>

                        <div class="card-category">
                            NOTICIAS
                        </div>

                        <h3>
                            Convenio con el Centro Comercial e
                            Industrial de Salto
                        </h3>

                        <p>
                            A partir de marzo de 2018 renovamos el
                            convenio con el Centro Comercial e
                            Industrial de Salto, el cual beneficiará
                            a los socios del mismo en todos nuestros
                            cursos.
                        </p>

                        <p>
                            Los socios obtendrán un
                            <strong>
                                descuento del veinticinco por
                                ciento (25%)
                            </strong>
                            en todos nuestros cursos.
                        </p>

                        <div class="discount">

                            <div class="discount-icon">
                                <i class="fa-solid fa-percent"></i>
                            </div>

                            <div>

                                <span>BENEFICIO DEL CONVENIO</span>

                                <strong>25% de descuento</strong>

                            </div>

                        </div>

                        <div class="card-footer">

                            <span>
                                <i class="fa-solid fa-building"></i>
                                Centro Comercial e Industrial
                            </span>

                            <span>
                                <i class="fa-solid fa-location-dot"></i>
                                Salto, Uruguay
                            </span>

                        </div>

                    </article>

                </section>


                <!-- SIDEBAR DE NOTICIAS -->

                <aside class="news-sidebar">

                    <div class="sidebar-card search-card">

                        <div class="sidebar-title">

                            <i class="fa-solid fa-magnifying-glass"></i>

                            <h3>Buscar</h3>

                        </div>

                        <div class="search-box">

                            <input
                                type="text"
                                placeholder="Buscar..."
                            >

                            <button
                                type="button"
                                title="Buscar"
                            >

                                <i class="fa-solid fa-magnifying-glass"></i>

                            </button>

                        </div>

                    </div>


                    <div class="sidebar-card">

                        <div class="sidebar-title">

                            <i class="fa-solid fa-folder"></i>

                            <h3>Categorías</h3>

                        </div>

                        <div class="category-item">

                            <span>Noticias</span>

                            <span class="category-count">
                                4
                            </span>

                        </div>

                    </div>


                    <div class="sidebar-card">

                        <div class="sidebar-title">

                            <i class="fa-solid fa-clock"></i>

                            <h3>Últimas Noticias</h3>

                        </div>

                        <div class="latest-news">

                            <div class="latest-item">

                                <h4>
                                    Talleres para aprender a utilizar
                                    tu tableta
                                </h4>

                                <span>
                                    diciembre 28, 2018
                                </span>

                            </div>

                            <div class="latest-item">

                                <h4>
                                    Acredita y certifica tus
                                    conocimientos
                                </h4>

                                <span>
                                    diciembre 28, 2018
                                </span>

                            </div>

                            <div class="latest-item">

                                <h4>
                                    Convenio con SUPU. 25% de
                                    descuento en todos nuestros
                                    cursos.
                                </h4>

                                <span>
                                    agosto 13, 2018
                                </span>

                            </div>

                            <div class="latest-item">

                                <h4>
                                    Convenio con el Centro Comercial
                                    e Industrial de Salto
                                </h4>

                                <span>
                                    agosto 13, 2018
                                </span>

                            </div>

                        </div>

                    </div>

                </aside>

            </div>


            <!-- CIERRE -->

            <section class="convenios-final">

                <div class="final-icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>

                <div>

                    <h2>
                        Más oportunidades para capacitarte
                    </h2>

                    <p>
                        Los convenios institucionales permiten que
                        más personas puedan acceder a la formación
                        informática de INADI y aprovechar beneficios
                        especiales en nuestros cursos.
                    </p>

                </div>

            </section>

        </main>

    </div>


    <!-- WHATSAPP -->

    <a
        href="#"
        class="whatsapp"
        title="Contactar por WhatsApp"
    >

        <i class="fa-brands fa-whatsapp"></i>

    </a>

</div>

</body>
</html>

<?php


// CONFIGURACIÓN INICIAL


$nombreUsuario = "Usuario";
$anioActual = date("Y");

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        INADI | Instituto de Informática
    </title>


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="CSS/styles.css"
    >

</head>


<body>

<div class="page">


    <!-- BARRA SUPERIOR -->

    <header class="topbar">

        <div class="topbar-left">


            <!-- LOGO -->

            <div class="top-logo">

                <img
                    src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                    alt="Logo INADI"
                >

                <span>
                    INADI
                </span>

            </div>


            <!-- NOTICIAS -->

            <div class="news">

                <i class="fa-solid fa-triangle-exclamation"></i>

                NOTICIAS

            </div>

        </div>



        <div class="topbar-right">


            <!-- IDIOMA -->

            <span>

                <i class="fa-solid fa-globe"></i>

                ES

            </span>


            <!-- USUARIO -->

            <a
                href="../Bienvenido/bienvenidoindex.html"
                class="user-icon"
                title="Iniciar sesión"
            >

                <i class="fa-regular fa-user"></i>

            </a>

        </div>

    </header>



    <!-- SIDEBAR -->

    <aside class="sidebar">

        <ul class="menu">


            <li class="active">

                <a href="../Nosotros/nosotros.php">

                <i class="fa-solid fa-building-columns"></i>

            <span>
                Nosotros
            </span>

                </a>

            </li>

            </li>


            <li class="active">

                <a href="../Cursos/cursosindex.php">

                    <i class="fa-solid fa-book"></i>

                    <span>
                        Cursos
                    </span>

                </a>

            </li>


            <li>

                <a href="../Matricula/matricula.php">

                    <i class="fa-solid fa-file-signature"></i>

                    <span>
                        Matrícula
                    </span>

                </a>

            </li>


            <li>

                <i class="fa-solid fa-envelope"></i>

                <span>
                    Contacto
                </span>

            </li>


            <li>

                <i class="fa-solid fa-image"></i>

                <span>
                    Galería
                </span>

            </li>


            <li>

                <i class="fa-solid fa-handshake"></i>

                <span>
                    Convenios
                </span>

            </li>


            <li>

                <i class="fa-solid fa-city"></i>

                <span>
                    Empresas
                </span>

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


            <!-- BIENVENIDA -->

            <section class="welcome">

                <h1>

                    Bienvenido a
                    <strong>INADI</strong>

                </h1>


                <p>

                    Instituto de Informática ·
                    Artigas 827, Salto, Uruguay ·
                    desde 1992

                </p>

            </section>



            <!-- ESTADÍSTICAS -->

            <section class="stats">


                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="fa-solid fa-award"></i>

                    </div>

                    <div>

                        <div class="stat-number">
                            34
                        </div>

                        <div class="stat-label">
                            Años de experiencia
                        </div>

                    </div>

                </div>



                <div class="stat-card">

                    <div class="stat-icon">
                        📚
                    </div>

                    <div>

                        <div class="stat-number">
                            23
                        </div>

                        <div class="stat-label">
                            Cursos activos
                        </div>

                    </div>

                </div>



                <div class="stat-card">

                    <div class="stat-icon">
                        🎓
                    </div>

                    <div>

                        <div class="stat-number">
                            10K
                        </div>

                        <div class="stat-label">
                            Certificados emitidos
                        </div>

                    </div>

                </div>



                <div class="stat-card">

                    <div class="stat-icon">
                        🤝
                    </div>

                    <div>

                        <div class="stat-number">
                            50
                        </div>

                        <div class="stat-label">
                            Empresas capacitadas
                        </div>

                    </div>

                </div>

            </section>



            <!-- CURSOS DESTACADOS -->

            <section class="section">

                <div class="section-title">

                    <h2>
                        Cursos Destacados
                    </h2>

                    <a
                        href="#"
                        class="view-all"
                    >
                        Ver todos →
                    </a>

                </div>


                <div class="course-carousel">


                    <button
                        class="carousel-arrow arrow-left"
                        onclick="cambiarCurso(-1)"
                        type="button"
                    >

                        <i class="fa-solid fa-chevron-left"></i>

                    </button>



                    <!-- CURSO 1 -->

                    <div class="course-slide active">

                        <img
                            src="https://plus.unsplash.com/premium_photo-1683121710572-7723bd2e235d?q=80&w=1332&auto=format&fit=crop"
                            alt="Inteligencia Artificial"
                        >

                        <div class="course-overlay">

                            <div class="course-tag">
                                Nuevo · 6 meses
                            </div>

                            <div class="course-category">
                                Machine Learning · Python · Automatización
                            </div>

                            <h3>
                                Inteligencia Artificial Aplicada
                            </h3>

                            <p>
                                20 lugares disponibles
                            </p>

                            <button
                                class="course-button"
                                type="button"
                            >
                                Inscribirme
                            </button>

                        </div>

                    </div>



                    <!-- CURSO 2 -->

                    <div class="course-slide">

                        <img
                            src="https://images.unsplash.com/photo-1607799279861-4dd421887fb3?q=80&w=1170&auto=format&fit=crop"
                            alt="Programación Python"
                        >

                        <div class="course-overlay">

                            <div class="course-tag">
                                Destacado · 6 meses
                            </div>

                            <div class="course-category">
                                Python · Programación · Desarrollo
                            </div>

                            <h3>
                                Programación Python
                            </h3>

                            <p>
                                15 lugares disponibles
                            </p>

                            <button
                                class="course-button"
                                type="button"
                            >
                                Inscribirme
                            </button>

                        </div>

                    </div>



                    <!-- CURSO 3 -->

                    <div class="course-slide">

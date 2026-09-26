<?php

// CONFIGURACIÓN INICIAL
include("../database/consultas/cursos/getCursos.php");
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
        INADI | Cursos
    </title>


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="cursostyles.css"
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


            <!-- NOSOTROS -->

            <li>

                <a href="../Nosotros/nosotros.php">

                    <i class="fa-solid fa-building-columns"></i>

                    <span>
                        Nosotros
                    </span>

                </a>

            </li>


            <!-- CURSOS -->

            <li class="active">

                <a href="cursosindex.php">

                    <i class="fa-solid fa-book"></i>

                    <span>
                        Cursos
                    </span>

                </a>

            </li>


            <!-- MATRÍCULA -->

            <li>

                <a href="../Matricula/matricula.php">

                    <i class="fa-solid fa-file-signature"></i>

                    <span>
                        Matrícula
                    </span>

                </a>

            </li>


            <!-- CONTACTO -->

            <li>

                <a href="#">

                    <i class="fa-solid fa-envelope"></i>

                    <span>
                        Contacto
                    </span>

                </a>

            </li>


            <!-- GALERÍA -->

            <li>

                <a href="#">

                    <i class="fa-solid fa-image"></i>

                    <span>
                        Galería
                    </span>

                </a>

            </li>


            <!-- CONVENIOS -->

            <li>

                <a href="#">

                    <i class="fa-solid fa-handshake"></i>

                    <span>
                        Convenios
                    </span>

                </a>

            </li>


            <!-- EMPRESAS -->

            <li>

                <a href="#">

                    <i class="fa-solid fa-city"></i>

                    <span>
                        Empresas
                    </span>

                </a>

            </li>

        </ul>


        <!-- INFORMACIÓN INSTITUCIONAL -->

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

            <section class="courses-header">

                <span class="courses-label">
                    INSTITUTO DE INFORMÁTICA
                </span>

                <h1>
                    Nuestros <strong>Cursos</strong>
                </h1>

                <p>
                    Descubrí nuestra propuesta educativa y
                    elegí la formación que mejor se adapte
                    a tus objetivos.
                </p>

            </section>



            <!-- ESTADÍSTICAS -->

            <section class="statistics">


                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="fa-solid fa-award"></i>

                    </div>

                    <div class="stat-info">

                        <strong>
                            34
                        </strong>

                        <span>
                            Años de<br>
                            experiencia
                        </span>

                    </div>

                </div>



                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="fa-solid fa-book-open"></i>

                    </div>

                    <div class="stat-info">

                        <strong>
                            23
                        </strong>

                        <span>
                            Cursos<br>
                            activos
                        </span>

                    </div>

                </div>



                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="fa-solid fa-graduation-cap"></i>

                    </div>

                    <div class="stat-info">

                        <strong>
                            10K
                        </strong>

                        <span>
                            Certificados<br>
                            emitidos
                        </span>

                    </div>

                </div>



                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="fa-solid fa-handshake"></i>

                    </div>

                    <div class="stat-info">

                        <strong>
                            50
                        </strong>

                        <span>
                            Empresas<br>
                            capacitadas
                        </span>

                    </div>

                </div>

            </section>



            <!-- CURSOS DESTACADOS -->

            <section
                class="courses-section"
                id="cursos"
            >


                <div class="section-title">

                    <div>

                        <span class="section-label">
                            FORMACIÓN
                        </span>

                        <h2>
                            Cursos Destacados
                        </h2>

                    </div>

                </div>



                <div class="courses-grid">


                    <!-- Cursos -->

                    <?php
                        mostrarCursos($pdo);
                    ?>


            <!-- BOTÓN CONTACTO -->

            <a
                href="#"
                class="whatsapp"
                title="Contactar"
                aria-label="Contactar"
            >

                <i class="fa-solid fa-phone"></i>

            </a>


        </main>

    </div>

</div>

</body>

</html>
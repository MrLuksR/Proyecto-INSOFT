<?php
/*
| INADI - Página de Inicio

| Archivo: Inicio/inicioindex.php

*/
include("getCursos.php");
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>INADI - Inicio</title>

    <link
        rel="stylesheet"
        href="styles.css"
    >

    <link
        rel="icon"
        type="image/png"
        href="img/logo.png"
    >

</head>

<body>


    <!-- =========================================================
         BARRA LATERAL
    ========================================================== -->

    <aside class="sidebar">


        <!-- LOGO -->

        <div class="logo">

            <img
                src="../Elementos Gráficos/Logo Inadi sin Brillo.png"
                alt="Logo INADI"
                class="logo-img"
            >

            <h1>INADI</h1>

            <span>Desde 1992</span>

        </div>


        <!-- MENÚ -->

        <nav class="menu">


            <!-- NOSOTROS -->

            <a
                href="#"
                class="menu-item"
            >

                <span class="icon">🎓</span>

                <span>Nosotros</span>

            </a>


            <!-- CURSOS -->

            <a
                href="#cursos"
                class="menu-item active"
            >

                <span class="icon">📖</span>

                <span>Cursos</span>

            </a>


            <!-- MATRÍCULA -->

            <a
                href="../Matricula/matricula.php"
                class="menu-item"
            >

                <span class="icon">☷</span>

                <span>Matrícula</span>

            </a>


            <!-- CONTACTO -->

            <a
                href="#contacto"
                class="menu-item"
            >

                <span class="icon">✉</span>

                <span>Contacto</span>

            </a>


            <!-- GALERÍA -->

            <a
                href="#"
                class="menu-item"
            >

                <span class="icon">▣</span>

                <span>Galería</span>

            </a>


            <!-- CONVENIOS -->

            <a
                href="#"
                class="menu-item"
            >

                <span class="icon">🤝</span>

                <span>Convenios</span>

            </a>

        </nav>


        <!-- PARTE INFERIOR -->

        <div class="sidebar-bottom">


            <!-- EMPRESAS -->

            <a
                href="#"
                class="bottom-item"
            >

                <span>▦</span>

                <span>Empresas</span>

            </a>


            <!-- CONTACTAR -->

            <a
                href="#contacto"
                class="contact-button"
            >

                Contactar

            </a>

        </div>


    </aside>



    <!-- =========================================================
         CONTENIDO PRINCIPAL
    ========================================================== -->

    <main class="main-content">


        <!-- =====================================================
             BARRA SUPERIOR
        ====================================================== -->

        <header class="topbar">


            <!-- NOTICIAS -->

            <div class="news">

                <strong>NOTICIAS:</strong>

                <span>
                    📢 Talleres para aprender a utilizar tu tableta
                </span>

                <span>
                    📢 Acredita y certifica tus conocimientos
                </span>

            </div>


            <!-- ACCIONES -->

            <div class="topbar-actions">


                <!-- PORTAL ESTUDIANTES -->

                <a
                    href="../Estudiante/portalEstudiante.php"
                    class="student-button"
                >
                    Portal Estudiantes
                </a>


                <!-- ICONO WEB -->

                <a
                    href="#"
                    class="top-icon"
                    aria-label="Sitio web"
                >
                    🌐
                </a>


                <!-- ICONO USUARIO -->

                <a
                    href="../login/loginn.php"
                    class="top-icon"
                    aria-label="Iniciar sesión"
                >
                    ◎
                </a>


            </div>

        </header>



        <!-- =====================================================
             BIENVENIDA
        ====================================================== -->

        <section class="welcome">


            <h2>
                Habilidades que transforman tu futuro
            </h2>


            <p>
                Elige tu próximo desafío
            </p>


        </section>



        <!-- =====================================================
             ESTADÍSTICAS
        ====================================================== -->

        <section class="statistics">


            <!-- ESTADÍSTICA 1 -->

            <div class="stat-card">

                <div class="stat-icon">
                    🏆
                </div>


                <div class="stat-info">

                    <strong>34</strong>

                    <span>
                        Años de<br>
                        experiencia
                    </span>

                </div>

            </div>



            <!-- ESTADÍSTICA 2 -->

            <div class="stat-card">

                <div class="stat-icon">
                    📚
                </div>


                <div class="stat-info">

                    <strong>23</strong>

                    <span>
                        Cursos<br>
                        activos
                    </span>

                </div>

            </div>



            <!-- ESTADÍSTICA 3 -->

            <div class="stat-card">

                <div class="stat-icon">
                    🎓
                </div>


                <div class="stat-info">

                    <strong>10K</strong>

                    <span>
                        Certificados<br>
                        emitidos
                    </span>

                </div>

            </div>



            <!-- ESTADÍSTICA 4 -->

            <div class="stat-card">

                <div class="stat-icon">
                    🤝
                </div>


                <div class="stat-info">

                    <strong>50</strong>

                    <span>
                        Empresas<br>
                        capacitadas
                    </span>

                </div>

            </div>


        </section>



        <!-- =====================================================
             CURSOS DESTACADOS
        ====================================================== -->

        <section
            class="courses-section"
            id="cursos"
        >


            <!-- TÍTULO -->

            <div class="section-title">


                <h2>
                    Cursos destacados
                </h2>

            </div>



            <!-- GRID DE CURSOS -->

            <div class="courses-grid">


                <!-- =================================================
                     CURSOS
                ================================================== -->
                <?php
                    mostrarCursos($pdo);
                ?>
                
            </div>
                


        <!-- =====================================================
             BOTÓN DE CONTACTO
        ====================================================== -->

        <a
            href="#contacto"
            class="whatsapp"
            aria-label="Contactar"
        >
            ☎
        </a>


    </main>


</body>

</html>
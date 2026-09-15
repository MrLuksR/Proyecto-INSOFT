<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI - Inicio</title>

    <link rel="stylesheet" href="syles.css">

    <link rel="icon" type="image/png" href="img/logo.png">
</head>

<body>

    <!-- 
        BARRA LATERAL
    -->

    <aside class="sidebar">

        <!-- LOGO -->
        <div class="logo">

            <img src="../Elementos Gráficos/Logo Inadi sin Brillo.png" alt="Logo INADI" class="logo-img">

            <h1>INADI</h1>

            <span>Desde 1992</span>

        </div>


        <!-- MENÚ -->
        <nav class="menu">

            <a href="#" class="menu-item">

                <span class="icon">🎓</span>

                <span>Nosotros</span>

            </a>


            <a href="#" class="menu-item active">

                <span class="icon">📖</span>

                <span>Cursos</span>

            </a>


            <a href="#" class="menu-item">

                <span class="icon">☷</span>

                <span>Matrícula</span>

            </a>


            <a href="#" class="menu-item">

                <span class="icon">✉</span>

                <span>Contacto</span>

            </a>


            <a href="#" class="menu-item">

                <span class="icon">▣</span>

                <span>Galería</span>

            </a>


            <a href="#" class="menu-item">

                <span class="icon">🤝</span>

                <span>Convenios</span>

            </a>

        </nav>


        <!-- PARTE INFERIOR -->
        <div class="sidebar-bottom">

            <a href="#" class="bottom-item">

                <span>▦</span>

                <span>Empresas</span>

            </a>


            <a href="#" class="contact-button">

                Contactar

            </a>

        </div>

    </aside>


    <!-- 
         CONTENIDO PRINCIPAL
     -->

    <main class="main-content">


        <!-- 
             BARRA SUPERIOR
         -->

        <header class="topbar">

            <div class="news">

                <strong>NOTICIAS:</strong>

                <span>📢 Talleres para aprender a utilizar tu tableta</span>

                <span>📢 Acredita y certifica tus conocimientos</span>

            </div>


            <div class="topbar-actions">

                <a href="#" class="student-button">
                    Portal Estudiantes
                </a>

                <a href="#" class="top-icon">
                    🌐
                </a>

                <a href="#" class="top-icon">
                    ◎
                </a>

            </div>

        </header>


        <!-- 
             BIENVENIDA
         -->

        <section class="welcome">

            <h2>
                Bienvenido a INADI
                
            </h2>

            <p>
                Instituto de Informática · Artigas 827, Salto, Uruguay · desde 1992
            </p>

        </section>


        <!-- 
             ESTADÍSTICAS
         -->

        <section class="statistics">


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


        <!-- 
             CURSOS DESTACADOS
         -->

        <section class="courses-section">


            <div class="section-title">

                <h2>
                    Cursos Destacados
                </h2>

                <a href="#">
                    Ver todos →
                </a>

            </div>


            <!-- GRID DE CURSOS -->

            <div class="courses-grid">

                <?php
                    include("getCursos.php");
                ?>

            </div>

        </section>


        <!-- 
             
         -->

        <a href="#" class="whatsapp">
            ☎
        </a>


    </main>


</body>

</html>
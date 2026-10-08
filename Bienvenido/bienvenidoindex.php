
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI | Bienvenido</title>

    <link
        rel="icon"
        href="../Elementos Gráficos/Logo Inadi sin Brillo.png"
    >

    <!-- Font Awesome para los iconos -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- CSS existente -->
    <link rel="stylesheet" href="stylesbienvenido.css">
</head>

<body>

    <!-- BOTÓN PARA VOLVER AL INICIO -->
    <a
        href="../Inicio/inicioindex.php"
        class="home-button"
        title="Volver al inicio"
        aria-label="Volver a la página de inicio"
    >
        <i class="fa-solid fa-house"></i>
    </a>

    <main class="welcome-card">

        <!-- PANEL IZQUIERDO -->
        <section class="welcome-panel">

            <div class="circle circle-top"></div>
            <div class="circle circle-bottom"></div>

            <!-- LOGO -->
            <img
                src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                alt="Instituto Nacional de Informática"
                class="logo"
            >

            <!-- CONTENIDO -->
            <div class="content">

                <h1>
                    INGRESA AQUÍ,<br>
                    REGÍSTRATE
                </h1>

                <p>
                    Buscá el curso que te interese
                    y accedé a sus detalles.
                </p>

                <!-- BOTONES -->
                <div class="btn-cont">

                    <a
                        href="../login/loginn.php"
                        class="login-button"
                    >
                        INGRESA AQUÍ
                    </a>

                    <a
                        href="../registro/index.php"
                        class="regis-button"
                    >
                        REGÍSTRATE
                    </a>

                </div>

            </div>

            <!-- PIE DE PÁGINA -->
            <footer class="footer">
                <span>INADI</span>
                <span>&bull;</span>
                <span>Instituto Nacional de Informática</span>
            </footer>

        </section>

        <!-- PANEL DERECHO -->
        <section class="visual-panel">

            <!-- DECORACIONES -->
            <div class="decor decor-one"></div>
            <div class="decor decor-two"></div>

            <!-- CURSOS -->
            <div class="courses-container">

                <!-- PYTHON -->
                <div class="course-card python-card">

                    <img
                        src="imagenes/phyton.jpeg"
                        alt="Curso de Python"
                    >

                    <div class="course-info">
                        <h3>Python</h3>
                        <p>Programación y desarrollo</p>
                    </div>

                </div>

                <!-- DISEÑO GRÁFICO -->
                <div class="course-card design-card">

                    <img
                        src="imagenes/diseñografico.jpeg"
                        alt="Curso de Diseño Gráfico"
                    >

                    <div class="course-info">
                        <h3>Diseño Gráfico</h3>
                        <p>Creatividad y diseño digital</p>
                    </div>

                </div>

                <!-- MARKETING -->
                <div class="course-card marketing-card">

                    <img
                        src="imagenes/marketing.jpeg"
                        alt="Curso de Marketing"
                    >

                    <div class="course-info">
                        <h3>Marketing</h3>
                        <p>Estrategias digitales</p>
                    </div>

                </div>

                <!-- VIDEOJUEGOS -->
                <div class="course-card videojuegos-card">

                    <img
                        src="imagenes/desarrollovideojuego.jpeg"
                        alt="Curso de Desarrollo de Videojuegos"
                    >

                    <div class="course-info">
                        <h3>Desarrollo de Videojuegos</h3>
                        <p>Creá tus propios videojuegos</p>
                    </div>

                </div>

            </div>

        </section>

    </main>

</body>
</html>

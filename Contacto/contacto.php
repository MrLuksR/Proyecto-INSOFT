<?php
session_start();
include("../auth/config.php");
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
        INADI | Contacto
    </title>


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="contacto.css"
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

            <!-- Inicio -->

            <li>

                <a href="../Inicio/inicioindex.php">

                    <i class="fa-solid fa-house"></i>

                    <span>
                        Inicio
                    </span>

                </a>

            </li>

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

            <li>

                <a href="../Cursos/cursosindex.php">

                    <i class="fa-solid fa-book"></i>

                    <span>
                        Cursos
                    </span>

                </a>

            </li>


            <!-- MATRÍCULA -->

            <?php
                if (isset($_SESSION['nombre_usuario']))
                    setMatriculaBtn();
            ?>


            <!-- CONTACTO -->

            <li class="active">

                <a href="contacto.php">

                    <i class="fa-solid fa-envelope"></i>

                    <span>
                        Contacto
                    </span>

                </a>

            </li>


            <!-- GALERÍA -->

            <li>

                <a href="../Galeria/galeria.php">

                    <i class="fa-solid fa-image"></i>

                    <span>
                        Galería
                    </span>

                </a>

            </li>


            <!-- CONVENIOS -->

            <li>

                <a href="../Convenios/convenios.php">

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

            <section class="contact-header">

                <span class="contact-label">
                    INSTITUTO DE INFORMÁTICA
                </span>

                <h1>
                    Contacto
                </h1>

                <p>
                    Estamos para ayudarte. Ponete en contacto con nosotros.
                </p>

            </section>



            <!-- CONTENIDO DE CONTACTO -->

            <section class="contact-container">


                <!-- FORMULARIO -->

                <div class="contact-form-card">

                    <div class="section-heading">

                        <div class="section-icon">

                            <i class="fa-solid fa-envelope"></i>

                        </div>

                        <div>

                            <span>
                                ESCRIBINOS
                            </span>

                            <h2>
                                Contactanos
                            </h2>

                        </div>

                    </div>


                    <form
                        action="#"
                        method="POST"
                        class="contact-form"
                    >


                        <!-- NOMBRE -->

                        <div class="form-group">

                            <label for="nombre">
                                Nombre
                            </label>

                            <div class="input-wrapper">

                                <i class="fa-regular fa-user"></i>

                                <input
                                    type="text"
                                    id="nombre"
                                    name="nombre"
                                    placeholder="Ingresá tu nombre"
                                    required
                                >

                            </div>

                        </div>


                        <!-- CORREO -->

                        <div class="form-group">

                            <label for="correo">
                                Correo Electrónico
                            </label>

                            <div class="input-wrapper">

                                <i class="fa-regular fa-envelope"></i>

                                <input
                                    type="email"
                                    id="correo"
                                    name="correo"
                                    placeholder="Ingresá tu correo electrónico"
                                    required
                                >

                            </div>

                        </div>


                        <!-- MENSAJE -->

                        <div class="form-group">

                            <label for="mensaje">
                                Mensaje
                            </label>

                            <div class="input-wrapper textarea-wrapper">

                                <i class="fa-regular fa-comment"></i>

                                <textarea
                                    id="mensaje"
                                    name="mensaje"
                                    placeholder="Escribí tu mensaje..."
                                    rows="6"
                                    required
                                ></textarea>

                            </div>

                        </div>


                        <!-- BOTÓN -->

                        <button
                            type="submit"
                            class="submit-button"
                        >

                            Enviar mensaje

                            <i class="fa-solid fa-paper-plane"></i>

                        </button>

                    </form>

                </div>



                <!-- INFORMACIÓN DE CONTACTO -->

                <div class="contact-info-card">


                    <!-- LOGO -->

                    <div class="contact-logo">

                        <img
                            src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                            alt="Logo de INADI"
                        >

                    </div>


                    <!-- ESTEMOS CONECTADOS -->

                    <div class="info-section">

                        <span class="info-label">
                            ESTEMOS CONECTADOS
                        </span>

                        <h2>
                            Encontranos
                        </h2>

                        <div class="info-item">

                            <div class="info-icon">

                                <i class="fa-solid fa-location-dot"></i>

                            </div>

                            <div>

                                <strong>
                                    Dirección
                                </strong>

                                <p>
                                    Artigas 827<br>
                                    Salto, Uruguay.
                                </p>

                            </div>

                        </div>


                        <div class="info-item">

                            <div class="info-icon">

                                <i class="fa-solid fa-phone"></i>

                            </div>

                            <div>

                                <strong>
                                    Teléfono
                                </strong>

                                <p>
                                    473 31609
                                </p>

                            </div>

                        </div>


                        <div class="info-item">

                            <div class="info-icon">

                                <i class="fa-solid fa-envelope"></i>

                            </div>

                            <div>

                                <strong>
                                    Correo electrónico
                                </strong>

                                <p>
                                    inadi@inadi.edu.uy
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- REDES -->

                    <div class="social-section">

                        <span class="info-label">
                            ESTEMOS CONECTADOS
                        </span>

                        <div class="social-icons">

                            <a
                                href="#"
                                title="Facebook"
                            >

                                <i class="fa-brands fa-facebook-f"></i>

                            </a>

                            <a
                                href="#"
                                title="Instagram"
                            >

                                <i class="fa-brands fa-instagram"></i>

                            </a>

                            <a
                                href="#"
                                title="WhatsApp"
                            >

                                <i class="fa-brands fa-whatsapp"></i>

                            </a>

                        </div>

                    </div>

                </div>

            </section>



            <!-- MAPA -->

            <section class="map-section">

                <div class="section-heading">

                    <div class="section-icon">

                        <i class="fa-solid fa-location-dot"></i>

                    </div>

                    <div>

                        <span>
                            NUESTRA UBICACIÓN
                        </span>

                        <h2>
                            Encontranos
                        </h2>

                    </div>

                </div>


                <div class="map-container">

                    <iframe
                        src="https://www.google.com/maps?q=Artigas+827,+Salto,+Uruguay&output=embed"
                        width="100%"
                        height="350"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>

                </div>

            </section>



            <!-- INFORMACIÓN FINAL -->

            <section class="contact-final">

                <div class="final-icon">

                    <i class="fa-solid fa-headset"></i>

                </div>

                <div>

                    <h2>
                        ¿Necesitás comunicarte con nosotros?
                    </h2>

                    <p>
                        Podés visitarnos en Artigas 827, Salto,
                        comunicarte telefónicamente al 473 31609
                        o enviarnos un correo electrónico a
                        inadi@inadi.edu.uy.
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
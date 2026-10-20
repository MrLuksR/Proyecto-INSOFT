
<?php
include("../auth/config.php");

session_start();

$nombreUsuario = "Usuario";
$anioActual = date("Y");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#292965">

    <title>INADI | Contacto</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="contacto.css?v=4">
</head>

<body>
<div class="page">

    <!-- BARRA SUPERIOR -->
    <header class="topbar">

        <div class="topbar-left">
            <a href="../Inicio/inicioindex.php" class="top-logo">
                <img src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                     alt="Logo INADI">
                <span>INADI</span>
            </a>

            <div class="news">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>NOTICIAS</span>
            </div>
        </div>

        <!-- Buscador visual -->
        <form class="top-search" role="search"
              action="../Inicio/inicioindex.php" method="get">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" name="buscar"
                   placeholder="Buscar en INADI..."
                   aria-label="Buscar en INADI">
            <button type="submit" aria-label="Buscar">Buscar</button>
        </form>

        <div class="topbar-right">

            <a class="portal-button"
               href="../Estudiante/portalEstudiante.php">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Portal estudiante</span>
            </a>

            <a class="portal-button portal-docente"
               href="../login/loginn.php">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span>Portal docente</span>
            </a>

            <div class="language" aria-label="Idioma español">
                <i class="fa-solid fa-globe"></i>
                <span>ES</span>
            </div>

            <div class="login-area">
                <?php setLogin(); ?>
            </div>

        </div>
    </header>

    <!-- BARRA LATERAL -->
    <aside class="sidebar">

        <button type="button" class="sidebar-toggle"
                id="sidebarToggle"
                aria-label="Contraer o expandir menú"
                aria-expanded="true">
            <i class="fa-solid fa-angles-left"></i>
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

            <?php
            if (isset($_SESSION['nombre_usuario'])) {
                setMatriculaBtn();
            }
            ?>

            <li class="active">
                <a href="contacto.php" aria-current="page">
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

    <!-- CONTENIDO -->
    <div class="content">

        <main class="main">

            <section class="contact-header">
                <span class="contact-label">INSTITUTO DE INFORMÁTICA</span>
                <h1>Hablemos</h1>
                <p>
                    Estamos para ayudarte. Escribinos y te responderemos
                    lo antes posible.
                </p>
                <div class="header-decoration">
                    <i class="fa-solid fa-comments"></i>
                </div>
            </section>

            <section class="contact-container">

                <!-- FORMULARIO -->
                <div class="contact-form-card">

                    <div class="section-heading">
                        <div class="section-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <span class="heading-kicker">ESCRIBINOS</span>
                            <h2>Contactanos</h2>
                            <p>Completá los campos para comunicarte con INADI.</p>
                        </div>
                    </div>

                    <form action="#" method="post" class="contact-form">

                        <div class="form-group">
                            <label for="nombre">Nombre completo</label>
                            <div class="input-wrapper">
                                <i class="fa-regular fa-user"></i>
                                <input type="text" id="nombre" name="nombre"
                                       placeholder="Ingresá tu nombre"
                                       autocomplete="name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="correo">Correo electrónico</label>
                            <div class="input-wrapper">
                                <i class="fa-regular fa-envelope"></i>
                                <input type="email" id="correo" name="correo"
                                       placeholder="nombre@correo.com"
                                       autocomplete="email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mensaje">¿En qué podemos ayudarte?</label>
                            <div class="input-wrapper textarea-wrapper">
                                <i class="fa-regular fa-comment"></i>
                                <textarea id="mensaje" name="mensaje"
                                          placeholder="Escribí tu mensaje..."
                                          rows="6" required></textarea>
                            </div>
                        </div>

                        <button type="submit" class="submit-button">
                            <span>Enviar mensaje</span>
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>

                        <p class="form-note">
                            <i class="fa-solid fa-lock"></i>
                            Usá un correo válido para que podamos responderte.
                        </p>
                    </form>
                </div>

                <!-- DATOS DE CONTACTO -->
                <aside class="contact-info-card">

                    <div class="contact-logo">
                        <img src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                             alt="INADI Instituto de Informática">
                    </div>

                    <span class="info-label">ESTEMOS CONECTADOS</span>
                    <h2>Encontranos</h2>
                    <p class="info-intro">
                        Estamos en Salto para acompañarte en tu formación.
                    </p>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <strong>Dirección</strong>
                            <p>Artigas 827<br>Salto, Uruguay</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <strong>Teléfono</strong>
                            <p><a href="tel:+59847331609">473 31609</a></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <strong>Correo electrónico</strong>
                            <p>
                                <a href="mailto:inadi@inadi.edu.uy">
                                    inadi@inadi.edu.uy
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="social-section">
                        <span class="info-label">SEGUINOS</span>
                        <div class="social-icons">
                            <a href="#" title="Facebook" aria-label="Facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                            <a href="#" title="Instagram" aria-label="Instagram">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                            <a href="https://wa.me/59847331609"
                               title="WhatsApp" aria-label="WhatsApp"
                               target="_blank" rel="noopener noreferrer">
                                <i class="fa-brands fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </aside>
            </section>

            <!-- MAPA -->
            <section class="map-section">
                <div class="section-heading">
                    <div class="section-icon">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div>
                        <span class="heading-kicker">CÓMO LLEGAR</span>
                        <h2>Nuestra ubicación</h2>
                        <p>Encontrá INADI en Salto, Uruguay.</p>
                    </div>
                </div>

                <div class="map-container">
                    <iframe
                        src="https://www.google.com/maps?q=Artigas%20827%2C%20Salto%2C%20Uruguay&output=embed"
                        title="Mapa de ubicación de INADI en Artigas 827, Salto"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen>
                    </iframe>
                </div>

                <a class="map-link"
                   href="https://www.google.com/maps/search/?api=1&query=Artigas+827%2C+Salto%2C+Uruguay"
                   target="_blank" rel="noopener noreferrer">
                    <i class="fa-solid fa-diamond-turn-right"></i>
                    Abrir indicaciones en Google Maps
                </a>
            </section>

            <!-- CIERRE -->
            <section class="contact-final">
                <div class="final-icon">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <h2>¿Necesitás comunicarte con nosotros?</h2>
                    <p>
                        Podés visitarnos en Artigas 827, Salto, llamarnos al
                        473 31609 o escribir a inadi@inadi.edu.uy.
                    </p>
                </div>
            </section>

        </main>

        <!-- FOOTER: DENTRO DE .content PARA RESPETAR LA BARRA LATERAL -->
        <footer class="footer">
            <div class="footer-container">

                <div class="footer-column footer-brand">
                    <div class="footer-logo">
                        <img src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                             alt="Logo INADI">
                        <span>INADI</span>
                    </div>
                    <p>
                        Instituto de Informática. Formación, tecnología e
                        innovación en Salto, Uruguay.
                    </p>
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
                    <p>
                        <i class="fa-solid fa-envelope"></i>
                        inadi@inadi.edu.uy
                    </p>
                </div>

                <div class="footer-column">
                    <h3>ENLACES RÁPIDOS</h3>
                    <ul>
                        <li><a href="../Inicio/inicioindex.php">Inicio</a></li>
                        <li><a href="../Nosotros/nosotros.php">Nosotros</a></li>
                        <li><a href="../Cursos/cursosindex.php">Cursos</a></li>
                        <li><a href="contacto.php">Contacto</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>© <?php echo $anioActual; ?> INADI — Instituto de Informática</span>
                <span>Salto, Uruguay</span>
            </div>
        </footer>
    </div>

    <!-- WHATSAPP FLOTANTE -->
    <a href="https://wa.me/59847331609"
       class="whatsapp"
       title="Contactar por WhatsApp"
       aria-label="Contactar por WhatsApp"
       target="_blank" rel="noopener noreferrer">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("sidebarToggle");
    const icon = toggle ? toggle.querySelector("i") : null;

    if (!toggle) return;

    toggle.addEventListener("click", function () {
        document.body.classList.toggle("sidebar-collapsed");

        const collapsed = document.body.classList.contains("sidebar-collapsed");
        toggle.setAttribute("aria-expanded", String(!collapsed));

        if (icon) {
            icon.classList.toggle("fa-angles-left", !collapsed);
            icon.classList.toggle("fa-angles-right", collapsed);
        }
    });
});
</script>

</body>
</html>

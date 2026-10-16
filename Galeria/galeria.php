
<?php
include("../auth/config.php");
include("../database/Clases/Estudiante.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nombreUsuario = $_SESSION['nombre_usuario'] ?? 'Usuario';
$anioActual = date("Y");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI | Galería</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- ESTILOS COMPARTIDOS CON NOSOTROS -->
    <link rel="stylesheet" href="../Inicio/CSS/stylesinicio.css?v=8">

    <!-- ESTILOS EXCLUSIVOS DE GALERÍA -->
    <link rel="stylesheet" href="galeria.css?v=5">
</head>

<body>
<div class="page">

    <!--BARRA SUPERIOR -->
    <header class="topbar">

        <div class="topbar-left">
            <a href="../Inicio/inicioindex.php" class="top-logo">
                <img
                    src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                    alt="Logo INADI"
                >
                <span>INADI</span>
            </a>

            <div class="news">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>NOTICIAS</span>
            </div>
        </div>

        <!-- BUSCADOR -->
        <div class="top-search">
            <form action="../Cursos/cursosindex.php" method="GET">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    type="search"
                    name="buscar"
                    placeholder="Buscar información..."
                    aria-label="Buscar información"
                >
                <button type="submit">Buscar</button>
            </form>
        </div>

        <!-- ACCESOS -->
        <div class="topbar-right">
            <a
                href="../Estudiante/portalEstudiante.php"
                class="portal-button portal-estudiante"
            >
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Portal Estudiantes</span>
            </a>

            <span class="language">
                <i class="fa-solid fa-globe"></i>
                ES
            </span>

            <?php setLogin(); ?>
        </div>
    </header>


    <!--  BARRA LATERAL: IGUAL A NOSOTROS -->
    <aside class="sidebar" aria-label="Menú principal">

        <button
            class="sidebar-toggle"
            id="sidebarToggle"
            type="button"
            aria-label="Contraer menú"
            aria-expanded="true"
            title="Contraer menú"
        >
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

            <!-- OPCIONES DEL ESTUDIANTE REGISTRADO -->
            <?php if (isset($_SESSION['nombre_usuario'])): ?>

                <li class="student-menu-heading">
                    <span class="student-avatar">
                        <i class="fa-solid fa-user-graduate"></i>
                    </span>

                    <span class="student-welcome">
                        <strong>
                            <?php
                            $nombreCompleto = trim(
                                ($_SESSION['nombre'] ?? '') . ' ' .
                                ($_SESSION['apellido'] ?? '')
                            );

                            echo htmlspecialchars(
                                $nombreCompleto !== ''
                                    ? $nombreCompleto
                                    : $_SESSION['nombre_usuario'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </strong>
                        <small>Estudiante registrado</small>
                    </span>
                </li>

                <li>
                    <a href="../Estudiante/portalEstudiante.php">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Mi portal</span>
                    </a>
                </li>

                <li>
                    <a href="../Matricula/matricula.php">
                        <i class="fa-solid fa-file-signature"></i>
                        <span>Mis matrículas</span>
                    </a>
                </li>

            <?php endif; ?>

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

            <li class="active">
                <a href="galeria.php" aria-current="page">
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
                <a href="../Empresas/empresas.php">
                    <i class="fa-solid fa-city"></i>
                    <span>Empresas</span>
                </a>
            </li>

        </ul>

        <div class="sidebar-bottom">
            © INADI
            <?php echo htmlspecialchars($anioActual, ENT_QUOTES, 'UTF-8'); ?>
            <br>
            Instituto de Informática
            <br>
            Salto - Uruguay
        </div>
    </aside>


    <!-- CONTENIDO DE GALERÍA -->
    <div class="content">
        <main class="main">

            <section class="gallery-header">
                <span class="gallery-label">INSTITUTO DE INFORMÁTICA</span>

                <h1>Galería</h1>

                <p>
                    Conocé un poco más sobre INADI y nuestras actividades.
                </p>
            </section>


            <!-- GALERÍA DE IMÁGENES -->
            <section class="gallery-section">

                <div class="section-heading">
                    <div class="section-icon">
                        <i class="fa-solid fa-images"></i>
                    </div>

                    <div>
                        <span>NUESTRAS IMÁGENES</span>
                        <h2>Galería INADI</h2>
                    </div>
                </div>

                <div class="gallery-grid">
                    <?php
                    require_once("../database/consultas/galeria/getGaleria.php");
                    mostrarImagenes($pdo);
                    ?>
                </div>

            </section>


            <!-- CIERRE -->
            <section class="gallery-final">

                <div class="final-icon">
                    <i class="fa-solid fa-camera"></i>
                </div>

                <div>
                    <h2>Momentos de INADI</h2>

                    <p>
                        Conocé nuestras instalaciones, actividades y algunos
                        de los momentos que forman parte de la trayectoria
                        del Instituto de Informática.
                    </p>
                </div>

            </section>

        </main>
    </div>


    <!-- VISOR AMPLIADO (LIGHTBOX) -->
    <div class="lightbox" id="lightbox" aria-hidden="true">

        <button
            class="lightbox-close"
            id="lightboxClose"
            type="button"
            title="Cerrar"
            aria-label="Cerrar imagen"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

        <button
            class="lightbox-prev"
            id="lightboxPrev"
            type="button"
            title="Imagen anterior"
            aria-label="Imagen anterior"
        >
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <img
            src=""
            alt="Imagen ampliada"
            class="lightbox-image"
            id="lightboxImage"
        >

        <button
            class="lightbox-next"
            id="lightboxNext"
            type="button"
            title="Imagen siguiente"
            aria-label="Imagen siguiente"
        >
            <i class="fa-solid fa-chevron-right"></i>
        </button>

    </div>


    <!-- WHATSAPP -->
    <a
        href="https://wa.me/"
        class="whatsapp"
        title="Contactar por WhatsApp"
        aria-label="Contactar por WhatsApp"
        target="_blank"
        rel="noopener noreferrer"
    >
        <i class="fa-brands fa-whatsapp"></i>
    </a>

</div>


<!-- JAVASCRIPT: MENÚ LATERAL Y GALERÍA -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    /* MENÚ LATERAL */
    const sidebar = document.querySelector(".sidebar");
    const page = document.querySelector(".page");
    const toggle = document.getElementById("sidebarToggle");

    if (sidebar && page && toggle) {
        toggle.addEventListener("click", function () {
            const contraida = sidebar.classList.toggle("sidebar-collapsed");

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
    }

    /* LIGHTBOX */
    const galleryItems = Array.from(
        document.querySelectorAll(".gallery-item")
    );

    const lightbox = document.getElementById("lightbox");
    const lightboxImage = document.getElementById("lightboxImage");
    const lightboxClose = document.getElementById("lightboxClose");
    const lightboxPrev = document.getElementById("lightboxPrev");
    const lightboxNext = document.getElementById("lightboxNext");

    if (
        !lightbox ||
        !lightboxImage ||
        !lightboxClose ||
        !lightboxPrev ||
        !lightboxNext
    ) {
        return;
    }

    let currentImage = 0;

    function showImage() {
        if (galleryItems.length === 0) {
            return;
        }

        const image = galleryItems[currentImage].querySelector("img");

        if (!image) {
            return;
        }

        lightboxImage.src = image.src;
        lightboxImage.alt = image.alt || "Imagen ampliada";
    }

    function openLightbox(index) {
        if (galleryItems.length === 0) {
            return;
        }

        currentImage = index;
        showImage();

        lightbox.classList.add("show");
        lightbox.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
        lightboxClose.focus();
    }

    function closeLightbox() {
        lightbox.classList.remove("show");
        lightbox.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
    }

    function previousImage() {
        if (galleryItems.length === 0) {
            return;
        }

        currentImage =
            (currentImage - 1 + galleryItems.length) % galleryItems.length;

        showImage();
    }

    function nextImage() {
        if (galleryItems.length === 0) {
            return;
        }

        currentImage = (currentImage + 1) % galleryItems.length;
        showImage();
    }

    galleryItems.forEach(function (item, index) {
        item.addEventListener("click", function () {
            openLightbox(index);
        });

        item.setAttribute("tabindex", "0");
        item.setAttribute("role", "button");
        item.setAttribute("aria-label", "Ampliar imagen " + (index + 1));

        item.addEventListener("keydown", function (event) {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                openLightbox(index);
            }
        });
    });

    lightboxClose.addEventListener("click", closeLightbox);
    lightboxPrev.addEventListener("click", previousImage);
    lightboxNext.addEventListener("click", nextImage);

    lightbox.addEventListener("click", function (event) {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });

    document.addEventListener("keydown", function (event) {
        if (!lightbox.classList.contains("show")) {
            return;
        }

        if (event.key === "Escape") {
            closeLightbox();
        } else if (event.key === "ArrowLeft") {
            previousImage();
        } else if (event.key === "ArrowRight") {
            nextImage();
        }
    });

});
</script>

</body>
</html>

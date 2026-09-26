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
        INADI | Galería
    </title>


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="galeria.css"
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

            <li>

                <a href="../Cursos/cursosindex.php">

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

                <a href="../Contacto/contacto.php">

                    <i class="fa-solid fa-envelope"></i>

                    <span>
                        Contacto
                    </span>

                </a>

            </li>


            <!-- GALERÍA -->

            <li class="active">

                <a href="galeria.php">

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

            <section class="gallery-header">

                <span class="gallery-label">
                    INSTITUTO DE INFORMÁTICA
                </span>

                <h1>
                    Galería
                </h1>

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

                        <span>
                            NUESTRAS IMÁGENES
                        </span>

                        <h2>
                            Galería INADI
                        </h2>

                    </div>

                </div>



                <div class="gallery-grid">


                    <!-- IMAGEN 1 -->

                    <div class="gallery-item">

                        <img
                            src="1.jpg"
                            alt="INADI - Imagen 1"
                        >

                        <div class="gallery-overlay">

                            <i class="fa-solid fa-magnifying-glass-plus"></i>

                            <span>
                                Ver imagen
                            </span>

                        </div>

                    </div>



                    <!-- IMAGEN 2 -->

                    <div class="gallery-item">

                        <img
                            src="2.jpg"
                            alt="INADI - Imagen 2"
                        >

                        <div class="gallery-overlay">

                            <i class="fa-solid fa-magnifying-glass-plus"></i>

                            <span>
                                Ver imagen
                            </span>

                        </div>

                    </div>



                    <!-- IMAGEN 3 -->

                    <div class="gallery-item">

                        <img
                            src="3.jpg"
                            alt="INADI - Imagen 3"
                        >

                        <div class="gallery-overlay">

                            <i class="fa-solid fa-magnifying-glass-plus"></i>

                            <span>
                                Ver imagen
                            </span>

                        </div>

                    </div>



                    <!-- IMAGEN 4 -->

                    <div class="gallery-item">

                        <img
                            src="4.jpg"
                            alt="INADI - Imagen 4"
                        >

                        <div class="gallery-overlay">

                            <i class="fa-solid fa-magnifying-glass-plus"></i>

                            <span>
                                Ver imagen
                            </span>

                        </div>

                    </div>



                    <!-- IMAGEN 5 -->

                    <div class="gallery-item">

                        <img
                            src="5.jpg"
                            alt="INADI - Imagen 5"
                        >

                        <div class="gallery-overlay">

                            <i class="fa-solid fa-magnifying-glass-plus"></i>

                            <span>
                                Ver imagen
                            </span>

                        </div>

                    </div>



                    <!-- IMAGEN 6 -->

                    <div class="gallery-item">

                        <img
                            src="6.jpg"
                            alt="INADI - Imagen 6"
                        >

                        <div class="gallery-overlay">

                            <i class="fa-solid fa-magnifying-glass-plus"></i>

                            <span>
                                Ver imagen
                            </span>

                        </div>

                    </div>

                </div>

            </section>



            <!-- CIERRE -->

            <section class="gallery-final">

                <div class="final-icon">

                    <i class="fa-solid fa-camera"></i>

                </div>

                <div>

                    <h2>
                        Momentos de INADI
                    </h2>

                    <p>
                        Conocé nuestras instalaciones, actividades
                        y algunos de los momentos que forman parte
                        de la trayectoria del Instituto de Informática.
                    </p>

                </div>

            </section>


        </main>

    </div>



    <!-- VISOR DE IMAGEN -->

    <div
        class="lightbox"
        id="lightbox"
    >

        <button
            class="lightbox-close"
            id="lightboxClose"
            title="Cerrar"
        >

            <i class="fa-solid fa-xmark"></i>

        </button>


        <button
            class="lightbox-prev"
            id="lightboxPrev"
            title="Imagen anterior"
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
            title="Imagen siguiente"
        >

            <i class="fa-solid fa-chevron-right"></i>

        </button>

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


<!-- JAVASCRIPT GALERÍA -->

<script>

    const galleryItems = document.querySelectorAll(".gallery-item");

    const lightbox = document.getElementById("lightbox");

    const lightboxImage = document.getElementById("lightboxImage");

    const lightboxClose = document.getElementById("lightboxClose");

    const lightboxPrev = document.getElementById("lightboxPrev");

    const lightboxNext = document.getElementById("lightboxNext");


    let currentImage = 0;


    // ABRIR IMAGEN

    galleryItems.forEach((item, index) => {

        item.addEventListener("click", () => {

            currentImage = index;

            showImage();

            lightbox.classList.add("show");

            document.body.style.overflow = "hidden";

        });

    });


    // MOSTRAR IMAGEN

    function showImage() {

        const image = galleryItems[currentImage].querySelector("img");

        lightboxImage.src = image.src;

        lightboxImage.alt = image.alt;

    }


    // CERRAR

    function closeLightbox() {

        lightbox.classList.remove("show");

        document.body.style.overflow = "";

    }


    lightboxClose.addEventListener(
        "click",
        closeLightbox
    );


    // IMAGEN ANTERIOR

    lightboxPrev.addEventListener("click", (event) => {

        event.stopPropagation();

        currentImage--;

        if (currentImage < 0) {

            currentImage = galleryItems.length - 1;

        }

        showImage();

    });


    // IMAGEN SIGUIENTE

    lightboxNext.addEventListener("click", (event) => {

        event.stopPropagation();

        currentImage++;

        if (currentImage >= galleryItems.length) {

            currentImage = 0;

        }

        showImage();

    });


    // CERRAR HACIENDO CLICK FUERA

    lightbox.addEventListener("click", (event) => {

        if (event.target === lightbox) {

            closeLightbox();

        }

    });


    // TECLADO

    document.addEventListener("keydown", (event) => {

        if (!lightbox.classList.contains("show")) {

            return;

        }


        if (event.key === "Escape") {

            closeLightbox();

        }


        if (event.key === "ArrowLeft") {

            currentImage--;

            if (currentImage < 0) {

                currentImage = galleryItems.length - 1;

            }

            showImage();

        }


        if (event.key === "ArrowRight") {

            currentImage++;

            if (currentImage >= galleryItems.length) {

                currentImage = 0;

            }

            showImage();

        }

    });

</script>

</body>

</html>
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
        INADI | Sobre Nosotros
    </title>


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="nosotros.css"
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

            <li class="active">

                <a href="nosotros.php">

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

            <section class="about-header">

                <div>

                    <span class="about-label">
                        INSTITUTO DE INFORMÁTICA
                    </span>

                    <h1>
                        Sobre <strong>INADI</strong>
                    </h1>

                    <p>
                        Conozca sobre nosotros y nuestra trayectoria.
                    </p>

                </div>

            </section>



            <!-- PRESENTACIÓN -->

            <section class="about-intro">

                <div class="about-image">

                    <img
                        src="https://inadi.edu.uy/wp-content/uploads/2018/09/FOTO-PRINCIPAL-199x300.jpg"
                        alt="INADI - Instituto de Informática"
                    >

                </div>


                <div class="about-text">

                    <h2>
                        Nuestra Misión
                    </h2>

                    <p>
                        Formar y perfeccionar a la sociedad salteña,
                        brindando educación a todos los grupos etáreos
                        y de diferentes clases sociales, para el logro
                        del crecimiento intelectual cubriendo así todas
                        las necesidades de todas las edades, niños,
                        adolescentes y adultos.
                    </p>

                    <p>
                        Educar impartiendo bases y conocimientos de
                        informática, teniendo como eje educacional
                        nuestra Institución el de formar seres capaces
                        de desenvolverse en el mundo actual que es el
                        mundo de la informática, donde la información
                        es la base de los futuros conocimientos,
                        generando la posibilidad de triunfar donde la
                        computación es una herramienta indispensable
                        para el desarrollo de los individuos.
                    </p>

                    <p>
                        Todo esto se logra ofreciendo un ambiente
                        familiar en constante actualización con
                        capacitación brindada por profesionales
                        altamente calificados, nos avala nuestra
                        trayectoria ininterrumpida con un excelente
                        equipo de trabajo y con una vocación que nos
                        caracteriza y es reconocida desde hace ya
                        34 años, donde miles de alumnos han pasado
                        por nuestras aulas.
                    </p>

                </div>

            </section>



            <!--VISIÓN -->

            <section class="about-section">

                <div class="section-heading">

                    <div class="section-icon">

                        <i class="fa-solid fa-eye"></i>

                    </div>

                    <div>

                        <span>
                            NUESTRO PROPÓSITO
                        </span>

                        <h2>
                            Nuestra Visión
                        </h2>

                    </div>

                </div>


                <div class="section-content">

                    <p>
                        Ser un Instituto orientado a la formación
                        integral de personas de todas las edades según
                        sus requerimientos, lo que implica formación
                        en valores que genera seres independientes y
                        autónomos, capaces de desarrollarse plenamente.
                    </p>

                    <p>
                        Nos comprometemos a seguir perfeccionándonos
                        para seguir siendo elegidos y continuar
                        capacitando a diferentes generaciones al momento
                        de su formación.
                    </p>

                    <p>
                        Los cursos de mayor demanda que se dictan son:
                        <strong>Operador PC, Operador PC avanzado,
                        Diseño Gráfico, Diseño y Programación Web.</strong>
                    </p>

                    <a
                        href="https://inadi.edu.uy/nuestros-cursos"
                        target="_blank"
                        class="courses-link"
                    >
                        Conocer nuestros cursos
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>

                </div>

            </section>



            <!-- EMPRESAS Y ENTES -->

            <section class="about-section">

                <div class="section-heading">

                    <div class="section-icon">

                        <i class="fa-solid fa-handshake"></i>

                    </div>

                    <div>

                        <span>
                            TRAYECTORIA
                        </span>

                        <h2>
                            Algunas empresas y entes del Estado que confiaron en nosotros
                        </h2>

                    </div>

                </div>


                <div class="institution-list">

                    <div class="institution-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Personal administrativo del Centro Comercial e Industrial de Salto.
                        </span>

                    </div>


                    <div class="institution-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Maestros para postularse como encargados del área de Informática en diversas Escuelas del Departamento – Salto.
                        </span>

                    </div>


                    <div class="institution-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Personal de Brigada de Infantería N.º 3 – Salto.
                        </span>

                    </div>


                    <div class="institution-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Funcionarios del M.T.O.P. – Salto.
                        </span>

                    </div>


                    <div class="institution-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Alumnos del interior del departamento. Curso dictado con el apoyo del Proyecto Prodenor de Mevir y la Unión Europea.
                        </span>

                    </div>


                    <div class="institution-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Personal de las empresas ASISPER y REQUIEM.
                        </span>

                    </div>


                    <div class="institution-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Personal de empresa El Revoltijo.
                        </span>

                    </div>


                    <div class="institution-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Funcionarios del Club Remeros de Salto.
                        </span>

                    </div>


                    <div class="institution-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Funcionarios del B.H.U. (Banco Hipotecario del Uruguay) de Salto.
                        </span>

                    </div>


                    <div class="institution-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Funcionarios de la Comisión Técnico Mixta de Salto Grande (C.T.M.).
                        </span>

                    </div>


                    <div class="institution-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Personal del Instituto Nacional de Investigación Agropecuaria (I.N.I.A. – Salto).
                        </span>

                    </div>


                    <div class="institution-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Funcionarios de la Junta Departamental de Salto.
                        </span>

                    </div>


                    <div class="institution-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Personal Administrativo del Frigorífico Tacuarembó.
                        </span>

                    </div>


                    <div class="institution-item">

                        <i class="fa-solid fa-check"></i>

                        <span>
                            Personal de TATA supermercados, sucursal centro.
                        </span>

                    </div>

                </div>

            </section>



            <!-- CONVENIOS -->

            <section class="about-section">

                <div class="section-heading">

                    <div class="section-icon">

                        <i class="fa-solid fa-building-columns"></i>

                    </div>

                    <div>

                        <span>
                            VÍNCULOS INSTITUCIONALES
                        </span>

                        <h2>
                            Convenios que tiene la institución
                        </h2>

                    </div>

                </div>


                <div class="agreements">

                    <div class="agreement-card">

                        <i class="fa-solid fa-school"></i>

                        <span>
                            Convenio con Instituto Artigas Washington – Salto.
                        </span>

                    </div>


                    <div class="agreement-card">

                        <i class="fa-solid fa-store"></i>

                        <span>
                            Convenio con Centro Comercial e Industrial de Salto.
                        </span>

                    </div>


                    <div class="agreement-card">

                        <i class="fa-solid fa-people-group"></i>

                        <span>
                            Convenio con SUPU, Sindicato Único de Policías del Uruguay.
                        </span>

                    </div>

                </div>

            </section>



            <!-- CIERRE -->

            <section class="about-final">

                <div class="final-icon">

                    <i class="fa-solid fa-award"></i>

                </div>

                <div>

                    <h2>
                        Una trayectoria que continúa
                    </h2>

                    <p>
                        Es imposible enumerar todos los logros y
                        reconocimientos obtenidos durante todos estos
                        años. Lo que nos enorgullece es que siempre se
                        trata de estar correctamente trabajando, con
                        los requerimientos que se exigen, educadores
                        altamente calificados, aulas con un parque de
                        computadoras de última generación y un contacto
                        permanente con los padres de los alumnos, para
                        así evitar la deserción que hoy es uno de los
                        graves problemas por los que atraviesa la
                        enseñanza.
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
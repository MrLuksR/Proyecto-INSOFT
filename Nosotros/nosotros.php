
<?php
include("../auth/config.php");
include("../database/Clases/Estudiante.php");
session_start();

session_start();

$nombreUsuario = "Usuario";
$anioActual = date("Y");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI | Sobre Nosotros</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- CSS principal: mismas barras, menú y footer que Inicio -->
    <link rel="stylesheet" href="../Inicio/CSS/stylesinicio.css">

    <!-- CSS específico de la página Nosotros -->
    <link rel="stylesheet" href="nosotros.css?v=8">
</head>

<body>
<div class="page">

    <!-- BARRA SUPERIOR: MISMA ESTRUCTURA QUE INICIO -->
    <header class="topbar">

        <div class="topbar-left">
            <div class="top-logo">
                <img
                    src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                    alt="Logo INADI">
                <span>INADI</span>
            </div>

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
                    aria-label="Buscar información">
                <button type="submit">Buscar</button>
            </form>
        </div>

        <!-- ACCESOS -->
        <div class="topbar-right">

            <?php
                setPortalBtn();
            ?>

            <span class="language">
                <i class="fa-solid fa-globe"></i>
                ES
            </span>

            <?php setLogin(); ?>

        </div>
    </header>

    <!-- BARRA LATERAL: MISMA ESTRUCTURA QUE INICIO -->
    <aside class="sidebar">

        <button
            class="sidebar-toggle"
            id="sidebarToggle"
            type="button"
            aria-label="Contraer menú"
            aria-expanded="true"
            title="Contraer menú">
            <i class="fa-solid fa-angles-left" aria-hidden="true"></i>
        </button>

        <ul class="menu">

            <li>
                <a href="../Inicio/inicioindex.php">
                    <i class="fa-solid fa-house"></i>
                    <span>Inicio</span>
                </a>
            </li>

            <li class="active">
                <a href="../Nosotros/nosotros.php" aria-current="page">
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

            <!-- MENÚ PERSONAL DEL ESTUDIANTE -->
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

    <!-- CONTENIDO PRINCIPAL -->
    <div class="content">
        <main class="main">

            <!-- ENCABEZADO -->
            <section class="about-header">
                <span class="about-label">INSTITUTO DE INFORMÁTICA</span>
                <h1>Sobre <strong>INADI</strong></h1>
                <p>Conocé nuestra institución, nuestra historia y trayectoria.</p>
            </section>

            <!-- MISIÓN Y PRESENTACIÓN -->
            <section class="about-intro">

                <div class="about-image">
                    <img
                        src="https://inadi.edu.uy/wp-content/uploads/2018/09/FOTO-PRINCIPAL-199x300.jpg"
                        alt="Instituto INADI">
                </div>

                <div class="about-text">
                    <h2>Nuestra misión</h2>

                    <p>
                        Formar y perfeccionar a la sociedad salteña,
                        brindando educación a todos los grupos etáreos
                        y de diferentes clases sociales, para el logro
                        del crecimiento intelectual, cubriendo las
                        necesidades de niños, adolescentes y adultos.
                    </p>

                    <p>
                        Educar impartiendo bases y conocimientos de
                        informática, teniendo como eje educacional
                        formar personas capaces de desenvolverse en
                        el mundo actual, donde la información es la
                        base de los futuros conocimientos y la
                        computación constituye una herramienta
                        indispensable para el desarrollo individual.
                    </p>

                    <p>
                        Esto se logra ofreciendo un ambiente familiar,
                        en constante actualización, con capacitación
                        brindada por profesionales calificados. Nos
                        avala una trayectoria ininterrumpida y un
                        excelente equipo de trabajo, con una vocación
                        reconocida desde hace ya
                        <?php setTray(); ?>
                        años, durante los cuales miles de alumnos han
                        pasado por nuestras aulas.
                    </p>
                </div>

            </section>

            <!-- VISIÓN -->
            <section class="about-section">

                <div class="section-heading">
                    <div class="section-icon">
                        <i class="fa-solid fa-eye"></i>
                    </div>

                    <div>
                        <span>NUESTRO PROPÓSITO</span>
                        <h2>Nuestra visión</h2>
                    </div>
                </div>

                <div class="section-content">
                    <p>
                        Ser un instituto orientado a la formación
                        integral de personas de todas las edades,
                        según sus requerimientos. Esto implica una
                        formación en valores que genere personas
                        independientes y autónomas, capaces de
                        desarrollarse plenamente.
                    </p>

                    <p>
                        Nos comprometemos a seguir perfeccionándonos
                        para continuar siendo elegidos y capacitando
                        a diferentes generaciones durante su formación.
                    </p>

                    <p>
                        Entre los cursos de mayor demanda se encuentran:
                        <strong>
                            Operador PC, Operador PC avanzado,
                            Diseño Gráfico, Diseño y Programación Web.
                        </strong>
                    </p>

                    <a href="../Cursos/cursosindex.php" class="courses-link">
                        Conocer nuestros cursos
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

            </section>

            <!-- EMPRESAS E INSTITUCIONES -->
            <section class="about-section">

                <div class="section-heading">
                    <div class="section-icon">
                        <i class="fa-solid fa-handshake"></i>
                    </div>

                    <div>
                        <span>TRAYECTORIA</span>
                        <h2>Empresas y entes del Estado que confiaron en nosotros</h2>
                    </div>
                </div>

                <div class="institution-list">

                    <div class="institution-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Personal administrativo del Centro Comercial e Industrial de Salto.</span>
                    </div>

                    <div class="institution-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Maestros que se postularon como encargados del área de Informática en escuelas del departamento de Salto.</span>
                    </div>

                    <div class="institution-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Personal de Brigada de Infantería N.º 3, Salto.</span>
                    </div>

                    <div class="institution-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Funcionarios del M.T.O.P., Salto.</span>
                    </div>

                    <div class="institution-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Alumnos del interior del departamento, en cursos con apoyo del Proyecto Prodenor de Mevir y la Unión Europea.</span>
                    </div>

                    <div class="institution-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Personal de las empresas ASISPER y REQUIEM.</span>
                    </div>

                    <div class="institution-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Personal de la empresa El Revoltijo.</span>
                    </div>

                    <div class="institution-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Funcionarios del Club Remeros de Salto.</span>
                    </div>

                    <div class="institution-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Funcionarios del B.H.U. (Banco Hipotecario del Uruguay), Salto.</span>
                    </div>

                    <div class="institution-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Funcionarios de la Comisión Técnico Mixta de Salto Grande (C.T.M.).</span>
                    </div>

                    <div class="institution-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Personal del Instituto Nacional de Investigación Agropecuaria (I.N.I.A.), Salto.</span>
                    </div>

                    <div class="institution-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Funcionarios de la Junta Departamental de Salto.</span>
                    </div>

                    <div class="institution-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Personal administrativo del Frigorífico Tacuarembó.</span>
                    </div>

                    <div class="institution-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Personal de TATA supermercados, sucursal centro.</span>
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
                        <span>VÍNCULOS INSTITUCIONALES</span>
                        <h2>Convenios de la institución</h2>
                    </div>
                </div>

                <div class="agreements">

                    <div class="agreement-card">
                        <div class="agreement-icon">
                            <i class="fa-solid fa-school"></i>
                        </div>
                        <span>Convenio con Instituto Artigas Washington, Salto.</span>
                    </div>

                    <div class="agreement-card">
                        <div class="agreement-icon">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <span>Convenio con Centro Comercial e Industrial de Salto.</span>
                    </div>

                    <div class="agreement-card">
                        <div class="agreement-icon">
                            <i class="fa-solid fa-people-group"></i>
                        </div>
                        <span>Convenio con SUPU, Sindicato Único de Policías del Uruguay.</span>
                    </div>

                </div>
            </section>

            <!-- CIERRE -->
            <section class="about-final">

                <div class="final-icon">
                    <i class="fa-solid fa-award"></i>
                </div>

                <div>
                    <span class="final-label">NUESTRO COMPROMISO</span>
                    <h2>Una trayectoria que continúa</h2>

                    <p>
                        Es imposible enumerar todos los logros y
                        reconocimientos obtenidos durante estos años.
                        Nos enorgullece trabajar con los requerimientos
                        necesarios, educadores altamente calificados,
                        aulas equipadas con computadoras y contacto
                        permanente con las familias de nuestros alumnos.
                        Seguimos comprometidos con la educación y la
                        formación informática de nuestra comunidad.
                    </p>
                </div>

            </section>

        </main>
    </div>

    <!-- FOOTER: MISMA ESTRUCTURA QUE INICIO -->
    <footer>
        <div class="footer-content">

            <div class="footer-brand">
                <div class="footer-brand-title">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>INADI</span>
                </div>

                <p class="footer-description">
                    Instituto de Informática de Salto.
                </p>

                <p class="footer-description">Desde 1992.</p>
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
            </div>

            <div class="footer-column">
                <h3>HORARIO</h3>
                <p>Lunes - Viernes: 08:00 - 20:00</p>
                <p>Sábados: 08:00 - 13:00</p>
            </div>

            <div class="footer-column">
                <h3>REDES SOCIALES</h3>

                <p>
                    <i class="fa-brands fa-facebook"></i>
                    Facebook
                </p>

                <p>
                    <i class="fa-brands fa-instagram"></i>
                    Instagram
                </p>
            </div>

        </div>
    </footer>

    <!-- WHATSAPP -->
    <div class="whatsapp">
        <i class="fa-brands fa-whatsapp"></i>
    </div>

</div>

<!-- SCRIPT DE LA BARRA LATERAL: MISMO COMPORTAMIENTO QUE INICIO -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector(".sidebar");
    const toggle = document.getElementById("sidebarToggle");
    const page = document.querySelector(".page");

    if (!sidebar || !toggle || !page) {
        console.error("No se encontró la barra lateral o su botón.");
        return;
    }

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
});
</script>

</body>
</html>

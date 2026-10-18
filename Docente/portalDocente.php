
<?php
session_start();

/*PORTAL DOCENTE - INADI*/

// Si tu sistema guarda el nombre del usuario en la sesión,
// se utilizará para mostrarlo en el portal.
$nombreUsuario = $_SESSION['nombre_usuario']
    ?? $_SESSION['nombre']
    ?? 'Docente';

$nombreUsuario = htmlspecialchars(
    (string) $nombreUsuario,
    ENT_QUOTES,
    'UTF-8'
);

// Obtener iniciales para el avatar.
$iniciales = 'D';

$partesNombre = preg_split('/\s+/', trim($nombreUsuario));

if (!empty($partesNombre[0])) {
    $iniciales = mb_substr($partesNombre[0], 0, 1, 'UTF-8');

    if (!empty($partesNombre[1])) {
        $iniciales .= mb_substr(
            $partesNombre[1],
            0,
            1,
            'UTF-8'
        );
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI | Portal Docente</title>

    <link rel="stylesheet" href="portalDocente.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >
</head>

<body>

<div class="portal-shell">

    <!-- BARRA LATERAL -->

    <aside class="sidebar">

        <div class="brand">

            <button
                type="button"
                class="icon-button sidebar-collapse"
                aria-label="Contraer menú"
                aria-expanded="true"
            >
                <i class="fa-solid fa-angles-left"></i>
            </button>

            <a href="../Inicio/inicioindex.php" class="brand-logo">
                <img
                    src="../Elementos Gráficos/Logo Inadi sin Brillo.png"
                    alt="Logo de INADI"
                >
            </a>

            <span class="brand-name">INADI</span>

            <div class="brand-since">
                PORTAL DOCENTE
            </div>

            <div class="brand-address">
                Salto, Uruguay
            </div>

        </div>

        <nav class="side-nav" aria-label="Menú docente">

            <a href="#inicio" class="nav-link active">
                <i class="fa-solid fa-house"></i>
                <span>Inicio</span>
            </a>

            <a href="#calendario" class="nav-link">
                <i class="fa-solid fa-calendar-days"></i>
                <span>Calendario docente</span>
            </a>

            <a href="#mensajes" class="nav-link">
                <i class="fa-solid fa-envelope"></i>
                <span>Mensajes</span>
            </a>

            <a href="#recursos" class="nav-link">
                <i class="fa-solid fa-lightbulb"></i>
                <span>Recursos docentes</span>
            </a>

            <a href="../Inicio/inicioindex.php" class="nav-link">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Volver al inicio</span>
            </a>

        </nav>

        <div class="sidebar-footer">
            Instituto de Informática<br>
            INADI · Salto
        </div>

    </aside>


    <!-- ÁREA PRINCIPAL -->

    <div class="main-area">

        <!-- BARRA SUPERIOR -->

        <header class="topbar">

            <div class="news-strip">

                <div class="news-label">
                    <i class="fa-solid fa-circle"></i>
                    PORTAL DOCENTE
                </div>

                <div class="news-track">
                    <span>
                        <i class="fa-solid fa-graduation-cap"></i>
                        Espacio de trabajo docente
                    </span>

                    <span>
                        <i class="fa-solid fa-calendar-check"></i>
                        Organización académica
                    </span>
                </div>

            </div>

            <div class="top-actions">

                <a
                    href="#calendario"
                    class="top-portal"
                >
                    <i class="fa-solid fa-calendar-days"></i>
                    Mi agenda
                </a>

                <div class="user-menu">

                    <div class="user-avatar">
                        <?= htmlspecialchars(
                            $iniciales,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>

                    <div class="user-data">

                        <strong>
                            <?= $nombreUsuario ?>
                        </strong>

                        <small>
                            <i class="fa-solid fa-circle"></i>
                            Portal Docente
                        </small>

                    </div>

                    <button
                        type="button"
                        class="profile-chevron"
                        aria-label="Abrir menú de usuario"
                        aria-expanded="false"
                    >
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>

                    <div class="profile-dropdown">

                        <a href="../Inicio/inicioindex.php">
                            <i class="fa-solid fa-house"></i>
                            Sitio principal
                        </a>

                        <a href="#recursos">
                            <i class="fa-solid fa-book-open"></i>
                            Recursos docentes
                        </a>

                        <!--
                        Integra aquí el enlace de cierre de sesión
                        que utiliza tu proyecto.
                        -->

                    </div>

                </div>

            </div>

        </header>


        <!-- CONTENIDO DEL PORTAL -->

        <main class="dashboard-content">


            <!-- BIENVENIDA -->

            <section
                class="dashboard-section"
                id="inicio"
            >

                <div class="section-heading">

                    <div>
                        <span class="section-kicker">
                            ESPACIO DE TRABAJO
                        </span>

                        <h2>
                            Bienvenido/a, <?= $nombreUsuario ?>
                        </h2>
                    </div>

                </div>

                <div class="information-panel">

                    <div class="information-icon">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>

                    <div>

                        <h3>Tu espacio docente en INADI</h3>

                        <p>
                            Desde este espacio podrás acceder a las
                            herramientas destinadas a la organización
                            académica, la planificación de clases y
                            los recursos de apoyo a la enseñanza.
                        </p>

                    </div>

                </div>

            </section>


            <!-- CALENDARIO DOCENTE -->

            <section
                class="dashboard-section"
                id="calendario"
            >

                <div class="section-heading">

                    <div>
                        <span class="section-kicker">
                            AGENDA
                        </span>

                        <h2>Calendario docente</h2>
                    </div>

                </div>

                <div class="information-panel">

                    <div class="information-icon">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>

                    <div>

                        <h3>Planificación de clases</h3>

                        <p>
                            Aquí se podrán visualizar las clases,
                            evaluaciones, entregas y eventos
                            académicos.
                        </p>

                    </div>

                </div>

            </section>


            <!-- MENSAJES -->

            <section
                class="dashboard-section"
                id="mensajes"
            >

                <div class="section-heading">

                    <div>
                        <span class="section-kicker">
                            COMUNICACIÓN
                        </span>

                        <h2>Mensajes</h2>
                    </div>

                </div>

                <div class="information-panel">

                    <div class="information-icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>

                    <div>

                        <h3>Comunicación educativa</h3>

                        <p>
                            Se podrá incorporar un espacio de
                            comunicación entre docentes y los
                            usuarios autorizados de la plataforma.
                        </p>

                    </div>

                </div>

            </section>


            <!-- RECURSOS DOCENTES -->

            <section
                class="dashboard-section"
                id="recursos"
            >

                <div class="section-heading">

                    <div>
                        <span class="section-kicker">
                            APOYO
                        </span>

                        <h2>Recursos docentes</h2>
                    </div>

                </div>

                <div class="information-panel">

                    <div class="information-icon">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>

                    <div>

                        <h3>Herramientas para enseñar</h3>

                        <p>
                            Un espacio para reunir guías, herramientas
                            digitales, propuestas didácticas y recursos
                            de apoyo a la enseñanza.
                        </p>

                    </div>

                </div>

            </section>


            <!-- PIE DE PÁGINA -->

            <footer class="teacher-footer">

                <div>
                    <strong>INADI</strong>
                    <span>Portal Docente</span>
                </div>

                <p>
                    Instituto de Informática · Salto, Uruguay
                </p>

                <a href="../Inicio/inicioindex.php">
                    Volver al sitio principal
                </a>

            </footer>

        </main>

    </div>

</div>


<script src="portal-docente.js" defer></script>

</body>
</html>

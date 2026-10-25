<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../Estructura/topbar_controls.php';

/* INADI | PORTAL DOCENTE */

// Nombre del docente
$nombreOriginal = $_SESSION['nombre_usuario']
    ?? $_SESSION['nombre']
    ?? 'Docente';

$nombreOriginal = trim((string) $nombreOriginal);

if ($nombreOriginal === '') {
    $nombreOriginal = 'Docente';
}

// Escapar texto para mostrar en HTML
$nombreUsuario = htmlspecialchars(
    $nombreOriginal,
    ENT_QUOTES,
    'UTF-8'
);

// Generar iniciales para el avatar
$partesNombre = preg_split('/\s+/u', $nombreOriginal);
$iniciales = '';

foreach (array_slice($partesNombre ?: [], 0, 2) as $parte) {
    if ($parte !== '') {
        if (function_exists('mb_substr')) {
            $iniciales .= mb_strtoupper(
                mb_substr($parte, 0, 1, 'UTF-8'),
                'UTF-8'
            );
        } else {
            $iniciales .= strtoupper(substr($parte, 0, 1));
        }
    }
}

if ($iniciales === '') {
    $iniciales = 'D';
}

$iniciales = htmlspecialchars(
    $iniciales,
    ENT_QUOTES,
    'UTF-8'
);

// Enlaces a las páginas del portal
$paginaInicio = 'bienvenidoindex.php';
$paginaCalendario = 'calendarioindex.php';
$paginaMensajes = 'mensajesindex.php';
$paginaRecursos = 'recursosindex.php';

// Identificar página activa
$archivoActual = basename($_SERVER['PHP_SELF'] ?? '');

$paginas = [
    'portalDocente.php'   => 'inicio',
    'bienvenidoindex.php' => 'inicio',
    'calendarioindex.php' => 'calendario',
    'mensajesindex.php'   => 'mensajes',
    'recursosindex.php'   => 'recursos'
];

$seccionActiva = $paginas[$archivoActual] ?? 'inicio';

// Fecha actual
$fechaActual = date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI | Portal Docente</title>

    <meta
        name="description"
        content="Portal docente de INADI. Organización académica y recursos educativos."
    >

    <!-- Estilos compartidos del Portal Docente -->
    <link rel="stylesheet" href="/Proyecto-INSOFT/Estructura/topbar-controls.css?v=1">
    <link rel="stylesheet" href="/Proyecto-INSOFT/Estructura/paneles-theme.css?v=1">
    <link rel="stylesheet" href="portalDocente.css?v=11">

    <!-- Iconos -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- JavaScript independiente -->
    <script src="portal-docente.js" defer></script>
</head>

<body>

<div class="portal-shell">

    <!-- BARRA LATERAL -->

    <aside class="sidebar" id="sidebar">

        <div class="brand">

            <button
                type="button"
                class="icon-button sidebar-collapse"
                id="sidebarToggle"
                aria-label="Contraer menú"
                aria-expanded="true"
                aria-controls="sidebar"
            >
                <i class="fa-solid fa-angles-left"></i>
            </button>

            <a href="../Inicio/inicioindex.php" class="brand-logo">
                <span class="brand-monogram" aria-hidden="true">IN</span>
                <img
                    src="../Elementos%20Gr%C3%A1ficos/Logo%20Inadi%20sin%20Brillo.png"
                    alt="Logo de INADI"
                    onerror="this.style.display='none';"
                >
            </a>

            <span class="brand-name">INADI</span>

            <span class="brand-since">PORTAL DOCENTE</span>

            <span class="brand-address">Salto, Uruguay</span>

        </div>

        <nav class="side-nav" aria-label="Menú docente">

            <span class="nav-section-label">General</span>

            <a
                href="<?= htmlspecialchars($paginaInicio, ENT_QUOTES, 'UTF-8') ?>"
                class="nav-link <?= $seccionActiva === 'inicio' ? 'active' : '' ?>"
                <?= $seccionActiva === 'inicio' ? 'aria-current="page"' : '' ?>
            >
                <i class="fa-solid fa-house"></i>
                <span>Inicio</span>
            </a>

            <span class="nav-section-label">Organización</span>

            <a
                href="<?= htmlspecialchars($paginaCalendario, ENT_QUOTES, 'UTF-8') ?>"
                class="nav-link <?= $seccionActiva === 'calendario' ? 'active' : '' ?>"
                <?= $seccionActiva === 'calendario' ? 'aria-current="page"' : '' ?>
            >
                <i class="fa-solid fa-calendar-days"></i>
                <span>Calendario docente</span>
            </a>

            <span class="nav-section-label">Comunicación</span>

            <a
                href="<?= htmlspecialchars($paginaMensajes, ENT_QUOTES, 'UTF-8') ?>"
                class="nav-link <?= $seccionActiva === 'mensajes' ? 'active' : '' ?>"
                <?= $seccionActiva === 'mensajes' ? 'aria-current="page"' : '' ?>
            >
                <i class="fa-solid fa-envelope"></i>
                <span>Mensajes</span>
            </a>

            <a
                href="<?= htmlspecialchars($paginaRecursos, ENT_QUOTES, 'UTF-8') ?>"
                class="nav-link <?= $seccionActiva === 'recursos' ? 'active' : '' ?>"
                <?= $seccionActiva === 'recursos' ? 'aria-current="page"' : '' ?>
            >
                <i class="fa-solid fa-lightbulb"></i>
                <span>Recursos docentes</span>
            </a>

            <a href="../Inicio/inicioindex.php" class="nav-link">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Volver al inicio</span>
            </a>

        </nav>

        <div class="sidebar-footer">
            <strong>Instituto de Informática</strong>
            <span>INADI · Salto</span>
        </div>

    </aside>

    <!-- ÁREA PRINCIPAL -->

    <div class="main-area">

        <!-- BARRA SUPERIOR -->

        <header class="topbar">

            <div class="news-strip">

                <div class="news-label">
                    <i class="fa-solid fa-circle"></i>
                    <span>PORTAL DOCENTE</span>
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

                <a href="<?= htmlspecialchars($paginaCalendario, ENT_QUOTES, 'UTF-8') ?>"
                   class="top-portal">

                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Mi agenda</span>

                </a>

                <?php renderSiteTopbarControls(); ?>

            </div>

        </header>

        <!-- CONTENIDO PRINCIPAL -->

        <main class="dashboard-content">

            <!-- BIENVENIDA -->

            <section class="dashboard-section" id="inicio">

                <div class="section-heading">

                    <div>
                        <span class="section-kicker">
                            ESPACIO DE TRABAJO
                        </span>

                        <h1>Bienvenido/a, <?= $nombreUsuario ?></h1>

                        <p>
                            Tu espacio de organización y apoyo a la tarea docente.
                        </p>
                    </div>

                    <div class="current-date">
                        <i class="fa-regular fa-calendar"></i>
                        <span><?= htmlspecialchars($fechaActual, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>

                </div>

                <div class="information-panel">

                    <div class="information-icon">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>

                    <div class="information-text">
                        <span class="panel-label">BIENVENIDA</span>

                        <h2>Tu espacio docente en INADI</h2>

                        <p>
                            Desde este espacio podrás acceder a las herramientas
                            destinadas a la organización académica, la planificación
                            de clases y los recursos de apoyo a la enseñanza.
                        </p>
                    </div>

                </div>

            </section>

            <!-- ACCESOS DIRECTOS -->

            <section class="dashboard-section">

                <div class="section-heading">
                    <div>
                        <span class="section-kicker">ACCESOS DIRECTOS</span>
                        <h2>¿Qué necesitás hacer?</h2>
                        <p>Seleccioná una herramienta para continuar.</p>
                    </div>
                </div>

                <div class="quick-access-grid">

                    <!-- CALENDARIO -->

                    <a
                        href="<?= htmlspecialchars($paginaCalendario, ENT_QUOTES, 'UTF-8') ?>"
                        class="quick-access-card"
                    >

                        <div class="card-top">
                            <div class="information-icon icon-calendar">
                                <i class="fa-solid fa-calendar-days"></i>
                            </div>

                            <span class="card-arrow">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </span>
                        </div>

                        <span class="card-category">ORGANIZACIÓN</span>

                        <h3>Calendario docente</h3>

                        <p>
                            Consultá tu agenda y organizá las actividades académicas.
                        </p>

                        <span class="quick-access-link">
                            Abrir calendario
                            <i class="fa-solid fa-arrow-right"></i>
                        </span>

                    </a>

                    <!-- MENSAJES -->

                    <a
                        href="<?= htmlspecialchars($paginaMensajes, ENT_QUOTES, 'UTF-8') ?>"
                        class="quick-access-card"
                    >

                        <div class="card-top">
                            <div class="information-icon icon-messages">
                                <i class="fa-solid fa-envelope"></i>
                            </div>

                            <span class="card-arrow">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </span>
                        </div>

                        <span class="card-category">COMUNICACIÓN</span>

                        <h3>Mensajes</h3>

                        <p>
                            Accedé al espacio de comunicación educativa.
                        </p>

                        <span class="quick-access-link">
                            Abrir mensajes
                            <i class="fa-solid fa-arrow-right"></i>
                        </span>

                    </a>

                    <!-- RECURSOS -->

                    <a
                        href="<?= htmlspecialchars($paginaRecursos, ENT_QUOTES, 'UTF-8') ?>"
                        class="quick-access-card"
                    >

                        <div class="card-top">
                            <div class="information-icon icon-resources">
                                <i class="fa-solid fa-lightbulb"></i>
                            </div>

                            <span class="card-arrow">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </span>
                        </div>

                        <span class="card-category">MATERIALES</span>

                        <h3>Recursos docentes</h3>

                        <p>
                            Explorá materiales y herramientas de apoyo a la enseñanza.
                        </p>

                        <span class="quick-access-link">
                            Ver recursos
                            <i class="fa-solid fa-arrow-right"></i>
                        </span>

                    </a>

                </div>

            </section>

            <!-- INFORMACIÓN INSTITUCIONAL -->

            <section class="dashboard-section">

                <div class="section-heading">
                    <div>
                        <span class="section-kicker">INSTITUCIÓN</span>
                        <h2>Comunidad educativa INADI</h2>
                    </div>
                </div>

                <div class="information-panel institutional-panel">

                    <div class="information-icon icon-institution">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>

                    <div class="information-text">
                        <h3>Instituto de Informática · Salto, Uruguay</h3>

                        <p>
                            Este portal reúne accesos para acompañar la tarea
                            docente y facilitar la organización de las actividades
                            educativas.
                        </p>
                    </div>

                </div>

            </section>

            <!-- PIE DE PÁGINA -->

            <footer class="teacher-footer">

                <div class="footer-brand">
                    <strong>INADI</strong>
                    <span>Portal Docente</span>
                </div>

                <p>Instituto de Informática · Salto, Uruguay</p>

                <a href="../Inicio/inicioindex.php">
                    Volver al sitio principal
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            </footer>

        </main>

    </div>

</div>

</body>
</html>
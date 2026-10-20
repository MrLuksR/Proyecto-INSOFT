<?php
session_start();

/*
 * INADI | Portal del estudiante
 * Ajustá las rutas de los módulos si en tu proyecto tienen otro nombre.
 */
$nombre = trim(($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? ''));
if ($nombre === '') {
    $nombre = $_SESSION['nombre_usuario'] ?? 'Estudiante';
}
$nombreUsuario = $_SESSION['nombre_usuario'] ?? 'Alumno INADI';
$iniciales = '';
foreach (preg_split('/\s+/', trim($nombre)) as $parte) {
    if ($parte !== '') $iniciales .= mb_strtoupper(mb_substr($parte, 0, 1, 'UTF-8'), 'UTF-8');
}
$iniciales = mb_substr($iniciales ?: 'AL', 0, 2, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#101521">
    <title>INADI | Portal del estudiante</title>
    <link rel="icon" href="../Elementos Gráficos/Logo Inadi sin Brillo.png">
    <link rel="stylesheet" href="CSS/stylesportal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<div class="portal-shell">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <button class="icon-button sidebar-collapse" id="sidebarToggle" aria-label="Contraer menú">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <a class="brand-logo" href="../Inicio/inicioindex.php" aria-label="Inicio INADI">
                <img src="../Elementos Gráficos/Logo Inadi sin Brillo.png" alt="Logo INADI">
            </a>
            <div class="brand-name">INADI</div>
            <div class="brand-since">DESDE 1992</div>
            <div class="brand-address">Artigas 827 · Salto, Uruguay</div>
        </div>

        <nav class="side-nav" aria-label="Navegación principal">
            <a class="nav-link" href="../Inicio/inicioindex.php">
                <i class="fa-solid fa-house"></i><span>Inicio / Web Pública</span>
            </a>
            <a class="nav-link active" href="portalEstudiante.php">
                <i class="fa-solid fa-book-open"></i><span>Portal Alumno &amp; Cursos</span><span class="nav-count">2</span>
            </a>
            <a class="nav-link" href="../Matricula/matricula.php">
                <i class="fa-solid fa-user-plus"></i><span>Matrícula</span>
            </a>
            <a class="nav-link" href="../Convenios/convenios.php">
                <i class="fa-solid fa-tag"></i><span>Convenios</span>
            </a>
            <a class="nav-link" href="../Empresas/empresas.php">
                <i class="fa-solid fa-briefcase"></i><span>Empresas</span>
            </a>
            <a class="nav-link" href="../Nosotros/nosotros.php">
                <i class="fa-solid fa-users"></i><span>Nosotros &amp; Historia</span>
            </a>
            <a class="nav-link" href="../Galeria/galeria.php">
                <i class="fa-regular fa-image"></i><span>Galería Institucional</span>
            </a>
        </nav>

        <a class="whatsapp-link" href="https://wa.me/59847331609" target="_blank" rel="noopener noreferrer">
            <i class="fa-brands fa-whatsapp"></i><span>¿Consultas? WhatsApp Salto</span>
        </a>
        <div class="sidebar-footer">INADI · Formación para el futuro</div>
    </aside>

    <main class="main-area">
        <header class="topbar">
            <div class="news-strip">
                <span class="news-label"><i class="fa-solid fa-circle"></i> NOTICIAS</span>
                <div class="news-track">
                    <span><i class="fa-solid fa-star"></i> Actualizaciones tecnológicas disponibles en campus</span>
                    <span><i class="fa-solid fa-star"></i> Entrega de certificados en sede Artigas 827</span>
                    <span><i class="fa-solid fa-star"></i> Consultá las novedades de tus cursos</span>
                </div>
            </div>
            <div class="top-actions">
                <a class="top-portal" href="portalEstudiante.php"><i class="fa-solid fa-graduation-cap"></i> Portal Estudiante</a>
                <button class="top-icon" id="languageButton" type="button" aria-label="Idioma"><i class="fa-solid fa-globe"></i><span>ES</span></button>
                <button class="top-icon notification-button" id="notificationButton" type="button" aria-label="Notificaciones"><i class="fa-regular fa-bell"></i><span class="notification-dot"></span></button>
                <div class="user-menu">
                    <div class="user-avatar"><?= htmlspecialchars($iniciales, ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="user-data">
                        <strong><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></strong>
                        <small><i class="fa-solid fa-circle"></i> Alumno/a regular · INADI Salto</small>
                    </div>
                    <button class="top-icon profile-chevron" id="profileButton" type="button" aria-label="Opciones de perfil"><i class="fa-solid fa-chevron-down"></i></button>
                    <div class="profile-dropdown" id="profileDropdown">
                        <a href="#perfil"><i class="fa-regular fa-user"></i> Mi perfil</a>
                        <a href="../auth/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-layout">
            <section class="dashboard-content">
                <div class="welcome-banner">
                    <div class="welcome-copy">
                        <div class="campus-pill"><span></span> CAMPUS DIGITAL · CICLO LECTIVO 2026</div>
                        <h1>¡Bienvenida<br>a tu Campus,<br><em><?= htmlspecialchars(explode(' ', $nombre)[0] ?: 'estudiante', ENT_QUOTES, 'UTF-8') ?></em>!</h1>
                        <p>Instituto Nacional de Informática · Sede Salto (Artigas 827). Más de 30 años formando profesionales del software y la tecnología aplicada en Uruguay.</p>
                    </div>
                    <div class="welcome-actions">
                        <a class="button button-yellow" href="#" data-not-configured="Moodle"><i class="fa-solid fa-graduation-cap"></i> Acceso a Aula Virtual (Moodle)</a>
                        <a class="button button-blue" href="#credencial"><i class="fa-regular fa-id-card"></i> Ficha &amp; Credencial Digital</a>
                    </div>
                    <div class="banner-watermark"><i class="fa-solid fa-shield-halved"></i></div>
                </div>

                <section class="stats-grid" aria-label="Resumen académico">
                    <article class="stat-card">
                        <div class="stat-icon orange"><i class="fa-solid fa-book"></i></div>
                        <div><strong>3</strong><span>Cursos<br>inscriptos</span></div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-icon blue"><i class="fa-regular fa-clock"></i></div>
                        <div><strong>8</strong><span>Meses<br>cursados</span></div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-icon green"><i class="fa-regular fa-circle-check"></i></div>
                        <div><strong>96%</strong><span>Asistencia a<br>clases</span></div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-icon violet"><i class="fa-regular fa-star"></i></div>
                        <div><strong>12</strong><span>Entregas<br>aprobadas</span></div>
                    </article>
                </section>

                <section class="section-block" id="cursos">
                    <div class="section-heading">
                        <div><span class="eyebrow">TU FORMACIÓN</span><h2>Mis cursos</h2></div>
                        <a class="text-link" href="../Cursos/cursosindex.php">Explorar cursos <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                    <div class="course-grid">
                        <article class="course-card">
                            <div class="course-top programming"><i class="fa-solid fa-code"></i><span>EN CURSO</span></div>
                            <div class="course-body"><small>TECNOLOGÍA</small><h3>Programación</h3><p>Aprendé a construir soluciones con código y lógica computacional.</p><div class="progress-label"><span>Progreso del curso</span><strong>72%</strong></div><div class="progress"><span style="width:72%"></span></div><a href="#" data-not-configured="Aula de Programación">Ingresar al curso <i class="fa-solid fa-arrow-right"></i></a></div>
                        </article>
                        <article class="course-card">
                            <div class="course-top design"><i class="fa-solid fa-pen-ruler"></i><span>EN CURSO</span></div>
                            <div class="course-body"><small>DISEÑO DIGITAL</small><h3>Diseño</h3><p>Desarrollá propuestas visuales y experiencias digitales.</p><div class="progress-label"><span>Progreso del curso</span><strong>58%</strong></div><div class="progress"><span style="width:58%"></span></div><a href="#" data-not-configured="Aula de Diseño">Ingresar al curso <i class="fa-solid fa-arrow-right"></i></a></div>
                        </article>
                        <article class="course-card">
                            <div class="course-top accounting"><i class="fa-solid fa-calculator"></i><span>EN CURSO</span></div>
                            <div class="course-body"><small>GESTIÓN</small><h3>Contabilidad</h3><p>Incorporá herramientas para la gestión administrativa y financiera.</p><div class="progress-label"><span>Progreso del curso</span><strong>84%</strong></div><div class="progress"><span style="width:84%"></span></div><a href="#" data-not-configured="Aula de Contabilidad">Ingresar al curso <i class="fa-solid fa-arrow-right"></i></a></div>
                        </article>
                    </div>
                </section>

                <section class="lower-grid">
                    <article class="panel" id="calificaciones">
                        <div class="section-heading compact"><div><span class="eyebrow">SEGUIMIENTO</span><h2>Actividad reciente</h2></div><i class="fa-regular fa-clock heading-icon"></i></div>
                        <div class="activity-item"><div class="activity-icon green"><i class="fa-solid fa-check"></i></div><div><strong>Entrega aprobada</strong><p>Programación · Actividad práctica</p></div><time>Hoy</time></div>
                        <div class="activity-item"><div class="activity-icon blue"><i class="fa-solid fa-file-lines"></i></div><div><strong>Material actualizado</strong><p>Diseño · Unidad 4</p></div><time>Ayer</time></div>
                        <div class="activity-item"><div class="activity-icon orange"><i class="fa-solid fa-calendar-check"></i></div><div><strong>Próxima evaluación</strong><p>Contabilidad · Revisá el calendario</p></div><time>20 oct.</time></div>
                    </article>
                    <article class="panel" id="calendario">
                        <div class="section-heading compact"><div><span class="eyebrow">ORGANIZACIÓN</span><h2>Próximas fechas</h2></div><i class="fa-regular fa-calendar heading-icon"></i></div>
                        <div class="date-item"><div class="date-box"><b>20</b><span>OCT</span></div><div><strong>Evaluación de Contabilidad</strong><p>Revisá las indicaciones del docente.</p></div></div>
                        <div class="date-item"><div class="date-box"><b>23</b><span>OCT</span></div><div><strong>Entrega de actividad</strong><p>Programación · Trabajo práctico.</p></div></div>
                        <a class="text-link calendar-link" href="#calendario">Ver calendario completo <i class="fa-solid fa-arrow-right"></i></a>
                    </article>
                </section>
                <footer class="page-footer">© <?= date('Y') ?> INADI · Instituto Nacional de Informática · Salto, Uruguay</footer>
            </section>

            <aside class="modules-panel">
                <div class="modules-heading"><span><i class="fa-solid fa-circle"></i> MÓDULOS ALUMNO</span><button class="modules-toggle" id="modulesToggle" aria-label="Ocultar módulos"><i class="fa-solid fa-chevron-right"></i></button></div>
                <nav class="module-nav" aria-label="Módulos del alumno">
                    <a class="module-link active" href="#cursos"><i class="fa-solid fa-graduation-cap"></i><span>Cursos</span></a>
                    <a class="module-link" href="#legajo" data-not-configured="Legajo"><i class="fa-regular fa-file-lines"></i><span>Legajo</span></a>
                    <a class="module-link" href="#calificaciones"><i class="fa-solid fa-wave-square"></i><span>Calificaciones</span></a>
                    <a class="module-link" href="#perfil" data-not-configured="Perfil estudiante"><i class="fa-regular fa-user"></i><span>Perfil Estudiante</span></a>
                    <a class="module-link" href="../Matricula/matricula.php"><i class="fa-regular fa-rectangle-list"></i><span>Matrícula</span></a>
                    <a class="module-link" href="#calendario"><i class="fa-regular fa-calendar"></i><span>Calendario</span></a>
                </nav>
                <div class="module-help">
                    <div class="help-symbol"><i class="fa-regular fa-circle-question"></i></div>
                    <h3>¿Necesitás ayuda?</h3>
                    <p>Contactate con el equipo de INADI para resolver tus consultas.</p>
                    <a href="https://wa.me/59847331609" target="_blank" rel="noopener noreferrer">Contactar soporte <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                </div>
                <a class="edit-data" href="#perfil" data-not-configured="Modificar datos"><i class="fa-regular fa-pen-to-square"></i> Modificar datos</a>
            </aside>
        </div>
    </main>
</div>

<div class="toast" id="portalToast" role="status" aria-live="polite"></div>
<script src="JS/scriptportal.js"></script>
</body>
</html>

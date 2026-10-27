<?php
/**
 * Portal Docente INADI - INSOFT
 * Vista previa visual: index.php?demo=1
 * El modo demo usa datos ficticios y no debe habilitarse en producción.
 */
session_start();
date_default_timezone_set('America/Montevideo');

function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// La vista previa solo se activa expresamente mediante ?demo=1.
$modoDemo = isset($_GET['demo']) && $_GET['demo'] === '1';
$autenticado = isset($_SESSION['docente_id']);
$sinSesion = !$autenticado && !$modoDemo;
if ($sinSesion) {
    http_response_code(403);
}

$nombreDocente = ($modoDemo && !$autenticado)
    ? 'María González'
    : ($_SESSION['docente_nombre'] ?? $_SESSION['nombre'] ?? 'Docente');
$nombreInicial = function_exists('mb_substr')
    ? mb_strtoupper(mb_substr($nombreDocente, 0, 1, 'UTF-8'), 'UTF-8')
    : strtoupper(substr($nombreDocente, 0, 1));
$pagina = isset($_GET['page']) ? (string)$_GET['page'] : 'inicio';

$modulos = [
    'inicio' => ['label'=>'Inicio', 'icon'=>'⌂', 'eyebrow'=>'RESUMEN GENERAL'],
    'cursos' => ['label'=>'Mis cursos', 'icon'=>'▤', 'eyebrow'=>'ACTIVIDAD ACADÉMICA'],
    'estudiantes' => ['label'=>'Estudiantes', 'icon'=>'♙', 'eyebrow'=>'ACTIVIDAD ACADÉMICA'],
    'calificaciones' => ['label'=>'Calificaciones', 'icon'=>'✓', 'eyebrow'=>'EVALUACIÓN'],
    'asistencia' => ['label'=>'Asistencia', 'icon'=>'◷', 'eyebrow'=>'SEGUIMIENTO'],
    'actividades' => ['label'=>'Actividades', 'icon'=>'▣', 'eyebrow'=>'ENSEÑANZA'],
    'materiales' => ['label'=>'Materiales', 'icon'=>'▧', 'eyebrow'=>'RECURSOS'],
    'calendario' => ['label'=>'Calendario', 'icon'=>'▦', 'eyebrow'=>'ORGANIZACIÓN'],
    'notificaciones' => ['label'=>'Notificaciones', 'icon'=>'♧', 'eyebrow'=>'COMUNICACIÓN'],
    'informes' => ['label'=>'Informes', 'icon'=>'▥', 'eyebrow'=>'REPORTES'],
    'perfil' => ['label'=>'Mi perfil', 'icon'=>'◎', 'eyebrow'=>'CUENTA'],
];
if (!isset($modulos[$pagina])) $pagina = 'inicio';

$titulos = [
    'inicio'=>'Panel docente', 'cursos'=>'Mis cursos', 'estudiantes'=>'Estudiantes',
    'calificaciones'=>'Calificaciones', 'asistencia'=>'Asistencia',
    'actividades'=>'Actividades y tareas', 'materiales'=>'Materiales educativos',
    'calendario'=>'Calendario académico', 'notificaciones'=>'Notificaciones',
    'informes'=>'Informes académicos', 'perfil'=>'Mi perfil'
];
$descripciones = [
    'cursos'=>'Consulta los cursos y grupos que tienes asignados.',
    'estudiantes'=>'Accede a las listas y fichas de estudiantes autorizadas.',
    'calificaciones'=>'Organiza evaluaciones y consulta las calificaciones.',
    'asistencia'=>'Registra y revisa la asistencia de tus grupos.',
    'actividades'=>'Gestiona actividades, tareas y entregas.',
    'materiales'=>'Organiza recursos y materiales para tus cursos.',
    'calendario'=>'Consulta fechas académicas, clases y vencimientos.',
    'notificaciones'=>'Revisa comunicaciones y avisos institucionales.',
    'informes'=>'Consulta reportes basados en los datos académicos disponibles.',
    'perfil'=>'Consulta y actualiza tu información profesional.'
];
$funciones = [
    'cursos'=>['Listado de cursos asignados','Grupo, turno y año lectivo','Estudiantes inscritos y horarios'],
    'estudiantes'=>['Listado filtrado por curso','Ficha académica según permisos','Búsqueda y filtros'],
    'calificaciones'=>['Evaluaciones y escalas de calificación','Registro y edición con validación','Historial de cambios'],
    'asistencia'=>['Lista de estudiantes por clase','Registro de presentes, ausentes y tardanzas','Resumen de asistencia'],
    'actividades'=>['Creación y edición de actividades','Fechas de vencimiento','Revisión de entregas'],
    'materiales'=>['Publicación de recursos','Descargas con control de permisos','Organización por curso'],
    'calendario'=>['Clases y fechas académicas','Vencimientos de actividades','Eventos institucionales'],
    'notificaciones'=>['Avisos institucionales','Mensajes vinculados a cursos','Estado de lectura'],
    'informes'=>['Resúmenes por curso','Reportes de asistencia y calificaciones','Exportación según permisos'],
    'perfil'=>['Datos personales existentes','Información profesional','Edición solo de campos permitidos']
];
$fechaHoy = new DateTimeImmutable('now', new DateTimeZone('America/Montevideo'));
$fechaFormato = $fechaHoy->format('d/m/Y');

// En ausencia de sesión real, se muestra el bloqueo normal salvo que se solicite demo.
if ($sinSesion): ?>
<!doctype html>
<html lang="es"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso requerido · Portal Docente INADI</title><link rel="stylesheet" href="CSS/docente.css">
</head><body class="access-page">
<main class="access-card">
    <div class="brand-mark">I</div><p class="eyebrow">INADI · INSOFT</p>
    <h1>Acceso al Portal Docente</h1>
    <p>Para continuar, inicia sesión en INSOFT con una cuenta docente. La autenticación docente todavía debe integrarse con el backend.</p>
    <div class="notice warning"><strong>Integración pendiente:</strong> el sistema debe establecer <code>$_SESSION['docente_id']</code> al autenticar al docente.</div>
    <a class="btn btn-primary" href="../index.php">Ir al inicio de INSOFT</a>
    <p class="muted" style="margin-top:16px;font-size:13px">Para revisar el diseño en tu entorno local, agrega <code>?demo=1</code> a la dirección del portal.</p>
</main></body></html>
<?php exit; endif; ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title><?= h($titulos[$pagina]) ?> · Portal Docente INADI</title>
    <link rel="stylesheet" href="CSS/docente.css?v=2"><script src="JS/docente.js" defer></script>
</head>
<body>
<?php if ($modoDemo && !$autenticado): ?>
<div style="background:#fff3cd;color:#664d03;padding:10px 16px;text-align:center;font:600 13px/1.4 system-ui,sans-serif">MODO DEMOSTRACIÓN · Datos ficticios · No se conecta a la base de datos</div>
<?php endif; ?>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <a class="sidebar-brand" href="index.php<?= $modoDemo ? '?demo=1' : '' ?>" aria-label="Portal Docente, inicio">
            <span class="brand-mark">I</span><span class="brand-copy"><strong>INADI</strong><small>Portal docente</small></span>
        </a>
        <div class="side-caption">MENÚ PRINCIPAL</div>
        <nav class="side-nav" aria-label="Navegación principal">
        <?php foreach ($modulos as $key => $item): ?>
            <a class="nav-link <?= $pagina === $key ? 'active' : '' ?>" href="index.php?page=<?= h($key) ?><?= $modoDemo ? '&amp;demo=1' : '' ?>" <?= $pagina === $key ? 'aria-current="page"' : '' ?>>
                <span class="nav-icon" aria-hidden="true"><?= h($item['icon']) ?></span><span><?= h($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
        </nav>
        <div class="sidebar-bottom">
            <div class="institution-note"><span class="note-dot"></span><span><strong>INSOFT</strong><small>Sistema institucional</small></span></div>
            <?php if ($modoDemo && !$autenticado): ?>
                <a class="nav-link logout-link" href="index.php"><span class="nav-icon">↪</span><span>Salir de demostración</span></a>
            <?php else: ?>
                <a class="nav-link logout-link" href="../logout.php"><span class="nav-icon">↪</span><span>Salir del sistema</span></a>
            <?php endif; ?>
        </div>
    </aside>
    <main class="main-area">
        <header class="topbar">
            <button class="icon-button menu-toggle" id="menuToggle" aria-label="Abrir menú" aria-controls="sidebar" aria-expanded="false">☰</button>
            <div class="topbar-context"><span class="topbar-label">INADI / INSOFT</span><span class="topbar-current"><?= h($titulos[$pagina]) ?></span></div>
            <div class="topbar-actions">
                <span class="date-chip"><?= h($fechaFormato) ?></span>
                <a class="user-chip" href="index.php?page=perfil<?= $modoDemo ? '&amp;demo=1' : '' ?>"><span class="avatar"><?= h($nombreInicial) ?></span><span class="user-details"><strong><?= h($nombreDocente) ?></strong><small>Docente<?= $modoDemo && !$autenticado ? ' · Demo' : '' ?></small></span></a>
            </div>
        </header>
        <div class="page-content">
        <?php if ($pagina === 'inicio'): ?>
            <section class="welcome-panel">
                <div class="welcome-copy"><span class="welcome-kicker">PORTAL DOCENTE · INADI</span>
                    <h1>¡Bienvenido/a, <?= h($nombreDocente) ?>!</h1>
                    <p>Tu espacio para organizar la actividad académica, acompañar a tus estudiantes y consultar tus cursos.</p>
                    <a class="btn btn-light" href="index.php?page=cursos<?= $modoDemo ? '&amp;demo=1' : '' ?>">Ver mis cursos <span aria-hidden="true"></span></a>
                </div><div class="welcome-art" aria-hidden="true"><div class="art-circle circle-one"></div><div class="art-circle circle-two"></div><div class="art-book">▤</div></div>
            </section>
            <div class="section-heading"><div><p class="eyebrow">RESUMEN</p><h2>Tu actividad académica</h2></div><span class="muted"><?= h($fechaFormato) ?></span></div>
            <div class="stats-grid">
            <?php
            $stats = [
                ['icon'=>'▤','class'=>'blue','label'=>'Cursos asignados','value'=>$modoDemo?'4':'—','foot'=>$modoDemo?'Cantidad ficticia de ejemplo':'Pendiente conectar con la base de datos'],
                ['icon'=>'♙','class'=>'cyan','label'=>'Estudiantes','value'=>$modoDemo?'86':'—','foot'=>$modoDemo?'Cantidad ficticia de ejemplo':'Pendiente conectar con la base de datos'],
                ['icon'=>'✓','class'=>'violet','label'=>'Entregas por corregir','value'=>$modoDemo?'12':'—','foot'=>$modoDemo?'Cantidad ficticia de ejemplo':'Pendiente conectar con la base de datos'],
                ['icon'=>'◷','class'=>'amber','label'=>'Próximas actividades','value'=>$modoDemo?'3':'—','foot'=>$modoDemo?'Cantidad ficticia de ejemplo':'Pendiente conectar con la base de datos']
            ]; foreach ($stats as $stat): ?>
                <article class="stat-card"><div class="stat-top"><span class="stat-icon <?= h($stat['class']) ?>"><?= h($stat['icon']) ?></span><span class="stat-status"><?= $modoDemo?'Demostración':'Datos reales' ?></span></div><p class="stat-label"><?= h($stat['label']) ?></p><p class="stat-value"><?= h($stat['value']) ?></p><p class="stat-foot"><?= h($stat['foot']) ?></p></article>
            <?php endforeach; ?>
            </div>
            <div class="content-grid">
                <section class="panel"><div class="panel-head"><div><p class="eyebrow">ACCESO RÁPIDO</p><h2>¿Qué necesitas hacer?</h2></div></div>
                    <div class="quick-grid">
                        <a class="quick-link" href="index.php?page=cursos<?= $modoDemo?'&amp;demo=1':'' ?>"><span class="quick-icon blue">▤</span><span><strong>Mis cursos</strong><small>Grupos y asignaturas</small></span><span class="quick-arrow"></span></a>
                        <a class="quick-link" href="index.php?page=calificaciones<?= $modoDemo?'&amp;demo=1':'' ?>"><span class="quick-icon violet">✓</span><span><strong>Calificaciones</strong><small>Evaluaciones y notas</small></span><span class="quick-arrow"></span></a>
                        <a class="quick-link" href="index.php?page=asistencia<?= $modoDemo?'&amp;demo=1':'' ?>"><span class="quick-icon cyan">◷</span><span><strong>Asistencia</strong><small>Registro por grupo</small></span><span class="quick-arrow"></span></a>
                        <a class="quick-link" href="index.php?page=materiales<?= $modoDemo?'&amp;demo=1':'' ?>"><span class="quick-icon amber">▧</span><span><strong>Materiales</strong><small>Recursos educativos</small></span><span class="quick-arrow"></span></a>
                    </div>
                </section>
                <section class="panel"><div class="panel-head"><div><p class="eyebrow">ESTADO DEL PORTAL</p><h2>Próximos pasos</h2></div></div>
                    <div class="check-list">
                        <div class="check-row"><span class="check-number">1</span><span><strong>Vincular la base de datos</strong><small>Mapear las tablas reales de cursos, grupos y usuarios.</small></span></div>
                        <div class="check-row"><span class="check-number">2</span><span><strong>Validar permisos docentes</strong><small>Limitar las consultas al docente autenticado.</small></span></div>
                        <div class="check-row"><span class="check-number">3</span><span><strong>Activar operaciones académicas</strong><small>Habilitar notas, asistencia y actividades según el esquema.</small></span></div>
                    </div>
                </section>
            </div>
        <?php else: ?>
            <div class="page-heading"><div><p class="eyebrow"><?= h($modulos[$pagina]['eyebrow']) ?></p><h1><?= h($titulos[$pagina]) ?></h1><p class="page-description"><?= h($descripciones[$pagina] ?? '') ?></p></div><a class="btn btn-secondary" href="index.php<?= $modoDemo?'?demo=1':'' ?>">Volver al inicio</a></div>
            <section class="panel module-panel">
                <div class="module-symbol"><?= h($modulos[$pagina]['icon']) ?></div>
                <h2><?= $modoDemo ? h($titulos[$pagina]).' · Vista previa' : 'Módulo preparado para integrar' ?></h2>
                <?php if ($modoDemo): ?>
                    <p>Esta vista permite revisar la navegación y el diseño. Los datos de ejemplo son ficticios y no se guardan.</p>
                    <?php if ($pagina === 'cursos'): ?>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:16px;margin-top:22px">
                        <?php $cursosDemo = [
                            ['Programación Web','Grupo A',24,'#e8f0ff'],
                            ['Python Básico','Grupo B',20,'#e4f7f5'],
                            ['Desarrollo de Videojuegos','Grupo A',22,'#f0eaff'],
                            ['Herramientas Digitales','Grupo C',20,'#fff0dc']
                        ]; foreach ($cursosDemo as $curso): ?>
                            <article style="padding:18px;border-radius:14px;background:<?= h($curso[3]) ?>;color:#24324a"><p style="font-size:12px;margin:0 0 10px"><?= h($curso[1]) ?></p><h3 style="margin:0 0 12px"><?= h($curso[0]) ?></h3><p style="margin:0;font-size:14px"><?= h($curso[2]) ?> estudiantes · Ejemplo</p></article>
                        <?php endforeach; ?>
                    </div>
                    <?php elseif ($pagina === 'perfil'): ?>
                        <p><strong>Nombre:</strong> María González</p><p><strong>Especialidad:</strong> Programación y tecnología (ejemplo)</p><p><strong>Correo:</strong> maria.ejemplo@insoft.test (ficticio)</p>
                    <?php else: ?>
                        <div class="notice warning" style="margin-top:18px"><strong>Vista previa:</strong> la funcionalidad real dependerá de la integración del backend.</div>
                        <ul class="feature-list"><?php foreach (($funciones[$pagina] ?? []) as $funcion): ?><li><span class="feature-check">✓</span><?= h($funcion) ?></li><?php endforeach; ?></ul>
                    <?php endif; ?>
                <?php else: ?>
                    <p>La estructura visual y la navegación están listas. Para mostrar o modificar información aquí, es necesario vincular este módulo con las tablas reales de la base de datos <code>insoft</code>.</p>
                    <div class="notice warning"><strong>No se muestran datos ficticios.</strong> El módulo debe conectarse a las tablas reales y validar los permisos en el servidor.</div>
                    <?php if ($pagina === 'perfil'): ?><p class="muted">Usuario de sesión: <strong><?= h($nombreDocente) ?></strong></p><?php endif; ?>
                    <div class="module-actions"><a class="btn btn-primary" href="index.php">Ir al panel docente</a></div>
                <?php endif; ?>
            </section>
            <?php if (!$modoDemo): ?><section class="panel implementation-panel"><div class="panel-head"><div><p class="eyebrow">INTEGRACIÓN</p><h2>Qué se conectará en este módulo</h2></div></div><ul class="feature-list"><?php foreach (($funciones[$pagina] ?? []) as $funcion): ?><li><span class="feature-check">✓</span><?= h($funcion) ?></li><?php endforeach; ?></ul></section><?php endif; ?>
        <?php endif; ?>
            <footer class="footer"><span>© <?= h($fechaHoy->format('Y')) ?> INADI · Portal Docente</span><span>Interfaz institucional INSOFT<?= $modoDemo?' · Modo demostración':'' ?></span></footer>
        </div>
    </main>
</div>
<div class="mobile-scrim" id="mobileScrim"></div>
</body>
</html>

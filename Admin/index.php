<?php
/**
 * INSOFT / INADI - Panel administrativo (plantilla visual)
 * Integración backend pendiente: conectar los formularios a las acciones del proyecto.
 * Mantiene el control de sesión admin_id y reutiliza config.php si está disponible.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// COMPROBAR QUE EL USUARIO SEA ADMINISTRATIVO
if (
    !isset($_SESSION['usuario'], $_SESSION['tipo_usuario']) ||
    $_SESSION['tipo_usuario'] !== 'administrativo'
) {
    header('Location: ../login/loginn.php');
    exit;
}

$configPath = __DIR__ . '/config.php';
if (file_exists($configPath)) {
    require_once $configPath; // Se espera que config.php inicialice $pdo.
}

$modules = [
    'dashboard' => ['Inicio', '▦', 'Resumen general'],
    'cursos'    => ['Cursos', '▤', 'Administrá la oferta educativa'],
    'docentes'  => ['Docentes', '♙', 'Docentes y cursos asignados'],
    'noticias'  => ['Noticias', '▣', 'Novedades y publicaciones'],
    'banners'   => ['Banners', '▧', 'Imágenes principales del sitio'],
    'galeria'   => ['Galería de fotos', '▧', 'Imágenes institucionales'],
    'empresas'  => ['Empresas que confían', '♧', 'Convenios y organizaciones'],
    'comentarios' => ['Comentarios', '☷', 'Mensajes y comentarios recibidos'],
    'formularios' => ['Formularios', '▤', 'Consultas recibidas'],
    'slides'    => ['Slides', '▱', 'Carruseles y presentaciones'],
    'usuarios'  => ['Usuarios', '♙', 'Accesos del portal'],
    'ajustes'   => ['Ajustes', '⚙', 'Configuración administrativa'],
    'perfil'    => ['Mi perfil', '◉', 'Datos de tu cuenta'],
];

$page = isset($_GET['page']) && isset($modules[$_GET['page']]) ? $_GET['page'] : 'dashboard';
$pageTitle = $modules[$page][0];
$adminName = $_SESSION['admin_name'] ?? 'Administrador';
$initials = mb_strtoupper(mb_substr(trim($adminName), 0, 1, 'UTF-8'), 'UTF-8');

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | Panel INSOFT</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--navy:#102a56;--navy2:#173a70;--blue:#2f68df;--blue2:#eaf1ff;--ink:#17243a;--muted:#7a879b;--line:#e8edf5;--bg:#f5f7fb;--white:#fff;--green:#168765;--orange:#c7791d;--shadow:0 10px 30px rgba(24,48,88,.06)}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font-family:'DM Sans',sans-serif;font-size:14px}a{text-decoration:none;color:inherit}button,input,select,textarea{font:inherit}.layout{min-height:100vh}.sidebar{position:fixed;inset:0 auto 0 0;width:254px;background:linear-gradient(180deg,#102a56 0%,#122447 100%);color:#d8e4fa;padding:25px 15px 18px;display:flex;flex-direction:column;z-index:10}.brand{display:flex;align-items:center;gap:12px;padding:0 12px 28px;border-bottom:1px solid rgba(255,255,255,.12)}.brand-mark{width:42px;height:42px;display:grid;place-items:center;border-radius:13px;background:#fff;color:var(--navy);font-family:Manrope,sans-serif;font-weight:800;font-size:19px;box-shadow:0 7px 18px #081b3b55}.brand-name{font-family:Manrope,sans-serif;font-size:16px;font-weight:800;color:#fff;letter-spacing:.3px}.brand-sub{font-size:10px;color:#9db5dc;letter-spacing:1.6px;margin-top:3px}.nav-label{font-size:10px;text-transform:uppercase;letter-spacing:1.6px;color:#8da7d1;font-weight:700;padding:24px 13px 10px}.nav{display:flex;flex-direction:column;gap:5px;overflow-y:auto}.nav-link{display:flex;align-items:center;gap:12px;padding:11px 13px;border-radius:11px;color:#c0d0eb;transition:.18s;font-weight:500}.nav-link:hover{background:#ffffff12;color:white}.nav-link.active{background:linear-gradient(100deg,#3777ed,#2d61c8);color:white;box-shadow:0 8px 18px #071a3a55}.nav-icon{width:22px;text-align:center;font-size:18px}.nav-link span:last-child{flex:1}.nav-dot{width:6px;height:6px;border-radius:50%;background:#8ab4ff}.sidebar-bottom{margin-top:auto;padding:16px 12px 2px;border-top:1px solid #ffffff18;color:#9db1d2;font-size:11px;line-height:1.7}.sidebar-bottom strong{display:block;color:#e6eeff;font-size:12px}.main{margin-left:254px;min-height:100vh}.topbar{height:76px;background:#fff;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:0 35px;position:sticky;top:0;z-index:5}.crumb{font-size:12px;color:#8a97aa;margin-bottom:4px}.crumb b{color:#53637a;font-weight:600}.top-title{font:700 16px Manrope,sans-serif;color:var(--ink)}.top-actions{display:flex;align-items:center;gap:18px}.icon-btn{width:38px;height:38px;border:1px solid var(--line);background:#fff;border-radius:11px;display:grid;place-items:center;color:#63718a;position:relative}.notice-dot{position:absolute;right:8px;top:7px;width:6px;height:6px;background:#ef6a6a;border-radius:50%;border:1px solid white}.profile{display:flex;align-items:center;gap:10px;padding-left:18px;border-left:1px solid var(--line)}.avatar{width:37px;height:37px;border-radius:12px;background:#e6edff;color:#285bc2;display:grid;place-items:center;font-weight:800}.profile-name{font-size:12px;font-weight:700}.profile-role{font-size:11px;color:var(--muted);margin-top:3px}.content{padding:31px 35px 45px;max-width:1600px;margin:auto}.welcome-row{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:27px}.eyebrow{font-size:11px;text-transform:uppercase;letter-spacing:1.7px;color:var(--blue);font-weight:800;margin-bottom:9px}.heading{font:800 27px Manrope,sans-serif;letter-spacing:-.7px;margin:0;color:#172744}.subtitle{color:var(--muted);margin:9px 0 0;font-size:13px;line-height:1.6}.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:10px;padding:11px 15px;background:var(--blue);color:#fff;font-weight:700;box-shadow:0 7px 16px #2f68df26;cursor:pointer;transition:.18s}.btn:hover{background:#2458c6;transform:translateY(-1px)}.btn-light{background:#fff;border:1px solid var(--line);color:#465873;box-shadow:none}.btn-light:hover{background:#f8faff}.btn-sm{padding:8px 11px;font-size:12px;border-radius:8px}.stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:17px;margin-bottom:23px}.stat-card,.panel{background:#fff;border:1px solid #e9eef6;border-radius:16px;box-shadow:var(--shadow)}.stat-card{padding:19px 20px;min-height:130px}.stat-top{display:flex;align-items:center;justify-content:space-between;color:#75839a;font-size:12px;font-weight:600}.stat-icon{width:37px;height:37px;border-radius:11px;display:grid;place-items:center;font-size:17px;background:#edf3ff;color:#3169d9}.stat-icon.mint{background:#e6f8f1;color:#1b9870}.stat-icon.amber{background:#fff4e5;color:#bd791c}.stat-icon.violet{background:#f1edff;color:#7959ca}.stat-num{font:800 27px Manrope,sans-serif;margin-top:12px;letter-spacing:-.7px}.stat-foot{font-size:11px;color:#8b98aa;margin-top:4px}.stat-foot .positive{color:#15946c;font-weight:700}.columns{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(280px,.85fr);gap:20px}.panel{padding:22px;margin-bottom:20px}.panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px}.panel-title{font:800 15px Manrope,sans-serif;margin:0}.panel-sub{font-size:11px;color:var(--muted);margin-top:5px}.text-link{color:var(--blue);font-weight:700;font-size:12px}.activity{display:flex;align-items:flex-start;gap:13px;padding:14px 0;border-bottom:1px solid #f0f3f8}.activity:last-child{border-bottom:0;padding-bottom:0}.activity-icon{width:36px;height:36px;border-radius:11px;background:#eef3ff;color:#3468d3;display:grid;place-items:center;flex-shrink:0}.activity-text{font-size:12px;line-height:1.55;color:#465570}.activity-text strong{color:#1d2c45}.activity-time{font-size:10px;color:#9aa5b5;margin-top:4px}.status-pill{display:inline-flex;align-items:center;gap:6px;border-radius:30px;padding:6px 9px;font-size:10px;font-weight:800;white-space:nowrap}.status-pill:before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor}.pill-green{background:#e8f8f1;color:#168763}.pill-blue{background:#edf3ff;color:#3269d6}.pill-orange{background:#fff4e5;color:#b87519}.quick-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}.quick{border:1px solid var(--line);border-radius:12px;padding:15px;transition:.18s}.quick:hover{border-color:#b9cdf9;background:#f8faff}.quick-ico{font-size:19px;color:var(--blue);margin-bottom:12px}.quick strong{display:block;font-size:12px}.quick span{display:block;color:var(--muted);font-size:11px;margin-top:5px;line-height:1.5}.toolbar{display:flex;gap:10px;align-items:center;flex-wrap:wrap}.search{position:relative;min-width:210px;flex:1}.search input{width:100%;border:1px solid var(--line);border-radius:10px;padding:11px 13px 11px 37px;outline:none;background:#fff;color:var(--ink)}.search:before{content:'⌕';position:absolute;left:13px;top:7px;font-size:21px;color:#8b99ad}.search input:focus,.field input:focus,.field select:focus,.field textarea:focus{border-color:#9cb9fa;box-shadow:0 0 0 3px #2f68df12;outline:none}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;white-space:nowrap}th{text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:#8b98aa;padding:12px 13px;background:#f8faff;font-weight:800}td{padding:15px 13px;border-bottom:1px solid #edf1f6;font-size:12px;color:#526078}tbody tr:last-child td{border-bottom:0}tbody tr:hover{background:#fbfcff}.person{display:flex;align-items:center;gap:10px}.mini-avatar{width:34px;height:34px;border-radius:10px;background:#eaf0ff;color:#2c61c8;display:grid;place-items:center;font-weight:800}.person strong{display:block;color:#24334d;font-size:12px}.person small{display:block;color:#929eb0;font-size:10px;margin-top:4px}.course-tags{display:flex;gap:5px;flex-wrap:wrap}.course-tag{background:#f0f4fc;color:#526a91;padding:5px 7px;border-radius:6px;font-size:10px;font-weight:700}.actions{display:flex;gap:6px}.action-btn{width:29px;height:29px;border:1px solid var(--line);border-radius:8px;background:#fff;color:#687892;display:grid;place-items:center;cursor:pointer}.action-btn:hover{border-color:#adc4f8;color:var(--blue)}.section-grid{display:grid;grid-template-columns:minmax(0,1.4fr) minmax(280px,.8fr);gap:20px;align-items:start}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px}.field{display:flex;flex-direction:column;gap:7px}.field.full{grid-column:1/-1}.field label{font-size:11px;font-weight:800;color:#53637a}.field input,.field select,.field textarea{width:100%;border:1px solid #e1e8f2;border-radius:9px;padding:11px 12px;color:#33445e;background:#fff;outline:none}.field input::placeholder,.field textarea::placeholder{color:#a8b2c1}.field small{font-size:10px;color:#929eb0;line-height:1.5}.input-note{display:flex;align-items:center;gap:7px;padding:11px 12px;border-radius:9px;background:#f2f6ff;color:#59709a;font-size:11px;line-height:1.5}.course-options{display:grid;grid-template-columns:1fr 1fr;gap:9px}.course-option{display:flex;align-items:flex-start;gap:9px;padding:12px;border:1px solid var(--line);border-radius:10px;cursor:pointer}.course-option:has(input:checked){border-color:#8eaff8;background:#f5f8ff}.course-option input{accent-color:var(--blue);margin-top:2px}.course-option strong{display:block;font-size:11px}.course-option small{display:block;font-size:10px;color:var(--muted);margin-top:4px}.form-actions{display:flex;justify-content:flex-end;gap:9px;margin-top:20px;padding-top:17px;border-top:1px solid #eef2f7}.side-info{background:linear-gradient(145deg,#173b76,#10264d);border-radius:16px;padding:23px;color:#fff;overflow:hidden;position:relative}.side-info:after{content:'';position:absolute;width:180px;height:180px;border:1px solid #ffffff16;border-radius:50%;right:-65px;top:-55px;box-shadow:0 0 0 24px #ffffff08,0 0 0 48px #ffffff05}.side-info h3{font:800 16px Manrope,sans-serif;margin:0 0 9px;position:relative;z-index:1}.side-info p{font-size:12px;line-height:1.7;color:#bfd0ed;margin:0 0 19px;position:relative;z-index:1}.tip{display:flex;gap:10px;border-top:1px solid #ffffff1c;padding:13px 0;position:relative;z-index:1}.tip-icon{width:28px;height:28px;border-radius:8px;background:#ffffff15;display:grid;place-items:center;flex-shrink:0}.tip strong{display:block;font-size:11px}.tip span{display:block;font-size:10px;color:#b5c8e8;line-height:1.5;margin-top:4px}.empty-state{text-align:center;padding:32px 15px;color:#8996a9}.empty-state .big{font-size:30px;margin-bottom:9px}.empty-state strong{display:block;color:#42516a;margin-bottom:6px}.footer{font-size:10px;color:#9ba6b5;text-align:center;padding:15px 0 0}.mobile-menu{display:none}
@media(max-width:1200px){.stats{grid-template-columns:repeat(2,minmax(0,1fr))}.columns,.section-grid{grid-template-columns:1fr}.sidebar{width:230px}.main{margin-left:230px}.content{padding:27px 24px}.topbar{padding:0 24px}}
@media(max-width:760px){.sidebar{transform:translateX(-100%);transition:.2s}.sidebar.open{transform:translateX(0)}.main{margin-left:0}.topbar{height:68px;padding:0 17px}.mobile-menu{display:grid}.content{padding:24px 16px}.welcome-row{flex-direction:column}.heading{font-size:23px}.stats{gap:10px}.stat-card{padding:15px;min-height:118px}.stat-num{font-size:23px}.top-actions{gap:9px}.profile{padding-left:10px}.profile-copy{display:none}.panel{padding:17px}.form-grid{grid-template-columns:1fr}.field.full{grid-column:auto}.course-options{grid-template-columns:1fr}.toolbar{width:100%}.search{min-width:100%}}
</style>
</head>
<body>
<div class="layout">
<aside class="sidebar" id="sidebar">
  <a class="brand" href="index.php?page=dashboard"><div class="brand-mark">I</div><div><div class="brand-name">INSOFT</div><div class="brand-sub">PANEL INSTITUCIONAL</div></div></a>
  <div class="nav-label">Menú principal</div>
  <nav class="nav">
  <?php foreach ($modules as $key => $item): ?>
    <a class="nav-link <?= $page === $key ? 'active' : '' ?>" href="?page=<?= e($key) ?>"><span class="nav-icon"><?= e($item[1]) ?></span><span><?= e($item[0]) ?></span><?php if ($page === $key): ?><span class="nav-dot"></span><?php endif; ?></a>
  <?php endforeach; ?>
  </nav>
  <div class="sidebar-bottom"><strong>INADI · INSOFT</strong>Administración del portal<br>Panel de gestión institucional</div>
</aside>
<main class="main">
  <header class="topbar">
    <div style="display:flex;align-items:center;gap:13px"><button class="icon-btn mobile-menu" type="button" onclick="document.getElementById('sidebar').classList.toggle('open')" aria-label="Abrir menú">☰</button><div><div class="crumb">Panel administrativo&nbsp; / &nbsp;<b><?= e($pageTitle) ?></b></div><div class="top-title"><?= e($pageTitle) ?></div></div></div>
    <div class="top-actions"><button class="icon-btn" type="button" aria-label="Notificaciones">♧<span class="notice-dot"></span></button><div class="profile"><div class="avatar"><?= e($initials) ?></div><div class="profile-copy"><div class="profile-name"><?= e($adminName) ?></div><div class="profile-role">Administrador</div></div><a class="icon-btn" href="logout.php" title="Cerrar sesión" aria-label="Cerrar sesión">↗</a></div></div>
  </header>
  <div class="content">
  <?php if ($page === 'dashboard'): ?>
    <div class="welcome-row"><div><div class="eyebrow">Tu espacio de gestión</div><h1 class="heading">¡Hola, <?= e($adminName) ?>! 👋</h1><p class="subtitle">Este es el resumen de tu plataforma. Gestioná los contenidos y mantené todo organizado desde un solo lugar.</p></div><a class="btn" href="?page=docentes">＋ &nbsp;Gestionar docentes</a></div>
    <div class="stats">
      <div class="stat-card"><div class="stat-top">Cursos publicados <span class="stat-icon">▤</span></div><div class="stat-num">—</div><div class="stat-foot">Datos conectados por backend</div></div>
      <div class="stat-card"><div class="stat-top">Docentes registrados <span class="stat-icon mint">♙</span></div><div class="stat-num">—</div><div class="stat-foot">Gestión de docentes</div></div>
      <div class="stat-card"><div class="stat-top">Mensajes recibidos <span class="stat-icon amber">✉</span></div><div class="stat-num">—</div><div class="stat-foot">Consultas del portal</div></div>
      <div class="stat-card"><div class="stat-top">Usuarios del sistema <span class="stat-icon violet">◉</span></div><div class="stat-num">—</div><div class="stat-foot">Accesos institucionales</div></div>
    </div>
    <div class="columns"><section class="panel"><div class="panel-head"><div><h2 class="panel-title">Actividad reciente</h2><div class="panel-sub">Un vistazo a las tareas habituales del panel</div></div><span class="status-pill pill-blue">Panel activo</span></div>
      <div class="activity"><div class="activity-icon">♙</div><div class="activity-text"><strong>Gestión de docentes</strong><br>Registrá docentes, asigná cursos y prepará el acceso inicial.<div class="activity-time">Disponible desde el menú Docentes</div></div></div>
      <div class="activity"><div class="activity-icon">▤</div><div class="activity-text"><strong>Administración de cursos</strong><br>Organizá la oferta educativa y mantené actualizada la información.<div class="activity-time">Módulo Cursos</div></div></div>
      <div class="activity"><div class="activity-icon">▣</div><div class="activity-text"><strong>Contenido institucional</strong><br>Gestioná noticias, banners, galería y slides del sitio.<div class="activity-time">Módulos de contenido</div></div></div>
    </section><section class="panel"><div class="panel-head"><div><h2 class="panel-title">Accesos rápidos</h2><div class="panel-sub">Atajos para las tareas más frecuentes</div></div></div><div class="quick-grid">
      <a class="quick" href="?page=docentes"><div class="quick-ico">♙</div><strong>Docentes</strong><span>Registro y asignación de cursos</span></a><a class="quick" href="?page=cursos"><div class="quick-ico">▤</div><strong>Cursos</strong><span>Oferta educativa</span></a><a class="quick" href="?page=noticias"><div class="quick-ico">▣</div><strong>Noticias</strong><span>Publicaciones del sitio</span></a><a class="quick" href="?page=usuarios"><div class="quick-ico">◉</div><strong>Usuarios</strong><span>Accesos al portal</span></a>
    </div></section></div>

  <?php elseif ($page === 'docentes'): ?>
    <div class="welcome-row"><div><div class="eyebrow">Equipo académico</div><h1 class="heading">Gestión de docentes</h1><p class="subtitle">Registrá al equipo docente, asigná sus cursos y prepará sus credenciales de acceso.</p></div><span class="status-pill pill-green">Módulo visual</span></div>
    <div class="section-grid"><section class="panel"><div class="panel-head"><div><h2 class="panel-title">Registrar nuevo docente</h2><div class="panel-sub">Completá los datos para crear una ficha docente</div></div><span class="stat-icon">♙</span></div>
      <!-- Maquetado visual: el backend debe conectar el submit y reemplazar las opciones de ejemplo. -->
      <form id="teacherForm" action="#" method="post" onsubmit="event.preventDefault();document.getElementById('demoNotice').hidden=false;">
        <div class="form-grid">
          <div class="field"><label for="nombre">Nombre y apellido</label><input id="nombre" name="nombre" placeholder="Ej. Ana Martínez" required><small>Nombre que se mostrará en la ficha del docente.</small></div>
          <div class="field"><label for="cedula">Cédula de identidad</label><input id="cedula" name="cedula" inputmode="numeric" placeholder="Ej. 4.567.890-1" required><small>Se utilizará como identificador de inicio de sesión.</small></div>
          <div class="field"><label for="correo">Correo electrónico</label><input id="correo" name="correo" type="email" placeholder="docente@institucion.edu.uy"><small>Para comunicaciones institucionales, si corresponde.</small></div>
          <div class="field"><label for="especialidad">Especialidad / título</label><input id="especialidad" name="especialidad" placeholder="Ej. Informática"></div>
          <div class="field full"><label>Cursos asignados</label><div class="course-options">
            <label class="course-option"><input type="checkbox" name="cursos[]" value="curso_demo_1"><span><strong>Introducción a la informática</strong><small>Curso de ejemplo · ID pendiente de conectar</small></span></label>
            <label class="course-option"><input type="checkbox" name="cursos[]" value="curso_demo_2"><span><strong>Programación web</strong><small>Curso de ejemplo · ID pendiente de conectar</small></span></label>
            <label class="course-option"><input type="checkbox" name="cursos[]" value="curso_demo_3"><span><strong>Base de datos</strong><small>Curso de ejemplo · ID pendiente de conectar</small></span></label>
            <label class="course-option"><input type="checkbox" name="cursos[]" value="curso_demo_4"><span><strong>Diseño de interfaces</strong><small>Curso de ejemplo · ID pendiente de conectar</small></span></label>
          </div><small>El backend deberá cargar los cursos reales desde la tabla curso y guardar sus IDs en docente_curso.</small></div>
          <div class="field full"><label for="password">Contraseña temporal</label><input id="password" name="password_temporal" placeholder="La generará el backend al guardar" readonly><small>Recomendación: generar una contraseña segura en el servidor y exigir su cambio en el primer inicio de sesión.</small></div>
          <div class="field full"><div class="input-note">ⓘ &nbsp; Este formulario es la propuesta visual. El registro, el cifrado de cédula y la generación de contraseña quedan para integrar con el backend.</div></div>
        </div><div id="demoNotice" class="input-note" style="margin-top:14px" hidden>Vista previa solamente: todavía no se guardaron datos. El backend debe conectar este formulario.</div>
        <div class="form-actions"><button class="btn btn-light" type="reset">Limpiar</button><button class="btn" type="submit">＋ &nbsp;Registrar docente</button></div>
      </form>
    </section>
    <aside><div class="side-info"><h3>Acceso docente</h3><p>Un flujo claro para administrar las cuentas docentes y mantener separado su acceso del panel administrativo.</p>
      <div class="tip"><div class="tip-icon">01</div><div><strong>Crear ficha</strong><span>Ingresar datos básicos y cédula del docente.</span></div></div>
      <div class="tip"><div class="tip-icon">02</div><div><strong>Asignar cursos</strong><span>Seleccionar uno o varios cursos existentes.</span></div></div>
      <div class="tip"><div class="tip-icon">03</div><div><strong>Acceso inicial</strong><span>Generar contraseña temporal y solicitar cambio al ingresar.</span></div></div>
    </div>
    <section class="panel" style="margin-top:18px"><div class="panel-head"><div><h2 class="panel-title">Resumen de módulo</h2><div class="panel-sub">Datos pendientes de conexión</div></div></div><div class="activity"><div class="activity-icon">♙</div><div class="activity-text"><strong>Docentes registrados</strong><div class="activity-time">Se mostrará desde la base de datos</div></div></div><div class="activity"><div class="activity-icon">▤</div><div class="activity-text"><strong>Cursos asignados</strong><div class="activity-time">Se mostrará desde docente_curso</div></div></div></section></aside></div>
    <section class="panel"><div class="panel-head"><div><h2 class="panel-title">Docentes registrados</h2><div class="panel-sub">La tabla se conectará con los registros reales cuando se integre el backend.</div></div><div class="toolbar" style="flex:0 1 280px"><div class="search"><input id="teacherSearch" placeholder="Buscar docente (vista previa)" aria-label="Buscar docente"></div></div></div>
      <div class="table-wrap"><table id="teacherTable"><thead><tr><th>Docente</th><th>Cédula</th><th>Especialidad</th><th>Cursos asignados</th><th>Estado</th><th>Acciones</th></tr></thead><tbody><tr id="emptyTeacherRow"><td colspan="6"><div class="empty-state"><div class="big">♙</div><strong>La lista de docentes aparecerá aquí</strong>Cuando se conecte el backend, se cargarán los datos guardados en la base de datos.</div></td></tr></tbody></table></div>
    </section>
  <?php elseif ($page === 'perfil'): ?>
    <div class="welcome-row"><div><div class="eyebrow">Cuenta institucional</div><h1 class="heading">Mi perfil</h1><p class="subtitle">Información de la sesión administrativa actual.</p></div></div><section class="panel" style="max-width:700px"><div class="panel-head"><div><h2 class="panel-title">Datos de la cuenta</h2><div class="panel-sub">Los cambios de perfil deben conectarse al backend.</div></div></div><div class="form-grid"><div class="field"><label>Nombre</label><input value="<?= e($adminName) ?>" readonly></div><div class="field"><label>Rol</label><input value="Administrador" readonly></div></div></section>
  <?php else: ?>
    <div class="welcome-row"><div><div class="eyebrow">Administración del portal</div><h1 class="heading"><?= e($pageTitle) ?></h1><p class="subtitle"><?= e($modules[$page][2]) ?>. Esta pantalla conserva el estilo visual del panel; la funcionalidad de datos se conecta por separado.</p></div><?php if ($page === 'cursos'): ?><a class="btn" href="#">＋ &nbsp;Nuevo curso</a><?php endif; ?></div>
    <section class="panel"><div class="panel-head"><div><h2 class="panel-title"><?= e($pageTitle) ?></h2><div class="panel-sub">Área preparada para integrar las operaciones existentes.</div></div><span class="status-pill pill-blue">Pendiente de conexión</span></div><div class="empty-state"><div class="big"><?= e($modules[$page][1]) ?></div><strong>El módulo <?= e(mb_strtolower($pageTitle, 'UTF-8')) ?> está listo para integrar</strong>Conectá aquí el listado y las acciones CRUD que ya utiliza tu proyecto, sin cambiar el diseño general.</div></section>
  <?php endif; ?>
  <div class="footer">© <?= date('Y') ?> INADI · INSOFT — Panel administrativo</div>
  </div>
</main>
</div>
<script>
// Filtro visual de la tabla de docentes (no reemplaza el filtrado del servidor).
const teacherSearch = document.getElementById('teacherSearch');
if (teacherSearch) teacherSearch.addEventListener('input', function(){
  const q = this.value.toLocaleLowerCase('es');
  document.querySelectorAll('#teacherTable tbody tr:not(#emptyTeacherRow)').forEach(row => {
    row.hidden = !row.innerText.toLocaleLowerCase('es').includes(q);
  });
});
</script>
</body>
</html>

<?php
/**
 * INSOFT / INADI - Panel administrativo (plantilla visual)
 * Integración backend pendiente: conectar los formularios a las acciones del proyecto.
 * Mantiene el control de sesión admin_id y reutiliza config.php si está disponible.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$configPath = __DIR__ . '/config.php';
if (file_exists($configPath)) {
    require_once $configPath; // Se espera que config.php inicialice $pdo.
}

$modules = [
    'dashboard' => ['Inicio', '▦', 'Resumen general'],
    'cursos'    => ['Cursos', '▤', 'Administrá la oferta educativa'],
    'panel_docente' => ['Panel docente', '▦', 'Resumen y accesos del área docente'],
    'docentes'  => ['Gestión de docentes', '♙', 'Docentes y cursos asignados'],
    'servicios' => ['Servicios', '▤', 'Gestión de servicios ofrecidos'],
    'estadisticas' => ['Estadísticas', '▥', 'Visitas y contrataciones'],
    'reportes' => ['Reportes de actividad', '▧', 'Resumen de actividad administrativa'],
    'analiticas' => ['Panel de analíticas', '▥', 'Indicadores y gráficos del portal'],
    'solicitudes_docentes' => ['Contratación docente', '✉', 'Solicitudes de contratación docente'],
    'historial_docente' => ['Historial docente', '☷', 'Historial de actividad y asignaciones docentes'],
    'historial_contrataciones' => ['Historial de contrataciones', '▤', 'Seguimiento de contrataciones docentes'],
    'pagos' => ['Comprobantes de pago', '＄', 'Confirmaciones y comprobantes de pago'],
    'panel_estudiante' => ['Panel estudiante', '▦', 'Resumen y accesos del área estudiantil'],
    'estudiantes' => ['Registrar estudiantes', '♙', 'Registro y administración de estudiantes'],
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
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font-family:'DM Sans',sans-serif;font-size:14px}a{text-decoration:none;color:inherit}button,input,select,textarea{font:inherit}.layout{min-height:100vh}.sidebar{position:fixed;inset:0 auto 0 0;width:286px;background:linear-gradient(180deg,#102a56 0%,#122447 100%);color:#d8e4fa;padding:25px 15px 18px;display:flex;flex-direction:column;z-index:10}.brand{display:flex;align-items:center;gap:12px;padding:0 12px 28px;border-bottom:1px solid rgba(255,255,255,.12)}.brand-mark{width:42px;height:42px;display:grid;place-items:center;border-radius:13px;background:#fff;color:var(--navy);font-family:Manrope,sans-serif;font-weight:800;font-size:19px;box-shadow:0 7px 18px #081b3b55}.brand-name{font-family:Manrope,sans-serif;font-size:16px;font-weight:800;color:#fff;letter-spacing:.3px}.brand-sub{font-size:10px;color:#9db5dc;letter-spacing:1.6px;margin-top:3px}.nav-label{font-size:10px;text-transform:uppercase;letter-spacing:1.6px;color:#8da7d1;font-weight:700;padding:24px 13px 10px}.nav{display:flex;flex-direction:column;gap:5px;overflow-y:auto}.nav-link{display:flex;align-items:center;gap:12px;padding:11px 13px;border-radius:11px;color:#c0d0eb;transition:.18s;font-weight:500}.nav-link:hover{background:#ffffff12;color:white}.nav-link.active{background:linear-gradient(100deg,#3777ed,#2d61c8);color:white;box-shadow:0 8px 18px #071a3a55}.nav-icon{width:22px;text-align:center;font-size:18px}.nav-link span:last-child{flex:1}.nav-dot{width:6px;height:6px;border-radius:50%;background:#8ab4ff}.nav-group{border-radius:11px;overflow:hidden}.nav-group summary{list-style:none;cursor:pointer;display:flex;align-items:center;gap:12px;padding:11px 13px;border-radius:11px;color:#c0d0eb;font-weight:600;transition:.18s;user-select:none}.nav-group summary::-webkit-details-marker{display:none}.nav-group summary:hover,.nav-group[open] summary{background:#ffffff12;color:white}.nav-group summary .nav-chevron{margin-left:auto;font-size:12px;transition:transform .18s}.nav-group[open] summary .nav-chevron{transform:rotate(180deg)}.nav-submenu{display:flex;flex-direction:column;gap:3px;padding:4px 0 5px 15px}.nav-submenu .nav-link{font-size:12px;padding:10px 11px;gap:9px}.nav-submenu .nav-icon{font-size:15px;width:19px}.profile{position:relative;display:flex;align-items:center;gap:10px;padding-left:18px;border-left:1px solid var(--line)}.profile-trigger{display:flex;align-items:center;gap:10px;border:0;background:transparent;color:inherit;cursor:pointer;padding:5px;border-radius:10px;text-align:left}.profile-trigger:hover{background:#f5f7fb}.profile-chevron{font-size:11px;color:#8b98aa;margin-left:2px}.profile-menu{position:absolute;right:0;top:calc(100% + 12px);width:205px;padding:7px;background:#fff;border:1px solid var(--line);border-radius:13px;box-shadow:0 16px 38px #17274420;z-index:30}.profile-menu a{display:flex;align-items:center;gap:10px;padding:11px 12px;border-radius:8px;color:#40516b;font-size:12px;font-weight:600}.profile-menu a:hover{background:#f2f6ff;color:var(--blue)}.profile-menu .menu-divider{height:1px;background:var(--line);margin:5px 4px}.sidebar-bottom{margin-top:auto;padding:16px 12px 2px;border-top:1px solid #ffffff18;color:#9db1d2;font-size:11px;line-height:1.7}.sidebar-bottom strong{display:block;color:#e6eeff;font-size:12px}.main{margin-left:286px;min-height:100vh}.topbar{height:76px;background:#fff;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:0 35px;position:sticky;top:0;z-index:5}.crumb{font-size:12px;color:#8a97aa;margin-bottom:4px}.crumb b{color:#53637a;font-weight:600}.top-title{font:700 16px Manrope,sans-serif;color:var(--ink)}.top-actions{display:flex;align-items:center;gap:18px}.icon-btn{width:38px;height:38px;border:1px solid var(--line);background:#fff;border-radius:11px;display:grid;place-items:center;color:#63718a;position:relative}.notice-dot{position:absolute;right:8px;top:7px;width:6px;height:6px;background:#ef6a6a;border-radius:50%;border:1px solid white}.profile{display:flex;align-items:center;gap:10px;padding-left:18px;border-left:1px solid var(--line)}.avatar{width:37px;height:37px;border-radius:12px;background:#e6edff;color:#285bc2;display:grid;place-items:center;font-weight:800}.profile-name{font-size:12px;font-weight:700}.profile-role{font-size:11px;color:var(--muted);margin-top:3px}.content{padding:31px 35px 45px;max-width:1600px;margin:auto}.welcome-row{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:27px}.eyebrow{font-size:11px;text-transform:uppercase;letter-spacing:1.7px;color:var(--blue);font-weight:800;margin-bottom:9px}.heading{font:800 27px Manrope,sans-serif;letter-spacing:-.7px;margin:0;color:#172744}.subtitle{color:var(--muted);margin:9px 0 0;font-size:13px;line-height:1.6}.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:10px;padding:11px 15px;background:var(--blue);color:#fff;font-weight:700;box-shadow:0 7px 16px #2f68df26;cursor:pointer;transition:.18s}.btn:hover{background:#2458c6;transform:translateY(-1px)}.btn-light{background:#fff;border:1px solid var(--line);color:#465873;box-shadow:none}.btn-light:hover{background:#f8faff}.btn-sm{padding:8px 11px;font-size:12px;border-radius:8px}.stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:17px;margin-bottom:23px}.stat-card,.panel{background:#fff;border:1px solid #e9eef6;border-radius:16px;box-shadow:var(--shadow)}.stat-card{padding:19px 20px;min-height:130px}.stat-top{display:flex;align-items:center;justify-content:space-between;color:#75839a;font-size:12px;font-weight:600}.stat-icon{width:37px;height:37px;border-radius:11px;display:grid;place-items:center;font-size:17px;background:#edf3ff;color:#3169d9}.stat-icon.mint{background:#e6f8f1;color:#1b9870}.stat-icon.amber{background:#fff4e5;color:#bd791c}.stat-icon.violet{background:#f1edff;color:#7959ca}.stat-num{font:800 27px Manrope,sans-serif;margin-top:12px;letter-spacing:-.7px}.stat-foot{font-size:11px;color:#8b98aa;margin-top:4px}.stat-foot .positive{color:#15946c;font-weight:700}.columns{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(280px,.85fr);gap:20px}.panel{padding:22px;margin-bottom:20px}.panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px}.panel-title{font:800 15px Manrope,sans-serif;margin:0}.panel-sub{font-size:11px;color:var(--muted);margin-top:5px}.text-link{color:var(--blue);font-weight:700;font-size:12px}.activity{display:flex;align-items:flex-start;gap:13px;padding:14px 0;border-bottom:1px solid #f0f3f8}.activity:last-child{border-bottom:0;padding-bottom:0}.activity-icon{width:36px;height:36px;border-radius:11px;background:#eef3ff;color:#3468d3;display:grid;place-items:center;flex-shrink:0}.activity-text{font-size:12px;line-height:1.55;color:#465570}.activity-text strong{color:#1d2c45}.activity-time{font-size:10px;color:#9aa5b5;margin-top:4px}.status-pill{display:inline-flex;align-items:center;gap:6px;border-radius:30px;padding:6px 9px;font-size:10px;font-weight:800;white-space:nowrap}.status-pill:before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor}.pill-green{background:#e8f8f1;color:#168763}.pill-blue{background:#edf3ff;color:#3269d6}.pill-orange{background:#fff4e5;color:#b87519}.quick-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}.quick{border:1px solid var(--line);border-radius:12px;padding:15px;transition:.18s}.quick:hover{border-color:#b9cdf9;background:#f8faff}.quick-ico{font-size:19px;color:var(--blue);margin-bottom:12px}.quick strong{display:block;font-size:12px}.quick span{display:block;color:var(--muted);font-size:11px;margin-top:5px;line-height:1.5}.toolbar{display:flex;gap:10px;align-items:center;flex-wrap:wrap}.search{position:relative;min-width:210px;flex:1}.search input{width:100%;border:1px solid var(--line);border-radius:10px;padding:11px 13px 11px 37px;outline:none;background:#fff;color:var(--ink)}.search:before{content:'⌕';position:absolute;left:13px;top:7px;font-size:21px;color:#8b99ad}.search input:focus,.field input:focus,.field select:focus,.field textarea:focus{border-color:#9cb9fa;box-shadow:0 0 0 3px #2f68df12;outline:none}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;white-space:nowrap}th{text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:#8b98aa;padding:12px 13px;background:#f8faff;font-weight:800}td{padding:15px 13px;border-bottom:1px solid #edf1f6;font-size:12px;color:#526078}tbody tr:last-child td{border-bottom:0}tbody tr:hover{background:#fbfcff}.person{display:flex;align-items:center;gap:10px}.mini-avatar{width:34px;height:34px;border-radius:10px;background:#eaf0ff;color:#2c61c8;display:grid;place-items:center;font-weight:800}.person strong{display:block;color:#24334d;font-size:12px}.person small{display:block;color:#929eb0;font-size:10px;margin-top:4px}.course-tags{display:flex;gap:5px;flex-wrap:wrap}.course-tag{background:#f0f4fc;color:#526a91;padding:5px 7px;border-radius:6px;font-size:10px;font-weight:700}.actions{display:flex;gap:6px}.action-btn{width:29px;height:29px;border:1px solid var(--line);border-radius:8px;background:#fff;color:#687892;display:grid;place-items:center;cursor:pointer}.action-btn:hover{border-color:#adc4f8;color:var(--blue)}.section-grid{display:grid;grid-template-columns:minmax(0,1.4fr) minmax(280px,.8fr);gap:20px;align-items:start}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px}.field{display:flex;flex-direction:column;gap:7px}.field.full{grid-column:1/-1}.field label{font-size:11px;font-weight:800;color:#53637a}.field input,.field select,.field textarea{width:100%;border:1px solid #e1e8f2;border-radius:9px;padding:11px 12px;color:#33445e;background:#fff;outline:none}.field input::placeholder,.field textarea::placeholder{color:#a8b2c1}.field small{font-size:10px;color:#929eb0;line-height:1.5}.input-note{display:flex;align-items:center;gap:7px;padding:11px 12px;border-radius:9px;background:#f2f6ff;color:#59709a;font-size:11px;line-height:1.5}.course-options{display:grid;grid-template-columns:1fr 1fr;gap:9px}.course-option{display:flex;align-items:flex-start;gap:9px;padding:12px;border:1px solid var(--line);border-radius:10px;cursor:pointer}.course-option:has(input:checked){border-color:#8eaff8;background:#f5f8ff}.course-option input{accent-color:var(--blue);margin-top:2px}.course-option strong{display:block;font-size:11px}.course-option small{display:block;font-size:10px;color:var(--muted);margin-top:4px}.form-actions{display:flex;justify-content:flex-end;gap:9px;margin-top:20px;padding-top:17px;border-top:1px solid #eef2f7}.side-info{background:linear-gradient(145deg,#173b76,#10264d);border-radius:16px;padding:23px;color:#fff;overflow:hidden;position:relative}.side-info:after{content:'';position:absolute;width:180px;height:180px;border:1px solid #ffffff16;border-radius:50%;right:-65px;top:-55px;box-shadow:0 0 0 24px #ffffff08,0 0 0 48px #ffffff05}.side-info h3{font:800 16px Manrope,sans-serif;margin:0 0 9px;position:relative;z-index:1}.side-info p{font-size:12px;line-height:1.7;color:#bfd0ed;margin:0 0 19px;position:relative;z-index:1}.tip{display:flex;gap:10px;border-top:1px solid #ffffff1c;padding:13px 0;position:relative;z-index:1}.tip-icon{width:28px;height:28px;border-radius:8px;background:#ffffff15;display:grid;place-items:center;flex-shrink:0}.tip strong{display:block;font-size:11px}.tip span{display:block;font-size:10px;color:#b5c8e8;line-height:1.5;margin-top:4px}.empty-state{text-align:center;padding:32px 15px;color:#8996a9}.empty-state .big{font-size:30px;margin-bottom:9px}.empty-state strong{display:block;color:#42516a;margin-bottom:6px}.footer{font-size:10px;color:#9ba6b5;text-align:center;padding:15px 0 0}.mobile-menu{display:none}
.notification-dropdown{position:relative}.notification-dropdown>summary{list-style:none;cursor:pointer}.notification-dropdown>summary::-webkit-details-marker{display:none}.notification-trigger{font-size:19px}.notice-count{position:absolute;top:-5px;right:-5px;min-width:17px;height:17px;padding:0 4px;border-radius:10px;background:#ef5261;color:#fff;border:2px solid #fff;font-size:9px;font-weight:800;display:grid;place-items:center}.notification-panel{position:absolute;right:-42px;top:calc(100% + 13px);width:340px;max-width:calc(100vw - 28px);background:#fff;border:1px solid #e5ebf4;border-radius:16px;box-shadow:0 18px 45px #1224472b;z-index:40;overflow:hidden;color:#24334d}.notification-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:17px;border-bottom:1px solid #edf1f7}.notification-head strong{display:block;font-size:14px}.notification-head small{display:block;color:#8a97aa;font-size:10px;margin-top:4px}.notification-mark,.notification-foot button{border:0;background:transparent;color:#3169d9;font-size:10px;font-weight:800;cursor:pointer}.notification-list{max-height:310px;overflow-y:auto}.notification-item{display:flex;gap:11px;padding:14px 16px;border-bottom:1px solid #f0f3f8}.notification-item.unread{background:#f5f8ff}.notification-symbol{width:34px;height:34px;flex-shrink:0;border-radius:10px;background:#eaf1ff;color:#3269d6;display:grid;place-items:center}.notification-copy{flex:1;min-width:0}.notification-copy strong{display:block;font-size:11px;margin-bottom:4px}.notification-copy p{font-size:11px;line-height:1.5;color:#687791;margin:0}.notification-copy time{display:block;font-size:9px;color:#9aa5b5;margin-top:6px}.notification-read{align-self:center;border:1px solid #e3e9f3;background:white;border-radius:7px;padding:5px 7px;color:#536782;font-size:9px;cursor:pointer}.notification-foot{display:flex;justify-content:space-between;align-items:center;padding:11px 16px;color:#9aa5b5;font-size:9px}.notification-empty{padding:24px;text-align:center;color:#8a97aa;font-size:12px}.nav-submenu .nav-link{white-space:normal;line-height:1.35}.nav-group summary span:nth-child(2),.nav-link span:last-child{overflow-wrap:anywhere}
@media(max-width:1200px){.stats{grid-template-columns:repeat(2,minmax(0,1fr))}.columns,.section-grid{grid-template-columns:1fr}.sidebar{width:270px}.main{margin-left:270px}.content{padding:27px 24px}.topbar{padding:0 24px}}
@media(max-width:760px){.sidebar{transform:translateX(-100%);transition:.2s}.sidebar.open{transform:translateX(0)}.main{margin-left:0}.topbar{height:68px;padding:0 17px}.mobile-menu{display:grid}.content{padding:24px 16px}.welcome-row{flex-direction:column}.heading{font-size:23px}.stats{gap:10px}.stat-card{padding:15px;min-height:118px}.stat-num{font-size:23px}.top-actions{gap:9px}.profile{padding-left:10px}.profile-copy{display:none}.panel{padding:17px}.form-grid{grid-template-columns:1fr}.field.full{grid-column:auto}.course-options{grid-template-columns:1fr}.toolbar{width:100%}.search{min-width:100%}}

.records-actions{display:flex;gap:6px;align-items:center}
.record-action{width:31px;height:31px;border:1px solid var(--line);border-radius:8px;background:#fff;color:#52627a;cursor:pointer}
.record-action:hover{border-color:#adc4f8;color:var(--blue)}
.record-action.danger{color:#bd4c59}
.record-name{display:flex;align-items:center;gap:10px;white-space:normal;min-width:180px}
.record-symbol{width:35px;height:35px;flex:0 0 35px;display:grid;place-items:center;background:#edf3ff;color:#3169d9;border-radius:10px;font-weight:800}
.record-name strong{display:block;color:#24334d;font-size:12px}
.record-name small{display:block;color:#929eb0;font-size:10px;margin-top:4px}
#recordEditor textarea,#recordDetails textarea{resize:vertical}
[hidden]{display:none!important}
.chart-bars{height:230px;display:flex;align-items:flex-end;justify-content:space-around;gap:15px;padding:20px 12px 0;border-bottom:1px solid var(--line);background:repeating-linear-gradient(to bottom,transparent 0,transparent 48px,#eef2f8 49px,#eef2f8 50px)}.chart-bar-group{height:100%;flex:1;display:flex;flex-direction:column;justify-content:flex-end;align-items:center;gap:9px;color:#8a97aa;font-size:11px}.chart-bars-inner{height:190px;display:flex;align-items:flex-end;gap:6px;width:100%;justify-content:center}.chart-bars-inner i,.chart-bars-inner b{display:block;width:min(28px,35%);border-radius:6px 6px 0 0;background:#3975e8}.chart-bars-inner b{background:#42b99a}.chart-legend{display:flex;gap:20px;padding-top:15px;color:#6d7b91;font-size:11px}.chart-legend span{display:flex;align-items:center;gap:7px}.chart-legend i{width:9px;height:9px;border-radius:3px;background:#3975e8}.chart-legend span+span i{background:#42b99a}[hidden]{display:none!important}

/* Ajustes visuales y navegación agrupada */
.brand{gap:11px;padding:0 9px 24px}
.brand-mark{width:54px;height:54px;border-radius:16px;background:linear-gradient(145deg,#fff 0%,#dbeafe 100%);color:#1846a0;box-shadow:0 8px 22px #06152d66;position:relative;font-size:0;flex:0 0 54px}
.brand-mark:before{content:"IN";font:800 19px Manrope,sans-serif;letter-spacing:-1px;color:#1c4ca4}
.brand-mark:after{content:"ADI";position:absolute;bottom:8px;right:6px;font:800 8px Manrope,sans-serif;letter-spacing:.5px;color:#e84a66;background:#fff;border-radius:4px;padding:1px 3px}
.brand-name{font-size:17px;letter-spacing:.7px}.brand-sub{font-size:9px;letter-spacing:1.25px}
.nav-group{display:block;flex:0 0 auto;overflow:visible}
.nav-group summary{display:flex;width:100%;min-height:43px;position:relative;z-index:1}
.nav-group[open] summary{background:linear-gradient(100deg,#244f91,#1d3b6c);color:#fff;border-radius:11px 11px 8px 8px}
.nav-group[open] .nav-submenu{display:flex!important;visibility:visible!important;opacity:1!important;height:auto!important;overflow:visible!important}
.nav-submenu{margin:4px 0 5px 10px;padding:5px 0 5px 11px;border-left:2px solid #4d86e8;display:flex!important;flex-direction:column;gap:3px}
.nav-submenu .nav-link{display:flex!important;min-height:38px;border-radius:9px;color:#c6d7f3}
.nav-submenu .nav-link.active{background:linear-gradient(100deg,#3777ed,#2d61c8);color:#fff}
.nav-group summary .nav-chevron{margin-left:auto}
.topbar{background:linear-gradient(112deg,#163d7b 0%,#245bc1 48%,#6b4bd1 100%);border-bottom:0;box-shadow:0 7px 22px #1b3f7b24;color:#fff}
.crumb{color:#dce8ff}.crumb b,.top-title{color:#fff}.top-title{font-size:17px}
.topbar .icon-btn{background:#ffffff18;border:1px solid #ffffff50;color:#fff;backdrop-filter:blur(8px)}
.topbar .icon-btn:hover{background:#ffffff2c}
.topbar .profile{border-left-color:#ffffff45}
.topbar .profile-trigger{color:#fff}.topbar .profile-trigger:hover{background:#ffffff18}
.topbar .profile-role{color:#dce6ff}.topbar .profile-chevron{color:#fff}
.topbar .avatar{background:linear-gradient(135deg,#fff,#dce8ff);color:#214ca4;box-shadow:0 4px 12px #102d6940}
.topbar .notice-dot{background:#ffcf56;border-color:#fff}
.profile-dropdown{position:relative}.profile-dropdown summary{list-style:none}.profile-dropdown summary::-webkit-details-marker{display:none}
.profile-dropdown[open] .profile-trigger{background:#ffffff1f}
.panel-shortcut-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.panel-shortcut{display:block;padding:20px;border:1px solid #e3eaf6;border-radius:15px;background:linear-gradient(145deg,#fff,#f5f8ff);transition:.2s}
.panel-shortcut:hover{transform:translateY(-3px);border-color:#9cb9fa;box-shadow:0 12px 25px #245bc11a}
.panel-shortcut .shortcut-icon{width:42px;height:42px;display:grid;place-items:center;border-radius:13px;background:linear-gradient(135deg,#2f68df,#6d58d6);color:#fff;font-size:20px;margin-bottom:14px}
.panel-shortcut strong{display:block;color:#1d2c45;font-size:13px}.panel-shortcut span{display:block;color:#7a879b;font-size:11px;line-height:1.55;margin-top:6px}
@media(max-width:760px){.panel-shortcut-grid{grid-template-columns:1fr}.topbar{background:linear-gradient(112deg,#163d7b,#245bc1,#6b4bd1)}}

/* Accesibilidad: menú lateral más ancho, contraíble y tipografía más legible */
.sidebar{width:320px;padding-left:17px;padding-right:17px;transition:width .22s ease,transform .22s ease}
.main{margin-left:320px;transition:margin-left .22s ease}
.nav-label{font-size:12px;padding-top:25px}
.nav-link,.nav-group summary{font-size:15px;min-height:47px;padding-top:13px;padding-bottom:13px}
.nav-submenu .nav-link{font-size:14px;min-height:42px}
.brand-name{font-size:19px}.brand-sub{font-size:10px}
.sidebar-bottom{font-size:13px}.sidebar-bottom strong{font-size:14px}
.mobile-menu{display:grid;flex:0 0 42px;cursor:pointer}
body.sidebar-collapsed .sidebar{width:86px;padding-left:12px;padding-right:12px}
body.sidebar-collapsed .main{margin-left:86px}
body.sidebar-collapsed .brand{justify-content:center;padding-left:0;padding-right:0}
body.sidebar-collapsed .brand>div:last-child,body.sidebar-collapsed .nav-label,body.sidebar-collapsed .nav-link span:last-child,body.sidebar-collapsed .nav-group summary span:nth-child(2),body.sidebar-collapsed .nav-group summary .nav-chevron,body.sidebar-collapsed .sidebar-bottom{display:none!important}
body.sidebar-collapsed .nav-link,body.sidebar-collapsed .nav-group summary{justify-content:center;padding-left:8px;padding-right:8px}
body.sidebar-collapsed .nav-icon{width:auto;font-size:20px}
body.sidebar-collapsed .nav-submenu{display:none!important}
.topbar{height:82px;padding-left:30px;padding-right:30px}
.crumb{font-size:14px}.top-title{font-size:20px}
.profile-name{font-size:15px}.profile-role{font-size:13px}
.notification-head strong{font-size:16px}.notification-head small{font-size:12px}
.notification-copy strong{font-size:13px}.notification-copy p{font-size:13px}.notification-copy time{font-size:11px}
.content{padding:35px 38px 48px}
.heading{font-size:31px}.subtitle{font-size:15px}
.eyebrow{font-size:12px}.btn{font-size:15px}
.stat-top{font-size:14px}.stat-num{font-size:30px}.stat-foot{font-size:13px}
.panel-title{font-size:18px}.panel-sub{font-size:13px}
.activity-text{font-size:14px}.activity-time{font-size:12px}
.quick strong{font-size:14px}.quick span{font-size:13px}
th{font-size:12px}td{font-size:14px}.person strong{font-size:14px}.person small{font-size:12px}
.field label{font-size:13px}.field input,.field select,.field textarea{font-size:14px}.field small{font-size:12px}
.text-link{font-size:14px}.status-pill{font-size:12px}.footer{font-size:12px}
.panel-shortcut strong{font-size:15px}.panel-shortcut span{font-size:13px}
@media(max-width:1200px){.sidebar{width:300px}.main{margin-left:300px}body.sidebar-collapsed .sidebar{width:86px}body.sidebar-collapsed .main{margin-left:86px}.content{padding:30px 25px}}
@media(max-width:760px){.sidebar{width:320px;transform:translateX(-100%)}.sidebar.open{transform:translateX(0)}.main,body.sidebar-collapsed .main{margin-left:0}.topbar{height:76px;padding:0 14px}.content{padding:25px 16px}.heading{font-size:26px}.crumb{font-size:12px}.top-title{font-size:18px}.profile-name{font-size:13px}.profile-role{font-size:11px}body.sidebar-collapsed .sidebar{width:320px}.mobile-menu{display:grid}}

</style>
</head>
<body>
<div class="layout">
<aside class="sidebar" id="sidebar">
  <a class="brand" href="index.php?page=dashboard" aria-label="INADI INSOFT - Inicio"><div class="brand-mark" aria-hidden="true">INADI</div><div><div class="brand-name">INADI</div><div class="brand-sub">PLATAFORMA INSOFT</div></div></a>
  <div class="nav-label">Menú principal</div>
  <nav class="nav">
    <?php
    $renderNavLink = static function (string $key) use ($modules, $page): void {
        $item = $modules[$key];
        echo '<a class="nav-link ' . ($page === $key ? 'active' : '') . '" href="?page=' . e($key) . '"><span class="nav-icon">' . e($item[1]) . '</span><span>' . e($item[0]) . '</span>';
        if ($page === $key) echo '<span class="nav-dot"></span>';
        echo '</a>';
    };
    ?>
    <?php $renderNavLink('dashboard'); ?>
    <details class="nav-group" <?= in_array($page, ['panel_docente', 'docentes', 'solicitudes_docentes', 'historial_docente', 'historial_contrataciones'], true) ? 'open' : '' ?>>
      <summary><span class="nav-icon">♙</span><span>Docente</span><span class="nav-chevron">⌄</span></summary>
      <div class="nav-submenu">
        <?php $renderNavLink('panel_docente'); ?>
        <?php $renderNavLink('docentes'); ?>
        <?php $renderNavLink('solicitudes_docentes'); ?>
        <?php $renderNavLink('historial_docente'); ?>
        <?php $renderNavLink('historial_contrataciones'); ?>
      </div>
    </details>
    <details class="nav-group" <?= in_array($page, ['panel_estudiante', 'estudiantes', 'pagos'], true) ? 'open' : '' ?>>
      <summary><span class="nav-icon">♧</span><span>Estudiante</span><span class="nav-chevron">⌄</span></summary>
      <div class="nav-submenu">
        <?php $renderNavLink('panel_estudiante'); ?>
        <?php $renderNavLink('estudiantes'); ?>
        <?php $renderNavLink('pagos'); ?>
      </div>
    </details>
    <?php foreach (['cursos', 'servicios', 'estadisticas', 'reportes', 'analiticas', 'noticias', 'banners', 'galeria', 'empresas', 'comentarios', 'formularios', 'slides', 'usuarios', 'ajustes'] as $key) $renderNavLink($key); ?>
  </nav>
  <div class="sidebar-bottom"><strong>INADI · INSOFT</strong>Administración del portal<br>Panel de gestión institucional</div>
</aside>
<main class="main">
  <header class="topbar">
    <div style="display:flex;align-items:center;gap:13px"><button class="icon-btn mobile-menu sidebar-toggle" type="button" onclick="toggleSidebar()" aria-label="Mostrar u ocultar menú lateral" title="Mostrar u ocultar menú lateral">☰</button><div><div class="crumb">Panel administrativo&nbsp; / &nbsp;<b><?= e($pageTitle) ?></b></div><div class="top-title"><?= e($pageTitle) ?></div></div></div>
    <div class="top-actions"><details class="notification-dropdown"><summary class="icon-btn notification-trigger" aria-label="Abrir notificaciones" title="Notificaciones">♧<span class="notice-dot" id="noticeDot"></span><span class="notice-count" id="noticeCount">3</span></summary><section class="notification-panel"><div class="notification-head"><div><strong>Notificaciones</strong><small id="notificationSummary">Tenés novedades pendientes</small></div><button type="button" class="notification-mark" id="markAllRead">Marcar todas leídas</button></div><div id="notificationList" class="notification-list"></div><div class="notification-foot"><span>Notificaciones de demostración</span><button type="button" id="resetNotifications">Restablecer</button></div></section></details><div class="profile"><details class="profile-dropdown"><summary class="profile-trigger"><span class="avatar"><?= e($initials) ?></span><span class="profile-copy"><span class="profile-name" style="display:block"><?= e($adminName) ?></span><span class="profile-role" style="display:block">Administrador</span></span><span class="profile-chevron">⌄</span></summary><div class="profile-menu"><a href="login.php"><span>↪</span><span>Iniciar sesión</span></a><a href="?page=perfil"><span>◉</span><span>Perfil</span></a><div class="menu-divider"></div><a href="logout.php"><span>↗</span><span>Cerrar sesión</span></a></div></details></div></div>
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

  <?php elseif (in_array($page, ['panel_docente', 'panel_estudiante'], true)): ?>
    <?php $isTeacherPanel = $page === 'panel_docente'; ?>
    <div class="welcome-row"><div><div class="eyebrow">INADI · INSOFT</div><h1 class="heading"><?= $isTeacherPanel ? 'Panel docente' : 'Panel estudiante' ?></h1><p class="subtitle"><?= $isTeacherPanel ? 'Accedé a la gestión docente, las solicitudes de contratación y el historial.' : 'Accedé al registro de estudiantes y a los comprobantes de pago.' ?></p></div><span class="status-pill pill-blue">Área de gestión</span></div>
    <div class="panel-shortcut-grid">
      <?php if ($isTeacherPanel): ?>
        <a class="panel-shortcut" href="?page=docentes"><div class="shortcut-icon">♙</div><strong>Gestión de docentes</strong><span>Registrar y consultar las fichas de docentes.</span></a>
        <a class="panel-shortcut" href="?page=solicitudes_docentes"><div class="shortcut-icon">✉</div><strong>Contratación docente</strong><span>Consultar solicitudes y su estado.</span></a>
        <a class="panel-shortcut" href="?page=historial_docente"><div class="shortcut-icon">☷</div><strong>Historial docente</strong><span>Ver actividad y asignaciones docentes.</span></a>
      <?php else: ?>
        <a class="panel-shortcut" href="?page=estudiantes"><div class="shortcut-icon">♧</div><strong>Registrar estudiantes</strong><span>Acceder al registro y administración de estudiantes.</span></a>
        <a class="panel-shortcut" href="?page=pagos"><div class="shortcut-icon">＄</div><strong>Comprobantes de pago</strong><span>Consultar comprobantes y confirmaciones.</span></a>
        <a class="panel-shortcut" href="?page=cursos"><div class="shortcut-icon">▤</div><strong>Cursos</strong><span>Consultar la oferta educativa del portal.</span></a>
      <?php endif; ?>
    </div>
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
  <?php elseif (in_array($page, ['estadisticas','analiticas','reportes'], true)): ?>
    <div class="welcome-row"><div><div class="eyebrow">Indicadores del sistema</div><h1 class="heading"><?= e($pageTitle) ?></h1><p class="subtitle">Vista demostrativa de indicadores. Los valores son de ejemplo y deberán alimentarse con datos reales del backend.</p></div><button class="btn btn-light" type="button" onclick="window.print()">Imprimir reporte</button></div>
    <div class="stats">
      <div class="stat-card"><div class="stat-top">Visitas del mes <span class="stat-icon">◉</span></div><div class="stat-num">12.840</div><div class="stat-foot"><span class="positive">+8,4%</span> frente al mes anterior</div></div>
      <div class="stat-card"><div class="stat-top">Contrataciones <span class="stat-icon mint">✓</span></div><div class="stat-num">186</div><div class="stat-foot">Solicitudes confirmadas · demo</div></div>
      <div class="stat-card"><div class="stat-top">Solicitudes pendientes <span class="stat-icon amber">◷</span></div><div class="stat-num">24</div><div class="stat-foot">Requieren revisión administrativa</div></div>
      <div class="stat-card"><div class="stat-top">Tasa de conversión <span class="stat-icon violet">↗</span></div><div class="stat-num">4,8%</div><div class="stat-foot">Visitas que generan una solicitud · demo</div></div>
    </div>
    <div class="columns"><section class="panel"><div class="panel-head"><div><h2 class="panel-title">Actividad mensual</h2><div class="panel-sub">Visitas y contrataciones de ejemplo</div></div><span class="status-pill pill-blue">Últimos 6 meses</span></div>
      <div class="chart-bars" role="img" aria-label="Gráfico de barras ilustrativo de actividad mensual"><div class="chart-bar-group"><div class="chart-bars-inner"><i style="height:38%"></i><b style="height:22%"></b></div><span>Abr</span></div><div class="chart-bar-group"><div class="chart-bars-inner"><i style="height:48%"></i><b style="height:29%"></b></div><span>May</span></div><div class="chart-bar-group"><div class="chart-bars-inner"><i style="height:57%"></i><b style="height:36%"></b></div><span>Jun</span></div><div class="chart-bar-group"><div class="chart-bars-inner"><i style="height:45%"></i><b style="height:31%"></b></div><span>Jul</span></div><div class="chart-bar-group"><div class="chart-bars-inner"><i style="height:72%"></i><b style="height:48%"></b></div><span>Ago</span></div><div class="chart-bar-group"><div class="chart-bars-inner"><i style="height:88%"></i><b style="height:62%"></b></div><span>Set</span></div></div>
      <div class="chart-legend"><span><i></i> Visitas</span><span><i></i> Contrataciones</span></div>
    </section><section class="panel"><div class="panel-head"><div><h2 class="panel-title">Resumen de actividad</h2><div class="panel-sub">Datos ilustrativos</div></div></div>
      <div class="activity"><div class="activity-icon">♙</div><div class="activity-text"><strong>Altas de estudiantes</strong><div class="activity-time">48 registros durante el período</div></div></div><div class="activity"><div class="activity-icon">✉</div><div class="activity-text"><strong>Solicitudes docentes</strong><div class="activity-time">12 solicitudes por revisar</div></div></div><div class="activity"><div class="activity-icon">＄</div><div class="activity-text"><strong>Pagos confirmados</strong><div class="activity-time">Estado a verificar con la pasarela o el registro real</div></div></div>
    </section></div>
    <section class="panel"><div class="panel-head"><div><h2 class="panel-title">Últimos eventos</h2><div class="panel-sub">Ejemplo de reporte de actividad administrativa</div></div></div><div class="table-wrap"><table><thead><tr><th>Fecha</th><th>Actividad</th><th>Módulo</th><th>Estado</th></tr></thead><tbody><tr><td>26/10/2026</td><td>Alta de estudiante (demostración)</td><td>Estudiantes</td><td><span class="status-pill pill-blue">Ejemplo</span></td></tr><tr><td>25/10/2026</td><td>Solicitud de contratación recibida</td><td>Contratación docente</td><td><span class="status-pill pill-orange">Pendiente</span></td></tr><tr><td>24/10/2026</td><td>Comprobante de pago emitido</td><td>Pagos</td><td><span class="status-pill pill-green">Confirmado · demo</span></td></tr></tbody></table></div></section>
  <?php elseif ($page === 'perfil'): ?>
    <div class="welcome-row"><div><div class="eyebrow">Cuenta institucional</div><h1 class="heading">Mi perfil</h1><p class="subtitle">Información de la sesión administrativa actual.</p></div></div><section class="panel" style="max-width:700px"><div class="panel-head"><div><h2 class="panel-title">Datos de la cuenta</h2><div class="panel-sub">Los cambios de perfil deben conectarse al backend.</div></div></div><div class="form-grid"><div class="field"><label>Nombre</label><input value="<?= e($adminName) ?>" readonly></div><div class="field"><label>Rol</label><input value="Administrador" readonly></div></div></section>
  <?php else: ?>
    <div class="welcome-row">
      <div>
        <div class="eyebrow">Administración del portal</div>
        <h1 class="heading"><?= e($pageTitle) ?></h1>
        <p class="subtitle"><?= e($modules[$page][2]) ?>. Administrá los registros desde esta pantalla.</p>
      </div>
      <button class="btn" type="button" id="newRecordBtn">＋ &nbsp;Nuevo registro</button>
    </div>

    <section class="panel" id="recordsPanel">
      <div class="panel-head">
        <div><h2 class="panel-title">Listado de <?= e(mb_strtolower($pageTitle, 'UTF-8')) ?></h2>
        <div class="panel-sub">Buscá registros y usá las acciones para consultar, editar o eliminar.</div></div>
        <span class="status-pill pill-blue" id="recordCount">0 registros</span>
      </div>
      <div class="toolbar" style="margin-bottom:17px">
        <div class="search"><input id="recordSearch" type="search" placeholder="Buscar por nombre o descripción..." aria-label="Buscar registros"></div>
        <select id="statusFilter" aria-label="Filtrar por estado" style="border:1px solid var(--line);border-radius:10px;padding:11px;color:var(--ink);background:#fff">
          <option value="">Todos los estados</option><option>Publicado</option><option>Borrador</option><option>Inactivo</option>
        </select>
      </div>
      <div class="table-wrap"><table id="recordsTable">
        <thead><tr><th>Registro</th><th>Descripción</th><th>Estado</th><th>Última actualización</th><th>Acciones</th></tr></thead>
        <tbody id="recordsBody"></tbody>
      </table></div>
      <div class="empty-state" id="recordsEmpty" hidden><div class="big"><?= e($modules[$page][1]) ?></div><strong id="emptyTitle">Todavía no hay registros</strong><span id="emptyDescription">Creá un registro con el botón “Nuevo registro”.</span></div>
    </section>

    <section class="panel" id="recordEditor" hidden>
      <div class="panel-head"><div><h2 class="panel-title" id="editorTitle">Nuevo registro</h2><div class="panel-sub">Completá la información y guardá los cambios.</div></div>
      <button class="btn btn-light btn-sm" type="button" id="cancelEditor">Volver al listado</button></div>
      <form id="recordForm">
        <input type="hidden" id="recordId">
        <div class="form-grid">
          <div class="field full"><label for="recordTitle"><?= in_array($page, ['estudiantes'], true) ? 'Nombre completo del estudiante *' : (in_array($page, ['pagos'], true) ? 'Número de comprobante / referencia *' : (in_array($page, ['solicitudes_docentes'], true) ? 'Nombre del postulante *' : (in_array($page, ['servicios'], true) ? 'Nombre del servicio *' : 'Nombre / título *'))) ?></label><input id="recordTitle" maxlength="190" required placeholder="Ingresá la información principal"></div>
          <div class="field full"><label for="recordDescription"><?= in_array($page, ['estudiantes'], true) ? 'Datos de contacto, documento y observaciones' : (in_array($page, ['pagos'], true) ? 'Detalle del pago, concepto y referencia de transacción' : (in_array($page, ['solicitudes_docentes'], true) ? 'Especialidad, experiencia y mensaje de solicitud' : 'Descripción / observaciones')) ?></label><textarea id="recordDescription" rows="4" maxlength="3000" placeholder="Agregá la información relevante"></textarea></div>
          <div class="field"><label for="recordStatus">Estado</label><select id="recordStatus"><option>Publicado</option><option>Borrador</option><option>Inactivo</option></select></div>
          <div class="field"><label for="recordLink">Enlace (opcional)</label><input id="recordLink" type="url" placeholder="https://..."></div>
          <div class="field full"><label for="recordImage">URL de imagen (opcional)</label><input id="recordImage" type="url" placeholder="https://..."></div>
        </div>
        <div class="form-actions"><button class="btn btn-light" type="button" id="cancelEditorBottom">Cancelar</button><button class="btn" type="submit">Guardar registro</button></div>
      </form>
    </section>

    <section class="panel" id="recordDetails" hidden>
      <div class="panel-head"><div><h2 class="panel-title">Consulta del registro</h2><div class="panel-sub">Vista detallada de la información guardada.</div></div><button class="btn btn-light btn-sm" type="button" id="closeDetails">Volver al listado</button></div>
      <div class="form-grid">
        <div class="field"><label>Nombre / título</label><input id="detailTitle" readonly></div>
        <div class="field"><label>Estado</label><input id="detailStatus" readonly></div>
        <div class="field full"><label>Descripción</label><textarea id="detailDescription" rows="4" readonly></textarea></div>
        <div class="field full"><label>Enlace</label><input id="detailLink" readonly></div>
        <div class="field full"><label>URL de imagen</label><input id="detailImage" readonly></div>
        <div class="field"><label>Creado</label><input id="detailCreated" readonly></div>
        <div class="field"><label>Actualizado</label><input id="detailUpdated" readonly></div>
      </div>
      <div class="form-actions"><button class="btn" type="button" id="editFromDetails">Editar registro</button></div>
    </section>
    <div class="input-note" style="margin-top:14px">ⓘ Estas acciones son una maqueta funcional del frontend: los datos se guardan en este navegador para probar la interfaz. Para que sean compartidos y persistentes en el sistema, hay que conectarlos al backend y a la base de datos.</div>
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

// CRUD de demostración para los módulos visuales. Sustituir localStorage por endpoints PHP al integrar el backend.
(function(){
  const page = <?= json_encode($page, JSON_UNESCAPED_UNICODE) ?>;
  const prefix = 'insoft_admin_demo_' + page;
  const $ = id => document.getElementById(id);
  const body = $('recordsBody');
  if (!body) return;
  const panel = $('recordsPanel'), editor = $('recordEditor'), details = $('recordDetails');
  const fields = ['recordTitle','recordDescription','recordStatus','recordLink','recordImage'];
  let records = [];
  try { records = JSON.parse(localStorage.getItem(prefix) || '[]'); if (!Array.isArray(records)) records = []; } catch(e) { records = []; }
  let detailId = null;
  const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const save = () => localStorage.setItem(prefix, JSON.stringify(records));
  const date = value => value ? new Date(value).toLocaleString('es-UY',{dateStyle:'short',timeStyle:'short'}) : '—';
  const closeViews = () => { panel.hidden=false; editor.hidden=true; details.hidden=true; };
  function render(){
    const q = ($('recordSearch').value || '').toLocaleLowerCase('es');
    const status = $('statusFilter').value;
    const filtered = records.filter(r => (!status || r.status===status) && [r.title,r.description,r.status].join(' ').toLocaleLowerCase('es').includes(q));
    body.innerHTML = filtered.map(r => `<tr>
      <td><div class="record-name"><span class="record-symbol"><?= e($modules[$page][1]) ?></span><div><strong>${esc(r.title)}</strong><small>ID: ${esc(r.id)}</small></div></div></td>
      <td style="white-space:normal;min-width:180px;max-width:330px">${esc(r.description || 'Sin descripción')}</td>
      <td><span class="status-pill ${r.status==='Publicado'?'pill-green':r.status==='Borrador'?'pill-orange':'pill-blue'}">${esc(r.status)}</span></td>
      <td>${esc(date(r.updatedAt))}</td>
      <td><div class="records-actions">
        <button class="record-action" type="button" data-action="view" data-id="${esc(r.id)}" title="Consultar" aria-label="Consultar ${esc(r.title)}">⌕</button>
        <button class="record-action" type="button" data-action="edit" data-id="${esc(r.id)}" title="Editar" aria-label="Editar ${esc(r.title)}">✎</button>
        <button class="record-action danger" type="button" data-action="delete" data-id="${esc(r.id)}" title="Eliminar" aria-label="Eliminar ${esc(r.title)}">×</button>
      </div></td></tr>`).join('');
    $('recordCount').textContent = `${filtered.length} ${filtered.length===1?'registro':'registros'}`;
    $('recordsEmpty').hidden = filtered.length !== 0;
    $('recordsTable').hidden = filtered.length === 0;
    $('emptyTitle').textContent = records.length ? 'No hay resultados para esta búsqueda' : 'Todavía no hay registros';
    $('emptyDescription').textContent = records.length ? 'Probá cambiar el texto o el filtro de estado.' : 'Creá un registro con el botón “Nuevo registro”.';
  }
  function newRecord(){
    $('recordForm').reset(); $('recordId').value=''; $('editorTitle').textContent='Nuevo registro';
    closeViews(); panel.hidden=true; editor.hidden=false; $('recordTitle').focus();
  }
  function editRecord(id){
    const r=records.find(x=>String(x.id)===String(id)); if(!r)return;
    $('recordForm').reset(); $('recordId').value=r.id;
    $('recordTitle').value=r.title||''; $('recordDescription').value=r.description||'';
    $('recordStatus').value=r.status||'Publicado'; $('recordLink').value=r.link||''; $('recordImage').value=r.image||'';
    $('editorTitle').textContent='Editar registro'; panel.hidden=true; details.hidden=true; editor.hidden=false; $('recordTitle').focus();
  }
  function viewRecord(id){
    const r=records.find(x=>String(x.id)===String(id)); if(!r)return; detailId=r.id;
    $('detailTitle').value=r.title||''; $('detailStatus').value=r.status||''; $('detailDescription').value=r.description||'';
    $('detailLink').value=r.link||'—'; $('detailImage').value=r.image||'—';
    $('detailCreated').value=date(r.createdAt); $('detailUpdated').value=date(r.updatedAt);
    panel.hidden=true; editor.hidden=true; details.hidden=false;
  }
  $('newRecordBtn').addEventListener('click',newRecord);
  $('cancelEditor').addEventListener('click',()=>{closeViews();render()});
  $('cancelEditorBottom').addEventListener('click',()=>{closeViews();render()});
  $('closeDetails').addEventListener('click',()=>{closeViews();render()});
  $('editFromDetails').addEventListener('click',()=>editRecord(detailId));
  $('recordSearch').addEventListener('input',render); $('statusFilter').addEventListener('change',render);
  $('recordForm').addEventListener('submit',e=>{
    e.preventDefault();
    const title=$('recordTitle').value.trim(); if(!title){$('recordTitle').focus();return}
    const id=$('recordId').value;
    const old=records.find(r=>String(r.id)===String(id));
    const now=new Date().toISOString();
    const item={id:old?old.id:(Date.now().toString(36)+Math.random().toString(36).slice(2,6)),title,
      description:$('recordDescription').value.trim(),status:$('recordStatus').value,
      link:$('recordLink').value.trim(),image:$('recordImage').value.trim(),
      createdAt:old?old.createdAt:now,updatedAt:now};
    if(old) records=records.map(r=>String(r.id)===String(id)?item:r); else records.unshift(item);
    save(); closeViews(); render();
  });
  body.addEventListener('click',e=>{
    const b=e.target.closest('button[data-action]'); if(!b)return;
    const id=b.dataset.id, action=b.dataset.action;
    if(action==='view')viewRecord(id);
    if(action==='edit')editRecord(id);
    if(action==='delete'){
      const r=records.find(x=>String(x.id)===String(id)); if(!r)return;
      if(confirm(`¿Querés eliminar el registro “${r.title}”? Esta acción no se puede deshacer.`)){
        records=records.filter(x=>String(x.id)!==String(id)); save(); render();
      }
    }
  });
  render();
})();

  // Los grupos laterales son desplegables explícitos y mantienen visible su submenú.
  document.querySelectorAll('.nav-group > summary').forEach(function(summary){
    summary.addEventListener('click', function(event){
      event.preventDefault();
      const group = summary.parentElement;
      const willOpen = !group.open;
      document.querySelectorAll('.nav-group').forEach(function(other){
        if (other !== group) other.open = false;
      });
      group.open = willOpen;
    });
  });
  // Centro de notificaciones funcional de demostración: se guarda en este navegador.
  (function(){
    const storageKey = 'insoft_admin_notifications_v1';
    const initialNotifications = [
      {id:'contratacion-1',icon:'✉',title:'Nueva solicitud de contratación',message:'Hay una solicitud docente pendiente de revisar.',time:'Hoy',read:false},
      {id:'estudiante-1',icon:'♙',title:'Registro de estudiante',message:'Revisá los datos del último estudiante registrado.',time:'Hoy',read:false},
      {id:'pago-1',icon:'＄',title:'Comprobante de pago',message:'Hay un comprobante que requiere verificación.',time:'Ayer',read:false}
    ];
    const panel = document.querySelector('.notification-dropdown');
    const list = document.getElementById('notificationList');
    if(!panel || !list) return;
    let notifications;
    try { notifications = JSON.parse(localStorage.getItem(storageKey) || 'null'); } catch(e) { notifications = null; }
    if(!Array.isArray(notifications)) notifications = initialNotifications;
    const save = () => { try { localStorage.setItem(storageKey, JSON.stringify(notifications)); } catch(e) {} };
    const render = () => {
      const unread = notifications.filter(n => !n.read).length;
      document.getElementById('noticeCount').textContent = unread;
      document.getElementById('noticeCount').style.display = unread ? 'grid' : 'none';
      document.getElementById('noticeDot').style.display = unread ? 'block' : 'none';
      document.getElementById('notificationSummary').textContent = unread ? `Tenés ${unread} notificación(es) sin leer` : 'Estás al día';
      if(!notifications.length){ list.innerHTML = '<div class="notification-empty">No hay notificaciones por mostrar.</div>'; return; }
      list.innerHTML = notifications.map(n => `<article class="notification-item ${n.read ? '' : 'unread'}"><span class="notification-symbol">${n.icon}</span><div class="notification-copy"><strong>${escapeHtml(n.title)}</strong><p>${escapeHtml(n.message)}</p><time>${escapeHtml(n.time)}</time></div>${n.read ? '' : `<button type="button" class="notification-read" data-read="${escapeHtml(n.id)}">Leída</button>`}</article>`).join('');
    };
    const escapeHtml = value => String(value).replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
    list.addEventListener('click', event => { const btn = event.target.closest('[data-read]'); if(!btn) return; notifications = notifications.map(n => n.id === btn.dataset.read ? {...n,read:true} : n); save(); render(); });
    document.getElementById('markAllRead').addEventListener('click', () => { notifications = notifications.map(n => ({...n,read:true})); save(); render(); });
    document.getElementById('resetNotifications').addEventListener('click', () => { notifications = initialNotifications.map(n => ({...n})); save(); render(); });
    document.addEventListener('click', event => { if(!panel.contains(event.target)) panel.removeAttribute('open'); });
    render();
  })();

  function toggleSidebar(){
    const sidebar = document.getElementById('sidebar');
    if (window.matchMedia('(max-width: 760px)').matches) {
      sidebar.classList.toggle('open');
    } else {
      document.body.classList.toggle('sidebar-collapsed');
    }
  }

  // Cerrar el desplegable de usuario al hacer clic fuera de él.
  document.addEventListener('click', function(event){
    document.querySelectorAll('.profile-dropdown[open]').forEach(function(menu){
      if (!menu.contains(event.target)) menu.removeAttribute('open');
    });
  });
</script>
</body>
</html>

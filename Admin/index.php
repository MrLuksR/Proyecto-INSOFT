<?php
/**
 * INSOFT / INADI - Panel administrativo (plantilla visual)
 * Integración backend pendiente: conectar los formularios a las acciones del proyecto.
 * Verifica la sesión administrativa y consulta los listados con PDO.
 */
// CARGAR LA CLASE ANTES DE RECUPERAR EL OBJETO DE SESIÓN
require_once __DIR__ . '/../database/Clases/Administrativo.php';

/* ==========================================================
   DESCARGA DE CURRÍCULUMS
========================================================== */

if (
    ($_GET['page'] ?? '') === 'descargar_cv'
) {

    require __DIR__ . '/descargar_cv.php';

    exit;

}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// COMPROBAR QUE EL USUARIO SEA ADMINISTRATIVO
if (
    !isset($_SESSION['usuario'], $_SESSION['tipo_usuario']) ||
    $_SESSION['tipo_usuario'] !== 'administrativo' ||
    !($_SESSION['usuario'] instanceof Administrativo)
) {
    header('Location: ../login/loginn.php');
    exit;
}
require_once '../database/consultas/conexion.php';
require_once '../database/consultas/panelAdmin/consultas.php';

$modules = [
    'dashboard' => ['Inicio', '▦', 'Resumen general'],
    'cursos'    => ['Cursos', '▤', 'Administrá la oferta educativa'],
    'editar_curso' => ['Editar curso', '✎', 'Modificar información del curso'],
    'panel_docente' => ['Panel docente', '▦', 'Resumen y accesos del área docente'],
    'docentes'  => ['Gestión de docentes', '♙', 'Docentes y cursos asignados'],
    'servicios' => ['Servicios', '▤', 'Gestión de servicios ofrecidos'],
    'estadisticas' => ['Estadísticas', '▥', 'Visitas y contrataciones'],
    'reportes' => ['Reportes de actividad', '▧', 'Resumen de actividad administrativa'],
    'analiticas' => ['Panel de analíticas', '▥', 'Indicadores y gráficos del portal'],
    'solicitudes_docentes' => ['Contratación docente', '✉', 'Solicitudes de contratación docente'],
    'historial_docente' => ['Historial docente', '☷', 'Historial de actividad y asignaciones docentes'],
    'pagos' => ['Comprobantes de pago', '＄', 'Confirmaciones y comprobantes de pago'],
    'panel_estudiante' => ['Panel estudiante', '▦', 'Resumen y accesos del área estudiantil'],
    'estudiantes' => ['Registrar estudiantes', '♙', 'Registro y administración de estudiantes'],
    'noticias'  => ['Noticias', '▣', 'Novedades y publicaciones'],
    'editar_noticia' => ['Editar noticia', '✎', 'Modificar una publicación'],
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
// DATOS DEL ADMINISTRATIVO AUTENTICADO
/** @var Administrativo $admin */
$admin = $_SESSION['usuario'];
$nombre = trim((string) $admin->getNombre());
$apellido = trim((string) $admin->getApellido());
$adminName = trim($nombre . ' ' . $apellido);
$adminName = $adminName !== '' ? $adminName : $admin->getNombreUsuario();
$nombreSaludo = $nombre !== '' ? $nombre : $adminName;
$cargo = trim((string) $admin->getCargo());
$area = trim((string) $admin->getArea());
$nivelAcceso = (string) $admin->getNivelAcceso();
$fechaIngreso = (string) $admin->getFechaIngreso();
$initials = mb_strtoupper(
    mb_substr($nombre !== '' ? $nombre : $adminName, 0, 1, 'UTF-8') .
    mb_substr($apellido, 0, 1, 'UTF-8'),
    'UTF-8'
);

$estadisticasInicio = adminResumen($pdo ?? null);
$vistasAdmin = [
    'dashboard' => 'dashboard',
    'panel_docente' => 'panel_docente',
    'panel_estudiante' => 'panel_estudiante',
    'docentes' => 'docentes',
    'solicitudes_docentes' => 'registro_docente',
    'cursos' => 'cursos',
    'editar_curso' => 'editar_curso',
    'estudiantes' => 'estudiantes',
    'pagos' => 'pagos',
    'usuarios' => 'usuarios',
    'noticias' => 'noticias',
    'editar_noticia' => 'editar_noticia',
    'banners' => 'banners',
    'galeria' => 'galeria',
    'historial_docente' => 'historial_docente',
    'estadisticas' => 'estadisticas',
    'analiticas' => 'analiticas',
    'reportes' => 'reportes',
    'perfil' => 'perfil',
    'servicios' => 'servicios',
    'empresas' => 'empresas',
    'comentarios' => 'comentarios',
    'formularios' => 'formularios',
    'slides' => 'slides',
    'ajustes' => 'ajustes',
];
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

<link rel="stylesheet" href="assets/admin2.css">
</head>
<body>
<div class="layout">
    <?php require __DIR__ . '/common_admin.php'; ?>
<main class="main">
    <header class="topbar">
        <div style="display:flex;align-items:center;gap:13px">
            <button class="icon-btn mobile-menu sidebar-toggle" type="button" onclick="toggleSidebar()" aria-label="Mostrar u ocultar menú lateral" title="Mostrar u ocultar menú lateral">☰</button>
            <div>
                <div class="crumb">Panel administrativo&nbsp; / &nbsp;<b><?= e($pageTitle) ?>
                    </b>
                </div>
                <div class="top-title"><?= e($pageTitle) ?>
                </div>
            </div>
        </div>
        <div class="top-actions">
            <details class="notification-dropdown">
                <summary class="icon-btn notification-trigger" aria-label="Abrir notificaciones" title="Notificaciones">♧<span class="notice-dot" id="noticeDot">
                    </span>
                    <span class="notice-count" id="noticeCount">3</span>
                </summary>
                <section class="notification-panel">
                    <div class="notification-head">
                        <div>
                            <strong>Notificaciones</strong>
                            <small id="notificationSummary">Tenés novedades pendientes</small>
                        </div>
                        <button type="button" class="notification-mark" id="markAllRead">Marcar todas leídas</button>
                    </div>
                    <div id="notificationList" class="notification-list">
                    </div>
                    <div class="notification-foot">
                        <span>Notificaciones de demostración</span>
                        <button type="button" id="resetNotifications">Restablecer</button>
                    </div>
                </section>
            </details>
            <div class="profile">
                <details class="profile-dropdown">
                    <summary class="profile-trigger">
                        <span class="avatar"><?= e($initials) ?>
                        </span>
                        <span class="profile-copy">
                        <span class="profile-name" style="display:block"><?= e($adminName) ?>
                        </span>
                        <span class="profile-role" style="display:block"><?= e($cargo !== '' ? $cargo : 'Administrativo') ?>
                        </span>
                        </span>
                        <span class="profile-chevron">⌄</span>
                    </summary>
                    <div class="profile-menu">
                        <a href="?page=perfil">
                        <span>◉</span>
                        <span>Perfil</span>
                        </a>
                        <div class="menu-divider">
                        </div>
                        <a href="../login/logout.php">
                        <span>↗</span>
                        <span>Cerrar sesión</span>
                        </a>
                    </div>
                </details>
            </div>
        </div>
    </header>
    <div class="content">

<?php
// Enrutamiento por lista permitida: cada pestaña carga su propio PHP.
$vista = $vistasAdmin[$page] ?? 'dashboard';
require __DIR__ . '/views/' . $vista . '.php';
?>
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
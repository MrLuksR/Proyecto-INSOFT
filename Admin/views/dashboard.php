
<div class="welcome-row">
    <div>
        <div class="eyebrow">Tu espacio de gestión</div>
        <h1 class="heading">Bienvenido, <?= e($nombreSaludo) ?></h1>
        <p class="subtitle">Este es el resumen de tu plataforma. Gestioná los contenidos y manten todo organizado desde un solo lugar.</p>
    </div>
    <a class="btn" href="?page=docentes">＋ &nbsp;Gestionar docentes</a>
</div>
<div class="stats">
    <div class="stat-card">
        <div class="stat-top">Cursos publicados <span class="stat-icon">▤</span>
    </div>
    <div class="stat-num"><?= e($estadisticasInicio['cursos'] ?? '—') ?></div>
    <div class="stat-foot">Consulta de base de datos</div>
</div>
<div class="stat-card">
    <div class="stat-top">Docentes registrados <span class="stat-icon mint">♙</span>
</div>
<div class="stat-num"><?= e($estadisticasInicio['docentes'] ?? '—') ?></div>
<div class="stat-foot">Docentes en la base de datos</div>
</div>
<div class="stat-card">
    <div class="stat-top">Mensajes recibidos <span class="stat-icon amber">✉</span>
</div>
<div class="stat-num"><?= e($estadisticasInicio['mensajes'] ?? '—') ?></div>
<div class="stat-foot">Consultas del portal</div>
</div>
<div class="stat-card">
    <div class="stat-top">Usuarios del sistema <span class="stat-icon violet">◉</span>
</div>
<div class="stat-num"><?= e($estadisticasInicio['usuarios'] ?? '—') ?></div>
<div class="stat-foot">Accesos institucionales</div>
</div>
</div>
<div class="columns">
    <section class="panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Actividad reciente</h2>
                <div class="panel-sub">Un vistazo a las tareas habituales del panel</div>
            </div>
            <span class="status-pill pill-blue">Panel activo</span>
        </div>
        <div class="activity">
            <div class="activity-icon">♙</div>
            <div class="activity-text">
                <strong>Gestión de docentes</strong>
                <br>Registrá docentes, asigná cursos y prepará el acceso inicial.<div class="activity-time">Disponible desde el menú Docentes</div>
            </div>
        </div>
        <div class="activity">
            <div class="activity-icon">▤</div>
            <div class="activity-text">
                <strong>Administración de cursos</strong>
                <br>Organizá la oferta educativa y manten actualizada la información.<div class="activity-time">Módulo Cursos</div>
            </div>
        </div>
        <div class="activity">
            <div class="activity-icon">▣</div>
            <div class="activity-text">
                <strong>Contenido institucional</strong>
                <br>Gestioná noticias, banners, galería y slides del sitio.<div class="activity-time">Módulos de contenido</div>
            </div>
        </div>
    </section>
    <section class="panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Accesos rápidos</h2>
                <div class="panel-sub">Atajos para las tareas más frecuentes</div>
            </div>
        </div>
        <div class="quick-grid">
            <a class="quick" href="?page=docentes">
            <div class="quick-ico">♙</div>
            <strong>Docentes</strong>
            <span>Registro y asignación de cursos</span>
            </a>
            <a class="quick" href="?page=cursos">
            <div class="quick-ico">▤</div>
            <strong>Cursos</strong>
            <span>Oferta educativa</span>
            </a>
            <a class="quick" href="?page=noticias">
            <div class="quick-ico">▣</div>
            <strong>Noticias</strong>
            <span>Publicaciones del sitio</span>
            </a>
            <a class="quick" href="?page=usuarios">
            <div class="quick-ico">◉</div>
            <strong>Usuarios</strong>
            <span>Accesos al portal</span>
            </a>
        </div>
    </section>
</div>


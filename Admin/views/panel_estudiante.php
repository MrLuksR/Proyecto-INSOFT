<?php $isTeacherPanel = false; ?>



<?php $isTeacherPanel = $page === 'panel_docente'; ?>
<div class="welcome-row">
    <div>
        <div class="eyebrow">INADI · INSOFT</div>
        <h1 class="heading"><?= $isTeacherPanel ? 'Panel docente' : 'Panel estudiante' ?>
        </h1>
        <p class="subtitle"><?= $isTeacherPanel ? 'Accedé a la gestión docente, las solicitudes de contratación y el historial.' : 'Accedé al registro de estudiantes y a los comprobantes de pago.' ?>
        </p>
    </div>
    <span class="status-pill pill-blue">Área de gestión</span>
</div>
<div class="panel-shortcut-grid">

<?php if ($isTeacherPanel): ?>
<a class="panel-shortcut" href="?page=docentes">
<div class="shortcut-icon">♙</div>
<strong>Gestión de docentes</strong>
<span>Registrar y consultar las fichas de docentes.</span>
</a>
<a class="panel-shortcut" href="?page=solicitudes_docentes">
<div class="shortcut-icon">✉</div>
<strong>Contratación docente</strong>
<span>Consultar solicitudes y su estado.</span>
</a>
<a class="panel-shortcut" href="?page=historial_docente">
<div class="shortcut-icon">☷</div>
<strong>Historial docente</strong>
<span>Ver actividad y asignaciones docentes.</span>
</a>

<?php else: ?>
<a class="panel-shortcut" href="?page=estudiantes">
<div class="shortcut-icon">♧</div>
<strong>Registrar estudiantes</strong>
<span>Acceder al registro y administración de estudiantes.</span>
</a>
<a class="panel-shortcut" href="?page=pagos">
<div class="shortcut-icon">＄</div>
<strong>Comprobantes de pago</strong>
<span>Consultar comprobantes y confirmaciones.</span>
</a>
<a class="panel-shortcut" href="?page=cursos">
<div class="shortcut-icon">▤</div>
<strong>Cursos</strong>
<span>Consultar la oferta educativa del portal.</span>
</a>

<?php endif; ?>
</div>


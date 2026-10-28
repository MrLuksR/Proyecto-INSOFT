<?php
/** Gestión docente: exclusivamente consulta de docentes registrados. */
if (!isset($admin) || !($admin instanceof Administrativo)) {
    http_response_code(403);
    exit;
}
$errorDocentes = null;
$docentes = [];
try {
    $docentes = adminObtenerDocentes($pdo ?? null);
} catch (Throwable $e) {
    error_log('[INSOFT listado docentes] ' . $e->getMessage());
    $errorDocentes = 'No se pudieron consultar los docentes.';
}
$agrupados = [];
foreach ($docentes as $fila) {
    $usuario = (string) $fila['nombre_usuario'];
    if (!isset($agrupados[$usuario])) {
        $agrupados[$usuario] = $fila;
        $agrupados[$usuario]['cursos_asignados'] = [];
    }
    if (!empty($fila['nombre_curso'])) {
        $agrupados[$usuario]['cursos_asignados'][] = $fila['nombre_curso'];
    }
}
?>
<div class="welcome-row">
    <div>
        <div class="eyebrow">Equipo académico</div>
        <h1 class="heading">Gestión de docentes</h1>
        <p class="subtitle">Consultá los docentes registrados y sus cursos asignados.</p>
    </div>
    <a class="btn" href="?page=solicitudes_docentes">＋ Contratación docente</a>
</div>
<section class="panel">
    <div class="panel-head">
        <div>
            <h2 class="panel-title">Docentes registrados</h2>
            <div class="panel-sub">Datos obtenidos desde la base de datos.</div>
        </div>
        <span class="status-pill pill-blue"><?= count($agrupados) ?> docentes</span>
    </div>
    <?php if ($errorDocentes): ?>
        <div class="input-note" role="alert"><?= e($errorDocentes) ?></div>
    <?php endif; ?>
    <div class="toolbar" style="margin-bottom:18px">
        <div class="search">
            <input type="search" id="teacherSearch" placeholder="Buscar docente" aria-label="Buscar docente">
        </div>
    </div>
    <div class="table-wrap">
        <table id="teacherTable">
            <thead>
                <tr>
                    <th>Docente</th>
                    <th>Usuario</th>
                    <th>Especialidad</th>
                    <th>Horario</th>
                    <th>Cursos asignados</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agrupados as $docente): ?>
                    <tr>
                        <td>
                            <strong><?= e(trim($docente['nombre'] . ' ' . $docente['apellido'])) ?></strong>
                            <br>
                            <small><?= e($docente['titulo'] ?: 'Sin título registrado') ?></small>
                        </td>
                        <td><?= e($docente['nombre_usuario']) ?></td>
                        <td><?= e($docente['especialidad'] ?: 'Sin especialidad') ?></td>
                        <td><?= e($docente['horario'] ?: 'Sin horario') ?></td>
                        <td><?= e(implode(', ', $docente['cursos_asignados']) ?: 'Sin cursos asignados') ?></td>
                        <td><span class="status-pill pill-blue"><?= e($docente['estado']) ?></span></td>

                        <!-- ACCIONES DEL DOCENTE -->

                        <td>

                            <?php

                            $estaActivo = strcasecmp(
                                trim((string) ($docente['estado'] ?? '')),
                                'Activo'
                            ) === 0;

                            ?>

                            <form
                                action="baja_docente.php"
                                method="POST"
                                onsubmit="return confirm('¿Estás seguro de que querés <?= $estaActivo ? 'dar de baja' : 'dar de alta' ?> a este docente?');"
                            >

                                <input
                                    type="hidden"
                                    name="nombre_usuario"
                                    value="<?= e($docente['nombre_usuario']) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="accion"
                                    value="<?= $estaActivo ? 'baja' : 'alta' ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-light"
                                    style="color: <?= $estaActivo ? '#991b1b' : '#166534' ?>;"
                                >

                                    <?php if ($estaActivo): ?>

                                        <i class="fa-solid fa-user-slash"></i>
                                        Dar de baja

                                    <?php else: ?>

                                        <i class="fa-solid fa-user-check"></i>
                                        Dar de alta

                                    <?php endif; ?>

                                </button>

                            </form>

                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$agrupados): ?>
                    <tr><td colspan="6">No hay docentes registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

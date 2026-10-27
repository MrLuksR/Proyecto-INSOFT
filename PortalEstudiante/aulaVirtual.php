<?php
require_once __DIR__ . '/portal_common.php';

if (!$haySesionEstudiante) {
    header('Location: ../Bienvenido/bienvenidoindex.php');
    exit;
}

$matriculas = [];
$fallo = false;

if (!$errorConexion) {
    try {
        $matriculas = portalObtenerMatriculas($pdo, $nombreUsuario);
    } catch (Throwable $e) {
        error_log('[INSOFT] Error al consultar matrículas: ' . $e->getMessage());
        $fallo = true;
    }
}

portalLayoutInicio('Aula virtual', 'aula');
?>
<link rel="stylesheet" href="CSS/aula-virtual.css">
<div class="aula-wrap">
<section class="aula-hero">
    <div>
        <span class="aula-kicker">APRENDIZAJE</span>
        <h1>Aula virtual</h1>
        <p>Accedé a los materiales y actividades de tus cursos habilitados.</p>
    </div>
</section>

<?php if ($fallo): ?>
    <div class="alert alert-warning">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span>No se pudo consultar tus cursos en este momento.</span>
    </div>
<?php elseif (!$errorConexion && !$matriculas): ?>
    <?php portalMostrarVacio('No hay aulas disponibles', 'No encontramos matrículas asociadas a tu usuario.', 'fa-chalkboard-user'); ?>
<?php elseif (!$errorConexion): ?>
    <div class="aula-course-grid">
        <?php foreach ($matriculas as $m): ?>
            <?php
            $habilitado = mb_strtolower(trim((string) ($m['estado'] ?? '')), 'UTF-8') === 'activa';
            $total = (int) ($m['total_tareas'] ?? 0);
            $completadas = (int) ($m['tareas_completadas'] ?? 0);
            $pendientes = max(0, $total - $completadas);
            ?>
            <article class="aula-course">
                <span class="aula-resource-icon"><i class="fa-solid fa-chalkboard-user"></i></span>
                <h3><?= e($m['nombre_curso'] ?: 'Curso no disponible') ?></h3>
                <p>Estado de matrícula: <strong><?= e($m['estado'] ?: 'Sin estado') ?></strong></p>
                <?php if ($habilitado): ?>
                    <p><i class="fa-solid fa-list-check"></i> <?= $pendientes ?> tarea(s) pendiente(s)</p>
                    <a class="aula-download" href="cursoVirtual.php?id=<?= (int) $m['id_curso'] ?>">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Ingresar al curso
                    </a>
                <?php else: ?>
                    <div class="aula-lock">
                        <i class="fa-solid fa-lock"></i>
                        <span>Acceso no habilitado. Consultá el estado de tu matrícula.</span>
                    </div>
                    <?php if (mb_strtolower(trim((string) ($m['estado'] ?? '')), 'UTF-8') === 'pendiente de pago'): ?>
                        <a class="btn btn-small" href="misPagos.php">Ver mis pagos</a>
                    <?php endif; ?>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
</div>
<?php portalLayoutFin(); ?>

<?php

/* 1. CARGA DE FUNCIONES COMUNES */
require_once __DIR__ . '/portal_common.php';

/* 2. CONSULTAR LAS TAREAS DE LOS CURSOS ACTIVOS */
$tareas = [];
$fallo = false;

if (!$errorConexion && $haySesionEstudiante) {
    try {
        $sql = "
            SELECT
                t.id_tarea,
                t.titulo,
                t.descripcion,
                t.fecha_tarea,
                t.fecha_limite,
                c.id_curso,
                c.nombre AS nombre_curso,
                te.fecha_entrega,
                CASE WHEN te.id_tarea IS NULL THEN 0 ELSE 1 END AS entregada
            FROM tareas t
            INNER JOIN clase cl ON cl.id_clase = t.id_clase
            INNER JOIN curso c ON c.id_curso = cl.id_curso
            LEFT JOIN tareas_estudiantes te
                ON te.id_tarea = t.id_tarea
                AND te.nombre_usuario_estudiante = :usuario_entrega
            WHERE EXISTS (
                SELECT 1
                FROM matricula m
                WHERE m.id_curso = c.id_curso
                  AND m.nombre_usuario_estudiante = :usuario_matricula
                  AND LOWER(TRIM(m.estado)) = 'activa'
            )
            ORDER BY t.fecha_tarea DESC, t.id_tarea DESC
        ";

        $tareas = portalConsultar($pdo, $sql, [
            ':usuario_entrega' => $nombreUsuario,
            ':usuario_matricula' => $nombreUsuario
        ]);
    } catch (Throwable $e) {
        error_log('[INADI] Error al consultar el calendario: ' . $e->getMessage());
        $fallo = true;
    }
}

/* FILTRAR Y ORDENAR LAS ACTIVIDADES */
$estadoFiltro = (string) ($_GET['estado'] ?? 'todas');
$ordenFiltro = (string) ($_GET['orden'] ?? 'recientes');

if (!in_array($estadoFiltro, ['todas', 'pendientes', 'entregadas'], true)) {
    $estadoFiltro = 'todas';
}
if (!in_array($ordenFiltro, ['recientes', 'antiguas', 'vencimiento', 'vencimiento_desc'], true)) {
    $ordenFiltro = 'recientes';
}

/* 3. PREPARAR ESTADÍSTICAS Y FECHAS */
$totalTareas = count($tareas);
$entregadas = count(array_filter($tareas, static function ($tarea) {
    return (int) $tarea['entregada'] === 1;
}));
$pendientes = $totalTareas - $entregadas;

$tareasFiltradas = array_values(array_filter($tareas, static function (array $tarea) use ($estadoFiltro): bool {
    $entregada = (int) $tarea['entregada'] === 1;
    return $estadoFiltro === 'todas'
        || ($estadoFiltro === 'entregadas' && $entregada)
        || ($estadoFiltro === 'pendientes' && !$entregada);
}));

usort($tareasFiltradas, static function (array $a, array $b) use ($ordenFiltro): int {
    if ($ordenFiltro === 'vencimiento' || $ordenFiltro === 'vencimiento_desc') {
        // Las tareas sin fecha límite aparecen siempre al final.
        $sinFechaA = empty($a['fecha_limite']);
        $sinFechaB = empty($b['fecha_limite']);
        if ($sinFechaA !== $sinFechaB) {
            return $sinFechaA ? 1 : -1;
        }
        $comparacion = strcmp((string) $a['fecha_limite'], (string) $b['fecha_limite']);
        if ($ordenFiltro === 'vencimiento_desc') {
            $comparacion = -$comparacion;
        }
    } else {
        $comparacion = strcmp((string) $a['fecha_tarea'], (string) $b['fecha_tarea']);
        if ($ordenFiltro === 'recientes') {
            $comparacion = -$comparacion;
        }
    }
    return $comparacion ?: ((int) $b['id_tarea'] <=> (int) $a['id_tarea']);
});

function calendarioFecha($fecha): string
{
    if (empty($fecha)) {
        return 'Sin fecha';
    }

    try {
        return (new DateTime((string) $fecha))->format('d/m/Y');
    } catch (Throwable $e) {
        return 'Fecha no disponible';
    }
}

portalLayoutInicio('Calendario académico', 'calendario');
?>

<!-- 4. ENCABEZADO -->
<section class="page-heading">
    <div>
        <p class="eyebrow">ORGANIZACIÓN</p>
        <h1>Calendario académico</h1>
        <p>Consultá las tareas de tus cursos, sus fechas y el estado de tus entregas.</p>
    </div>
</section>

<!-- 5. CONTENIDO -->
<?php if ($errorConexion || $fallo): ?>
    <div class="alert alert-warning" role="alert">
        <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
        No se pudieron consultar las tareas. Intentá nuevamente más tarde.
    </div>
<?php elseif (!$haySesionEstudiante): ?>
    <?php portalMostrarVacio('Iniciá sesión', 'Accedé como estudiante para consultar tus tareas.', 'fa-lock'); ?>
<?php elseif (!$tareas): ?>
    <?php portalMostrarVacio('No hay tareas asignadas', 'Las actividades de tus cursos activos aparecerán aquí cuando estén disponibles.', 'fa-calendar-days'); ?>
<?php else: ?>
    <section class="calendario-resumen" aria-label="Resumen de tareas">
        <div class="calendario-resumen-item">
            <i class="fa-solid fa-list-check" aria-hidden="true"></i>
            <div><strong><?= $totalTareas ?></strong><span>Total de tareas</span></div>
        </div>
        <div class="calendario-resumen-item">
            <i class="fa-solid fa-clock" aria-hidden="true"></i>
            <div><strong><?= $pendientes ?></strong><span>Sin entregar</span></div>
        </div>
        <div class="calendario-resumen-item">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
            <div><strong><?= $entregadas ?></strong><span>Entregadas</span></div>
        </div>
    </section>

    <section class="calendario-listado" aria-label="Listado de tareas">
        <div class="calendario-listado-titulo">
            <h2>Mis tareas</h2>
            <span><?= count($tareasFiltradas) ?> de <?= $totalTareas ?> actividades</span>
        </div>

        <form class="calendario-filtros" method="GET" action="calendario.php">
            <div class="calendario-filtro-grupo">
                <label for="filtroEstado"><i class="fa-solid fa-filter" aria-hidden="true"></i> Estado</label>
                <select name="estado" id="filtroEstado">
                    <option value="todas" <?= $estadoFiltro === 'todas' ? 'selected' : '' ?>>Todas las actividades</option>
                    <option value="pendientes" <?= $estadoFiltro === 'pendientes' ? 'selected' : '' ?>>Pendientes</option>
                    <option value="entregadas" <?= $estadoFiltro === 'entregadas' ? 'selected' : '' ?>>Entregadas</option>
                </select>
            </div>
            <div class="calendario-filtro-grupo">
                <label for="filtroOrden"><i class="fa-solid fa-arrow-down-wide-short" aria-hidden="true"></i> Ordenar por</label>
                <select name="orden" id="filtroOrden">
                    <option value="recientes" <?= $ordenFiltro === 'recientes' ? 'selected' : '' ?>>Más recientes</option>
                    <option value="antiguas" <?= $ordenFiltro === 'antiguas' ? 'selected' : '' ?>>Más antiguas</option>
                    <option value="vencimiento" <?= $ordenFiltro === 'vencimiento' ? 'selected' : '' ?>>Próximas a vencer</option>
                    <option value="vencimiento_desc" <?= $ordenFiltro === 'vencimiento_desc' ? 'selected' : '' ?>>Últimas en vencer</option>
                </select>
            </div>
            <button class="btn btn-primary calendario-filtrar" type="submit">
                <i class="fa-solid fa-check" aria-hidden="true"></i> Aplicar filtros
            </button>
            <a class="calendario-limpiar" href="calendario.php">Limpiar</a>
        </form>

        <?php if (!$tareasFiltradas): ?>
            <div class="calendario-sin-resultados">
                <i class="fa-regular fa-folder-open" aria-hidden="true"></i>
                <strong>No hay actividades con estos filtros.</strong>
                <span>Probá otro estado u orden.</span>
            </div>
        <?php endif; ?>

        <?php foreach ($tareasFiltradas as $tarea): ?>
            <?php $entregada = (int) $tarea['entregada'] === 1; ?>
            <article class="calendario-tarea <?= $entregada ? 'is-entregada' : 'is-pendiente' ?>">
                <div class="calendario-tarea-icono">
                    <i class="fa-solid <?= $entregada ? 'fa-circle-check' : 'fa-clipboard-list' ?>" aria-hidden="true"></i>
                </div>

                <div class="calendario-tarea-contenido">
                    <div class="calendario-tarea-superior">
                        <h3><?= e($tarea['titulo']) ?></h3>
                        <span class="calendario-tarea-estado">
                            <?= $entregada ? 'Entregada' : 'Pendiente' ?>
                        </span>
                    </div>

                    <p class="calendario-tarea-curso">
                        <i class="fa-solid fa-book-open" aria-hidden="true"></i>
                        <?= e($tarea['nombre_curso']) ?>
                    </p>

                    <?php if (!empty($tarea['descripcion'])): ?>
                        <p class="calendario-tarea-descripcion"><?= e($tarea['descripcion']) ?></p>
                    <?php endif; ?>

                    <div class="calendario-tarea-fechas">
                        <span><i class="fa-regular fa-calendar-plus" aria-hidden="true"></i> Publicada: <?= e(calendarioFecha($tarea['fecha_tarea'])) ?></span>
                        <?php if (!empty($tarea['fecha_limite'])): ?>
                            <span><i class="fa-regular fa-clock" aria-hidden="true"></i> Vence: <?= e(calendarioFecha($tarea['fecha_limite'])) ?></span>
                        <?php endif; ?>
                        <?php if ($entregada): ?>
                            <span><i class="fa-solid fa-check" aria-hidden="true"></i> Enviada: <?= e(calendarioFecha($tarea['fecha_entrega'])) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <a class="calendario-tarea-enlace" href="cursoVirtual.php?id=<?= (int) $tarea['id_curso'] ?>">
                    Ver curso <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php portalLayoutFin(); ?>

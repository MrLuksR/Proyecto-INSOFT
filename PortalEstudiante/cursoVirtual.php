<?php
require_once __DIR__ . '/portal_common.php';
if (!$haySesionEstudiante) { header('Location: ../Bienvenido/bienvenidoindex.php'); exit; }
$idCurso = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$idCurso || $idCurso < 1) { header('Location: aulaVirtual.php'); exit; }
$curso = null; $clases = []; $materiales = []; $tareas = []; $fallo = false; $sinAcceso = false;
if (!$errorConexion) {
    try {
        $stmt = $pdo->prepare("SELECT c.id_curso,c.nombre,c.descripcion,c.modalidad,c.duracion,c.img FROM matricula m INNER JOIN curso c ON c.id_curso=m.id_curso WHERE m.nombre_usuario_estudiante=:usuario AND m.id_curso=:curso AND LOWER(TRIM(m.estado))='activa' LIMIT 1");
        $stmt->execute([':usuario'=>$nombreUsuario,':curso'=>$idCurso]);
        $curso = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        if (!$curso) { $sinAcceso = true; http_response_code(403); }
        else {
            $clases = portalConsultar($pdo, 'SELECT id_clase,titulo,descripcion,fecha FROM clase WHERE id_curso=:curso ORDER BY fecha,id_clase', [':curso'=>$idCurso]);
            $materiales = portalConsultar($pdo, 'SELECT mat.id_material,mat.id_clase,mat.nombre,mat.descripcion,mat.tipo,mat.url_archivo,mat.fecha FROM materiales mat INNER JOIN clase cl ON cl.id_clase=mat.id_clase WHERE cl.id_curso=:curso ORDER BY mat.id_material', [':curso'=>$idCurso]);
            $tareas = portalConsultar($pdo, 'SELECT t.id_tarea,t.id_clase,t.titulo,t.descripcion,t.fecha_tarea,t.fecha_limite,CASE WHEN te.id_tarea IS NULL THEN 0 ELSE 1 END AS entregada,te.fecha_entrega,te.calificacion,te.texto_entrega,te.archivo_entrega FROM tareas t INNER JOIN clase cl ON cl.id_clase=t.id_clase LEFT JOIN tareas_estudiantes te ON te.id_tarea=t.id_tarea AND te.nombre_usuario_estudiante=:usuario WHERE cl.id_curso=:curso ORDER BY t.fecha_limite,t.id_tarea', [':usuario'=>$nombreUsuario,':curso'=>$idCurso]);
        }
    } catch (Throwable $e) { error_log('[INSOFT] Aula: '.$e->getMessage()); $fallo=true; }
}
$materialesPorClase=[];foreach($materiales as $m){$materialesPorClase[(int)$m['id_clase']][]=$m;}
$tareasPorClase=[];foreach($tareas as $t){$tareasPorClase[(int)$t['id_clase']][]=$t;}
$ahoraBD = null;
if (!$errorConexion && !$fallo) {
    try { $ahoraBD = new DateTime((string) $pdo->query('SELECT NOW()')->fetchColumn()); }
    catch (Throwable $e) { error_log('[INSOFT] Hora BD: '.$e->getMessage()); $fallo = true; }
}
if (empty($_SESSION['csrf_tarea'])) { $_SESSION['csrf_tarea'] = bin2hex(random_bytes(32)); }
$avisoTarea = $_SESSION['aviso_tarea'] ?? null;
unset($_SESSION['aviso_tarea']);
$entregadas=count(array_filter($tareas,fn($t)=>(int)$t['entregada']===1));
function aulaFecha($fecha): string { if (!$fecha) return 'Sin fecha'; try {return (new DateTime($fecha))->format('d/m/Y');}catch(Throwable $e){return (string)$fecha;} }
// Ajustar esta carpeta según la ubicación real de las imágenes de cursos.
$rutaImagenesCursos = '../Cursos/imagenes/';
$imagenCurso = ($curso && !empty($curso['img']))
    ? $rutaImagenesCursos . rawurlencode(basename((string) $curso['img']))
    : null;
portalLayoutInicio($curso['nombre'] ?? 'Aula virtual','aula');
?>
<link rel="stylesheet" href="CSS/aula-virtual.css">
<div class="aula-wrap">
<?php if ($avisoTarea): ?>
    <div class="aula-notice <?= ($avisoTarea['tipo'] ?? '') === 'exito' ? 'is-success' : 'is-error' ?>" role="alert">
        <i class="fa-solid fa-circle-info"></i> <?= e($avisoTarea['mensaje'] ?? '') ?>
    </div>
<?php endif; ?>
<section class="aula-hero aula-hero-banner">
    <?php if ($imagenCurso): ?>
        <img
            class="aula-banner-image"
            src="<?= e($imagenCurso) ?>"
            alt=""
            loading="eager"
        >
    <?php endif; ?>

    <div class="aula-banner-overlay">
        <a class="aula-back" href="aulaVirtual.php">
            <i class="fa-solid fa-arrow-left"></i> Volver a mis cursos
        </a>

        <div class="aula-hero-content">
            <div class="aula-kicker">Mi espacio de aprendizaje</div>
            <h1><?= e($curso['nombre'] ?? 'Aula virtual') ?></h1>

            <?php if ($curso): ?>
                <p><?= e($curso['descripcion']) ?></p>
                <div class="aula-meta">
                    <span><i class="fa-solid fa-laptop"></i> <?= e($curso['modalidad']) ?></span>
                    <span><i class="fa-regular fa-clock"></i> <?= e($curso['duracion']) ?></span>
                    <span><i class="fa-solid fa-circle-check"></i> Matrícula activa</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php if($errorConexion||$fallo):?><div class="alert alert-warning">No se pudo cargar el contenido del curso.</div>
<?php elseif($sinAcceso):?><div class="alert alert-warning"><i class="fa-solid fa-lock"></i> No tenés acceso a este curso. Verificá tu matrícula.</div>
<?php else:?>
<div class="aula-stats">
    <div class="aula-stat"><i class="fa-solid fa-book-open"></i><div><strong><?=count($clases)?></strong><span>Clases disponibles</span></div></div>
    <div class="aula-stat"><i class="fa-solid fa-folder-open"></i><div><strong><?=count($materiales)?></strong><span>Materiales publicados</span></div></div>
    <div class="aula-stat"><i class="fa-solid fa-list-check"></i><div><strong><?=max(0,count($tareas)-$entregadas)?></strong><span>Tareas pendientes</span></div></div>
</div>
<div class="aula-section-heading"><h2>Contenido del curso</h2><span class="aula-muted"><?=count($clases)?> clases</span></div>
<?php if(!$clases):?><?php portalMostrarVacio('Todavía no hay clases','El contenido de este curso aún no está disponible.','fa-book-open');?><?php endif;?>
<?php foreach($clases as $indice=>$clase):$idClase=(int)$clase['id_clase'];?>
<section class="aula-class">
    <header class="aula-class-header"><div><span class="aula-class-number">Clase <?=($indice+1)?></span><h3><?=e($clase['titulo'])?></h3><p><?=e($clase['descripcion'])?></p></div><span class="aula-date"><i class="fa-regular fa-calendar"></i> <?=e(aulaFecha($clase['fecha']))?></span></header>
    <div class="aula-class-body">
        <h4 class="aula-subheading"><i class="fa-solid fa-folder-open"></i> Materiales de la clase</h4>
        <div class="aula-resource-list">
        <?php if(empty($materialesPorClase[$idClase])):?><div class="aula-empty">No hay materiales publicados para esta clase.</div><?php endif;?>
        <?php foreach(($materialesPorClase[$idClase]??[]) as $material):?>
            <div class="aula-resource"><span class="aula-resource-icon"><i class="fa-solid fa-file-arrow-down"></i></span><div class="aula-resource-info"><strong><?=e($material['nombre'])?></strong><p><?=e($material['descripcion'])?></p><small><?=e($material['tipo'])?></small></div>
            <?php if(trim((string)$material['url_archivo'])!==''):?><a class="aula-download" href="descargarMaterial.php?id=<?=(int)$material['id_material']?>"><i class="fa-solid fa-download"></i> Descargar material</a><?php else:?><span class="aula-muted">Archivo no disponible</span><?php endif;?>
            </div>
        <?php endforeach;?>
        </div>
        <h4 class="aula-subheading"><i class="fa-solid fa-clipboard-list"></i> Actividades y tareas</h4>
        <?php if(empty($tareasPorClase[$idClase])):?><div class="aula-empty">No hay tareas asignadas a esta clase.</div><?php endif;?>
        <?php foreach(($tareasPorClase[$idClase]??[]) as $tarea):$hecha=(int)$tarea['entregada']===1;?>
        <?php
            $limite = !empty($tarea['fecha_limite']) ? new DateTime($tarea['fecha_limite']) : null;
            $vencida = $limite !== null && $ahoraBD !== null && $ahoraBD > $limite;
        ?>
        <article class="aula-task <?= $hecha ? 'done' : '' ?>">
            <details class="aula-task-details">
                <summary class="aula-task-toggle">
                    <span class="aula-task-title"><?= e($tarea['titulo']) ?></span>
                    <i class="fa-solid fa-chevron-down aula-task-arrow" aria-hidden="true"></i>
                </summary>
                <div class="aula-task-content">
                    <div class="aula-task-top">
                        <span class="aula-badge <?= $hecha ? 'done' : '' ?>">
                            <?= $hecha ? 'Entregada' : ($vencida ? 'Vencida' : 'Pendiente') ?>
                        </span>
                    </div>
                    <p><?= e($tarea['descripcion'] ?? 'Sin descripción') ?></p>
                    <div class="aula-task-meta">
            <span><i class="fa-regular fa-calendar-plus"></i> Publicada: <?= e(aulaFecha($tarea['fecha_tarea'])) ?></span>
            <?php if($tarea['fecha_limite']):?><span><i class="fa-regular fa-clock"></i> Vence: <?=e(aulaFecha($tarea['fecha_limite']))?></span><?php endif;?><?php if($hecha):?><span><i class="fa-solid fa-check"></i> Entregada: <?=e(aulaFecha($tarea['fecha_entrega']))?></span><?php endif;?></div>
            <?php if ($hecha): ?>
                <div class="aula-submission-summary">
                    <strong><i class="fa-solid fa-circle-check"></i> Tu entrega fue registrada.</strong>
                    <?php if (!empty($tarea['texto_entrega'])): ?>
                        <p><?= nl2br(e($tarea['texto_entrega'])) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($tarea['archivo_entrega'])): ?>
                        <a href="descargarEntrega.php?id=<?= (int) $tarea['id_tarea'] ?>">Ver mi PDF entregado</a>
                    <?php endif; ?>
                </div>
            <?php elseif ($vencida): ?>
                <div class="aula-task-closed"><i class="fa-solid fa-lock"></i> El plazo de entrega finalizó.</div>
            <?php else: ?>
                <form class="aula-submit-form" action="enviarTarea.php?id_curso=<?= (int) $idCurso ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_tarea" value="<?= e($_SESSION['csrf_tarea']) ?>">
                    <input type="hidden" name="id_tarea" value="<?= (int) $tarea['id_tarea'] ?>">
                    <input type="hidden" name="id_curso" value="<?= (int) $idCurso ?>">
                    <label for="respuesta-<?= (int) $tarea['id_tarea'] ?>">Respuesta escrita (opcional si adjuntás PDF)</label>
                    <textarea id="respuesta-<?= (int) $tarea['id_tarea'] ?>" name="texto_entrega" maxlength="10000" rows="3" placeholder="Escribí tu respuesta..."></textarea>
                    <label for="archivo-<?= (int) $tarea['id_tarea'] ?>">Adjuntar PDF (máximo 25 MB)</label>
                    <input id="archivo-<?= (int) $tarea['id_tarea'] ?>" type="file" name="archivo_entrega" accept=".pdf,application/pdf">
                    <button type="submit" class="aula-download"><i class="fa-solid fa-paper-plane"></i> Enviar tarea</button>
                </form>
            <?php endif; ?>
                </div>
            </details>
        </article>
        <?php endforeach;?>
    </div>
</section>
<?php endforeach;?>
<?php endif;?>
</div>
<?php portalLayoutFin(); ?>

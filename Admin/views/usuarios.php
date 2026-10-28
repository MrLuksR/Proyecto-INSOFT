<?php
/** Vista de solo lectura: las consultas se centralizan en consultas.php. */
$datosListado = adminListado($pdo ?? null, $page);
$columnas = $datosListado['columnas'];
$filas = $datosListado['filas'];
?>
<div class="welcome-row"><div><div class="eyebrow">Registros de INSOFT</div><h1 class="heading"><?= e($pageTitle) ?></h1><p class="subtitle">Información consultada directamente desde la base de datos. Edición y registro disponibles en una próxima etapa.</p></div><span class="status-pill pill-blue"><?= count($filas) ?> registros</span></div>
<section class="panel"><div class="panel-head"><div><h2 class="panel-title">Listado de <?= e(mb_strtolower($pageTitle, 'UTF-8')) ?></h2><div class="panel-sub">Vista de solo lectura</div></div></div>
<div class="toolbar" style="margin-bottom:18px"><div class="search"><input type="search" id="listadoBusqueda" placeholder="Buscar en los registros..." aria-label="Buscar registros"></div></div>
<div class="table-wrap"><table id="listadoTabla"><thead><tr><?php foreach ($columnas as $etiqueta): ?><th><?= e($etiqueta) ?></th><?php endforeach; ?></tr></thead><tbody>
<?php foreach ($filas as $fila): ?><tr><?php foreach ($columnas as $columna => $etiqueta): ?><td><?= e($fila[$columna] ?? '—') ?></td><?php endforeach; ?></tr><?php endforeach; ?>
<?php if (!$filas): ?><tr><td colspan="<?= count($columnas) ?>">No hay registros para mostrar (o la consulta no pudo completarse).</td></tr><?php endif; ?>
</tbody></table></div></section>
<script>document.getElementById('listadoBusqueda')?.addEventListener('input',function(){const q=this.value.toLocaleLowerCase('es');document.querySelectorAll('#listadoTabla tbody tr').forEach(r=>r.hidden=!r.innerText.toLocaleLowerCase('es').includes(q))});</script>

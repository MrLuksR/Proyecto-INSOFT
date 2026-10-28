<?php
if(empty($_SESSION['csrf_cursos'])) $_SESSION['csrf_cursos']=bin2hex(random_bytes(32));
$cursos=[];$categorias=[];$errorCarga=null;
try {$cursos=adminObtenerCursos($pdo);$categorias=adminCategoriasCursos($pdo);}catch(Throwable $e){error_log('[INSOFT cursos] '.$e->getMessage());$errorCarga='No se pudieron cargar los cursos.';}
$mensaje=$_SESSION['mensaje_cursos']??null;$error=$_SESSION['error_cursos']??$errorCarga;
unset($_SESSION['mensaje_cursos'],$_SESSION['error_cursos']);
?>
<div class="welcome-row"><div><div class="eyebrow">Gestión institucional</div><h1 class="heading">Cursos</h1><p class="subtitle">Registrá cursos y administrá su estado sin eliminar información.</p></div><span class="status-pill pill-blue"><?= count($cursos) ?> registros</span></div>
<section class="panel" style="margin-bottom:20px;padding:24px">
<?php if($mensaje):?><p role="status" style="background:#dcfce7;color:#166534;padding:12px;border-radius:8px"><?= e($mensaje) ?></p><?php endif;?>
<?php if($error):?><p role="alert" style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px"><?= e($error) ?></p><?php endif;?>
<div class="panel-head"><div><h2 class="panel-title">Registrar nuevo curso</h2><div class="panel-sub">Los campos marcados con * son obligatorios.</div></div></div>
<form method="post" action="guardar_curso.php" autocomplete="off">
<input type="hidden" name="csrf" value="<?= e($_SESSION['csrf_cursos']) ?>">
<div class="form-grid">
<div class="field"><label for="curso_nombre">Nombre *</label><input id="curso_nombre" name="nombre" maxlength="150" required></div>
<div class="field"><label for="curso_modalidad">Modalidad *</label><input id="curso_modalidad" name="modalidad" maxlength="50" placeholder="Presencial" required></div>
<div class="field"><label for="curso_duracion">Duración *</label><input id="curso_duracion" name="duracion" maxlength="50" placeholder="6 meses" required></div>
<div class="field"><label for="curso_costo">Costo *</label><input id="curso_costo" name="costo" type="number" min="0" step="1" required></div>
<div class="field"><label for="curso_cupo">Cupo *</label><input id="curso_cupo" name="cupo" type="number" min="1" step="1" required></div>
<div class="field"><label for="curso_categoria">Categoría *</label><select id="curso_categoria" name="id_categoria" required><option value="">Seleccioná una categoría</option><?php foreach($categorias as $categoria):?><option value="<?= (int)$categoria['id_categoria'] ?>"><?= e($categoria['nombre']) ?></option><?php endforeach;?></select></div>
<!-- IMAGEN DEL CURSO -->

<div class="field full">

    <label for="curso_img">
        Imagen del curso *
    </label>

    <input
        type="file"
        id="curso_img"
        name="img"
        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
        required
    >

    <small>
        Formatos permitidos: JPG, PNG y WEBP.
        Tamaño máximo: 5 MB.
    </small>

</div>
<div class="field full"><label for="curso_descripcion">Descripción *</label><textarea id="curso_descripcion" name="descripcion" rows="4" required></textarea></div>
</div><div class="form-actions"><button class="btn" type="submit" <?= !$categorias?'disabled':'' ?>>＋ Registrar curso</button></div>
</form></section>
<section class="panel"><div class="panel-head"><div><h2 class="panel-title">Listado de cursos</h2><div class="panel-sub">Dar de baja conserva el curso y sus relaciones.</div></div></div>
<div class="toolbar" style="margin-bottom:18px"><div class="search"><input type="search" id="listadoBusqueda" placeholder="Buscar cursos..." aria-label="Buscar cursos"></div></div>
<div class="table-wrap"><table id="listadoTabla"><thead><tr><th>ID</th><th>Curso</th><th>Categoría</th><th>Modalidad</th><th>Duración</th><th>Costo</th><th>Cupo</th><th>Docente</th><th>Estado</th><th>Acción</th></tr></thead><tbody>
<?php foreach($cursos as $curso):$activo=mb_strtolower(trim($curso['estado']),'UTF-8')==='activo';?>
<tr><td><?= (int)$curso['id_curso'] ?></td><td><?= e($curso['nombre']) ?></td><td><?= e($curso['categoria']) ?></td><td><?= e($curso['modalidad']) ?></td><td><?= e($curso['duracion']) ?></td><td><?= e($curso['costo']) ?></td><td><?= (int)$curso['cupo'] ?></td><td><?= e($curso['nombre_usuario_docente']?:'Sin asignar') ?></td><td>
    <?php
        $estado = mb_strtolower(
            trim($curso['estado']),
            'UTF-8'
        );

        $claseEstado = match ($estado) {
            'activo'    => 'estado-activo',
            'inactivo'  => 'estado-inactivo',
            'en espera' => 'estado-espera',
            default     => 'estado-desconocido'
        };
    ?>

    <span class="estado-curso <?= $claseEstado ?>">
        <?= e($curso['estado']) ?>
    </span>
</td><td>
    <div style="display:flex; gap:8px; align-items:center;">

        <!-- EDITAR CURSO -->

        <a
            href="index.php?page=editar_curso&id=<?= (int) $curso['id_curso'] ?>"
            class="btn btn-light"
        >
            <i class="fa-solid fa-pen-to-square"></i>
            Editar
        </a>

        <!-- ALTA / BAJA -->

        <form
            method="post"
            action="estado_curso.php"
            onsubmit="return confirm('¿Confirmás el cambio de estado de este curso?')"
        >
            <input
                type="hidden"
                name="csrf"
                value="<?= e($_SESSION['csrf_cursos']) ?>"
            >

            <input
                type="hidden"
                name="id_curso"
                value="<?= (int) $curso['id_curso'] ?>"
            >

            <input
                type="hidden"
                name="accion"
                value="<?= $activo ? 'baja' : 'alta' ?>"
            >

            <button
                class="btn <?= $activo ? 'btn-light' : '' ?>"
                type="submit"
            >
                <?= $activo ? 'Dar de baja' : 'Dar de alta' ?>
            </button>

        </form>

    </div>
</td></tr>
<?php endforeach;?><?php if(!$cursos):?><tr><td colspan="10">No hay cursos para mostrar.</td></tr><?php endif;?>
</tbody></table></div></section>
<script>document.getElementById('listadoBusqueda')?.addEventListener('input',function(){const q=this.value.toLocaleLowerCase('es');document.querySelectorAll('#listadoTabla tbody tr').forEach(r=>r.hidden=!r.innerText.toLocaleLowerCase('es').includes(q))});</script>
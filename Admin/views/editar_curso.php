<?php

/* ==========================================================
   1. OBTENER CURSO
========================================================== */

$idCurso = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$idCurso || $idCurso < 1) {
    echo '<p>El curso solicitado no es válido.</p>';
    return;
}

try {

    $curso = adminObtenerCursoPorId($pdo, $idCurso);

    $categorias = adminCategoriasCursos($pdo);

} catch (Throwable $e) {

    error_log('[INSOFT editar curso] ' . $e->getMessage());

    echo '<p>No se pudieron cargar los datos del curso.</p>';
    return;
}

if (!$curso) {
    echo '<p>No se encontró el curso solicitado.</p>';
    return;
}

if (empty($_SESSION['csrf_cursos'])) {
    $_SESSION['csrf_cursos'] = bin2hex(random_bytes(32));
}

?>

<!-- ENCABEZADO -->

<div class="welcome-row">

    <div>
        <div class="eyebrow">Gestión institucional</div>

        <h1 class="heading">Editar curso</h1>

        <p class="subtitle">
            Modificá la información del curso seleccionado.
        </p>
    </div>

    <a href="index.php?page=cursos" class="btn btn-light">
        Volver a cursos
    </a>

</div>


<!-- FORMULARIO -->

<section class="panel" style="padding:24px;">

    <div class="panel-head">
        <div>
            <h2 class="panel-title">
                <?= e($curso['nombre']) ?>
            </h2>

            <div class="panel-sub">
                Curso #<?= (int) $curso['id_curso'] ?>
            </div>
        </div>
    </div>

    <form
        method="post"
        action="actualizar_curso.php"
        enctype="multipart/form-data"
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

        <div class="form-grid">

            <!-- NOMBRE -->

            <div class="field">
                <label>Nombre *</label>

                <input
                    type="text"
                    name="nombre"
                    maxlength="150"
                    value="<?= e($curso['nombre']) ?>"
                    required
                >
            </div>


            <!-- MODALIDAD -->

            <div class="field">
                <label>Modalidad *</label>

                <input
                    type="text"
                    name="modalidad"
                    maxlength="50"
                    value="<?= e($curso['modalidad']) ?>"
                    required
                >
            </div>


            <!-- DURACIÓN -->

            <div class="field">
                <label>Duración *</label>

                <input
                    type="text"
                    name="duracion"
                    maxlength="50"
                    value="<?= e($curso['duracion']) ?>"
                    required
                >
            </div>


            <!-- COSTO -->

            <div class="field">
                <label>Costo *</label>

                <input
                    type="number"
                    name="costo"
                    min="0"
                    step="0.01"
                    value="<?= e($curso['costo']) ?>"
                    required
                >
            </div>


            <!-- CUPO -->

            <div class="field">
                <label>Cupo *</label>

                <input
                    type="number"
                    name="cupo"
                    min="1"
                    value="<?= (int) $curso['cupo'] ?>"
                    required
                >
            </div>


            <!-- CATEGORÍA -->

            <div class="field">

                <label>Categoría *</label>

                <select name="id_categoria" required>

                    <?php foreach ($categorias as $categoria): ?>

                        <option
                            value="<?= (int) $categoria['id_categoria'] ?>"
                            <?= (int) $curso['id_categoria'] === (int) $categoria['id_categoria'] ? 'selected' : '' ?>
                        >
                            <?= e($categoria['nombre']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- IMAGEN -->

            <div class="field full">

                <label>Imagen del curso</label>

                <?php if (!empty($curso['img'])): ?>

                    <div style="margin-bottom:12px;">

                        <img
                            src="../Cursos/imagenes/<?= rawurlencode(basename($curso['img'])) ?>"
                            alt="Imagen actual del curso"
                            style="max-width:240px; border-radius:12px;"
                        >

                    </div>

                <?php endif; ?>

                <input
                    type="file"
                    name="img"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <small>
                    Dejá este campo vacío para conservar la imagen actual.
                    Tamaño máximo: 5 MB.
                </small>

            </div>


            <!-- DESCRIPCIÓN -->

            <div class="field full">

                <label>Descripción *</label>

                <textarea
                    name="descripcion"
                    rows="5"
                    required
                ><?= e($curso['descripcion']) ?></textarea>

            </div>

        </div>


        <!-- BOTONES -->

        <div class="form-actions">

            <a
                href="index.php?page=cursos"
                class="btn btn-light"
            >
                Cancelar
            </a>

            <button class="btn" type="submit">
                Guardar cambios
            </button>

        </div>

    </form>

</section>
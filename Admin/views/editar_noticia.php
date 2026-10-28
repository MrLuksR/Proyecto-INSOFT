<?php

/* ==========================================================
   1. OBTENER NOTICIA
========================================================== */

$idNoticia = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$idNoticia || $idNoticia < 1) {
    echo '<p>La noticia solicitada no es válida.</p>';
    return;
}

try {

    $noticia = adminObtenerNoticiaPorId(
        $pdo,
        $idNoticia
    );

} catch (Throwable $e) {

    error_log(
        '[INSOFT editar noticia] ' . $e->getMessage()
    );

    echo '<p>No se pudieron cargar los datos de la noticia.</p>';
    return;
}

if (!$noticia) {
    echo '<p>No se encontró la noticia solicitada.</p>';
    return;
}


/* ==========================================================
   2. TOKEN CSRF
========================================================== */

if (empty($_SESSION['csrf_noticias'])) {
    $_SESSION['csrf_noticias'] = bin2hex(random_bytes(32));
}


/* ==========================================================
   3. IMAGEN ACTUAL
========================================================== */

$nombreImagen = basename(
    (string) ($noticia['imagen'] ?? '')
);

$rutaFisica = __DIR__
    . '/../../Noticias/imagenes/'
    . $nombreImagen;

$rutaWeb = '../Noticias/imagenes/'
    . rawurlencode($nombreImagen);

$imagenDisponible = (
    $nombreImagen !== '' &&
    is_file($rutaFisica)
);

?>

<!-- ENCABEZADO -->

<div class="welcome-row">

    <div>

        <div class="eyebrow">
            Gestión institucional
        </div>

        <h1 class="heading">
            Editar noticia
        </h1>

        <p class="subtitle">
            Modificá la información de la noticia seleccionada.
        </p>

    </div>

    <a
        href="index.php?page=noticias"
        class="btn btn-light"
    >
        Volver a noticias
    </a>

</div>


<!-- FORMULARIO -->

<section class="panel" style="padding:24px;">

    <div class="panel-head">

        <div>

            <h2 class="panel-title">
                <?= e($noticia['titulo']) ?>
            </h2>

            <div class="panel-sub">
                Noticia #<?= (int) $noticia['id_noticia'] ?>
            </div>

        </div>

    </div>


    <form
        method="post"
        action="actualizar_noticia.php"
        enctype="multipart/form-data"
    >

        <!-- TOKEN CSRF -->

        <input
            type="hidden"
            name="csrf"
            value="<?= e($_SESSION['csrf_noticias']) ?>"
        >


        <!-- IDENTIFICADOR -->

        <input
            type="hidden"
            name="id_noticia"
            value="<?= (int) $noticia['id_noticia'] ?>"
        >


        <div class="form-grid">

            <!-- TÍTULO -->

            <div class="field full">

                <label>Título *</label>

                <input
                    type="text"
                    name="titulo"
                    maxlength="200"
                    value="<?= e($noticia['titulo']) ?>"
                    required
                >

            </div>


            <!-- FECHA -->

            <div class="field">

                <label>Fecha de publicación *</label>

                <input
                    type="date"
                    name="fecha"
                    value="<?= e(
                        substr((string) $noticia['fecha'], 0, 10)
                    ) ?>"
                    required
                >

            </div>


            <!-- ENLACE -->

            <div class="field">

                <label>Enlace</label>

                <input
                    type="url"
                    name="enlace"
                    maxlength="500"
                    value="<?= e($noticia['enlace']) ?>"
                    placeholder="https://..."
                >

            </div>


            <!-- IMAGEN -->

            <div class="field full">

                <label>Imagen de la noticia</label>

                <?php if ($imagenDisponible): ?>

                    <div style="margin-bottom:15px;">

                        <img
                            src="<?= e($rutaWeb) ?>"
                            alt="Imagen actual de la noticia"
                            style="
                                width:240px;
                                max-width:100%;
                                height:150px;
                                object-fit:cover;
                                border-radius:12px;
                                border:1px solid #e2e8f0;
                            "
                        >

                    </div>

                <?php else: ?>

                    <p class="panel-sub">
                        La noticia no tiene una imagen disponible.
                    </p>

                <?php endif; ?>


                <input
                    type="file"
                    name="imagen"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <small>
                    Dejá este campo vacío para conservar la imagen actual.
                    Tamaño máximo: 5 MB.
                </small>

            </div>


            <!-- CONTENIDO -->

            <div class="field full">

                <label>Contenido *</label>

                <textarea
                    name="contenido"
                    rows="8"
                    required
                ><?= e($noticia['contenido']) ?></textarea>

            </div>

        </div>


        <!-- INFORMACIÓN DEL REGISTRO -->

        <div style="margin-top:20px;">

            <p class="panel-sub">

                Registrada por:
                <strong>
                    <?= e($noticia['nombre_usuario_admin']) ?>
                </strong>

            </p>

            <p class="panel-sub">

                Estado:
                <strong>
                    <?= e($noticia['estado']) ?>
                </strong>

            </p>

        </div>


        <!-- BOTONES -->

        <div class="form-actions">

            <a
                href="index.php?page=noticias"
                class="btn btn-light"
            >
                Cancelar
            </a>

            <button
                class="btn"
                type="submit"
            >
                Guardar cambios
            </button>

        </div>

    </form>

</section>
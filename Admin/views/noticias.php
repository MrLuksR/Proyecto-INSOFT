<?php

/* ==========================================================
   1. CARGAR NOTICIAS
========================================================== */

$noticias = [];
$errorListado = false;

try {

    $noticias = adminObtenerNoticias($pdo);

} catch (Throwable $e) {

    error_log('[INSOFT noticias] ' . $e->getMessage());

    $errorListado = true;
}


/* ==========================================================
   2. TOKEN CSRF
========================================================== */

if (empty($_SESSION['csrf_noticias'])) {
    $_SESSION['csrf_noticias'] = bin2hex(random_bytes(32));
}


/* ==========================================================
   3. MENSAJES
========================================================== */

$mensaje = $_SESSION['mensaje_noticias'] ?? null;
$error = $_SESSION['error_noticias'] ?? null;

unset(
    $_SESSION['mensaje_noticias'],
    $_SESSION['error_noticias']
);

?>

<!-- ENCABEZADO -->

<div class="welcome-row">

    <div>

        <div class="eyebrow">
            Gestión institucional
        </div>

        <h1 class="heading">
            Noticias
        </h1>

        <p class="subtitle">
            Administrá las noticias y novedades institucionales.
        </p>

    </div>

    <button
        type="button"
        class="btn"
        onclick="mostrarFormularioNoticia()"
    >
        <i class="fa-solid fa-plus"></i>
        Nueva noticia
    </button>

</div>


<!-- MENSAJES -->

<?php if ($mensaje): ?>

    <div class="status-pill pill-green" style="margin-bottom:20px;">
        <?= e($mensaje) ?>
    </div>

<?php endif; ?>

<?php if ($error): ?>

    <div class="status-pill pill-orange" style="margin-bottom:20px;">
        <?= e($error) ?>
    </div>

<?php endif; ?>


<!-- FORMULARIO DE REGISTRO -->

<section
    class="panel"
    id="formularioNoticia"
    style="padding:24px; margin-bottom:24px; display:none;"
>

    <div class="panel-head">

        <div>

            <h2 class="panel-title">
                Registrar noticia
            </h2>

            <div class="panel-sub">
                Completá la información de la nueva publicación.
            </div>

        </div>

        <button
            type="button"
            class="btn btn-light"
            onclick="ocultarFormularioNoticia()"
        >
            Cancelar
        </button>

    </div>


    <form
        method="post"
        action="guardar_noticia.php"
        enctype="multipart/form-data"
    >

        <input
            type="hidden"
            name="csrf"
            value="<?= e($_SESSION['csrf_noticias']) ?>"
        >

        <div class="form-grid">

            <!-- TÍTULO -->

            <div class="field full">

                <label>Título *</label>

                <input
                    type="text"
                    name="titulo"
                    maxlength="200"
                    placeholder="Título de la noticia"
                    required
                >

            </div>


            <!-- FECHA -->

            <div class="field">

                <label>Fecha de publicación *</label>

                <input
                    type="date"
                    name="fecha"
                    value="<?= date('Y-m-d') ?>"
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
                    placeholder="https://..."
                >

            </div>


            <!-- IMAGEN -->

            <div class="field full">

                <label>Imagen de la noticia</label>

                <input
                    type="file"
                    name="imagen"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <small>
                    Formatos permitidos: JPG, PNG y WEBP.
                    Tamaño máximo: 5 MB.
                </small>

            </div>


            <!-- CONTENIDO -->

            <div class="field full">

                <label>Contenido *</label>

                <textarea
                    name="contenido"
                    rows="7"
                    placeholder="Escribí el contenido de la noticia..."
                    required
                ></textarea>

            </div>

        </div>


        <!-- BOTONES -->

        <div class="form-actions">

            <button
                type="button"
                class="btn btn-light"
                onclick="ocultarFormularioNoticia()"
            >
                Cancelar
            </button>

            <button
                type="submit"
                class="btn"
            >
                Publicar noticia
            </button>

        </div>

    </form>

</section>


<!-- LISTADO DE NOTICIAS -->

<section class="panel">

    <div class="panel-head">

        <div>

            <h2 class="panel-title">
                Noticias registradas
            </h2>

            <div class="panel-sub">
                Listado de publicaciones institucionales.
            </div>

        </div>

        <span class="status-pill pill-blue">
            <?= count($noticias) ?> noticias
        </span>

    </div>


    <!-- BUSCADOR -->

    <div class="toolbar" style="margin-bottom:18px;">

        <div class="search">

            <input
                type="search"
                id="buscarNoticias"
                placeholder="Buscar noticias..."
            >

        </div>

    </div>


    <!-- TABLA -->

    <div class="table-wrap">

        <table id="tablaNoticias">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Título</th>
                    <th>Fecha</th>
                    <th>Administrador</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>

            </thead>

            <tbody>

                <?php foreach ($noticias as $noticia): ?>

                    <?php

                    $nombreImagen = basename(
                        (string) ($noticia['imagen'] ?? '')
                    );

                    $rutaFisica = __DIR__
                        . '/../../Noticias/imagenes/'
                        . $nombreImagen;

                    $rutaWeb = '../Noticias/imagenes/'
                        . rawurlencode($nombreImagen);

                    $estado = mb_strtolower(
                        trim((string) $noticia['estado']),
                        'UTF-8'
                    );

                    $claseEstado = match ($estado) {
                        'activo' => 'pill-green',
                        'inactivo' => 'pill-orange',
                        default => 'pill-blue'
                    };

                    ?>

                    <tr>

                        <td>
                            <?= (int) $noticia['id_noticia'] ?>
                        </td>


                        <!-- IMAGEN -->

                        <td>

                            <?php if (
                                $nombreImagen !== '' &&
                                is_file($rutaFisica)
                            ): ?>

                                <img
                                    src="<?= e($rutaWeb) ?>"
                                    alt="Imagen de la noticia"
                                    style="
                                        width:80px;
                                        height:55px;
                                        object-fit:cover;
                                        border-radius:8px;
                                    "
                                >

                            <?php else: ?>

                                <span>Sin imagen</span>

                            <?php endif; ?>

                        </td>


                        <!-- TÍTULO -->

                        <td>

                            <strong>
                                <?= e($noticia['titulo']) ?>
                            </strong>

                            <div class="panel-sub">

                                <?= e(
                                    mb_strimwidth(
                                        $noticia['contenido'],
                                        0,
                                        100,
                                        '...',
                                        'UTF-8'
                                    )
                                ) ?>

                            </div>

                        </td>


                        <!-- FECHA -->

                        <td>
                            <?= e($noticia['fecha']) ?>
                        </td>


                        <!-- ADMINISTRADOR -->

                        <td>
                            <?= e($noticia['nombre_usuario_admin']) ?>
                        </td>


                        <!-- ESTADO -->

                        <td>

                            <span class="status-pill <?= $claseEstado ?>">

                                <?= e($noticia['estado']) ?>

                            </span>

                        </td>

                        <!-- ACCIONES -->

                        <td>

                            <a
                                href="index.php?page=editar_noticia&id=<?= (int) $noticia['id_noticia'] ?>"
                                class="btn btn-light"
                            >
                                <i class="fa-solid fa-pen-to-square"></i>
                                Editar
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>


                <?php if (!$noticias): ?>

                    <tr>

                        <td colspan="6">

                            <?= $errorListado
                                ? 'No se pudieron cargar las noticias.'
                                : 'Todavía no hay noticias registradas.'
                            ?>

                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>


<!-- JAVASCRIPT -->

<script>

function mostrarFormularioNoticia() {

    const formulario = document.getElementById(
        'formularioNoticia'
    );

    formulario.style.display = 'block';

    formulario.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });

}


function ocultarFormularioNoticia() {

    document.getElementById(
        'formularioNoticia'
    ).style.display = 'none';

}


// BUSCADOR

document.getElementById('buscarNoticias')
    ?.addEventListener('input', function () {

        const busqueda = this.value.toLocaleLowerCase('es');

        document.querySelectorAll(
            '#tablaNoticias tbody tr'
        ).forEach(fila => {

            fila.hidden = !fila.innerText
                .toLocaleLowerCase('es')
                .includes(busqueda);

        });

    });

</script>
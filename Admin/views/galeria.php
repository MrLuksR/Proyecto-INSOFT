<?php

/* ==========================================================
   1. CARGAR CLASES
========================================================== */

require_once __DIR__ . '/../../database/Clases/Galeria.php';
require_once __DIR__ . '/../../database/Clases/Administrativo.php';


/* ==========================================================
   2. INICIALIZAR VARIABLES
========================================================== */

$imagenes = [];

$mensaje = null;
$error = null;

$imagenEditar = null;

$directorioImagenes = __DIR__ . '/../../Galeria/imagenes galeria/';

$rutaWebImagenes = '../Galeria/imagenes galeria/';

if (empty($_SESSION['csrf_galeria'])) {

    $_SESSION['csrf_galeria'] = bin2hex(
        random_bytes(32)
    );

}


/* ==========================================================
   3. OBTENER ADMINISTRADOR
========================================================== */

$usuarioSesion = $_SESSION['usuario'] ?? null;

if (
    ($_SESSION['tipo_usuario'] ?? '') !== 'administrativo' ||
    !($usuarioSesion instanceof Administrativo)
) {
    throw new RuntimeException(
        'No tenés permisos para administrar la galería.'
    );
}

$nombreUsuarioAdmin = $usuarioSesion->getNombreUsuario();


/* ==========================================================
   4. PROCESAR FORMULARIOS
========================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $rutaImagenNueva = null;
    $operacionConfirmada = false;

    try {

        /* ==================================================
           VALIDAR TOKEN CSRF
        ================================================== */

        if (
            !isset($_POST['csrf']) ||
            !hash_equals(
                $_SESSION['csrf_galeria'],
                (string) $_POST['csrf']
            )
        ) {

            throw new RuntimeException(
                'Solicitud inválida.'
            );

        }


        /* ==================================================
           OBTENER ACCIÓN
        ================================================== */

        $accion = trim(
            (string) ($_POST['accion'] ?? '')
        );

        $accionesPermitidas = [
            'crear',
            'modificar',
            'desactivar',
            'reactivar'
        ];

        if (!in_array($accion, $accionesPermitidas, true)) {

            throw new InvalidArgumentException(
                'La operación solicitada no es válida.'
            );

        }


        /* ==================================================
           OBTENER REGISTRO EXISTENTE
        ================================================== */

        $registro = null;

        if ($accion !== 'crear') {

            $idImagen = filter_var(
                $_POST['id_imagen'] ?? null,
                FILTER_VALIDATE_INT
            );

            if (!$idImagen || $idImagen < 1) {

                throw new InvalidArgumentException(
                    'Seleccioná una imagen válida.'
                );

            }

            $consulta = $pdo->prepare("
                SELECT
                    id_imagen,
                    titulo,
                    fecha,
                    imagen,
                    nombre_usuario_admin,
                    estado
                FROM galeria
                WHERE id_imagen = :id
                LIMIT 1
            ");

            $consulta->execute([
                ':id' => $idImagen
            ]);

            $registro = $consulta->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {

                throw new RuntimeException(
                    'No se encontró la imagen seleccionada.'
                );

            }

        }


        /* ==================================================
           CREAR O MODIFICAR
        ================================================== */

        if (
            $accion === 'crear' ||
            $accion === 'modificar'
        ) {

            /* ==============================================
               5. OBTENER DATOS
            ============================================== */

            $titulo = trim(
                (string) ($_POST['titulo'] ?? '')
            );

            $fecha = trim(
                (string) ($_POST['fecha'] ?? '')
            );


            /* ==============================================
               6. VALIDAR DATOS
            ============================================== */

            if ($titulo === '') {

                throw new InvalidArgumentException(
                    'Ingresá un título.'
                );

            }

            if (mb_strlen($titulo) > 200) {

                throw new InvalidArgumentException(
                    'El título no puede superar los 200 caracteres.'
                );

            }

            $fechaValida = DateTimeImmutable::createFromFormat(
                '!Y-m-d',
                $fecha
            );

            if (
                !$fechaValida ||
                $fechaValida->format('Y-m-d') !== $fecha
            ) {

                throw new InvalidArgumentException(
                    'Ingresá una fecha válida.'
                );

            }


            /* ==============================================
               7. IMAGEN ACTUAL
            ============================================== */

            $nombreImagen = $registro
                ? (string) $registro['imagen']
                : '';

            $imagenAnterior = $registro
                ? basename((string) $registro['imagen'])
                : '';


            /* ==============================================
               8. PROCESAR ARCHIVO
            ============================================== */

            $hayArchivo = (
                isset($_FILES['imagen']) &&
                is_array($_FILES['imagen']) &&
                $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE
            );

            if ($accion === 'crear' && !$hayArchivo) {

                throw new InvalidArgumentException(
                    'Seleccioná una imagen para la galería.'
                );

            }

            if ($hayArchivo) {

                $archivo = $_FILES['imagen'];

                if ($archivo['error'] !== UPLOAD_ERR_OK) {

                    throw new RuntimeException(
                        'Ocurrió un error al subir la imagen.'
                    );

                }

                $archivoTemporal = $archivo['tmp_name'];

                $tamano = (int) $archivo['size'];


                /* ==========================================
                   VALIDAR TAMAÑO
                ========================================== */

                if (
                    $tamano <= 0 ||
                    $tamano > 5 * 1024 * 1024
                ) {

                    throw new InvalidArgumentException(
                        'La imagen no puede superar los 5 MB.'
                    );

                }

                if (!is_uploaded_file($archivoTemporal)) {

                    throw new RuntimeException(
                        'El archivo recibido no es válido.'
                    );

                }


                /* ==========================================
                   VALIDAR FORMATO
                ========================================== */

                $finfo = new finfo(FILEINFO_MIME_TYPE);

                $tipoMime = $finfo->file(
                    $archivoTemporal
                );

                $formatosPermitidos = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                if (
                    !isset($formatosPermitidos[$tipoMime]) ||
                    @getimagesize($archivoTemporal) === false
                ) {

                    throw new InvalidArgumentException(
                        'Solo se permiten imágenes JPG, PNG y WEBP.'
                    );

                }


                /* ==========================================
                   GENERAR NOMBRE
                ========================================== */

                $nombreImagen = 'galeria_'
                    . date('Ymd_His')
                    . '_'
                    . bin2hex(random_bytes(5))
                    . '.'
                    . $formatosPermitidos[$tipoMime];


                /* ==========================================
                   GUARDAR IMAGEN
                ========================================== */

                if (
                    !is_dir($directorioImagenes) &&
                    !mkdir(
                        $directorioImagenes,
                        0755,
                        true
                    ) &&
                    !is_dir($directorioImagenes)
                ) {

                    throw new RuntimeException(
                        'No se pudo crear la carpeta de imágenes.'
                    );

                }

                $rutaDestino = $directorioImagenes
                    . $nombreImagen;

                if (!move_uploaded_file(
                    $archivoTemporal,
                    $rutaDestino
                )) {

                    throw new RuntimeException(
                        'No se pudo guardar la imagen.'
                    );

                }

                $rutaImagenNueva = $rutaDestino;

            }


            /* ==============================================
               9. CREAR OBJETO GALERIA
            ============================================== */

            $galeria = new Galeria(
                $titulo,
                $fecha,
                $nombreImagen,
                $registro
                    ? $registro['nombre_usuario_admin']
                    : $nombreUsuarioAdmin,
                $registro
                    ? $registro['estado']
                    : 'Activo',
                $registro
                    ? (int) $registro['id_imagen']
                    : null
            );


            /* ==============================================
               10. GUARDAR MEDIANTE LA CLASE
            ============================================== */

            if ($accion === 'crear') {

                $galeria->crear($pdo);

                $mensaje = 'Imagen registrada correctamente.';

            } else {

                $galeria->modificar($pdo);

                $mensaje = 'Imagen modificada correctamente.';

            }

            $operacionConfirmada = true;


            /* ==============================================
               11. ELIMINAR IMAGEN ANTERIOR
            ============================================== */

            if (
                $accion === 'modificar' &&
                $rutaImagenNueva !== null &&
                $imagenAnterior !== '' &&
                $imagenAnterior !== $nombreImagen
            ) {

                $rutaAnterior = $directorioImagenes
                    . $imagenAnterior;

                if (is_file($rutaAnterior)) {

                    if (!unlink($rutaAnterior)) {

                        error_log(
                            '[INSOFT] No se pudo eliminar la imagen anterior.'
                        );

                    }

                }

            }

        }


        /* ==================================================
           12. CAMBIAR ESTADO
        ================================================== */

        if (
            $accion === 'desactivar' ||
            $accion === 'reactivar'
        ) {

            $galeria = new Galeria(
                $registro['titulo'],
                $registro['fecha'],
                $registro['imagen'],
                $registro['nombre_usuario_admin'],
                $registro['estado'],
                (int) $registro['id_imagen']
            );

            if ($accion === 'desactivar') {

                $galeria->darDeBaja($pdo);

                $mensaje = 'Imagen desactivada correctamente.';

            } else {

                $galeria->reactivar($pdo);

                $mensaje = 'Imagen reactivada correctamente.';

            }

            $operacionConfirmada = true;

        }


        /* ==================================================
           13. RENOVAR TOKEN
        ================================================== */

        $_SESSION['csrf_galeria'] = bin2hex(
            random_bytes(32)
        );

    } catch (Throwable $e) {

        /* ==================================================
           LIMPIAR ARCHIVO SI FALLÓ EL REGISTRO
        ================================================== */

        if (
            !$operacionConfirmada &&
            $rutaImagenNueva !== null &&
            is_file($rutaImagenNueva)
        ) {

            if (!unlink($rutaImagenNueva)) {

                error_log(
                    '[INSOFT] No se pudo limpiar la imagen subida.'
                );

            }

        }


        error_log(
            '[INSOFT galería] ' . $e->getMessage()
        );

        $error = $e instanceof InvalidArgumentException
            ? $e->getMessage()
            : 'No se pudo completar la operación.';

    }

}


/* ==========================================================
   14. CONSULTAR IMÁGENES
========================================================== */

try {

    $consulta = $pdo->prepare("
        SELECT
            id_imagen,
            titulo,
            fecha,
            imagen,
            nombre_usuario_admin,
            estado
        FROM galeria
        ORDER BY fecha DESC, id_imagen DESC
    ");

    $consulta->execute();

    $imagenes = $consulta->fetchAll(
        PDO::FETCH_ASSOC
    );

} catch (Throwable $e) {

    error_log(
        '[INSOFT consultar galería] ' . $e->getMessage()
    );

    $error = 'No se pudieron cargar las imágenes.';

}

?>


<!-- ========================================================
     15. ENCABEZADO
======================================================== -->

<div class="welcome-row">

    <div>

        <div class="eyebrow">
            Gestión institucional
        </div>

        <h1 class="heading">
            Galería de imágenes
        </h1>

        <p class="subtitle">
            Administrá las fotografías y publicaciones de la galería.
        </p>

    </div>

    <button
        type="button"
        class="btn"
        onclick="mostrarFormularioGaleria()"
    >
        <i class="fa-solid fa-plus"></i>
        Agregar imagen
    </button>

</div>


<!-- ========================================================
     16. MENSAJES
======================================================== -->

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


<!-- ========================================================
     17. FORMULARIO DE REGISTRO
======================================================== -->

<section
    class="panel"
    id="formularioGaleria"
    style="padding:24px;margin-bottom:24px;display:none;"
>

    <div class="panel-head">

        <div>

            <h2 class="panel-title">
                Registrar imagen
            </h2>

            <div class="panel-sub">
                Completá los datos de la nueva fotografía.
            </div>

        </div>

        <button
            type="button"
            class="btn btn-light"
            onclick="ocultarFormularioGaleria()"
        >
            Cancelar
        </button>

    </div>


    <form
        method="post"
        action="index.php?page=galeria"
        enctype="multipart/form-data"
    >

        <input
            type="hidden"
            name="csrf"
            value="<?= e($_SESSION['csrf_galeria']) ?>"
        >

        <input
            type="hidden"
            name="accion"
            value="crear"
        >


        <div class="form-grid">

            <div class="field full">

                <label>Título *</label>

                <input
                    type="text"
                    name="titulo"
                    maxlength="200"
                    placeholder="Título de la fotografía"
                    required
                >

            </div>


            <div class="field">

                <label>Fecha *</label>

                <input
                    type="date"
                    name="fecha"
                    value="<?= date('Y-m-d') ?>"
                    required
                >

            </div>


            <div class="field full">

                <label>Imagen *</label>

                <input
                    type="file"
                    name="imagen"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    required
                >

                <small>
                    Formatos permitidos: JPG, PNG y WEBP.
                    Tamaño máximo: 5 MB.
                </small>

            </div>

        </div>


        <div class="form-actions">

            <button
                type="button"
                class="btn btn-light"
                onclick="ocultarFormularioGaleria()"
            >
                Cancelar
            </button>

            <button
                type="submit"
                class="btn"
            >
                Guardar imagen
            </button>

        </div>

    </form>

</section>


<!-- ========================================================
     18. LISTADO
======================================================== -->

<section class="panel">

    <div class="panel-head">

        <div>

            <h2 class="panel-title">
                Imágenes registradas
            </h2>

            <div class="panel-sub">
                Fotografías almacenadas en INSOFT.
            </div>

        </div>

        <span class="status-pill pill-blue">
            <?= count($imagenes) ?> registros
        </span>

    </div>


    <div class="toolbar" style="margin-bottom:18px;">

        <div class="search">

            <input
                type="search"
                id="buscarGaleria"
                placeholder="Buscar imágenes..."
                aria-label="Buscar imágenes"
            >

        </div>

    </div>


    <div class="table-wrap">

        <table id="tablaGaleria">

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

                <?php foreach ($imagenes as $imagen): ?>

                    <?php

                    $nombreArchivo = basename(
                        (string) $imagen['imagen']
                    );

                    $rutaFisica = $directorioImagenes
                        . $nombreArchivo;

                    $rutaWeb = $rutaWebImagenes
                        . rawurlencode($nombreArchivo);

                    $imagenDisponible = (
                        $nombreArchivo !== '' &&
                        is_file($rutaFisica)
                    );

                    $activo = mb_strtolower(
                        trim((string) $imagen['estado']),
                        'UTF-8'
                    ) === 'activo';

                    ?>

                    <tr>

                        <td>
                            <?= (int) $imagen['id_imagen'] ?>
                        </td>


                        <!-- IMAGEN -->

                        <td>

                            <?php if ($imagenDisponible): ?>

                                <img
                                    src="<?= e($rutaWeb) ?>"
                                    alt="<?= e($imagen['titulo']) ?>"
                                    style="
                                        width:100px;
                                        height:70px;
                                        object-fit:cover;
                                        border-radius:8px;
                                    "
                                >

                            <?php else: ?>

                                <span class="panel-sub">
                                    Sin imagen
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- TÍTULO -->

                        <td>
                            <strong>
                                <?= e($imagen['titulo']) ?>
                            </strong>
                        </td>


                        <!-- FECHA -->

                        <td>
                            <?= e($imagen['fecha']) ?>
                        </td>


                        <!-- ADMINISTRADOR -->

                        <td>
                            <?= e($imagen['nombre_usuario_admin']) ?>
                        </td>


                        <!-- ESTADO -->

                        <td>

                            <span class="status-pill <?= $activo ? 'pill-green' : 'pill-orange' ?>">

                                <?= e($imagen['estado']) ?>

                            </span>

                        </td>


                        <!-- ACCIONES -->

                        <td>

                            <div style="
                                display:flex;
                                flex-wrap:wrap;
                                gap:8px;
                            ">

                                <button
                                    type="button"
                                    class="btn btn-light"
                                    onclick="editarImagenGaleria(<?= (int) $imagen['id_imagen'] ?>)"
                                >
                                    Editar
                                </button>


                                <form
                                    method="post"
                                    action="index.php?page=galeria"
                                >

                                    <input
                                        type="hidden"
                                        name="csrf"
                                        value="<?= e($_SESSION['csrf_galeria']) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="id_imagen"
                                        value="<?= (int) $imagen['id_imagen'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="accion"
                                        value="<?= $activo ? 'desactivar' : 'reactivar' ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-light"
                                    >
                                        <?= $activo ? 'Dar de baja' : 'Reactivar' ?>
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>


                <?php if (!$imagenes): ?>

                    <tr>

                        <td colspan="7">
                            No hay imágenes registradas.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>


<!-- ========================================================
     19. FORMULARIO DE EDICIÓN
======================================================== -->

<section
    class="panel"
    id="editarGaleria"
    style="padding:24px;margin-top:24px;display:none;"
>

    <div class="panel-head">

        <div>

            <h2 class="panel-title">
                Modificar imagen
            </h2>

            <div class="panel-sub">
                Actualizá la información de la fotografía.
            </div>

        </div>

        <button
            type="button"
            class="btn btn-light"
            onclick="ocultarEdicionGaleria()"
        >
            Cancelar
        </button>

    </div>


    <form
        method="post"
        action="index.php?page=galeria"
        enctype="multipart/form-data"
    >

        <input
            type="hidden"
            name="csrf"
            value="<?= e($_SESSION['csrf_galeria']) ?>"
        >

        <input
            type="hidden"
            name="accion"
            value="modificar"
        >

        <input
            type="hidden"
            name="id_imagen"
            id="editar_id_imagen"
        >


        <div class="form-grid">

            <div class="field full">

                <label>Título *</label>

                <input
                    type="text"
                    name="titulo"
                    id="editar_titulo"
                    maxlength="200"
                    required
                >

            </div>


            <div class="field">

                <label>Fecha *</label>

                <input
                    type="date"
                    name="fecha"
                    id="editar_fecha"
                    required
                >

            </div>


            <div class="field full">

                <label>Imagen actual</label>

                <img
                    id="editar_imagen_actual"
                    alt="Imagen actual"
                    style="
                        width:240px;
                        max-width:100%;
                        height:160px;
                        object-fit:cover;
                        border-radius:12px;
                        margin-bottom:15px;
                    "
                >

            </div>


            <div class="field full">

                <label>Reemplazar imagen</label>

                <input
                    type="file"
                    name="imagen"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <small>
                    Dejá este campo vacío para conservar la imagen actual.
                </small>

            </div>

        </div>


        <div class="form-actions">

            <button
                type="button"
                class="btn btn-light"
                onclick="ocultarEdicionGaleria()"
            >
                Cancelar
            </button>

            <button
                type="submit"
                class="btn"
            >
                Guardar cambios
            </button>

        </div>

    </form>

</section>


<!-- ========================================================
     20. JAVASCRIPT
======================================================== -->

<script>

const imagenesGaleria = <?= json_encode(
    $imagenes,
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT
) ?>;

const rutaImagenesGaleria = <?= json_encode(
    $rutaWebImagenes
) ?>;


/* ==========================================================
   MOSTRAR REGISTRO
========================================================== */

function mostrarFormularioGaleria() {

    const formulario = document.getElementById(
        'formularioGaleria'
    );

    formulario.style.display = 'block';

    formulario.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });

}


/* ==========================================================
   OCULTAR REGISTRO
========================================================== */

function ocultarFormularioGaleria() {

    document.getElementById(
        'formularioGaleria'
    ).style.display = 'none';

}


/* ==========================================================
   EDITAR IMAGEN
========================================================== */

function editarImagenGaleria(id) {

    const imagen = imagenesGaleria.find(
        registro => Number(registro.id_imagen) === Number(id)
    );

    if (!imagen) {
        return;
    }

    document.getElementById(
        'editar_id_imagen'
    ).value = imagen.id_imagen;

    document.getElementById(
        'editar_titulo'
    ).value = imagen.titulo;

    document.getElementById(
        'editar_fecha'
    ).value = imagen.fecha.substring(0, 10);


    const vistaPrevia = document.getElementById(
        'editar_imagen_actual'
    );

    if (imagen.imagen) {

        const nombreArchivo = String(imagen.imagen)
            .split(/[\\/]/)
            .pop();

        vistaPrevia.src = rutaImagenesGaleria
            + encodeURIComponent(nombreArchivo);

        vistaPrevia.style.display = 'block';

    } else {

        vistaPrevia.removeAttribute('src');

        vistaPrevia.style.display = 'none';

    }


    const formulario = document.getElementById(
        'editarGaleria'
    );

    formulario.querySelector('form').reset();

    // RESTAURAR DATOS DESPUÉS DEL RESET

    document.getElementById(
        'editar_id_imagen'
    ).value = imagen.id_imagen;

    document.getElementById(
        'editar_titulo'
    ).value = imagen.titulo;

    document.getElementById(
        'editar_fecha'
    ).value = imagen.fecha.substring(0, 10);


    formulario.style.display = 'block';

    formulario.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });

}


/* ==========================================================
   OCULTAR EDICIÓN
========================================================== */

function ocultarEdicionGaleria() {

    document.getElementById(
        'editarGaleria'
    ).style.display = 'none';

}


/* ==========================================================
   BUSCADOR
========================================================== */

document.getElementById('buscarGaleria')
    ?.addEventListener('input', function () {

        const busqueda = this.value.toLocaleLowerCase('es');

        document.querySelectorAll(
            '#tablaGaleria tbody tr'
        ).forEach(fila => {

            fila.hidden = !fila.innerText
                .toLocaleLowerCase('es')
                .includes(busqueda);

        });

    });

</script>
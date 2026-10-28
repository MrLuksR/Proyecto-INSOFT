<?php

/* ==========================================================
   1. INICIALIZACIÓN
========================================================== */

$mensajeBanner = null;
$errorBanner = null;

$banners = [];
$cursosDisponibles = [];

if (empty($_SESSION['csrf_banners'])) {
    $_SESSION['csrf_banners'] = bin2hex(random_bytes(32));
}


/* ==========================================================
   2. PROCESAR OPERACIONES
========================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        // VALIDAR TOKEN CSRF

        if (
            !isset($_POST['csrf']) ||
            !hash_equals(
                $_SESSION['csrf_banners'],
                (string) $_POST['csrf']
            )
        ) {
            throw new RuntimeException('Solicitud inválida.');
        }

        if (!($pdo instanceof PDO)) {
            throw new RuntimeException('No hay conexión con la base de datos.');
        }

        $accion = (string) ($_POST['accion'] ?? '');

        $accionesPermitidas = [
            'agregar',
            'eliminar',
            'subir',
            'bajar',
            'ordenar',
            'cambiar_curso',
            'cambiar_estado'
        ];

        if (!in_array($accion, $accionesPermitidas, true)) {
            throw new InvalidArgumentException('Acción no válida.');
        }

        $pdo->beginTransaction();

        /* ==================================================
           3. OBTENER BANNERS BLOQUEADOS
        ================================================== */

        $consulta = $pdo->prepare("
            SELECT id_banner, id_curso, orden, estado
            FROM banner
            ORDER BY orden ASC, id_banner ASC
            FOR UPDATE
        ");

        $consulta->execute();

        $registros = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $ids = array_map(
            fn($fila) => (int) $fila['id_banner'],
            $registros
        );

        $idBanner = filter_var(
            $_POST['id_banner'] ?? null,
            FILTER_VALIDATE_INT
        );

        $requiereBanner = $accion !== 'agregar';

        if (
            $requiereBanner &&
            (
                $idBanner === false ||
                $idBanner === null ||
                !in_array($idBanner, $ids, true)
            )
        ) {
            throw new InvalidArgumentException(
                'Seleccioná un banner válido.'
            );
        }


        /* ==================================================
           4. AGREGAR BANNER
        ================================================== */

        if ($accion === 'agregar') {

            $idCurso = filter_var(
                $_POST['id_curso'] ?? null,
                FILTER_VALIDATE_INT
            );

            if (!$idCurso || $idCurso < 1) {
                throw new InvalidArgumentException(
                    'Seleccioná un curso.'
                );
            }

            // COMPROBAR EXISTENCIA DEL CURSO

            $consulta = $pdo->prepare("
                SELECT id_curso
                FROM curso
                WHERE id_curso = :id
                LIMIT 1
            ");

            $consulta->execute([
                ':id' => $idCurso
            ]);

            if (!$consulta->fetchColumn()) {
                throw new InvalidArgumentException(
                    'El curso seleccionado no existe.'
                );
            }

            // EVITAR CURSOS REPETIDOS

            foreach ($registros as $registro) {

                if ((int) $registro['id_curso'] === $idCurso) {
                    throw new InvalidArgumentException(
                        'Este curso ya tiene un banner asignado.'
                    );
                }

            }

            $nuevoOrden = count($registros) + 1;

            $consulta = $pdo->prepare("
                INSERT INTO banner (
                    id_curso,
                    orden,
                    estado
                )
                VALUES (
                    :id_curso,
                    :orden,
                    'Activo'
                )
            ");

            $consulta->execute([
                ':id_curso' => $idCurso,
                ':orden' => $nuevoOrden
            ]);

            $mensajeBanner = 'Banner agregado correctamente.';
        }


        /* ==================================================
           5. ELIMINAR BANNER
        ================================================== */

        if ($accion === 'eliminar') {

            $consulta = $pdo->prepare("
                DELETE FROM banner
                WHERE id_banner = :id
            ");

            $consulta->execute([
                ':id' => $idBanner
            ]);

            // ELIMINAR DEL ARREGLO DE ORDEN

            $ids = array_values(
                array_filter(
                    $ids,
                    fn($id) => $id !== $idBanner
                )
            );

            $mensajeBanner = 'Banner eliminado correctamente.';
        }


        /* ==================================================
           6. SUBIR O BAJAR
        ================================================== */

        if ($accion === 'subir' || $accion === 'bajar') {

            $posicion = array_search(
                $idBanner,
                $ids,
                true
            );

            $nuevaPosicion = $accion === 'subir'
                ? $posicion - 1
                : $posicion + 1;

            if (
                $nuevaPosicion >= 0 &&
                $nuevaPosicion < count($ids)
            ) {

                $temporal = $ids[$posicion];

                $ids[$posicion] = $ids[$nuevaPosicion];

                $ids[$nuevaPosicion] = $temporal;

            }

            $mensajeBanner = 'Orden actualizado correctamente.';
        }


        /* ==================================================
           7. ASIGNAR POSICIÓN ESPECÍFICA
        ================================================== */

        if ($accion === 'ordenar') {

            $nuevoOrden = filter_var(
                $_POST['orden'] ?? null,
                FILTER_VALIDATE_INT
            );

            if (
                !$nuevoOrden ||
                $nuevoOrden < 1 ||
                $nuevoOrden > count($ids)
            ) {
                throw new InvalidArgumentException(
                    'La posición seleccionada no es válida.'
                );
            }

            // QUITAR EL BANNER DE SU POSICIÓN

            $ids = array_values(
                array_filter(
                    $ids,
                    fn($id) => $id !== $idBanner
                )
            );

            // INSERTAR EN LA NUEVA POSICIÓN

            array_splice(
                $ids,
                $nuevoOrden - 1,
                0,
                [$idBanner]
            );

            $mensajeBanner = 'Posición modificada correctamente.';
        }


        /* ==================================================
           8. CAMBIAR CURSO DEL SLOT
        ================================================== */

        if ($accion === 'cambiar_curso') {

            $idCurso = filter_var(
                $_POST['id_curso'] ?? null,
                FILTER_VALIDATE_INT
            );

            if (!$idCurso || $idCurso < 1) {
                throw new InvalidArgumentException(
                    'Seleccioná un curso válido.'
                );
            }

            // COMPROBAR EXISTENCIA

            $consulta = $pdo->prepare("
                SELECT id_curso
                FROM curso
                WHERE id_curso = :id
                LIMIT 1
            ");

            $consulta->execute([
                ':id' => $idCurso
            ]);

            if (!$consulta->fetchColumn()) {
                throw new InvalidArgumentException(
                    'El curso seleccionado no existe.'
                );
            }

            // EVITAR REPETICIONES

            foreach ($registros as $registro) {

                if (
                    (int) $registro['id_banner'] !== $idBanner &&
                    (int) $registro['id_curso'] === $idCurso
                ) {
                    throw new InvalidArgumentException(
                        'El curso ya está asignado a otro banner.'
                    );
                }

            }

            $consulta = $pdo->prepare("
                UPDATE banner
                SET id_curso = :id_curso
                WHERE id_banner = :id_banner
            ");

            $consulta->execute([
                ':id_curso' => $idCurso,
                ':id_banner' => $idBanner
            ]);

            $mensajeBanner = 'Curso del banner actualizado.';
        }


        /* ==================================================
           9. ACTIVAR O DESACTIVAR
        ================================================== */

        if ($accion === 'cambiar_estado') {

            $estadoActual = null;

            foreach ($registros as $registro) {

                if ((int) $registro['id_banner'] === $idBanner) {

                    $estadoActual = $registro['estado'];
                    break;

                }

            }

            $nuevoEstado = strtolower(
                trim((string) $estadoActual)
            ) === 'activo'
                ? 'Inactivo'
                : 'Activo';

            $consulta = $pdo->prepare("
                UPDATE banner
                SET estado = :estado
                WHERE id_banner = :id
            ");

            $consulta->execute([
                ':estado' => $nuevoEstado,
                ':id' => $idBanner
            ]);

            $mensajeBanner = 'Estado actualizado correctamente.';
        }


        /* ==================================================
           10. REORGANIZAR POSICIONES
        ================================================== */

        if (in_array(
            $accion,
            ['agregar', 'eliminar', 'subir', 'bajar', 'ordenar'],
            true
        )) {

            if ($accion === 'agregar') {
                $ids[] = (int) $pdo->lastInsertId();
            }

            $consulta = $pdo->prepare("
                UPDATE banner
                SET orden = :orden
                WHERE id_banner = :id
            ");

            foreach ($ids as $indice => $id) {

                $consulta->execute([
                    ':orden' => $indice + 1,
                    ':id' => $id
                ]);

            }

        }


        /* ==================================================
           11. CONFIRMAR TRANSACCIÓN
        ================================================== */

        $pdo->commit();

        $_SESSION['csrf_banners'] = bin2hex(random_bytes(32));

    } catch (Throwable $e) {

        if (
            $pdo instanceof PDO &&
            $pdo->inTransaction()
        ) {
            $pdo->rollBack();
        }

        error_log(
            '[INSOFT banners] ' . $e->getMessage()
        );

        $errorBanner = $e instanceof InvalidArgumentException
            ? $e->getMessage()
            : 'No se pudo completar la operación.';
    }

}


/* ==========================================================
   12. CONSULTAR BANNERS
========================================================== */

try {

    $consulta = $pdo->prepare("
        SELECT
            b.id_banner,
            b.id_curso,
            b.orden,
            b.estado,
            c.nombre AS nombre_curso,
            c.img,
            c.modalidad

        FROM banner b

        INNER JOIN curso c
            ON c.id_curso = b.id_curso

        ORDER BY b.orden ASC, b.id_banner ASC
    ");

    $consulta->execute();

    $banners = $consulta->fetchAll(PDO::FETCH_ASSOC);


    /* ======================================================
       13. CONSULTAR CURSOS DISPONIBLES
    ====================================================== */

    $consulta = $pdo->prepare("
        SELECT
            id_curso,
            nombre,
            img
        FROM curso
        ORDER BY nombre ASC
    ");

    $consulta->execute();

    $cursosDisponibles = $consulta->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {

    error_log(
        '[INSOFT listar banners] ' . $e->getMessage()
    );

    $errorBanner = 'No se pudieron cargar los banners.';
}

?>


<!-- ========================================================
     14. ENCABEZADO
======================================================== -->

<div class="welcome-row">

    <div>

        <div class="eyebrow">
            Gestión institucional
        </div>

        <h1 class="heading">
            Administrar banners
        </h1>

        <p class="subtitle">
            Organizá los cursos destacados y su posición en el carrusel.
        </p>

    </div>

    <span class="status-pill pill-blue">
        <?= count($banners) ?> banners
    </span>

</div>


<!-- ========================================================
     15. MENSAJES
======================================================== -->

<?php if ($mensajeBanner): ?>

    <div class="status-pill pill-green" style="margin-bottom:20px;">
        <?= e($mensajeBanner) ?>
    </div>

<?php endif; ?>


<?php if ($errorBanner): ?>

    <div class="status-pill pill-orange" style="margin-bottom:20px;">
        <?= e($errorBanner) ?>
    </div>

<?php endif; ?>


<!-- ========================================================
     16. AGREGAR BANNER
======================================================== -->

<section class="panel" style="padding:24px;margin-bottom:24px;">

    <div class="panel-head">

        <div>

            <h2 class="panel-title">
                Agregar banner
            </h2>

            <div class="panel-sub">
                Seleccioná un curso para agregarlo al carrusel.
            </div>

        </div>

    </div>


    <form
        method="post"
        action="index.php?page=banners"
    >

        <input
            type="hidden"
            name="csrf"
            value="<?= e($_SESSION['csrf_banners']) ?>"
        >

        <input
            type="hidden"
            name="accion"
            value="agregar"
        >


        <div class="form-grid">

            <div class="field full">

                <label>Curso *</label>

                <select name="id_curso" required>

                    <option value="">
                        Seleccionar curso
                    </option>

                    <?php foreach ($cursosDisponibles as $curso): ?>

                        <option value="<?= (int) $curso['id_curso'] ?>">

                            <?= e($curso['nombre']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

        </div>


        <div class="form-actions">

            <button
                type="submit"
                class="btn"
            >
                Agregar banner
            </button>

        </div>

    </form>

</section>


<!-- ========================================================
     17. LISTADO DE BANNERS
======================================================== -->

<section class="panel">

    <div class="panel-head">

        <div>

            <h2 class="panel-title">
                Orden del carrusel
            </h2>

            <div class="panel-sub">
                Modificá el orden, el curso o el estado de cada slot.
            </div>

        </div>

    </div>


    <div class="table-wrap">

        <table>

            <thead>

                <tr>
                    <th>Orden</th>
                    <th>Imagen</th>
                    <th>Curso</th>
                    <th>Slot</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>

            </thead>

            <tbody>

                <?php foreach ($banners as $indice => $banner): ?>

                    <?php

                    $nombreImagen = basename(
                        (string) ($banner['img'] ?? '')
                    );

                    $rutaFisica = __DIR__
                        . '/../../Cursos/imagenes/'
                        . $nombreImagen;

                    $rutaWeb = '../Cursos/imagenes/'
                        . rawurlencode($nombreImagen);

                    $imagenDisponible = (
                        $nombreImagen !== '' &&
                        is_file($rutaFisica)
                    );

                    $activo = strtolower(
                        trim((string) $banner['estado'])
                    ) === 'activo';

                    ?>

                    <tr>

                        <!-- ORDEN -->

                        <td>

                            <strong>
                                #<?= (int) $banner['orden'] ?>
                            </strong>

                        </td>


                        <!-- IMAGEN -->

                        <td>

                            <?php if ($imagenDisponible): ?>

                                <img
                                    src="<?= e($rutaWeb) ?>"
                                    alt="Imagen del curso"
                                    style="
                                        width:100px;
                                        height:65px;
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


                        <!-- CURSO -->

                        <td>

                            <strong>
                                <?= e($banner['nombre_curso']) ?>
                            </strong>

                            <div class="panel-sub">
                                <?= e($banner['modalidad']) ?>
                            </div>

                        </td>


                        <!-- SLOT -->

                        <td>

                            <form
                                method="post"
                                action="index.php?page=banners"
                                style="display:flex;gap:8px;align-items:center;"
                            >

                                <input
                                    type="hidden"
                                    name="csrf"
                                    value="<?= e($_SESSION['csrf_banners']) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="accion"
                                    value="ordenar"
                                >

                                <input
                                    type="hidden"
                                    name="id_banner"
                                    value="<?= (int) $banner['id_banner'] ?>"
                                >

                                <select
                                    name="orden"
                                    aria-label="Seleccionar posición"
                                >

                                    <?php for (
                                        $i = 1;
                                        $i <= count($banners);
                                        $i++
                                    ): ?>

                                        <option
                                            value="<?= $i ?>"
                                            <?= $i === $indice + 1 ? 'selected' : '' ?>
                                        >
                                            <?= $i ?>
                                        </option>

                                    <?php endfor; ?>

                                </select>

                                <button
                                    type="submit"
                                    class="btn btn-light"
                                >
                                    Aplicar
                                </button>

                            </form>

                        </td>


                        <!-- ESTADO -->

                        <td>

                            <span class="status-pill <?= $activo ? 'pill-green' : 'pill-orange' ?>">

                                <?= e($banner['estado']) ?>

                            </span>

                        </td>


                        <!-- ACCIONES -->

                        <td>

                            <div style="
                                display:flex;
                                flex-wrap:wrap;
                                gap:8px;
                                align-items:center;
                            ">

                                <!-- SUBIR -->

                                <form method="post" action="index.php?page=banners">

                                    <input type="hidden" name="csrf"
                                        value="<?= e($_SESSION['csrf_banners']) ?>">

                                    <input type="hidden" name="accion" value="subir">

                                    <input type="hidden" name="id_banner"
                                        value="<?= (int) $banner['id_banner'] ?>">

                                    <button
                                        class="btn btn-light"
                                        type="submit"
                                        title="Subir posición"
                                        <?= $indice === 0 ? 'disabled' : '' ?>
                                    >
                                        ↑
                                    </button>

                                </form>


                                <!-- BAJAR -->

                                <form method="post" action="index.php?page=banners">

                                    <input type="hidden" name="csrf"
                                        value="<?= e($_SESSION['csrf_banners']) ?>">

                                    <input type="hidden" name="accion" value="bajar">

                                    <input type="hidden" name="id_banner"
                                        value="<?= (int) $banner['id_banner'] ?>">

                                    <button
                                        class="btn btn-light"
                                        type="submit"
                                        title="Bajar posición"
                                        <?= $indice === count($banners) - 1 ? 'disabled' : '' ?>
                                    >
                                        ↓
                                    </button>

                                </form>


                                <!-- CAMBIAR ESTADO -->

                                <form method="post" action="index.php?page=banners">

                                    <input type="hidden" name="csrf"
                                        value="<?= e($_SESSION['csrf_banners']) ?>">

                                    <input type="hidden" name="accion" value="cambiar_estado">

                                    <input type="hidden" name="id_banner"
                                        value="<?= (int) $banner['id_banner'] ?>">

                                    <button
                                        class="btn btn-light"
                                        type="submit"
                                    >
                                        <?= $activo ? 'Desactivar' : 'Activar' ?>
                                    </button>

                                </form>


                                <!-- ELIMINAR -->

                                <form
                                    method="post"
                                    action="index.php?page=banners"
                                    onsubmit="return confirm('¿Eliminar este banner del carrusel?');"
                                >

                                    <input type="hidden" name="csrf"
                                        value="<?= e($_SESSION['csrf_banners']) ?>">

                                    <input type="hidden" name="accion" value="eliminar">

                                    <input type="hidden" name="id_banner"
                                        value="<?= (int) $banner['id_banner'] ?>">

                                    <button
                                        class="btn btn-light"
                                        type="submit"
                                        style="color:#dc2626;"
                                    >
                                        Eliminar
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>


                    <!-- CAMBIAR CURSO ASIGNADO -->

                    <tr>

                        <td colspan="6">

                            <form
                                method="post"
                                action="index.php?page=banners"
                                style="
                                    display:flex;
                                    flex-wrap:wrap;
                                    gap:10px;
                                    align-items:center;
                                    padding:8px 0;
                                "
                            >

                                <input
                                    type="hidden"
                                    name="csrf"
                                    value="<?= e($_SESSION['csrf_banners']) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="accion"
                                    value="cambiar_curso"
                                >

                                <input
                                    type="hidden"
                                    name="id_banner"
                                    value="<?= (int) $banner['id_banner'] ?>"
                                >

                                <strong>
                                    Cambiar curso del slot <?= $indice + 1 ?>:
                                </strong>

                                <select name="id_curso" required>

                                    <?php foreach ($cursosDisponibles as $curso): ?>

                                        <option
                                            value="<?= (int) $curso['id_curso'] ?>"
                                            <?= (int) $curso['id_curso'] === (int) $banner['id_curso'] ? 'selected' : '' ?>
                                        >
                                            <?= e($curso['nombre']) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                                <button
                                    type="submit"
                                    class="btn btn-light"
                                >
                                    Cambiar curso
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>


                <?php if (!$banners): ?>

                    <tr>

                        <td colspan="6">
                            No hay banners registrados.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>
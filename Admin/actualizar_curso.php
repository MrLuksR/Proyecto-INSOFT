<?php

/* ==========================================================
   1. CARGAR CLASES Y VALIDAR SESIÓN
========================================================== */

require_once __DIR__ . '/../database/Clases/Administrativo.php';
require_once __DIR__ . '/../database/Clases/Curso.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['usuario'], $_SESSION['tipo_usuario']) ||
    $_SESSION['tipo_usuario'] !== 'administrativo' ||
    !($_SESSION['usuario'] instanceof Administrativo)
) {
    header('Location: ../login/loginn.php');
    exit;
}

require_once __DIR__ . '/../database/consultas/conexion.php';
require_once __DIR__ . '/../database/consultas/panelAdmin/consultas.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=cursos');
    exit;
}


/* ==========================================================
   2. ACTUALIZAR CURSO
========================================================== */

$rutaImagenNueva = null;
$actualizacionConfirmada = false;

try {

    // VALIDAR CSRF

    if (
        empty($_SESSION['csrf_cursos']) ||
        !hash_equals(
            $_SESSION['csrf_cursos'],
            (string) ($_POST['csrf'] ?? '')
        )
    ) {
        throw new RuntimeException('Solicitud inválida.');
    }


    // OBTENER IDENTIFICADOR

    $idCurso = filter_var(
        $_POST['id_curso'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$idCurso || $idCurso < 1) {
        throw new InvalidArgumentException('Curso inválido.');
    }


    // CONSULTAR CURSO EXISTENTE

    $datos = adminObtenerCursoPorId($pdo, $idCurso);

    if (!$datos) {
        throw new RuntimeException('El curso no existe.');
    }


    /* ======================================================
       3. VALIDAR DATOS
    ====================================================== */

    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $modalidad = trim((string) ($_POST['modalidad'] ?? ''));
    $duracion = trim((string) ($_POST['duracion'] ?? ''));
    $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

    $costo = filter_var(
        $_POST['costo'] ?? null,
        FILTER_VALIDATE_FLOAT
    );

    $cupo = filter_var(
        $_POST['cupo'] ?? null,
        FILTER_VALIDATE_INT
    );

    $idCategoria = filter_var(
        $_POST['id_categoria'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (
        $nombre === '' ||
        $modalidad === '' ||
        $duracion === '' ||
        $descripcion === '' ||
        $costo === false ||
        !is_finite((float) $costo) ||
        $costo < 0 ||
        $cupo === false ||
        $cupo < 1 ||
        $idCategoria === false ||
        $idCategoria < 1
    ) {
        throw new InvalidArgumentException(
            'Verificá los datos ingresados.'
        );
    }


    /* ======================================================
       4. CONSERVAR IMAGEN EXISTENTE
    ====================================================== */

    $nombreImagen = $datos['img'];


    /* ======================================================
       5. PROCESAR IMAGEN NUEVA
    ====================================================== */

    if (
        isset($_FILES['img']) &&
        $_FILES['img']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        $archivo = $_FILES['img'];

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException(
                'No se pudo recibir la imagen.'
            );
        }

        if (
            $archivo['size'] <= 0 ||
            $archivo['size'] > 5 * 1024 * 1024
        ) {
            throw new RuntimeException(
                'La imagen no puede superar los 5 MB.'
            );
        }

        if (!is_uploaded_file($archivo['tmp_name'])) {
            throw new RuntimeException(
                'El archivo recibido no es válido.'
            );
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))
            ->file($archivo['tmp_name']);

        $formatos = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (
            !isset($formatos[$mime]) ||
            @getimagesize($archivo['tmp_name']) === false
        ) {
            throw new RuntimeException(
                'Formato de imagen no permitido.'
            );
        }

        $fecha = (new DateTimeImmutable(
            'now',
            new DateTimeZone('America/Montevideo')
        ))->format('Ymd_His');

        $nombreImagen = 'curso_'
            . $fecha
            . '_'
            . bin2hex(random_bytes(4))
            . '.'
            . $formatos[$mime];

        $directorio = __DIR__ . '/../Cursos/imagenes/';

        if (
            !is_dir($directorio) &&
            !mkdir($directorio, 0755, true) &&
            !is_dir($directorio)
        ) {
            throw new RuntimeException(
                'No se pudo crear el directorio de imágenes.'
            );
        }

        $rutaImagenNueva = $directorio . $nombreImagen;

        if (!move_uploaded_file(
            $archivo['tmp_name'],
            $rutaImagenNueva
        )) {
            throw new RuntimeException(
                'No se pudo guardar la nueva imagen.'
            );
        }

    }


    /* ======================================================
       6. CREAR OBJETO CURSO
    ====================================================== */

    $curso = new Curso(
        $nombre,
        $modalidad,
        $duracion,
        $descripcion,
        (float) $costo,
        (int) $cupo,
        $datos['estado'],
        $nombreImagen,
        (int) $idCategoria,
        (int) $idCurso
    );


    /* ======================================================
       7. ACTUALIZAR MEDIANTE LA CLASE
    ====================================================== */

    $curso->modificar($pdo);

    $actualizacionConfirmada = true;


    /* ======================================================
       8. ELIMINAR IMAGEN ANTERIOR
    ====================================================== */

    if (
        $rutaImagenNueva !== null &&
        !empty($datos['img']) &&
        basename($datos['img']) === $datos['img']
    ) {

        $rutaAnterior = __DIR__
            . '/../Cursos/imagenes/'
            . $datos['img'];

        if (is_file($rutaAnterior)) {
            if (!unlink($rutaAnterior)) {
                error_log(
                    '[INSOFT] No se pudo eliminar la imagen anterior del curso.'
                );
            }
        }

    }


    /* ======================================================
       9. CONFIRMAR ACTUALIZACIÓN
    ====================================================== */

    $_SESSION['mensaje_cursos'] =
        'El curso fue actualizado correctamente.';

    $_SESSION['csrf_cursos'] = bin2hex(random_bytes(32));

} catch (Throwable $e) {

    if (
        !$actualizacionConfirmada &&
        $rutaImagenNueva !== null &&
        is_file($rutaImagenNueva)
    ) {
        unlink($rutaImagenNueva);
    }

    error_log('[INSOFT actualizar curso] ' . $e->getMessage());

    $_SESSION['error_cursos'] =
        $e instanceof InvalidArgumentException
            ? $e->getMessage()
            : 'No se pudo actualizar el curso.';

}

header('Location: index.php?page=cursos');
exit;
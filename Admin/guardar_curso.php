<?php

/* ==========================================================
   1. CARGAR CLASES
========================================================== */

require_once __DIR__ . '/../database/Clases/Administrativo.php';
require_once __DIR__ . '/../database/Clases/Curso.php';


// INICIAR SESIÓN

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// VALIDAR ADMINISTRADOR

if (
    !isset($_SESSION['usuario'], $_SESSION['tipo_usuario']) ||
    $_SESSION['tipo_usuario'] !== 'administrativo' ||
    !($_SESSION['usuario'] instanceof Administrativo)
) {
    header('Location: ../login/loginn.php');
    exit;
}


/* ==========================================================
   2. CARGAR CONEXIÓN
========================================================== */

require_once __DIR__ . '/../database/consultas/conexion.php';


/* ==========================================================
   3. VALIDAR SOLICITUD
========================================================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=cursos');
    exit;
}


/* ==========================================================
   4. INICIALIZAR VARIABLES
========================================================== */

$rutaImagenGuardada = null;
$cursoRegistrado = false;


/* ==========================================================
   5. PROCESAR REGISTRO
========================================================== */

try {

    // VALIDAR TOKEN CSRF

    if (
        empty($_SESSION['csrf_cursos']) ||
        !hash_equals(
            $_SESSION['csrf_cursos'],
            (string) ($_POST['csrf'] ?? '')
        )
    ) {
        throw new RuntimeException('Solicitud inválida.');
    }


    /* ======================================================
       6. OBTENER DATOS
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


    /* ======================================================
       7. VALIDAR DATOS
    ====================================================== */

    if (
        $nombre === '' ||
        $modalidad === '' ||
        $duracion === '' ||
        $descripcion === ''
    ) {
        throw new InvalidArgumentException(
            'Completá todos los campos obligatorios.'
        );
    }

    if (
        $costo === false ||
        !is_finite((float) $costo) ||
        $costo < 0
    ) {
        throw new InvalidArgumentException(
            'Ingresá un costo válido.'
        );
    }

    if ($cupo === false || $cupo < 1) {
        throw new InvalidArgumentException(
            'Ingresá un cupo válido.'
        );
    }

    if ($idCategoria === false || $idCategoria < 1) {
        throw new InvalidArgumentException(
            'Seleccioná una categoría válida.'
        );
    }


    /* ======================================================
       8. VALIDAR IMAGEN
    ====================================================== */

    if (
        !isset($_FILES['img']) ||
        !is_array($_FILES['img']) ||
        $_FILES['img']['error'] !== UPLOAD_ERR_OK
    ) {
        throw new InvalidArgumentException(
            'Seleccioná una imagen válida.'
        );
    }

    $archivo = $_FILES['img'];

    $archivoTemporal = $archivo['tmp_name'];

    $tamano = (int) $archivo['size'];


    // VALIDAR TAMAÑO MÁXIMO DE 5 MB

    if ($tamano <= 0 || $tamano > 5 * 1024 * 1024) {
        throw new InvalidArgumentException(
            'La imagen no puede superar los 5 MB.'
        );
    }


    // COMPROBAR ARCHIVO SUBIDO

    if (!is_uploaded_file($archivoTemporal)) {
        throw new RuntimeException(
            'El archivo recibido no es válido.'
        );
    }


    /* ======================================================
       9. VALIDAR FORMATO
    ====================================================== */

    $finfo = new finfo(FILEINFO_MIME_TYPE);

    $tipoMime = $finfo->file($archivoTemporal);

    $formatosPermitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (
        !isset($formatosPermitidos[$tipoMime]) ||
        @getimagesize($archivoTemporal) === false
    ) {
        throw new InvalidArgumentException(
            'Solo se permiten imágenes JPG, PNG o WEBP.'
        );
    }


    /* ======================================================
       10. GENERAR NOMBRE DE IMAGEN
    ====================================================== */

    $fecha = new DateTimeImmutable(
        'now',
        new DateTimeZone('America/Montevideo')
    );

    $nombreImagen = 'curso_'
        . $fecha->format('Ymd_His')
        . '_'
        . bin2hex(random_bytes(4))
        . '.'
        . $formatosPermitidos[$tipoMime];


    /* ======================================================
       11. GUARDAR IMAGEN
    ====================================================== */

    $directorio = __DIR__ . '/../Cursos/imagenes/';

    if (
        !is_dir($directorio) &&
        !mkdir($directorio, 0755, true) &&
        !is_dir($directorio)
    ) {
        throw new RuntimeException(
            'No se pudo crear la carpeta de imágenes.'
        );
    }

    $rutaDestino = $directorio . $nombreImagen;

    if (!move_uploaded_file($archivoTemporal, $rutaDestino)) {
        throw new RuntimeException(
            'No se pudo guardar la imagen.'
        );
    }

    $rutaImagenGuardada = $rutaDestino;


    /* ======================================================
       12. CREAR OBJETO CURSO
    ====================================================== */

    $curso = new Curso(
        $nombre,
        $modalidad,
        $duracion,
        $descripcion,
        (float) $costo,
        (int) $cupo,
        'En espera',
        $nombreImagen,
        (int) $idCategoria
    );


    /* ======================================================
       13. REGISTRAR MEDIANTE EL MÉTODO DE LA CLASE
    ====================================================== */

    $resultado = $curso->crear($pdo);

    if (!$resultado) {
        throw new RuntimeException(
            'No se pudo registrar el curso.'
        );
    }

    $cursoRegistrado = true;


    /* ======================================================
       14. CONFIRMAR REGISTRO
    ====================================================== */

    $_SESSION['mensaje_cursos'] =
        'El curso fue registrado correctamente.';

    $_SESSION['csrf_cursos'] = bin2hex(random_bytes(32));


} catch (Throwable $e) {

    // ELIMINAR IMAGEN SI NO SE REGISTRÓ EL CURSO

    if (
        !$cursoRegistrado &&
        $rutaImagenGuardada !== null &&
        is_file($rutaImagenGuardada)
    ) {
        if (!unlink($rutaImagenGuardada)) {
            error_log(
                '[INSOFT] No se pudo eliminar la imagen del registro fallido.'
            );
        }
    }

    error_log(
        '[INSOFT registrar curso] ' . $e->getMessage()
    );

    $_SESSION['error_cursos'] =
        $e instanceof InvalidArgumentException
            ? $e->getMessage()
            : 'No se pudo registrar el curso.';

}


/* ==========================================================
   15. REDIRECCIONAR
========================================================== */

header('Location: index.php?page=cursos');
exit;
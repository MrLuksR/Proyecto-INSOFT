<?php

/* ==========================================================
   1. CARGAR CLASES
========================================================== */

require_once __DIR__ . '/../database/Clases/Administrativo.php';
require_once __DIR__ . '/../database/Clases/Noticia.php';


// INICIAR SESIÓN

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// COMPROBAR ADMINISTRADOR

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
    header('Location: index.php?page=noticias');
    exit;
}


/* ==========================================================
   4. INICIALIZAR VARIABLES
========================================================== */

$rutaImagenGuardada = null;
$noticiaRegistrada = false;


/* ==========================================================
   5. PROCESAR REGISTRO
========================================================== */

try {

    // VALIDAR TOKEN CSRF

    if (
        empty($_SESSION['csrf_noticias']) ||
        !hash_equals(
            $_SESSION['csrf_noticias'],
            (string) ($_POST['csrf'] ?? '')
        )
    ) {
        throw new RuntimeException('Solicitud inválida.');
    }


    /* ======================================================
       6. OBTENER DATOS
    ====================================================== */

    $fecha = trim((string) ($_POST['fecha'] ?? ''));

    $titulo = trim((string) ($_POST['titulo'] ?? ''));

    $contenido = trim((string) ($_POST['contenido'] ?? ''));

    $enlace = trim((string) ($_POST['enlace'] ?? ''));


    // ADMINISTRADOR AUTENTICADO

    $nombreUsuarioAdmin =
        $_SESSION['usuario']->getNombreUsuario();


    /* ======================================================
       7. VALIDAR DATOS
    ====================================================== */

    if ($titulo === '') {
        throw new InvalidArgumentException(
            'Ingresá el título de la noticia.'
        );
    }

    if ($contenido === '') {
        throw new InvalidArgumentException(
            'Ingresá el contenido de la noticia.'
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

    if (
        $enlace !== '' &&
        filter_var($enlace, FILTER_VALIDATE_URL) === false
    ) {
        throw new InvalidArgumentException(
            'Ingresá un enlace válido.'
        );
    }

    if (
        $enlace !== '' &&
        !in_array(
            strtolower((string) parse_url($enlace, PHP_URL_SCHEME)),
            ['http', 'https'],
            true
        )
    ) {
        throw new InvalidArgumentException(
            'El enlace debe utilizar HTTP o HTTPS.'
        );
    }


    /* ======================================================
       8. PROCESAR IMAGEN
    ====================================================== */

    $nombreImagen = '';

    if (
        isset($_FILES['imagen']) &&
        is_array($_FILES['imagen']) &&
        $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        $archivo = $_FILES['imagen'];

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException(
                'Ocurrió un error al subir la imagen.'
            );
        }

        $archivoTemporal = $archivo['tmp_name'];

        $tamano = (int) $archivo['size'];


        // VALIDAR TAMAÑO

        if ($tamano <= 0 || $tamano > 5 * 1024 * 1024) {
            throw new InvalidArgumentException(
                'La imagen no puede superar los 5 MB.'
            );
        }


        // COMPROBAR ARCHIVO

        if (!is_uploaded_file($archivoTemporal)) {
            throw new RuntimeException(
                'El archivo recibido no es válido.'
            );
        }


        /* ==================================================
           9. VALIDAR FORMATO
        ================================================== */

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
                'Solo se permiten imágenes JPG, PNG y WEBP.'
            );
        }


        /* ==================================================
           10. GENERAR NOMBRE
        ================================================== */

        $fechaActual = new DateTimeImmutable(
            'now',
            new DateTimeZone('America/Montevideo')
        );

        $nombreImagen = 'noticia_'
            . $fechaActual->format('Ymd_His')
            . '_'
            . bin2hex(random_bytes(4))
            . '.'
            . $formatosPermitidos[$tipoMime];


        /* ==================================================
           11. GUARDAR ARCHIVO
        ================================================== */

        $directorio = __DIR__ . '/../Noticias/imagenes/';

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

        if (!move_uploaded_file(
            $archivoTemporal,
            $rutaDestino
        )) {
            throw new RuntimeException(
                'No se pudo guardar la imagen.'
            );
        }

        $rutaImagenGuardada = $rutaDestino;

    }


    /* ======================================================
       12. CREAR OBJETO NOTICIA
    ====================================================== */

    $noticia = new Noticia(
        $fecha,
        $titulo,
        $contenido,
        $nombreImagen,
        $enlace,
        $nombreUsuarioAdmin,
        'Activo'
    );


    /* ======================================================
       13. REGISTRAR MEDIANTE LA CLASE
    ====================================================== */

    $resultado = $noticia->crear($pdo);

    if (!$resultado) {
        throw new RuntimeException(
            'No se pudo registrar la noticia.'
        );
    }

    $noticiaRegistrada = true;


    /* ======================================================
       14. CONFIRMAR REGISTRO
    ====================================================== */

    $_SESSION['mensaje_noticias'] =
        'La noticia fue registrada correctamente.';

    $_SESSION['csrf_noticias'] = bin2hex(random_bytes(32));


} catch (Throwable $e) {

    // ELIMINAR IMAGEN SI FALLÓ EL REGISTRO

    if (
        !$noticiaRegistrada &&
        $rutaImagenGuardada !== null &&
        is_file($rutaImagenGuardada)
    ) {
        if (!unlink($rutaImagenGuardada)) {
            error_log(
                '[INSOFT] No se pudo eliminar la imagen de la noticia fallida.'
            );
        }
    }


    // REGISTRAR ERROR

    error_log(
        '[INSOFT registrar noticia] ' . $e->getMessage()
    );


    // MENSAJE DE ERROR

    $_SESSION['error_noticias'] =
        $e instanceof InvalidArgumentException
            ? $e->getMessage()
            : 'No se pudo registrar la noticia.';

}


/* ==========================================================
   15. REDIRECCIONAR
========================================================== */

header('Location: index.php?page=noticias');
exit;
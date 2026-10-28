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
   2. CARGAR CONEXIÓN Y CONSULTAS
========================================================== */

require_once __DIR__ . '/../database/consultas/conexion.php';

require_once __DIR__ . '/../database/consultas/panelAdmin/consultas.php';


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

$rutaImagenNueva = null;
$actualizacionConfirmada = false;


/* ==========================================================
   5. ACTUALIZAR NOTICIA
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
       6. OBTENER IDENTIFICADOR
    ====================================================== */

    $idNoticia = filter_var(
        $_POST['id_noticia'] ?? null,
        FILTER_VALIDATE_INT
    );

    if ($idNoticia === false || $idNoticia < 1) {
        throw new InvalidArgumentException(
            'La noticia seleccionada no es válida.'
        );
    }


    /* ======================================================
       7. OBTENER NOTICIA ACTUAL
    ====================================================== */

    $datos = adminObtenerNoticiaPorId(
        $pdo,
        $idNoticia
    );

    if (!$datos) {
        throw new RuntimeException(
            'No se encontró la noticia.'
        );
    }


    /* ======================================================
       8. OBTENER DATOS DEL FORMULARIO
    ====================================================== */

    $fecha = trim((string) ($_POST['fecha'] ?? ''));

    $titulo = trim((string) ($_POST['titulo'] ?? ''));

    $contenido = trim((string) ($_POST['contenido'] ?? ''));

    $enlace = trim((string) ($_POST['enlace'] ?? ''));


    /* ======================================================
       9. VALIDAR DATOS
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


    // VALIDAR FECHA

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


    // VALIDAR ENLACE

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
       10. CONSERVAR IMAGEN ACTUAL
    ====================================================== */

    $nombreImagen = (string) ($datos['imagen'] ?? '');

    $imagenAnterior = basename($nombreImagen);


    /* ======================================================
       11. PROCESAR IMAGEN NUEVA
    ====================================================== */

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


        // COMPROBAR ARCHIVO SUBIDO

        if (!is_uploaded_file($archivoTemporal)) {
            throw new RuntimeException(
                'El archivo recibido no es válido.'
            );
        }


        /* ==================================================
           12. VALIDAR FORMATO
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
           13. GENERAR NOMBRE NUEVO
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
           14. GUARDAR IMAGEN NUEVA
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
                'No se pudo guardar la imagen nueva.'
            );
        }

        $rutaImagenNueva = $rutaDestino;

    }


    /* ======================================================
       15. CREAR OBJETO NOTICIA
    ====================================================== */

    $noticia = new Noticia(
        $fecha,
        $titulo,
        $contenido,
        $nombreImagen,
        $enlace,
        $datos['nombre_usuario_admin'],
        $datos['estado'],
        (int) $idNoticia
    );


    /* ======================================================
       16. MODIFICAR MEDIANTE LA CLASE
    ====================================================== */

    $noticia->modificar($pdo);

    // La clase devuelve false si no hubo cambios en los datos.
    // Eso no necesariamente representa un error.

    $actualizacionConfirmada = true;


    /* ======================================================
       17. ELIMINAR IMAGEN ANTERIOR
    ====================================================== */

    if (
        $rutaImagenNueva !== null &&
        $imagenAnterior !== '' &&
        $imagenAnterior !== $nombreImagen
    ) {

        $rutaAnterior = __DIR__
            . '/../Noticias/imagenes/'
            . $imagenAnterior;

        if (is_file($rutaAnterior)) {

            if (!unlink($rutaAnterior)) {
                error_log(
                    '[INSOFT] No se pudo eliminar la imagen anterior de la noticia.'
                );
            }

        }

    }


    /* ======================================================
       18. CONFIRMAR ACTUALIZACIÓN
    ====================================================== */

    $_SESSION['mensaje_noticias'] =
        'La noticia fue actualizada correctamente.';

    $_SESSION['csrf_noticias'] = bin2hex(random_bytes(32));


} catch (Throwable $e) {

    /* ======================================================
       ELIMINAR IMAGEN NUEVA SI FALLÓ LA ACTUALIZACIÓN
    ====================================================== */

    if (
        !$actualizacionConfirmada &&
        $rutaImagenNueva !== null &&
        is_file($rutaImagenNueva)
    ) {
        if (!unlink($rutaImagenNueva)) {
            error_log(
                '[INSOFT] No se pudo limpiar la imagen nueva tras el error.'
            );
        }
    }


    // REGISTRAR ERROR

    error_log(
        '[INSOFT actualizar noticia] ' . $e->getMessage()
    );


    // MENSAJE DE ERROR

    $_SESSION['error_noticias'] =
        $e instanceof InvalidArgumentException
            ? $e->getMessage()
            : 'No se pudo actualizar la noticia.';

}


/* ==========================================================
   19. REDIRECCIONAR
========================================================== */

header('Location: index.php?page=noticias');
exit;
<?php

/* ==========================================================
   1. CONFIGURAR MENSAJES DE ERROR
========================================================== */

// INICIAR SESIÓN SI NO ESTÁ INICIADA

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// FUNCIÓN PARA REDIRIGIR CON ERROR

function errorDescargaCV($mensaje)
{
    $_SESSION['error_descarga_cv'] = $mensaje;

    header('Location: index.php?page=historial_docente');

    exit;
}


/* ==========================================================
   2. OBTENER USUARIO
========================================================== */

$usuario = trim(
    (string) ($_GET['usuario'] ?? '')
);


// VALIDAR USUARIO

if (
    $usuario === '' ||
    !preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $usuario)
) {

    errorDescargaCV(
        'No se pudo identificar al docente.'
    );

}


/* ==========================================================
   3. BUSCAR CURRÍCULUM
========================================================== */

// CARPETA DE CURRÍCULUMS

$directorio = __DIR__ . '/../curriculums';


// COMPROBAR CARPETA

if (!is_dir($directorio)) {

    errorDescargaCV(
        'No se encontró la carpeta de currículums.'
    );

}


// BUSCAR ARCHIVO

$archivos = glob(
    $directorio . '/' . $usuario . '_*.pdf'
) ?: [];


// OBTENER ARCHIVO

$archivo = $archivos[0] ?? null;


/* ==========================================================
   4. COMPROBAR ARCHIVO
========================================================== */

if (
    !$archivo ||
    !is_file($archivo)
) {

    errorDescargaCV(
        'No se encontró el currículum del docente.'
    );

}


/* ==========================================================
   5. DESCARGAR CURRÍCULUM
========================================================== */

if (headers_sent()) {

    error_log(
        '[INSOFT] No se pudo descargar el CV: cabeceras enviadas.'
    );

    exit;
}


// CONFIGURAR DESCARGA

header('Content-Type: application/pdf');

header(
    'Content-Disposition: attachment; filename="' .
    basename($archivo) .
    '"'
);

header(
    'Content-Length: ' . filesize($archivo)
);


// ENVIAR ARCHIVO

readfile($archivo);

exit;
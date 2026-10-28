<?php

require_once __DIR__ . '/portal_common.php';

// FUNCIÓN PARA REDIRIGIR CON UN ERROR
function errorDescarga($mensaje, $idCurso = null)
{
    $_SESSION['error_descarga'] = $mensaje;

    $destino = 'aulaVirtual.php';

    if ($idCurso !== null && $idCurso > 0) {
        $destino = 'cursoVirtual.php?id=' . (int) $idCurso;
    }

    header('Location: ' . $destino);
    exit;
}

// COMPROBAR SESIÓN
if (!$haySesionEstudiante) {
    header('Location: ../Bienvenido/bienvenidoindex.php');
    exit;
}

// OBTENER ID DEL MATERIAL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// OBTENER ID DEL CURSO PARA LA REDIRECCIÓN
$idCurso = filter_input(INPUT_GET, 'curso', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    errorDescarga('El material solicitado no es válido.', $idCurso);
}

// COMPROBAR CONEXIÓN
if ($errorConexion) {
    errorDescarga('No se pudo conectar con la base de datos.', $idCurso);
}

try {

    // CONSULTAR MATERIAL Y VERIFICAR MATRÍCULA
    $stmt = $pdo->prepare("
        SELECT
            mat.nombre,
            mat.tipo,
            mat.url_archivo,
            cl.id_curso

        FROM materiales mat

        INNER JOIN clase cl
            ON cl.id_clase = mat.id_clase

        INNER JOIN matricula m
            ON m.id_curso = cl.id_curso

        WHERE mat.id_material = :material
          AND m.nombre_usuario_estudiante = :usuario
          AND LOWER(TRIM(m.estado)) = 'activa'

        LIMIT 1
    ");

    $stmt->execute([
        ':material' => $id,
        ':usuario' => $nombreUsuario
    ]);

    $material = $stmt->fetch(PDO::FETCH_ASSOC);

    // COMPROBAR PERMISOS
    if (!$material) {
        errorDescarga(
            'No tenés permiso para descargar este material.',
            $idCurso
        );
    }

    // OBTENER CURSO REAL DEL MATERIAL
    $idCurso = (int) $material['id_curso'];

    // DEFINIR CARPETA DE MATERIALES
    $baseMateriales = realpath(
        dirname(__DIR__) . '/materiales'
    );

    if ($baseMateriales === false) {
        errorDescarga(
            'La carpeta de materiales no existe.',
            $idCurso
        );
    }

    // NORMALIZAR RUTA
    $ruta = str_replace(
        '\\',
        '/',
        trim((string) $material['url_archivo'])
    );

    $ruta = preg_replace(
        '~^materiales/~i',
        '',
        $ruta
    );

    // VALIDAR RUTA
    if (
        $ruta === '' ||
        str_contains($ruta, "\0") ||
        str_starts_with($ruta, '/') ||
        preg_match('~^[a-zA-Z]:~', $ruta)
    ) {
        errorDescarga(
            'La ruta del material no es válida.',
            $idCurso
        );
    }

    // OBTENER ARCHIVO
    $archivo = realpath(
        $baseMateriales . DIRECTORY_SEPARATOR . $ruta
    );

    // COMPROBAR EXISTENCIA Y PERMISOS
    if (
        $archivo === false ||
        !str_starts_with(
            $archivo,
            $baseMateriales . DIRECTORY_SEPARATOR
        ) ||
        !is_file($archivo) ||
        !is_readable($archivo)
    ) {
        errorDescarga(
            'El archivo todavía no está disponible en el servidor.',
            $idCurso
        );
    }

    // PREPARAR DESCARGA
    $nombreDescarga = basename($archivo);

    $tipo = mime_content_type($archivo)
        ?: 'application/octet-stream';

    // LIMPIAR BÚFER
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    // ENCABEZADOS DE DESCARGA
    header('Content-Type: ' . $tipo);

    header(
        'Content-Disposition: attachment; filename="' .
        str_replace(['"', "\r", "\n"], '', $nombreDescarga) .
        '"'
    );

    header('Content-Length: ' . filesize($archivo));
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store');

    // ENVIAR ARCHIVO
    readfile($archivo);
    exit;

} catch (Throwable $e) {

    error_log(
        '[INSOFT] Descarga: ' . $e->getMessage()
    );

    errorDescarga(
        'No se pudo procesar la descarga. Intentá nuevamente.',
        $idCurso
    );
}
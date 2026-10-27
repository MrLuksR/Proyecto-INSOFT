<?php

/**
 * INSOFT - REGISTRO DE ENTREGAS
 *
 * Permite entregar tareas mediante:
 * - Respuesta escrita.
 * - Archivo PDF.
 * - Ambas opciones.
 *
 * Los archivos se guardan en:
 * INSOFT/entregas/tarea_ID/
 *
 * Tamaño máximo: 25 MB.
 */


/* ==========================================================
   1. DEPENDENCIAS Y AUTENTICACIÓN
========================================================== */

require_once __DIR__ . '/portal_common.php';


// COMPROBAR SESIÓN DEL ESTUDIANTE
if (!$haySesionEstudiante) {

    header('Location: ../Bienvenido/bienvenidoindex.php');
    exit;

}


/* ==========================================================
   2. OBTENER IDENTIFICADORES
========================================================== */

// OBTENER ID DEL CURSO DESDE POST O GET
$idCurso = filter_input(
    INPUT_POST,
    'id_curso',
    FILTER_VALIDATE_INT
);

if (!$idCurso) {
    $idCurso = filter_input(
        INPUT_GET,
        'id_curso',
        FILTER_VALIDATE_INT
    );
}

// OBTENER ID DE LA TAREA
$idTarea = filter_input(
    INPUT_POST,
    'id_tarea',
    FILTER_VALIDATE_INT
);

// DEFINIR PÁGINA DE RETORNO
$destino = 'cursoVirtual.php';

if ($idCurso && $idCurso > 0) {
    $destino .= '?id=' . $idCurso;
}

/* ==========================================================
   3. FUNCIÓN PARA MOSTRAR NOTIFICACIONES
========================================================== */

function avisoEntrega(
    string $mensaje,
    string $tipo,
    string $destino
): never {

    $_SESSION['aviso_tarea'] = [
        'mensaje' => $mensaje,
        'tipo' => $tipo
    ];

    header('Location: ' . $destino, true, 303);
    exit;

}


/* ==========================================================
   4. DETECTAR FORMULARIOS QUE SUPERAN POST_MAX_SIZE
========================================================== */

// CONVERTIR VALORES COMO 30M O 1G A BYTES.
function limitePhpEnBytes(string $valor): int
{
    $valor = trim($valor);
    $numero = (float) $valor;
    $unidad = strtolower(substr($valor, -1));

    return (int) ($numero * match ($unidad) {
        'g' => 1024 ** 3,
        'm' => 1024 ** 2,
        'k' => 1024,
        default => 1,
    });
}

$limitePost = limitePhpEnBytes((string) ini_get('post_max_size'));
$tamanoSolicitud = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);

if ($limitePost > 0 && $tamanoSolicitud > $limitePost) {
    avisoEntrega(
        'El archivo es demasiado grande. El tamaño máximo permitido es de 25 MB.',
        'error',
        $destino
    );
}

/* ==========================================================
   5. VALIDAR SOLICITUD
========================================================== */

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !$idCurso ||
    !$idTarea ||
    $idCurso < 1 ||
    $idTarea < 1
) {

    avisoEntrega(
        'Solicitud inválida.',
        'error',
        $destino
    );

}


/* ==========================================================
   5. VALIDAR TOKEN CSRF
========================================================== */

if (
    empty($_SESSION['csrf_tarea']) ||
    !hash_equals(
        (string) $_SESSION['csrf_tarea'],
        (string) ($_POST['csrf_tarea'] ?? '')
    )
) {

    avisoEntrega(
        'La sesión del formulario venció. Recargá la página.',
        'error',
        $destino
    );

}


/* ==========================================================
   6. COMPROBAR CONEXIÓN
========================================================== */

if (
    $errorConexion ||
    !($pdo instanceof PDO)
) {

    avisoEntrega(
        'No se pudo conectar a la base de datos.',
        'error',
        $destino
    );

}


/* ==========================================================
   7. OBTENER RESPUESTA Y ARCHIVO
========================================================== */

$texto = trim(
    (string) ($_POST['texto_entrega'] ?? '')
);

$adjunto = $_FILES['archivo_entrega'] ?? null;


// COMPROBAR SI SE ADJUNTÓ UN ARCHIVO
$hayArchivo = (
    is_array($adjunto) &&
    ($adjunto['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
);


// VALIDAR LONGITUD DEL TEXTO
if (mb_strlen($texto) > 10000) {

    avisoEntrega(
        'La respuesta es demasiado larga.',
        'error',
        $destino
    );

}


// COMPROBAR QUE EXISTA UNA ENTREGA
if ($texto === '' && !$hayArchivo) {

    avisoEntrega(
        'Escribí una respuesta o adjuntá un PDF.',
        'error',
        $destino
    );

}


/* ==========================================================
   8. CONFIGURACIÓN DEL ALMACENAMIENTO
========================================================== */

// TAMAÑO MÁXIMO: 25 MB
$tamanoMaximo = 25 * 1024 * 1024;


// CARPETA PRINCIPAL DEL PROYECTO
$raizProyecto = dirname(__DIR__);


// CARPETA DONDE SE GUARDAN LAS ENTREGAS
$carpetaEntregas = $raizProyecto . '/entregas';


// CARPETA ESPECÍFICA DE LA TAREA
$carpetaTarea = $carpetaEntregas . '/tarea_' . $idTarea;


// RUTA DEL ARCHIVO GUARDADO
$rutaFinal = null;


/* ==========================================================
   9. PROCESAR ENTREGA
========================================================== */

try {

    // INICIAR TRANSACCIÓN
    $pdo->beginTransaction();


    /* ======================================================
       10. VERIFICAR TAREA Y MATRÍCULA
    ====================================================== */

    $stmt = $pdo->prepare("

        SELECT
            t.id_tarea,
            t.fecha_limite,
            NOW() AS ahora

        FROM tareas t

        INNER JOIN clase c
            ON c.id_clase = t.id_clase

        INNER JOIN matricula m
            ON m.id_curso = c.id_curso

        WHERE t.id_tarea = :tarea
          AND c.id_curso = :curso
          AND m.nombre_usuario_estudiante = :usuario
          AND LOWER(TRIM(m.estado)) = 'activa'

        LIMIT 1

        FOR UPDATE

    ");

    $stmt->execute([
        ':tarea' => $idTarea,
        ':curso' => $idCurso,
        ':usuario' => $nombreUsuario
    ]);

    $tarea = $stmt->fetch(PDO::FETCH_ASSOC);


    // COMPROBAR ACCESO
    if (!$tarea) {

        throw new DomainException(
            'No tenés acceso a esta tarea.'
        );

    }


    /* ======================================================
       11. COMPROBAR FECHA LÍMITE
    ====================================================== */

    if (
        $tarea['fecha_limite'] !== null &&
        strtotime($tarea['ahora']) >
        strtotime($tarea['fecha_limite'])
    ) {

        throw new DomainException(
            'El plazo para entregar esta tarea ya finalizó.'
        );

    }


    /* ======================================================
       12. COMPROBAR SI YA FUE ENTREGADA
    ====================================================== */

    $stmt = $pdo->prepare("

        SELECT id_tarea

        FROM tareas_estudiantes

        WHERE id_tarea = :tarea
          AND nombre_usuario_estudiante = :usuario

        LIMIT 1

    ");

    $stmt->execute([
        ':tarea' => $idTarea,
        ':usuario' => $nombreUsuario
    ]);


    if ($stmt->fetchColumn()) {

        throw new DomainException(
            'Esta tarea ya fue entregada.'
        );

    }


    /* ======================================================
       13. PROCESAR ARCHIVO PDF
    ====================================================== */

    $rutaRelativa = null;


    if ($hayArchivo) {


        // COMPROBAR ERRORES DE SUBIDA
        if (
            ($adjunto['error'] ?? null) !== UPLOAD_ERR_OK ||
            !isset($adjunto['tmp_name']) ||
            !is_uploaded_file($adjunto['tmp_name'])
        ) {

            if (
                ($adjunto['error'] ?? null) === UPLOAD_ERR_INI_SIZE ||
                ($adjunto['error'] ?? null) === UPLOAD_ERR_FORM_SIZE
            ) {

                throw new DomainException(
                    'El archivo es demasiado grande. El tamaño máximo permitido es de 25 MB.'
                );

            }

            throw new DomainException(
                'No se pudo recibir el archivo.'
            );

        }


        /* VALIDAR TAMAÑO */

        if (
            ($adjunto['size'] ?? 0) < 1 ||
            $adjunto['size'] > $tamanoMaximo
        ) {

            throw new DomainException(
                'El PDF debe pesar como máximo 25 MB.'
            );

        }


        /* VALIDAR FORMATO */

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $tipoArchivo = $finfo->file(
            $adjunto['tmp_name']
        );

        $extension = strtolower(
            pathinfo(
                $adjunto['name'] ?? '',
                PATHINFO_EXTENSION
            )
        );


        if (
            $tipoArchivo !== 'application/pdf' ||
            $extension !== 'pdf'
        ) {

            throw new DomainException(
                'Solo se permiten archivos PDF.'
            );

        }


        /* ==================================================
           14. CREAR CARPETAS LOCALES
        ================================================== */

        // CREAR CARPETA PRINCIPAL
        if (
            !is_dir($carpetaEntregas) &&
            !mkdir($carpetaEntregas, 0700, true) &&
            !is_dir($carpetaEntregas)
        ) {

            throw new RuntimeException(
                'No se pudo crear la carpeta de entregas.'
            );

        }


        // CREAR CARPETA DE LA TAREA
        if (
            !is_dir($carpetaTarea) &&
            !mkdir($carpetaTarea, 0700, true) &&
            !is_dir($carpetaTarea)
        ) {

            throw new RuntimeException(
                'No se pudo crear la carpeta de la tarea.'
            );

        }


        /* ==================================================
           15. GENERAR NOMBRE DEL ARCHIVO
        ================================================== */

        // CONSERVAR EL NOMBRE ORIGINAL, SIN RUTAS NI CARACTERES PELIGROSOS
        $nombreOriginal = pathinfo(
            basename(str_replace('\\', '/', (string) $adjunto['name'])),
            PATHINFO_FILENAME
        );

        // CONVERTIR A UN NOMBRE SEGURO PARA EL SISTEMA DE ARCHIVOS
        $nombreBase = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nombreOriginal);
        if ($nombreBase === false) {
            $nombreBase = $nombreOriginal;
        }
        $nombreBase = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $nombreBase);
        $nombreBase = trim((string) $nombreBase, '-_');
        $nombreBase = substr($nombreBase, 0, 80);
        if ($nombreBase === '') {
            $nombreBase = 'entrega';
        }

        // FECHA, HORA, SEGUNDOS Y SUFIJO ALEATORIO ANTICOLISIONES
        $fechaHora = (new DateTimeImmutable())->format('Y-m-d_H-i-s');
        $identificador = bin2hex(random_bytes(4));

        $nombreArchivo = $nombreBase . '_' . $fechaHora . '_' . $identificador . '.pdf';


        // RUTA COMPLETA
        $rutaFinal = $carpetaTarea
            . DIRECTORY_SEPARATOR
            . $nombreArchivo;


        /* ==================================================
           16. GUARDAR ARCHIVO
        ================================================== */

        if (
            !move_uploaded_file(
                $adjunto['tmp_name'],
                $rutaFinal
            )
        ) {

            throw new RuntimeException(
                'No se pudo guardar el PDF.'
            );

        }


        // GUARDAR RUTA RELATIVA EN LA BASE DE DATOS
        $rutaRelativa = 'tarea_'
            . $idTarea
            . '/'
            . $nombreArchivo;

    }


    /* ======================================================
       17. REGISTRAR ENTREGA EN LA BASE DE DATOS
    ====================================================== */

    $stmt = $pdo->prepare("

        INSERT INTO tareas_estudiantes (

            id_tarea,
            nombre_usuario_estudiante,
            calificacion,
            fecha_entrega,
            texto_entrega,
            archivo_entrega

        ) VALUES (

            :tarea,
            :usuario,
            NULL,
            NOW(),
            :texto,
            :archivo

        )

    ");


    $stmt->execute([

        ':tarea' => $idTarea,

        ':usuario' => $nombreUsuario,

        ':texto' => $texto !== ''
            ? $texto
            : null,

        ':archivo' => $rutaRelativa

    ]);


    /* ======================================================
       18. CONFIRMAR TRANSACCIÓN
    ====================================================== */

    $pdo->commit();


    avisoEntrega(
        'La tarea se entregó correctamente.',
        'exito',
        $destino
    );


/* ==========================================================
   19. CONTROLAR ERRORES DE VALIDACIÓN
========================================================== */

} catch (DomainException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    // ELIMINAR ARCHIVO SI FALLÓ EL REGISTRO
    if ($rutaFinal && is_file($rutaFinal)) {
        unlink($rutaFinal);
    }


    avisoEntrega(
        $e->getMessage(),
        'error',
        $destino
    );


/* ==========================================================
   20. CONTROLAR ERRORES DEL SISTEMA
========================================================== */

} catch (Throwable $e) {

    // REVERTIR TRANSACCIÓN
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // ELIMINAR ARCHIVO SI FALLÓ EL REGISTRO
    if ($rutaFinal && is_file($rutaFinal)) {
        unlink($rutaFinal);
    }

    // REGISTRAR ERROR COMPLETO
    error_log(
        '[INSOFT] Error al enviar tarea: ' .
        $e->getMessage() .
        ' | Archivo: ' .
        $e->getFile() .
        ' | Línea: ' .
        $e->getLine()
    );

    // MOSTRAR ERROR GENÉRICO AL ESTUDIANTE
    avisoEntrega(
        'No se pudo guardar la entrega. Contactá con el administrador.',
        'error',
        $destino
    );

}
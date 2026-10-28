<?php

/**
 * INSOFT - GESTIÓN DEL ESTADO DE ESTUDIANTES
 * Permite dar de alta y de baja estudiantes.
 */

// CARGAR CLASES ANTES DE INICIAR SESIÓN
require_once __DIR__ . '/../database/Clases/Administrativo.php';
require_once __DIR__ . '/../database/Clases/Estudiante.php';
require_once __DIR__ . '/../database/consultas/encriptacion.php';
require_once __DIR__ . '/../Mailer/enviarCorreo.php';

// INICIAR SESIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// VERIFICAR AUTORIZACIÓN
if (
    !isset($_SESSION['usuario']) ||
    !($_SESSION['usuario'] instanceof Administrativo) ||
    ($_SESSION['tipo_usuario'] ?? '') !== 'administrativo'
) {
    http_response_code(403);
    exit('Acceso no autorizado.');
}

// COMPROBAR MÉTODO
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=estudiantes');
    exit;
}

// VALIDAR CSRF
$tokenSesion = $_SESSION['csrf_estudiantes'] ?? '';
$tokenRecibido = $_POST['csrf_token'] ?? '';

if (
    !is_string($tokenSesion) ||
    !is_string($tokenRecibido) ||
    $tokenSesion === '' ||
    !hash_equals($tokenSesion, $tokenRecibido)
) {
    http_response_code(403);
    exit('Solicitud no válida.');
}

// OBTENER ESTUDIANTE
$nombreUsuario = trim(
    (string) ($_POST['nombre_usuario'] ?? '')
);

// OBTENER ACCIÓN
$accion = $_POST['accion'] ?? '';

// OBTENER MOTIVO DE BAJA
$motivoBaja = trim(
    (string) ($_POST['motivo_baja'] ?? '')
);

// VALIDAR MOTIVO CUANDO SEA UNA BAJA
if ($accion === 'baja' && $motivoBaja === '') {

    $_SESSION['mensaje_estudiantes'] = [
        'tipo' => 'error',
        'texto' => 'Debés indicar el motivo de la baja.'
    ];

    header('Location: index.php?page=estudiantes');
    exit;
}

// VALIDAR ESTUDIANTE
if ($nombreUsuario === '') {

    $_SESSION['mensaje_estudiantes'] = [
        'tipo' => 'error',
        'texto' => 'No se seleccionó ningún estudiante.'
    ];

    header('Location: index.php?page=estudiantes');
    exit;
}

// VALIDAR ACCIÓN
if (!in_array($accion, ['alta', 'baja'], true)) {

    $_SESSION['mensaje_estudiantes'] = [
        'tipo' => 'error',
        'texto' => 'La acción seleccionada no es válida.'
    ];

    header('Location: index.php?page=estudiantes');
    exit;
}

// CONEXIÓN
require_once __DIR__ . '/../database/consultas/conexion.php';

try {

    // VERIFICAR CONEXIÓN
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new RuntimeException('No hay conexión con la base de datos.');
    }

    // EJECUTAR ACCIÓN
    if ($accion === 'alta') {

        // REACTIVAR ESTUDIANTE
        $resultado = Estudiante::darDeAlta(
            $pdo,
            $nombreUsuario
        );

        $mensajeExito = 'El estudiante fue reactivado correctamente.';
        $mensajeError = 'No se modificó el estudiante. Puede que ya esté activo.';

    } else {

        // DAR DE BAJA AL ESTUDIANTE
        $resultado = Estudiante::darDeBaja(
            $pdo,
            $nombreUsuario
        );

        $mensajeExito = 'El estudiante fue dado de baja correctamente.';
        $mensajeError = 'No se modificó el estudiante. Puede que ya esté inactivo.';
    }

    // GUARDAR NOTIFICACIÓN
    $_SESSION['mensaje_estudiantes'] = [
        'tipo' => $resultado ? 'exito' : 'error',
        'texto' => $resultado
            ? $mensajeExito
            : $mensajeError
    ];

} catch (Throwable $e) {

    // REGISTRAR ERROR
    error_log(
        '[INSOFT][Estado estudiante] ' . $e->getMessage()
    );

    $_SESSION['mensaje_estudiantes'] = [
        'tipo' => 'error',
        'texto' => 'Ocurrió un error al modificar el estado del estudiante.'
    ];
}

// Enviar un correo con el motivo de la baja
if ($resultado && $accion === 'baja'){

    $resultado = false;

    try{
        // Enviar correo de baja con el motivo
        $correoEnviado = Estudiante::enviarMotivoDeBaja($pdo, $nombreUsuario, $motivoBaja, $key);

        // COMPROBAR RESULTADO DEL ENVÍO
        if ($correoEnviado === false) {

            $_SESSION['mensaje_estudiantes'] = [
                'tipo' => 'error',
                'texto' => 'El estudiante fue dado de baja, pero no se pudo enviar el correo de notificación.'
            ];

        }
    } catch (Throwable $e) {

        // REGISTRAR ERROR
        error_log(
            '[INSOFT][Correo baja estudiante] ' . $e->getMessage()
        );

        // NOTIFICAR SIN CONFUNDIR EL ERROR CON LA BAJA
        $_SESSION['mensaje_estudiantes'] = [
            'tipo' => 'error',
            'texto' => 'El estudiante fue dado de baja correctamente, pero ocurrió un error al enviar el correo.'
        ];
    
    }
}

// REDIRIGIR AL LISTADO
header('Location: index.php?page=estudiantes');
exit;
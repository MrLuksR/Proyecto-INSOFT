<?php

/* ==========================================================
   1. CARGAR CLASES
========================================================== */

require_once __DIR__ . '/../database/Clases/Administrativo.php';
require_once __DIR__ . '/../database/Clases/Docente.php';


// INICIAR SESIÓN

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// COMPROBAR SESIÓN ADMINISTRATIVA

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
   3. PROCESAR SOLICITUD
========================================================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=docentes');
    exit;
}


// OBTENER DATOS

$nombreUsuario = trim(
    (string) ($_POST['nombre_usuario'] ?? '')
);

$accion = (string) ($_POST['accion'] ?? '');


// CAMBIAR ESTADO

try {

    if ($accion === 'alta') {

        $resultado = Docente::darDeAlta(
            $pdo,
            $nombreUsuario
        );

        $mensaje = 'El docente fue dado de alta correctamente.';

    } elseif ($accion === 'baja') {

        $resultado = Docente::darDeBaja(
            $pdo,
            $nombreUsuario
        );

        $mensaje = 'El docente fue dado de baja correctamente.';

    } else {

        throw new InvalidArgumentException('Acción no válida.');

    }

    if (!$resultado) {
        throw new RuntimeException(
            'No se pudo actualizar el estado del docente.'
        );
    }

    $_SESSION['mensaje_docentes'] = $mensaje;

} catch (Throwable $e) {

    error_log(
        '[INSOFT estado docente] ' . $e->getMessage()
    );

    $_SESSION['error_docentes'] =
        'No se pudo actualizar el estado del docente.';

}


/* ==========================================================
   4. REDIRECCIONAR
========================================================== */

header('Location: index.php?page=docentes');
exit;
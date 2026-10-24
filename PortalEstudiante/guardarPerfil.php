<?php

// CONEXIÓN Y FUNCIONES
require_once __DIR__ . '/portal_common.php';

// FUNCIONES DE CIFRADO
require_once __DIR__ . '/../database/consultas/encriptacion.php';

// CONFIGURACIÓN DE CLAVE MAESTRA
require_once __DIR__ . '/../auth/config.php';


// VERIFICAR MÉTODO
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: editarPerfil.php');
    exit;
}


// VERIFICAR SESIÓN
if (
    empty($haySesionEstudiante) ||
    !isset($usuario) ||
    !($usuario instanceof Estudiante)
) {
    header('Location: ../Bienvenido/bienvenidoindex.php');
    exit;
}


// VERIFICAR CONEXIÓN
if (!isset($pdo) || !($pdo instanceof PDO)) {

    $_SESSION['error_perfil'] =
        'No se pudo conectar con la base de datos.';

    header('Location: editarPerfil.php');
    exit;
}


// ACTUALIZAR PERFIL
try {

    // OBTENER DATOS ACTUALES
    $nombreActual = $usuario->getNombre();
    $apellidoActual = $usuario->getApellido();
    $correoActual = $usuario->getCorreo($key);
    $telefonoActual = $usuario->getTelefono($key);
    $fechaActual = $usuario->getFechaNacimiento($key);


    // OBTENER DATOS DEL FORMULARIO
    $nombre = trim($_POST['nombre'] ?? $nombreActual);
    $apellido = trim($_POST['apellido'] ?? $apellidoActual);
    $correo = trim($_POST['correo'] ?? $correoActual);
    $telefono = trim($_POST['telefono'] ?? $telefonoActual);
    $fecha = trim($_POST['fecha_nacimiento'] ?? $fechaActual);


    // COMPROBAR MODIFICACIONES
    $nombreModificado = ($nombre !== $nombreActual);
    $apellidoModificado = ($apellido !== $apellidoActual);
    $correoModificado = ($correo !== $correoActual);
    $telefonoModificado = ($telefono !== $telefonoActual);
    $fechaModificada = ($fecha !== $fechaActual);

    $fotoModificada = (
        isset($_FILES['foto']) &&
        $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE
    );


    // COMPROBAR SI EXISTEN CAMBIOS
    if (
        !$nombreModificado &&
        !$apellidoModificado &&
        !$correoModificado &&
        !$telefonoModificado &&
        !$fechaModificada &&
        !$fotoModificada
    ) {

        $_SESSION['info_perfil'] =
            'No se detectaron cambios.';

        header('Location: editarPerfil.php');
        exit;
    }


    // INICIAR TRANSACCIÓN
    $pdo->beginTransaction();


    // ACTUALIZAR NOMBRE
    if ($nombreModificado) {
        $usuario->updateNombre($pdo, $nombre);
    }


    // ACTUALIZAR APELLIDO
    if ($apellidoModificado) {
        $usuario->updateApellido($pdo, $apellido);
    }


    // ACTUALIZAR CORREO
    if ($correoModificado) {
        $usuario->updateCorreo($pdo, $correo, $key);
    }


    // ACTUALIZAR TELÉFONO
    if ($telefonoModificado) {
        $usuario->updateTelefono($pdo, $telefono, $key);
    }


    // ACTUALIZAR FECHA DE NACIMIENTO
    if ($fechaModificada) {
        $usuario->updateFechaNacimiento($pdo, $fecha, $key);
    }


    // ACTUALIZAR FOTOGRAFÍA
    if ($fotoModificada) {
        $usuario->updateFoto($pdo);
    }


    // CONFIRMAR TRANSACCIÓN
    $pdo->commit();


    // ACTUALIZAR OBJETO DE SESIÓN
    $_SESSION['usuario'] = $usuario;


    // MENSAJE DE ÉXITO
    $_SESSION['exito_perfil'] =
        'Tu perfil se actualizó correctamente.';


} catch (Throwable $e) {

    // REVERTIR TRANSACCIÓN
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // REGISTRAR ERROR
    error_log(
        'Error al actualizar perfil: ' . $e->getMessage()
    );

    $_SESSION['error_perfil'] =
        'Ocurrió un error al actualizar el perfil.';
}


// REDIRIGIR
header('Location: editarPerfil.php');
exit;
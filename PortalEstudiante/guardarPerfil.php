<?php

// CONEXIÓN Y FUNCIONES
require_once __DIR__ . '/portal_common.php';

// FUNCIONES DE CIFRADO
require_once __DIR__ . '/../database/consultas/encriptacion.php';

// CONFIGURACIÓN DE CLAVE MAESTRA
require_once __DIR__ . '/../auth/config.php';

// FUNCIÓN PARA ENVIAR CORREOS
require_once '../Mailer/enviarCorreo.php';

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


    // OBTENER CONTRASEÑAS
    $passwordActual = $_POST['password_actual'] ?? '';
    $passwordNueva = $_POST['password_nueva'] ?? '';


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

    // COMPROBAR CAMBIO DE CONTRASEÑA
    $passwordModificada = (
        $passwordActual !== '' ||
        $passwordNueva !== ''
    );


    // VALIDAR CONTRASEÑAS
    if ($passwordModificada) {

        // COMPROBAR QUE SE INGRESARON AMBAS
        if (
            $passwordActual === '' ||
            $passwordNueva === ''
        ) {
            throw new Exception(
                'Debés ingresar la contraseña actual y la nueva.'
            );
        }

        // VALIDAR LONGITUD
        if (strlen($passwordNueva) < 8) {
            throw new Exception(
                'La nueva contraseña debe tener al menos 8 caracteres.'
            );
        }

        // COMPROBAR CONTRASEÑA ACTUAL
        if (!password_verify(
            $passwordActual,
            $usuario->getPassword()
        )) {
            throw new Exception(
                'La contraseña actual es incorrecta.'
            );
        }

        // COMPROBAR QUE SEA DIFERENTE
        if (password_verify(
            $passwordNueva,
            $usuario->getPassword()
        )) {
            throw new Exception(
                'La nueva contraseña debe ser diferente a la actual.'
            );
        }
    }


    // COMPROBAR SI EXISTEN CAMBIOS
    if (
        !$nombreModificado &&
        !$apellidoModificado &&
        !$correoModificado &&
        !$telefonoModificado &&
        !$fechaModificada &&
        !$fotoModificada &&
        !$passwordModificada
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


    // ACTUALIZAR CONTRASEÑA
    if ($passwordModificada) {

        $usuario->updatePassword(
            $pdo,
            $passwordActual,
            $passwordNueva
        );
    }


    // CONFIRMAR TRANSACCIÓN
$pdo->commit();


// ACTUALIZAR OBJETO DE SESIÓN
$_SESSION['usuario'] = $usuario;


// MENSAJE DE ÉXITO
$_SESSION['exito_perfil'] =
    $passwordModificada
        ? 'Tu perfil y contraseña se actualizaron correctamente.'
        : 'Tu perfil se actualizó correctamente.';


// NOTIFICAR CAMBIO DE CONTRASEÑA
if ($passwordModificada) {

    try {

        // OBTENER DATOS DEL ESTUDIANTE
        $destinatario = $usuario->getCorreo($key);
        $nombre = htmlspecialchars(
            $usuario->getNombre(),
            ENT_QUOTES,
            'UTF-8'
        );

        // ASUNTO DEL CORREO
        $asunto = 'Cambio de contraseña';

        // TÍTULO
        $titulo = 'Tu contraseña ha sido modificada';

        // MENSAJE
        $mensaje = '
            Hola, <strong>' . $nombre . '</strong>.<br><br>

            Te informamos que la contraseña de tu cuenta
            del Instituto Nacional de Informática
            ha sido modificada correctamente.<br><br>

            Si realizaste este cambio, no necesitás
            realizar ninguna acción adicional.
        ';

        // AVISO DE SEGURIDAD
        $aviso = '
            <strong>¿No reconocés este cambio?</strong><br>
            Si no modificaste tu contraseña, comunicate
            inmediatamente con la administración del instituto
            para proteger tu cuenta.
        ';

        // GENERAR CORREO CON PLANTILLA INSTITUCIONAL
        $contenido = plantillaCorreo(
            $titulo,
            $mensaje,
            $aviso
        );

        // ENVIAR CORREO
        $enviado = enviarCorreo(
            $destinatario,
            $asunto,
            $contenido
        );

        if (!$enviado) {
            error_log(
                'No se pudo enviar la notificación de cambio de contraseña.'
            );
        }

    } catch (Throwable $e) {

        // REGISTRAR ERROR SIN CANCELAR LA ACTUALIZACIÓN
        error_log(
            'Error al notificar cambio de contraseña: '
            . $e->getMessage()
        );

    }

}


} catch (Throwable $e) {

    // REVERTIR TRANSACCIÓN
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // REGISTRAR ERROR
    error_log(
        'Error al actualizar perfil: ' . $e->getMessage()
    );

    // MENSAJES ESPECÍFICOS
    $erroresPermitidos = [
        'Debés ingresar la contraseña actual y la nueva.',
        'La nueva contraseña debe tener al menos 8 caracteres.',
        'La contraseña actual es incorrecta.',
        'La nueva contraseña debe ser diferente a la actual.'
    ];

    if (in_array($e->getMessage(), $erroresPermitidos, true)) {

        $_SESSION['error_perfil'] = $e->getMessage();

    } else {

        $_SESSION['error_perfil'] =
            'Ocurrió un error al actualizar el perfil.';
    }

}


// REDIRIGIR
header('Location: editarPerfil.php');
exit;
<?php

/**
 * INSOFT - REENVIAR CÓDIGO DE VERIFICACIÓN
 */

declare(strict_types=1);

// =====================================================
// DEPENDENCIAS
// =====================================================

session_start();

require_once __DIR__ . '/../Mailer/enviarCorreo.php';


// =====================================================
// VALIDAR SOLICITUD
// =====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: verificarCorreo.php');
    exit;
}

if (!isset($_SESSION['verificacion'])) {
    header('Location: loginn.php');
    exit;
}


// =====================================================
// LIMITAR REENVÍOS
// =====================================================

$ahora = time();

$ultimoEnvio = (int) (
    $_SESSION['verificacion']['ultimo_envio'] ?? 0
);

if ($ahora - $ultimoEnvio < 60) {

    $_SESSION['error_verificacion'] =
        'Esperá un minuto antes de solicitar otro código.';

    header('Location: verificarCorreo.php');
    exit;
}


// =====================================================
// COMPROBAR DESTINATARIO
// =====================================================

// ADAPTAR ESTA CLAVE A LA ESTRUCTURA REAL DE TU SESIÓN
$correo = $_SESSION['verificacion']['correo'] ?? null;

if (
    !is_string($correo) ||
    !filter_var($correo, FILTER_VALIDATE_EMAIL)
) {

    $_SESSION['error_verificacion'] =
        'No se pudo reenviar el código. Iniciá sesión nuevamente.';

    header('Location: verificarCorreo.php');
    exit;
}


// =====================================================
// GENERAR NUEVO CÓDIGO
// =====================================================

$codigo = (string) random_int(1000, 9999);

$codigoHash = password_hash(
    $codigo,
    PASSWORD_DEFAULT
);


// =====================================================
// ENVIAR CORREO
// =====================================================

try {

    $contenido = '
        Solicitaste un nuevo código de verificación
        para iniciar sesión en INADI.

        <br><br>

        <div style="
            font-size: 30px;
            font-weight: bold;
            letter-spacing: 6px;
            text-align: center;
        ">
            ' . htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8') . '
        </div>

        <br><br>

        Este código tiene una validez de 10 minutos.
    ';

    $enviado = enviarCorreo(
        $correo,
        'INADI - Nuevo código de verificación',
        plantillaCorreo(
            'Verificación de identidad',
            $contenido
        )
    );

    if (!$enviado) {
        throw new RuntimeException(
            'No se pudo enviar el correo.'
        );
    }

    // INVALIDAR EL CÓDIGO ANTERIOR
    $_SESSION['verificacion']['codigo_hash'] = $codigoHash;

    // REINICIAR VENCIMIENTO
    $_SESSION['verificacion']['expira'] = time() + 600;

    // REINICIAR INTENTOS
    $_SESSION['verificacion']['intentos'] = 0;

    // REGISTRAR ÚLTIMO ENVÍO
    $_SESSION['verificacion']['ultimo_envio'] = time();

    $_SESSION['exito_verificacion'] =
        'Te enviamos un nuevo código de verificación.';

} catch (Throwable $e) {

    error_log(
        '[INSOFT][Reenvío] ' . $e->getMessage()
    );

    $_SESSION['error_verificacion'] =
        'No se pudo enviar el código. Intentá nuevamente.';
}

header('Location: verificarCorreo.php');
exit;
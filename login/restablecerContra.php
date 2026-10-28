<?php

/**
 * INSOFT - RESTABLECER CONTRASEÑA
 */

declare(strict_types=1);


// =====================================================
// 1. DEPENDENCIAS
// =====================================================

require_once __DIR__ . '/../database/consultas/conexion.php';


// =====================================================
// 2. VARIABLES
// =====================================================

$mensaje = '';
$tipoMensaje = '';

$token = trim((string) (
    $_POST['token'] ?? $_GET['token'] ?? ''
));

$tokenValido = false;
$nombreUsuario = '';


// =====================================================
// 3. PROTEGER CONTENIDO HTML
// =====================================================

function escaparRestablecimiento(mixed $valor): string
{
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}


// =====================================================
// 4. VERIFICAR TOKEN DE RECUPERACIÓN
// =====================================================

function verificarTokenRecuperacion(string $token, PDO $pdo): ?string
{
    // OBTENER CLAVE SECRETA
    $secreto = getenv('INSOFT_RESET_SECRET');

    if (
        !is_string($secreto) ||
        strlen($secreto) < 32
    ) {
        throw new RuntimeException(
            'No se configuró la clave de recuperación.'
        );
    }

    // VALIDAR LONGITUD
    if ($token === '' || strlen($token) > 2048) {
        return null;
    }

    // SEPARAR DATOS Y FIRMA
    $partes = explode('.', $token);

    if (count($partes) !== 2) {
        return null;
    }

    [$datosCodificados, $firmaRecibida] = $partes;

    // VALIDAR FORMATO
    if (
        !preg_match('/^[A-Za-z0-9_-]+$/', $datosCodificados) ||
        !preg_match('/^[a-f0-9]{64}$/', $firmaRecibida)
    ) {
        return null;
    }

    // RECONSTRUIR BASE64
    $datosBase64 = strtr(
        $datosCodificados,
        '-_',
        '+/'
    );

    $datosBase64 .= str_repeat(
        '=',
        (4 - strlen($datosBase64) % 4) % 4
    );

    // DESCODIFICAR DATOS
    $json = base64_decode($datosBase64, true);

    if ($json === false) {
        return null;
    }

    $datos = json_decode($json, true);

    if (!is_array($datos)) {
        return null;
    }

    // OBTENER USUARIO Y VENCIMIENTO
    $nombreUsuario = $datos['usuario'] ?? null;
    $expira = $datos['expira'] ?? null;

    if (
        !is_string($nombreUsuario) ||
        $nombreUsuario === '' ||
        strlen($nombreUsuario) > 100 ||
        !is_int($expira)
    ) {
        return null;
    }

    // COMPROBAR VENCIMIENTO
    if (
        $expira < time() ||
        $expira > time() + 1800
    ) {
        return null;
    }

    // CONSULTAR HASH ACTUAL DE LA CONTRASEÑA
    $consulta = $pdo->prepare(
        'SELECT password
         FROM usuarios
         WHERE nombre_usuario = :usuario
         LIMIT 1'
    );

    $consulta->execute([
        ':usuario' => $nombreUsuario
    ]);

    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        return null;
    }

    // GENERAR CLAVE VINCULADA A LA CONTRASEÑA ACTUAL
    $claveFirma = hash_hmac(
        'sha256',
        $usuario['password'],
        $secreto
    );

    // CALCULAR FIRMA ESPERADA
    $firmaEsperada = hash_hmac(
        'sha256',
        $datosBase64,
        $claveFirma
    );

    // COMPROBAR FIRMA
    if (!hash_equals($firmaEsperada, $firmaRecibida)) {
        return null;
    }

    return $nombreUsuario;
}


// =====================================================
// 5. VALIDAR ENLACE
// =====================================================

try {

    if ($token === '') {

        $mensaje = 'No se proporcionó un enlace de recuperación válido.';
        $tipoMensaje = 'error';

    } else {

        $usuarioToken = verificarTokenRecuperacion($token, $pdo);

        if ($usuarioToken === null) {

            $mensaje = 'El enlace de recuperación es inválido o ha vencido.';
            $tipoMensaje = 'error';

        } else {

            $nombreUsuario = $usuarioToken;
            $tokenValido = true;

        }

    }

} catch (Throwable $e) {

    error_log(
        '[INSOFT][Restablecimiento] ' . $e->getMessage()
    );

    $mensaje = 'No se pudo verificar el enlace de recuperación.';
    $tipoMensaje = 'error';

}


// =====================================================
// 6. PROCESAR CAMBIO DE CONTRASEÑA
// =====================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $tokenValido
) {

    // OBTENER CONTRASEÑAS
    $password = (string) ($_POST['password'] ?? '');
    $confirmarPassword = (string) (
        $_POST['confirmar_password'] ?? ''
    );

    // VALIDAR CAMPOS
    if ($password === '' || $confirmarPassword === '') {

        $mensaje = 'Completá ambos campos.';
        $tipoMensaje = 'error';

    } elseif (strlen($password) < 8) {

        $mensaje = 'La contraseña debe tener al menos 8 caracteres.';
        $tipoMensaje = 'error';

    } elseif (strlen($password) > 1024) {

        $mensaje = 'La contraseña es demasiado larga.';
        $tipoMensaje = 'error';

    } elseif ($password !== $confirmarPassword) {

        $mensaje = 'Las contraseñas no coinciden.';
        $tipoMensaje = 'error';

    } else {

        try {

            // COMPROBAR CONEXIÓN
            if (!isset($pdo) || !($pdo instanceof PDO)) {
                throw new RuntimeException(
                    'No hay conexión con la base de datos.'
                );
            }

            // COMPROBAR QUE EL USUARIO EXISTE
            $consulta = $pdo->prepare(
                'SELECT nombre_usuario
                 FROM usuarios
                 WHERE nombre_usuario = :usuario
                 LIMIT 1'
            );

            $consulta->execute([
                ':usuario' => $nombreUsuario
            ]);

            if (!$consulta->fetch(PDO::FETCH_ASSOC)) {

                $mensaje = 'No se pudo restablecer la contraseña.';
                $tipoMensaje = 'error';

            } else {

                // GENERAR HASH ARGON2ID
                $hashPassword = password_hash(
                    $password,
                    PASSWORD_ARGON2ID
                );

                if ($hashPassword === false) {
                    throw new RuntimeException(
                        'No se pudo generar el hash.'
                    );
                }

                // ACTUALIZAR CONTRASEÑA
                $actualizar = $pdo->prepare(
                    'UPDATE usuarios
                     SET password = :password
                     WHERE nombre_usuario = :usuario'
                );

                $actualizar->execute([
                    ':password' => $hashPassword,
                    ':usuario' => $nombreUsuario
                ]);

                // REDIRIGIR AL LOGIN
                header(
                    'Location: loginn.php?recuperacion=exitosa'
                );

                exit;

            }

        } catch (Throwable $e) {

            error_log(
                '[INSOFT][Restablecimiento] ' . $e->getMessage()
            );

            $mensaje = 'No se pudo actualizar la contraseña. Intentá nuevamente.';
            $tipoMensaje = 'error';

        }

    }

}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>INADI | Restablecer contraseña</title>

    <link rel="stylesheet" href="stylesloginn.css">

    <style>

        .restablecer-ayuda {
            color: #70708a;
            line-height: 1.55;
        }

        .restablecer-enlace {
            display: block;
            margin-top: 18px;

            color: #45459c;

            font-weight: 700;
            text-align: center;
            text-decoration: none;
        }

        .restablecer-enlace:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>

    <main class="login-contenedor">

        <section class="formulario">


            <!-- ENCABEZADO -->

            <div class="encabezado">

                <a
                    href="loginn.php"
                    class="volver"
                >
                    <span aria-hidden="true">
                        &#8592;
                    </span>

                    Volver al inicio de sesión
                </a>

                <img
                    src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                    alt="Logo INADI"
                    class="logo"
                >

            </div>


            <!-- TÍTULO -->

            <div class="titulo">

                <span class="etiqueta">
                    INADI
                </span>

                <h1>
                    Restablecer contraseña
                </h1>

                <p class="restablecer-ayuda">
                    Ingresá tu nueva contraseña para recuperar
                    el acceso a tu cuenta.
                </p>

            </div>


            <!-- NOTIFICACIONES -->

            <?php if ($mensaje !== ''): ?>

                <div
                    class="mensaje <?= escaparRestablecimiento($tipoMensaje) ?>"
                    role="alert"
                    aria-live="polite"
                >
                    <?= escaparRestablecimiento($mensaje) ?>
                </div>

            <?php endif; ?>


            <!-- FORMULARIO -->

            <?php if ($tokenValido): ?>

                <form
                    action="restablecerContra.php"
                    method="POST"
                >

                    <!-- TOKEN -->

                    <input
                        type="hidden"
                        name="token"
                        value="<?= escaparRestablecimiento($token) ?>"
                    >


                    <!-- NUEVA CONTRASEÑA -->

                    <div class="campo">

                        <label for="password">
                            Nueva contraseña
                        </label>

                        <div class="password-contenedor">

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Ingresá tu nueva contraseña"
                                autocomplete="new-password"
                                minlength="8"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-password="password"
                                aria-label="Mostrar contraseña"
                                aria-pressed="false"
                            >
                                <img
                                    src="../Elementos Gráficos/ojo-cerrado.png"
                                    alt=""
                                >
                            </button>

                        </div>

                    </div>


                    <!-- CONFIRMAR CONTRASEÑA -->

                    <!-- CONFIRMAR CONTRASEÑA -->

                    <div class="campo">

                        <label for="confirmar_password">
                            Confirmar contraseña
                        </label>

                        <div class="password-contenedor">

                            <input
                                type="password"
                                id="confirmar_password"
                                name="confirmar_password"
                                placeholder="Repetí tu nueva contraseña"
                                autocomplete="new-password"
                                minlength="8"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-password="confirmar_password"
                                aria-label="Mostrar contraseña"
                                aria-pressed="false"
                            >
                                <img
                                    src="../Elementos Gráficos/ojo-cerrado.png"
                                    alt=""
                                >
                            </button>

                        </div>

                    </div>


                    <!-- BOTÓN -->

                    <button
                        type="submit"
                        class="boton"
                    >
                        CAMBIAR CONTRASEÑA
                    </button>

                </form>

            <?php else: ?>

                <a
                    href="recuperarContra.php"
                    class="restablecer-enlace"
                >
                    Solicitar un nuevo enlace
                </a>

            <?php endif; ?>


            <!-- PIE -->

            <p class="pie-formulario">
                INADI · Instituto de Informática
            </p>

        </section>

    </main>

</body>

</html>
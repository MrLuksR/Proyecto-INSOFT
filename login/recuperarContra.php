<?php

// =====================================================
// DEPENDENCIAS
// =====================================================

require_once __DIR__ . '/../database/consultas/conexion.php';
require_once __DIR__ . '/../database/consultas/encriptacion.php';
require_once __DIR__ . '/../Mailer/enviarCorreo.php';


// =====================================================
// VARIABLES
// =====================================================

$mensaje = '';
$tipoMensaje = '';


// =====================================================
// PROCESAR FORMULARIO
// =====================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombreUsuario = trim($_POST['nombre_usuario'] ?? '');

    if ($nombreUsuario === '') {

        $mensaje = 'Ingresá tu nombre de usuario.';
        $tipoMensaje = 'error';

    } else {

        // RESPUESTA GENÉRICA POR SEGURIDAD
        $mensaje = 'Si el usuario existe, recibirás un correo con un enlace para restablecer tu contraseña.';
        $tipoMensaje = 'success';

        try {

            // BUSCAR USUARIO
            $consulta = $pdo->prepare(
                'SELECT
                    nombre_usuario,
                    correo,
                    correo_iv,
                    correo_tag,
                    password
                FROM usuarios
                WHERE nombre_usuario = :usuario
                LIMIT 1'
            );

            $consulta->execute([
                ':usuario' => $nombreUsuario
            ]);

            $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {

                // DESCIFRAR CORREO
                $correo = decrypt_aes_256_gcm(
                    $usuario['correo'],
                    $key,
                    $usuario['correo_iv'],
                    $usuario['correo_tag']
                );

                // CLAVE SECRETA PARA FIRMAR ENLACES
                $secreto = getenv('INSOFT_RESET_SECRET');

                if (!$secreto || strlen($secreto) < 32) {
                    throw new RuntimeException(
                        'Falta configurar INSOFT_RESET_SECRET.'
                    );
                }

                // GENERAR TOKEN CON VENCIMIENTO
                $expira = time() + 1800;

                $datos = base64_encode(
                    json_encode([
                        'usuario' => $nombreUsuario,
                        'expira' => $expira
                    ])
                );

                // VINCULAR EL TOKEN A LA CONTRASEÑA ACTUAL
                $claveFirma = hash_hmac(
                    'sha256',
                    $usuario['password'],
                    $secreto
                );

                // FIRMAR LOS DATOS DE RECUPERACIÓN
                $firma = hash_hmac(
                    'sha256',
                    $datos,
                    $claveFirma
                );

                $token = rtrim(strtr($datos, '+/', '-_'), '=')
                    . '.'
                    . $firma;

                // URL PÚBLICA
                $base = rtrim(
                    getenv('MP_BASE_URL'),
                    '/'
                );

                if (
                    !filter_var($base, FILTER_VALIDATE_URL) ||
                    parse_url($base, PHP_URL_SCHEME) !== 'https'
                ) {
                    throw new RuntimeException(
                        'La URL pública no está configurada correctamente.'
                    );
                }

                $url = $base
                    . '/login/restablecerContra.php?token='
                    . urlencode($token);

                $urlSegura = htmlspecialchars(
                    $url,
                    ENT_QUOTES,
                    'UTF-8'
                );

                // PREPARAR CORREO
                $contenido = '
                    Recibimos una solicitud para recuperar tu contraseña.
                    <br><br>

                    <a href="' . $urlSegura . '"
                       style="
                           display:inline-block;
                           padding:14px 20px;
                           background:#45459c;
                           color:white;
                           border-radius:8px;
                           text-decoration:none;
                       ">
                        Restablecer contraseña
                    </a>

                    <br><br>
                    Este enlace vence en 30 minutos.

                    <br><br>
                    Si no solicitaste este cambio, ignorá el mensaje.
                ';

                // ENVIAR CORREO
                enviarCorreo(
                    $correo,
                    'INADI - Recuperar contraseña',
                    plantillaCorreo(
                        'Recuperar contraseña',
                        $contenido
                    )
                );

            }

        } catch (Throwable $e) {

            error_log(
                '[INSOFT][Recuperación] ' . $e->getMessage()
            );

        }

    }

}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI | Recuperar contraseña</title>

    <link rel="stylesheet" href="stylesloginn.css">

</head>

<body>

    <main class="login-contenedor">

        <section class="formulario">

            <!-- ENCABEZADO -->
            <div class="encabezado">

                <a href="loginn.php" class="volver">
                    ← Volver al inicio de sesión
                </a>

                <img
                    src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                    alt="Logo INADI"
                    class="logo"
                >

            </div>

            <!-- TÍTULO -->
            <div class="titulo">

                <span class="etiqueta">INADI</span>

                <h1>Recuperar contraseña</h1>

                <p>
                    Ingresá tu nombre de usuario para recibir
                    un enlace de recuperación en tu correo.
                </p>

            </div>

            <!-- NOTIFICACIÓN -->
            <?php if ($mensaje !== ''): ?>

                <div class="mensaje <?= htmlspecialchars($tipoMensaje) ?>">
                    <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
                </div>

            <?php endif; ?>

            <!-- FORMULARIO -->
            <form method="POST">

                <div class="campo">

                    <label for="nombre_usuario">
                        Nombre de usuario
                    </label>

                    <input
                        type="text"
                        id="nombre_usuario"
                        name="nombre_usuario"
                        placeholder="Ingresá tu nombre de usuario"
                        autocomplete="username"
                        required
                    >

                </div>

                <button type="submit" class="boton">
                    ENVIAR CORREO DE RECUPERACIÓN
                </button>

            </form>

            <p class="pie-formulario">
                INADI · Instituto de Informática
            </p>

        </section>

    </main>

</body>
</html>
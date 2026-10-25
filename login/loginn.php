
<?php
$mensaje = '';
$tipoMensaje = '';
$nombreUsuario = trim($_GET['usuario'] ?? '');

$error = $_GET['error'] ?? '';
$registro = $_GET['registro'] ?? '';

// Mostrar notificaciones.
if ($registro === 'exitoso') {
    $mensaje = '¡Registro exitoso! Tu cuenta fue creada. Ya podés iniciar sesión.';
    $tipoMensaje = 'success';
} else {
    switch ($error) {
        case 'campos':
            $mensaje = 'Por favor, completá todos los campos.';
            $tipoMensaje = 'error';
            break;

        case 'correo':
            $mensaje = 'Ingresá una dirección de correo electrónico válida.';
            $tipoMensaje = 'error';
            break;

        case 'duplicado':
            $mensaje = 'El nombre de usuario, la cédula o algún dato ya está registrado.';
            $tipoMensaje = 'error';
            break;

        case 'registro':
            $mensaje = 'No se pudo completar el registro. Intentá nuevamente.';
            $tipoMensaje = 'error';
            break;

        case 'credenciales':
            $mensaje = 'El nombre de usuario o la contraseña son incorrectos.';
            $tipoMensaje = 'error';
            break;

        case 'rol':
            $mensaje = 'Tu cuenta no tiene acceso al portal de estudiantes.';
            $tipoMensaje = 'error';
            break;

        case 'estado':
            $mensaje = 'Tu cuenta no está habilitada para iniciar sesión.';
            $tipoMensaje = 'error';
            break;

        case 'error':
            $mensaje = 'Ocurrió un error al iniciar sesión. Intentá nuevamente.';
            $tipoMensaje = 'error';
            break;
            
        case 'envio':
            $mensaje = 'No pudimos enviar el código de verificación a tu correo. Intentá nuevamente.';
            $tipoMensaje = 'error';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI | Iniciar sesión</title>

    <link rel="stylesheet" href="stylesloginn.css">
</head>

<body>

    <main class="login-contenedor">
        <section class="formulario">

            <!-- ENCABEZADO -->
            <div class="encabezado">
                <a
                    href="../Inicio/inicioindex.php"
                    class="volver"
                    aria-label="Volver al inicio"
                    title="Volver al inicio"
                >
                    <span aria-hidden="true">&#8592;</span>
                    Volver al inicio
                </a>

                <img
                    src="../Elementos%20Gr%C3%A1ficos/Logo%20Inadi%20con%20Brillo.png"
                    alt="Logo INADI"
                    class="logo"
                >
            </div>

            <!-- TÍTULO -->
            <div class="titulo">
                <span class="etiqueta">INADI</span>
                <h1>Iniciar sesión</h1>
                <p>Ingresá con tu nombre de usuario y contraseña.</p>
            </div>

            <!-- NOTIFICACIONES -->
            <?php if ($mensaje !== ''): ?>
                <div
                    class="mensaje <?= htmlspecialchars($tipoMensaje, ENT_QUOTES, 'UTF-8') ?>"
                    role="alert"
                    aria-live="polite"
                >
                    <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO DE INICIO DE SESIÓN -->
            <form action="login.php" method="POST">

                <div class="campo">
                    <label for="nombre_usuario">
                        Nombre de usuario
                    </label>

                    <input
                        type="text"
                        id="nombre_usuario"
                        name="nombre_usuario"
                        placeholder="Ingresá tu nombre de usuario"
                        value="<?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="username"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="password">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Ingresá tu contraseña"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="boton">
                    INICIAR SESIÓN
                    <span aria-hidden="true">&#8594;</span>
                </button>

            </form>

            <!-- REGISTRO -->
            <div class="enlace-registro">
                <p>¿No tenés cuenta?</p>

                <a
                    href="../registro/index.php"
                    class="boton-registro"
                >
                    Regístrate aquí
                </a>
            </div>

            <!-- PIE -->
            <p class="pie-formulario">
                INADI · Instituto de Informática
            </p>

        </section>
    </main>

</body>
</html>

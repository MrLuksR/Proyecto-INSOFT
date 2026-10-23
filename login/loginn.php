
<?php
$mensaje = "";
$tipoMensaje = "";
$nombreUsuario = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombreUsuario = trim($_POST["nombre_usuario"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($nombreUsuario === "" || $password === "") {
        $mensaje = "Por favor, completa todos los campos.";
        $tipoMensaje = "error";
    } else {
       
        $mensaje = "Datos recibidos correctamente.";
        $tipoMensaje = "success";
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

            <!-- LOGO Y BOTÓN VOLVER -->
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
                    src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                    alt="Logo INADI"
                    class="logo"
                >
            </div>

            <!-- TÍTULO -->
            <div class="titulo">

                <span class="etiqueta">INADI</span>

                <h1>Iniciar sesión</h1>

                <p>
                    Ingresá con tu nombre de usuario y contraseña.
                </p>

            </div>

            <!-- MENSAJE -->
            <?php if ($mensaje !== ""): ?>

                <div class="mensaje <?php echo htmlspecialchars($tipoMensaje, ENT_QUOTES, 'UTF-8'); ?>"
                     role="status">

                    <?php
                    echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');
                    ?>

                </div>

            <?php endif; ?>

            <!-- FORMULARIO -->
            <form action="login.php" method="POST">

                <!-- NOMBRE DE USUARIO -->
                <div class="campo">

                    <label for="nombre_usuario">
                        Nombre de usuario
                    </label>

                    <input
                        type="text"
                        id="nombre_usuario"
                        name="nombre_usuario"
                        placeholder="Ingresá tu nombre de usuario"
                        value="<?php echo htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8'); ?>"
                        autocomplete="username"
                        required
                    >

                </div>

                <!-- CONTRASEÑA -->
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

                <!-- BOTÓN -->
                <button type="submit" class="boton">
                    INICIAR SESIÓN
                    <span aria-hidden="true">&#8594;</span>
                </button>

            </form>

            <p class="pie-formulario">
                INADI · Instituto de Informática
            </p>

        </section>

    </main>

</body>
</html>

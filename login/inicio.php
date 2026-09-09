<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.html');
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI - Inicio</title>
</head>

<body>

    <h1>¡Bienvenido a INADI!</h1>

    <h2>Inicio de sesión correcto</h2>

    <p>
        <strong>Usuario:</strong>
        <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>
    </p>

    <p>
        <strong>Nombre:</strong>
        <?php echo htmlspecialchars($_SESSION['nombre']); ?>
    </p>

    <p>
        <strong>Apellido:</strong>
        <?php echo htmlspecialchars($_SESSION['apellido']); ?>
    </p>

    <p>
        <strong>Rol:</strong>
        <?php echo htmlspecialchars($_SESSION['id_rol']); ?>
    </p>

    <br>

    <a href="logout.php">Cerrar sesión</a>

</body>

</html>
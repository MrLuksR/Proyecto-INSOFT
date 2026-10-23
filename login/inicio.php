<?php
include("../database/Clases/Estudiante.php");
session_start();

// No permitir que el navegador guarde esta página
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['usuario'])) {
    header('Location: loginn.php');
    exit;
}else{
    $usuario = $_SESSION['usuario'];
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
        <?php echo htmlspecialchars($usuario->getNombreUsuario()); ?>
    </p>

    <p>
        <strong>Nombre:</strong>
        <?php echo htmlspecialchars($usuario->getNombre()); ?>
    </p>

    <p>
        <strong>Apellido:</strong>
        <?php echo htmlspecialchars($usuario->getApellido()); ?>
    </p>

    <p>
        <strong>Rol:</strong>
        <?php echo htmlspecialchars($_SESSION['tipo_usuario']); ?>
    </p>

    <br>

    <a href="logout.php">Cerrar sesión</a>
    <a href="../Estudiante/portalEstudiante.php">Acceder al Portal</a>

</body>

</html>
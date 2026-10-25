
<?php

require_once __DIR__ . '/../conexion.php';

// Aceptar únicamente envíos del formulario.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Recibir los datos.
$nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
$nombre         = trim($_POST['nombre'] ?? '');
$apellido       = trim($_POST['apellido'] ?? '');
$cedula         = trim($_POST['cedula'] ?? '');
$fecha          = trim($_POST['fecha'] ?? '');
$correo         = trim($_POST['correo'] ?? '');
$password       = $_POST['password'] ?? '';

// Rol de estudiante.
$id_rol = 5;

// Validar campos obligatorios.
if (
    $nombre_usuario === '' ||
    $nombre === '' ||
    $apellido === '' ||
    $cedula === '' ||
    $fecha === '' ||
    $correo === '' ||
    $password === ''
) {
    header('Location: index.php?error=campos');
    exit;
}

// Validar el correo electrónico.
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?error=correo');
    exit;
}

// Cifrar la contraseña.
$password_hash = password_hash($password, PASSWORD_DEFAULT);

if ($password_hash === false) {
    header('Location: index.php?error=registro');
    exit;
}

// Insertar el estudiante.
$sql = 'INSERT INTO usuarios (
            nombre_usuario,
            nombre,
            apellido,
            cedula,
            fecha,
            correo,
            password,
            id_rol
        ) VALUES (
            :nombre_usuario,
            :nombre,
            :apellido,
            :cedula,
            :fecha,
            :correo,
            :password,
            :id_rol
        )';

try {
    $consulta = $pdo->prepare($sql);

    $consulta->execute([
        ':nombre_usuario' => $nombre_usuario,
        ':nombre'         => $nombre,
        ':apellido'       => $apellido,
        ':cedula'         => $cedula,
        ':fecha'          => $fecha,
        ':correo'         => $correo,
        ':password'       => $password_hash,
        ':id_rol'         => $id_rol
    ]);

    // Registro exitoso: ir al inicio de sesión.
    header('Location: ../login/loginn.php?registro=exitoso');
    exit;

} catch (PDOException $e) {
    error_log('Error en registro.php: ' . $e->getMessage());

    // MySQL 1062: dato duplicado.
    if (
        isset($e->errorInfo[1]) &&
        (int) $e->errorInfo[1] === 1062
    ) {
        header('Location: index.php?error=duplicado');
        exit;
    }

    header('Location: index.php?error=registro');
    exit;
}

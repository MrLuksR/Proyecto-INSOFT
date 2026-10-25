<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../encriptacion.php';

function volverAlFormulario(array $errores, array $datos = []): never
{
    $_SESSION['errores_registro'] = $errores;
    $_SESSION['datos_registro'] = $datos;
    header('Location: /Proyecto-INSOFT/registro/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /Proyecto-INSOFT/registro/index.php');
    exit;
}

/*
 * Los nombres coinciden con los campos reales de registro/index.php.
 */
$nombre_usuario = trim($_POST['username'] ?? '');
$nombre         = trim($_POST['nombre'] ?? '');
$apellido       = trim($_POST['apellido'] ?? '');
$cedula         = trim($_POST['cedula'] ?? '');
$fecha          = trim($_POST['fecha_nacimiento'] ?? '');
$correo         = trim($_POST['email'] ?? '');
$telefono       = trim($_POST['telefono'] ?? '');
$password       = (string)($_POST['password'] ?? '');
$curso          = trim($_POST['curso'] ?? '');

$datosFormulario = [
    'nombre' => $nombre,
    'apellido' => $apellido,
    'cedula' => $cedula,
    'fecha_nacimiento' => $fecha,
    'telefono' => $telefono,
    'email' => $correo,
    'username' => $nombre_usuario,
    'curso' => $curso,
    'nombre_tutor' => trim($_POST['nombre_tutor'] ?? ''),
    'telefono_tutor' => trim($_POST['telefono_tutor'] ?? ''),
];

$errores = [];
if ($nombre_usuario === '' || $nombre === '' || $apellido === '' ||
    $cedula === '' || $fecha === '' || $correo === '' ||
    $password === '' || $curso === '') {
    $errores[] = 'Completá todos los campos obligatorios.';
}
if ($nombre_usuario !== '' && !preg_match('/^[a-zA-Z0-9_.-]{4,50}$/', $nombre_usuario)) {
    $errores[] = 'El nombre de usuario debe tener entre 4 y 50 caracteres y solo puede contener letras, números, punto, guion o guion bajo.';
}
if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'Ingresá un correo electrónico válido.';
}
if (strlen($password) < 8) {
    $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
}
if (mb_strlen($nombre) > 20) {
    $errores[] = 'El nombre no puede superar los 20 caracteres.';
}
if ($errores) {
    volverAlFormulario($errores, $datosFormulario);
}

$fotoNombre = null;
try {
    // El esquema actual usa usuarios como tabla principal y estudiantes
    // para los datos complementarios del estudiante.
    $consulta = $pdo->prepare('SELECT nombre_usuario FROM usuarios WHERE nombre_usuario = :u LIMIT 1');
    $consulta->execute([':u' => $nombre_usuario]);
    if ($consulta->fetch()) {
        volverAlFormulario(['Ese nombre de usuario ya está registrado. Elegí otro.'], $datosFormulario);
    }

    // Guardar foto con nombre seguro, si se adjuntó.
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            volverAlFormulario(['No se pudo cargar la fotografía. Probá nuevamente.'], $datosFormulario);
        }
        if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
            volverAlFormulario(['La fotografía no puede superar los 5 MB.'], $datosFormulario);
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['foto']['tmp_name']);
        $extensiones = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($extensiones[$mime])) {
            volverAlFormulario(['La fotografía debe estar en formato JPG, PNG o WEBP.'], $datosFormulario);
        }

        $directorioFotos = __DIR__ . '/fotosEst';
        if (!is_dir($directorioFotos) && !mkdir($directorioFotos, 0755, true) && !is_dir($directorioFotos)) {
            throw new RuntimeException('No se pudo crear el directorio de fotografías.');
        }
        $fotoNombre = 'f_' . substr(hash('sha256', $nombre_usuario), 0, 12) . '.' . $extensiones[$mime];
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $directorioFotos . '/' . $fotoNombre)) {
            throw new RuntimeException('No se pudo guardar la fotografía.');
        }
    }

    $cifradoCedula = encrypt_aes_256_gcm($cedula, $key);
    $cifradoCorreo = encrypt_aes_256_gcm($correo, $key);
    $cifradoFecha = encrypt_aes_256_gcm($fecha, $key);
    $cifradoTelefono = $telefono !== '' ? encrypt_aes_256_gcm($telefono, $key) : null;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    if ($hash === false) {
        throw new RuntimeException('No se pudo proteger la contraseña.');
    }

    $pdo->beginTransaction();

    $sqlUsuario = 'INSERT INTO usuarios
        (nombre_usuario, nombre, apellido, cedula, cedula_iv, cedula_tag,
         correo, correo_iv, correo_tag, password, estado)
        VALUES
        (:usuario, :nombre, :apellido, :cedula, :cedula_iv, :cedula_tag,
         :correo, :correo_iv, :correo_tag, :password, :estado)';
    $stmt = $pdo->prepare($sqlUsuario);
    $stmt->execute([
        ':usuario' => $nombre_usuario,
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':cedula' => $cifradoCedula['ciphertext'],
        ':cedula_iv' => $cifradoCedula['iv'],
        ':cedula_tag' => $cifradoCedula['tag'],
        ':correo' => $cifradoCorreo['ciphertext'],
        ':correo_iv' => $cifradoCorreo['iv'],
        ':correo_tag' => $cifradoCorreo['tag'],
        ':password' => $hash,
        ':estado' => 'Activo',
    ]);

    $sqlEstudiante = 'INSERT INTO estudiantes
        (nombre_usuario, telefono, telefono_iv, telefono_tag,
         fecha_nacimiento, fecha_nacimiento_iv, fecha_nacimiento_tag, foto)
        VALUES (:usuario, :telefono, :telefono_iv, :telefono_tag,
                :fecha, :fecha_iv, :fecha_tag, :foto)';
    $stmtEstudiante = $pdo->prepare($sqlEstudiante);
    $stmtEstudiante->execute([
        ':usuario' => $nombre_usuario,
        ':telefono' => $cifradoTelefono['ciphertext'] ?? null,
        ':telefono_iv' => $cifradoTelefono['iv'] ?? null,
        ':telefono_tag' => $cifradoTelefono['tag'] ?? null,
        ':fecha' => $cifradoFecha['ciphertext'],
        ':fecha_iv' => $cifradoFecha['iv'],
        ':fecha_tag' => $cifradoFecha['tag'],
        ':foto' => $fotoNombre,
    ]);

    $pdo->commit();

    $_SESSION['registro_exitoso'] = true;
    $_SESSION['nombre_registrado'] = $nombre;
    header('Location: /Proyecto-INSOFT/registro/index.php');
    exit;

} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    if ($fotoNombre) {
        $rutaFoto = __DIR__ . '/fotosEst/' . basename($fotoNombre);
        if (is_file($rutaFoto)) {
            @unlink($rutaFoto);
        }
    }
    error_log('Error en registro de estudiante INADI: ' . $e->getMessage());
    volverAlFormulario(['No se pudo completar el registro. Verificá los datos e intentá nuevamente.'], $datosFormulario);
}

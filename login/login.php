<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../database/consultas/conexion.php';
require_once __DIR__ . '/../database/consultas/encriptacion.php';
require_once __DIR__ . '/../database/Clases/Estudiante.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: loginn.php');
    exit;
}

$nombreUsuario = trim($_POST['nombre_usuario'] ?? '');
$password = (string)($_POST['password'] ?? '');

if ($nombreUsuario === '' || $password === '') {
    header('Location: loginn.php?error=campos');
    exit;
}

try {
    /*
     * En el esquema actual el rol de estudiante se identifica por la
     * existencia de su registro en la tabla estudiantes. La tabla usuarios
     * del SQL del proyecto no tiene columnas id_rol ni tipo_usuario.
     */
    $sql = 'SELECT
                u.nombre_usuario, u.nombre, u.apellido,
                u.cedula, u.cedula_iv, u.cedula_tag,
                u.correo, u.correo_iv, u.correo_tag,
                u.password, u.estado,
                e.telefono, e.telefono_iv, e.telefono_tag,
                e.fecha_nacimiento, e.fecha_nacimiento_iv, e.fecha_nacimiento_tag,
                e.foto
            FROM usuarios u
            INNER JOIN estudiantes e ON e.nombre_usuario = u.nombre_usuario
            WHERE u.nombre_usuario = :usuario
            LIMIT 1';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':usuario' => $nombreUsuario]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$datos || !password_verify($password, (string)$datos['password'])) {
        header('Location: loginn.php?error=credenciales');
        exit;
    }

    $estado = strtolower(trim((string)($datos['estado'] ?? '')));
    if (in_array($estado, ['inactivo', 'bloqueado', 'suspendido', 'baja'], true)) {
        header('Location: loginn.php?error=estado');
        exit;
    }

    // Descifrar los datos personales cifrados durante el registro.
    $cedula = '';
    $correo = '';
    $telefono = '';
    $fechaNacimiento = '';

    try {
        if (!empty($datos['cedula_iv']) && !empty($datos['cedula_tag'])) {
            $cedula = decrypt_aes_256_gcm($datos['cedula'], $key, $datos['cedula_iv'], $datos['cedula_tag']);
        }
        if (!empty($datos['correo_iv']) && !empty($datos['correo_tag'])) {
            $correo = decrypt_aes_256_gcm($datos['correo'], $key, $datos['correo_iv'], $datos['correo_tag']);
        }
        if (!empty($datos['telefono']) && !empty($datos['telefono_iv']) && !empty($datos['telefono_tag'])) {
            $telefono = decrypt_aes_256_gcm($datos['telefono'], $key, $datos['telefono_iv'], $datos['telefono_tag']);
        }
        if (!empty($datos['fecha_nacimiento']) && !empty($datos['fecha_nacimiento_iv']) && !empty($datos['fecha_nacimiento_tag'])) {
            $fechaNacimiento = decrypt_aes_256_gcm(
                $datos['fecha_nacimiento'], $key,
                $datos['fecha_nacimiento_iv'], $datos['fecha_nacimiento_tag']
            );
        }
    } catch (Throwable $e) {
        // El acceso puede continuar aunque algún dato antiguo no sea descifrable.
        error_log('No se pudieron descifrar todos los datos del estudiante: ' . $e->getMessage());
    }

    session_regenerate_id(true);

    $_SESSION['usuario'] = new Estudiante(
        null,
        (string)$datos['nombre'],
        (string)$datos['apellido'],
        $cedula,
        $correo,
        (string)$datos['nombre_usuario'],
        'estudiante'
    );

    // Datos simples para que las páginas del portal puedan personalizarse.
    $_SESSION['id_usuario'] = (string)$datos['nombre_usuario'];
    $_SESSION['nombre_usuario'] = (string)$datos['nombre_usuario'];
    $_SESSION['nombre'] = (string)$datos['nombre'];
    $_SESSION['apellido'] = (string)$datos['apellido'];
    $_SESSION['cedula'] = $cedula;
    $_SESSION['correo'] = $correo;
    $_SESSION['telefono'] = $telefono;
    $_SESSION['fecha_nacimiento'] = $fechaNacimiento;
    $_SESSION['foto'] = (string)($datos['foto'] ?? '');
    $_SESSION['tipo_usuario'] = 'estudiante';

    header('Location: /Proyecto-INSOFT/Inicio/inicioestudiante.php');
    exit;

} catch (Throwable $e) {
    error_log('Error en login.php: ' . $e->getMessage());
    header('Location: loginn.php?error=error');
    exit;
}

<?php
    // Traer la conexión de BD
    require_once '../database/consultas/conexion.php';
    require_once '../database/consultas/encriptacion.php';
    $key = "d21c2cdfbca3d79e4dc4d31cea91f75a78a872f874d58fae9ad1a7fbcf0b1053";

    // Preparar consulta
    $consulta = "SELECT
                    u.cedula,
                    u.cedula_iv,
                    u.cedula_tag,

                    u.correo,
                    u.correo_iv,
                    u.correo_tag,

                    u.estado,

                    e.telefono,
                    e.telefono_iv,
                    e.telefono_tag,

                    e.fecha_nacimiento,
                    e.fecha_nacimiento_iv,
                    e.fecha_nacimiento_tag

                FROM usuarios AS u

                INNER JOIN estudiantes AS e
                    ON u.nombre_usuario = e.nombre_usuario

                WHERE u.nombre_usuario = :nombre_usuario

                LIMIT 1;";
    
    $stmt = $pdo->prepare($consulta);
    $stmt->execute([':nombre_usuario' => $_SESSION['nombre_usuario']]);
    $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

    $estudiante['cedula'] = decrypt_aes_256_gcm($estudiante['cedula'], $key, $estudiante['cedula_iv'], $estudiante['cedula_tag']);
    $estudiante['correo'] = decrypt_aes_256_gcm($estudiante['correo'], $key, $estudiante['correo_iv'], $estudiante['correo_tag']);
    $estudiante['telefono'] = decrypt_aes_256_gcm($estudiante['telefono'], $key, $estudiante['telefono_iv'], $estudiante['telefono_tag']);
    $estudiante['fecha_nacimiento'] = decrypt_aes_256_gcm($estudiante['fecha_nacimiento'], $key, $estudiante['fecha_nacimiento_iv'], $estudiante['fecha_nacimiento_tag']);
?>
<?php
// Se selecciona el algoritmo Argon2id como medida de protección para contraseñas
// Se selecciona el algoritmo AES-256-GCM de OpenSSL para cifrado de datos personales
// CONEXIÓN CON LA BASE DE DATOS
require_once '../conexion.php';
require_once '../encriptacion.php';

/* 
Se implementa el módulo de encriptacion.php que contiene funciones de cifrado y
decifrado para diferentes datos sensibles dentro de la BD, de este modo se asegura
que usuario está robustamente protegido. Se utiliza funciones de la bilbioteca de
código abierto OpenSSL que permite generar cifrados de distintos tipos. Es necesario
agregar al key a un archivo .env (ya creado) en la carpeta .gitignore, de este modo
la clave maestra de cifrado de datos está oculta para Github.
 */

// COMPROBAR SI SE ENVIÓ EL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Clave extraida del .env
    //$key = $_ENV['ENCRIPTON_KEY'];
    $key = "d21c2cdfbca3d79e4dc4d31cea91f75a78a872f874d58fae9ad1a7fbcf0b1053";

    // RECIBIR LOS DATOS DEL FORMULARIO
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cedula = encrypt_aes_256_gcm($_POST['cedula'], $key); // Cifrado con clave .env
    $fecha = encrypt_aes_256_gcm($_POST['fecha'], $key);
    $correo = encrypt_aes_256_gcm($_POST['correo'], $key);
    $password = password_hash($_POST['password'], PASSWORD_ARGON2ID); // Contraseña cifrada (No es recuperable)
    // EL ROL 5 ES ESTUDIANTE
    $id_rol = 5;

    try {

        // INSERTAR EL ESTUDIANTE
        $sql = "INSERT INTO usuarios
                (
                    nombre_usuario,
                    apellido,
                    cedula,
                    cedula_iv,
                    cedula_tag,
                    fecha,
                    fecha_iv,
                    fecha_tag,
                    email,
                    email_iv,
                    email_tag,
                    password,
                    id_rol
                )
                VALUES
                (
                    :nombre,
                    :apellido,
                    :cedula,
                    :cedula_iv,
                    :cedula_tag,
                    :fecha,
                    :fecha_iv,
                    :fecha_tag,
                    :email,
                    :email_iv,
                    :email_tag,
                    :password,
                    :id_rol
                )";

        // PREPARAR LA CONSULTA
        $consulta = $pdo->prepare($sql);

        // EJECUTAR
        $consulta->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':cedula' => $cedula['ciphertext'],
            ':cedula_iv' => $cedula['iv'],
            ':cedula_tag' => $cedula['tag'],
            ':fecha' => $fecha['ciphertext'],
            ':fecha_iv' => $fecha['iv'],
            ':fecha_tag' => $fecha['tag'],
            ':email' => $correo['ciphertext'],
            ':email_iv' => $correo['iv'],
            ':email_tag' => $correo['tag'],
            ':password' => $password,
            ':id_rol' => 5
        ]);

        // REGISTRO EXITOSO

        echo "<!DOCTYPE html>";
        echo "<html lang='es'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>Registro exitoso - INADI</title>";
        echo "</head>";

        echo "<body>";

        echo "<h1>¡Registro exitoso!</h1>";

        echo "<p>El estudiante fue registrado correctamente.</p>";

        echo "<a href='index.html'>Volver al formulario</a>";

        echo "</body>";
        echo "</html>";

    } catch (PDOException $e) {

        echo "<h1>Error al registrar</h1>";

        echo "<p>No se pudo registrar el estudiante.</p>";

        echo "<p>" . $e->getMessage() . "</p>";
    }

} else {

    echo "<h1>Error</h1>";
    echo "<p>El formulario no fue enviado correctamente.</p>";

}

?>
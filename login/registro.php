<?php

// CONEXIÓN CON LA BASE DE DATOS
require_once '../conexion.php';

// COMPROBAR SI SE ENVIÓ EL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // RECIBIR Y LIMPIAR LOS DATOS
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $cedula = trim($_POST['cedula'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    // EL ROL 5 CORRESPONDE A "ESTUDIANTE"
    $id_rol = 5;

    // COMPROBAR QUE LOS CAMPOS OBLIGATORIOS NO ESTÉN VACÍOS
    if (
        empty($nombre_usuario) ||
        empty($nombre) ||
        empty($apellido) ||
        empty($cedula) ||
        empty($fecha) ||
        empty($correo) ||
        empty($password)
    ) {
        die('Error: todos los campos obligatorios deben estar completos.');
    }

    // CIFRAR LA CONTRASEÑA
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // CONSULTA SQL
    $sql = 'INSERT INTO usuarios
            (
                nombre_usuario,
                nombre,
                apellido,
                cedula,
                fecha,
                correo,
                password,
                id_rol
            )
            VALUES
            (
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

        // PREPARAR LA CONSULTA
        $consulta = $pdo->prepare($sql);

        // ASIGNAR LOS VALORES
        $consulta->bindParam(
            ':nombre_usuario',
            $nombre_usuario,
            PDO::PARAM_STR
        );

        $consulta->bindParam(
            ':nombre',
            $nombre,
            PDO::PARAM_STR
        );

        $consulta->bindParam(
            ':apellido',
            $apellido,
            PDO::PARAM_STR
        );

        $consulta->bindParam(
            ':cedula',
            $cedula,
            PDO::PARAM_STR
        );

        $consulta->bindParam(
            ':fecha',
            $fecha,
            PDO::PARAM_STR
        );

        $consulta->bindParam(
            ':correo',
            $correo,
            PDO::PARAM_STR
        );

        // GUARDAR LA CONTRASEÑA CIFRADA
        $consulta->bindParam(
            ':password',
            $password_hash,
            PDO::PARAM_STR
        );

        $consulta->bindParam(
            ':id_rol',
            $id_rol,
            PDO::PARAM_INT
        );

        // EJECUTAR LA CONSULTA
        $consulta->execute();

        // REGISTRO EXITOSO
        echo '<!DOCTYPE html>';
        echo '<html lang="es">';

        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<title>Registro exitoso - INADI</title>';
        echo '</head>';

        echo '<body>';

        echo '<h1>¡Registro exitoso!</h1>';

        echo '<p>';
        echo 'El estudiante fue registrado correctamente.';
        echo '</p>';

        echo '<p>';
        echo '<a href="login.html">Iniciar sesión</a>';
        echo '</p>';

        echo '</body>';
        echo '</html>';

    } catch (PDOException $e) {

        // COMPROBAR SI EL ERROR ES POR UN DATO DUPLICADO
        if ($e->errorInfo[1] == 1062) {

            echo '<h1>Error al registrar</h1>';

            echo '<p>';
            echo 'El nombre de usuario o algún dato ingresado ya existe.';
            echo '</p>';

            echo '<p>';
            echo '<a href="index.html">Volver al formulario</a>';
            echo '</p>';

        } else {

            echo '<h1>Error al registrar</h1>';

            echo '<p>';
            echo 'No se pudo registrar el estudiante.';
            echo '</p>';

            echo '<p>';
            echo $e->getMessage();
            echo '</p>';
        }
    }
}

?>



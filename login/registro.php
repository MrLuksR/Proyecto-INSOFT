<?php

// CONEXIÓN CON LA BASE DE DATOS
require_once '../conexion.php';

// COMPROBAR SI SE ENVIÓ EL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // RECIBIR LOS DATOS DEL FORMULARIO
    $nombre_usuario = $_POST['nombre_usuario'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cedula = $_POST['cedula'];
    $fecha = $_POST['fecha'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    // EL ROL 5 CORRESPONDE A "ESTUDIANTE"
    $id_rol = 5;

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

        $consulta->bindParam(
            ':password',
            $password,
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
        echo '<p>El estudiante fue registrado correctamente.</p>';

        echo '<p>';
        echo '<a href="index.html">Volver al formulario</a>';
        echo '</p>';

        echo '</body>';
        echo '</html>';

    } catch (PDOException $e) {

        echo '<h1>Error al registrar</h1>';
        echo '<p>No se pudo registrar el estudiante.</p>';
        echo '<p>' . $e->getMessage() . '</p>';

    }

}

?>

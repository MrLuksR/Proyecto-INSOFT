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

    // EL ROL 5 ES ESTUDIANTE
    $id_rol = 5;

    try {

        // INSERTAR EL ESTUDIANTE
        $sql = "INSERT INTO usuarios
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
                )";

        // PREPARAR LA CONSULTA
        $consulta = $pdo->prepare($sql);

        // EJECUTAR
        $consulta->execute([
            ':nombre_usuario' => $nombre_usuario,
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':cedula' => $cedula,
            ':fecha' => $fecha,
            ':correo' => $correo,
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
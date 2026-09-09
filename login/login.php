<?php

// CONEXIÓN CON LA BASE DE DATOS
require_once '../conexion.php';

// INICIAR SESIÓN
session_start();

// COMPROBAR SI SE ENVIÓ EL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // RECIBIR LOS DATOS
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $password = $_POST['password'] ?? '';  

    // COMPROBAR QUE LOS CAMPOS NO ESTÉN VACÍOS
    if ($nombre_usuario === '' || $password === '') {

        echo '<h1>Error</h1>';
        echo '<p>Debe completar todos los campos.</p>';
        echo '<a href="login.html">Volver al login</a>';

        exit;
    }

    try {

        // BUSCAR EL USUARIO EN LA BASE DE DATOS
        $sql = "SELECT *
                FROM usuarios
                WHERE nombre_usuario = :nombre_usuario";

        $consulta = $pdo->prepare($sql);

        $consulta->bindParam(
            ':nombre_usuario',
            $nombre_usuario,
            PDO::PARAM_STR
        );

        $consulta->execute();

        // OBTENER EL USUARIO
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);


        // COMPROBAR USUARIO Y CONTRASEÑA
        if (
            $usuario &&
            password_verify($password, $usuario['password'])
        ) {

            // CREAR LA SESIÓN DEL USUARIO
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['apellido'] = $usuario['apellido'];
            $_SESSION['id_rol'] = $usuario['id_rol'];

            // REDIRIGIR A LA PÁGINA DE INICIO
            header('Location: inicio.php');
            exit;

        } else {

            // DATOS INCORRECTOS
            echo '<!DOCTYPE html>';

            echo '<html lang="es">';

            echo '<head>';

            echo '<meta charset="UTF-8">';

            echo '<title>Error de inicio de sesión</title>';

            echo '</head>';

            echo '<body>';

            echo '<h1>Datos incorrectos</h1>';

            echo '<p>';
            echo 'El nombre de usuario o la contraseña son incorrectos.';
            echo '</p>';

            echo '<a href="login.html">';
            echo 'Volver al inicio de sesión';
            echo '</a>';

            echo '</body>';

            echo '</html>';
        }

    } catch (PDOException $e) {

        echo '<h1>Error de conexión</h1>';

        echo '<p>';
        echo 'No se pudo realizar el inicio de sesión.';
        echo '</p>';

        echo '<p>';
        echo $e->getMessage();
        echo '</p>';
    }

}

?>

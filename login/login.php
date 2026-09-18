
<?php



error_reporting(E_ALL);
ini_set('display_errors', 1);



// Mostrar errores durante el desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
session_start();

// Conexión con la base de datos
require_once __DIR__ . '/../database/consultas/conexion.php';



// PROCESAR LOGIN


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recibir datos del formulario
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    // Comprobar campos vacíos
    if ($nombre_usuario === '' || $password === '') {

        echo '<h1>Error</h1>';
        echo '<p>Debe completar todos los campos.</p>';
        echo '<a href="login.php">Volver al login</a>';

        exit;
    }

    try {

        // Buscar usuario en la base de datos
        $sql = "SELECT *
                FROM usuarios
                WHERE nombre_usuario = :nombre_usuario";

        $consulta = $pdo->prepare($sql);

        $consulta->bindValue(
            ':nombre_usuario',
            $nombre_usuario,
            PDO::PARAM_STR
        );

        $consulta->execute();

        // Obtener usuario
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);


        
        // COMPROBAR USUARIO Y CONTRASEÑA
        

        if (
            $usuario &&
            isset($usuario['password']) &&
            password_verify($password, $usuario['password'])
        ) {

            // Crear sesión del usuario
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['apellido'] = $usuario['apellido'];
            $_SESSION['id_rol'] = $usuario['id_rol'];

            // Ir a inicio.php
            header('Location: inicio.php');
            exit;

        } else {

            // Datos incorrectos
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

            exit;
        }

    } catch (PDOException $e) {

        // Error de conexión o consulta
        echo '<h1>Error de conexión</h1>';

        echo '<p>';
        echo 'No se pudo realizar el inicio de sesión.';
        echo '</p>';

        echo '<p>';
        echo $e->getMessage();
        echo '</p>';

        echo '<a href="login.html">';
        echo 'Volver al login';
        echo '</a>';

        exit;
    }
}

?>

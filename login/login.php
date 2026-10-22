<?php

// CONEXIÓN CON LA BASE DE DATOS
require_once '../database/consultas/conexion.php';
include("../database/Clases/Estudiante.php");
require_once '../Mailer/enviarCorreo.php';

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
        echo '<a href="login.php">Volver al login</a>';

        exit;
    }

    try {

        // BUSCAR EL USUARIO EN LA BASE DE DATOS
        $sql = "SELECT
                    nombre_usuario,
                    password,
                    estado,
                    correo,
                    correo_iv,
                    correo_tag
                FROM usuarios
                WHERE nombre_usuario = :nombre_usuario
                LIMIT 1";

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

            // Comprobar que la cuenta esté habilitada.
            // Adaptar esta condición al valor real de tu BD.
            if ($usuario['estado'] !== 'Activo') {
                exit('La cuenta no está habilitada.');
            }

            // Generar un código aleatorio de cuatro cifras.
            $codigo = (string) random_int(1000, 9999);

            // Preparar una sesión temporal.
            session_regenerate_id(true);

            unset(
                $_SESSION['usuario'],
                $_SESSION['tipo_usuario'],
                $_SESSION['verificacion']
            );

            $_SESSION['verificacion'] = [
                'nombre_usuario' => $usuario['nombre_usuario'],
                'codigo_hash' => password_hash($codigo, PASSWORD_DEFAULT),
                'expira' => time() + 600,
                'intentos' => 0
            ];

            // Datos del correo
            $destinatario = decrypt_aes_256_gcm($usuario['correo'], $key, $usuario['correo_iv'], $usuario['correo_tag']);
            $asunto = 'Prueba de correo - INSOFT';
            $mensaje = plantillaCorreo('Código de verificación para iniciar sesión | INADI',
            'Hemos recibido una solicitud para iniciar sesión en tu cuenta de INADI.
            Para completar el proceso de autenticación, ingresá el siguiente código de verificación: <strong>' . $codigo .'</strong>', 
            'Si no intentaste iniciar sesión, podés ignorar este mensaje. Por seguridad, no compartas el código con nadie.');

            // Enviar correo
            $resultado = enviarCorreo(
                $destinatario,
                $asunto,
                $mensaje
            );

            // COMPROBAR SI EL CORREO SE ENVIÓ
            if (!$resultado) {

                unset($_SESSION['verificacion']);

                error_log('No se pudo enviar el código de verificación.');

                header('Location: loginn.php');
                exit;
            }

            // REDIRIGIR A LA VERIFICACIÓN
            header('Location: verificarCorreo.php');
            exit;
        }else {

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

            echo '<a href="loginn.php">';
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

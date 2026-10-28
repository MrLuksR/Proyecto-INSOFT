<?php

// CONEXIÓN CON LA BASE DE DATOS
require_once '../database/consultas/conexion.php';
include("../database/Clases/Estudiante.php");
require_once '../Mailer/enviarCorreo.php';

// INICIAR SESIÓN
session_start();

// REDIRIGIR AL LOGIN CON UN MENSAJE DE ERROR
function mostrarErrorLogin($tipo, $nombreUsuario = '')
{
    $parametros = http_build_query([
        'error' => $tipo,
        'usuario' => $nombreUsuario
    ]);

    header('Location: loginn.php?' . $parametros);
    exit;
}

// COMPROBAR SI SE ENVIÓ EL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // RECIBIR LOS DATOS
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    // COMPROBAR QUE LOS CAMPOS NO ESTÉN VACÍOS
    if ($nombre_usuario === '' || $password === '') {

        mostrarErrorLogin('campos', $nombre_usuario);

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

            // COMPROBAR QUE LA CUENTA ESTÉ HABILITADA
            if ($usuario['estado'] !== 'Activo') {

                mostrarErrorLogin('estado', $nombre_usuario);

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
            $asunto = 'Código de Verificación - INADI';
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

                mostrarErrorLogin('envio', $nombre_usuario);
            }

            // REDIRIGIR A LA VERIFICACIÓN
            header('Location: verificarCorreo.php');
            exit;
        } else {

            // NOMBRE DE USUARIO O CONTRASEÑA INCORRECTOS
            mostrarErrorLogin('credenciales', $nombre_usuario);

        }

    } catch (Throwable $e) {

        // GUARDAR EL ERROR REAL EN EL REGISTRO DEL SERVIDOR
        error_log('Error de inicio de sesión: ' . $e->getMessage());

        // MOSTRAR UN MENSAJE GENERAL AL USUARIO
        mostrarErrorLogin('error', $nombre_usuario);

    }

}

?>
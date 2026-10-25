<?php

// CONEXIÓN CON LA BASE DE DATOS
require_once '../database/consultas/conexion.php';
include("../database/Clases/Estudiante.php");

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
                    u.nombre_usuario,
                    u.nombre,
                    u.apellido,
                    u.cedula,
                    u.cedula_iv,
                    u.cedula_tag,
                    u.correo,
                    u.correo_iv,
                    u.correo_tag,
                    u.password,
                    u.estado,

                    e.telefono,
                    e.telefono_iv,
                    e.telefono_tag,

                    e.fecha_nacimiento,
                    e.fecha_nacimiento_iv,
                    e.fecha_nacimiento_tag,
                    e.foto,

                    CASE
                        WHEN a.nombre_usuario IS NOT NULL
                            THEN 'administrativo'
                        WHEN d.nombre_usuario IS NOT NULL
                            THEN 'docente'
                        WHEN e.nombre_usuario IS NOT NULL
                            THEN 'estudiante'
                        ELSE 'sin_perfil'
                    END AS tipo_usuario

                FROM usuarios AS u

                LEFT JOIN estudiantes AS e
                    ON u.nombre_usuario = e.nombre_usuario

                LEFT JOIN docentes AS d
                    ON u.nombre_usuario = d.nombre_usuario

                LEFT JOIN administrativos AS a
                    ON u.nombre_usuario = a.nombre_usuario

                WHERE u.nombre_usuario = :nombre_usuario
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

            // Regenerar el identificador para evitar fijación de sesión.
            session_regenerate_id(true);

            // Guardar datos comunes para que el inicio actual pueda reconocer la sesión.
            $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'] ?? '';
            $_SESSION['apellido'] = $usuario['apellido'] ?? '';

            // CREAR LA SESIÓN DEL USUARIO
            $tipo = $usuario['tipo_usuario'];

            switch($tipo){
                case 'estudiante':
                    $_SESSION['usuario'] = new Estudiante(
                        $usuario['nombre_usuario'], $usuario['nombre'], $usuario['apellido'],
                        $usuario['cedula'], $usuario['cedula_iv'], $usuario['cedula_tag'],
                        $usuario['correo'], $usuario['correo_iv'], $usuario['correo_tag'],
                        $usuario['password'], $usuario['estado'],
                        $usuario['telefono'], $usuario['telefono_iv'], $usuario['telefono_tag'],
                        $usuario['fecha_nacimiento'], $usuario['fecha_nacimiento_iv'], $usuario['fecha_nacimiento_tag'],
                        $usuario['foto']
                    );
                    $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
                    break;
                case 'docente':
                case 'administrativo':
                    // Guardar el rol para que otras páginas puedan decidir el acceso.
                    $_SESSION['tipo_usuario'] = $tipo;
                    break;
                default:
                    session_unset();
                    session_destroy();
                    header('Location: loginn.php?error=perfil');
                    exit;
            }

            // Cada rol ingresa a su espacio correspondiente.
            if ($tipo === 'estudiante') {
                header('Location: ../Inicio/inicioestudiante.php');
            } elseif ($tipo === 'docente') {
                header('Location: ../Inicio/inicioindex.php');
            } else {
                header('Location: ../Admin/login.php');
            }
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

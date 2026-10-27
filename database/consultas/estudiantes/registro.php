<?php
session_start();
// Se selecciona el algoritmo Argon2id como medida de protección para contraseñas
// Se selecciona el algoritmo AES-256-GCM de OpenSSL para cifrado de datos personales
// CONEXIÓN CON LA BASE DE DATOS
require_once '../conexion.php';
require_once '../encriptacion.php';
require_once 'getFoto.php';
include("../../Clases/Estudiante.php");
include("../../../auth/config.php");

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
    // RECIBIR LOS DATOS DEL FORMULARIO
    $username = $_POST['nombre_usuario'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cedula = encrypt_aes_256_gcm($_POST['cedula'], $key); // Cifrado con clave .env
    $correo = encrypt_aes_256_gcm($_POST['correo'], $key);
    $telefono = encrypt_aes_256_gcm($_POST['telefono'], $key);
    $password = password_hash($_POST['password'], PASSWORD_ARGON2ID); // Contraseña cifrada (No es recuperable)
    $fecha = encrypt_aes_256_gcm($_POST['fecha'], $key);
    // EL ESTADO SERÁ ACTIVO COMO PRUEBA, LUEGO SE VERIFICARÁ MEDIANTE CORREO
    $estado = "Activo";

    // Inicializar Archivo
    $archivo = NULL;

    // Validar si ya existe el nombre de usuario
    if (getNombreUsuariosEstudiantes($pdo, $username)) {
        volverAlRegistro("El nombre de usuario ya existe. Por favor elige otro.", $username, $nombre, $apellido);
        exit;
    }

    $pattern = '/^[a-zA-Z0-9_]+$/'; // Regex del nombre de usuario, permite solo numeros, letras y guión bajo

    if (!preg_match($pattern, $username) || strlen($username) < 5){
        volverAlRegistro("El nombre de usuario debe tener al menos 5 caracteres y contener mayúsculas, minúsculas, números o guiones bajos (_).", $username, $nombre, $apellido);
        exit;
    }


    // Procesar fotografía
    $archivo = getFoto($username);

    // OBTENER CURSO SELECCIONADO
    $nombreCurso = trim($_POST['curso'] ?? '');

    // COMPROBAR QUE SE SELECCIONÓ UN CURSO
    if ($nombreCurso === '') {
        volverAlRegistro(
            "Debés seleccionar un curso.",
            $username,
            $nombre,
            $apellido
        );
        exit;
    }

    // BUSCAR EL IDENTIFICADOR DEL CURSO
    $sqlCurso = "SELECT id_curso
                FROM curso
                WHERE nombre = :nombre
                AND estado = 'Activo'
                LIMIT 1";

    $consultaCurso = $pdo->prepare($sqlCurso);

    $consultaCurso->execute([
        ':nombre' => $nombreCurso
    ]);

    $idCurso = $consultaCurso->fetchColumn();

    // COMPROBAR QUE EL CURSO EXISTA
    if ($idCurso === false) {
        volverAlRegistro(
            "El curso seleccionado no existe o no está disponible.",
            $username,
            $nombre,
            $apellido
        );
        exit;
    }

    $idCurso = (int) $idCurso;

    $estudiante = new Estudiante(
        $username, $nombre, $apellido,
        $cedula['ciphertext'], $cedula['iv'], $cedula['tag'],
        $correo['ciphertext'], $correo['iv'], $correo['tag'],
        $password, $estado,
        $telefono['ciphertext'], $telefono['iv'], $telefono['tag'],
        $fecha['ciphertext'], $fecha['iv'], $fecha['tag'],
        $archivo
    );

    if ($estudiante->registrar($pdo, $idCurso)) {
        // REGISTRO EXITOSO
        mostrarResultadoRegistro(
            "¡Registro completado!",
            "Tu cuenta fue creada correctamente y tu solicitud de inscripción al curso seleccionado ha sido registrada. La matrícula permanecerá pendiente hasta que se confirme el pago correspondiente. Ya podés iniciar sesión para consultar la información de tu cuenta.",
            "exito"
        );
    } else {

        // MENSAJE GENÉRICO PARA EL USUARIO
        $_SESSION['error_usuario'] =
            "No pudimos completar tu registro. Por favor, intentá nuevamente más tarde.";

        // CONSERVAR LOS DATOS DEL FORMULARIO
        $_SESSION['formulario'] = [
            'nombre_usuario' => $username,
            'nombre'         => $nombre,
            'apellido'       => $apellido,
            'cedula'         => $_POST['cedula'] ?? '',
            'correo'         => $_POST['correo'] ?? '',
            'telefono'       => $_POST['telefono'] ?? '',
            'fecha'          => $_POST['fecha'] ?? '',
            'curso'          => $_POST['curso'] ?? ''
        ];

        // VOLVER AL FORMULARIO
        header("Location: ../../../registro/index.php");
        exit;
    }

} else {

    echo "<h1>Error</h1>";
    echo "<p>Reenvio del Formulario</p>";
    
    // Espera 2 segundos antes de redirigir al formulario de registro
    header("Refresh: 1; URL=../../../registro/index.php");
    exit;

}

function volverAlRegistro($mensaje, $username, $nombre, $apellido){
    $_SESSION['error_usuario'] = $mensaje;

    $_SESSION['formulario'] = [
        'nombre_usuario' => $username,
        'nombre'         => $nombre,
        'apellido'       => $apellido,
        'cedula'         => $_POST['cedula'] ?? '',
        'correo'         => $_POST['correo'] ?? '',
        'telefono'       => $_POST['telefono'] ?? '',
        'fecha'          => $_POST['fecha'] ?? ''
    ];

    header("Location: ../../../registro/index.php");
}

// MOSTRAR RESULTADO DEL REGISTRO
function mostrarResultadoRegistro($titulo, $mensaje, $tipo = 'exito'){
    // CONFIGURACIÓN DEL MENSAJE
    $tipos = [
        'exito' => [
            'icono' => '✓',
            'color' => '#17683a',
            'fondo' => '#ecfdf3',
            'borde' => '#c8eed6'
        ],
        'error' => [
            'icono' => '!',
            'color' => '#a32222',
            'fondo' => '#fff0f0',
            'borde' => '#f2cccc'
        ]
    ];

    $config = $tipos[$tipo] ?? $tipos['error'];

    // PROTEGER CONTENIDO
    $titulo = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
    $mensaje = htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');

    $icono = $config['icono'];
    $color = $config['color'];
    $fondo = $config['fondo'];
    $borde = $config['borde'];

    // RUTA DE INICIO DE SESIÓN
    $rutaLogin = '../../../login/loginn.php';

    echo '
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>'. $titulo .' | INADI</title>

        <style>
            /* RESET GENERAL */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            /* FONDO DE LA PÁGINA */
            html {
                min-height: 100%;
            }

            body {
                min-height: 100vh;
                width: 100%;
                padding: 30px 16px;

                display: flex;
                justify-content: center;
                align-items: center;

                background: #f3f7ff;
                color: #17365e;
                font-family: Arial, Helvetica, sans-serif;
            }

            /* CONTENEDOR */
            .resultado-contenedor {
                width: 100%;
                max-width: 440px;
                margin: auto;
            }

            /* TARJETA */
            .resultado {
                width: 100%;
                padding: 35px 30px;

                background: #ffffff;
                border: 1px solid #dce5f3;
                border-radius: 20px;

                box-shadow: 0 12px 35px rgba(25, 65, 120, 0.10);

                text-align: center;
            }

            /* ICONO DE RESULTADO */
            .resultado-icono {
                width: 76px;
                height: 76px;
                margin: 0 auto 25px;

                display: flex;
                justify-content: center;
                align-items: center;

                background: {$fondo};
                color: {$color};

                border: 1px solid {$borde};
                border-radius: 50%;

                font-size: 36px;
                font-weight: bold;
            }

            /* ETIQUETA INSTITUCIONAL */
            .etiqueta {
                display: inline-block;
                margin-bottom: 15px;
                padding: 6px 13px;

                background: #eaf2ff;
                color: #1764b5;

                border-radius: 20px;

                font-size: 11px;
                font-weight: bold;
                letter-spacing: 1.5px;
                text-transform: uppercase;
            }

            /* TÍTULO */
            .resultado h1 {
                margin-bottom: 15px;

                color: #10284c;
                font-size: 28px;
                line-height: 1.3;
            }

            /* DESCRIPCIÓN */
            .descripcion {
                color: #64748b;
                font-size: 14px;
                line-height: 1.8;
            }

            /* AVISO */
            .aviso {
                margin-top: 25px;
                padding: 14px 16px;

                background: {$fondo};
                color: {$color};

                border: 1px solid {$borde};
                border-radius: 10px;

                font-size: 13px;
                line-height: 1.6;
            }

            /* BOTÓN */
            .boton {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 10px;

                width: 100%;
                min-height: 48px;
                margin-top: 25px;
                padding: 13px 18px;

                background: #1769c2;
                color: #ffffff;

                border: none;
                border-radius: 30px;

                font-family: inherit;
                font-size: 13px;
                font-weight: bold;
                letter-spacing: 0.5px;
                text-decoration: none;

                cursor: pointer;
                transition: background 0.2s, transform 0.2s;
            }

            .boton:hover {
                background: #1055a0;
                transform: translateY(-1px);
            }

            .boton:focus-visible {
                outline: 3px solid #9bc8ff;
                outline-offset: 3px;
            }

            /* PIE */
            .pie {
                margin-top: 25px;
                padding-top: 18px;

                border-top: 1px solid #edf1f7;

                color: #8492a6;
                font-size: 12px;
                line-height: 1.6;
            }

            /* CELULARES */
            @media (max-width: 480px) {
                body {
                    padding: 16px 12px;
                }

                .resultado {
                    padding: 30px 22px;
                    border-radius: 16px;
                }

                .resultado h1 {
                    font-size: 25px;
                }

                .descripcion {
                    font-size: 13px;
                }
            }
        </style>
    </head>

    <body>

        <main class="resultado-contenedor">

            <section class="resultado">

                <!-- ICONO -->
                <div class="resultado-icono">
                    '. $icono .'
                </div>

                <!-- ETIQUETA -->
                <span class="etiqueta">
                    Instituto Nacional de Informática
                </span>

                <!-- TÍTULO -->
                <h1>'. $titulo .'</h1>

                <!-- MENSAJE -->
                <p class="descripcion">
                    '. $mensaje .'
                </p>

                <!-- AVISO -->
                <div class="aviso">
                    Tu solicitud será procesada de acuerdo
                    con los procedimientos de la institución.
                </div>

                <!-- BOTÓN -->
                <a href="' . $rutaLogin. '" class="boton">
                    Iniciar sesión
                    <span aria-hidden="true"></span>
                </a>

                <!-- PIE -->
                <div class="pie">
                    INADI · Sistema de registro de estudiantes
                </div>

            </section>

        </main>

    </body>
    </html>
    ';

    exit;
}
?>
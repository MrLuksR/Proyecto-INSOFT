<?php

/**
 * INSOFT - REGISTRO DE ESTUDIANTES
 *
 * Permite:
 * - Registro público.
 * - Registro desde el panel administrativo.
 *
 * Reutiliza:
 * - Cifrado AES-256-GCM.
 * - Contraseñas Argon2id.
 * - Estudiante::registrar().
 * - Matrícula al curso seleccionado.
 */

// =====================================================
// 1. INICIAR SESIÓN
// =====================================================


// =====================================================
// 2. DEPENDENCIAS
// =====================================================

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../encriptacion.php';
require_once __DIR__ . '/getFoto.php';
require_once __DIR__ . '/../../Clases/Estudiante.php';
require_once __DIR__ . '/../../Clases/Administrativo.php';
require_once __DIR__ . '/../../../auth/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// =====================================================
// 3. CONFIGURACIÓN DE RUTAS
// =====================================================

$rutaRegistroPublico = '../../../registro/index.php';

// AJUSTAR SI TU PANEL UTILIZA OTRA RUTA
$rutaPanelAdmin = '../../../Admin/index.php?page=estudiantes';


// =====================================================
// 4. IDENTIFICAR ORIGEN
// =====================================================

// EL FORMULARIO INDICA SI PROVIENE DEL ADMINISTRADOR.
// ESTE DATO NO CONCEDE PERMISOS POR SÍ MISMO.

$registroAdmin = (
    ($_POST['registro_admin'] ?? '') === '1'
);


// =====================================================
// 5. COMPROBAR MÉTODO
// =====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: ' . $rutaRegistroPublico);
    exit;
}


// =====================================================
// 6. PROTEGER REGISTRO ADMINISTRATIVO
// =====================================================

if ($registroAdmin) {

    $tipoUsuario = strtolower(
        trim((string) ($_SESSION['tipo_usuario'] ?? ''))
    );

    $administradorAutenticado = (
        isset($_SESSION['usuario']) &&
        $_SESSION['usuario'] instanceof Administrativo &&
        $tipoUsuario === 'administrativo'
    );

    if (!$administradorAutenticado) {

        http_response_code(403);
        exit('No tenés autorización para registrar estudiantes desde el administrador.');
    }

    // VALIDAR TOKEN CSRF
    $tokenSesion = $_SESSION['csrf_registro_admin'] ?? '';
    $tokenFormulario = $_POST['csrf_token'] ?? '';

    if (
        !is_string($tokenSesion) ||
        !is_string($tokenFormulario) ||
        $tokenSesion === '' ||
        !hash_equals($tokenSesion, $tokenFormulario)
    ) {

        http_response_code(403);
        exit('La solicitud no es válida. Actualizá el panel e intentá nuevamente.');
    }

}


// =====================================================
// 7. FUNCIÓN PARA CONSERVAR DATOS
// =====================================================

function obtenerDatosFormularioRegistro(): array
{
    return [
        'nombre_usuario' => trim((string) ($_POST['nombre_usuario'] ?? '')),
        'nombre'         => trim((string) ($_POST['nombre'] ?? '')),
        'apellido'       => trim((string) ($_POST['apellido'] ?? '')),
        'cedula'         => trim((string) ($_POST['cedula'] ?? '')),
        'correo'         => trim((string) ($_POST['correo'] ?? '')),
        'telefono'       => trim((string) ($_POST['telefono'] ?? '')),
        'fecha'          => trim((string) ($_POST['fecha'] ?? '')),
        'curso'          => trim((string) ($_POST['curso'] ?? ''))
    ];
}


// =====================================================
// 8. FUNCIÓN PARA VOLVER AL FORMULARIO
// =====================================================

function volverAlRegistro(
    string $mensaje,
    string $username = '',
    string $nombre = '',
    string $apellido = ''
): void {

    global $registroAdmin;
    global $rutaRegistroPublico;
    global $rutaPanelAdmin;

    $datos = obtenerDatosFormularioRegistro();

    if ($registroAdmin) {

        $_SESSION['admin_error_registro'] = $mensaje;
        $_SESSION['admin_datos_registro'] = $datos;

        header('Location: ' . $rutaPanelAdmin);
        exit;
    }

    $_SESSION['error_usuario'] = $mensaje;
    $_SESSION['formulario'] = $datos;

    header('Location: ' . $rutaRegistroPublico);
    exit;
}


// =====================================================
// 9. RECIBIR DATOS
// =====================================================

$datos = obtenerDatosFormularioRegistro();

$username = $datos['nombre_usuario'];
$nombre = $datos['nombre'];
$apellido = $datos['apellido'];
$cedulaOriginal = $datos['cedula'];
$correoOriginal = $datos['correo'];
$telefonoOriginal = $datos['telefono'];
$fechaOriginal = $datos['fecha'];
$nombreCurso = $datos['curso'];

$passwordOriginal = (string) ($_POST['password'] ?? '');


// =====================================================
// 10. VALIDACIONES GENERALES
// =====================================================

if (
    $username === '' ||
    $nombre === '' ||
    $apellido === '' ||
    $cedulaOriginal === '' ||
    $correoOriginal === '' ||
    $fechaOriginal === '' ||
    $passwordOriginal === ''
) {

    volverAlRegistro(
        'Completá todos los campos obligatorios.'
    );
}


// =====================================================
// 11. VALIDAR NOMBRE DE USUARIO
// =====================================================

if (
    !preg_match('/^[a-zA-Z0-9_]{5,60}$/', $username)
) {

    volverAlRegistro(
        'El nombre de usuario debe tener entre 5 y 60 caracteres y contener solamente letras, números o guiones bajos.'
    );
}


// =====================================================
// 12. COMPROBAR USUARIO EXISTENTE
// =====================================================

try {

    if (getNombreUsuariosEstudiantes($pdo, $username)) {

        volverAlRegistro(
            'El nombre de usuario ya existe. Elegí otro.'
        );
    }

} catch (Throwable $e) {

    error_log(
        '[INSOFT][Registro] Error al comprobar usuario: '
        . $e->getMessage()
    );

    volverAlRegistro(
        'No se pudo comprobar la disponibilidad del usuario.'
    );
}


// =====================================================
// 13. VALIDAR CORREO
// =====================================================

if (!filter_var($correoOriginal, FILTER_VALIDATE_EMAIL)) {

    volverAlRegistro(
        'Ingresá un correo electrónico válido.'
    );
}


// =====================================================
// 14. VALIDAR CONTRASEÑA
// =====================================================

if (strlen($passwordOriginal) < 8) {

    volverAlRegistro(
        'La contraseña debe contener al menos 8 caracteres.'
    );
}


// =====================================================
// 15. VALIDAR FECHA
// =====================================================

$fechaNacimiento = DateTimeImmutable::createFromFormat(
    '!d/m/Y',
    $fechaOriginal
);

$erroresFecha = DateTimeImmutable::getLastErrors();

if (
    !$fechaNacimiento ||
    (
        $erroresFecha !== false &&
        (
            $erroresFecha['warning_count'] > 0 ||
            $erroresFecha['error_count'] > 0
        )
    ) ||
    $fechaNacimiento > new DateTimeImmutable('today')
) {

    volverAlRegistro(
        'Ingresá una fecha de nacimiento válida.'
    );
}


// =====================================================
// 16. VALIDAR CURSO
// =====================================================

if ($nombreCurso === '') {

    volverAlRegistro(
        'Debés seleccionar un curso.'
    );
}


// =====================================================
// 17. BUSCAR CURSO ACTIVO
// =====================================================

try {

    $sqlCurso = "
        SELECT id_curso
        FROM curso
        WHERE nombre = :nombre
        AND estado = :estado
        LIMIT 1
    ";

    $consultaCurso = $pdo->prepare($sqlCurso);

    $consultaCurso->execute([
        ':nombre' => $nombreCurso,
        ':estado' => 'Activo'
    ]);

    $idCurso = $consultaCurso->fetchColumn();

    if ($idCurso === false) {

        volverAlRegistro(
            'El curso seleccionado no existe o no está disponible.'
        );
    }

    $idCurso = (int) $idCurso;

} catch (Throwable $e) {

    error_log(
        '[INSOFT][Registro] Error al consultar curso: '
        . $e->getMessage()
    );

    volverAlRegistro(
        'No se pudo comprobar el curso seleccionado.'
    );
}


// =====================================================
// 18. CIFRAR INFORMACIÓN PERSONAL
// =====================================================

try {

    $cedula = encrypt_aes_256_gcm(
        $cedulaOriginal,
        $key
    );

    $correo = encrypt_aes_256_gcm(
        $correoOriginal,
        $key
    );

    $telefono = encrypt_aes_256_gcm(
        $telefonoOriginal,
        $key
    );

    $fecha = encrypt_aes_256_gcm(
        $fechaOriginal,
        $key
    );

    // GENERAR HASH DE CONTRASEÑA
    $password = password_hash(
        $passwordOriginal,
        PASSWORD_ARGON2ID
    );

    // ESTADO INICIAL
    $estado = 'Activo';

} catch (Throwable $e) {

    error_log(
        '[INSOFT][Registro] Error al proteger datos: '
        . $e->getMessage()
    );

    volverAlRegistro(
        'No se pudo procesar la información del estudiante.'
    );
}


// =====================================================
// 19. PROCESAR FOTOGRAFÍA
// =====================================================

try {

    $archivo = getFoto($username);

} catch (Throwable $e) {

    error_log(
        '[INSOFT][Registro] Error al procesar fotografía: '
        . $e->getMessage()
    );

    volverAlRegistro(
        'No se pudo procesar la fotografía de perfil.'
    );
}


// =====================================================
// 20. CREAR OBJETO ESTUDIANTE
// =====================================================

$estudiante = new Estudiante(
    $username,
    $nombre,
    $apellido,

    $cedula['ciphertext'],
    $cedula['iv'],
    $cedula['tag'],

    $correo['ciphertext'],
    $correo['iv'],
    $correo['tag'],

    $password,
    $estado,

    $telefono['ciphertext'],
    $telefono['iv'],
    $telefono['tag'],

    $fecha['ciphertext'],
    $fecha['iv'],
    $fecha['tag'],

    $archivo
);


// =====================================================
// 21. REGISTRAR ESTUDIANTE
// =====================================================

try {

    $registrado = $estudiante->registrar(
        $pdo,
        $idCurso
    );

    if (!$registrado) {

        volverAlRegistro(
            'No se pudo completar el registro del estudiante.'
        );
    }

} catch (Throwable $e) {

    error_log(
        '[INSOFT][Registro] Error al registrar estudiante: '
        . $e->getMessage()
    );

    volverAlRegistro(
        'Ocurrió un error al registrar al estudiante.'
    );
}


// =====================================================
// 22. REGISTRO ADMINISTRATIVO EXITOSO
// =====================================================

if ($registroAdmin) {

    $_SESSION['admin_registro_exitoso'] =
        'El estudiante fue registrado correctamente.';

    unset($_SESSION['admin_datos_registro']);

    // RENOVAR TOKEN CSRF
    $_SESSION['csrf_registro_admin'] =
        bin2hex(random_bytes(32));

    header('Location: ' . $rutaPanelAdmin);
    exit;
}


// =====================================================
// 23. REGISTRO PÚBLICO EXITOSO
// =====================================================

mostrarResultadoRegistro(
    '¡Registro completado!',
    'Tu cuenta fue creada correctamente y tu solicitud de inscripción al curso seleccionado ha sido registrada. La matrícula permanecerá pendiente hasta que se confirme el pago correspondiente. Ya podés iniciar sesión para consultar la información de tu cuenta.',
    'exito'
);


// =====================================================
// CONSERVAR DEBAJO TU FUNCIÓN mostrarResultadoRegistro()
// =====================================================
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
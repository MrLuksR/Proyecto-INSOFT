
<?php

// CONEXIÓN CON LA BASE DE DATOS
require_once '../database/consultas/conexion.php';
require_once '../database/Clases/Estudiante.php';
require_once '../database/Clases/Administrativo.php';
require_once '../Mailer/enviarCorreo.php';

// INICIAR SESIÓN
session_start();

// FUNCIÓN PARA REDIRIGIR CON UN MENSAJE DE ERROR
function mostrarError($mensaje)
{
    $_SESSION['error_verificacion'] = $mensaje;

    header('Location: verificarCorreo.php');
    exit;
}

// COMPROBAR SI SE ENVIÓ EL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: loginn.php');
    exit;
}

// COMPROBAR SI EXISTE UNA VERIFICACIÓN PENDIENTE
if (!isset($_SESSION['verificacion'])) {
    header('Location: loginn.php');
    exit;
}

// OBTENER DATOS DE VERIFICACIÓN
$verificacion = $_SESSION['verificacion'];

// OBTENER LOS CUATRO DÍGITOS
$codigo1 = $_POST['codigo1'] ?? '';
$codigo2 = $_POST['codigo2'] ?? '';
$codigo3 = $_POST['codigo3'] ?? '';
$codigo4 = $_POST['codigo4'] ?? '';

// UNIR LOS DÍGITOS
$codigo = $codigo1 . $codigo2 . $codigo3 . $codigo4;

// COMPROBAR VENCIMIENTO
if (time() > $verificacion['expira']) {

    unset($_SESSION['verificacion']);

    mostrarError(
        'El código de verificación ha vencido. Iniciá sesión nuevamente.'
    );
}

// COMPROBAR LÍMITE DE INTENTOS
if ($verificacion['intentos'] >= 5) {

    unset($_SESSION['verificacion']);

    mostrarError(
        'Superaste el límite de intentos. Solicitá un nuevo código.'
    );
}

// CONTABILIZAR EL INTENTO
$_SESSION['verificacion']['intentos']++;

// COMPROBAR EL CÓDIGO
if (
    !preg_match('/^[0-9]{4}$/', $codigo) ||
    !password_verify($codigo, $verificacion['codigo_hash'])
) {

    // COMPROBAR SI SE AGOTARON LOS INTENTOS
    if ($_SESSION['verificacion']['intentos'] >= 5) {

        unset($_SESSION['verificacion']);

        mostrarError(
            'Superaste el límite de 5 intentos. Iniciá sesión nuevamente.'
        );
    }

    $restantes = 5 - $_SESSION['verificacion']['intentos'];

    mostrarError(
        "El código ingresado es incorrecto. Te quedan $restantes intentos."
    );
}

// CÓDIGO CORRECTO: BUSCAR EL USUARIO
try {

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

                a.nivel_acceso,
                a.fecha_ingreso,
                a.cargo,
                a.area,

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

    $consulta->bindValue(
        ':nombre_usuario',
        $verificacion['nombre_usuario'],
        PDO::PARAM_STR
    );

    $consulta->execute();

    // OBTENER LOS DATOS DEL USUARIO
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    // COMPROBAR QUE EXISTA Y ESTÉ HABILITADO
    if (!$usuario || $usuario['estado'] !== 'Activo') {

        unset($_SESSION['verificacion']);

        mostrarError(
            'La cuenta no existe o no está habilitada.'
        );
    }

    // COMPROBAR EL TIPO DE USUARIO
    $tipo = $usuario['tipo_usuario'];

    // CREAR EL OBJETO SEGÚN EL ROL
    switch ($tipo) {

        case 'estudiante':

            $objetoUsuario = new Estudiante(
                $usuario['nombre_usuario'],
                $usuario['nombre'],
                $usuario['apellido'],

                $usuario['cedula'],
                $usuario['cedula_iv'],
                $usuario['cedula_tag'],

                $usuario['correo'],
                $usuario['correo_iv'],
                $usuario['correo_tag'],

                $usuario['password'],
                $usuario['estado'],

                $usuario['telefono'],
                $usuario['telefono_iv'],
                $usuario['telefono_tag'],

                $usuario['fecha_nacimiento'],
                $usuario['fecha_nacimiento_iv'],
                $usuario['fecha_nacimiento_tag'],

                $usuario['foto']
            );

            break;
        
        case 'administrativo':
            $objetoUsuario = new Administrativo(
                $usuario['nombre_usuario'],
                $usuario['nombre'],
                $usuario['apellido'],
                $usuario['cedula'],
                $usuario['cedula_iv'],
                $usuario['cedula_tag'],
                $usuario['correo'],
                $usuario['correo_iv'],
                $usuario['correo_tag'],
                $usuario['password'],
                $usuario['estado'],
                $usuario['nivel_acceso'],
                $usuario['fecha_ingreso'],
                $usuario['cargo'],
                $usuario['area']
            );

            break;

        // FALTAN LOS DEMÁS ROLES

        default:

            mostrarError(
                'Este tipo de usuario todavía no tiene acceso habilitado.'
            );
    }

    // RENOVAR EL IDENTIFICADOR DE SESIÓN
    session_regenerate_id(true);

    // ELIMINAR LOS DATOS TEMPORALES
    unset(
        $_SESSION['verificacion'],
        $_SESSION['error_verificacion']
    );

    // CREAR LA SESIÓN AUTENTICADA
    $_SESSION['usuario'] = $objetoUsuario;
    $_SESSION['tipo_usuario'] = $tipo;

    // GUARDAR LA SESIÓN ANTES DE ENVIAR EL CORREO
    session_write_close();

} catch (Throwable $e) {

    error_log($e->getMessage());

    mostrarError(
        'Ocurrió un problema al verificar tu cuenta. Intentá nuevamente.'
    );
}


// =====================================================
// ENVIAR NOTIFICACIÓN DE INICIO DE SESIÓN EXITOSO
// =====================================================

try {

    // DESCIFRAR EL CORREO DEL USUARIO
    $destinatario = decrypt_aes_256_gcm(
        $usuario['correo'],
        $key,
        $usuario['correo_iv'],
        $usuario['correo_tag']
    );

    // OBTENER FECHA Y HORA DE URUGUAY
    $fechaActual = new DateTimeImmutable(
        'now',
        new DateTimeZone('America/Montevideo')
    );

    $fecha = $fechaActual->format('d/m/Y');
    $hora = $fechaActual->format('H:i');

    // PREPARAR DATOS DEL MENSAJE
    $nombre = htmlspecialchars(
        $usuario['nombre'],
        ENT_QUOTES,
        'UTF-8'
    );

    $asunto = 'Nuevo inicio de sesión en tu cuenta | INADI';

    // GENERAR EL MENSAJE HTML
    $mensaje = plantillaCorreo(

        'Inicio de sesión exitoso',

        'Hola, <strong>' . $nombre . '</strong>.<br><br>' .

        'Te informamos que se ha iniciado sesión correctamente en tu cuenta del Instituto Nacional de Informática (INADI).' .

        '<br><br><strong>Detalles del acceso:</strong><br>' .

        '<strong>Fecha:</strong> ' . $fecha . '<br>' .

        '<strong>Hora:</strong> ' . $hora . ' (Uruguay)<br><br>' .

        'Si reconocés esta actividad, no necesitás realizar ninguna acción.',

        '¿No reconocés este inicio de sesión? Te recomendamos cambiar tu contraseña y comunicarte con el equipo de soporte de INADI.'
    );

    // ENVIAR EL CORREO
    $resultado = enviarCorreo(
        $destinatario,
        $asunto,
        $mensaje
    );

    // COMPROBAR RESULTADO
    if (!$resultado) {

        error_log(
            'No se pudo enviar la notificación de inicio de sesión.'
        );
    }

} catch (Throwable $e) {

    // EL ERROR DEL CORREO NO INTERRUMPE EL ACCESO
    error_log(
        'Error al enviar notificación: ' .
        $e->getMessage()
    );
}


// =====================================================
// REDIRIGIR AL INICIO
// =====================================================

header('Location: ../Inicio/inicioindex.php');
exit;

?>
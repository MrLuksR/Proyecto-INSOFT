<?php
// Se selecciona el algoritmo Argon2id como medida de protección para contraseñas
// Se selecciona el algoritmo AES-256-GCM de OpenSSL para cifrado de datos personales
// CONEXIÓN CON LA BASE DE DATOS
require_once '../conexion.php';
require_once '../encriptacion.php';
include("../../Clases/Estudiante.php");

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
    // Clave extraida del .env
    //$key = $_ENV['ENCRIPTON_KEY'];
    $key = "d21c2cdfbca3d79e4dc4d31cea91f75a78a872f874d58fae9ad1a7fbcf0b1053";

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
    
    // PROCESAR FOTOGRAFÍA
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === UPLOAD_ERR_OK) {

        $nombreOriginal = $_FILES['foto']['name'];
        $archivoTemporal = $_FILES['foto']['tmp_name'];
        
        // Obtener el texto extra del input POST (y limpiar caracteres raros por seguridad)
        $textoExtra = trim($username);
        $textoExtra = preg_replace('/[^a-zA-Z0-9_-]/', '_', $textoExtra);
        
        // Separar nombre base y extensión de forma segura
        $info = pathinfo($nombreOriginal);
        $extension  = $info['extension'] ?? '';
        
         /* Crear la fotografía junto a su nombre
        de usuario para evitar sobrescritura de nombres*/
        $archivo = "FotoPerfil_" . $textoExtra . "." . $extension;

        // Crear carpeta si no existe

        $carpeta = "fotosEst/";

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $rutaFoto = $carpeta . $archivo;


        // Mover fotografía

        if (move_uploaded_file($archivoTemporal, $rutaFoto)) {
            $foto = $rutaFoto;
        }
    }

    $estudiante = new Estudiante(
        $username, $nombre, $apellido,
        $cedula['ciphertext'], $cedula['iv'], $cedula['tag'],
        $correo['ciphertext'], $correo['iv'], $correo['tag'],
        $password, $estado,
        $telefono['ciphertext'], $telefono['iv'], $telefono['tag'],
        $fecha['ciphertext'], $fecha['iv'], $fecha['tag'],
        $archivo
    );

    if ($estudiante->registrar($pdo)){
        // REGISTRO EXITOSO
        echo "<!DOCTYPE html>";
        echo "<html lang='es'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>Registro exitoso - INADI</title>";
        echo "</head>";

        echo "<body>";

        echo "<h1>¡Registro completado!</h1>";

        echo "<p>Tus datos se completaron correctamente, ya podes iniciar sesión.</p>";

        // Espera 5 segundos antes de redirigir
        header("Refresh: 5; URL=../../../Nosotros/nosotros.php");
        echo "Serás redirigido en 5 segundos...";
        exit;

        echo "</body>";
        echo "</html>";
    }

} else {

    echo "<h1>Error</h1>";
    echo "<p>Reenvio del Formulario</p>";
    
    // Espera 2 segundos antes de redirigir al formulario de registro
    header("Refresh: 2; URL=../../../registro/index.php");
    exit;

}

?>
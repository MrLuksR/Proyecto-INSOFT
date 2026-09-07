<?php
    // Se selecciona el algoritmo Argon2id como medida de protección para contraseñas
    // Se selecciona el algoritmo AES-256-GCM de OpenSSL para cifrado de datos personales
    // Traer la conexión de BD
    include("../../conexion.php");

    
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $ci = $_POST["ci"]; // Sensible
        $nombre = $_POST["nombre"];
        $apellido = $_POST["apellido"];
        $email = $_POST["email"];  // Sensible
        $passWd = $_POST["passwd"]; // Sensible
        $fecNac = $_POST["fecha"]; // Sensible
    }

?>
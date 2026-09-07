<?php
    // Variables de conexión
    $host = "localhost"; // host (por defecto en local)
    $dbName = "insoft"; // nombre de la base de datos
    $user = "root"; // usuario
    $pass = ""; // contraseña del usuario

    try{
        // Conexión a partir de PDO
        $phpConn = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $user, $pass);

        // Muestra un mensaje como conexión
        echo "<div class='conn'>Conexión realizada con éxito</div>";
    }catch(PDOException $e){
        die("<div class='noConn'>Error al conectar con el servidor</div>");
    }
?>
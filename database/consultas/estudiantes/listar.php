<?php
    // Traer la conexión de BD
    include("../../conexion.php");

    // Preparar consulta
    $stmt = $phpConn->prepare("SELECT * FROM usuarios WHERE id_rol = 3;");
    $stmt->execute(); // Ejecutar consulta, devolverá un diccionario.
    $res = $stmt->fetch(PDO::FETCH_ASSOC); // Convierte esto en un array asociativo/diccionario (nombres de las columnas como claves)
    // $res = array(['id'] => 1, ['nombre'] => Juan, ..., etc.)
    
    // Mostrar resultados obtenidos (Preparado para modificar y añadir estilos para mostrar estudiantes registrados)
    foreach ($res as $dato){
        echo "<p>$dato</p>";
    }
?>
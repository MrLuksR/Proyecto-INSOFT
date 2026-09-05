<?php
    // Traer la conexión de BD
    include("../../conexion.php");

    // Preparar consulta
    $consulta = "SELECT id_usuario, nombre_usuario, apellido, email FROM usuarios WHERE id_rol = :rol;";
    $stmt = $phpConn->prepare($consulta);
    $stmt->execute([':rol' => 3]); // Ejecutar consulta, devolverá un diccionario.
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC); // Convierte esto en un array asociativo/diccionario (nombres de las columnas como claves)
    // $res = array(['id'] => 1, ['nombre'] => Juan, ..., etc.)
    
    // Mostrar resultados obtenidos (Preparado para modificar y añadir estilos para mostrar estudiantes registrados)
    foreach ($res as $dato){
        echo $dato["nombre_usuario"];
        echo $dato["apellido"];
        echo $dato["email"];
    }
?>
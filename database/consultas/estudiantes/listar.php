<?php
    // Traer la conexión de BD
    include("../../conexion.php");

    // Preparar consulta
    $consulta = "SELECT nombre_usuario, apellido, email FROM usuarios WHERE id_rol = :rol;";
    $stmt = $phpConn->prepare($consulta);
    $stmt->execute([':rol' => 3]); // Ejecutar consulta, devolverá un diccionario.
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC); // Convierte esto en un array asociativo/diccionario (nombres de las columnas como claves)
    // $res = Array([0] => array(['id'] => 1, ['nombre'] => Juan, ..., etc.) [1] => array(...)...);
    
    // Obtener longitud del array asociativo
    $len = count($res);

    // Recrrer la matriz y mostrar los datos
    for ($i=0 ; $i < $len ; $i++){
        foreach ($res[$i] as $dato => $valor){
            echo "$valor ";
        }
        echo "<br></br>";
    }
?>
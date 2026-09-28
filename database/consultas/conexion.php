<?php

$host = 'localhost';
$usuario = 'root';
$contraseña = '';
$baseDatos = 'insoft';

$dsn = "mysql:host=$host;dbname=$baseDatos;charset=utf8mb4";

try {

    $pdo = new PDO(
        $dsn,
        $usuario,
        $contraseña
    );

    // Mostrar errores de PDO
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Devolver resultados como arrays asociativos
    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {

    die(
        "Error de conexión con la base de datos: "
        . $e->getMessage()
    );
}

?>

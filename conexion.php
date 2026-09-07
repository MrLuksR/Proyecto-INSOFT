<?php

$host = 'localhost';
$usuario = 'root';
$contraseña = '';
$baseDatos = 'insoft';

$dsn = 'mysql:host=' . $host . ';dbname=' . $baseDatos;

try {

    $pdo = new PDO($dsn, $usuario, $contraseña);

    // Mostrar errores de PDO
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Trabajar con los resultados como objetos
    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_OBJ
    );

    // Permitir caracteres como ñ y tildes
    $pdo->exec("SET NAMES utf8");

} catch (PDOException $e) {

    die("Error de conexión con la base de datos: " . $e->getMessage());

}

?>
<?php

session_start();

unset($_SESSION['usuario'], $_SESSION['tipo_usuario']);

session_destroy();

// VOLVER AL LOGIN
header('Location: ../Inicio/inicioindex.php');
exit;

?>

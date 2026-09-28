<?php

session_start();

$_SESSION = [];

session_destroy();

// VOLVER AL LOGIN
header('Location: ../Inicio/inicioindex.php');
exit;

?>

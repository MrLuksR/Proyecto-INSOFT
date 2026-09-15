<?php

// INICIAR SESIÓN
session_start();

// ELIMINAR LOS DATOS DE LA SESIÓN
session_unset();

// DESTRUIR LA SESIÓN
session_destroy();

// VOLVER AL LOGIN
header('Location: login.html');
exit;

?>

<?php

session_start();

require_once '../Mailer/enviarCorreo.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // OBTENER DATOS DEL FORMULARIO
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    // VALIDAR CAMPOS
    if (
        $nombre == '' ||
        !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
        $mensaje == ''
    ) {
        header('Location: contacto.php?consulta=invalida');
        exit;
    }

    // PROTEGER DATOS PARA MOSTRAR EN HTML
    $nombre = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
    $correo = htmlspecialchars($correo, ENT_QUOTES, 'UTF-8');
    $mensaje = nl2br(htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'));

    // PREPARAR CORREO
    $destinatario = 'drawlu2004@gmail.com';
    $asunto = 'Nueva consulta desde la página web';

    $contenido = "
        <strong>Nombre:</strong> $nombre<br>
        <strong>Correo:</strong> $correo<br><br>
        <strong>Consulta:</strong><br>
        $mensaje
    ";

    // UTILIZAR PLANTILLA INSTITUCIONAL
    $cuerpo = plantillaCorreo(
        'Nueva consulta recibida',
        $contenido
    );

    // ENVIAR CORREO
    if (enviarCorreo($destinatario, $asunto, $cuerpo)) {
        header('Location: contacto.php?consulta=exito');
    } else {
        header('Location: contacto.php?consulta=error');
    }

    exit;
}
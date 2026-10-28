<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar PHPMailer
require_once __DIR__ . '/src/Exception.php';
require_once __DIR__ . '/src/PHPMailer.php';
require_once __DIR__ . '/src/SMTP.php';

function enviarCorreo($destinatario, $asunto, $mensaje) {

    $mail = new PHPMailer(true);

    try {

        // CONFIGURACIÓN DEL CLIENTE SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Credenciales SMTP
        $mail->Username = 'inadi.insoft.uy@gmail.com';
        $mail->Password = 'wbcl vszp ijmr oumk';

        // Configuración de seguridad
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Codificación
        $mail->CharSet = 'UTF-8';

        // Remitente
        $mail->setFrom(
            $mail->Username,
            'INADI'
        );

        // Destinatario
        $mail->addAddress($destinatario);

        // Incrustar logotipo institucional
        $mail->addEmbeddedImage(
            __DIR__ . '/../Elementos gráficos/Logo Inadi con Brillo.png',
            'logo_inadi',
            'Logo Inadi sin Brillo.png'
        );

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensaje;
        $mail->AltBody = strip_tags($mensaje);

        // Enviar correo
        $mail->send();

        return true;

    } catch (Exception $e) {

        echo "<h3>Error al enviar el correo:</h3>";

        echo "<pre>";
        echo htmlspecialchars($mail->ErrorInfo);
        echo "</pre>";

        return false;
    }
}

function plantillaCorreo($titulo, $mensaje, $aviso = '') {
    return '
        <div style="background:#f1f5f9; margin:0; padding:0; font-family:Arial;">

        <div style="width:100%; background:white; overflow:hidden;">

            <!-- ENCABEZADO -->
            <div style="background:#45459c; padding:25px; text-align:center;">

                <img src="cid:logo_inadi" width="110" alt="INADI">

                <h2 style="color:white;">
                    Instituto Nacional de Informática
                </h2>

            </div>

            <!-- CONTENIDO -->
            <div style="padding:25px; color:#475569; line-height:1.6;">

                <h2 style="color:#102b4e;">' . $titulo . '</h2>

                <p>' . $mensaje . '</p>

                ' . ($aviso !== '' ? '
                <div style="background:#eaf3fc; padding:15px;
                            border-left:4px solid #2563eb;">
                    ' . $aviso . '
                </div>' : '') . '

                <p style="margin-top:25px;">
                    Atentamente,<br>
                    <strong>Equipo de INADI</strong>
                </p>

            </div>

            <!-- PIE -->
            <div style="background:#36367f; padding:20px;
                        text-align:center; color:white; font-size:12px;">

                Instituto Nacional de Informática<br>
                Mensaje automático del sistema INADI.

            </div>

        </div>
    </div>';
}
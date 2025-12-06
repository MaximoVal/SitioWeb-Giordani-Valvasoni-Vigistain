<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function enviarCorreo($destinatario, $asunto, $cuerpoHTML, $replyTo = null) {
    $mail = new PHPMailer(true);
    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'paseodelafortuna@gmail.com';   
        $mail->Password   = 'lwkf gyjj kobv nxbf';   
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Configuración del correo
        $mail->setFrom('paseodelafortuna@gmail.com', 'Paseo de la Fortuna');
        $mail->addAddress($destinatario);
        
        // Si hay un "Responder a" (para el formulario de contacto)
        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        }

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpoHTML;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false; // Error: $mail->ErrorInfo
    }
}
?>
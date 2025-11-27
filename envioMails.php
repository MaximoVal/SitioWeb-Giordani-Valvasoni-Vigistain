<?php
require_once 'config.php';

class ResendMailer {
    private $apiKey;
    private $apiUrl = 'https://api.resend.com/emails';
    
    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }
    
    public function send($params) {
        
        if (empty($params['to']) || empty($params['subject']) || (empty($params['html']) && empty($params['text']))) {
            throw new Exception("Faltan parámetros obligatorios: to, subject, y html/text");
        }
        
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("Error de conexión: " . $error);
        }
        
        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMsg = isset($errorData['message']) ? $errorData['message'] : $response;
            throw new Exception("Error HTTP " . $httpCode . ": " . $errorMsg);
        }
        
        return json_decode($response, true);
    }
}

function enviarEmail($to, $subject, $html, $from = null) {
    try {
        $mailer = new ResendMailer(RESEND_API_KEY);
        
        $params = [
            'from' => $from ?? RESEND_FROM_EMAIL,
            'to' => $to,
            'subject' => $subject,
            'html' => $html
        ];
        
        $response = $mailer->send($params);
        
        return [
            'success' => true,
            'data' => $response,
            'error' => null
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'data' => null,
            'error' => $e->getMessage()
        ];
    }
}


function enviarEmailContacto($nombre, $email, $asunto, $mensaje) {
    $htmlEmail = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <h2 style='color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px;'>
            Nuevo mensaje de contacto - Paseo de la Fortuna
        </h2>
        <div style='margin: 20px 0;'>
            <p style='margin: 10px 0;'><strong>Nombre:</strong> {$nombre}</p>
            <p style='margin: 10px 0;'><strong>Email:</strong> {$email}</p>
            <p style='margin: 10px 0;'><strong>Asunto:</strong> {$asunto}</p>
        </div>
        <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
            <h3 style='color: #555; margin-top: 0;'>Mensaje:</h3>
            <p style='color: #666; line-height: 1.6; white-space: pre-wrap;'>{$mensaje}</p>
        </div>
        <div style='margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #999;'>
            <p>Este mensaje fue enviado desde el formulario de contacto de Paseo de la Fortuna</p>
            <p>Para responder, envía un email directamente a: {$email}</p>
        </div>
    </div>
    ";
    
    return enviarEmail(RESEND_ADMIN_EMAIL, "Contacto: " . $asunto, $htmlEmail);
}


function enviarEmailBienvenida($nombre, $email) {
    $htmlEmail = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <h2 style='color: #007bff;'>¡Bienvenido a Paseo de la Fortuna, {$nombre}!</h2>
        <p style='font-size: 16px; color: #333; line-height: 1.6;'>
            Gracias por registrarte en nuestro shopping center. Estamos emocionados de tenerte con nosotros.
        </p>
        <div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>
            <h3 style='color: #555;'>¿Qué puedes hacer ahora?</h3>
            <ul style='color: #666;'>
                <li>Explorar nuestras tiendas</li>
                <li>Ver promociones exclusivas</li>
                <li>Reservar espacios comerciales</li>
            </ul>
        </div>
        <p style='color: #666;'>
            Si tienes alguna pregunta, no dudes en contactarnos.
        </p>
    </div>
    ";
    
    return enviarEmail($email, "Bienvenido a Paseo de la Fortuna", $htmlEmail);
}


function enviarEmailNotificacion($email, $asunto, $mensaje) {
    $htmlEmail = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <h2 style='color: #333;'>Paseo de la Fortuna</h2>
        <div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>
            <p style='font-size: 16px; color: #333; line-height: 1.6;'>{$mensaje}</p>
        </div>
        <div style='margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #999;'>
            <p>Este es un mensaje automático de Paseo de la Fortuna</p>
        </div>
    </div>
    ";
    
    return enviarEmail($email, $asunto, $htmlEmail);
}
?>
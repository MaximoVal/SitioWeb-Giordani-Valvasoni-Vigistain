<?php
session_start();

require_once 'mail.php'; 

$mensajeExito = '';
$mensajeError = '';

if(isset($_POST["enviar"])){
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $email = htmlspecialchars(trim($_POST['email']));
    $asunto = htmlspecialchars(trim($_POST['asunto']));
    $mensaje = htmlspecialchars(trim($_POST['mensaje']));
    
    if(!empty($nombre) && !empty($email) && !empty($asunto) && !empty($mensaje)) {
        
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            
            $cuerpoHTML = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
                        .card { background-color: #fff; max-width: 600px; margin: 20px auto; padding: 20px; border-radius: 8px; border: 1px solid #ddd; }
                        h2 { color: #315c3d; border-bottom: 2px solid #DAB561; padding-bottom: 10px; margin-top: 0; }
                        .label { font-weight: bold; color: #555; }
                        .message-box { background-color: #f9f9f9; padding: 15px; border-left: 4px solid #315c3d; margin-top: 10px; white-space: pre-wrap; }
                        .footer { font-size: 12px; color: #888; text-align: center; margin-top: 20px; }
                    </style>
                </head>
                <body>
                    <div class='card'>
                        <h2>Nuevo Mensaje de Contacto</h2>
                        <p><span class='label'>Cliente:</span> $nombre</p>
                        <p><span class='label'>Email:</span> $email</p>
                        <p><span class='label'>Asunto:</span> $asunto</p>
                        <hr style='border: 0; border-top: 1px solid #eee;'>
                        <p class='label'>Mensaje:</p>
                        <div class='message-box'>$mensaje</div>
                        <div class='footer'>
                            Enviado desde el sitio web Paseo de la Fortuna
                        </div>
                    </div>
                </body>
                </html>
            ";

            if (function_exists('enviarCorreo')) {

                $enviado = enviarCorreo('paseodelafortuna@gmail.com', 'Consulta Web: ' . $asunto, $cuerpoHTML, $email);
                
                if($enviado) {
                    $mensajeExito = "¡Mensaje enviado exitosamente! Te responderemos pronto.";
                
                    $nombre = $email = $asunto = $mensaje = ''; 
         
                    if(isset($_POST)) $_POST = array();
                } else {
                    $mensajeError = "Hubo un error al intentar enviar el correo. Verifica tu configuración SMTP.";
                }
            } else {
                $mensajeError = "Error crítico: La función de envío no se encontró.";
            }
            
        } else {
            $mensajeError = "El formato del email no es válido.";
        }
        
    } else {
        $mensajeError = "Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paseo de la Fortuna - Shopping Center</title>
    <link rel="stylesheet" href="../Estilos/estilos.css">
    <link rel="stylesheet" href="../Estilos/contactoEstilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
    :root {
            --color-dorado-fondo: #eac764;
            --color-dorado-btn: #DAB561;
            --color-verde-login: #315c3d;
            --color-foco: #fff4b8; 
        }
        a:focus, button:focus {
            outline: 3px solid var(--color-foco) !important;
            outline-offset: 2px;
            border-radius: 4px;
        }
    </style>

</head>

<body  style="background-image: url('../Footage/Galeria2.png');background-repeat:no-repeat;background-size: cover;">
    <?php 
    
        if(!isset($_SESSION['usuario'])) {
            if(file_exists('navNoRegistrado.php')) include 'navNoRegistrado.php';
        } else if($_SESSION['tipoUsuario'] == 'cliente') {
            if(file_exists('navCliente.php')) include 'navCliente.php';  
        } else if($_SESSION['tipoUsuario'] == 'dueno de local') {
            if(file_exists('navDueno.php')) include 'navDueno.php';  
        } else {
            if(file_exists('navAdmin.php')) include 'navAdmin.php';  
        }
    ?>
    
    <!-- Header -->
    <div class="header-section" style="padding: 60px 0; text-align: center; background-color: rgba(0,0,0,0.5);">
        <div class="container">
            <h1 style="color:white; font-weight: bold;">Contáctanos</h1>
            <p style="color:white; font-size: 1.2rem;">Estamos aquí para ayudarte. Tu opinión y consultas son importantes para nosotros en Paseo de la Fortuna.</p>
        </div>
    </div>

    <!-- Formulario Principal -->
    <div class="container" style="margin-top: -30px; margin-bottom: 50px;">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <?php if(!empty($mensajeExito)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>¡Éxito!</strong> <?php echo $mensajeExito; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($mensajeError)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error:</strong> <?php echo $mensajeError; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card shadow-lg" style="border: none; border-radius: 15px; overflow: hidden;">
                    <div class="card-header text-white text-center py-4" style="background-color: var(--color-verde-login);">
                        <h2 class="mb-0">Envíanos tu mensaje</h2>
                    </div>
                    <div class="card-body p-5" style="background-color: white;">
                        <form action="" method="POST" id="contactForm">
                            <div class="row">
                                <!-- Nombre Completo -->
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" id="nombre" name="nombre" 
                                               value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>"
                                               placeholder="Tu nombre completo" required>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-bold">Correo Electrónico <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                                               placeholder="tu@email.com" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Asunto -->
                            <div class="mb-3">
                                <label for="asunto" class="form-label fw-bold">Asunto <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-chat-left-text"></i></span>
                                    <input type="text" class="form-control" id="asunto" name="asunto" 
                                           value="<?php echo isset($asunto) ? htmlspecialchars($asunto) : ''; ?>"
                                           placeholder="Escribe el asunto de tu mensaje" required>
                                </div>
                            </div>

                            <!-- Mensaje -->
                            <div class="mb-3">
                                <label for="mensaje" class="form-label fw-bold">Mensaje <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="mensaje" name="mensaje" rows="6" 
                                          placeholder="Escribe aquí tu mensaje detallado..." required><?php echo isset($mensaje) ? htmlspecialchars($mensaje) : ''; ?></textarea>
                            </div>
                            
                            <!-- Botón Enviar -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-lg text-white" name="enviar" 
                                        style="background-color: var(--color-dorado-btn); font-weight: bold;">
                                    Enviar Mensaje <i class="bi bi-send-fill ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if(file_exists('footer.php')) include 'footer.php'; ?>
    

</body>
</html>
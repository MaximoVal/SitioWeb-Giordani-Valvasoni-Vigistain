<?php
session_start();
require_once 'envioMails.php';

$mensajeExito = '';
$mensajeError = '';


if(isset($_POST["enviar"])){
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $email = htmlspecialchars(trim($_POST['email']));
    $asunto = htmlspecialchars(trim($_POST['asunto']));
    $mensaje = htmlspecialchars(trim($_POST['mensaje']));
    

    if(!empty($nombre) && !empty($email) && !empty($asunto) && !empty($mensaje)) {
        

        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            

            $resultado = enviarEmailContacto($nombre, $email, $asunto, $mensaje);
            
            if($resultado['success']) {
                $mensajeExito = "¡Mensaje enviado exitosamente! Te responderemos pronto.";
            } else {
                $mensajeError = "Error al enviar el mensaje: " . $resultado['error'];
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body  style="background-image: url('../Footage/Galeria2.png');background-repeat:no-repeat;background-size: cover;">
    <?php 
        if(!isset($_SESSION['usuario'])) {
            include 'navNoRegistrado.php';
        } else if($_SESSION['tipoUsuario'] == 'cliente') {
            include 'navCliente.php';  
        } else if($_SESSION['tipoUsuario'] == 'dueño de local') {
            include 'navDueño.php';  
        } else {
            include 'navAdmin.php';  
        }
    ?>
    
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <h1 style="color:white;">Contáctanos</h1>
            <p style="color:white;">Estamos aquí para ayudarte. Tu opinión y consultas son importantes para nosotros en Paseo de la Fortuna.</p>
        </div>
    </div>

    <!-- Formulario Principal -->
    <div class="container">
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
                
                <div class="form-container">
                    <div class="form-header">
                        <h2>Envíanos tu mensaje</h2>
                    </div>
                    <div class="form-body">
                        <form action="#" method="POST" id="contactForm">
                            <div class="row">
                                <!-- Nombre Completo -->
                                <div class="col-md-6">
                                    <div class="input-group-custom">
                                        <label for="nombre" class="form-label">
                                            Nombre Completo <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control with-icon" id="nombre" name="nombre" 
                                               placeholder="Tu nombre completo" required>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="input-group-custom">
                                        <label for="email" class="form-label">
                                            Correo Electrónico <span class="required">*</span>
                                        </label>
                                        <input type="email" class="form-control with-icon" id="email" name="email" 
                                               placeholder="tu@email.com" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Asunto -->
                            <div class="input-group-custom">
                                <label for="asunto" class="form-label">
                                    Asunto <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control with-icon" id="asunto" name="asunto" 
                                       placeholder="Escribe el asunto de tu mensaje" required>
                            </div>

                            <!-- Mensaje -->
                            <div class="input-group-custom">
                                <label for="mensaje" class="form-label">
                                    Mensaje <span class="required">*</span>
                                </label>
                                <textarea class="form-control" id="mensaje" name="mensaje" rows="6" 
                                          placeholder="Escribe aquí tu mensaje detallado..." required></textarea>
                            </div>
                            
                            <!-- Botón Enviar -->
                            <button type="submit" class="btn-enviar" name="enviar">
                                Enviar Mensaje
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
</body>
</html>
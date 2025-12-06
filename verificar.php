<?php
require 'db.php'; 
$estado = ''; 
$tituloMensaje = '';
$textoMensaje = '';
$botonTexto = '';
$botonLink = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $fecha_actual = date('Y-m-d H:i:s');


    $sql = "SELECT codUsuario FROM usuarios WHERE token_verificacion = ? AND token_expiracion > ? AND verificado != 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $token, $fecha_actual);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
     
        $usuario = $result->fetch_assoc();
        $id = $usuario['codUsuario'];

        $update = "UPDATE usuarios SET verificado = 1, token_verificacion = NULL WHERE codUsuario = ?";
        $stmt_up = $conn->prepare($update);
        $stmt_up->bind_param("i", $id);
        
        if ($stmt_up->execute()) {
    
            $estado = 'exito';
            $tituloMensaje = '¡Cuenta verificada con éxito!';
            $textoMensaje = 'Tu correo electrónico ha sido confirmado correctamente. Ya puedes ingresar a la pagina de Paseo de la Fortuna. ¡Bienvenido!';
            $botonTexto = 'Iniciar Sesión';
            $botonLink = 'login.php';
        } else {
        
            $estado = 'error';
            $tituloMensaje = 'Error del sistema';
            $textoMensaje = 'Ocurrió un problema al intentar activar tu cuenta. Por favor, intenta nuevamente más tarde.';
            $botonTexto = 'Volver al Inicio';
            $botonLink = 'index.php';
        }
    } else {
       
        $estado = 'error';
        $tituloMensaje = 'Token inválido o expirado';
        $textoMensaje = 'El enlace de verificación que has utilizado no es válido, ha caducado o la cuenta ya fue verificada anteriormente.';
        $botonTexto = 'Volver al Inicio';
        $botonLink = 'index.php';
    }
} else {

    $estado = 'warning';
    $tituloMensaje = 'Enlace incompleto';
    $textoMensaje = 'No se proporcionó ningún token de seguridad. Por favor, utiliza el enlace que enviamos a tu correo electrónico.';
    $botonTexto = 'Ir al Inicio';
    $botonLink = 'index.php';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Cuenta - Cafetería Suiza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../Estilos/estilos.css"> <!-- Tu archivo de estilos base -->

    <style>
        :root {
            --color-dorado: #EED284;
            --color-dorado-oscuro: #DAB561;
            --color-verde: #355B38;
            --color-blanco: #FFFFFF;
            --color-fondo: #f4f4f4;
        }

        body {
            background-color: var(--color-fondo);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }

        .card-verificacion {
            background: var(--color-blanco);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            border: none;
            max-width: 500px;
            width: 90%; 
            margin: 20px;
            text-align: center;
        }

        .img-header {
            width: 100%;
            height: 180px;
            object-fit: cover;
            object-position: center;
            border-bottom: 5px solid var(--color-dorado);
        }

        .card-body {
            padding: 3rem 2rem;
        }

        .icon-status {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            display: inline-block;
        }

        .status-exito { color: var(--color-verde); }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }

        h1 {
            color: var(--color-verde);
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        p {
            color: #6c757d;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn-accion {
            background-color: var(--color-dorado);
            color: #333;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            border: none;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: inline-block;
        }

        .btn-accion:hover {
            background-color: var(--color-dorado-oscuro);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            color: #000;
        }
    </style>
</head>
<body>

    <div class="card-verificacion">
   
        <img src="Footage/Portada.png" alt="Cafetería Suiza" class="img-header">
        
        <div class="card-body">
            
            <?php if($estado == 'exito'): ?>
                <i class="bi bi-check-circle-fill icon-status status-exito"></i>
                <h1><?php echo $tituloMensaje; ?></h1>
                <p><?php echo $textoMensaje; ?></p>
                <a href="<?php echo $botonLink; ?>" class="btn-accion">
                    <i class="bi bi-box-arrow-in-right me-2"></i><?php echo $botonTexto; ?>
                </a>

            <?php elseif($estado == 'error'): ?>
                <i class="bi bi-x-circle-fill icon-status status-error"></i>
                <h1 style="color: #dc3545;"><?php echo $tituloMensaje; ?></h1>
                <p><?php echo $textoMensaje; ?></p>
                <a href="<?php echo $botonLink; ?>" class="btn-accion">
                    <i class="bi bi-house-door-fill me-2"></i><?php echo $botonTexto; ?>
                </a>

            <?php else: ?>
                <i class="bi bi-exclamation-triangle-fill icon-status status-warning"></i>
                <h1 style="color: #d39e00;"><?php echo $tituloMensaje; ?></h1>
                <p><?php echo $textoMensaje; ?></p>
                <a href="<?php echo $botonLink; ?>" class="btn-accion">
                    <i class="bi bi-house-door-fill me-2"></i><?php echo $botonTexto; ?>
                </a>
            <?php endif; ?>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const card = document.querySelector('.card-verificacion');
            card.style.opacity = 0;
            card.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.opacity = 1;
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>

</body>
</html>
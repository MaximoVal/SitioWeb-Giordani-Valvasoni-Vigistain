<?php
session_start();
require_once 'db.php';   
require_once 'mail.php'; 

$mensaje = "";
$tipo_alerta = "";

if (isset($_POST['enviar'])) {
    $email = trim($_POST['email']);

    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        
        $sql = "SELECT codUsuario, nombre FROM usuarios WHERE nombreUsuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            
       
            $token = bin2hex(random_bytes(32));
            $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

         
            $update = "UPDATE usuarios SET token_verificacion = ?, token_expiracion = ? WHERE codUsuario = ?";
            $stmt_up = $conn->prepare($update);
            $stmt_up->bind_param("ssi", $token, $expiracion, $usuario['codUsuario']);
            
            if ($stmt_up->execute()) {
 
                $enlace = "http://paseofortuna.free.nf/cambiar_password.php?token=" . $token;
                
                $asunto = "Recuperar Contraseña - Paseo de la Fortuna";
                $cuerpo = "
                <div style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
                    <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;'>
                        <h2 style='color: #315c3d;'>Recuperación de contraseña</h2>
                        <p>Hola <strong>" . htmlspecialchars($usuario['nombre']) . "</strong>,</p>
                        <p>Has solicitado restablecer tu contraseña. Haz clic en el siguiente botón para crear una nueva:</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='$enlace' style='background-color: #DAB561; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Cambiar Contraseña</a>
                        </div>
                        <p>Si el botón no funciona, copia y pega este enlace:</p>
                        <p style='word-break: break-all; color: #666;'>$enlace</p>
                        <hr>
                        <small>Este enlace expirará en 1 hora.</small>
                    </div>
                </div>";

                if (enviarCorreo($email, $asunto, $cuerpo)) {
                    $tipo_alerta = "success";
                    $mensaje = "Se ha enviado un correo con las instrucciones. Revisa tu bandeja de entrada (y spam).";
                } else {
                    $tipo_alerta = "danger";
                    $mensaje = "Error al enviar el correo. Intenta más tarde.";
                }
            }
        } else {
            $tipo_alerta = "warning";
            $mensaje = "Ese correo no está registrado en nuestro sistema.";
        }
    } else {
        $tipo_alerta = "danger";
        $mensaje = "Ingresa un correo válido.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Paseo de la Fortuna</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --color-dorado-fondo: #eac764;
            --color-dorado-btn: #DAB561;
            --color-verde-login: #315c3d;
            --color-foco: #fff4b8; 
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            background-image: url('Footage/Galeria3.png'); 
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            border: none;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.95);
        }

        .card h3 {
            color: var(--color-verde-login);
            font-weight: bold;
        }

        .form-control:focus {
            border-color: var(--color-dorado-btn);
            box-shadow: 0 0 0 0.25rem rgba(218, 181, 97, 0.25);
        }
        
        a:focus, button:focus {
            outline: 3px solid var(--color-foco) !important;
            outline-offset: 2px;
            border-radius: 4px;
        }

        .btn-primary {
            background-color: var(--color-dorado-btn);
            border-color: var(--color-dorado-btn);
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--color-verde-login);
            border-color: var(--color-verde-login);
        }
        a {
            color: var(--color-verde-login);
            text-decoration: none;
        }
        a:hover {
            color: var(--color-dorado-btn);
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="card shadow-lg p-4" style="width: 450px; max-width: 90vw;">
            <div class="card-body">
                
                <h3 class="text-center mb-4">
                    <i class="fas fa-unlock-alt me-2"></i>Recuperar Contraseña
                </h3>
                
                <p class="text-center text-muted mb-4">
                    Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                </p>

                <?php if($mensaje): ?>
                    <div class="alert alert-<?php echo $tipo_alerta; ?>" role="alert">
                        <?php if($tipo_alerta == 'success') echo '<i class="fas fa-check-circle me-2"></i>'; ?>
                        <?php if($tipo_alerta == 'warning') echo '<i class="fas fa-exclamation-triangle me-2"></i>'; ?>
                        <?php if($tipo_alerta == 'danger') echo '<i class="fas fa-exclamation-circle me-2"></i>'; ?>
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-muted">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-envelope text-muted"></i>
                            </span>
                            <input type="email" name="email" class="form-control border-start-0 ps-0" required placeholder="ejemplo@email.com">
                        </div>
                    </div>
                    
                    <button type="submit" name="enviar" class="btn btn-primary w-100 py-2 mt-2">
                        <i class="fas fa-paper-plane me-2"></i>Enviar enlace
                    </button>
                </form>

                <div class="text-center mt-4 border-top pt-3">
                    <a href="login.php" class="fw-bold">
                        <i class="fas fa-arrow-left me-1"></i> Volver al Login
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
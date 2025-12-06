<?php
require_once 'db.php'; 
$mensaje = "";
$tipo_alerta = "";
$mostrar_form = false;
$token = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $fecha_actual = date('Y-m-d H:i:s');

    $sql = "SELECT codUsuario FROM usuarios WHERE token_verificacion = ? AND token_expiracion > ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $token, $fecha_actual);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $mostrar_form = true; 
    } else {
        $tipo_alerta = "danger";
        $mensaje = "El enlace no es válido o ha expirado. Solicita uno nuevo.";
    }
} else {
    header("Location: login.php");
    exit();
}

if (isset($_POST['cambiar']) && $mostrar_form) {
    $pass = $_POST['password'];
    $pass2 = $_POST['password_confirm'];

    if ($pass !== $pass2) {
        $tipo_alerta = "danger";
        $mensaje = "Las contraseñas no coinciden.";
    } elseif (strlen($pass) < 8 || !preg_match('/[A-Z]/', $pass)) {
        $tipo_alerta = "danger";
        $mensaje = "La contraseña debe tener 8 caracteres y una mayúscula.";
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        
        $update = "UPDATE usuarios SET contrasena = ?, token_verificacion = NULL WHERE token_verificacion = ?";
        $stmt_up = $conn->prepare($update);
        $stmt_up->bind_param("ss", $hash, $token);
        
        if ($stmt_up->execute()) {
            $tipo_alerta = "success";
            $mensaje = "¡Contraseña actualizada! <a href='login.php' class='alert-link'>Iniciar Sesión</a>";
            $mostrar_form = false; 
        } else {
            $tipo_alerta = "danger";
            $mensaje = "Error al actualizar. Intenta de nuevo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - Paseo de la Fortuna</title>
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

        .password-wrapper {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            border: none;
            background: none;
            padding: 5px;
        }
        .toggle-password:hover {
            color: var(--color-verde-login);
        }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="card shadow-lg p-4" style="width: 450px; max-width: 90vw;">
            <div class="card-body">
                
                <h3 class="text-center mb-4">
                    <i class="fas fa-key me-2"></i>Nueva Contraseña
                </h3>
                
                <?php if($mensaje): ?>
                    <div class="alert alert-<?php echo $tipo_alerta; ?>" role="alert">
                        <?php if($tipo_alerta == 'success') echo '<i class="fas fa-check-circle me-2"></i>'; ?>
                        <?php if($tipo_alerta == 'danger') echo '<i class="fas fa-exclamation-circle me-2"></i>'; ?>
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>

                <?php if($mostrar_form): ?>
                <form method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Nueva Contraseña</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="pass1" class="form-control" required minlength="8" placeholder="********">
                            <button type="button" class="toggle-password" onclick="togglePassword('pass1', 'icon1')">
                                <i class="fas fa-eye" id="icon1"></i>
                            </button>
                        </div>
                        <small class="text-muted" style="font-size: 0.8rem;">
                            <i class="fas fa-info-circle me-1"></i>Mínimo 8 caracteres y una mayúscula.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Repetir Contraseña</label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirm" id="pass2" class="form-control" required placeholder="********">
                            <button type="button" class="toggle-password" onclick="togglePassword('pass2', 'icon2')">
                                <i class="fas fa-eye" id="icon2"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="cambiar" class="btn btn-primary w-100 py-2 mt-2">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </form>
                <?php endif; ?>
                
                <div class="text-center mt-4 border-top pt-3">
                    <a href="login.php" class="fw-bold">
                        <i class="fas fa-arrow-left me-1"></i> Volver al Login
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
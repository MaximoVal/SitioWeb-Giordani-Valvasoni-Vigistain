<?php
    session_start();
    
    include("funciones.php"); 
    require_once 'mail.php'; 
    
    $mensaje = "";
    $errores = [];
    $mensajeExito = "";

    if(isset($_POST['enviar'])){

        if(!isset($_POST['email']) || !isset($_POST['contrasena']) || 
           !isset($_POST['nombre']) || !isset($_POST['apellido']) || !isset($_POST['tipoUsuario'])){
            $errores[] = "Todos los campos son obligatorios.";
        }
        
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
        $tipo = isset($_POST['tipoUsuario']) ? $_POST['tipoUsuario'] : '';
        
        if(empty($email)){
            $errores[] = "El correo electrónico es obligatorio.";
        } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errores[] = "El formato del correo electrónico no es válido.";
        }
        
        if(empty($password)){
            $errores[] = "La contraseña es obligatoria.";
        } else {
            if(strlen($password) < 8){
                $errores[] = "La contraseña debe tener al menos 8 caracteres.";
            }
            if(!preg_match('/[A-Z]/', $password)){
                $errores[] = "La contraseña debe contener al menos una letra mayúscula.";
            }
        }

        if(empty($errores)){
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "SELECT * FROM usuarios WHERE nombreUsuario='$email'";
            $resultado = consultaSQL($sql);
            
            if(mysqli_num_rows($resultado) > 0){
                $mensaje = "El usuario ya está registrado. Por favor, inicie sesión.";
            } else {
                $token = bin2hex(random_bytes(32));
                $expiracion = date('Y-m-d H:i:s', strtotime('+24 hours'));
                
                if($tipo == 'cliente'){
                    $sqlInsert = "INSERT INTO usuarios (nombre, apellido, nombreUsuario, contrasena, tipoUsuario, categoriaCliente, verificado, token_verificacion, token_expiracion) 
                                  VALUES ('$nombre', '$apellido', '$email', '$passwordHash', '$tipo', 'Inicial', 0, '$token', '$expiracion')";
                } else {
                    if($tipo == 'dueno de local'){
                        $localNoLocal = 'no';
                        $pendienteAprobacion = 'si';
                        
                        $sqlInsert = "INSERT INTO usuarios (nombre, apellido, nombreUsuario, contrasena, tipoUsuario, localNoLocal, pendiente, verificado, token_verificacion, token_expiracion) 
                                      VALUES ('$nombre', '$apellido', '$email', '$passwordHash', '$tipo', '$localNoLocal', '$pendienteAprobacion', 1, '$token', '$expiracion')";
                    } else {
            
                        $sqlInsert = "INSERT INTO usuarios (nombre, apellido, nombreUsuario, contrasena, tipoUsuario, verificado, token_verificacion, token_expiracion) 
                                      VALUES ('$nombre', '$apellido', '$email', '$passwordHash', '$tipo', 0, '$token', '$expiracion')";
                    }
                }

                if(consultaSQL($sqlInsert)){
                    
           
                    if ($tipo == 'dueno de local') {
                        $mensajeExito = "Registro exitoso. Su cuenta de dueño ha sido creada y está pendiente de aprobación por la administración.";
                    } 
                    else {
                        $enlace = "http://paseofortuna.free.nf/verificar.php?token=" . $token;

                        $asunto = 'Confirma tu registro - Paseo de la Fortuna';
                        
                        $cuerpo = "
                        <div style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
                            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;'>
                                <h2 style='color: #315c3d;'>¡Bienvenido a Paseo de la Fortuna!</h2>
                                <p>Hola <strong>$nombre</strong>,</p>
                                <p>Gracias por registrarte. Para completar tu registro y activar tu cuenta, por favor haz clic en el siguiente botón:</p>
                                <div style='text-align: center; margin: 30px 0;'>
                                    <a href='$enlace' style='background-color: #DAB561; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Verificar mi cuenta</a>
                                </div>
                                <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
                                <p style='word-break: break-all; color: #666;'>$enlace</p>
                                <hr>
                                <small>Este enlace expirará en 24 horas.</small>
                            </div>
                        </div>";

                        if(enviarCorreo($email, $asunto, $cuerpo)){
                            $mensajeExito = "Registro exitoso. Por favor, revisa tu correo electrónico ($email) para verificar tu cuenta.";
                        } else {
                            $mensaje = "Usuario registrado, pero hubo un error al enviar el email de verificación. Contacta a soporte.";
                        }
                    }

                } else {
                    $mensaje = "Error al registrar usuario en la base de datos.";
                }
            }
        } else {
            $mensaje = implode("<br>", $errores);
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Paseo de la Fortuna</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Estilos/estilos.css">
    <link rel="stylesheet" href="../Estilos/loginEstilos.css">
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
        .invalid-feedback {
            display: block;
        }
        .password-requirements {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }
        .password-requirements i {
            margin-right: 5px;
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
            color: #315c3d;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
    </style>
</head>
<body>
   <?php include('navNoRegistrado.php')?>

    <div class="container-fluid main-container" style="background-image: url('../Footage/Galeria3.png');background-size: cover; background-position: center; min-height: 100vh; display: flex; justify-content: center; align-items: center; opacity: 0.95;">
        <div class="card login-card shadow-lg p-4" style="width: 500px; max-width: 90vw;">
            <div class="card-body">
                <?php if(!empty($mensajeExito)): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $mensajeExito; ?>
                </div>
                <div class="text-center">
                    <a href="login.php" class="btn btn-primary">Ir al inicio de sesión</a>
                </div>
                <?php else: ?>
                <form action="" method="POST">
                    <h3 class="text-center mb-4">
                        <i class="fas fa-user-circle me-2"></i>
                        Registrarse
                    </h3>
                    <div class="form-row"style="display: flex; gap: 10px; margin-bottom: 15px;">
                        <div class="form-group">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="50" placeholder="Nombre" value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required maxlength="50" placeholder="Apellido" value="<?php echo isset($apellido) ? htmlspecialchars($apellido) : ''; ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">
                            <i class="fas fa-envelope me-1"></i>
                            Correo Electrónico
                        </label>
                        <input type="email" class="form-control" id="exampleInputEmail1" name="email" 
                               placeholder="tu@email.com" required aria-describedby="emailHelp" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">
                            <i class="fas fa-lock me-1"></i>
                            Contraseña
                        </label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="exampleInputPassword1" 
                                   name="contrasena" placeholder="Tu contraseña" required minlength="8" style="padding-right: 40px;">
                            <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Mostrar u ocultar contraseña">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        
                        <div class="password-requirements">
                            <i class="fas fa-info-circle"></i>
                            <small>La contraseña debe tener al menos 8 caracteres y contener al menos una letra mayúscula.</small>
                        </div>
                        
                        <?php if(!empty($mensaje)): ?>
                        <div class="form-text text-danger mt-2">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $mensaje; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <p>Seleccione tipo de usuario</p>
                        
                        <input type="radio" id="cliente" name="tipoUsuario" value="cliente" <?php echo (!isset($tipo) || $tipo == 'cliente') ? 'checked' : ''; ?>>
                        <label for="cliente">Cliente</label><br>
                        
                        <input type="radio" id="dueno" name="tipoUsuario" value="dueno de local" <?php echo (isset($tipo) && $tipo == 'dueno de local') ? 'checked' : ''; ?>>
                        <label for="dueno">Dueño</label><br> 
                        
                        <input type="radio" id="administrador" name="tipoUsuario" value="administrador" <?php echo (isset($tipo) && $tipo == 'administrador') ? 'checked' : ''; ?>>
                        <label for="administrador">Administrador</label><br>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3" name="enviar">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Registrarse
                    </button>
                    
                </form>
                <hr>
                <p class="text-center">¿Ya tienes usuario? <a href="login.php">Iniciar sesión</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('exampleInputPassword1');
            const toggleIcon = document.getElementById('toggleIcon');
            
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
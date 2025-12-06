<?php
    session_start();
    include("funciones.php");
    $mensaje = "";
    $errores = [];

    if(isset($_POST['enviar'])){

        if(empty($_POST['email'])){
            $errores[] = "El correo electrónico es obligatorio";
        }
        
        if(empty($_POST['contraseña'])){
            $errores[] = "La contraseña es obligatoria";
        }

        if(!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $errores[] = "El formato del correo electrónico no es válido";
        }
        
        
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['contraseña']);

        if(empty($errores)){

            $sql = "SELECT * FROM usuarios WHERE nombreUsuario='$email'";
            $resultado = consultaSQL($sql);

            if(mysqli_num_rows($resultado) > 0){
           
                $usuario = mysqli_fetch_assoc($resultado);
         
                $contraseñaProtegida = $usuario['contrasena'];
                
                if(password_verify($password, $contraseñaProtegida)){
                    
                    if($usuario['verificado'] == 1){
                        
                        $_SESSION['usuario'] = $email;
                        $_SESSION['tipoUsuario'] = $usuario['tipoUsuario'];
                        
                        if(isset($_SESSION['redirect_after_login'])){
                            $redirect = $_SESSION['redirect_after_login'];
                            unset($_SESSION['redirect_after_login']);
                            header("Location: $redirect");
                            exit();
                        } else {
                            header("Location: index.php");
                            exit();
                        }

                    } else {
                        $errores[] = "Tu cuenta aún no ha sido verificada. Por favor revisa tu correo electrónico para activarla.";
                    }

                } else {
                    $errores[] = "Contraseña incorrecta";
                }
            } else {
                $errores[] = "Usuario no registrado, por favor regístrese";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Paseo de la Fortuna</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Estilos/estilos.css">
    <link rel="stylesheet" href="../Estilos/loginEstilo.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
        .btn-primary {
            background: linear-gradient(135deg, var(--color-dorado) 0%, var(--color-dorado-oscuro) 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            color: #333;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
        .error-message.show {
            display: block;
        }
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .form-control.is-valid {
            border-color: #198754;
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
    </style>
</head>

<body>
   <?php
    include 'navNoRegistrado.php';
	?>
    <!-- Contenedor principal -->
    <div class="container-fluid main-container" style="background-image: url('../Footage/Paseo 4.png');background-size: cover; background-position: center; min-height: 100vh; display: flex; justify-content: center; align-items: center; opacity: 0.95;">
        <div class="card login-card shadow-lg p-4" style="width: 400px; max-width: 90vw;">
            <div class="card-body">
                <!-- FORM con validación JavaScript -->
                <form id="loginForm" action="" method="POST" novalidate>
                    <h3 class="text-center mb-4">
                        <i class="fas fa-user-circle me-2"></i>
                        Iniciar Sesión
                    </h3>

                    <!-- Mostrar errores del servidor -->
                    <?php if(!empty($errores)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            <?php foreach($errores as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Campo Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-1"></i>
                            Correo Electrónico
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="tu@email.com" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                        <div class="error-message" id="emailError">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="emailErrorText"></span>
                        </div>
                    </div>

                    <!-- Campo Contraseña -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-1"></i>
                            Contraseña
                        </label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="password" 
                                   name="contraseña" placeholder="Tu contraseña" required style="padding-right: 40px;">
                            <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Mostrar u ocultar contraseña">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        <div class="error-message" id="passwordError">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="passwordErrorText"></span>
                        </div>
                    </div>
                    <a href='recuperar.php'>¿Olvidaste tu contraseña?</a>

                    <button type="submit" class="btn btn-primary w-100 mb-3" name="enviar">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Ingresar
                    </button>
                </form>
                <hr>
                <p class="text-center">¿No tenes usuario? <a href="registro.php">Registrarse ahora</a></p>
            </div>
        </div>
    </div>


    <?php if(isset($_SESSION['mensaje_warning'])): ?>
    <script>
    Swal.fire({
        icon: 'warning',
        title: 'Acceso restringido',
        text: '<?php echo $_SESSION['mensaje_warning']; ?>',
        confirmButtonColor: '#DAB561',
        confirmButtonText: 'Entendido'
    });
    </script>
    <?php 
        unset($_SESSION['mensaje_warning']); 
    endif; 
    ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
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
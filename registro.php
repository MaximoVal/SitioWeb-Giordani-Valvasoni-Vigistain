<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();
    include("funciones.php");
    $mensaje = "";
    $errores = [];


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
            $errores[] = "La contrasena es obligatoria.";
        } 

        if(empty($errores)){
           
            
         
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            
            $sql = "SELECT * FROM usuarios WHERE nombreUsuario='$email'";
            $resultado = consultaSQL($sql);
            
            if(mysqli_num_rows($resultado) > 0){
                $mensaje = "El usuario ya está registrado. Por favor, inicie sesión.";
            } else {
                if($tipo == 'cliente'){
                    $sqlInsert = "INSERT INTO usuarios (nombre, apellido, nombreUsuario, contrasena, tipoUsuario, categoriaCliente) 
                                  VALUES ('$nombre', '$apellido', '$email', '$passwordHash', '$tipo', 'Inicial')";

                    if(consultaSQL($sqlInsert)){
                        $_SESSION['usuario'] = $email;
                        $_SESSION['tipoUsuario'] = $tipo;
                        header("Location: index.php");
                        exit();
                    } else {
                        $mensaje = "Error al registrar usuario. Inténtelo de nuevo.";
                    }
                } else {
                    if($tipo == 'dueno de local'){
                        $localNoLocal = 'no';
                        $pendienteAprobacion = 'si';
                        $sqlInsert = "INSERT INTO usuarios (nombre, apellido, nombreUsuario, contrasena, tipoUsuario, localNoLocal, pendiente) 
                                      VALUES ('$nombre', '$apellido', '$email', '$passwordHash', '$tipo', '$localNoLocal', '$pendienteAprobacion')";
                    } else {
                        $sqlInsert = "INSERT INTO usuarios (nombre, apellido, nombreUsuario, contrasena, tipoUsuario) 
                                      VALUES ('$nombre', '$apellido', '$email', '$passwordHash', '$tipo')";
                    } 

                    if(consultaSQL($sqlInsert)){
                        $_SESSION['usuario'] = $email;
                        $_SESSION['tipoUsuario'] = $tipo;
                        header("Location: index.php");
                        exit();
                    } else {
                        $mensaje = "Error al registrar usuario. Inténtelo de nuevo.";
                    }
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
        .invalid-feedback {
            display: block;
        }
        .password-requirements {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.html">
                <img src="../Footage/Logo.png" alt="Paseo de la Fortuna Logo" style="margin=0;border:none;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contacto.php">Contacto</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-container" style="background-image: url('../Footage/Galeria3.png');background-size: cover; background-position: center; min-height: 100vh; display: flex; justify-content: center; align-items: center; opacity: 0.95;">
        <div class="card login-card shadow-lg p-4" style="width: 500px; max-width: 90vw;">
            <div class="card-body">
                <form action="" method="POST">
                    <h3 class="text-center mb-4">
                        <i class="fas fa-user-circle me-2"></i>
                        Registrarse
                    </h3>
                    <div class="form-row"style="display: flex; gap: 10px; margin-bottom: 15px;">
                        <div class="form-group">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="50" placeholder="Nombre">
                        </div>
                        <div class="form-group">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required maxlength="50" placeholder="Apellido">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">
                            <i class="fas fa-envelope me-1"></i>
                            Correo Electrónico
                        </label>
                        <input type="email" class="form-control" id="exampleInputEmail1" name="email" 
                               placeholder="tu@email.com" required aria-describedby="emailHelp">
                    </div>

                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">
                            <i class="fas fa-lock me-1"></i>
                            Contraseña
                        </label>
                        <input type="password" class="form-control" id="exampleInputPassword1" 
                               name="contrasena" placeholder="Tu contraseña" required>
                        
                        <div class="form-text text-danger">
                            <?php if(isset($mensaje)) echo $mensaje; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <p>Seleccione tipo de usuario</p>
                        
                        <input type="radio" id="cliente" name="tipoUsuario" value="cliente" checked>
                        <label for="cliente">Cliente</label><br>
                        
                        <input type="radio" id="dueno" name="tipoUsuario" value="dueno de local">
                        <label for="dueno">Dueño</label><br> 
                        
                        <input type="radio" id="administrador" name="tipoUsuario" value="administrador">
                        <label for="administrador">Administrador</label><br>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3" name="enviar">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Registrarse
                    </button>
                    
                </form>
                <hr>
                <p class="text-center">¿Ya tienes usuario? <a href="login.php">Iniciar sesión</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
</body>
</html>
<?php
    session_start();
    include("funciones.php"); 

    $email = $_SESSION['usuario'];
    $sql = "SELECT nombre, apellido, nombreUsuario, contrasena, categoriaCliente, cantPromoUsada FROM usuarios WHERE nombreUsuario='$email'";
    $resultado = consultaSQL($sql);

    if($resultado && mysqli_num_rows($resultado) > 0){
        $usuario = mysqli_fetch_assoc($resultado);
    } else {
        echo "No se encontraron datos del usuario.";
        exit();
    }

    $mensaje = "";
    $tipoAlerta = ""; 
    if(isset($_POST['enviar-cambios'])){

        $nombre = !empty($_POST['nombre']) ? $_POST['nombre'] : $usuario['nombre'];
        $apellido = !empty($_POST['apellido']) ? $_POST['apellido'] : $usuario['apellido'];
        $emailNuevo = !empty($_POST['email']) ? $_POST['email'] : $usuario['nombreUsuario'];

        $contrasenaActual = $_POST['contrasena-actual'] ?? '';
        $contrasenaNueva = $_POST['contrasena-nueva'] ?? '';

 
        if(!empty($contrasenaNueva)){
            
       
            if (strlen($contrasenaNueva) < 8) {
                $mensaje = "La contraseña debe tener al menos 8 caracteres.";
                $tipoAlerta = "danger";
            }
  
            elseif (!preg_match('/[A-Z]/', $contrasenaNueva)) {
                $mensaje = "La contraseña debe contener al menos una letra mayúscula.";
                $tipoAlerta = "danger";
            }
            
            else {
             
                $contrasenaProtegida = $usuario['contrasena']; 
                
                if(password_verify($contrasenaActual, $contrasenaProtegida)){
     
                    $contrasenaNuevaHash = password_hash($contrasenaNueva, PASSWORD_DEFAULT);
           
                    $sqlActualizar = "UPDATE usuarios 
                                    SET nombre='$nombre', apellido='$apellido', nombreUsuario='$emailNuevo', contrasena='$contrasenaNuevaHash' 
                                    WHERE nombreUsuario='$email'";
                    consultaSQL($sqlActualizar);
                    
                    $mensaje = "¡Datos y contraseña actualizados correctamente!";
                    $tipoAlerta = "success";
                    
                    $_SESSION['usuario'] = $emailNuevo;
                    $usuario['nombre'] = $nombre;
                    $usuario['apellido'] = $apellido;
                    $usuario['nombreUsuario'] = $emailNuevo;

                } else {
                    $mensaje = "La contraseña actual es incorrecta. Solo se actualizaron los datos de perfil.";
                    $tipoAlerta = "warning";
                    
      
                    $sqlActualizar = "UPDATE usuarios 
                                    SET nombre='$nombre', apellido='$apellido', nombreUsuario='$emailNuevo' 
                                    WHERE nombreUsuario='$email'";
                    consultaSQL($sqlActualizar);
                    
                    $_SESSION['usuario'] = $emailNuevo;
                    $usuario['nombre'] = $nombre;
                    $usuario['apellido'] = $apellido;
                    $usuario['nombreUsuario'] = $emailNuevo;
                }
            }
        } else {

            $sqlActualizar = "UPDATE usuarios 
                            SET nombre='$nombre', apellido='$apellido', nombreUsuario='$emailNuevo' 
                            WHERE nombreUsuario='$email'";
            consultaSQL($sqlActualizar);
            
            $mensaje = "Datos de perfil actualizados correctamente.";
            $tipoAlerta = "success";

            $_SESSION['usuario'] = $emailNuevo;
            $usuario['nombre'] = $nombre;
            $usuario['apellido'] = $apellido;
            $usuario['nombreUsuario'] = $emailNuevo;
        }
    }

    if(isset($_POST['cerrar-sesion'])){
        header("Location: cerrar_sesion.php");
        exit();
    }

    if(isset($_POST['eliminar-cuenta'])){
        $sqlEliminar = "DELETE FROM usuarios WHERE nombreUsuario='$email'";
        consultaSQL($sqlEliminar);
        session_destroy();
        header("Location: index.php");
        exit();
    }


    $cantidadUsada = $usuario['cantPromoUsada'];
    $medium = 3;
    $premium = 6;
   
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Cuenta</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="../Estilos/estilos.css">
    <link rel="stylesheet" href="../Estilos/usuarioCuentaEstilos.css">
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
    .list-group-item {
        border: none;
        border-bottom: 1px solid #eee;
        padding: 15px 20px;
        color: #555;
        font-weight: 500;
        background-color:var(--color-dorado)

    }

    .list-group-item:hover {
        background-color: #f8f9fa !important;
    }

    .list-group-item.active {
        background-color: var(--color-dorado-oscuro) !important;
        border-color: var(--color-dorado-oscuro)!important;
        color: white;
    }
    </style>
</head>

<body>
    <?php
        include 'navCliente.php';
    ?>
    <div class="container">
        <div class="header">
            <h1>Panel de Administración</h1>
            <p>Gestiona tu cuenta y configuración personal</p>
        </div>

        <div class="main-content">
            <div class="sidebar">
                <div class="user-info">
                    <h3>
                        <i class="fas fa-user-circle me-2"></i>
                        <br>
                        <?php echo ($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
                    </h3>
                    <p><?php echo ($usuario['nombreUsuario'])?></p>
                </div>
                <div class="card sidebar-links">
            <div class=" d-flex flex-column justify-content-start">
                <button class="btn btn-primary w-100 d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#adminMenu" aria-expanded="false" aria-controls="adminMenu">
                    <i class="bi bi-menu-button-wide me-2"></i>Panel administrador
                </button>

                <div class="collapse d-md-block" id="adminMenu">
                    <div class="list-group">
                        <a href="cuentaUsuario.php" class="list-group-item list-group-item-action active">Administrar datos personales</a>
                        <a href="verPromocionesCliente.php" class="list-group-item list-group-item-action ">Ver promociones</a>
                    </div>
                </div>
            </div>
        </div>
            </div>
            

            <div class="content-area">
                <h2 class="section-title">Información Personal</h2>

                <?php if(!empty($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipoAlerta; ?> alert-dismissible fade show" role="alert">
                        <?php 
                            // Icono según el tipo de mensaje
                            if($tipoAlerta == 'success') echo '<i class="bi bi-check-circle-fill me-2"></i>';
                            if($tipoAlerta == 'danger') echo '<i class="bi bi-exclamation-triangle-fill me-2"></i>';
                            if($tipoAlerta == 'warning') echo '<i class="bi bi-exclamation-circle-fill me-2"></i>';
                            echo $mensaje; 
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">
                            <?php echo $cantidadUsada; ?>
                        </div>
                        <div>Promociones usadas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">
                            <?php 
                                $categoriaActual = $usuario['categoriaCliente'];
                                $categoriaActual = ucfirst($categoriaActual); 
                                echo $categoriaActual;
                            ?>
                        </div>
                        <div>Categoria actual</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">
                            <?php
                                if($categoriaActual == 'Inicial'){
                                    $promocionesFaltantes = $medium - $cantidadUsada;
                                } elseif($categoriaActual == 'Medium'){
                                    $promocionesFaltantes = $premium - $cantidadUsada;
                                } else {
                                    $promocionesFaltantes = "MAX"; 
                                }
                                echo $promocionesFaltantes;
                            ?>
                        </div>
                        <div>Promos para nivel</div>
                    </div>
                </div>

                <form action="" method="POST" class="profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" placeholder="<?php echo ($usuario['nombre'])?>" name="nombre">
                        </div>
                        <div class="form-group">
                            <label for="apellido">Apellido</label>
                            <input type="text" id="apellido" placeholder="<?php echo ($usuario['apellido'])?>" name="apellido">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" placeholder="<?php echo ($usuario['nombreUsuario'])?>" name="email">
                        </div>
                        <br>
                    </div>
                    <div class="security-section">
                        <h3 style="margin-bottom: 15px; color: var(--color-verde);">Seguridad de la Cuenta</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password-actual">Contraseña Actual</label>
                                <input type="password" id="password-actual" placeholder="Ingresa tu contraseña actual" name="contrasena-actual">
                            </div>
                            <div class="form-group">
                                <label for="password-nueva">Nueva Contraseña</label>
                                <input type="password" id="password-nueva" placeholder="Mínimo 8 caracteres y 1 mayúscula" name="contrasena-nueva">
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" name="enviar-cambios">Guardar Cambios</button>
                        
                        <button type="submit" class="btn btn-secondary" name="cerrar-sesion">Cerrar Sesión</button>

                        <button type="submit" class="btn btn-danger" style="margin-left: auto;" name="eliminar-cuenta" onclick="return confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción no se puede deshacer.');">Eliminar Cuenta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    

</body>
</html>
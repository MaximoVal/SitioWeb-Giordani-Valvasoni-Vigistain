<?php
    session_start();
    include("funciones.php"); 

    $email = $_SESSION['usuario'];

 
    $sql = "SELECT nombre, apellido, nombreUsuario, contrasena FROM usuarios WHERE nombreUsuario='$email'";
    $resultado = consultaSQL($sql);

    if($resultado && mysqli_num_rows($resultado) > 0){
        $usuario = mysqli_fetch_assoc($resultado);
    } else {
        echo "No se encontraron datos del usuario.";
        exit();
    }


    $mensaje = "";

    if(isset($_POST['enviar-cambios'])){

        $nombre = !empty($_POST['nombre']) ? $_POST['nombre'] : $usuario['nombre'];
        $apellido = !empty($_POST['apellido']) ? $_POST['apellido'] : $usuario['apellido'];
        $emailNuevo = !empty($_POST['email']) ? $_POST['email'] : $usuario['nombreUsuario'];

        $contraseñaActual = $_POST['contraseña-actual'] ?? '';
        $contraseñaNueva = $_POST['contraseña-nueva'] ?? '';

   
        if(!empty($contraseñaNueva)){
            $contraseñaProtegida = $usuario['contraseña'];
            if(password_verify($contraseñaActual, $contraseñaProtegida)){
                $contraseñaNueva= password_hash($contraseñaNueva, PASSWORD_DEFAULT);
       
                $sqlActualizar = "UPDATE usuarios 
                                SET nombre='$nombre', apellido='$apellido', nombreUsuario='$emailNuevo', contraseña='$contraseñaNueva' 
                                WHERE nombreUsuario='$email'";
                consultaSQL($sqlActualizar);
                $mensaje = "Datos y contraseña actualizados correctamente.";
            } else {
                
                $mensaje = "Contraseña actual incorrecta. No se actualizó la contraseña.";
                
                $sqlActualizar = "UPDATE usuarios 
                                SET nombre='$nombre', apellido='$apellido', nombreUsuario='$emailNuevo' 
                                WHERE nombreUsuario='$email'";
                consultaSQL($sqlActualizar);
            }
        } else {
 
            $sqlActualizar = "UPDATE usuarios 
                            SET nombre='$nombre', apellido='$apellido', nombreUsuario='$emailNuevo' 
                            WHERE nombreUsuario='$email'";
            consultaSQL($sqlActualizar);
            $mensaje = "Datos actualizados correctamente.";
        }


        $_SESSION['usuario'] = $emailNuevo;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
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

    $emali=$_SESSION['usuario'];
    $medium=3;
    $premium=6;
    $consulta="SELECT * FROM usuarios WHERE nombreUsuario='$emali'";
     $resultado=consultaSQL($consulta);
    $fila=mysqli_fetch_assoc($resultado);
   
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

    
</head>
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
<body>
    <?php
        include 'navCliente.php';
    ?>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Panel de Administración</h1>
            <p>Gestiona tu cuenta y configuración personal</p>
        </div>

        <div class="main-content">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="user-info">
                    <h3>
                        <i class="fas fa-user-circle me-2"></i>
                        <br>
                        <?php echo ($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
                    </h3>
                    <p><?php echo ($usuario['nombreUsuario'])?></p>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <h2 class="section-title">Información Personal</h2>
                
                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">
                            <?php
                                $cantidadUsada=$fila['cantPromoUsada'];
                                echo $cantidadUsada;    
                            ?>
                        </div>
                        <div>Promociones usadas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">
                            <?php
                            
                                $categoriaActual = $fila['categoriaCliente'];
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
                                    $promocionesFaltantes = "Rango maximo "; // Ya es premium
                                }
                                echo $promocionesFaltantes;
                            ?>
                        </div>
                        <div>Promociones para mejorar categoria</div>
                    </div>
                </div>

                <!-- Profile Form -->
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
                                <input type="password" id="password-actual" placeholder="Ingresa tu contraseña actual" name="contraseña-actual">
                            </div>
                            <div class="form-group">
                                <label for="password-nueva">Nueva Contraseña</label>
                                <input type="password" id="password-nueva" placeholder="Ingrese la nueva contraseña" name="contraseña-nueva">
                            </div>
                        </div>
                    </div>
                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" name="enviar-cambios">Guardar Cambios</button>
                        <a href="cerrar_sesion.php" class="btn btn-secondary">Cerrar Sesion</a>

                        <button type="button" class="btn btn-danger" style="margin-left: auto;" name="eliminar-cuenta">Eliminar Cuenta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
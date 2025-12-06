<?php
    session_start();
    include_once("funciones.php");
    
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
    $tipoAlerta = ""; 

    // --- LOGICA DE ACTUALIZACIÓN ---
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
          
                    $contrasenaHash = password_hash($contrasenaNueva, PASSWORD_DEFAULT);
             
                    $sqlActualizar = "UPDATE usuarios 
                                    SET nombre='$nombre', apellido='$apellido', nombreUsuario='$emailNuevo', contrasena='$contrasenaHash' 
                                    WHERE nombreUsuario='$email'";
                    consultaSQL($sqlActualizar);
                    
                    $mensaje = "Datos y contraseña actualizados correctamente.";
                    $tipoAlerta = "success";
                    
      
                    $_SESSION['usuario'] = $emailNuevo;
                    $usuario['nombre'] = $nombre;
                    $usuario['apellido'] = $apellido;
                    $usuario['nombreUsuario'] = $emailNuevo;
                    
                } else {
                    $mensaje = "Contraseña actual incorrecta. No se realizaron cambios sensibles.";
                    $tipoAlerta = "danger";
                    
       
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
            
            $mensaje = "Datos actualizados correctamente.";
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
    </style>
</head>


<body>
    <?php include 'navDueno.php'; ?>
    <div class="container">
        <div class="header">
            <h1>Administracion de cuenta</h1>
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
                    <p><strong>Tipo Usuario:</strong> Dueño</p>
                </div>

                <div>
                    <h5>Locales en propiedad</h5>
                    <?php
                        $sqlLocales = "SELECT * FROM locales WHERE codDueno = (SELECT codUsuario FROM usuarios WHERE nombreUsuario='$email')";
                        $resultadoLocales = consultaSQL($sqlLocales);

                        if($resultadoLocales && mysqli_num_rows($resultadoLocales) > 0){
                            echo '<ul class="locales-list">';
                            while($local = mysqli_fetch_assoc($resultadoLocales)){
                               echo '<i class="bi bi-shop me-2" style="color: var(--color-dorado-oscuro); font-size: 1.3rem;"></i>' . $local['nombreLocal'] . '<br>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<p>No tienes locales registrados.</p>';
                        }
                    ?>
                </div>
            </div>

            <div class="content-area">
                <h2 class="section-title">Información Personal</h2>

                <?php if(!empty($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipoAlerta; ?> alert-dismissible fade show" role="alert">
                        <?php 
                            if($tipoAlerta == 'success') echo '<i class="bi bi-check-circle-fill me-2"></i>';
                            if($tipoAlerta == 'danger') echo '<i class="bi bi-exclamation-triangle-fill me-2"></i>';
                            echo $mensaje; 
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php
                    $consultaStats = "SELECT COUNT(*) AS promociones_activas FROM promociones WHERE codLocal IN (SELECT codLocal FROM locales WHERE codDueno = (SELECT codUsuario FROM usuarios WHERE nombreUsuario='$email'))";
                    $resultadoStats = consultaSQL($consultaStats);
                    $stats = mysqli_fetch_assoc($resultadoStats);
                ?>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">
                            <?php echo $stats['promociones_activas']; ?>
                        </div>
                        <div>Promociones activas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">
                            <?php 
                                $consultaSolicitadas = "SELECT COUNT(*) AS promociones_solicitadas FROM uso_promociones WHERE codPromo IN (SELECT codPromo FROM promociones WHERE codLocal IN (SELECT codLocal FROM locales WHERE codDueno = (SELECT codUsuario FROM usuarios WHERE nombreUsuario='$email')))";
                                $resultadoSolicitadas = consultaSQL($consultaSolicitadas);
                                $solicitadas = mysqli_fetch_assoc($resultadoSolicitadas);
                                echo $solicitadas['promociones_solicitadas'];
                            ?>
                        </div>
                        <div>Promociones solicitadas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">
                            <?php 
                                $consultaAprobadas = "SELECT COUNT(*) AS promociones_aprobadas FROM uso_promociones WHERE estado='aceptada' AND codPromo IN (SELECT codPromo FROM promociones WHERE codLocal IN (SELECT codLocal FROM locales WHERE codDueno = (SELECT codUsuario FROM usuarios WHERE nombreUsuario='$email')))";
                                $resultadoAprobadas = consultaSQL($consultaAprobadas);
                                $aprobadas = mysqli_fetch_assoc($resultadoAprobadas);
                                if($solicitadas['promociones_solicitadas'] == 0){
                                    echo "0%";
                                } else {
                                $porcentaje = ($aprobadas['promociones_aprobadas'] / $solicitadas['promociones_solicitadas']) * 100;
                                echo number_format($porcentaje, 0) . "%";
                                }
                            ?>    
                        </div>
                        <div>Tasa de aprobación</div>
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

                        <button type="submit" class="btn btn-danger" style="margin-left: auto;" name="eliminar-cuenta" onclick="return confirm('¿Estás seguro de que quieres eliminar tu cuenta de dueño? Esto podría afectar a tus locales asociados.');">Eliminar Cuenta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
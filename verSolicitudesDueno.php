<?php

$hoy= date('Y-m-d');

function obtenCodLocal($codDueno){
    $sqlObtenLocal="SELECT * FROM locales WHERE codDueno='$codDueno'";
    $result= consultaSQL($sqlObtenLocal);
    $local = mysqli_fetch_assoc($result);
    return $local['codLocal'];
}
function obtenNombreLocal($codDueno){
    $sqlObtenLocal="SELECT * FROM locales WHERE codDueno='$codDueno'";
    $result= consultaSQL($sqlObtenLocal);
    $local = mysqli_fetch_assoc($result);
    return $local['nombreLocal'];
}
function obtenLocal($codDueno){
    $sqlObtenLocal="SELECT * FROM locales WHERE codDueno='$codDueno'";
    $result= consultaSQL($sqlObtenLocal);
    $dataLocal = mysqli_fetch_assoc($result);
    return $dataLocal;
}
function obtenNombreCliente($codCliente){
    $sqlObtenUsu="SELECT * FROM usuarios WHERE codUsuario='$codCliente'";
    $result= consultaSQL($sqlObtenUsu);
    $usu = mysqli_fetch_assoc($result);
    return $usu['nombre'];
}
function obtenDescPromo($codPromo){
    $sqlObtenPromo="SELECT * FROM promociones WHERE codPromo='$codPromo'";
    $result= consultaSQL($sqlObtenPromo);
    $promo = mysqli_fetch_assoc($result);
    return $promo['textoPromo'];
}

function verificaLocalPromo($codPromo, $codLocal){
    $sqlObtencionLocal="SELECT * FROM promociones WHERE codPromo='$codPromo'";
    $resultOL=consultaSQL($sqlObtencionLocal);
    $promo=mysqli_fetch_assoc($resultOL);
    if($codLocal==$promo['codLocal']){
        return 1;
    }else{
        return 0;
    }
}

if(isset($_POST['aprobarSolicitud'])){
    $codUsoPromo = $_POST['codUsoPromo']; 

    $sqlVerifica = "SELECT * FROM uso_promociones WHERE codUsoPromo='$codUsoPromo' AND estado='enviada'";
    $resultVerifica = consultaSQL($sqlVerifica);
    
    if(mysqli_num_rows($resultVerifica) > 0){
        $cliente = mysqli_fetch_assoc($resultVerifica);
        

        $sqlActualizaUso = "UPDATE uso_promociones SET estado='aceptada' WHERE codUsoPromo='$codUsoPromo'";
        consultaSQL($sqlActualizaUso);
        
  
        $sqlActualizaUsoCliente = "UPDATE usuarios SET cantPromoUsada = cantPromoUsada + 1 WHERE codUsuario='{$cliente['codCliente']}'";
        consultaSQL($sqlActualizaUsoCliente);
        
        actualizaCategoriaCliente($cliente['codCliente']);
        
        $_SESSION['solicitud_ok'] = "La solicitud se aceptó correctamente.";
    } else {
        $_SESSION['solicitud_error'] = "Esta solicitud ya fue procesada o no existe.";
    }
    
    header("Location: " . $_SERVER['PHP_SELF'] . "?accion=verSolicitudesDueno");
    exit();
}

if(isset($_POST['rechazarSolicitud'])){
    $codUsoPromo = $_POST['codUsoPromo']; 
    

    $sqlVerifica = "SELECT * FROM uso_promociones WHERE codUsoPromo='$codUsoPromo' AND estado='enviada'";
    $resultVerifica = consultaSQL($sqlVerifica);
    
    if(mysqli_num_rows($resultVerifica) > 0){
        $sqlActualizaUso = "UPDATE uso_promociones SET estado='rechazada' WHERE codUsoPromo='$codUsoPromo'";
        consultaSQL($sqlActualizaUso);
        $_SESSION['rechazo_ok'] = "La solicitud se rechazó correctamente.";
    } else {
        $_SESSION['solicitud_error'] = "Esta solicitud ya fue procesada o no existe.";
    }
    
    header("Location: " . $_SERVER['PHP_SELF'] . "?accion=verSolicitudesDueno");
    exit();
}


if(isset($_SESSION['usuario'])){
    $emailUsu = $_SESSION['usuario'];
    $sqlDueno = "SELECT * FROM usuarios WHERE nombreUsuario='$emailUsu'";
    $resultadoDueno = consultaSQL($sqlDueno);
    $rc = mysqli_fetch_assoc($resultadoDueno);
    $duenoNombre = $rc['categoriaCliente'];
    $codDueno = $rc['codUsuario'];
    $codLocal = obtenCodLocal($codDueno);
    $dataLocal = obtenLocal($codDueno);
}

if(isset($codLocal)){
    $sqlTraeTodoSoli = "SELECT * FROM uso_promociones WHERE estado='enviada'";
    $resultGlobal = consultaSQL($sqlTraeTodoSoli);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Promociones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    
    <main class="container mt-5">
        <div class="row">
            <section class="col-md-9 col-lg-10">
                <div class="p-4 bg-white shadow rounded-3">
                    <h4 class="mb-4" style="color: var(--color-negro); font-weight:600;">
                        Solicitudes de Promociones de: 
                        <span style="color: var(--color-dorado-oscuro);">
                            <?php echo obtenNombreLocal($codDueno); ?>
                        </span>
                    </h4>

                    
                    <div class="table-responsive">
                        <form method="POST" action="" id="formPromos">
                            <table class="table table-hover table-striped align-middle text-center border flex-grow-1">
                                <thead style="background: linear-gradient(135deg, var(--color-dorado), var(--color-dorado-oscuro)); color: var(--color-negro);">
                                    <tr>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th>Id Solicitante</th>
                                        <th>Nombre Solicitante</th>
                                        <th>Fecha de Solicitud</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if(isset($resultGlobal) && mysqli_num_rows($resultGlobal) != 0){
                                        while($soliGlobal = mysqli_fetch_assoc($resultGlobal)){
                                            if(verificaLocalPromo($soliGlobal['codPromo'], $codLocal) == 1 ){
                                    ?>
                                    <tr>
                                        <td>PR-<?php echo $soliGlobal['codPromo']; ?></td>
                                        <td><?php echo obtenDescPromo($soliGlobal['codPromo']); ?></td>
                                        <td><?php echo $soliGlobal['codCliente']; ?></td>
                                        <td><?php echo obtenNombreCliente($soliGlobal['codCliente']); ?></td>
                                        <td><?php echo $soliGlobal['fechaUsoPromo']; ?></td>
                                        <td>
                                            <form method="POST" action="" style="display:inline;">
                                                <input type="hidden" name="codUsoPromo" value="<?php echo $soliGlobal['codUsoPromo']; ?>">
                                                <button class="btn btn-sm btn-success mb-1" type="submit" name="aprobarSolicitud">
                                                    <i class="bi bi-check"></i> Aprobar
                                                </button>
                                            </form>

                                            <form method="POST" action="" style="display:inline;">
                                                <input type="hidden" name="codUsoPromo" value="<?php echo $soliGlobal['codUsoPromo']; ?>">
                                                <button class="btn btn-sm btn-danger mb-1" type="submit" name="rechazarSolicitud">
                                                    <i class="bi bi-x"></i> Rechazar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php
                                            }
                                        }
                                    } else {
                                    ?>
                                        <tr>
                                            <td colspan="6">
                                                <p class="fw-bold" style="color: var(--color-gris);">
                                                    <i class="bi bi-search"></i> [NO SE ENCONTRARON PROMOCIONES PENDIENTES]
                                                </p>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if(isset($_SESSION['solicitud_ok'])) { ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Solicitud aprobada',
        text: '<?php echo $_SESSION['solicitud_ok']; ?>',
        showConfirmButton: false
    });
    </script>
    <?php
        unset($_SESSION['solicitud_ok']);
        
    } ?>

    <?php if(isset($_SESSION['rechazo_ok'])) { ?>
    <script>
    Swal.fire({
        icon: 'warning',
        title: 'Solicitud rechazada',
        text: '<?php echo $_SESSION['rechazo_ok']; ?>',
        timer: 2500,
        showConfirmButton: false
    });
    </script>
    <?php
        unset($_SESSION['rechazo_ok']);
    } ?>

    <?php if(isset($_SESSION['solicitud_error'])) { ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?php echo $_SESSION['solicitud_error']; ?>',
        timer: 3000
    });
    </script>
    <?php
        unset($_SESSION['solicitud_error']);
    } ?>

</body>
</html>
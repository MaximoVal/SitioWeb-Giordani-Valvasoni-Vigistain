<?php
session_start();
include_once('funciones.php');
$hoy = date('Y-m-d');


$emailUsu = $_SESSION['usuario'];

$sqlDataDueno = "SELECT * FROM usuarios WHERE tipoUsuario='dueno de local' AND nombreUsuario='$emailUsu'";
$dataDueno = consultaSQL($sqlDataDueno);
$dueno = mysqli_fetch_assoc($dataDueno);
$codDueno = $dueno['codUsuario'];

if($dueno['localNoLocal'] != 'no'){
    $sqlBuscaLocalDueno = "SELECT * FROM locales WHERE codDueno='$codDueno'";
    $dataLocalDueno = consultaSQL($sqlBuscaLocalDueno);
    $localDueno = mysqli_fetch_assoc($dataLocalDueno);
    $codLocal = $localDueno['codLocal']; 
} else {
    $localDueno = [];
    $codLocal = null;
}

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'verSolicitudesDueno'; 


function obtenNombreCliente($codCliente){
    $sqlObtenUsu = "SELECT * FROM usuarios WHERE codUsuario='$codCliente'";
    $result = consultaSQL($sqlObtenUsu);
    $usu = mysqli_fetch_assoc($result);
    return $usu['nombre'];
}
function obtenDescPromo($codPromo){
    $sqlObtenPromo = "SELECT * FROM promociones WHERE codPromo='$codPromo'";
    $result = consultaSQL($sqlObtenPromo);
    $promo = mysqli_fetch_assoc($result);
    return $promo['textoPromo'];
}
function verificaLocalPromo($codPromo, $codLocal){
    $sqlObtencionLocal = "SELECT * FROM promociones WHERE codPromo='$codPromo'";
    $resultOL = consultaSQL($sqlObtencionLocal);
    $promo = mysqli_fetch_assoc($resultOL);
    return ($codLocal == $promo['codLocal']) ? 1 : 0;
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
    header("Location: " . $_SERVER['PHP_SELF']);
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
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
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
    <title>Solicitudes - Panel Dueño</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../Estilos/adiministraDuenoEstilos.css">
    <link rel="stylesheet" href="../Estilos/estilos.css">
    <link rel="stylesheet" href="../Estilos/usuarioCuentaEstilos.css">

    <style>
        :root {
            --color-dorado: #EED284;
            --color-dorado-oscuro: #DAB561;
            --color-dorado-foco: #fff4b8;
            --color-negro: #333333;
            --color-blanco: #FFFFFF;
            --color-verde: #355B38;
        }
        
        a:focus, button:focus {
            outline: 3px solid var(--color-dorado-foco) !important;
            outline-offset: 2px;
            border-radius: 4px;
        }

        body {
            color: var(--color-negro);
            background-color: #f8f9fa; 
        }
        a:focus-visible, button:focus-visible {
            outline: 3px solid var(--color-verde);
            outline-offset: 2px;
            border-radius: 4px;
        }

        .bg-dorado { background-color: var(--color-dorado); }
        .text-verde { color: var(--color-verde); }
        .text-negro { color: var(--color-negro); }
        
        .btn-mobile-nav {
            background-color: var(--color-dorado);
            color: var(--color-negro);
            font-weight: 700;
            border: 1px solid var(--color-dorado-oscuro);
        }
        .btn-mobile-nav:hover, .btn-mobile-nav:focus {
            background-color: var(--color-dorado-oscuro);
            color: var(--color-negro);
        }

        .sidebar-box {
            background-color: var(--color-blanco);
            border-left: 5px solid var(--color-dorado);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            overflow: hidden; 
        }

   
        .nav-link-admin {
            color: var(--color-negro);
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease-in-out;
            font-weight: 500;
        }
        
        .nav-link-admin:hover {
            background-color: var(--color-dorado-foco);
            transform: translateX(5px);
            color: var(--color-negro);
        }

        .nav-link-admin.active {
            background-color: var(--color-dorado);
            color: var(--color-negro);
            font-weight: 700;
            border-color: var(--color-dorado-oscuro);
            border-left: 4px solid var(--color-verde); 
        }

        
        .text-break-nice {
            word-break: break-word;
            overflow-wrap: break-word;
        }
        
        .text-label {
            font-weight: 700;
            color: var(--color-verde); 
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <?php include('navDueno.php'); ?>

    <main class="container-fluid my-4">
        <div class="row">
            
            <!-- SIDEBAR RESPONSIVO -->
            <aside class="col-12 col-md-12 col-lg-3 mb-4" aria-label="Menú lateral de administración">
                
                <!-- Botones Móviles/Tablet (Visibles < Large) -->
                <div class="mb-3 mt-3 d-flex flex-wrap gap-2 d-lg-none">
                    <button class="btn btn-mobile-nav flex-fill d-flex align-items-center justify-content-center gap-2 py-2" 
                            type="button" data-bs-toggle="collapse" data-bs-target="#panelAdmin">
                        <i class="bi bi-gear-fill" aria-hidden="true"></i><span>Panel</span>
                    </button>
                    <button class="btn btn-mobile-nav flex-fill d-flex align-items-center justify-content-center gap-2 py-2" 
                            type="button" data-bs-toggle="collapse" data-bs-target="#infoLocal">
                        <i class="bi bi-shop" aria-hidden="true"></i><span>Local</span>
                    </button>
                    <button class="btn btn-mobile-nav flex-fill d-flex align-items-center justify-content-center gap-2 py-2" 
                            type="button" data-bs-toggle="collapse" data-bs-target="#infoDueno">
                        <i class="bi bi-person-vcard" aria-hidden="true"></i><span>Dueño</span>
                    </button>
                </div>

                <!-- Panel Admin -->
                <nav class="sidebar-box mb-3 p-3 collapse d-lg-block" id="panelAdmin">
                    <h2 class="h5 mb-3 d-flex align-items-center text-negro fw-bold">
                        <i class="bi bi-gear-fill me-2 text-verde" aria-hidden="true"></i> Administración
                    </h2>
                    <ul class="list-unstyled m-0">
                        <li class="mb-2">
                            <a href="administraDueno.php?accion=adminPromos" 
                               class="text-decoration-none d-flex align-items-center py-2 px-3 rounded-2 nav-link-admin">
                                <i class="bi bi-tag-fill me-2"></i> Administrar Promociones
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="verSolicitudesDueno.php" 
                               class="text-decoration-none d-flex align-items-center py-2 px-3 rounded-2 nav-link-admin active" aria-current="page">
                                <i class="bi bi-check-square me-2"></i> Solicitudes
                            </a>
                        </li>
                        <li>
                            <a href="administraDueno.php?accion=verReportes" 
                               class="text-decoration-none d-flex align-items-center py-2 px-3 rounded-2 nav-link-admin">
                                <i class="bi bi-file-earmark-bar-graph me-2"></i> Reportes
                            </a>
                        </li>
                    </ul>
                </nav>
                
                <!-- Info Local -->
                <section class="sidebar-box p-3 mb-3 collapse d-lg-block" id="infoLocal">
                    <h2 class="h5 mb-3 d-flex align-items-center text-negro fw-bold">
                        <i class="bi bi-shop me-2 text-verde"></i> Datos del Local
                    </h2>
                    <?php if(!empty($localDueno)){ ?>
                        <div class="mb-2">
                            <p class="mb-0 text-label">Código:</p>
                            <p class="text-negro m-0 text-break-nice"><?php echo $localDueno['codLocal']; ?></p>
                        </div>
                        <div class="mb-2">
                            <p class="mb-0 text-label">Nombre:</p>
                            <p class="text-negro m-0 text-break-nice"><?php echo $localDueno['nombreLocal']; ?></p>
                        </div>
                        <div class="mb-2">
                            <p class="mb-0 text-label">Sector:</p>
                            <p class="text-negro m-0"><?php echo $localDueno['ubicacionLocal']; ?></p>
                        </div>
                        <div>
                            <p class="mb-0 text-label">Rubro:</p>
                            <p class="text-negro m-0"><?php echo ucfirst($localDueno['categoriaLocal']); ?></p>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-warning small">No posee local registrado.</div>
                    <?php } ?>
                </section>

                <!-- Info Dueño -->
                <section class="sidebar-box p-3 collapse d-lg-block" id="infoDueno">
                    <h2 class="h5 mb-3 d-flex align-items-center text-negro fw-bold">
                        <i class="bi bi-person-vcard me-2 text-verde"></i> Datos del Dueño
                    </h2>
                    <div class="mb-2">
                        <p class="mb-0 text-label">Código:</p>
                        <p class="text-negro m-0"><?php echo $dueno['codUsuario']; ?></p>
                    </div>
                    <div class="mb-2">
                        <p class="mb-0 text-label">Nombre:</p>
                        <p class="text-negro m-0 text-break-nice"><?php echo $dueno['nombre'] . ' ' . $dueno['apellido']; ?></p>
                    </div>
                    <div>
                        <p class="mb-0 text-label">Email:</p>
                        <p class="text-negro m-0 text-break-nice"><?php echo $dueno['nombreUsuario']; ?></p>
                    </div>
                </section>
            </aside>

            <!-- CONTENIDO PRINCIPAL (Tabla de Solicitudes) -->
            <section class="col-12 col-md-12 col-lg-9" role="main">
                
                <div class="p-4 bg-white shadow rounded-3" style="border-top: 5px solid var(--color-dorado);">
                    <h4 class="mb-4" style="color: var(--color-negro); font-weight:600;">
                        <i class="bi bi-inbox-fill me-2" style="color: var(--color-verde);"></i>
                        Solicitudes de Promociones: 
                        <span style="color: var(--color-dorado-oscuro);">
                            <?php echo !empty($localDueno) ? $localDueno['nombreLocal'] : 'Sin Local'; ?>
                        </span>
                    </h4>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle text-center border">
                            <thead style="background: linear-gradient(135deg, var(--color-dorado), var(--color-dorado-oscuro));">
                                <tr>
                                    <th class="text-negro">Código</th>
                                    <th class="text-negro">Descripción</th>
                                    <th class="text-negro">ID Cliente</th>
                                    <th class="text-negro">Cliente</th>
                                    <th class="text-negro">Fecha</th>
                                    <th class="text-negro">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $haySolicitudes = false;
                                if(isset($resultGlobal) && mysqli_num_rows($resultGlobal) != 0){
                                    while($soliGlobal = mysqli_fetch_assoc($resultGlobal)){
                                        if(verificaLocalPromo($soliGlobal['codPromo'], $codLocal) == 1 ){
                                            $haySolicitudes = true;
                                ?>
                                <tr>
                                    <td class="fw-bold">PR-<?php echo $soliGlobal['codPromo']; ?></td>
                                    <td class="text-break-nice" style="max-width: 250px;"><?php echo obtenDescPromo($soliGlobal['codPromo']); ?></td>
                                    <td><?php echo $soliGlobal['codCliente']; ?></td>
                                    <td class="text-break-nice"><?php echo obtenNombreCliente($soliGlobal['codCliente']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($soliGlobal['fechaUsoPromo'])); ?></td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <form method="POST" action="">
                                                <input type="hidden" name="codUsoPromo" value="<?php echo $soliGlobal['codUsoPromo']; ?>">
                                                <button class="btn btn-sm btn-success" type="submit" name="aprobarSolicitud" title="Aprobar">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>

                                            <form method="POST" action="">
                                                <input type="hidden" name="codUsoPromo" value="<?php echo $soliGlobal['codUsoPromo']; ?>">
                                                <button class="btn btn-sm btn-danger" type="submit" name="rechazarSolicitud" title="Rechazar">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                        }
                                    }
                                } 
                                
                                if(!$haySolicitudes) {
                                ?>
                                    <tr>
                                        <td colspan="6" class="py-4">
                                            <p class="fw-bold text-secondary mb-0">
                                                <i class="bi bi-check-circle me-2"></i> No hay solicitudes pendientes.
                                            </p>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if(isset($_SESSION['solicitud_ok'])) { ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: '¡Aprobada!',
        text: '<?php echo $_SESSION['solicitud_ok']; ?>',
        timer: 2000,
        showConfirmButton: false
    });
    </script>
    <?php unset($_SESSION['solicitud_ok']); } ?>

    <?php if(isset($_SESSION['rechazo_ok'])) { ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Rechazada',
        text: '<?php echo $_SESSION['rechazo_ok']; ?>',
        timer: 2000,
        showConfirmButton: false
    });
    </script>
    <?php unset($_SESSION['rechazo_ok']); } ?>

    <?php if(isset($_SESSION['solicitud_error'])) { ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?php echo $_SESSION['solicitud_error']; ?>'
    });
    </script>
    <?php unset($_SESSION['solicitud_error']); } ?>


</body>
</html>
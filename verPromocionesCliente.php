<?php
session_start(); 
$hoy = date('Y-m-d');

include_once('funciones.php'); 

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$nombreUsuarioSesion = $_SESSION['usuario'];

$sqlGetId = "SELECT codUsuario, nombre, apellido, nombreUsuario 
             FROM usuarios 
             WHERE nombreUsuario = '$nombreUsuarioSesion'";

$resultGetId = consultaSQL($sqlGetId);
$datosUsuarioBD = mysqli_fetch_assoc($resultGetId);

if (!$datosUsuarioBD) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$idUsuario = $datosUsuarioBD['codUsuario'];
$usuario = $datosUsuarioBD; 

$porPagina = 3;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $porPagina;

$filtroEstado = '';
$filtroBusqueda = '';

if(isset($_GET['estado']) && !empty($_GET['estado'])){
    $estadoFiltro = $_GET['estado'];
    $filtroEstado = " AND up.estado='$estadoFiltro'";
}

if(isset($_GET['busqueda']) && !empty($_GET['busqueda'])){
    $busqueda = $_GET['busqueda'];
    $filtroBusqueda = " AND (p.textoPromo LIKE '%$busqueda%' OR l.nombreLocal LIKE '%$busqueda%' OR up.codUsoPromo LIKE '%$busqueda%')";
}

$sqlCount = "SELECT COUNT(*) AS total 
             FROM uso_promociones up
             INNER JOIN usuarios u ON up.codCliente = u.codUsuario
             INNER JOIN promociones p ON up.codPromo = p.codPromo
             INNER JOIN locales l ON p.codLocal = l.codLocal
             WHERE up.codCliente = '$idUsuario' 
             $filtroEstado $filtroBusqueda";
$resultCount = consultaSQL($sqlCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$total = $rowCount['total'] ?? 0;
$totalPaginas = max(1, ceil($total / $porPagina));

$sqlSolicitudes = "SELECT 
                    up.*,
                    p.textoPromo,
                    p.categoriaPromo,
                    p.fechaDesdePromo,
                    p.fechaHastaPromo,
                    p.estadoPromo,
                    l.nombreLocal,
                    l.categoriaLocal
                   FROM uso_promociones up
                   INNER JOIN usuarios u ON up.codCliente = u.codUsuario
                   INNER JOIN promociones p ON up.codPromo = p.codPromo
                   INNER JOIN locales l ON p.codLocal = l.codLocal
                   WHERE up.codCliente = '$idUsuario'
                   $filtroEstado
                   $filtroBusqueda
                   ORDER BY up.fechaUsoPromo DESC
                   LIMIT $porPagina OFFSET $offset";
$resultSolicitudes = consultaSQL($sqlSolicitudes);

function contarSolicitudesPorEstado($estado = null, $idUsuario = null){
    $filtro = "";
    if($estado) {
        $filtro .= " AND estado='$estado'";
    }

    if($idUsuario) {
        $filtro .= " AND codCliente='$idUsuario'";
    }
    
    $sql = "SELECT COUNT(*) as total FROM uso_promociones WHERE 1=1 $filtro";
    $result = consultaSQL($sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

$totalSolicitudes = contarSolicitudesPorEstado(null, $idUsuario);
$solicitudesEnviadas = contarSolicitudesPorEstado('enviada', $idUsuario);
$solicitudesAceptadas = contarSolicitudesPorEstado('aceptada', $idUsuario);
$solicitudesRechazadas = contarSolicitudesPorEstado('rechazada', $idUsuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promociones</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../Estilos/estilos.css">
    <link rel="stylesheet" href="../Estilos/usuarioCuentaEstilos.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    :root {
        --color-dorado-fondo: #eac764;
        --color-dorado-btn: #DAB561;
        --color-dorado-oscuro: #b89643;
        --color-verde-login: #315c3d;
        --color-foco: #fff4b8; 
    }
    
    body { background-color: #f5f5f5; }

    .sidebar-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        overflow: hidden;
        border-top: 4px solid var(--color-dorado-oscuro);
    }

    .user-info h3 { font-size: 1.2rem; margin-bottom: 5px; font-weight: bold; }
    .user-info p { margin-bottom: 0; font-size: 0.9rem; opacity: 0.9; }
    .user-info i { font-size: 3rem; margin-bottom: 10px; }

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

    /* Estilos generales */
    .solicitud-card {
        border: 1px solid #eee; 
        border-left: 4px solid var(--color-dorado);
        margin-bottom: 20px;
        transition: all 0.3s ease;
        background: white;
    }
    
    .badge-enviada { background-color: #17a2b8; }
    .badge-aceptada { background-color: #28a745; }
    .badge-rechazada { background-color: #dc3545; }
    
    .info-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    .main-wrapper-card {
        background-color: white;
        border-radius: 12px;
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }
    .page-item.disabled .page-link {
        background-color: #e9ecef;
        opacity: 0.6;
        cursor: not-allowed;
    }
     .page-item.active .page-link {
        background-color: var(--color-dorado-oscuro);
        border-color: var(--color-dorado-oscuro);
        color: #000;
        font-weight: bold;
    }

    .page-link {
        color: var(--color-negro);
        font-weight: 500;
    }

    .page-link:hover,
    .page-link:focus {
        background-color: var(--color-dorado);
        color: #000;
        border-color: var(--color-dorado-oscuro);
    }

    @media print {
        nav, .sidebar, .container-fluid, footer, .no-print {
            display: none !important;
        }

        #area-impresion {
            display: block !important;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            margin: 0;
            padding: 20px;
            background: white;
            z-index: 9999;
        }

        #area-impresion .card {
            border: 2px solid #000;
            box-shadow: none;
        }
        
        #area-impresion .badge {
            border: 1px solid #000;
            color: #000 !important;
            background: transparent !important;
        }
    }
</style>
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include_once ('navCliente.php'); ?>
    
    <div class="container-fluid my-4 flex-grow-1 px-4" id="contenido-principal">
        
        <div class="row g-4">
            
            <aside class="col-12 col-md-3 col-xl-2">
                <div class="sidebar">
                    <div class="user-info text-center mb-3">
                        <h3>
                            <i class="fas fa-user-circle me-2" style="font-size: 2.5rem;"></i>
                            <br>
                            <?php echo ($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
                        </h3>
                        <p><?php echo ($usuario['nombreUsuario'])?></p>
                    </div>

                    <div class="card sidebar-links border-0 bg-transparent">
                        <div class="d-flex flex-column justify-content-start">
                            
                            <button class="btn btn-primary w-100 d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#adminMenu" aria-expanded="false" aria-controls="adminMenu">
                                <i class="bi bi-menu-button-wide me-2"></i>Panel administrador
                            </button>

                            <div class="collapse d-md-block" id="adminMenu">
                                <div class="list-group">
                                    <a href="cuentaUsuario.php" class="list-group-item list-group-item-action">
                                        Administrar datos personales
                                    </a>
                                    <a href="verPromocionesCliente.php" class="list-group-item list-group-item-action active">
                                        Ver promociones
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="col-12 col-md-9 col-xl-10">
                
                <div class="card main-wrapper-card">
                    <div class="card-body p-4 p-lg-5"> <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <div>
                                <h2 class="fw-bold" style="color: var(--color-negro); margin-bottom: 0;">
                                    <i class="bi bi-tags-fill me-2" style="color: var(--color-dorado-oscuro);"></i>
                                    Gestión de Promociones
                                </h2>
                                <p class="text-muted mb-0 small">Panel de administración y estadísticas</p>
                            </div>
                        </div>

                        <div class="row g-3 mb-5">
                            
                            <div class="col-12 col-sm-6">
                                <div class="p-3 rounded bg-light border-start border-4" style="border-color: var(--color-dorado-oscuro) !important;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="d-block text-muted small text-uppercase fw-bold">Total</span>
                                            <h3 class="mb-0 text-dark"><?php echo $totalSolicitudes; ?></h3>
                                        </div>
                                        <i class="bi bi-collection text-muted fs-3 opacity-25"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="p-3 rounded bg-light border-start border-4 border-info">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="d-block text-muted small text-uppercase fw-bold">Enviadas</span>
                                            <h3 class="mb-0 text-info"><?php echo $solicitudesEnviadas; ?></h3>
                                        </div>
                                        <i class="bi bi-send text-info fs-3 opacity-25"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="p-3 rounded bg-light border-start border-4 border-success">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="d-block text-muted small text-uppercase fw-bold">Aceptadas</span>
                                            <h3 class="mb-0 text-success"><?php echo $solicitudesAceptadas; ?></h3>
                                        </div>
                                        <i class="bi bi-check-circle text-success fs-3 opacity-25"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="p-3 rounded bg-light border-start border-4 border-danger">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="d-block text-muted small text-uppercase fw-bold">Rechazadas</span>
                                            <h3 class="mb-0 text-danger"><?php echo $solicitudesRechazadas; ?></h3>
                                        </div>
                                        <i class="bi bi-x-circle text-danger fs-3 opacity-25"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="mb-5" style="border-top: 2px dashed #ddd;">


                        <h4 class="mb-3 text-secondary"><i class="bi bi-list-check me-2"></i>Historial de Solicitudes</h4>

                        <?php if(mysqli_num_rows($resultSolicitudes) > 0): ?>
                            <?php while($solicitud = mysqli_fetch_assoc($resultSolicitudes)): 
                                $promoVencida = ($solicitud['fechaHastaPromo'] < $hoy);
                           
                                $idCard = 'promo-card-' . $solicitud['codUsoPromo'];
                            ?>
                            
                            <div class="card solicitud-card" id="<?php echo $idCard; ?>">
                                <div class="card-header" style="background: linear-gradient(135deg, var(--color-dorado), var(--color-dorado-oscuro));">
                                    <div class="row align-items-center">
                                        <div class="col-md-7">
                                            <h5 class="mb-0 text-white">
                                                <i class="bi bi-receipt me-2"></i>
                                                Solicitud #<?php echo $solicitud['codUsoPromo']; ?>
                                            </h5>
                                            <small class="text-white-50">
                                                <i class="bi bi-calendar3"></i> 
                                                Solicitado el: <?php echo date('d/m/Y', strtotime($solicitud['fechaUsoPromo'])); ?>
                                            </small>
                                        </div>
                                        <div class="col-md-5 text-end">
                                            <button type="button" class="btn btn-sm btn-light text-warning me-2 no-print" onclick="imprimirPromo('<?php echo $idCard; ?>')" title="Imprimir cupón">
                                                <i class="bi bi-printer-fill"></i>
                                            </button>

                                            <?php
                                            $estadoBadge = '';
                                            $icon = '';
                                            switch($solicitud['estado']){
                                                case 'enviada': $estadoBadge = 'badge-enviada'; $icon = 'send'; break;
                                                case 'aceptada': $estadoBadge = 'badge-aceptada'; $icon = 'check-circle'; break;
                                                case 'rechazada': $estadoBadge = 'badge-rechazada'; $icon = 'x-circle'; break;
                                            }
                                            ?>
                                            <span class="badge <?php echo $estadoBadge; ?> fs-6">
                                                <i class="bi bi-<?php echo $icon; ?>"></i>
                                                <?php echo ucfirst($solicitud['estado']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <div class="info-section">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="text-success mb-0" style="color: var(--color-verde-login) !important;">
                                                <i class="bi bi-tag-fill me-2"></i> <?php echo $solicitud['nombreLocal']; ?>
                                            </h5>
                                            <span class="badge bg-secondary">
                                                <?php echo ucfirst($solicitud['categoriaPromo']); ?>
                                            </span>
                                        </div>

                                        <p class="mb-3 lead" style="font-weight: 500;">
                                            <?php echo $solicitud['textoPromo']; ?>
                                        </p>

                                        <div class="d-flex gap-4 flex-wrap text-muted small">
                                            <div>
                                                <strong><i class="bi bi-upc-scan"></i> Código:</strong> 
                                                PR-<?php echo $solicitud['codPromo']; ?>
                                            </div>
                                            <div>
                                                <strong><i class="bi bi-shop"></i> Tipo Local:</strong> 
                                                <?php echo ucfirst($solicitud['categoriaLocal']); ?>
                                            </div>
                                            <div>
                                                <strong><i class="bi bi-calendar-event"></i> Vigencia:</strong>
                                                <?php echo date('d/m/Y', strtotime($solicitud['fechaDesdePromo'])); ?> al 
                                                <?php echo date('d/m/Y', strtotime($solicitud['fechaHastaPromo'])); ?>
                                                <?php if($promoVencida): ?>
                                                    <span class="badge bg-danger ms-1">Vencida</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>       
                                </div>
                            </div>
                            
                            <?php endwhile; ?>

                            <?php if($totalPaginas > 0): ?>
                            <nav aria-label="Paginación" class="mt-4 no-print">
                                <ul class="pagination justify-content-center ">
                                    <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?pagina=<?php echo max(1, $pagina - 1); ?><?php echo isset($_GET['estado']) ? '&estado='.$_GET['estado'] : ''; ?><?php echo isset($_GET['busqueda']) ? '&busqueda='.$_GET['busqueda'] : ''; ?>">Anterior</a>
                                    </li>
                                    <?php for($i = 1; $i <= $totalPaginas; $i++): ?>
                                    <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?pagina=<?php echo $i; ?><?php echo isset($_GET['estado']) ? '&estado='.$_GET['estado'] : ''; ?><?php echo isset($_GET['busqueda']) ? '&busqueda='.$_GET['busqueda'] : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo ($pagina >= $totalPaginas) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?pagina=<?php echo min($totalPaginas, $pagina + 1); ?><?php echo isset($_GET['estado']) ? '&estado='.$_GET['estado'] : ''; ?><?php echo isset($_GET['busqueda']) ? '&busqueda='.$_GET['busqueda'] : ''; ?>">Siguiente</a>
                                    </li>
                                </ul>
                            </nav>
                            <?php endif; ?>

                        <?php else: ?>
                        <div class="card shadow-sm border-0 bg-light">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 4rem; color: #dee2e6;"></i>
                                <h5 class="mt-3 text-muted">No se encontraron solicitudes</h5>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div> </div> </main>
        </div> 
    </div> 

    <?php if(isset($_SESSION['mensaje_exito'])): ?>
    <script>
    Swal.fire({
        icon: 'success', title: 'Éxito', text: '<?php echo $_SESSION['mensaje_exito']; ?>', confirmButtonColor: '#DAB561'
    });
    </script>
    <?php unset($_SESSION['mensaje_exito']); endif; ?>

    <?php if(isset($_SESSION['mensaje_error'])): ?>
    <script>
    Swal.fire({
        icon: 'error', title: 'Error', text: '<?php echo $_SESSION['mensaje_error']; ?>', confirmButtonColor: '#DAB561'
    });
    </script>
    <?php unset($_SESSION['mensaje_error']); endif; ?>

    <div id="area-impresion" class="d-none"></div>

    <script>
    function imprimirPromo(idElemento) {
        var contenidoCard = document.getElementById(idElemento).outerHTML;

        var areaImpresion = document.getElementById('area-impresion');

        areaImpresion.innerHTML = contenidoCard;

        window.print();
        
        areaImpresion.innerHTML = '';
    }
    </script>

</body>
</html>
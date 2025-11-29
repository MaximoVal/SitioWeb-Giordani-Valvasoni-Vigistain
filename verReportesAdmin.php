<?php

$hoy = date('Y-m-d');


$porPagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $porPagina;


$filtroEstado = '';
$filtroCategoria = '';
$filtroBusqueda = '';

if(isset($_GET['estado']) && !empty($_GET['estado'])){
    $estadoFiltro = $_GET['estado'];
    $filtroEstado = " AND p.estadoPromo='$estadoFiltro'";
}

if(isset($_GET['categoria']) && !empty($_GET['categoria'])){
    $categoriaFiltro = $_GET['categoria'];
    $filtroCategoria = " AND p.categoriaPromo='$categoriaFiltro'";
}

if(isset($_GET['busqueda']) && !empty($_GET['busqueda'])){
    $busqueda = $_GET['busqueda'];
    $filtroBusqueda = " AND (p.textoPromo LIKE '%$busqueda%' OR l.nombreLocal LIKE '%$busqueda%' OR u.nombre LIKE '%$busqueda%')";
}


$sqlCount = "SELECT COUNT(*) AS total 
             FROM promociones p
             INNER JOIN locales l ON p.codLocal = l.codLocal
             INNER JOIN usuarios u ON l.codDueno = u.codUsuario
             WHERE 1=1 
             $filtroEstado 
             $filtroCategoria 
             $filtroBusqueda";
$resultCount = consultaSQL($sqlCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$total = $rowCount['total'] ?? 0;
$totalPaginas = max(1, ceil($total / $porPagina));


$sqlPromociones = "SELECT 
                    p.*,
                    l.nombreLocal,
                    l.categoriaLocal,
                    u.nombre as nombreDueno,
                    u.nombreUsuario as emailDueno
                   FROM promociones p
                   INNER JOIN locales l ON p.codLocal = l.codLocal
                   INNER JOIN usuarios u ON l.codDueno = u.codUsuario
                   WHERE 1=1
                   $filtroEstado
                   $filtroCategoria
                   $filtroBusqueda
                   ORDER BY p.fechaDesdePromo DESC
                   LIMIT $porPagina OFFSET $offset";
$resultPromociones = consultaSQL($sqlPromociones);


function obtenerSolicitudesPromo($codPromo){
    $sql = "SELECT 
                up.*,
                u.nombre as nombreCliente,
                u.nombreUsuario as emailCliente,
                u.categoriaCliente
            FROM uso_promociones up
            INNER JOIN usuarios u ON up.codCliente = u.codUsuario
            WHERE up.codPromo = '$codPromo'
            ORDER BY up.fechaUsoPromo DESC";
    return consultaSQL($sql);
}


function contarSolicitudes($codPromo, $estado = null){
    $filtro = $estado ? " AND estado='$estado'" : "";
    $sql = "SELECT COUNT(*) as total FROM uso_promociones WHERE codPromo='$codPromo' $filtro";
    $result = consultaSQL($sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}


if(isset($_POST['cambiarEstadoPromo'])){
    $codPromo = $_POST['codPromo'];
    $nuevoEstado = $_POST['nuevoEstado'];
    $sqlUpdate = "UPDATE promociones SET estadoPromo='$nuevoEstado' WHERE codPromo='$codPromo'";
    if(consultaSQL($sqlUpdate)){
        $_SESSION['mensaje_exito'] = "Estado de la promoción actualizado correctamente.";
    } else {
        $_SESSION['mensaje_error'] = "Error al actualizar el estado.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


if(isset($_POST['cambiarEstadoSolicitud'])){
    $codUsoPromo = $_POST['codUsoPromo'];
    $nuevoEstado = $_POST['nuevoEstadoSolicitud'];
    $sqlUpdate = "UPDATE uso_promociones SET estado='$nuevoEstado' WHERE codUsoPromo='$codUsoPromo'";
    if(consultaSQL($sqlUpdate)){
        $_SESSION['mensaje_exito'] = "Estado de la solicitud actualizado correctamente.";
    } else {
        $_SESSION['mensaje_error'] = "Error al actualizar el estado.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


$totalPromos = mysqli_num_rows(consultaSQL("SELECT * FROM promociones"));
$promosAprobadas = mysqli_num_rows(consultaSQL("SELECT * FROM promociones WHERE estadoPromo='aprobada'"));
$promosPendientes = mysqli_num_rows(consultaSQL("SELECT * FROM promociones WHERE estadoPromo='pendiente'"));
$totalSolicitudes = mysqli_num_rows(consultaSQL("SELECT * FROM uso_promociones"));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Promociones - Admin</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../Estilos/estilos.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    .promo-card {
        border-left: 4px solid var(--color-dorado);
        margin-bottom: 20px;
    }
    
    .cliente-item {
        background: #f8f9fa;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 5px;
        border-left: 3px solid #6c757d;
    }
    
    .badge-aprobada { background-color: #28a745; }
    .badge-pendiente { background-color: #ffc107; color: #000; }
    .badge-rechazada { background-color: #dc3545; }
    .badge-enviada { background-color: #17a2b8; }
    .badge-aceptada { background-color: #28a745; }
    
    .stat-card {
        border-left: 4px solid var(--color-dorado-oscuro);
    }
    
    .info-local {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
</style>
<body class="d-flex flex-column min-vh-100">

    <!-- CONTENEDOR PRINCIPAL -->
    <main class="container-fluid my-4 flex-grow-1">
        
 
        <div class="row mb-4">
            <div class="col-12">
                <h2 style="color: var(--color-negro);">
                    <i class="bi bi-clipboard-check me-2" style="color: var(--color-dorado-oscuro);"></i>
                    Gestión de Promociones
                </h2>
                <p class="text-muted">Panel de administración de todas las promociones del sistema</p>
            </div>
        </div>


        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted">Total Promociones</h6>
                        <h3 class="mb-0"><?php echo $totalPromos; ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted">Aprobadas</h6>
                        <h3 class="mb-0 text-success"><?php echo $promosAprobadas; ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted">Pendientes Aprobación</h6>
                        <h3 class="mb-0 text-warning"><?php echo $promosPendientes; ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted">Total Solicitudes</h6>
                        <h3 class="mb-0 text-info"><?php echo $totalSolicitudes; ?></h3>
                    </div>
                </div>
            </div>
        </div>

 
        <div class="row">
            <div class="col-12">
                <?php if(mysqli_num_rows($resultPromociones) > 0): ?>
                    <?php while($promo = mysqli_fetch_assoc($resultPromociones)): 
                        $vencida = ($promo['fechaHastaPromo'] < $hoy);
                        $solicitudes = obtenerSolicitudesPromo($promo['codPromo']);
                        $totalSolicitudesPromo = mysqli_num_rows($solicitudes);
                    ?>
                    
                    <div class="card promo-card">
                        <div class="card-header" style="background: linear-gradient(135deg, var(--color-dorado), var(--color-dorado-oscuro));">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="mb-0">
                                        <i class="bi bi-tag-fill me-2"></i>
                                        Promoción PR-<?php echo $promo['codPromo']; ?>
                                    </h5>
                                </div>
                                <div class="col-md-4 text-end">
                                    <?php
                                    $badgeClass = '';
                                    $icon = '';
                                    switch($promo['estadoPromo']){
                                        case 'aprobada':
                                            $badgeClass = 'badge-aprobada';
                                            $icon = 'check-circle';
                                            break;
                                        case 'pendiente':
                                            $badgeClass = 'badge-pendiente';
                                            $icon = 'clock';
                                            break;
                                        case 'rechazada':
                                            $badgeClass = 'badge-rechazada';
                                            $icon = 'x-circle';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?> fs-6">
                                        <i class="bi bi-<?php echo $icon; ?>"></i>
                                        <?php echo ucfirst($promo['estadoPromo']); ?>
                                    </span>
                                    <?php if($vencida): ?>
                                        <span class="badge bg-danger fs-6">
                                            <i class="bi bi-calendar-x"></i> Vencida
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                          
                            <div class="info-local">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary">
                                            <i class="bi bi-shop"></i> Información del Local
                                        </h6>
                                        <p class="mb-1"><strong>Local:</strong> <?php echo $promo['nombreLocal']; ?></p>
                                        <p class="mb-1"><strong>Categoría:</strong> 
                                            <span class="badge bg-info"><?php echo ucfirst($promo['categoriaLocal']); ?></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-success">
                                            <i class="bi bi-person-circle"></i> Dueño del Local
                                        </h6>
                                        <p class="mb-1"><strong>Nombre:</strong> <?php echo $promo['nombreDueno']; ?></p>
                                        <p class="mb-1"><strong>Email:</strong> <?php echo $promo['emailDueno']; ?></p>
                                    </div>
                                </div>
                            </div>

       
                            <div class="row mt-3">
                                <div class="col-md-8">
                                    <h6 class="text-dark">
                                        <i class="bi bi-card-text"></i> Detalles de la Promoción
                                    </h6>
                                    <p><strong>Descripción:</strong> <?php echo $promo['textoPromo']; ?></p>
                                    <p class="mb-1">
                                        <strong>Categoría Promoción:</strong> 
                                        <span class="badge bg-secondary"><?php echo ucfirst($promo['categoriaPromo']); ?></span>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Categoría Cliente:</strong> 
                                        <span class="badge bg-warning text-dark"><?php echo ucfirst($promo['categoriaCliente']); ?></span>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Días habilitados:</strong> <?php echo $promo['diasSemana']; ?>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-dark">
                                        <i class="bi bi-calendar-range"></i> Vigencia
                                    </h6>
                                    <p class="mb-1">
                                        <i class="bi bi-calendar-check text-success"></i> 
                                        <strong>Desde:</strong> <?php echo date('d/m/Y', strtotime($promo['fechaDesdePromo'])); ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="bi bi-calendar-x text-danger"></i> 
                                        <strong>Hasta:</strong> <?php echo date('d/m/Y', strtotime($promo['fechaHastaPromo'])); ?>
                                    </p>
                                </div>
                            </div>

                   
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-dark mb-3">
                                        <i class="bi bi-people-fill"></i> 
                                        Solicitudes de Clientes 
                                        <span class="badge bg-info"><?php echo $totalSolicitudesPromo; ?></span>
                                    </h6>
                                    
                                    <?php if($totalSolicitudesPromo > 0): ?>
                                        <div class="row">
                                            <?php while($solicitud = mysqli_fetch_assoc($solicitudes)): ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="cliente-item">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <p class="mb-1">
                                                                <strong><i class="bi bi-person"></i> <?php echo $solicitud['nombreCliente']; ?></strong>
                                                            </p>
                                                            <p class="mb-1 small">
                                                                <i class="bi bi-envelope"></i> <?php echo $solicitud['emailCliente']; ?>
                                                            </p>
                                                            
                                                            <p class="mb-1 small">
                                                                <i class="bi bi-star"></i> 
                                                                <span class="badge bg-secondary"><?php echo ucfirst($solicitud['categoriaCliente']); ?></span>
                                                            </p>
                                                            <p class="mb-1 small text-muted">
                                                                <i class="bi bi-calendar"></i> Solicitada: <?php echo date('d/m/Y', strtotime($solicitud['fechaUsoPromo'])); ?>
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <?php
                                                            $estadoBadge = '';
                                                            switch($solicitud['estado']){
                                                                case 'enviada':
                                                                    $estadoBadge = 'badge-enviada';
                                                                    break;
                                                                case 'aceptada':
                                                                    $estadoBadge = 'badge-aceptada';
                                                                    break;
                                                                case 'rechazada':
                                                                    $estadoBadge = 'badge-rechazada';
                                                                    break;
                                                            }
                                                            ?>
                                                            <span class="badge <?php echo $estadoBadge; ?>">
                                                                <?php echo ucfirst($solicitud['estado']); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endwhile; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted text-center py-3">
                                            <i class="bi bi-inbox"></i> No hay solicitudes para esta promoción
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php endwhile; ?>

                  
                    <?php if($totalPaginas > 1): ?>
                    <nav aria-label="Paginación" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo max(1, $pagina - 1); ?><?php echo isset($_GET['estado']) ? '&estado='.$_GET['estado'] : ''; ?><?php echo isset($_GET['categoria']) ? '&categoria='.$_GET['categoria'] : ''; ?><?php echo isset($_GET['busqueda']) ? '&busqueda='.$_GET['busqueda'] : ''; ?>">
                                    Anterior
                                </a>
                            </li>

                            <?php for($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $i; ?><?php echo isset($_GET['estado']) ? '&estado='.$_GET['estado'] : ''; ?><?php echo isset($_GET['categoria']) ? '&categoria='.$_GET['categoria'] : ''; ?><?php echo isset($_GET['busqueda']) ? '&busqueda='.$_GET['busqueda'] : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>

                            <li class="page-item <?php echo ($pagina >= $totalPaginas) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo min($totalPaginas, $pagina + 1); ?><?php echo isset($_GET['estado']) ? '&estado='.$_GET['estado'] : ''; ?><?php echo isset($_GET['categoria']) ? '&categoria='.$_GET['categoria'] : ''; ?><?php echo isset($_GET['busqueda']) ? '&busqueda='.$_GET['busqueda'] : ''; ?>">
                                    Siguiente
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>

                <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: var(--color-gris);"></i>
                        <h5 class="mt-3 text-muted">No se encontraron promociones</h5>
                        <p class="text-muted">Intenta con otros filtros de búsqueda</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </main>

    <?php if(isset($_SESSION['mensaje_exito'])): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: '<?php echo $_SESSION['mensaje_exito']; ?>',
        confirmButtonColor: '#DAB561'
    });
    </script>
    <?php 
        unset($_SESSION['mensaje_exito']); 
    endif; 
    ?>

    <?php if(isset($_SESSION['mensaje_error'])): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?php echo $_SESSION['mensaje_error']; ?>',
        confirmButtonColor: '#DAB561'
    });
    </script>
    <?php 
        unset($_SESSION['mensaje_error']); 
    endif; 
    ?>

</body>
</html>
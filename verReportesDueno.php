<?php
    
if(!isset($_SESSION['usuario'])){
    $_SESSION['mensaje_warning'] = 'Debes iniciar sesión para acceder a esta página.';
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit();
}

if($_SESSION['tipoUsuario'] != 'dueno de local'){
    $_SESSION['mensaje_error'] = 'Esta página es solo para dueños de local.';
    header('Location: index.php');
    exit();
}

$hoy = date('Y-m-d');

$emailUsu = $_SESSION['usuario'];
$sqlDueno = "SELECT codUsuario FROM usuarios WHERE nombreUsuario='$emailUsu'";
$resultadoDueno = consultaSQL($sqlDueno);
$dueno = mysqli_fetch_assoc($resultadoDueno);
$codDueno = $dueno['codUsuario'];

$sqlLocal = "SELECT codLocal, nombreLocal FROM locales WHERE codDueno='$codDueno'";
$resultLocal = consultaSQL($sqlLocal);
$local = mysqli_fetch_assoc($resultLocal);
if(isset($local['codLocal'])){
    $codLocal = $local['codLocal'];
    $nombreLocal = $local['nombreLocal'];
} else {
    ?>
    <style>
        .alert-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #f59e0b;
        }

        .alert-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .icon {
            width: 50px;
            height: 50px;
            background: #fef3c7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        h1 {
            color: #92400e;
            font-size: 20px;
            font-weight: 700;
        }

        .message {
            color: #4b5563;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
            padding-left: 65px;
        }

        .contact-link {
            display: inline-flex;
            align-items: center;
            color: #3b82f6;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: 10px 18px;
            border-radius: 8px;
            transition: all 0.2s ease;
            background: #eff6ff;
            margin-left: 65px;
        }

        .contact-link:hover {
            background: #dbeafe;
            color: #2563eb;
        }

        .contact-link svg {
            margin-left: 6px;
            width: 16px;
            height: 16px;
        }
        
    </style>
    <div class="alert-container d-flex flex-column justify-content-center align-items-start mx-auto mt-5">
        <div class="alert-header">
            <div class="icon">⚠️</div>
            <h1>Local no encontrado</h1>
        </div>
        <p class="message">
            No tienes un local asociado a tu cuenta. Por favor, crea un local primero para continuar operando en el sistema.
        </p>
        <a href="contacto.php" class="contact-link">
            Contactarte con administrador
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    <?php
    exit();
}

$porPagina = 4;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $porPagina;


$filtroEstado = '';
if(isset($_GET['estado']) && !empty($_GET['estado'])){
    $estadoFiltro = $_GET['estado'];
    $filtroEstado = " AND estadoPromo='$estadoFiltro'";
}


$sqlCount = "SELECT COUNT(*) AS total 
             FROM promociones 
             WHERE codLocal='$codLocal' 
             $filtroEstado";
$resultCount = consultaSQL($sqlCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$total = $rowCount['total'] ?? 0;
$totalPaginas = max(1, ceil($total / $porPagina));


$sqlPromociones = "SELECT * 
                   FROM promociones 
                   WHERE codLocal='$codLocal'
                   $filtroEstado
                   ORDER BY fechaDesdePromo DESC
                   LIMIT $porPagina OFFSET $offset";
$resultPromociones = consultaSQL($sqlPromociones);


function contarSolicitudes($codPromo, $estado = null){
    $filtro = $estado ? " AND estado='$estado'" : "";
    $sql = "SELECT COUNT(*) as total FROM uso_promociones WHERE codPromo='$codPromo' $filtro";
    $result = consultaSQL($sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Promociones - <?php echo $nombreLocal; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="../Estilos/estilos.css">
    
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
    .badge-aprobada { background-color: #28a745; }
    .badge-pendiente { background-color: #ff7707ff; color: #000; }
    .badge-rechazada { background-color: #dc3545; }
    .badge-finalizada { background-color: #6c757d; }
    
    .promo-card {
        transition: transform 0.2s;
        border-left: 4px solid var(--color-dorado);
    }
    .promo-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .stat-card {
        border-left: 4px solid var(--color-dorado-oscuro);
    }
    .btn-primary {
            background: linear-gradient(135deg, var(--color-dorado) 0%, var(--color-dorado-oscuro) 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            transition: all 0.3s 
        ease;
            color: #333;
    }
    .btn-primary {
            --bs-btn-hover-bg: var(--color-dorado-oscuro);
    }
    .text-warning {
    
    color: #ff7707ff !important;
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
</style>
</head>

<body class="d-flex flex-column min-vh-100">



    <!-- CONTENEDOR PRINCIPAL -->
    <main class="container my-4 flex-grow-1" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        
        <!-- Encabezado -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0" style="color: var(--color-negro);">
                            <i class="bi bi-shop me-2" style="color: var(--color-dorado-oscuro);"></i>
                            <?php echo $nombreLocal; ?>
                        </h2>
                        <p class="text-muted mb-0">Gestión de Promociones</p>
                    </div>
                    
                </div>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="row mb-4">
            <?php
            $totalPromos = mysqli_num_rows(consultaSQL("SELECT * FROM promociones WHERE codLocal='$codLocal'"));
            $promosAprobadas = mysqli_num_rows(consultaSQL("SELECT * FROM promociones WHERE codLocal='$codLocal' AND estadoPromo='aprobada'"));
            $promosPendientes = mysqli_num_rows(consultaSQL("SELECT * FROM promociones WHERE codLocal='$codLocal' AND estadoPromo='pendiente'"));
            $solicitudesPendientes = mysqli_num_rows(consultaSQL("SELECT * FROM uso_promociones up INNER JOIN promociones p ON up.codPromo = p.codPromo WHERE p.codLocal='$codLocal' AND up.estado='enviada'"));
            ?>
            
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
                        <h6 class="text-muted">Solicitudes Clientes Pendientes</h6>
                        <h3 class="mb-0 text-info"><?php echo $solicitudesPendientes; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de promociones -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="bi bi-list-ul me-2"></i>Mis Promociones
                            <span class="badge bg-secondary"><?php echo $total; ?></span>
                        </h5>

                        <?php if(mysqli_num_rows($resultPromociones) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead style="background: linear-gradient(135deg, var(--color-dorado), var(--color-dorado-oscuro)); color: var(--color-negro);">
                                    <tr>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th>Categoría</th>
                                        <th>Vigencia</th>
                                        <th>Días</th>
                                        <th>Cliente</th>
                                        <th>Estado</th>
                                        <th>Solicitudes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($promo = mysqli_fetch_assoc($resultPromociones)): 
                                        $solicitudesEnviadas = contarSolicitudes($promo['codPromo'], 'enviada');
                                        $solicitudesAceptadas = contarSolicitudes($promo['codPromo'], 'aceptada');
                                        $solicitudesTotal = contarSolicitudes($promo['codPromo']);
                                        $vencida = ($promo['fechaHastaPromo'] < $hoy);
                                    ?>
                                    <tr>
                                        <td>
                                            <strong>PR-<?php echo $promo['codPromo']; ?></strong>
                                        </td>
                                        <td>
                                            <div style="max-width: 300px;">
                                                <?php echo $promo['textoPromo']; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo ucfirst($promo['categoriaPromo']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small>
                                                <i class="bi bi-calendar-check"></i> <?php echo date('d/m/Y', strtotime($promo['fechaDesdePromo'])); ?><br>
                                                <i class="bi bi-calendar-x"></i> <?php echo date('d/m/Y', strtotime($promo['fechaHastaPromo'])); ?>
                                                <?php if($vencida): ?>
                                                    <br><span class="badge bg-danger">Vencida</span>
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small><?php echo $promo['diasSemana']; ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo ucfirst($promo['categoriaCliente']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = '';
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
                                                default:
                                                    $badgeClass = 'bg-secondary';
                                                    $icon = 'question-circle';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <i class="bi bi-<?php echo $icon; ?>"></i>
                                                <?php echo ucfirst($promo['estadoPromo']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark" title="Pendientes">
                                                <i class="bi bi-clock"></i> <?php echo $solicitudesEnviadas; ?>
                                            </span>
                                            <span class="badge bg-success" title="Aceptadas">
                                                <i class="bi bi-check"></i> <?php echo $solicitudesAceptadas; ?>
                                            </span>
                                            <br>
                                            <small class="text-muted">Total: <?php echo $solicitudesTotal; ?></small>
                                        </td>

                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <?php if($totalPaginas > 0): ?>
                        <nav aria-label="Paginación" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?accion=verReportes&pagina=<?php echo max(1, $pagina - 1); ?><?php echo isset($_GET['estado']) ? '&estado='.$_GET['estado'] : ''; ?>">
                                        Anterior
                                    </a>
                                </li>

                                <?php for($i = 1; $i <= $totalPaginas; $i++): ?>
                                <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?accion=verReportes&pagina=<?php echo $i; ?><?php echo isset($_GET['estado']) ? '&estado='.$_GET['estado'] : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                                <?php endfor; ?>

                                <li class="page-item <?php echo ($pagina >= $totalPaginas) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?accion=verReportes&pagina=<?php echo min($totalPaginas, $pagina + 1); ?><?php echo isset($_GET['estado']) ? '&estado='.$_GET['estado'] : ''; ?>">
                                        Siguiente
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>

                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; color: var(--color-gris);"></i>
                            <h5 class="mt-3 text-muted">No tienes promociones creadas</h5>
                            <p class="text-muted">Comienza creando tu primera promoción para atraer más clientes</p>
                            <a href="crearPromocion.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Crear mi primera promoción
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </main>

</body>
</html>
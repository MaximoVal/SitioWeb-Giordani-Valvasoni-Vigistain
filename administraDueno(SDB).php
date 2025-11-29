<?php
session_start();
include('funciones.php');
$hoy= date('Y-m-d');
$tresSemanas = date('Y-m-d', strtotime('+21 days'));
$emailUsu=$_SESSION['usuario'];

$sqlDataDueno="SELECT * FROM usuarios WHERE tipoUsuario='dueno de local' AND nombreUsuario='$emailUsu'";
$dataDueno= consultaSQL($sqlDataDueno);
$dueno= mysqli_fetch_assoc($dataDueno);
$codDueno= $dueno['codUsuario'];

if($dueno['localNoLocal']!='no'){
    $sqlBuscaLocalDueno="SELECT * FROM locales WHERE codDueno='$codDueno'";
    $dataLocalDueno=consultaSQL($sqlBuscaLocalDueno);
    $localDueno=mysqli_fetch_assoc($dataLocalDueno);
}else{
    $localDueno=[];
}

$accion = isset($_GET['accion']) ? $_GET['accion'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Dueño</title>
    
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


        .dashboard-card {
            border: 1px solid rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
            border-color: var(--color-dorado);
        }

        .card-icon {
            color: var(--color-verde); 
            font-size: 2.5rem;
        }
        

        .text-label {
            font-weight: 700;
            color: var(--color-verde); 
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <?php include('navDueño.php'); ?>

    <main class="container-fluid my-4">
        <div class="row">
            
            <aside class="col-md-3 col-lg-2 mb-4" aria-label="Menú lateral de administración">
                
                <div class="d-md-none mb-3 mt-3 d-flex flex-wrap gap-2">
                    <?php if(isset($accion)){ ?>
                    <button class="btn btn-mobile-nav flex-fill d-flex align-items-center justify-content-center gap-2 py-2" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#panelAdmin" 
                            aria-expanded="false" 
                            aria-controls="panelAdmin">
                        <i class="bi bi-gear-fill" aria-hidden="true"></i>
                        <span>Panel</span>
                    </button>
                    <?php } ?>
                    
                    <button class="btn btn-mobile-nav flex-fill d-flex align-items-center justify-content-center gap-2 py-2" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#infoLocal" 
                            aria-expanded="false" 
                            aria-controls="infoLocal">
                        <i class="bi bi-shop" aria-hidden="true"></i>
                        <span>Local</span>
                    </button>
                    
                    <button class="btn btn-mobile-nav flex-fill d-flex align-items-center justify-content-center gap-2 py-2" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#infoDueno" 
                            aria-expanded="false" 
                            aria-controls="infoDueno">
                        <i class="bi bi-person-vcard" aria-hidden="true"></i>
                        <span>Dueño</span>
                    </button>
                </div>

                <?php if(isset($accion)){ ?>
                <nav class="sidebar-box mb-3 p-3 collapse d-md-block" id="panelAdmin">
                    <h2 class="h5 mb-3 d-flex align-items-center text-negro fw-bold">
                        <i class="bi bi-gear-fill me-2 text-verde" aria-hidden="true"></i>
                        Administración
                    </h2>
                    <ul class="list-unstyled m-0">
                        <li class="mb-2">
                            <a href="administraDueno(SDB).php?accion=adminPromos" 
                               class="text-decoration-none d-flex align-items-center py-2 px-3 rounded-2 nav-link-admin <?php echo ($accion == 'adminPromos') ? 'active' : ''; ?>"
                               <?php echo ($accion == 'adminPromos') ? 'aria-current="page"' : ''; ?>>
                                <i class="bi bi-tag-fill me-2" aria-hidden="true"></i>
                                Administrar Promociones
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="administraDueno(SDB).php?accion=verSolicitudesDueno" 
                               class="text-decoration-none d-flex align-items-center py-2 px-3 rounded-2 nav-link-admin <?php echo ($accion == 'verSolicitudesDueno') ? 'active' : ''; ?>"
                               <?php echo ($accion == 'verSolicitudesDueno') ? 'aria-current="page"' : ''; ?>>
                                <i class="bi bi-check-square me-2" aria-hidden="true"></i>
                                Solicitudes
                            </a>
                        </li>
                        <li>
                            <a href="administraDueno(SDB).php?accion=verReportes" 
                               class="text-decoration-none d-flex align-items-center py-2 px-3 rounded-2 nav-link-admin <?php echo ($accion == 'verReportes') ? 'active' : ''; ?>"
                               <?php echo ($accion == 'verReportes') ? 'aria-current="page"' : ''; ?>>
                                <i class="bi bi-file-earmark-bar-graph me-2" aria-hidden="true"></i>
                                Reportes
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php } ?>
                
                <section class="sidebar-box p-3 mb-3 collapse d-md-block" id="infoLocal" aria-labelledby="headingLocal">
                    <h2 class="h5 mb-3 d-flex align-items-center text-negro fw-bold" id="headingLocal">
                        <i class="bi bi-shop me-2 text-verde" aria-hidden="true"></i> 
                        Datos del Local
                    </h2>
                    
                    <?php if($dueno['localNoLocal']!='no'){ ?>
                        <div class="mb-2">
                            <p class="mb-0 text-label">Código:</p>
                            <p class="text-negro m-0"><?php echo $localDueno['codLocal']; ?></p>
                        </div>
                        <div class="mb-2">
                            <p class="mb-0 text-label">Nombre:</p>
                            <p class="text-negro m-0"><?php echo $localDueno['nombreLocal']; ?></p>
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
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
                            <div>
                                <strong>Atención:</strong> No posee local registrado.
                            </div>
                        </div>
                    <?php } ?>
                </section>

                <section class="sidebar-box p-3 collapse d-md-block" id="infoDueno" aria-labelledby="headingDueno">
                    <h2 class="h5 mb-3 d-flex align-items-center text-negro fw-bold" id="headingDueno">
                        <i class="bi bi-person-vcard me-2 text-verde" aria-hidden="true"></i> 
                        Datos del Dueño
                    </h2>
                    <div class="mb-2">
                        <p class="mb-0 text-label">Código:</p>
                        <p class="text-negro m-0"><?php echo $dueno['codUsuario']; ?></p>
                    </div>
                    <div class="mb-2">
                        <p class="mb-0 text-label">Nombre completo:</p>
                        <p class="text-negro m-0"><?php echo $dueno['nombre'] . ' ' . $dueno['apellido']; ?></p>
                    </div>
                    <div>
                        <p class="mb-0 text-label">Email:</p>
                        <p class="text-negro m-0" style="word-break: break-all;"><?php echo $dueno['nombreUsuario']; ?></p>
                    </div>
                </section>
            </aside>

            <section class="col-md-9 col-lg-10" role="main">
                <?php if(isset($accion)){

                    if($accion=='adminPromos' && file_exists('administrarPromocionesDueno.php')){
                        include('administrarPromocionesDueno.php');
                    } elseif($accion=='verSolicitudesDueno' && file_exists('verSolicitudesDueno.php')){
                        include('verSolicitudesDueno.php');
                    } elseif($accion=='verReportes' && file_exists('verReportesDueno.php')){
                        include('verReportesDueno.php');
                    } else {
                        echo '<div class="alert alert-danger">Opción no válida o archivo no encontrado.</div>';
                    }
                } else { ?>
                
                <div class="p-4 p-md-5 bg-white shadow-sm rounded-3" style="border-top: 5px solid var(--color-dorado);">
                    <div class="text-center mb-5">
                        <i class="bi bi-gear-fill" style="font-size: 3rem; color: var(--color-verde);" aria-hidden="true"></i>
                        <h1 class="h3 mt-3 mb-2 fw-bold text-negro">Panel de Administración</h1>
                        <p class="text-muted fs-5">Selecciona una opción para comenzar a gestionar tu negocio</p>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-4">
                            <a href="administraDueno(SDB).php?accion=adminPromos" class="text-decoration-none h-100 d-block group">
                                <article class="card dashboard-card h-100 bg-white">
                                    <div class="card-body text-center p-4">
                                        <i class="bi bi-tag-fill mb-3 card-icon" aria-hidden="true"></i>
                                        <h2 class="h5 card-title mb-2 text-negro fw-bold">Promociones</h2>
                                        <p class="card-text text-secondary small">Crear, editar y eliminar promociones activas.</p>
                                        <span class="btn btn-sm mt-2" style="background-color: var(--color-dorado); color: var(--color-negro);">Gestionar</span>
                                    </div>
                                </article>
                            </a>
                        </div>
                        
                        <div class="col-md-4">
                            <a href="administraDueno(SDB).php?accion=verSolicitudesDueno" class="text-decoration-none h-100 d-block">
                                <article class="card dashboard-card h-100 bg-white">
                                    <div class="card-body text-center p-4">
                                        <i class="bi bi-check-square mb-3 card-icon" aria-hidden="true"></i>
                                        <h2 class="h5 card-title mb-2 text-negro fw-bold">Solicitudes</h2>
                                        <p class="card-text text-secondary small">Revisar estado de aprobaciones pendientes.</p>
                                        <span class="btn btn-sm mt-2" style="background-color: var(--color-dorado); color: var(--color-negro);">Ver estado</span>
                                    </div>
                                </article>
                            </a>
                        </div>
                        
                        <div class="col-md-4">
                            <a href="administraDueno(SDB).php?accion=verReportes" class="text-decoration-none h-100 d-block">
                                <article class="card dashboard-card h-100 bg-white">
                                    <div class="card-body text-center p-4">
                                        <i class="bi bi-file-earmark-bar-graph mb-3 card-icon" aria-hidden="true"></i>
                                        <h2 class="h5 card-title mb-2 text-negro fw-bold">Reportes</h2>
                                        <p class="card-text text-secondary small">Analizar estadísticas y métricas de desempeño.</p>
                                        <span class="btn btn-sm mt-2" style="background-color: var(--color-dorado); color: var(--color-negro);">Consultar</span>
                                    </div>
                                </article>
                            </a>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </section>
        </div>
    </main>

    <?php include 'footer.php'; ?>



</body>
</html>
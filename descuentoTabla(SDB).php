<?php
session_set_cookie_params(0);
session_start();
$hoy = date('Y-m-d');

include('funciones.php');
$tipoUsuario = '';
    if(isset($_SESSION['usuario'])){
    $emailUsu = $_SESSION['usuario'];
    $sqlCategoriaCliente = "SELECT * FROM usuarios WHERE nombreUsuario='$emailUsu'";
    $resultadoCategoria = consultaSQL($sqlCategoriaCliente);
    $rc = mysqli_fetch_assoc($resultadoCategoria);
    $resultadoCat = $rc['categoriaCliente'];
    $codCliente = $rc['codUsuario'];
    $tipoUsuario = $rc['tipoUsuario'];

    if($tipoUsuario == 'dueno de local' || $tipoUsuario == 'administrador'){
        $resultadoCat = 'Premium'; 
    }
} else {
     $resultadoCat = 'Premium'; 
}

$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

if(isset($resultadoCat)){

    $porPagina = 7;
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    if ($pagina < 1) $pagina = 1;

    $offset = ($pagina - 1) * $porPagina;

    
    $filtroCategoria = '';
    if(!empty($categoria)){
        $filtroCategoria = " AND categoriaPromo='$categoria'";
    }


    if($resultadoCat == 'Inicial'){

        $sqlCount = "SELECT COUNT(*) AS total 
                    FROM promociones p
                    WHERE p.estadoPromo='aprobada' 
                    AND p.categoriaCliente='Inicial'
                    AND p.fechaHastaPromo >= '$hoy'
                    AND NOT EXISTS (
                        SELECT 1 
                        FROM uso_promociones up 
                        WHERE up.codPromo = p.codPromo 
                        AND up.codCliente = '$codCliente'
                    )
                    $filtroCategoria";

        $resultCount = consultaSQL($sqlCount);
        $rowCount = mysqli_fetch_assoc($resultCount);
        $total = $rowCount['total'] ?? 0;

        $totalPaginas = max(1, ceil($total / $porPagina));

        $sqlPromosCategoricas = "SELECT p.* 
                                FROM promociones p
                                WHERE p.estadoPromo='aprobada' 
                                AND p.categoriaCliente='Inicial'
                                AND p.fechaHastaPromo >= '$hoy'
                                AND NOT EXISTS (
                                    SELECT 1 
                                    FROM uso_promociones up 
                                    WHERE up.codPromo = p.codPromo 
                                    AND up.codCliente = '$codCliente'
                                )
                                $filtroCategoria
                                ORDER BY p.fechaDesdePromo ASC
                                LIMIT $porPagina OFFSET $offset";


    } elseif($resultadoCat == 'Medium') {

        $sqlCount = "SELECT COUNT(*) AS total 
                    FROM promociones p
                    WHERE p.estadoPromo='aprobada' 
                    AND (p.categoriaCliente='Inicial' OR p.categoriaCliente='Medium')
                    AND p.fechaHastaPromo >= '$hoy'
                    AND NOT EXISTS (
                        SELECT 1 
                        FROM uso_promociones up 
                        WHERE up.codPromo = p.codPromo 
                        AND up.codCliente = '$codCliente'
                    )
                    $filtroCategoria";

        $resultCount = consultaSQL($sqlCount);
        $rowCount = mysqli_fetch_assoc($resultCount);
        $total = $rowCount['total'] ?? 0;

        $totalPaginas = max(1, ceil($total / $porPagina));

        $sqlPromosCategoricas = "SELECT p.* 
                                FROM promociones p
                                WHERE p.estadoPromo='aprobada' 
                                AND (p.categoriaCliente='Inicial' OR p.categoriaCliente='Medium')
                                AND p.fechaHastaPromo >= '$hoy'
                                AND NOT EXISTS (
                                    SELECT 1 
                                    FROM uso_promociones up 
                                    WHERE up.codPromo = p.codPromo 
                                    AND up.codCliente = '$codCliente'
                                )
                                $filtroCategoria
                                ORDER BY p.fechaDesdePromo ASC
                                LIMIT $porPagina OFFSET $offset";


    } else {

        $sqlCount = "SELECT COUNT(*) AS total 
                    FROM promociones p
                    WHERE p.estadoPromo='aprobada' 
                    AND p.fechaHastaPromo >= '$hoy'
                    $filtroCategoria";

        $resultCount = consultaSQL($sqlCount);
        $rowCount = mysqli_fetch_assoc($resultCount);
        $total = $rowCount['total'] ?? 0;

        $totalPaginas = max(1, ceil($total / $porPagina));

        if($tipoUsuario == 'dueno de local' || $tipoUsuario == 'administrador' || !isset($_SESSION['usuario'])){
            $sqlPromosCategoricas = "SELECT p.* 
                                    FROM promociones p
                                    WHERE p.estadoPromo='aprobada' 
                                    AND p.fechaHastaPromo >= '$hoy'
                                    $filtroCategoria
                                    ORDER BY p.fechaDesdePromo ASC
                                    LIMIT $porPagina OFFSET $offset";
        } else {
       
            $sqlPromosCategoricas = "SELECT p.* 
                                    FROM promociones p
                                    WHERE p.estadoPromo='aprobada' 
                                    AND p.fechaHastaPromo >= '$hoy'
                                    AND NOT EXISTS (
                                        SELECT 1 
                                        FROM uso_promociones up 
                                        WHERE up.codPromo = p.codPromo 
                                        AND up.codCliente = '$codCliente'
                                    )
                                    $filtroCategoria
                                    ORDER BY p.fechaDesdePromo ASC
                                    LIMIT $porPagina OFFSET $offset";
        }
    }

    $resultPromosTotales = consultaSQL($sqlPromosCategoricas);
}

function obtenNombreLocal($codLocal){
    $sqlObtenLocal = "SELECT nombreLocal FROM locales WHERE codLocal='$codLocal'";
    $result = consultaSQL($sqlObtenLocal);
    $nombre = mysqli_fetch_assoc($result);
    return $nombre['nombreLocal'];
}

function verificaPromoSoli($promoCod, $cliente){
    $sqlVerificacion = "SELECT * FROM uso_promociones WHERE codPromo='$promoCod' and codCliente='$cliente'";
    $resultadoVerificacion = consultaSQL($sqlVerificacion);
    return mysqli_num_rows($resultadoVerificacion);
}

if(isset($_POST['solicitarPromo'])){
    $codPromo = $_POST['codPromo'];
    $estadoInicial = "enviada";
    if(verificaPromoSoli($codPromo, $codCliente) == 0){    
        $sqlSolicitaPromo = "INSERT INTO uso_promociones (codCliente, codPromo, fechaUsoPromo, estado) VALUES ('$codCliente', '$codPromo', '$hoy', '$estadoInicial')";
        consultaSQL($sqlSolicitaPromo);
        $_SESSION['solicitud_ok'] = "La promoción se solicitó correctamente.";
    } else {
        $_SESSION['solicitudHecha_ok'] = "La promoción ya fue solicitada anteriormente por usted.";
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?categoria=$categoria&pagina=$pagina");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tabla de promociones y descuentos disponibles por categoría en Paseo de la Fortuna">
    <title>Promociones por Categoría - Paseo de la Fortuna</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../Estilos/adiministraDuenoEstilos.css">
    <link rel="stylesheet" href="../Estilos/estilos.css">

    <!-- Íconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
    :root{
        --color-dorado: #EED284;
        --color-dorado-oscuro: #DAB561;
        --color-negro: #333333;
        --color-blanco: #FFFFFF;
        --color-verde: #355B38;
        --color-gris: #666666;
    }


    .skip-link {
        position: absolute;
        top: -40px;
        left: 0;
        background: #000;
        color: #fff;
        padding: 8px 16px;
        text-decoration: none;
        z-index: 9999;
        font-weight: bold;
    }
    
    .skip-link:focus {
        top: 0;
    }


    *:focus {
        outline: 3px solid #0066cc;
        outline-offset: 2px;
    }


    .list-group-item.active {
        background-color: #DAB561 !important;
        border-color: #DAB561 !important;
        color: #000000 !important;
        font-weight: bold;
    }

    .page-item.active .page-link {
        background-color: var(--color-dorado-oscuro);
        border-color: var(--color-dorado-oscuro);
        color: #000;
        font-weight: bold;
    }


    aside a {
        transition: all 0.2s ease;
    }

    aside a:hover,
    aside a:focus {
        background-color: rgba(218, 181, 97, 0.1);
        padding-left: 8px;
    }


    .btn {
        min-height: 44px;
        min-width: 44px;
        font-weight: 500;
    }


    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }


    thead th {
        font-weight: 600;
        color: #000 !important;
    }


    .page-item.disabled .page-link {
        background-color: #e9ecef;
        opacity: 0.6;
        cursor: not-allowed;
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

    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.03);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(218, 181, 97, 0.1);
    }


    .alert {
        font-weight: 500;
        border-left: 4px solid;
    }
</style>
<body>

    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

    <?php 
        if(!isset($_SESSION['usuario'])) {
            include 'navNoRegistrado.php';   
        } else if($_SESSION['tipoUsuario'] == 'cliente') {
            include 'navCliente.php';  
        } else if($_SESSION['tipoUsuario'] == 'dueno de local') {
            include 'navDueño.php';  
        } else {
            include 'navAdmin.php';  
        }
    ?>

    <!-- CONTENEDOR PRINCIPAL -->
    <main id="main-content" class="container-fluid my-4">
        <div class="row">
            <!-- PANEL LATERAL DE CATEGORÍAS -->
            <aside class="col-md-3 col-lg-2 mb-4" role="complementary" aria-label="Menú de categorías">
                <nav class="p-3 bg-white shadow rounded-3">
                    <!-- Botón desplegable para móviles -->
                    <button class="btn w-100 d-md-none mb-3" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#categoriesMenu" 
                            aria-expanded="false" 
                            aria-controls="categoriesMenu"
                            aria-label="Mostrar menú de categorías"
                            style="background-color: var(--color-dorado-oscuro); color: white; font-weight: 600;">
                        <i class="bi bi-list me-2" aria-hidden="true"></i>Categorías
                    </button>
                    
               
                    <h2 class="mb-3 text-center d-none d-md-block" style="color: var(--color-negro); font-weight:600; font-size: 1.5rem;">
                        Categorías
                    </h2>
                    
                
                    <div class="collapse d-md-block" id="categoriesMenu">
                        <ul class="list-unstyled" role="menu">
                            <li role="none">
                                <a href="descuentoTabla(SDB).php?categoria=Deporte" 
                                   role="menuitem"
                                   class="text-decoration-none d-block py-2 px-2 text-center fw-bold rounded" 
                                   style="<?php echo ($categoria == 'Deporte')? 'color: var(--color-dorado-oscuro); background-color: rgba(218, 181, 97, 0.2); font-size: 1.1rem;' : 'color: var(--color-gris);'; ?>"
                                   <?php echo ($categoria == 'Deporte')? 'aria-current="page"' : ''; ?>>
                                    <i class="bi bi-trophy" aria-hidden="true"></i> Deporte
                                </a>
                            </li>          
                            <li role="none">
                                <a href="descuentoTabla(SDB).php?categoria=Entretenimiento" 
                                   role="menuitem"
                                   class="text-decoration-none d-block py-2 px-2 text-center fw-bold rounded" 
                                   style="<?php echo ($categoria == 'Entretenimiento')? 'color: var(--color-dorado-oscuro); background-color: rgba(218, 181, 97, 0.2); font-size: 1.1rem;' : 'color: var(--color-gris);'; ?>"
                                   <?php echo ($categoria == 'Entretenimiento')? 'aria-current="page"' : ''; ?>>
                                    <i class="bi bi-film" aria-hidden="true"></i> Entretenimiento
                                </a>
                            </li> 
                            <li role="none">
                                <a href="descuentoTabla(SDB).php?categoria=Gastronomia" 
                                   role="menuitem"
                                   class="text-decoration-none d-block py-2 px-2 text-center fw-bold rounded" 
                                   style="<?php echo ($categoria == 'Gastronomia')? 'color: var(--color-dorado-oscuro); background-color: rgba(218, 181, 97, 0.2); font-size: 1.1rem;' : 'color: var(--color-gris);'; ?>"
                                   <?php echo ($categoria == 'Gastronomia')? 'aria-current="page"' : ''; ?>>
                                    <i class="bi bi-cup-hot" aria-hidden="true"></i> Gastronomía
                                </a>
                            </li> 
                            <li role="none">
                                <a href="descuentoTabla(SDB).php?categoria=Indumentaria" 
                                   role="menuitem"
                                   class="text-decoration-none d-block py-2 px-2 text-center fw-bold rounded" 
                                   style="<?php echo ($categoria == 'Indumentaria')? 'color: var(--color-dorado-oscuro); background-color: rgba(218, 181, 97, 0.2); font-size: 1.1rem;' : 'color: var(--color-gris);'; ?>"
                                   <?php echo ($categoria == 'Indumentaria')? 'aria-current="page"' : ''; ?>>
                                    <i class="bi bi-bag" aria-hidden="true"></i> Indumentaria
                                </a>
                            </li> 
                            <li role="none">
                                <a href="descuentoTabla(SDB).php?categoria=Tecnologia" 
                                   role="menuitem"
                                   class="text-decoration-none d-block py-2 px-2 text-center fw-bold rounded" 
                                   style="<?php echo ($categoria == 'Tecnologia')? 'color: var(--color-dorado-oscuro); background-color: rgba(218, 181, 97, 0.2); font-size: 1.1rem;' : 'color: var(--color-gris);'; ?>"
                                   <?php echo ($categoria == 'Tecnologia')? 'aria-current="page"' : ''; ?>>
                                    <i class="bi bi-laptop" aria-hidden="true"></i> Tecnología
                                </a>
                            </li>
                            <li role="none">
                                <a href="descuentoTabla(SDB).php?categoria=Otros" 
                                   role="menuitem"
                                   class="text-decoration-none d-block py-2 px-2 text-center fw-bold rounded" 
                                   style="<?php echo ($categoria == 'Otros')? 'color: var(--color-dorado-oscuro); background-color: rgba(218, 181, 97, 0.2); font-size: 1.1rem;' : 'color: var(--color-gris);'; ?>"
                                   <?php echo ($categoria == 'Otros')? 'aria-current="page"' : ''; ?>>
                                    <i class="bi bi-three-dots" aria-hidden="true"></i> Otros
                                </a>
                            </li> 
                        </ul>
                    </div>
                </nav>
            </aside>

            <!-- CONTENIDO PRINCIPAL -->
            <section class="col-md-9 col-lg-10" aria-labelledby="promotions-title">
                <div class="p-4 bg-white shadow rounded-3">
                    <h1 id="promotions-title" class="mb-4" style="color: var(--color-negro); font-weight:600; font-size: 1.75rem;">
                        Promociones disponibles
                        <?php if(!empty($categoria)): ?>
                            <span class="d-block d-sm-inline mt-2 mt-sm-0">
                                en <span style="color: var(--color-dorado-oscuro);"><?php echo htmlspecialchars($categoria); ?></span>
                            </span>
                        <?php endif; ?>
                    </h1>

                    <!-- TABLA DE PROMOCIONES -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle text-center border" 
                               role="table"
                               aria-label="Tabla de promociones disponibles">
                            <thead style="background: linear-gradient(135deg, var(--color-dorado), var(--color-dorado-oscuro)); color: var(--color-negro);">
                                <tr>
                                    <th scope="col">Código</th>
                                    <th scope="col">Descripción</th>
                                    <th scope="col">Local</th>
                                    <th scope="col">Caducidad</th>
                                    <th scope="col">Días Habilitados</th>
                                    <th scope="col">
                                        <?php
                                        if($tipoUsuario == 'dueno de local' || $tipoUsuario == 'administrador') {
                                            echo '<span class="visually-hidden">Sin acciones disponibles</span>';
                                        } else {
                                            echo 'Acciones';
                                        }
                                        ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(mysqli_num_rows($resultPromosTotales) != 0){
                                while($promo = mysqli_fetch_assoc($resultPromosTotales)){
                                    $nombreLocal = htmlspecialchars(obtenNombreLocal($promo['codLocal']));
                                    $textoPromo = htmlspecialchars($promo['textoPromo']);
                                    $diasSemana = htmlspecialchars($promo['diasSemana']);
                                    $fechaCaducidad = date('d/m/Y', strtotime($promo['fechaHastaPromo']));
                            ?>
                                <tr>
                                    <td data-label="Código">
                                        <strong>PR-<?php echo $promo['codPromo']; ?></strong>
                                    </td>
                                    <td data-label="Descripción"><?php echo $textoPromo; ?></td>
                                    <td data-label="Local"><?php echo $nombreLocal; ?></td>
                                    <td data-label="Caducidad">
                                        <time datetime="<?php echo $promo['fechaHastaPromo']; ?>">
                                            <?php echo $fechaCaducidad; ?>
                                        </time>
                                    </td>
                                    <td data-label="Días Habilitados"><?php echo $diasSemana; ?></td>
                                    <td data-label="Acciones">
                                        <?php
                                        if($tipoUsuario == 'dueno de local' || $tipoUsuario == 'administrador') {
                                            echo '<span class="text-muted">—</span>';
                                        } elseif(!isset($_SESSION['usuario'])) {
                                            echo '<a href="login.php" 
                                                     class="btn btn-sm btn-warning"
                                                     aria-label="Iniciar sesión para solicitar la promoción ' . $promo['codPromo'] . '">
                                                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> 
                                                    Iniciar Sesión
                                                  </a>';
                                        } else {
                                            echo '<form method="POST" action="" class="d-inline">
                                                    <input type="hidden" name="codPromo" value="' . $promo['codPromo'] . '">
                                                    <button class="btn btn-sm btn-success" 
                                                            type="submit" 
                                                            name="solicitarPromo"
                                                            aria-label="Solicitar promoción ' . $promo['codPromo'] . ' de ' . $nombreLocal . '">
                                                        <i class="bi bi-check-circle" aria-hidden="true"></i> Solicitar
                                                    </button>
                                                  </form>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else { 
                            ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div role="alert" aria-live="polite">
                                            <i class="bi bi-search fs-2" aria-hidden="true"></i>
                                            <p class="fw-bold mb-0 mt-2" style="color: var(--color-gris);">
                                                No se encontraron promociones disponibles
                                                <?php if(!empty($categoria)): ?>
                                                    en la categoría <?php echo htmlspecialchars($categoria); ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>

              
                    <?php if($totalPaginas > 1): ?>
                    <nav aria-label="Navegación de páginas de promociones" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <!-- Botón Anterior -->
                            <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" 
                                   href="?pagina=<?php echo $pagina - 1; ?>&categoria=<?php echo urlencode($categoria); ?>"
                                   <?php echo ($pagina <= 1) ? 'aria-disabled="true" tabindex="-1"' : ''; ?>
                                   aria-label="Ir a página anterior">
                                    <i class="bi bi-chevron-left" aria-hidden="true"></i> Anterior
                                </a>
                            </li>

                        
                            <?php for($i = 1; $i <= $totalPaginas; $i++): ?>
                                <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                                    <a class="page-link" 
                                       href="?pagina=<?php echo $i; ?>&categoria=<?php echo urlencode($categoria); ?>"
                                       <?php echo ($i == $pagina) ? 'aria-current="page"' : ''; ?>
                                       aria-label="<?php echo ($i == $pagina) ? 'Página actual, página ' : 'Ir a página '; ?><?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                          
                            <li class="page-item <?php echo ($pagina >= $totalPaginas) ? 'disabled' : ''; ?>">
                                <a class="page-link" 
                                   href="?pagina=<?php echo $pagina + 1; ?>&categoria=<?php echo urlencode($categoria); ?>"
                                   <?php echo ($pagina >= $totalPaginas) ? 'aria-disabled="true" tabindex="-1"' : ''; ?>
                                   aria-label="Ir a página siguiente">
                                    Siguiente <i class="bi bi-chevron-right" aria-hidden="true"></i>
                                </a>
                            </li>
                        </ul>

                     
                        <p class="text-center text-muted mt-2" aria-live="polite">
                            Página <?php echo $pagina; ?> de <?php echo $totalPaginas; ?>
                            <?php if($total > 0): ?>
                                (<?php echo $total; ?> <?php echo $total == 1 ? 'promoción' : 'promociones'; ?> en total)
                            <?php endif; ?>
                        </p>
                    </nav>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>


    <?php if(isset($_SESSION['solicitud_ok'])): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Solicitud exitosa',
            text: '<?php echo addslashes($_SESSION['solicitud_ok']); ?>',
            confirmButtonColor: '#DAB561',
            confirmButtonText: 'Entendido'
        });
        </script>
        <?php unset($_SESSION['solicitud_ok']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['solicitudHecha_ok'])): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        Swal.fire({
            icon: 'warning',
            title: 'Promoción ya solicitada',
            text: '<?php echo addslashes($_SESSION['solicitudHecha_ok']); ?>',
            confirmButtonColor: '#DAB561',
            confirmButtonText: 'Entendido'
        });
        </script>
        <?php unset($_SESSION['solicitudHecha_ok']); ?>
    <?php endif; ?>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>
</html>
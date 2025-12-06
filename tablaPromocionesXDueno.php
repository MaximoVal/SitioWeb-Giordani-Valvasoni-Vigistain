<?php
session_start();
include('funciones.php');
$hoy= date('Y-m-d');
function verificaPromoSoli($promoCod, $cliente){
    $sqlVerificacion="SELECT * FROM uso_promociones WHERE codPromo='$promoCod' and codCliente='$cliente'";
    $resultadoVerificacion= consultaSQL($sqlVerificacion);
    return mysqli_num_rows($resultadoVerificacion);
}
if(isset($_SESSION['usuario'])){
    $emailUsu=$_SESSION['usuario'];
    $sqlCategoriaCliente="SELECT * FROM usuarios WHERE nombreUsuario='$emailUsu'";
    $resultadoCategoria=consultaSQL($sqlCategoriaCliente);
    $rc=mysqli_fetch_assoc($resultadoCategoria);
    $resultadoCat=$rc['categoriaCliente'];
    $codCliente=$rc['codUsuario'];
}
else{
    $resultadoCat='Premium';
}
$nombreLocal = $_POST['nombreLocal'] ?? $_GET['nombreLocal'] ?? null;
$codLocal = $_POST['codLocal'] ?? $_GET['codLocal'] ?? null;

echo $codLocal;

if ($codLocal) {
    $sqlBuscaLocal="SELECT * FROM locales WHERE codLocal='$codLocal'";
} else {
    $sqlBuscaLocal="SELECT * FROM locales WHERE nombreLocal='$nombreLocal'";
}
$resultLocal=consultaSQL($sqlBuscaLocal);
$dataLocal=mysqli_fetch_assoc($resultLocal);
$codLocal= $dataLocal['codLocal'];
if(isset($resultadoCat)){

    $porPagina = 5;
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    if ($pagina < 1) $pagina = 1;

    $offset = ($pagina - 1) * $porPagina;

    $categoria= $resultadoCat;
    

    if($resultadoCat=='Inicial'){

        $sqlCount = "SELECT COUNT(*) AS total 
                     FROM promociones 
                     WHERE estadoPromo='aprobada' 
                       AND categoriaCliente='Inicial'
                       AND fechaDesdePromo <= '$hoy'
                       AND fechaHastaPromo >= '$hoy'
                       AND codLocal='$codLocal'";

        $resultCount = consultaSQL($sqlCount);
        $total = mysqli_fetch_assoc($resultCount)['total'];

        $totalPaginas = ceil($total / $porPagina);

        $sqlPromosCategoricas= "SELECT * 
                                FROM promociones 
                                WHERE estadoPromo='aprobada' 
                                AND categoriaCliente='Inicial'
                                AND fechaDesdePromo <= '$hoy'
                       			AND fechaHastaPromo >= '$hoy'
                                AND codLocal='$codLocal'
                                LIMIT $porPagina OFFSET $offset
                                ";

  
    } elseif($resultadoCat=='Medium') {

        $sqlCount = "SELECT COUNT(*) AS total 
                     FROM promociones 
                     WHERE estadoPromo='aprobada' 
                       AND (categoriaCliente='Inicial' OR categoriaCliente='Medium')
                       AND fechaDesdePromo <= '$hoy'
                       AND fechaHastaPromo >= '$hoy'
                       AND codLocal='$codLocal'";

        $resultCount = consultaSQL($sqlCount);
        $total = mysqli_fetch_assoc($resultCount)['total'];

        $totalPaginas = ceil($total / $porPagina);

        $sqlPromosCategoricas= "SELECT * 
                                FROM promociones 
                                WHERE estadoPromo='aprobada' 
                                  AND (categoriaCliente='Inicial' OR categoriaCliente='Medium')
                                AND fechaDesdePromo <= '$hoy'
                      			 AND fechaHastaPromo >= '$hoy'
                                AND codLocal='$codLocal'
                                LIMIT $porPagina OFFSET $offset"
                                ;


    } else {

        $sqlCount = "SELECT COUNT(*) AS total 
                     FROM promociones 
                     WHERE estadoPromo='aprobada' 
                       AND fechaDesdePromo <= '$hoy'
                       AND fechaHastaPromo >= '$hoy'
                       AND codLocal='$codLocal'";

        $resultCount = consultaSQL($sqlCount);
        $total = mysqli_fetch_assoc($resultCount)['total'];

        $totalPaginas = ceil($total / $porPagina);

        $sqlPromosCategoricas= "SELECT * 
                                FROM promociones 
                                WHERE estadoPromo='aprobada' 
                                AND fechaDesdePromo <= '$hoy'
                       			AND fechaHastaPromo >= '$hoy'
                                AND codLocal='$codLocal'                                
                                LIMIT $porPagina OFFSET $offset "
                                ;
    }

    $resultPromosTotales=consultaSQL($sqlPromosCategoricas);
}
if(isset($_POST['solicitarPromo'])){
    $codPromo= $_POST['codPromo'];
    $estadoInicial="enviada";
    if(verificaPromoSoli($codPromo, $codCliente)==0){    
        $sqlSolicitaPromo="INSERT INTO uso_promociones (codCliente, codPromo, fechaUsoPromo, estado) VALUES ('$codCliente', '$codPromo', '$hoy', '$estadoInicial')";
        consultaSQL($sqlSolicitaPromo);
        $_SESSION['solicitud_ok'] = "La promocion se solicito correctamente.";
    }else{
        $_SESSION['solicitudHecha_ok'] = "La promocion ya fue solicitada anteriormente por usted.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promociones por Local</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Estilos/adiministraDuenoEstilos.css">
    <link rel="stylesheet" href="../Estilos/estilos.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
    if(isset($_SESSION['tipoUsuario'])){
        $tipoUsuario = $_SESSION['tipoUsuario'];
        if($tipoUsuario == 'cliente'){
            include 'navCliente.php';
        } elseif($tipoUsuario == 'dueno de local'){
            include 'navDueno.php';
        } elseif($tipoUsuario == 'administrador'){
            include 'navAdmin.php';
        } else {
            include 'navNoRegistrado.php';
        }
    } else {
        include 'navNoRegistrado.php';
    }
    ?>

    <main class="container-fluid my-4">
        <div class="row">
            <!-- BARRA LATERAL -->
            <aside class="col-md-3 col-lg-2 mb-4">
                <div class="p-3 bg-white shadow rounded-3">    
                    <h5 class="mb-3" style="color:var(--color-negro); font-weight:600;"><i class="bi bi-shop"></i> Información del Local</h5>
                    <p><strong>Codigo de Local:</strong> <?php echo $dataLocal['codLocal']; ?></p>
                    <p class="mb-2"><strong>Nombre:</strong> <?php echo $dataLocal['nombreLocal']; ?></p>
                    <p class="mb-3"><strong>Sector:</strong> <?php echo $dataLocal['ubicacionLocal']; ?></p>
                    <p class="mb-2"><strong>Rubro:</strong> <?php echo $dataLocal['categoriaLocal']; ?></p>
                </div>
                <nav class="p-3 bg-white shadow rounded-3 mt-3">
              
                    <button class="btn w-100 d-md-none mb-3" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#categoriesMenu" 
                            aria-expanded="false" 
                            aria-controls="categoriesMenu"
                            aria-label="Mostrar menú de categorías"
                            style="background-color: var(--color-dorado-oscuro); color: white; font-weight: 600;">
                        <i class="bi bi-list me-2" aria-hidden="true"></i> Menú Promociones
                    </button>
                    
                    
                    <h2 class="mb-3 text-center d-none d-md-block" style="color: var(--color-negro); font-weight:600; font-size: 1.5rem;">
                        Categorías
                    </h2>
                    
                    
                    <div class="collapse d-md-block" id="categoriesMenu">
                        <ul class="list-unstyled" role="menu">
                            <li role="none">
                                <a href="tablaPromocionesComp.php" 
                                   role="menuitem"
                                   class="text-decoration-none d-block py-2 px-2 text-center fw-bold rounded" 
                                   style="color: var(--color-gris);background-color:#F5F5F5">
                                    <i class="bi bi-percent" aria-hidden="true"></i> Ver todas las promociones
                                </a>
                            </li>          
                        </ul>
                    </div>
                </nav>
            </aside>
          
                
            

            <!-- CONTENIDO PRINCIPAL -->
            <section class="col-md-9 col-lg-10" method="POST">
                    <div class="p-4 bg-white shadow rounded-3">
                    <h4 class="mb-4" style="color: var(--color-negro); font-weight:600;">Promociones pertenecientes a: <span style="color: var(--color-dorado-oscuro);"><?php echo $dataLocal['nombreLocal']; ?></span></h4>

                    <!-- TABLA DE PROMOCIONES -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle text-center border" 
                           role="grid" 
                           aria-readonly="true"
                           aria-label="Tabla de promociones del local. Use las flechas para navegar entre celdas.">

                        <thead style="background: linear-gradient(135deg, var(--color-dorado), var(--color-dorado-oscuro)); color: var(--color-negro);">
                            <tr role="row">
                                <th scope="col" role="columnheader" id="th-codigo">Código</th>
                                <th scope="col" role="columnheader" id="th-desc">Descripción</th>
                                <th scope="col" role="columnheader" id="th-dias">Días Disponibles</th>
                                <th scope="col" role="columnheader" id="th-caducidad">Caducidad</th>
                                <th scope="col" role="columnheader" id="th-acciones">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            if(mysqli_num_rows($resultPromosTotales) != 0){
                                while($promo = mysqli_fetch_assoc($resultPromosTotales)){
                                    // Generamos ID único para vincular la fila
                                    $idFila = 'row-' . $promo['codPromo'];
                                    $textoPromo = htmlspecialchars($promo['textoPromo']);
                                    $fechaRaw = $promo['fechaHastaPromo'];
                            ?>
                                <tr role="row">
                                    <th scope="row" role="rowheader" 
                                        id="<?php echo $idFila; ?>" 
                                        headers="th-codigo" 
                                        tabindex="0"
                                        class="fw-bold">
                                        PR-<?php echo $promo['codPromo']; ?>
                                    </th>

                                    <td role="gridcell" 
                                        headers="th-desc <?php echo $idFila; ?>" 
                                        tabindex="0">
                                        <?php echo $textoPromo; ?>
                                    </td>

                                    <td role="gridcell" 
                                        headers="th-dias <?php echo $idFila; ?>" 
                                        tabindex="0">
                                        <?php echo $promo['diasSemana']; ?>
                                    </td>

                                    <td role="gridcell" 
                                        headers="th-caducidad <?php echo $idFila; ?>" 
                                        tabindex="0">
                                        <time datetime="<?php echo $fechaRaw; ?>">
                                            <?php echo date('d/m/Y', strtotime($fechaRaw)); ?>
                                        </time>
                                    </td>

                                    <td role="gridcell" headers="th-acciones <?php echo $idFila; ?>">
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="codPromo" value="<?php echo $promo['codPromo']; ?>">

                                            <button class="btn btn-sm btn-success" 
                                                    name="solicitarPromo" 
                                                    type="submit"
                                                    tabindex="0"
                                                    aria-label="Solicitar promoción <?php echo $textoPromo; ?>">
                                                <i class="bi bi-check" aria-hidden="true"></i> Solicitar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else { 
                            ?>
                                <tr role="row">
                                    <td colspan="5" role="gridcell" tabindex="0" class="text-center py-4">
                                        <p class="fw-bold mb-0" style="color: var(--color-gris);">
                                            <i class="bi bi-search" aria-hidden="true"></i> 
                                            No se encontraron promociones disponibles para usted en este local
                                        </p>
                                    </td>
                                </tr>
                            <?php 
                            } 
                            ?>
                        </tbody>
                    </table>

                    <nav aria-label="Paginación de resultados" class="mt-3">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>&codLocal=<?php echo $codLocal; ?>" aria-label="Ir a página anterior">
                                    Anterior
                                </a>
                            </li>

                            <?php for($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                                <a class="page-link" 
                                   href="?pagina=<?php echo $i; ?>&codLocal=<?php echo $codLocal; ?>"
                                   aria-label="Página <?php echo $i; ?>"
                                   <?php if($i == $pagina) echo 'aria-current="page"'; ?>>
                                   <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>

                            <li class="page-item <?php echo ($pagina >= $totalPaginas) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>&codLocal=<?php echo $codLocal; ?>" aria-label="Ir a página siguiente">
                                    Siguiente
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
    </main>
<?php  if(isset($_SESSION['solicitud_ok'])) { ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Solicitud de promocion exitosa',
        text: '<?php echo $_SESSION['solicitud_ok']; ?>',
    });
    </script>
    <?php
        unset($_SESSION['solicitud_ok']); 
    } 
   ?>
<?php  if(isset($_SESSION['solicitudHecha_ok'])) { ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    Swal.fire({
        icon: 'warning',
        title: 'Solicitud de promocion ya realizada anteriormente',
        text: '<?php echo $_SESSION['solicitudHecha_ok']; ?>',
    });
    </script>
    <?php
        unset($_SESSION['solicitudHecha_ok']); 
    } 
  ?>
    <!-- Footer -->
    <?php include 'footer.php'; ?>


</body>
</html>
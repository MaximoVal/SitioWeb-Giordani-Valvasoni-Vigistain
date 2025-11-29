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
                       AND fechaDesdePromo > '$hoy' 
                       AND codLocal='$codLocal'";

        $resultCount = consultaSQL($sqlCount);
        $total = mysqli_fetch_assoc($resultCount)['total'];

        $totalPaginas = ceil($total / $porPagina);

        $sqlPromosCategoricas= "SELECT * 
                                FROM promociones 
                                WHERE estadoPromo='aprobada' 
                                  AND categoriaCliente='Inicial'
                                  AND fechaDesdePromo > '$hoy'
                                AND codLocal='$codLocal'
                                LIMIT $porPagina OFFSET $offset
                                ";

  
    } elseif($resultadoCat=='Medium') {

        $sqlCount = "SELECT COUNT(*) AS total 
                     FROM promociones 
                     WHERE estadoPromo='aprobada' 
                       AND (categoriaCliente='Inicial' OR categoriaCliente='Medium')
                       AND fechaDesdePromo > '$hoy'
                       AND codLocal='$codLocal'";

        $resultCount = consultaSQL($sqlCount);
        $total = mysqli_fetch_assoc($resultCount)['total'];

        $totalPaginas = ceil($total / $porPagina);

        $sqlPromosCategoricas= "SELECT * 
                                FROM promociones 
                                WHERE estadoPromo='aprobada' 
                                  AND (categoriaCliente='Inicial' OR categoriaCliente='Medium')
                                  AND fechaDesdePromo > '$hoy'
                                AND codLocal='$codLocal'
                                LIMIT $porPagina OFFSET $offset"
                                ;


    } else {

        $sqlCount = "SELECT COUNT(*) AS total 
                     FROM promociones 
                     WHERE estadoPromo='aprobada' 
                       AND fechaDesdePromo > '$hoy'
                       AND codLocal='$codLocal'";

        $resultCount = consultaSQL($sqlCount);
        $total = mysqli_fetch_assoc($resultCount)['total'];

        $totalPaginas = ceil($total / $porPagina);

        $sqlPromosCategoricas= "SELECT * 
                                FROM promociones 
                                WHERE estadoPromo='aprobada' 
                                  AND fechaDesdePromo > '$hoy'
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
            </aside>

            <!-- CONTENIDO PRINCIPAL -->
            <section class="col-md-9 col-lg-10" method="POST">
                    <div class="p-4 bg-white shadow rounded-3">
                    <h4 class="mb-4" style="color: var(--color-negro); font-weight:600;">Promociones pertenecientes a: <span style="color: var(--color-dorado-oscuro);"><?php echo $dataLocal['nombreLocal']; ?></span></h4>

                    <!-- TABLA DE PROMOCIONES -->
                <div class="table-responsive">
                <table class="table table-hover table-striped align-middle text-center border">
                    <thead style="background: linear-gradient(135deg, var(--color-dorado), var(--color-dorado-oscuro)); color: var(--color-negro);">
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Dias Disponibles</th>
                        <th>Caducidad</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    
                    <?php
                    if(mysqli_num_rows($resultPromosTotales) != 0){
                        while($promo = mysqli_fetch_assoc($resultPromosTotales)){
                    ?><form method="POST" action=""> 
                            <input type="hidden" name="codPromo" value="<?php echo $promo['codPromo']; ?>">
                            <tr><td>PR-<?php echo ($promo['codPromo']); ?></td>
                            <td><?php echo ($promo['textoPromo']); ?></td>
                            <td><?php echo ($promo['diasSemana']); ?></td>
                            <td><?php echo ($promo['fechaHastaPromo']); ?></td>
                            <td><button class="btn btn-sm btn-success" name="solicitarPromo"><i class="bi bi-check"></i>Solicitar</button>
                            
                        </tr></form>
                            <?php
                        }
                    }else{ ?>
                        <p class="fw-bold" style="color: var(--color-gris);">
                            <i class="bi bi-search"></i>[NO SE ENCONTRARON PROMOCIONES DISPONIBLES PARA USTED EN ESTE LOCAL]
                        </p> <?php
                    }
                        ?>
                        </tbody>
                    </table>
                    <nav aria-label="Paginación" class="mt-3">
                    <ul class="pagination justify-content-center">

                    
                        <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>&codLocal=<?php echo $codLocal; ?>">Anterior</a>
                        </li>

                  
                        <?php for($i = 1; $i <= $totalPaginas; $i++): ?>
                        <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $i; ?>&codLocal=<?php echo $codLocal; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo ($pagina >= $totalPaginas) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>&codLocal=<?php echo $codLocal; ?>">Siguiente</a>
                        </li>

                    </ul>
                    </nav>

            </section>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?php
session_start();
include("funciones.php");

$sql1 = "SELECT codUsuario, nombre, apellido FROM usuarios WHERE tipoUsuario='dueno de local' AND localNoLocal='no'";
$resultado1 = consultaSQL($sql1);

if(isset($_POST['realizarActualizacionLocal'])){
    $nomLocal = $_POST['nomLocalOriginal'];
    $rubroOriginal = $_POST['categoriaOriginal'];
    $ubicOriginal = $_POST['ubicacionOriginal'];
    $duenoOriginal = $_POST['duenoOriginal'];


    $nombreLocaln = !empty($_POST['nombreLocal']) ? $_POST['nombreLocal'] : $nomLocal;
    $rubroLocal = !empty($_POST['rubro']) ? $_POST['rubro'] : $rubroOriginal;
    $ubicacionLocal = !empty($_POST['ubicacion']) ? $_POST['ubicacion'] : $ubicOriginal;
    $codDueno = !empty($_POST['dueno']) ? $_POST['dueno'] : $duenoOriginal;

    $sqlActualizacion = "UPDATE locales SET nombreLocal='$nombreLocaln', ubicacionLocal='$ubicacionLocal', categoriaLocal='$rubroLocal', codDueno='$codDueno' WHERE nombreLocal='$nomLocal'";
    consultaSQL($sqlActualizacion);

    $sqlModificacion2 = "UPDATE usuarios SET localNoLocal='no' WHERE codUsuario='$duenoOriginal'";
    consultaSQL($sqlModificacion2);

    $sqlModificacion = "UPDATE usuarios SET localNoLocal='si' WHERE codUsuario='$codDueno'";
    consultaSQL($sqlModificacion);

    $_SESSION['update_ok'] = "El local se actualizó correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Administrador - Locales</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
	 <link rel="icon" type="image/png" href="../Footage/iconoPagina.png" >
    <link rel="stylesheet" href="../Estilos/adiministraDuenoEstilos.css">
    <link rel="stylesheet" href="../Estilos/estilos.css">
    
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
        .list-group-item.active {
            background-color: #DAB561 !important;
            border-color: #DAB561 !important;
            color: #000000 !important;
        }

        #localDisplay {
            position: relative; 
        }
        #resultado {
            position: absolute; 
            z-index: 1000;      
            width: 100%;
            background-color: white;
            box-shadow: 0px 4px 6px rgba(0,0,0,0.1);
            display: none;    
            max-height: 200px;
            overflow-y: auto;
        }
        .item {
            cursor: pointer;
        }
        .item:hover {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <?php include 'navAdmin.php'; ?>

    <main class="container-fluid my-4">
        <div class="row gx-4">

            <aside class="col-12 col-md-3 mb-3">
                <div class="card sidebar-links">
                    <div class="card-body d-flex flex-column justify-content-start">
                        <button class="btn btn-primary w-100 d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#adminMenu" aria-expanded="false" aria-controls="adminMenu">
                            <i class="bi bi-menu-button-wide me-2"></i>Panel administrador
                        </button>
                        
                        <h3 class="card-title title d-none d-md-block">Panel administrador</h3>
                        
                        <div class="collapse d-md-block" id="adminMenu">
                            <div class="list-group">
                                <a href="duenosAdmin.php" class="list-group-item list-group-item-action">Administrar dueños</a>
                                <a href="administraLocalAdmin.php" class="list-group-item list-group-item-action active">Administrar locales</a>
                                <a href="administrarPromocionesAdmin.php" class="list-group-item list-group-item-action">Administrar promociones</a>
                                <a href="creaLocalAdmin.php" class="list-group-item list-group-item-action">Crear local</a>
                                <a href="crearNovedad.php" class="list-group-item list-group-item-action">Crear novedad</a>
                                <a href="eliminaLocalAdmin.php" class="list-group-item list-group-item-action">Eliminar local</a>   
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="card card-form col-md-7 col-lg-8">
                <div class="card-body">
                    <h5 class="card-title">Búsqueda de Local</h5>

                    <form id="admLocalForm" class="row g-2" method="POST" action="">
                        <div id="localDisplay" class="mb-3">
                            <label class="form-label">Nombre del local</label>
                            <input type="text" id="buscar" class="form-control" placeholder="Buscar local..." name="nombre" autocomplete="off">
                            <div id="resultado" class="list-group position-absolut w-100"></div>
                        </div>
                        
                        <div class="col-12">
                            <button class="btn btn-primary" name="comenzaredicion">Comenzar edición</button>
                        </div>
                    </form>
                    
                    <hr class="my-4">

                    <?php
                    if(isset($_POST['comenzaredicion'])){
                        $nombre = $_POST['nombre'];
                        
                        $sqlbusqLocal = "SELECT * FROM locales WHERE nombreLocal='$nombre'";
                        $localData = consultaSQL($sqlbusqLocal);
                        
                        if(mysqli_num_rows($localData) != 0){
                            $filaLocal = mysqli_fetch_assoc($localData);
                            $nomLocal = $filaLocal['nombreLocal'];
                            $rubroLocal = $filaLocal['categoriaLocal'];
                            $sectorLocal = $filaLocal['ubicacionLocal'];
                            $idDueno = $filaLocal['codDueno'];
                    ?>
                            <form id="editLocalForm" class="row g-3" method="POST" action="">
                                
                                <input type="hidden" name="nomLocalOriginal" value="<?php echo $nomLocal; ?>">
                                <input type="hidden" name="categoriaOriginal" value="<?php echo $rubroLocal; ?>">
                                <input type="hidden" name="ubicacionOriginal" value="<?php echo $sectorLocal; ?>">
                                <input type="hidden" name="duenoOriginal" value="<?php echo $idDueno; ?>">

                                <div class="col-12">
                                    <label class="form-label">Nombre de Local</label>
                                    <input type="text" id="input-nombre" class="form-control" placeholder="<?php echo $nomLocal; ?>" name="nombreLocal">
                                </div>
                                
                                <div class="col-12">
                                    <label class="form-label">Rubro actual: <strong><?php echo $rubroLocal; ?></strong></label>
                                    <select class="form-select" id="rubro-seleccion" name="rubro">
                                        <option value="" disabled selected>Cambiar rubro...</option>
                                        <option value="gastronomia">Gastronomía</option>
                                        <option value="entretenimiento">Entretenimiento</option>
                                        <option value="deporte">Deporte</option>
                                        <option value="tecnologia">Tecnología</option>
                                        <option value="indumentaria">Indumentaria</option>
                                        <option value="otros">Otros</option>
                                    </select>
                                </div>
                                
                                <div class="container mt-4 border p-3 rounded">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h5 class="mb-3">Seleccionar Sector</h5>
                                            <p class="text-muted small">Sector actual: <?php echo $sectorLocal; ?></p>
                                            <select id="selectSector" class="form-select" name="ubicacion">
                                                <option value="">Elegir sector...</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8 text-center">
                                            <h5 class="mb-3">Mapa de Sectores</h5>
                                            <img id="mapaSectores" src="../Footage/mapaSectores.png" class="img-fluid rounded shadow" style="max-width: 300px;">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12 mt-3">
                                    <label class="form-label" for="seleccion-dueno">Dueño actual (ID): <strong><?php echo $idDueno; ?></strong></label>
                                    <select class="form-select" id="seleccion-dueno" name="dueno">
                                        <option value="" disabled selected>Asignar nuevo dueño (Disponibles)</option>
                                        <?php
                                        mysqli_data_seek($resultado1, 0);
                                        while($dueno = mysqli_fetch_assoc($resultado1)){
                                        ?>
                                            <option value="<?php echo $dueno['codUsuario']; ?>">
                                                ID: <?php echo $dueno['codUsuario']; ?> - <?php echo $dueno['nombre'] . " " . $dueno['apellido']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-12 mt-4 text-end">
                                    <button class="btn btn-info text-white" name="realizarActualizacionLocal">
                                        <i class="bi bi-arrow-clockwise me-1"></i> Actualizar Datos
                                    </button>
                                </div>
                            </form>
                    <?php 
                        } else {
                            echo '<div class="alert alert-warning mt-3">No se encontró un local con ese nombre.</div>';
                        }
                    } 
                    ?>
                </div> </div> </div>
    </main>

    <?php if(isset($_SESSION['update_ok'])) { ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Actualización exitosa',
        text: '<?php echo $_SESSION['update_ok']; ?>',
    });
    </script>
    <?php unset($_SESSION['update_ok']); } ?>

    <?php include 'footer.php'; ?>

    <script>
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("buscarLocal");
    const resultados = document.getElementById("resultados");

    input.addEventListener("keyup", function () {
        let query = this.value;

        if (query.length > 0) {
            fetch("buscarLocal.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "query=" + encodeURIComponent(query)
            })
            .then(res => res.text())
            .then(data => {
                resultados.innerHTML = data;
                resultados.style.display = "block";

                // Cuando se hace clic en un item de la lista
                document.querySelectorAll(".item").forEach(item => {
                    item.addEventListener("click", () => {
                        input.value = item.textContent;
                        resultados.innerHTML = "";
                        resultados.style.display = "none";
                    });
                });
            });
        } else {
            resultados.innerHTML = "";
            resultados.style.display = "none";
        }
    });
});
</script>


</body>
</html>
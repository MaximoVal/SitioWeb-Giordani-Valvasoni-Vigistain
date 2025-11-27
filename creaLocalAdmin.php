<?php
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("funciones.php");
$sql1 = "SELECT codUsuario, nombre, apellido FROM usuarios WHERE tipoUsuario='dueno de local' AND localNoLocal='no'";
$resultado1= consultaSQL($sql1);
$mensaje="";
if(isset($_POST['crear-local'])){
    $nombre= $_POST['nombre'];
    $rubro = $_POST['rubro'];
    $ubicacion= $_POST['ubicacion'];
    $dueno = $_POST['dueno'];
    $sqlAgregarLocal="INSERT INTO locales (nombreLocal, ubicacionLocal, categoriaLocal, codDueno) VALUES ('$nombre', '$ubicacion', '$rubro', '$dueno')";
    if(consultaSQL($sqlAgregarLocal)){
        $mensaje="Local cargado con exito!!";
        $sqlModificacion="UPDATE usuarios SET localNoLocal='si' WHERE codUsuario='$dueno'";
        consultaSQL($sqlModificacion);
        $_SESSION['carga_ok'] = "El local se creo correctamente.";
 
    }else{$mensaje="Ocurrio un error al querer cargar Local.";};
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Administrador</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../Estilos/adiministraDuenoEstilos.css">
    <link rel="stylesheet" href="../Estilos/estilos.css">

    <!-- Íconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
    .list-group-item.active
        {
            background-color: #DAB561 !important;
            border-color: #DAB561 !important;
            color: #000000 !important;
        }   
</style>
<body>

<?php include 'navAdmin.php'; ?>
    <!-- CONTENEDOR PRINCIPAL -->
<main class="container-fluid my-4">
        <div class="row">
            <!-- MENÚ LATERAL -->
            <aside class="col-12 col-md-3 mb-3">
                <div class="card sidebar-links">
                    <div class="card-body d-flex flex-column justify-content-start">
                        <!-- Botón desplegable para móviles -->
                        <button class="btn btn-primary w-100 d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#adminMenu" aria-expanded="false" aria-controls="adminMenu">
                            <i class="bi bi-menu-button-wide me-2"></i>Panel administrador
                        </button>
                        
                    
                        <h3 class="card-title title d-none d-md-block">Panel administrador</h3>
                        
                       
                        <div class="collapse d-md-block" id="adminMenu">
                            <div class="list-group">
                                <a href="duenosAdmin(SDB).php" class="list-group-item list-group-item-action">Administrar dueños</a>
                                <a href="administraLocalAdmin.php" class="list-group-item list-group-item-action ">Administrar locales</a>
                                <a href="administrarPromocionesAdmin.php" class="list-group-item list-group-item-action">Administrar promociones</a>
                                <a href="creaLocalAdmin.php" class="list-group-item list-group-item-action active">Crear local</a>
                                <a href="crearNovedad.php" class="list-group-item list-group-item-action">Crear novedad</a>
                                <a href="eliminaLocalAdmin.php" class="list-group-item list-group-item-action">Eliminar local</a>   
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- CONTENIDO PRINCIPAL -->
            <section class="col-md-7 col-lg-8 ml">
                <div class="form-container">
                    <div class="form-header">
                        <h2>Registrar Local</h2>
                    </div>
                    <div class="form-body">
                        <form method="POST" action="">
                        
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" placeholder="Ej: Café Fortuna" required name="nombre">
                            </div>

                     
                            <div class="mb-3">
                                <label for="rubro-seleccion" class="form-label">Rubro</label>
                                
                                <select class="form-select" id="rubro-seleccion" name="rubro" required>
                                    <option value="" disabled selected>Rubros Disponibles</option>
                                    <option value="gastronomia">Gastronomía</option>
                                    <option value="entretenimiento">Entretenimiento</option>
                                    <option value="deporte">Deporte</option>
                                    <option value="tecnologia">Tecnología</option>
                                    <option value="indumentaria">Indumentaria</option>
                                    <option value="otros">Otros</option>
                                </select>
                            </div>

                           
                            <div class="container mt-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h5 class="mb-3">Seleccionar Sector</h5>
                                        <select id="selectSector" class="form-select" name="ubicacion" required>
                                            <option value="">Elegir sector...</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <h5 class="mb-3">Mapa de Sectores</h5>
                                        <img id="mapaSectores" alt="Mapa del shopping mostrando sectorea" src="../Footage/mapaShopping.jpeg" class="img-fluid rounded " style="width: 340px; height: 340px ">
                                    </div>
                                </div>
                            </div>

                           
                            <div class="mb-3">
                                <label for="seleccion-dueno" class="form-label">ID Dueño</label>
                                <select class="form-select" id="seleccion-dueno" name="dueno" required>
                                    <option value="" disabled selected>Dueños Disponibles</option>
                                    <?php
                                    while($dueno = mysqli_fetch_assoc($resultado1)){
                                        ?><option value="<?php echo $dueno['codUsuario'];?>">Id:<?php echo $dueno['codUsuario'];?>,       Nombre:<?php echo $dueno['nombre'];?>,       Apellido:<?php echo $dueno['apellido'];?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <button type="submit" class="btn-enviar mt-3" name="crear-local">Registrar Local</button>
                            <!--<p><?php #echo $mensaje; ?></p> -->
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <?php  if(isset($_SESSION['carga_ok'])) { ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Registro de local exitoso',
        text: '<?php echo $_SESSION['carga_ok']; ?>',
    });
    </script>
    <?php
        unset($_SESSION['carga_ok']); 
    } 
  ?>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    
</body>
</html>

<?php
session_start();
include("funciones.php");
$sql1 = "SELECT codUsuario, nombre, apellido FROM usuarios WHERE tipoUsuario='dueño de local' AND localNoLocal='no'";
$resultado1= consultaSQL($sql1);
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
  $sqlModificacion2="UPDATE usuarios SET localNoLocal='no' WHERE codUsuario='$duenoOriginal'";
  consultaSQL($sqlModificacion2);
  $sqlModificacion="UPDATE usuarios SET localNoLocal='si' WHERE codUsuario='$codDueno'";
  consultaSQL($sqlModificacion);
  $_SESSION['update_ok'] = "El local se actualizó correctamente.";
                                  
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

    <!-- HEADER -->
    <?php include 'navAdmin.php'; ?>

    <!-- CONTENEDOR PRINCIPAL -->
  <main class="container-fluid my-4">
    <div class="row gx-4">

      <!-- SIDEBAR (izq) -->
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

      <!-- CONTENIDO PRINCIPAL  -->
        <div class="card card-form col-md-7 col-lg-8">
          <div class="card-body">
            <h5 class="card-title">Busqueda Local.</h5>

            <!-- Visualización actual del local -->
          <form id="admLocalForm" class="row g-2" method="POST" action="">
            <div id="localDisplay" class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" id="buscar" class="form-control" placeholder="Buscar local..." name="nombre" >
                <div id="resultado" class="list-group"></div>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                $(document).ready(function(){
                    $("#buscar").on("keyup", function(){
                        let texto = $(this).val();
                        
                        if(texto.length >= 1){
                        $.post("buscarLocal.php", { query: texto }, function(data){
                            $("#resultado").html(data).show();
                        });
                        } else {
                            $("#resultado").hide();
                        }
                    });

                 
                    $(document).on("click", ".item", function(){
                        $("#buscar").val($(this).text());
                        $("#resultado").hide();
                    });
                });
                </script>
            </div>
            <button class="btn btn-primary" name="comenzaredicion">Comenzar edicion</button>
              <?php
                if(isset($_POST['comenzaredicion'])){
                  $nombre=$_POST['nombre'];
                  $sqlbusqLocal="SELECT * FROM locales WHERE nombreLocal='$nombre'";
                  $localData=consultaSQL($sqlbusqLocal);
                  
                  if(mysqli_num_rows($localData) != 0){?>
                      <?php
                        $filaLocal = mysqli_fetch_assoc($localData);
                        $nomLocal = $filaLocal['nombreLocal'];
                        $rubroLocal = $filaLocal['categoriaLocal'];
                        $sectorLocal = $filaLocal['ubicacionLocal'];
                        $idDueno = $filaLocal['codDueno'];
                  } 
                      ?>
                                    <form id="editLocalForm" class="row g-2" method="POST" action="">

                                    <input type="hidden" name="nomLocalOriginal" value="<?php echo $nomLocal; ?>">
                                    <input type="hidden" name="categoriaOriginal" value="<?php echo $rubroLocal; ?>">
                                    <input type="hidden" name="ubicacionOriginal" value="<?php echo $sectorLocal; ?>">
                                    <input type="hidden" name="duenoOriginal" value="<?php echo $idDueno; ?>">
  
                                    <div class="col-12">
                                        <label class="form-label">Nombre de Local</label>
                                        <input type="text" id="input-nombre" class="form-control" placeholder=<?php echo ($nomLocal)?> name="nombreLocal">
                                      </div>
                                      <div class="col-12">
                                        <label class="form-label">Rubro de Local : <?php echo ($rubroLocal)?></label>
                                         <select class="form-select" id="rubro-seleccion" name="rubro" >
                                              <option value="" disabled selected>Rubros Disponibles...</option>
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
                                                  <h5 class="mb-3" >Seleccionar Sector:<p style="color:grey">(sector actual <?php echo ($sectorLocal)?>)</p></h5>
                                                  <select id="selectSector" class="form-select" name="ubicacion" >
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
                                                  <img id="mapaSectores" src="../Footage/mapaSectores.png" class="img-fluid rounded shadow" style="width: 340px; height: 340px ">
                                              </div>
                                          </div>
                                      </div>
                                        
                                        
                                        
                                        <div class="col-12">
                                          <label class="form-label" for="seleccion-dueno">Dueño del Local(ID): <?php echo ($idDueno); ?> </label>
                                            <select class="form-select" id="seleccion-dueno" name="dueno">
                                                <option value="" disabled selected>Dueños Disponibles</option>
                                                <?php
                                                while($dueno = mysqli_fetch_assoc($resultado1)){
                                                    ?><option value="<?php echo ($dueno['codUsuario']); ?>">
                                                    Id:<?php echo $dueno['codUsuario'];?>
                                                    ,       Nombre:<?php echo $dueno['nombre'];?>
                                                    ,       Apellido:<?php echo $dueno['apellido'];?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                          <button class="btn btn-info" name="realizarActualizacionLocal"><i class="bi bi-arrow-clockwise"></i> Actualizar</button>
                                    </form>
                <?php } else {
                    ?>  <!--<p style="color: red;">No se encontro Local con ese nombre.</p></p>--> <?php
                  }
                
              ?>
          
              </form>
          </div>
        </div>
      </section>


    </div>
  </main>

    <?php  if(isset($_SESSION['update_ok'])) { ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Actualización exitosa',
        text: '<?php echo $_SESSION['update_ok']; ?>',
    });
    </script>
    <?php
        unset($_SESSION['update_ok']); 
    } 
  ?>
    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>
</html>

<?php
session_start();
include("funciones.php");

if(isset($_POST['elimina-local'])){
  $nombre = $_POST['nombre'];
  $sqlDataDueno="SELECT codDueno FROM locales WHERE nombreLocal='$nombre'";
  $dataDueno=consultaSQL($sqlDataDueno);
  $filaDueno = mysqli_fetch_assoc($dataDueno);
  $codDueno = $filaDueno["codDueno"];
  #$filaDueno = mysqli_fetch_assoc($dataDueno); 
  #$codDueno = $filaDueno['codUsuario'];
  $sqlEliminaLocal="DELETE FROM locales WHERE nombreLocal='$nombre'";
  consultaSQL($sqlEliminaLocal);
  $sqlModificacion2="UPDATE usuarios SET localNoLocal='no' WHERE codUsuario='$codDueno'";
  consultaSQL($sqlModificacion2);
  $_SESSION['elimina_ok'] = "El local se elimino correctamente.";
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

      <!-- SIDEBAR -->
      <aside class="col-12 col-md-3 mb-3">
        <div class="card sidebar-links">
            <div class="card-body d-flex flex-column justify-content-start">
             
                <button class="btn btn-primary w-100 d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#adminMenu" aria-expanded="false" aria-controls="adminMenu">
                    <i class="bi bi-menu-button-wide me-2"></i>Panel administrador
                </button>
                
            
                <h3 class="card-title title d-none d-md-block">Panel administrador</h3>
                
              
                <div class="collapse d-md-block" id="adminMenu">
                    <div class="list-group">
                        <a href="duenosAdmin(SDB).php" class="list-group-item list-group-item-action">Administrar dueños</a>
                        <a href="administraLocalAdmin.php" class="list-group-item list-group-item-action ">Administrar locales</a>
                        <a href="administrarPromocionesAdmin.php" class="list-group-item list-group-item-action">Administrar promociones</a>
                        <a href="creaLocalAdmin.php" class="list-group-item list-group-item-action">Crear local</a>
                        <a href="crearNovedad.php" class="list-group-item list-group-item-action">Crear novedad</a>
                        <a href="eliminaLocalAdmin.php" class="list-group-item list-group-item-action active">Eliminar local</a>   
                    </div>
                </div>
            </div>
        </div>
    </aside>

      <!-- CONTENIDO PRINCIPAL (centro) -->
       <section class="col-12 col-md-8 mb-3">
        <div class="card card-form">
          <div class="card-body">
            <h5 class="card-title">Formulario eliminación local</h5>

            
            <form id="editLocalForm" class="row g-2" method="POST" action="">
              <div class="col-12">
                <label class="form-label">Nombre</label>
                <input type="text" id="buscar" class="form-control" placeholder="Buscar local..." name="nombre">
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

              
                    $("#btnReset").on("click", function(){
                        $("#buscar").val("");
                        $("#resultado").hide();
                    });
                });
                </script>
              </div>

              <div class="col-12 d-flex gap-2 mt-2">
                <button class="btn btn-danger btn-sm" name="elimina-local">Eliminar</button>
                <button type="button" id="btnReset" class="btn btn-outline-secondary" name="reset">Reset</button>
              </div>
            </form>

          </div>
        </div>
      </section>


    </div>
  </main>
    <?php  if(isset($_SESSION['elimina_ok'])) { ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Eliminacion de local exitoso',
        text: '<?php echo $_SESSION['elimina_ok']; ?>',
    });
    </script>
    <?php
        unset($_SESSION['elimina_ok']); 
    } 
  ?>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>
</html>

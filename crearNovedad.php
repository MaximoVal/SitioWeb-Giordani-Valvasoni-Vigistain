<?php
 include_once "funciones.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if(isset($_POST['enviar'])) {
 
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    $tipoCliente = $_POST['tipoCliente'];
    $fechaDesde = $_POST['fechaDesde'];
    $fechaHasta = $_POST['fechaHasta'];

    $sqlInster="INSERT INTO novedades (nombre, descripcionNovedad, categoria, tipoCliente, fechaDesdeNovedad, fechaHastaNovedad) VALUES ('$nombre', '$descripcion', '$categoria', '$tipoCliente', '$fechaDesde', '$fechaHasta')";
    $resultado = consultaSQL($sqlInster);

    if($resultado) {
        $_SESSION['novedad_ok'] = 'La novedad se ha creado correctamente';
        
    }
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
        .date-input {
    cursor: pointer;
}

.date-input::-webkit-calendar-picker-indicator {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    cursor: pointer;
    opacity: 0;
}

.input-group-text {
    cursor: pointer;
    pointer-events: none;
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
                                <a href="creaLocalAdmin.php" class="list-group-item list-group-item-action">Crear local</a>
                                <a href="crearNovedad.php" class="list-group-item list-group-item-action active">Crear novedad</a>
                                <a href="eliminaLocalAdmin.php" class="list-group-item list-group-item-action">Eliminar local</a>   
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- CONTENIDO PRINCIPAL -->
            <section class="col-md-7 col-lg-8">
                <div class="form-container">
                    <div class="form-header">
                        <h2>Crear novedad</h2>
                    </div>
                    <div class="form-body">
                        <form method="POST" action="">
                            <!-- Nombre -->
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" placeholder="Ej: Inaguracion de sector VIP" name="nombre" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="6" 
                                          placeholder="Escribe aquí la descripción de la novedad..." required></textarea>
                            </div>

                        
                            <div class="mb-3">
                                <label for="rubro-seleccion" class="form-label">Categoria de la novedead</label>    
                                <select class="form-select" id="rubro-seleccion" name="categoria" required>
                                    <option value="" disabled selected>Categorias Disponibles</option>
                                    <option value="gastronomia">Gastronomía</option>
                                    <option value="entretenimiento">Entretenimiento</option>
                                    <option value="deporte">Deporte</option>
                                    <option value="tecnologia">Tecnología</option>
                                    <option value="indumentaria">Indumentaria</option>
                                    <option value="infraestructura">Infraestructura</option>
                                    <option value="otros">Otros</option>
                                </select>
                            </div>

                           
                            <div class="mb-3">
                                <label class="form-label">Seleccione categoría del cliente</label>
                                
                                <input type="radio" id="incial" name="tipoCliente" value="Inicial" checked>
                                <label for="inicial">Inicial</label><br>
                                
                                <input type="radio" id="medium" name="tipoCliente" value="Medium">
                                <label for="dueño">Medium</label><br> 
                                
                                <input type="radio" id="premium" name="tipoCliente" value="Premium">
                                <label for="administrador">Premium</label><br>
                            </div>

                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="fechaDesde" class="form-label">Fecha de inicio</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-calendar3"></i>
                                            </span>
                                            <input type="date" 
                                                class="form-control date-input" 
                                                id="fechaDesde" 
                                                name="fechaDesde" 
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fechaHasta" class="form-label">Fecha de fin</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-calendar3"></i>
                                            </span>
                                            <input type="date" 
                                                class="form-control date-input" 
                                                id="fechaHasta" 
                                                name="fechaHasta" 
                                                required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                        

                            <button type="submit" class="btn-enviar mt-3" name="enviar">Crear novedad</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Alerta de éxito -->
    <?php if(isset($_SESSION['novedad_ok'])) { ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Novedad creada exitosamente',
            text: '<?php echo $_SESSION['novedad_ok']; ?>',
        });
        </script>
        <?php
            unset($_SESSION['novedad_ok']); 
        } 
    ?>
    
    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>
</html>
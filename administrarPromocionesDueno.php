<?php

$hoy= date('Y-m-d');
$tresSemanas = date('Y-m-d', strtotime('+21 days'));
if($localDueno!=[]){
    $codLocal=$localDueno['codLocal'];
    $sqlPromosDeLocal="SELECT codPromo, textoPromo, fechaHastaPromo, categoriaPromo FROM promociones WHERE codLocal='$codLocal'";
    $resultadoPromos=consultaSQL($sqlPromosDeLocal);}
if(isset($_POST['crearPromo'])){
    $diasSemana= implode(",", $_POST['dias']);
    $estado= 'pendiente';
    $categoriaClientePromo= $_POST['categoriaPromon'];
    $categoriaLocalPromo= $localDueno['categoriaLocal'];
    $codLocalPromo=$localDueno['codLocal'];
    $nombrePromo=$_POST['nombrePromo'];
    $fechaDesde=$_POST['fechaInicio'];
    $fechaHata=$_POST['fechaFin'];

    $sqlAgregaPromo="INSERT INTO promociones (textoPromo, fechaDesdePromo, fechaHastaPromo, categoriaPromo, categoriaCliente, diasSemana, estadoPromo, codLocal) VALUES ('$nombrePromo', '$fechaDesde', '$fechaHata', '$categoriaLocalPromo', '$categoriaClientePromo', '$diasSemana', '$estado', '$codLocalPromo')";
    consultaSQL($sqlAgregaPromo);
    $_SESSION['carga_ok'] = "La promo fue creada correctamente.";
}
if(isset($_POST['eliminaPromo'])){
    $codPromoEliminada=$_POST['promoEliminada'];
    $sqlEliminaPromo="DELETE FROM promociones WHERE codPromo='$codPromoEliminada'";
    consultaSQL($sqlEliminaPromo);
    $_SESSION['baja_ok'] = "La promo fue eliminada correctamente.";
}
?>
<div class="form-container mb-4">
    <div class="form-header">
        <h2>Crear Promoción</h2>
    </div>
    <div class="form-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Título de la Promoción</label>
                <input type="text" class="form-control" placeholder="Ej: 2x1 en cafés" required name="nombrePromo">
            </div>

            <div class="mb-3">
                <label for="fechaInicio" class="form-label">Fecha de inicio</label>
                <div style="position: relative; cursor: pointer;" onclick="document.getElementById('fechaInicio').showPicker()">
                    <input type="date" id="fechaInicio" name="fechaInicio" class="form-control" style="cursor: pointer; width: 100%;" min="<?php echo $hoy; ?>" max="<?php echo $tresSemanas; ?>" required>
                </div>
            </div>

            <script>
            document.getElementById("fechaInicio").addEventListener("change", function () {
                let inicio = this.value;
                let inputFin = document.getElementById("fechaFin");
                inputFin.min = inicio;
                if (inputFin.value < inicio) {
                    inputFin.value = "";
                }
            });
            </script>

            <div class="mb-3">
                <label for="fechaFin" class="form-label">Fecha de fin</label>
                <div style="position: relative; cursor: pointer;" onclick="document.getElementById('fechaFin').showPicker()">
                    <input type="date" id="fechaFin" name="fechaFin" class="form-control" style="cursor: pointer; width: 100%;" min="<?php echo $hoy; ?>" max="<?php echo $tresSemanas; ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Días disponibles</label>

                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias[]" value="lunes" id="lunes">
                    <label class="form-check-label" for="lunes">Lunes</label>
                    </div>

                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias[]" value="martes" id="martes">
                    <label class="form-check-label" for="martes">Martes</label>
                    </div>

                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias[]" value="miercoles" id="miercoles">
                    <label class="form-check-label" for="miercoles">Miércoles</label>
                    </div>

                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias[]" value="jueves" id="jueves">
                    <label class="form-check-label" for="jueves">Jueves</label>
                    </div>

                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias[]" value="viernes" id="viernes">
                    <label class="form-check-label" for="viernes">Viernes</label>
                    </div>

                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias[]" value="sabado" id="sabado">
                    <label class="form-check-label" for="sabado">Sábado</label>
                    </div>

                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias[]" value="domingo" id="domingo">
                    <label class="form-check-label" for="domingo">Domingo</label>
                    </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Seleccionar categoria de la promocion.</label>    
                <select class="form-select" required name="categoriaPromon">
                    <option value="Inicial">Inicial</option>
                    <option value="Medium">Medium</option>
                    <option value="Premium">Premium</option>
                </select>
            
            <button type="submit" class="btn-enviar mt-3" name="crearPromo">Crear Promoción</button>
            </div>
        </form>
    </div>
</div>

<!-- Formulario eliminación promoción -->
<div class="form-container">    
    <div class="form-header">
        <h2>Eliminar Promoción</h2>
    </div>
    <div class="form-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Seleccionar promoción a eliminar</label>
                <select class="form-select" name="promoEliminada">
                    <option value="" disabled selected>Promociones Activas.</option>
                        <?php
                        if($localDueno!=[]){
                        while($promo = mysqli_fetch_assoc($resultadoPromos)){
                            ?><option value="<?php echo $promo['codPromo'];?>">Id: <?php echo $promo['codPromo'];?>,       Titulo: <?php echo $promo['textoPromo'];?>,       Caducidad: <?php echo $promo['fechaHastaPromo'];?>,       Categoria: <?php echo $promo['categoriaPromo'];?></option>
                        <?php } 
                        }else{ ?> 
                        <option value="">No se tienen promociones vigentes.</option>
                        <?php }
                            ?>
                </select>
            <button type="submit" class="btn-enviar mt-3" name="eliminaPromo">Eliminar</button>
            </div>
        </form>
    </div>
</div>

<?php  if(isset($_SESSION['carga_ok'])) { ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Registro de promocion exitoso',
        text: '<?php echo $_SESSION['carga_ok']; ?>',
    });
    </script>
    <?php
        unset($_SESSION['carga_ok']); 
    } 
  ?>
<?php  if(isset($_SESSION['baja_ok'])) { ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    Swal.fire({
        icon: 'warning',
        title: 'Eliminacion de promocion exitoso',
        text: '<?php echo $_SESSION['baja_ok']; ?>',
    });
    </script>
    <?php
        unset($_SESSION['baja_ok']); 
    } 
  ?>
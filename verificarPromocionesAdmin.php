<?php
include("funciones.php");



function promocionesPendiente() {
    $sql = "SELECT * FROM promociones WHERE estadoPromo = 'pendiente'"; 
    $res = consultaSQL($sql);

    if(mysqli_num_rows($res) > 0){
        while($fila = mysqli_fetch_assoc($res)){
            echo "
            <div class='border rounded-3 p-3 mb-3 d-flex justify-content-between align-items-center'>
                <div>
                    <p class='mb-1 fw-bold' style='color: var(--color-negro);'>{$fila['textoPromo']}</p>
                    <p class='mb-0' style='color: var(--color-gris);'>
                        {$fila['categoriaCliente']} | ID: {$fila['codLocal']} | 
                        Desde: {$fila['fechaDesdePromo']} | Hasta: {$fila['fechaHastaPromo']}
                    </p>
                </div>
                <div>
                    <form method='POST' style='display: inline-block;'>
                        <input type='hidden' name='codPromo' value='{$fila['codPromo']}'>
                        <input type='hidden' name='accion' value='aceptar'>
                        <button type='submit' class='btn btn-success btn-sm me-2'>Aceptar</button>
                    </form>
                    <form method='POST' style='display: inline-block;'>
                        <input type='hidden' name='codPromo' value='{$fila['codPromo']}'>
                        <input type='hidden' name='accion' value='rechazar'>
                        <button type='submit' class='btn btn-danger btn-sm'>Rechazar</button>
                    </form>
                </div>
            </div>";
        }
    } else {
        echo '<div class="list-group-item">Sin resultados</div>';
    }
}
?>
<?php



if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && isset($_POST['codPromo'])){
    $codPromo = intval($_POST['codPromo']);
    $accion = $_POST['accion'];
    
    if($accion === 'aceptar'){
        $sql = "UPDATE promociones SET estadoPromo = 'aprobada' WHERE codPromo = $codPromo";
        $resultado = consultaSQL($sql);
        
        if($resultado){
            $mensaje = "<div class='alert alert-success'>Promocion aceptada correctamente</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al aceptar usuario</div>";
        }
        
    } elseif($accion === 'rechazar'){
        $sql = "UPDATE promociones SET estadoPromo = 'denegada' WHERE codPromo = $codPromo";
        $resultado = consultaSQL($sql);
        
        if($resultado){
            $mensaje = "<div class='alert alert-warning'>Promocion rechazada correctamente</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al rechazar usuario</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Promociones Pendientes</title>
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
  
</head>
    
    <div class="container mt-4">
       
        
        <?php 
       
        if(isset($mensaje)){
            echo $mensaje;
        }
        ?>
        
        <div class="mt-4">
            <?php promocionesPendiente(); ?>
        </div>
    </div>
</body>
</html>
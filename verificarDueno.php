<?php
include("funciones.php");
function dueñosPendiente() {
    $sql = "SELECT codUsuario, nombre, apellido FROM usuarios WHERE pendiente = 'si'";
    
    $res = consultaSQL($sql);

    if(mysqli_num_rows($res) > 0){
        while($fila = mysqli_fetch_assoc($res)){
            $nombreCompleto = $fila['nombre'] . ' ' . $fila['apellido'];
            echo "
            <div class='border rounded-3 p-3 mb-3 d-flex justify-content-between align-items-center'>
                <div>
                    <p class='mb-1 fw-bold' style='color: var(--color-negro);'>{$nombreCompleto}</p>
                    <p class='mb-0' style='color: var(--color-gris);'>ID: {$fila['codUsuario']}</p>
                </div>
                <div>
                    <form method='POST' style='display: inline-block;'>
                        <input type='hidden' name='codUsuario' value='{$fila['codUsuario']}'>
                        <input type='hidden' name='accion' value='aceptar'>
                        <button type='submit' class='btn btn-success btn-sm me-2'>Aceptar</button>
                    </form>
                    <form method='POST' style='display: inline-block;'>
                        <input type='hidden' name='codUsuario' value='{$fila['codUsuario']}'>
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



if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && isset($_POST['codUsuario'])){
    $codUsuario = intval($_POST['codUsuario']);
    $accion = $_POST['accion'];
    
    if($accion === 'aceptar'){
        $sql = "UPDATE usuarios SET pendiente = 'no' WHERE codUsuario = $codUsuario";
        $resultado = consultaSQL($sql);
        
        if($resultado){
            $mensaje = "<div class='alert alert-success'>Usuario aceptado correctamente</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al aceptar usuario</div>";
        }
        
    } elseif($accion === 'rechazar'){
        $sql = "UPDATE usuarios SET pendiente = 'rechazado' WHERE codUsuario = $codUsuario";
        $resultado = consultaSQL($sql);
        
        if($resultado){
            $mensaje = "<div class='alert alert-warning'>Usuario rechazado correctamente</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al rechazar usuario</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Usuarios Pendientes</title>
   
</head>
<body>
    <div class="container mt-4">
       
        
        <?php 
       
        if(isset($mensaje)){
            echo $mensaje;
        }
        ?>
        
        <div class="mt-4">
            <?php dueñosPendiente(); ?>
        </div>
    </div>
</body>
</html>
<?php
include("funciones.php");

if(isset($_POST['query'])){
    $q = $_POST['query'];

    $sql = "SELECT nombreLocal FROM locales 
            WHERE nombreLocal LIKE '%$q%' 
            LIMIT 10";

    $res = consultaSQL($sql);

    if(mysqli_num_rows($res) > 0){
        while($fila = mysqli_fetch_assoc($res)){
            echo '<a class="list-group-item list-group-item-action item">'
                . $fila['nombreLocal'] .
                '</a>';
        }
    } else {
        echo '<div class="list-group-item">Sin resultados</div>';
    }
}
?>
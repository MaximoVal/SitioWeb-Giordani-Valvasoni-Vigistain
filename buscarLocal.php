<?php

include("funciones.php"); 

if (isset($_POST['query'])) {

    $texto = $_POST['query'];

    $sql = "SELECT nombreLocal 
            FROM locales 
            WHERE nombreLocal LIKE '%$texto%' 
            LIMIT 5";

    $resultado = consultaSQL($sql);

    if (mysqli_num_rows($resultado) > 0) {

        while ($fila = mysqli_fetch_assoc($resultado)) {
            echo '<a href="#" 
                     class="list-group-item list-group-item-action item border-1">'
                     . htmlspecialchars($fila['nombreLocal']) . 
                 '</a>';
        }

    } else {
        echo '<p class="list-group-item border-1">No se encontraron resultados</p>';
    }
}
?>

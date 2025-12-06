<?php
// buscarLocal.php
include("funciones.php"); // Incluye la conexión + consultaSQL()

if (isset($_POST['query'])) {

    $texto = $_POST['query'];

    // --- Seguridad mínima si tu consultaSQL no usa prepared statements ---
    // (Si consultaSQL NO protege, entonces activá esta línea)
    // $texto = mysqli_real_escape_string($conexion, $texto);

    // Búsqueda de locales (máximo 5 resultados)
    $sql = "SELECT nombreLocal 
            FROM locales 
            WHERE nombreLocal LIKE '%$texto%' 
            LIMIT 5";

    $resultado = consultaSQL($sql);

    if (mysqli_num_rows($resultado) > 0) {

        // Mostramos cada resultado como una opción clickeable
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

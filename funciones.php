<?php



function consultaSQL($consulta_sql){
    $conexion = mysqli_connect("sql109.infinityfree.com", "if0_40512518", "CvxOy5VdkRi6Ru");
    mysqli_select_db($conexion, "if0_40512518_paseofortuna");

    $resultado = mysqli_query($conexion, $consulta_sql);
    mysqli_close($conexion);
    return $resultado;
}

function consultaLocales($consulta_sql){
    $conexion = mysqli_connect("localhost", "root", "");
    mysqli_select_db($conexion, "paseofortuna");

    $resultado = mysqli_query($conexion, $consulta_sql);
    mysqli_close($conexion);
    return $resultado;
}

function insertarRegistroNuevo($email, $password){
    $sql = "INSERT INTO usuarios (email, contraseña) VALUES ('$email', '$password')";
    return consultaSQL($sql);
}

function actualizaCategoriaCliente($codCliente){
    $sqlCliente="SELECT * FROM usuarios WHERE codUsuario='$codCliente'";
    $resultCliente=consultaSQL($sqlCliente);
    $cliente=mysqli_fetch_assoc($resultCliente);
    if($cliente['cantPromoUsada']>=6){
        $sqlActualizaUsoCliente="UPDATE usuarios SET categoriaCliente='Premium' WHERE codUsuario='$codCliente'";
        consultaSQL($sqlActualizaUsoCliente);
    }elseif($cliente['cantPromoUsada']<=6 && $cliente['cantPromoUsada']>=3){
        $sqlActualizaUsoCliente="UPDATE usuarios SET categoriaCliente='Medium' WHERE codUsuario='$codCliente'";
        consultaSQL($sqlActualizaUsoCliente);
    }
}
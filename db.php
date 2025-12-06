<?php
// Credenciales de InfinityFree (búscalas en tu panel VistaPanel)
$host = "sql109.infinityfree.com"; 
$user = "if0_40512518"; 
$pass = "CvxOy5VdkRi6Ru"; 
$db   = "if0_40512518_paseofortuna";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
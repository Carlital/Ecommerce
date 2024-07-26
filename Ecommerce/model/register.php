<?php
include("../Config/conexion.php");

$cedula = $_POST['cedula'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$email = $_POST['correo'];
$password = $_POST['contrasena'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$genero = $_POST['genero'];

$sql = "INSERT INTO Cliente (Cedula, Nombre, Apellido, Correo, Contrasena, Fecha_Nacimiento, Genero) VALUES ('$cedula', '$nombre', '$apellido', '$email', MD5('$password'), '$fecha_nacimiento', '$genero')";

if (mysqli_query($conec, $sql)) {
    header("Location: ../index.php");
    exit();
} else {
    echo "Error en el registro: " . mysqli_error($conec);
}
?>

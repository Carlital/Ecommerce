<?php
include("../Config/conexion.php");

$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];

$sql = "SELECT * FROM Cliente WHERE Correo='$correo' AND Contrasena=MD5('$contrasena')";
$result = mysqli_query($conec, $sql);

if (mysqli_num_rows($result) > 0) {
    session_start();
    $cliente = mysqli_fetch_assoc($result);
    $_SESSION['loggedin'] = true;
    $_SESSION['correo'] = $correo;
    $_SESSION['ID_Cliente'] = $cliente['Cedula'];
    if ($correo == 'admin@admin.com') {
        header('Location: ../view/admin.php');
    } else {
        header('Location: ../view/clients.php');
    }
} else {
    echo "Credenciales inválidas.";
}

mysqli_close($conec);
?>

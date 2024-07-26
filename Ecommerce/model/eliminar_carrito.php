<?php
session_start();
include("../Config/conexion.php");

if (!isset($_SESSION['ID_Cliente'])) {
    header("Location: login.php");
    exit();
}

$id_cliente = $_SESSION['ID_Cliente'];
$id_carrito = $_GET['id'];

$sql = "DELETE FROM carrito WHERE ID='$id_carrito' AND ID_Cliente='$id_cliente'";
if ($conec->query($sql) === TRUE) {
    header("Location: carrito.php");
} else {
    echo "Error: " . $conec->error;
}
?>

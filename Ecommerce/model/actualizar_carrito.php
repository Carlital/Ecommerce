<?php
session_start();
include("../Config/conexion.php");

if (!isset($_SESSION['ID_Cliente'])) {
    header("Location: login.php");
    exit();
}

$id_cliente = $_SESSION['ID_Cliente'];

foreach ($_POST['cantidad'] as $id_carrito => $cantidad) {
    $sql = "UPDATE carrito SET Cantidad = '$cantidad' WHERE ID = '$id_carrito' AND ID_Cliente = '$id_cliente'";
    $conec->query($sql);
}

header("Location: carrito.php");
?>

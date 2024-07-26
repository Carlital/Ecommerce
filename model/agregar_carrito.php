<?php
session_start();
include("../Config/conexion.php");

if (!isset($_SESSION['ID_Cliente'])) {
    header("Location: ../view/login.html");
    exit();
}

$id_cliente = $_SESSION['ID_Cliente'];
$cod_prod = $_GET['id'];
$cantidad = 1;

$sql = "SELECT * FROM carrito WHERE ID_Cliente='$id_cliente' AND Cod_Prod='$cod_prod'";
$result = $conec->query($sql);

if ($result->num_rows > 0) {
    $sql = "UPDATE carrito SET Cantidad = Cantidad + 1 WHERE ID_Cliente='$id_cliente' AND Cod_Prod='$cod_prod'";
} else {
    $sql = "SELECT Precio_Venta FROM Producto WHERE Codigo='$cod_prod'";
    $result = $conec->query($sql);
    $row = $result->fetch_assoc();
    $precio_venta = $row['Precio_Venta'];

    $sql = "INSERT INTO carrito (ID_Cliente, Cod_Prod, Cantidad, Precio_Venta) VALUES ('$id_cliente', '$cod_prod', '$cantidad', '$precio_venta')";
}

if ($conec->query($sql) === TRUE) {
    echo "Producto agregado al carrito";
} else {
    echo "Error: " . $sql . "<br>" . $conec->error;
}
?>

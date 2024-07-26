<?php
session_start();
include("../Config/conexion.php");

$id_cliente = $_SESSION['ID_Cliente'] ?? null;
$count = 0;

if ($id_cliente) {
    $sql = "SELECT SUM(Cantidad) as count FROM carrito WHERE ID_Cliente='$id_cliente'";
    $result = $conec->query($sql);
    $row = $result->fetch_assoc();
    $count = $row['count'];
}

echo json_encode(['count' => min($count, 9)]);
?>

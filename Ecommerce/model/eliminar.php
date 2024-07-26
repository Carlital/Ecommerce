<?php
include("../Config/conexion.php");

if (isset($_GET['id'])) {
    $codigo = $_GET['id'];
    $query = "DELETE FROM Producto WHERE Codigo = '$codigo'";
    $result = $conec->query($query);

    if ($result) {
        echo "<script>alert('Producto eliminado correctamente'); window.location='../index.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el producto'); window.location='../index.php';</script>";
    }
} else {
    echo "<script>alert('ID no válido'); window.location='../index.php';</script>";
}
?>

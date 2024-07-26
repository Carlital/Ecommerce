<?php
include("../Config/conexion.php");

if (isset($_POST['codigo'])) {
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $marca = $_POST['marca'];
    $precio_venta = $_POST['precio_venta'];
    $color = $_POST['color'];
    $talla = $_POST['talla'];
    $tipo= $_POST['tipo'];
    $stock = $_POST['stock'];
    $cancel=$_POST['cancel'];
    $query = "UPDATE Producto SET Nombre='$nombre', Marca='$marca', Precio_Venta='$precio_venta', Color='$color', Talla='$talla', Tipo='$tipo',Stock='$stock'";

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $imagen = $_FILES['imagen']['name'];
        $temp_name = $_FILES['imagen']['tmp_name'];
        $upload_dir = '../img/';

        if (move_uploaded_file($temp_name, $upload_dir . $imagen)) {
            $query .= ", Imagen='$imagen'";
        } else {
            echo "<script>alert('Error al subir la imagen'); window.location='../view/admin.php';</script>";
            exit;
        }
    }

    $query .= " WHERE Codigo='$codigo'";
    $result = $conec->query($query);

    if ($result && $cancel!=1) {
        echo "<script>alert('Producto actualizado correctamente'); window.location='../view/admin.php';</script>";
    }else{
        echo "<script>window.location='../view/admin.php';</script>"; 
    }
} else {
    echo "<script>alert('Datos no válidos'); window.location='../view/admin.php';</script>";
}
?>

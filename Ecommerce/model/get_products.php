<?php
include("../Config/conexion.php");

$codigo = $_POST['codigo'];
$categoria = $_POST['categoria'];
$nombre = $_POST['nombre'];
$marca = $_POST['marca'];
$precio_coste = $_POST['precio_coste'];
$precio_venta = $_POST['precio_venta'];
$color = $_POST['color'];
$talla = $_POST['talla'];
$tipo_cuello = $_POST['tipo_cuello'];
$stock = $_POST['stock'];
$genero = $_POST['genero'];
$tipo = $_POST['tipo'];
$imagen = $_FILES['imagen']['name'];
$target_dir = "../img/";
$target_file = $target_dir . basename($imagen);
if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
    $imagen_path = $target_file;
} else {
    $imagen_path = '';
}
if ($categoria === 'pantalones') {
    $tipo_cuello = NULL;
}

$sql = "INSERT INTO Producto (Codigo, Categoria, Nombre, Marca, Precio_Coste, Precio_Venta, Color, Talla, Tipo_Cuello, Stock, Imagen, Genero, Tipo)
        VALUES ('$codigo', '$categoria', '$nombre', '$marca', '$precio_coste', '$precio_venta', '$color', '$talla', ".($tipo_cuello === NULL ? "NULL" : "'$tipo_cuello'").", '$stock', '$imagen_path', '$genero', '$tipo')";

if ($conec->query($sql) === TRUE) {
    header("Location: ../view/admin.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conec->error;
}

?>

<?php
include("../Config/conexion.php");

$categoria = $_GET['categoria'] ?? 'camisetas';
$search = $_GET['search'] ?? '';
$genero = $_GET['genero'] ?? '';
$marca = $_GET['marca'] ?? '';
$color = $_GET['color'] ?? '';
$talla = $_GET['talla'] ?? '';
$tipo_cuello = $_GET['tipo_cuello'] ?? '';
$tipo = $_GET['tipo'] ?? '';

$query = "SELECT * FROM Producto WHERE Categoria='$categoria'";

if ($search != '') {
    $query .= " AND Nombre LIKE '%$search%'";
}
if ($genero != '') {
    $query .= " AND Genero='$genero'";
}
if ($marca != '') {
    $query .= " AND Marca='$marca'";
}
if ($color != '') {
    $query .= " AND Color='$color'";
}
if ($talla != '') {
    $query .= " AND Talla='$talla'";
}
if ($tipo != '') {
    $query .= " AND Tipo='$tipo'";
}
if ($tipo_cuello != '') {
    $query .= " AND Tipo_Cuello='$tipo_cuello'";
}

$result = $conec->query($query);

if ($result->num_rows > 0) {
    echo '<div class="row">';
    while ($row = $result->fetch_assoc()) {
        echo '<div class="col-md-4 mb-4">';
        echo '<div class="card">';
        if (!empty($row['Imagen'])) {
            echo '<img src="img/'.$row['Imagen'].'" class="card-img-top" alt="'.$row['Nombre'].'">';
        }
        echo '<div class="card-body">';
        echo '<h5 class="card-title">'.$row['Nombre'].'</h5>';
        echo '<a href="#" class="btn btn-primary">Agregar al carrito</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo '<p>No se encontraron productos.</p>';
}
?>

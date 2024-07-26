<?php
include("../Config/conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ganancia = $_POST['ganancia'];
    $iva = $_POST['iva'];
    $promocion = $_POST['promocion'];

    $query = "UPDATE Configuracion SET Ganancia = ?, IVA = ?, Promocion = ? WHERE ID_Conf = 1";
    $stmt = $conec->prepare($query);
    $stmt->bind_param("iii", $ganancia, $iva, $promocion);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
}
?>

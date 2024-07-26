<?php
session_start();
include("../Config/conexion.php");
require('../fpdf182/fpdf.php');

if (!isset($_SESSION['ID_Cliente'])) {
    header("Location: login.php");
    exit();
}

$id_cliente = $_SESSION['ID_Cliente'];
$total = $_GET['total'];

class PDF extends FPDF
{
    function Header()
    {
        $this->Image('../img/arq1.jpg', 10, 8, 33);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Factura', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Fecha: ' . date('d/m/Y'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-30);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 1, 'C');
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Ubicación: Riobamba, Ecuador'), 0, 0, 'C');
    }

    function TableHeader()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(200, 210, 255);
        $this->Cell(30, 10, utf8_decode('Código'), 1, 0, 'C', true);
        $this->Cell(70, 10, 'Nombre', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Subtotal', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Cantidad', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Total', 1, 0, 'C', true);
        $this->Ln();
    }

    function TableRow($data)
    {
        $this->Cell(30, 10, $data['Cod_Prod'], 1);
        $this->Cell(70, 10, $data['Nombre'], 1);
        $this->Cell(30, 10, '$' . number_format($data['Precio_Venta'], 2), 1);
        $this->Cell(30, 10, $data['Cantidad'], 1);
        $this->Cell(30, 10, '$' . number_format($data['Subtotal'], 2), 1);
        $this->Ln();
    }
}

$sql = "INSERT INTO factura (ID_Cliente, Fecha, Total) VALUES ('$id_cliente', NOW(), '$total')";
if ($conec->query($sql) === TRUE) {
    $id_factura = $conec->insert_id;

    $productos = [];
    foreach ($_GET as $key => $value) {
        if ($key != 'total') {
            $cod_prod = $key;
            $cantidad = $value;

            $sql_precio = "SELECT Nombre, Precio_Venta FROM Producto WHERE Codigo='$cod_prod'";
            $result_precio = $conec->query($sql_precio);
            if ($result_precio->num_rows > 0) {
                $row_precio = $result_precio->fetch_assoc();
                $precio_venta = $row_precio['Precio_Venta'];
                $nombre = $row_precio['Nombre'];
                $subtotal = $precio_venta * $cantidad;

                $sql_detalle = "INSERT INTO detalle_factura (ID_Factura, Cod_Prod, Precio_Venta, Cantidad, Subtotal) VALUES ('$id_factura', '$cod_prod', '$precio_venta', '$cantidad', '$subtotal')";
                $conec->query($sql_detalle);

                $sql_stock = "UPDATE Producto SET Stock = Stock - $cantidad WHERE Codigo = '$cod_prod'";
                $conec->query($sql_stock);

                $productos[] = [
                    'Cod_Prod' => $cod_prod,
                    'Nombre' => $nombre,
                    'Precio_Venta' => $precio_venta,
                    'Cantidad' => $cantidad,
                    'Subtotal' => $subtotal
                ];
            }
        }
    }

    $sql = "DELETE FROM carrito WHERE ID_Cliente='$id_cliente'";
    $conec->query($sql);

    $sql_cliente = "SELECT * FROM Cliente WHERE Cedula = '$id_cliente'";
    $result_cliente = $conec->query($sql_cliente);
    $cliente = $result_cliente->fetch_assoc();

    generatePurchaseReport($id_factura, $productos, $total, $cliente);

    echo "Compra registrada exitosamente";
    echo "<script>window.location='../view/clients.php';</script>"; 
} else {
    echo "Error: " . $sql . "<br>" . $conec->error;
}

function generatePurchaseReport($id_factura, $productos, $total, $cliente)
{
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Datos del Cliente:', 0, 1, 'L');
    $pdf->Cell(95, 10, 'Cedula: ' . $cliente['Cedula'], 0, 0, 'L');
    $pdf->Cell(95, 10, 'Nombre: ' . $cliente['Nombre'] . ' ' . $cliente['Apellido'], 0, 1, 'L');
    $pdf->Cell(95, 10, 'Correo: ' . $cliente['Correo'], 0, 0, 'L');
    $pdf->Cell(95, 10, 'Fecha de Nacimiento: ' . $cliente['Fecha_Nacimiento'], 0, 1, 'L');
    $pdf->Cell(95, 10, 'Nro. de Factura: ' . $id_factura, 0, 0, 'L');
    $pdf->Cell(95, 10, 'Total: $' . number_format($total, 2), 0, 1, 'L');
    $pdf->Ln(10);

    $pdf->TableHeader();
    foreach ($productos as $producto) {
        $pdf->TableRow($producto);
    }

    $pdf->Output('I', 'reporte_compra.pdf');
}
?>

<?php
session_start();
include("../Config/conexion.php");
require('../fpdf182/fpdf.php');
date_default_timezone_set('America/Guayaquil');

if (!isset($_SESSION['correo']) || $_SESSION['correo'] != 'admin@admin.com') {
    header("Location: login.php");
    exit();
}

class PDF extends FPDF
{
    function Header()
    {
        $this->Image('../img/arq1.jpg', 10, 8, 33);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Reporte de Productos con Stock Bajo', 0, 1, 'C');
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
        $this->Cell(30, 10, 'Stock', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Precio', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Marca', 1, 0, 'C', true);
        $this->Ln();
    }

    function TableRow($data)
    {
        $this->Cell(30, 10, $data['Codigo'], 1);
        $this->Cell(70, 10, $data['Nombre'], 1);
        $this->Cell(30, 10, $data['Stock'], 1);
        $this->Cell(30, 10, '$' . number_format($data['Precio_Venta'], 2), 1);
        $this->Cell(30, 10, $data['Marca'], 1);
        $this->Ln();
    }
}

function generateLowStockReport($conec)
{
    $pdf = new PDF();
    $pdf->AddPage();

    $sql = "SELECT * FROM Producto WHERE Stock < 5";
    $result = mysqli_query($conec, $sql);

    if (!$result) {
        echo "Error en la consulta de productos: " . mysqli_error($conec);
        exit();
    }

    $productos = [];
    if (mysqli_num_rows($result) > 0) {
        $pdf->TableHeader();
        while ($row = mysqli_fetch_assoc($result)) {
            $productos[] = $row;
            $pdf->TableRow($row);
        }
    } else {
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'No se encontraron productos con stock bajo.', 0, 1, 'C');
    }

    $pdf->Output('I', 'reporte_stock_bajo.pdf');
}

generateLowStockReport($conec);
?>

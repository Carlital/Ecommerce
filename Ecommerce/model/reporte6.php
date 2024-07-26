<?php
session_start();
include("../Config/conexion.php");
require('../fpdf182/fpdf.php');
date_default_timezone_set('America/Guayaquil');

if (!isset($_SESSION['ID_Cliente'])) {
    echo "No hay sesión iniciada.";
    exit();
}

$cliente_id = $_SESSION['ID_Cliente'];

class PDF extends FPDF
{
    private $rowHeight = 30; // Ajuste la altura de la fila para acomodar las imágenes
    private $tableWidth = 170;

    function Header()
    {
        $this->Image('../img/arq1.jpg', 10, 8, 33);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Reporte de Recomendaciones de Productos', 0, 1, 'C');
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
        $this->SetX(($this->GetPageWidth() - $this->tableWidth) / 2);
        $this->Cell(30, $this->rowHeight, utf8_decode('Código'), 1, 0, 'C', true);
        $this->Cell(70, $this->rowHeight, 'Nombre', 1, 0, 'C', true);
        $this->Cell(30, $this->rowHeight, 'Precio', 1, 0, 'C', true);
        $this->Cell(40, $this->rowHeight, 'Imagen', 1, 0, 'C', true);
        $this->SetFont('Arial', '', 12);
        $this->Ln($this->rowHeight);
    }

    function TableRow($data)
    {
        $this->SetX(($this->GetPageWidth() - $this->tableWidth) / 2);
        $this->Cell(30, $this->rowHeight, $data['Codigo'], 1, 0, 'C');
        $this->Cell(70, $this->rowHeight, $data['Nombre'], 1, 0, 'C');
        $this->Cell(30, $this->rowHeight, '$' . number_format((float)$data['Precio_Venta'], 2), 1, 0, 'C');

        $imagePath = '../img/' . $data['Imagen'];
        if (file_exists($imagePath)) {
            $this->Cell(40, $this->rowHeight, $this->Image($imagePath, $this->GetX(), $this->GetY(), 30, 30), 1, 0, 'C');
        } else {
            $this->Cell(40, $this->rowHeight, 'No Image', 1, 0, 'C');
        }
        $this->Ln($this->rowHeight);
    }

    function Recommendations($data)
    {
        $this->SetFont('Arial', '', 12);
        foreach ($data as $row) {
            $this->TableRow($row);
        }
    }
}

function generateRecommendationsPDF($conec, $cliente_id)
{
    $pdf = new PDF();
    $pdf->AddPage();

    $sqlCliente = "SELECT Genero FROM Cliente WHERE Cedula = '$cliente_id'";
    $resultCliente = mysqli_query($conec, $sqlCliente);

    if (!$resultCliente) {
        echo "Error en la consulta de cliente: " . mysqli_error($conec);
        exit();
    }

    $cliente = mysqli_fetch_assoc($resultCliente);

    if ($cliente) {
        $genero = $cliente['Genero'];

        $sqlProductos = "SELECT * FROM Producto WHERE Genero = '$genero'";
        $resultProductos = mysqli_query($conec, $sqlProductos);

        if (!$resultProductos) {
            echo "Error en la consulta de productos: " . mysqli_error($conec);
            exit();
        }

        $productos = [];
        if (mysqli_num_rows($resultProductos) > 0) {
            while ($row = mysqli_fetch_assoc($resultProductos)) {
                $productos[] = $row;
            }

            $pdf->TableHeader();
            $pdf->Recommendations($productos);
        } else {
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'No se encontraron productos recomendados.', 0, 1, 'C');
        }
    } else {
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'No se encontró información del cliente.', 0, 1, 'C');
    }

    $pdf->Output('I', 'reporte_recomendaciones.pdf');
}

generateRecommendationsPDF($conec, $cliente_id);
?>

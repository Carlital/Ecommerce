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
    private $rowHeight = 10;
    private $tableWidth = 170;

    function Header()
    {
        $this->Image('../img/arq1.jpg', 10, 8, 33);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Reporte de Facturas', 0, 1, 'C');
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
        $this->Cell(40, $this->rowHeight, 'ID_Factura', 1, 0, 'C', true);
        $this->Cell(60, $this->rowHeight, 'Total', 1, 0, 'C', true);
        $this->Ln();
    }

    function TableRow($data)
    {
        $this->Cell(40, $this->rowHeight, $data['ID_Factura'], 1, 0, 'C');
        $this->Cell(60, $this->rowHeight, $data['Total'], 1, 0, 'C');
        $this->Ln();
    }

    function BarChartAndSummary($data, $summary)
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, utf8_decode('Gráfico de Barras - Total por Factura'), 0, 1, 'C');
        $this->Ln(20);

        $chartWidth = 190;
        $chartHeight = 80;
        $barWidth = $chartWidth / count($data);
        $chartX = ($this->GetPageWidth() - $chartWidth) / 2; 
        $chartY = 50;

        $maxValue = max(array_column($data, 'Total'));
        $scale = $chartHeight / $maxValue;
        foreach ($data as $index => $row) {
            $barHeight = $row['Total'] * $scale;
            $x = $chartX + $index * $barWidth;
            $y = $chartY + $chartHeight - $barHeight;
            $this->Rect($x, $y, $barWidth - 2, $barHeight, 'DF');
            $this->SetXY($x, $chartY + $chartHeight + 2);
            $this->SetFont('Arial', '', 8);
            $this->MultiCell($barWidth - 2, 5, $row['ID_Factura'], 0, 'C');
        }

        $this->Line($chartX, $chartY, $chartX, $chartY + $chartHeight);
        $this->Line($chartX, $chartY + $chartHeight, $chartX + $chartWidth, $chartY + $chartHeight);

        $this->SetFont('Arial', '', 12);
        $this->SetXY($chartX, $chartY + $chartHeight + 10);
        $this->Cell(0, 10, 'Eje X: Facturas', 0, 1, 'C');
        $this->SetXY($chartX - 10, $chartY);
        $this->Cell(0, 10, 'Eje Y: Total', 0, 1, 'C');

        $this->Ln(100);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Resumen del Reporte', 0, 1, 'C');
        $this->Ln(5);

        $this->SetFont('Arial', '', 12);
        foreach ($summary as $line) {
            $this->MultiCell(0, 7, $line);
            $this->Ln(5);
        }
    }
}

function generatePDF($conec)
{
    $pdf = new PDF();
    $pdf->AddPage();

    $sql = "SELECT * FROM Factura";
    $result = mysqli_query($conec, $sql);
    $data = [];

    $totalCounts = [];
    if ($result && mysqli_num_rows($result) > 0) {
        $pdf->TableHeader();
        while ($row = mysqli_fetch_assoc($result)) {
            $pdf->TableRow($row);
            $data[] = $row;
            if (isset($totalCounts[$row['Total']])) {
                $totalCounts[$row['Total']]++;
            } else {
                $totalCounts[$row['Total']] = 1;
            }
        }

        $mostProducts = array_keys($totalCounts, max($totalCounts))[0];
        $leastProducts = array_keys($totalCounts, min($totalCounts))[0];

        $summary = [
            "La factura con el total mayor es: $mostProducts con " . max($totalCounts) . " ocurrencias.",
            "La factura con el total menor es: $leastProducts con " . min($totalCounts) . " ocurrencias."
        ];

        $pdf->BarChartAndSummary($data, $summary);
    } else {
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'No se encontraron datos.', 0, 1, 'C');
    }

    $pdf->Output('I', 'reporte_facturas.pdf');
}

generatePDF($conec);
?>

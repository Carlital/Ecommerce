<?php
include("../Config/conexion.php");
require('../fpdf182/fpdf.php');
date_default_timezone_set('America/Guayaquil');

class PDF extends FPDF
{
    private $rowHeight = 30; // Ajuste la altura de la fila para acomodar las imágenes
    private $tableWidth = 800; // Ajuste para modo apaisado

    function Header()
    {
        $this->Image('../img/arq1.jpg', 10, 8, 33);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Reporte de Productos', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Fecha: ' . date('d/m/Y'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-30);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Pagina ') . $this->PageNo(), 0, 1, 'C');
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Ubicación: Riobamba, Ecuador'), 0, 0, 'C');
    }

    function TableHeader()
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(200, 210, 255);
        $this->Cell(15, $this->rowHeight, 'Codigo', 1, 0, 'C', true);
        $this->Cell(30, $this->rowHeight, 'Categoria', 1, 0, 'C', true);
        $this->Cell(30, $this->rowHeight, 'Nombre', 1, 0, 'C', true);
        $this->Cell(20, $this->rowHeight, 'Marca', 1, 0, 'C', true);
        $this->Cell(25, $this->rowHeight, 'Precio Coste', 1, 0, 'C', true);
        $this->Cell(25, $this->rowHeight, 'Precio Venta', 1, 0, 'C', true);
        $this->Cell(20, $this->rowHeight, 'Color', 1, 0, 'C', true);
        $this->Cell(20, $this->rowHeight, 'Talla', 1, 0, 'C', true);
        $this->Cell(25, $this->rowHeight, 'Tipo Cuello', 1, 0, 'C', true);
        $this->Cell(15, $this->rowHeight, 'Stock', 1, 0, 'C', true);
        $this->Cell(30, $this->rowHeight, 'Imagen', 1, 0, 'C', true);
        $this->Cell(20, $this->rowHeight, 'Genero', 1, 0, 'C', true);
        $this->Cell(20, $this->rowHeight, 'Tipo', 1, 1, 'C', true);
        $this->SetFont('Arial', '', 10);
    }

    function TableRow($data)
    {
        $this->Cell(15, $this->rowHeight, $data['Codigo'], 1, 0, 'C');
        $this->Cell(30, $this->rowHeight, $data['Categoria'], 1, 0, 'C');
        $this->Cell(30, $this->rowHeight, $data['Nombre'], 1, 0, 'C');
        $this->Cell(20, $this->rowHeight, $data['Marca'], 1, 0, 'C');
        $this->Cell(25, $this->rowHeight, $data['Precio_Coste'], 1, 0, 'C');
        $this->Cell(25, $this->rowHeight, $data['Precio_Venta'], 1, 0, 'C');
        $this->Cell(20, $this->rowHeight, $data['Color'], 1, 0, 'C');
        $this->Cell(20, $this->rowHeight, $data['Talla'], 1, 0, 'C');
        $this->Cell(25, $this->rowHeight, $data['Tipo_Cuello'], 1, 0, 'C');
        $this->Cell(15, $this->rowHeight, $data['Stock'], 1, 0, 'C');

        

        $this->Cell(20, $this->rowHeight, $data['Genero'], 1, 0, 'C');
        $this->Cell(20, $this->rowHeight, $data['Tipo'], 1, 1, 'C');
    }

    function BarChartAndSummary($data, $summary)
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, utf8_decode('Gráfico de Barras - Stock por Producto'), 0, 1, 'C');
        $this->Ln(10);

        $chartWidth = 250;
        $chartHeight = 80;
        $barWidth = $chartWidth / count($data);
        $chartX = ($this->GetPageWidth() - $chartWidth) / 2; 
        $chartY = 50;

        $maxValue = max(array_column($data, 'Stock'));
        $scale = $chartHeight / $maxValue;

        foreach ($data as $index => $row) {
            $barHeight = $row['Stock'] * $scale;
            $x = $chartX + $index * $barWidth;
            $y = $chartY + $chartHeight - $barHeight;
            $this->Rect($x, $y, $barWidth - 2, $barHeight, 'DF');
            $this->SetXY($x, $chartY + $chartHeight + 2);
            $this->SetFont('Arial', '', 8);
            $this->MultiCell($barWidth - 2, 5, $row['Marca'], 0, 'C');
        }

        $this->Line($chartX, $chartY, $chartX, $chartY + $chartHeight);
        $this->Line($chartX, $chartY + $chartHeight, $chartX + $chartWidth, $chartY + $chartHeight);

        $this->SetFont('Arial', '', 12);
        $this->SetXY($chartX, $chartY + $chartHeight + 10);
        $this->Cell(0, 10, 'Eje X: Productos', 0, 1, 'C');
        $this->SetXY($chartX - 10, $chartY);
        $this->Cell(0, 10, 'Eje Y: Stock', 0, 1, 'C');

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
    $pdf = new PDF('L'); // Establecer el PDF en modo apaisado
    $pdf->AddPage();

    $sql = "SELECT * FROM Producto";
    $result = mysqli_query($conec, $sql);
    $data = [];

    $brandCounts = [];
    if ($result && mysqli_num_rows($result) > 0) {
        $pdf->TableHeader();
        while ($row = mysqli_fetch_assoc($result)) {
            $pdf->TableRow($row);
            $data[] = $row;
            if (isset($brandCounts[$row['Marca']])) {
                $brandCounts[$row['Marca']]++;
            } else {
                $brandCounts[$row['Marca']] = 1;
            }
        }

        $mostProducts = array_keys($brandCounts, max($brandCounts))[0];
        $leastProducts = array_keys($brandCounts, min($brandCounts))[0];

        $summary = [
            "La marca con más productos es: $mostProducts con " . max($brandCounts) . " productos.",
            "La marca con menos productos es: $leastProducts con " . min($brandCounts) . " productos."
        ];

        $pdf->BarChartAndSummary($data, $summary);
    } else {
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'No se encontraron datos.', 0, 1, 'C');
    }

    $pdf->Output('I', 'reporte_productos.pdf');
}

generatePDF($conec);
?>

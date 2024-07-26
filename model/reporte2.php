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
        $this->Cell(0, 10, 'Reporte de Clientes', 0, 1, 'C');
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
        $this->Cell(30, $this->rowHeight, 'Cedula', 1, 0, 'C', true);
        $this->Cell(40, $this->rowHeight, 'Nombre', 1, 0, 'C', true);
        $this->Cell(40, $this->rowHeight, 'Apellido', 1, 0, 'C', true);
        $this->Cell(60, $this->rowHeight, 'Correo', 1, 1, 'C', true);
        $this->SetFont('Arial', '', 12);
    }

    function TableRow($data)
    {
        $this->SetX(($this->GetPageWidth() - $this->tableWidth) / 2);
        $this->Cell(30, $this->rowHeight, $data['Cedula'], 1, 0, 'C');
        $this->Cell(40, $this->rowHeight, $data['Nombre'], 1, 0, 'C');
        $this->Cell(40, $this->rowHeight, $data['Apellido'], 1, 0, 'C');
        $this->Cell(60, $this->rowHeight, $data['Correo'], 1, 1, 'C');
    }
}

function generatePDF($conec)
{
    $pdf = new PDF();
    $pdf->AddPage();

    $sql = "SELECT * FROM Cliente WHERE Correo != 'admin@admin.com'";
    $result = mysqli_query($conec, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $pdf->TableHeader();
        while ($row = mysqli_fetch_assoc($result)) {
            $pdf->TableRow($row);
        }
    } else {
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'No se encontraron datos.', 0, 1, 'C');
    }

    $pdf->Output('I', 'reporte_clientes.pdf');
}

generatePDF($conec);
?>
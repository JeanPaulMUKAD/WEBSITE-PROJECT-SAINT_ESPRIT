<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    exit('Non autorisé');
}

// Inclusion de FPDF - chemin CORRIGÉ
require_once('fpdf.php');

$input = json_decode(file_get_contents('php://input'), true);
$data = $input['data'];
$filter = $input['filter'];
$date_export = $input['date'];

// Création du PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Liste des Sacrements', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Généré le ' . $date_export, 0, 1, 'C');
$pdf->Ln(10);

// En-têtes du tableau
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(139, 0, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(15, 10, 'ID', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Nom', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Baptême', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Communion', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Confirmation', 1, 1, 'C', true);

// Données
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0, 0, 0);
foreach($data as $row) {
    $pdf->Cell(15, 8, $row['id'], 1);
    $pdf->Cell(50, 8, substr($row['nom'], 0, 25), 1);
    $pdf->Cell(40, 8, $row['bapteme'], 1);
    $pdf->Cell(40, 8, $row['communion'], 1);
    $pdf->Cell(40, 8, $row['confirmation'], 1);
    $pdf->Ln();
}

$pdf->Output('sacrements_' . date('Y-m-d') . '.pdf', 'D');
?>
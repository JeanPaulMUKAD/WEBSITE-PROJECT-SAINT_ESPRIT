<?php
error_reporting(0);
session_start();

if(!isset($_SESSION['admin_id'])) {
    exit('Non autorisé');
}

// Chemin ABSOLU correct (celui que tu as trouvé)
require_once('/home/u913148723/domains/paroisseuniversitairestespritlushi.com/public_html/assets/admin/pages/fpdf.php');

// OU chemin RELATIF (si tu es dans le même dossier)
// require_once('fpdf.php'); // puisque fpdf.php est dans le même dossier que ce script

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['data'])) {
    exit('Données invalides');
}
$data = $input['data'];

// Nettoyer les buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Création du PDF
$pdf = new FPDF();
$pdf->AddPage();

// En-tête
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(139, 0, 0);
$pdf->Cell(0, 10, utf8_decode('PAROISSE SAINT ESPRIT'), 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 10, utf8_decode('Liste des Sacrements'), 0, 1, 'C');
$pdf->Ln(10);

// Informations de génération
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 5, utf8_decode('Généré le : ' . date('d/m/Y H:i:s')), 0, 1, 'R');
$pdf->Ln(5);

// En-têtes du tableau
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(139, 0, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
$pdf->Cell(60, 10, utf8_decode('Nom'), 1, 0, 'C', true);
$pdf->Cell(35, 10, utf8_decode('Baptême'), 1, 0, 'C', true);
$pdf->Cell(35, 10, utf8_decode('Communion'), 1, 0, 'C', true);
$pdf->Cell(35, 10, utf8_decode('Confirmation'), 1, 1, 'C', true);

// Données
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0, 0, 0);
$fill = false;

foreach($data as $row) {
    $pdf->SetFillColor(245, 245, 245);
    
    // Nettoyer les données HTML
    $nom = strip_tags($row['nom']);
    $bapteme = strip_tags($row['bapteme']);
    $communion = strip_tags($row['communion']);
    $confirmation = strip_tags($row['confirmation']);
    
    // Formater les dates si nécessaire
    if ($bapteme != '-' && !empty($bapteme)) {
        $bapteme = date('d/m/Y', strtotime($bapteme));
    }
    if ($communion != '-' && !empty($communion)) {
        $communion = date('d/m/Y', strtotime($communion));
    }
    if ($confirmation != '-' && !empty($confirmation)) {
        $confirmation = date('d/m/Y', strtotime($confirmation));
    }
    
    $pdf->Cell(20, 8, $row['id'], 1, 0, 'C', $fill);
    $pdf->Cell(60, 8, utf8_decode(substr($nom, 0, 30)), 1, 0, 'L', $fill);
    $pdf->Cell(35, 8, utf8_decode($bapteme), 1, 0, 'C', $fill);
    $pdf->Cell(35, 8, utf8_decode($communion), 1, 0, 'C', $fill);
    $pdf->Cell(35, 8, utf8_decode($confirmation), 1, 1, 'C', $fill);
    
    $fill = !$fill;
}

// Pied de page
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 5, utf8_decode('Total des enregistrements : ' . count($data)), 0, 1, 'L');

// Envoyer les en-têtes
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="sacrements_' . date('Y-m-d') . '.pdf"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Générer le PDF
$pdf->Output('D', 'sacrements_' . date('Y-m-d') . '.pdf');
exit;
?>
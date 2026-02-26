<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

require_once '../includes/db_connect.php';

// Récupérer les données
$query = "SELECT * FROM visiteurs ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Configuration des en-têtes pour forcer le téléchargement
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="membres_cev_' . date('Y-m-d') . '.csv"');

// Créer le fichier en mémoire
$output = fopen('php://output', 'w');

// BOM UTF-8 pour les accents (indispensable pour Excel)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// --- EN-TÊTE DU DOCUMENT ---
fputcsv($output, ['PAROISSE SAINT ESPRIT'], ';');
fputcsv($output, ['LISTE DES MEMBRES CEV'], ';');
fputcsv($output, ['Généré le : ' . date('d/m/Y H:i')], ';');
fputcsv($output, [], ';'); // Ligne vide

// --- EN-TÊTES DU TABLEAU ---
$headers = ['ID', 'Nom', 'Email', 'Téléphone', 'Adresse', 'CEV', 'Nature', 'Date d\'inscription'];
fputcsv($output, $headers, ';');

// --- DONNÉES ---
while ($row = mysqli_fetch_assoc($result)) {
    $ligne = [
        $row['id'],
        $row['nom'],
        $row['mail'],
        $row['phone'],
        $row['adresse'],
        $row['cev'],
        $row['nature'],
        date('d/m/Y', strtotime($row['created_at']))
    ];
    fputcsv($output, $ligne, ';');
}

// --- STATISTIQUES ---
fputcsv($output, [], ';'); // Ligne vide
fputcsv($output, ['STATISTIQUES'], ';');

// Total membres
fputcsv($output, ['Total membres', mysqli_num_rows($result)], ';');

// CEV distinctes
$cev_query = "SELECT COUNT(DISTINCT cev) as total FROM visiteurs WHERE cev IS NOT NULL AND cev != ''";
$cev_result = mysqli_query($conn, $cev_query);
$cev_total = mysqli_fetch_assoc($cev_result)['total'];
fputcsv($output, ['CEV différentes', $cev_total], ';');

// Natures distinctes
$nature_query = "SELECT COUNT(DISTINCT nature) as total FROM visiteurs WHERE nature IS NOT NULL AND nature != ''";
$nature_result = mysqli_query($conn, $nature_query);
$nature_total = mysqli_fetch_assoc($nature_result)['total'];
fputcsv($output, ['Types de membres', $nature_total], ';');

fclose($output);
exit;
?>
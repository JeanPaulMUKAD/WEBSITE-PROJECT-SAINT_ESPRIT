<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    exit('Non autorisé');
}

require_once '../includes/db_connect.php';

$id = $_GET['id'];
$query = "SELECT * FROM sacrements WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
?>

<div class="detail-card">
    <div class="detail-item">
        <div class="detail-icon"><i class="fas fa-user"></i></div>
        <div class="detail-content">
            <div class="detail-label">Nom complet</div>
            <div class="detail-value"><?php echo htmlspecialchars($row['nom_complet']); ?></div>
        </div>
    </div>
    
    <div class="detail-item">
        <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
        <div class="detail-content">
            <div class="detail-label">Lieu de naissance</div>
            <div class="detail-value"><?php echo htmlspecialchars($row['lieu_naissance']) ?: '-'; ?></div>
        </div>
    </div>
    
    <div class="detail-item">
        <div class="detail-icon"><i class="fas fa-birthday-cake"></i></div>
        <div class="detail-content">
            <div class="detail-label">Date de naissance</div>
            <div class="detail-value"><?php echo $row['date_naissance'] ? date('d/m/Y', strtotime($row['date_naissance'])) : '-'; ?></div>
        </div>
    </div>
    
    <div class="detail-item">
        <div class="detail-icon"><i class="fas fa-male"></i></div>
        <div class="detail-content">
            <div class="detail-label">Nom du père</div>
            <div class="detail-value"><?php echo htmlspecialchars($row['nom_pere']) ?: '-'; ?></div>
        </div>
    </div>
    
    <div class="detail-item">
        <div class="detail-icon"><i class="fas fa-female"></i></div>
        <div class="detail-content">
            <div class="detail-label">Nom de la mère</div>
            <div class="detail-value"><?php echo htmlspecialchars($row['nom_mere']) ?: '-'; ?></div>
        </div>
    </div>
    
    <div class="detail-item">
        <div class="detail-icon"><i class="fas fa-hand-holding-heart"></i></div>
        <div class="detail-content">
            <div class="detail-label">Nom du parrain</div>
            <div class="detail-value"><?php echo htmlspecialchars($row['nom_parrain']) ?: '-'; ?></div>
        </div>
    </div>
    
    <div class="detail-item">
        <div class="detail-icon"><i class="fas fa-water"></i></div>
        <div class="detail-content">
            <div class="detail-label">Date de baptême</div>
            <div class="detail-value"><?php echo $row['date_bapteme'] ? date('d/m/Y', strtotime($row['date_bapteme'])) : '-'; ?></div>
        </div>
    </div>
    
    <div class="detail-item">
        <div class="detail-icon"><i class="fas fa-bread-slice"></i></div>
        <div class="detail-content">
            <div class="detail-label">Date de communion</div>
            <div class="detail-value"><?php echo $row['date_communion'] ? date('d/m/Y', strtotime($row['date_communion'])) : '-'; ?></div>
        </div>
    </div>
    
    <div class="detail-item">
        <div class="detail-icon"><i class="fas fa-dove"></i></div>
        <div class="detail-content">
            <div class="detail-label">Date de confirmation</div>
            <div class="detail-value"><?php echo $row['date_confirmation'] ? date('d/m/Y', strtotime($row['date_confirmation'])) : '-'; ?></div>
        </div>
    </div>
</div>

<div class="text-muted text-center mt-3">
    <small><i class="far fa-clock mr-1"></i>Enregistré le <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></small>
</div>
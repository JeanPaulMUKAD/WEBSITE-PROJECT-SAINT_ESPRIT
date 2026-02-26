<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    exit('Non autorisé');
}

require_once '../includes/db_connect.php';

$id = intval($_GET['id']);

// Récupérer les informations du membre
$query = "SELECT * FROM visiteurs WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo '<div class="alert alert-danger">Membre non trouvé</div>';
    exit;
}

// Formatage des dates
$date_inscription = date('d/m/Y H:i', strtotime($row['created_at']));
$inscription_fr = strftime('%d %B %Y à %H:%M', strtotime($row['created_at']));
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
?>

<style>
    .detail-container {
        padding: 10px;
    }
    
    .detail-header {
        text-align: center;
        margin-bottom: 25px;
        position: relative;
    }
    
    .detail-avatar {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        box-shadow: 0 10px 20px rgba(139,0,0,0.3);
    }
    
    .detail-avatar i {
        font-size: 50px;
        color: white;
    }
    
    .detail-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .detail-badge {
        background: #8B0000;
        color: white;
        padding: 5px 15px;
        border-radius: 25px;
        display: inline-block;
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-top: 25px;
    }
    
    .detail-card {
        background: #f8f9fc;
        border-radius: 15px;
        padding: 15px;
        transition: all 0.3s;
        border-left: 4px solid #8B0000;
    }
    
    .detail-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .detail-card.full-width {
        grid-column: span 2;
    }
    
    .detail-card i {
        font-size: 24px;
        color: #8B0000;
        margin-bottom: 10px;
    }
    
    .detail-card .label {
        font-size: 0.8rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .detail-card .value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        word-break: break-word;
    }
    
    .detail-footer {
        margin-top: 25px;
        padding-top: 15px;
        border-top: 2px dashed #e0e0e0;
        text-align: center;
        color: #666;
        font-size: 0.9rem;
    }
    
    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
        .detail-card.full-width {
            grid-column: span 1;
        }
    }
</style>

<div class="detail-container">
    <div class="detail-header">
        <div class="detail-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="detail-name"><?php echo htmlspecialchars($row['nom']); ?></div>
        <span class="detail-badge">
            <i class="fas fa-tag mr-1"></i>
            <?php echo htmlspecialchars($row['nature'] ?? 'Non défini'); ?>
        </span>
    </div>
    
    <div class="detail-grid">
        <!-- Email -->
        <div class="detail-card">
            <i class="fas fa-envelope"></i>
            <div class="label">Adresse email</div>
            <div class="value"><?php echo htmlspecialchars($row['mail']) ?: 'Non renseigné'; ?></div>
        </div>
        
        <!-- Téléphone -->
        <div class="detail-card">
            <i class="fas fa-phone-alt"></i>
            <div class="label">Téléphone</div>
            <div class="value"><?php echo htmlspecialchars($row['phone']) ?: 'Non renseigné'; ?></div>
        </div>
        
        <!-- Adresse -->
        <div class="detail-card full-width">
            <i class="fas fa-map-marker-alt"></i>
            <div class="label">Adresse</div>
            <div class="value"><?php echo htmlspecialchars($row['adresse']) ?: 'Non renseignée'; ?></div>
        </div>
        
        <!-- CEV -->
        <div class="detail-card">
            <i class="fas fa-church"></i>
            <div class="label">Communauté CEV</div>
            <div class="value"><?php echo htmlspecialchars($row['cev']) ?: 'Non renseignée'; ?></div>
        </div>
        
        <!-- Date d'inscription -->
        <div class="detail-card">
            <i class="fas fa-calendar-alt"></i>
            <div class="label">Inscription</div>
            <div class="value"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></div>
        </div>
    </div>
    
    <div class="detail-footer">
        <i class="far fa-clock mr-1"></i>
        Membre depuis le <?php echo strftime('%d %B %Y', strtotime($row['created_at'])); ?>
    </div>
</div>
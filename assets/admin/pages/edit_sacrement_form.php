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

<form method="POST" action="gestion_sacrements.php" class="edit-form">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nom complet *</label>
                <input type="text" class="form-control" name="nom_complet" value="<?php echo htmlspecialchars($row['nom_complet']); ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Lieu de naissance</label>
                <input type="text" class="form-control" name="lieu_naissance" value="<?php echo htmlspecialchars($row['lieu_naissance']); ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label><i class="fas fa-birthday-cake"></i> Date de naissance</label>
                <input type="date" class="form-control" name="date_naissance" value="<?php echo $row['date_naissance']; ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label><i class="fas fa-male"></i> Nom du père</label>
                <input type="text" class="form-control" name="nom_pere" value="<?php echo htmlspecialchars($row['nom_pere']); ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label><i class="fas fa-female"></i> Nom de la mère</label>
                <input type="text" class="form-control" name="nom_mere" value="<?php echo htmlspecialchars($row['nom_mere']); ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label><i class="fas fa-hand-holding-heart"></i> Nom du parrain</label>
                <input type="text" class="form-control" name="nom_parrain" value="<?php echo htmlspecialchars($row['nom_parrain']); ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label><i class="fas fa-water"></i> Date de baptême</label>
                <input type="date" class="form-control" name="date_bapteme" value="<?php echo $row['date_bapteme']; ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label><i class="fas fa-bread-slice"></i> Date de communion</label>
                <input type="date" class="form-control" name="date_communion" value="<?php echo $row['date_communion']; ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label><i class="fas fa-dove"></i> Date de confirmation</label>
                <input type="date" class="form-control" name="date_confirmation" value="<?php echo $row['date_confirmation']; ?>">
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
        <button type="submit" class="btn-save-edit">
            <i class="fas fa-save mr-2"></i>Enregistrer les modifications
        </button>
    </div>
</form>
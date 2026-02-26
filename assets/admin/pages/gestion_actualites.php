<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

require_once '../includes/db_connect.php';

// Traitement des actions
$message = '';
$message_type = '';

// Ajout d'une actualité
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if($_POST['action'] == 'add') {
        $titre = mysqli_real_escape_string($conn, $_POST['titre']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $contenu = mysqli_real_escape_string($conn, $_POST['contenu']);
        $categorie = mysqli_real_escape_string($conn, $_POST['categorie']);
        $date_evenement = $_POST['date_evenement'] ?: null;
        
        // Gestion de l'upload d'image
        $image = '';
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../../assets/images/actualites/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = 'assets/images/actualites/' . uniqid() . '.' . $extension;
            move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $image);
        }
        
        $query = "INSERT INTO actualites (titre, description, contenu, image, categorie, date_evenement) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $titre, $description, $contenu, $image, $categorie, $date_evenement);
        
        if(mysqli_stmt_execute($stmt)) {
            $message = "Actualité ajoutée avec succès !";
            $message_type = "success";
        } else {
            $message = "Erreur lors de l'ajout : " . mysqli_error($conn);
            $message_type = "error";
        }
    }
    
    // Suppression
    if($_POST['action'] == 'delete' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $query = "DELETE FROM actualites WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if(mysqli_stmt_execute($stmt)) {
            $message = "Actualité supprimée avec succès !";
            $message_type = "success";
        } else {
            $message = "Erreur lors de la suppression.";
            $message_type = "error";
        }
    }
}

// Récupération des actualités
$query = "SELECT * FROM actualites ORDER BY date_evenement DESC, created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Actualités - Administration</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        * { font-family: 'Montserrat', sans-serif; }
        body { background: #f8f9fc; margin: 0; padding: 0; }
        
        .main-content {
            margin-left: 280px;
            padding: 20px 30px;
        }
        
        .page-header {
            background: white;
            border-radius: 20px;
            padding: 25px 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
            color: #333;
        }
        
        .page-title span {
            color: #8B0000;
            font-size: 0.9rem;
            background: #f0f0f0;
            padding: 5px 12px;
            border-radius: 20px;
            margin-left: 15px;
        }
        
        .btn-add {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139,0,0,0.3);
            color: white;
        }
        
        .table-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .table-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .table-title i {
            font-size: 20px;
            color: #8B0000;
            background: rgba(139,0,0,0.1);
            padding: 10px;
            border-radius: 10px;
        }
        
        .badge-categorie {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-homelie { background: #17a2b8; color: white; }
        .badge-evenement { background: #28a745; color: white; }
        .badge-fete { background: #ffc107; color: #333; }
        .badge-anniversaire { background: #8B0000; color: white; }
        .badge-general { background: #6c757d; color: white; }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-view { background: #17a2b8; color: white; }
        .btn-edit { background: #ffc107; color: #333; }
        .btn-delete { background: #dc3545; color: white; }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.2);
        }
        
        /* Modal */
        .modal-content {
            border-radius: 25px;
            border: none;
            overflow: hidden;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            border: none;
            padding: 20px 25px;
        }
        
        .modal-body {
            padding: 30px;
        }
        
        .form-control {
            border: 2px solid #e1e1e1;
            border-radius: 12px;
            padding: 12px 15px;
        }
        
        .form-control:focus {
            border-color: #8B0000;
            box-shadow: 0 0 0 3px rgba(139,0,0,0.1);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139,0,0,0.3);
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <?php if($message): ?>
        <div class="notification">
            <div class="alert alert-<?php echo $message_type == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-circle'; ?> mr-2"></i>
                <?php echo $message; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="page-header">
            <h1 class="page-title">
                Gestion des Actualités
                <span><i class="far fa-calendar-alt mr-1"></i><?php echo date('d/m/Y'); ?></span>
            </h1>
            <button class="btn-add" data-toggle="modal" data-target="#addModal">
                <i class="fas fa-plus-circle mr-2"></i>Nouvelle actualité
            </button>
        </div>
        
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <i class="fas fa-newspaper"></i>
                    <h5>Liste des actualités</h5>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Titre</th>
                            <th>Catégorie</th>
                            <th>Date événement</th>
                            <th>Date création</th>
                            <th>Vues</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $categorie_class = 'badge-general';
                            if($row['categorie'] == 'homelie') $categorie_class = 'badge-homelie';
                            elseif($row['categorie'] == 'evenement') $categorie_class = 'badge-evenement';
                            elseif($row['categorie'] == 'fete') $categorie_class = 'badge-fete';
                            elseif($row['categorie'] == 'anniversaire') $categorie_class = 'badge-anniversaire';
                        ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td>
                                <?php if($row['image']): ?>
                                <img src="../../<?php echo $row['image']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px;">
                                <?php else: ?>
                                <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($row['titre']); ?></strong></td>
                            <td><span class="badge-categorie <?php echo $categorie_class; ?>"><?php echo ucfirst($row['categorie']); ?></span></td>
                            <td><?php echo $row['date_evenement'] ? date('d/m/Y', strtotime($row['date_evenement'])) : '-'; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                            <td><span class="badge badge-info"><?php echo $row['vues']; ?></span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-view" onclick="voirDetails(<?php echo $row['id']; ?>)" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-edit" onclick="modifierActualite(<?php echo $row['id']; ?>)" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action btn-delete" onclick="supprimerActualite(<?php echo $row['id']; ?>)" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal Ajout -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Nouvelle actualité
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="form-group">
                            <label><i class="fas fa-heading mr-2"></i>Titre *</label>
                            <input type="text" class="form-control" name="titre" required>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-align-left mr-2"></i>Description courte</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-file-alt mr-2"></i>Contenu détaillé</label>
                            <textarea class="form-control" name="contenu" rows="4"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-tag mr-2"></i>Catégorie</label>
                                    <select class="form-control" name="categorie">
                                        <option value="general">Général</option>
                                        <option value="homelie">Homélie</option>
                                        <option value="evenement">Événement</option>
                                        <option value="fete">Fête</option>
                                        <option value="anniversaire">Anniversaire</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar mr-2"></i>Date de l'événement</label>
                                    <input type="date" class="form-control" name="date_evenement">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-image mr-2"></i>Image à la une</label>
                            <input type="file" class="form-control-file" name="image" accept="image/*">
                            <small class="text-muted">Formats acceptés : JPG, PNG, GIF (Max 2Mo)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save mr-2"></i>Publier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
                },
                "order": [[0, "desc"]],
                "pageLength": 10
            });
            
            setTimeout(function() {
                $('.notification').fadeOut('slow');
            }, 5000);
        });
        
        function voirDetails(id) {
            // Implémenter la vue détaillée
            Swal.fire({
                title: 'Détails de l\'actualité',
                text: 'Fonctionnalité à venir',
                icon: 'info'
            });
        }
        
        function modifierActualite(id) {
            window.location.href = 'edit_actualite.php?id=' + id;
        }
        
        function supprimerActualite(id) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Cette action est irréversible !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#8B0000',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Créer un formulaire de suppression
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="${id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>
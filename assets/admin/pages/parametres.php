<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

require_once '../includes/db_connect.php';

$message = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Vérification du mot de passe actuel
        if($current_password == 'sec123') { // À remplacer par vérification avec la base de données
            if($new_password == $confirm_password) {
                // Mettre à jour le mot de passe
                $message = 'Mot de passe modifié avec succès !';
            } else {
                $error = 'Les nouveaux mots de passe ne correspondent pas';
            }
        } else {
            $error = 'Mot de passe actuel incorrect';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Administration</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header_admin.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar_admin.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Paramètres</h1>
                </div>

                <?php if($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-key mr-1"></i>
                                Modifier le mot de passe
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label>Mot de passe actuel</label>
                                        <input type="password" class="form-control" name="current_password" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Nouveau mot de passe</label>
                                        <input type="password" class="form-control" name="new_password" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Confirmer le nouveau mot de passe</label>
                                        <input type="password" class="form-control" name="confirm_password" required>
                                    </div>
                                    <button type="submit" name="change_password" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Modifier le mot de passe
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-info-circle mr-1"></i>
                                Informations
                            </div>
                            <div class="card-body">
                                <h5>Administrateur connecté</h5>
                                <p><strong>Nom d'utilisateur:</strong> <?php echo $_SESSION['admin_username']; ?></p>
                                <p><strong>Rôle:</strong> Administrateur</p>
                                <hr>
                                <h5>À propos</h5>
                                <p>Système de gestion paroissiale v1.0</p>
                                <p>Dernière connexion: <?php echo date('d/m/Y H:i:s'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>
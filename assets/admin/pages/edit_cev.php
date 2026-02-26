<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

require_once '../includes/db_connect.php';

$message = '';
$error = '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer les informations du membre
$query = "SELECT * FROM visiteurs WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$membre = mysqli_fetch_assoc($result);

if (!$membre) {
    header('Location: gestion_cev.php?error=notfound');
    exit();
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $mail = mysqli_real_escape_string($conn, $_POST['mail']);
    $adresse = mysqli_real_escape_string($conn, $_POST['adresse']);
    $cev = mysqli_real_escape_string($conn, $_POST['cev']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $nature = mysqli_real_escape_string($conn, $_POST['nature']);
    
    $update_query = "UPDATE visiteurs SET nom=?, mail=?, adresse=?, cev=?, phone=?, nature=? WHERE id=?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "ssssssi", $nom, $mail, $adresse, $cev, $phone, $nature, $id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        header('Location: gestion_cev.php?success=updated');
        exit();
    } else {
        $error = "Erreur lors de la modification : " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un membre - Administration</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Montserrat', sans-serif; }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .edit-container {
            background: white;
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 800px;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .edit-header {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .edit-header::before {
            content: '\f007';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: -20px;
            bottom: -20px;
            font-size: 100px;
            opacity: 0.1;
            color: white;
        }
        
        .edit-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 2rem;
        }
        
        .edit-header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        
        .edit-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-group label i {
            color: #8B0000;
            margin-right: 8px;
            width: 20px;
        }
        
        .form-control {
            border: 2px solid #e1e1e1;
            border-radius: 12px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #8B0000;
            box-shadow: 0 0 0 3px rgba(139,0,0,0.1);
            outline: none;
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%238B0000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
        }
        
        .btn-group-action {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn-save {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            flex: 2;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(139,0,0,0.3);
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            flex: 1;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-3px);
            color: white;
            text-decoration: none;
        }
        
        .alert {
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
            border: none;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .info-card {
            background: #f8f9fc;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-left: 4px solid #8B0000;
        }
        
        .info-card i {
            font-size: 24px;
            color: #8B0000;
        }
        
        .info-card span {
            color: #666;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .edit-body {
                padding: 25px;
            }
            
            .btn-group-action {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <div class="edit-header">
            <h2><i class="fas fa-user-edit mr-2"></i>Modifier un membre</h2>
            <p>Modifiez les informations du membre CEV</p>
        </div>
        
        <div class="edit-body">
            <?php if($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="info-card">
                <i class="fas fa-info-circle"></i>
                <span>Vous êtes en train de modifier le membre #<?php echo $id; ?></span>
            </div>
            
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i>Nom complet *</label>
                            <input type="text" class="form-control" name="nom" value="<?php echo htmlspecialchars($membre['nom']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i>Email</label>
                            <input type="email" class="form-control" name="mail" value="<?php echo htmlspecialchars($membre['mail']); ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i>Adresse</label>
                            <textarea class="form-control" name="adresse" rows="2"><?php echo htmlspecialchars($membre['adresse']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-church"></i>CEV</label>
                            <select name="cev" class="form-control">
                                <option value="">Sélectionnez une CEV</option>
                                <option value="Cev Anaurite" <?php echo $membre['cev'] == 'Cev Anaurite' ? 'selected' : ''; ?>>Cev Anaurite</option>
                                <option value="Cev Bakanja" <?php echo $membre['cev'] == 'Cev Bakanja' ? 'selected' : ''; ?>>Cev Bakanja</option>
                                <option value="Cev Bakhita" <?php echo $membre['cev'] == 'Cev Bakhita' ? 'selected' : ''; ?>>Cev Bakhita</option>
                                <option value="Cev Bethanie" <?php echo $membre['cev'] == 'Cev Bethanie' ? 'selected' : ''; ?>>Cev Bethanie</option>
                                <option value="Cev Coeur Im." <?php echo $membre['cev'] == 'Cev Coeur Im.' ? 'selected' : ''; ?>>Cev Coeur Im.</option>
                                <option value="Cev St Charles L." <?php echo $membre['cev'] == 'Cev St Charles L.' ? 'selected' : ''; ?>>Cev St Charles L.</option>
                                <option value="Cev St Ignace" <?php echo $membre['cev'] == 'Cev St Ignace' ? 'selected' : ''; ?>>Cev St Ignace</option>
                                <option value="Cev St Kizito" <?php echo $membre['cev'] == 'Cev St Kizito' ? 'selected' : ''; ?>>Cev St Kizito</option>
                                <option value="Cev St Vincent de Paul" <?php echo $membre['cev'] == 'Cev St Vincent de Paul' ? 'selected' : ''; ?>>Cev St Vincent de Paul</option>
                                <option value="Cev St Therese" <?php echo $membre['cev'] == 'Cev St Therese' ? 'selected' : ''; ?>>Cev St Therese</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i>Téléphone</label>
                            <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($membre['phone']); ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-tag"></i>Nature *</label>
                            <select name="nature" class="form-control" required>
                                <option value="">Sélectionnez la nature</option>
                                <option value="Membre" <?php echo $membre['nature'] == 'Membre' ? 'selected' : ''; ?>>Membre</option>
                                <option value="Visiteur" <?php echo $membre['nature'] == 'Visiteur' ? 'selected' : ''; ?>>Visiteur</option>
                                <option value="Catéchumène" <?php echo $membre['nature'] == 'Catéchumène' ? 'selected' : ''; ?>>Catéchumène</option>
                                <option value="Fidèle" <?php echo $membre['nature'] == 'Fidèle' ? 'selected' : ''; ?>>Fidèle</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i>Date d'inscription</label>
                            <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i', strtotime($membre['created_at'])); ?>" readonly disabled>
                            <small class="text-muted">Non modifiable</small>
                        </div>
                    </div>
                </div>
                
                <div class="btn-group-action">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                    </button>
                    <a href="gestion_cev.php" class="btn-cancel">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
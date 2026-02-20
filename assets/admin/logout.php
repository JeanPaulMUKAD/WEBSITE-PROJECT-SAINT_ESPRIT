<?php
session_start();

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['admin_id'])) {
    header('Location: connexion/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion - Administration</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            position: relative;
            overflow: hidden;
        }
        
        /* Effet de particules */
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><path d="M50 15 L61 40 L88 44 L67 59 L73 86 L50 72 L27 86 L33 59 L12 44 L39 40 Z" fill="white"/></svg>');
            background-size: 50px 50px;
            pointer-events: none;
        }
        
        .logout-container {
            background: white;
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            padding: 50px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            position: relative;
            z-index: 10;
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
        
        .logout-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 10px 30px rgba(139,0,0,0.3);
        }
        
        .logout-icon i {
            font-size: 50px;
            color: white;
        }
        
        h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        p {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        
        .user-info {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #8B0000;
        }
        
        .user-info i {
            color: #8B0000;
            font-size: 1.2rem;
            width: 30px;
        }
        
        .user-info .row {
            margin-bottom: 10px;
        }
        
        .btn-container {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .btn-logout {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            flex: 1;
            text-decoration: none;
        }
        
        .btn-logout:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(139,0,0,0.3);
            color: white;
            text-decoration: none;
        }
        
        .btn-cancel {
            background: #f0f0f0;
            color: #333;
            border: none;
            padding: 15px 40px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            flex: 1;
            text-decoration: none;
        }
        
        .btn-cancel:hover {
            background: #e0e0e0;
            transform: translateY(-3px);
            color: #333;
            text-decoration: none;
        }
        
        .warning-message {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }
        
        .warning-message i {
            margin-right: 8px;
            color: #856404;
        }
        
        @media (max-width: 480px) {
            .logout-container {
                padding: 30px 20px;
            }
            
            .btn-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        
        <h2>Déconnexion</h2>
        <p>Êtes-vous sûr de vouloir quitter votre session ?</p>
        
        <div class="user-info">
            <div class="row">
                <div class="col-4 text-right"><i class="fas fa-user"></i></div>
                <div class="col-8 text-left"><strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></div>
            </div>
            <div class="row">
                <div class="col-4 text-right"><i class="fas fa-clock"></i></div>
                <div class="col-8 text-left">Dernière activité : <?php echo date('d/m/Y H:i'); ?></div>
            </div>
            <div class="row">
                <div class="col-4 text-right"><i class="fas fa-shield-alt"></i></div>
                <div class="col-8 text-left">Administrateur</div>
            </div>
        </div>
        
        <div class="warning-message">
            <i class="fas fa-exclamation-triangle"></i>
            Vous serez redirigé vers la page de connexion
        </div>
        
        <div class="btn-container">
            <a href="logout_process.php" class="btn-logout">
                <i class="fas fa-check-circle mr-2"></i>Confirmer
            </a>
            <a href="dashboard.php" class="btn-cancel">
                <i class="fas fa-times-circle mr-2"></i>Annuler
            </a>
        </div>
    </div>
    
    <!-- Script pour animation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Empêcher la soumission multiple
            const confirmBtn = document.querySelector('.btn-logout');
            if(confirmBtn) {
                confirmBtn.addEventListener('click', function(e) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Déconnexion...';
                });
            }
        });
    </script>
</body>
</html>
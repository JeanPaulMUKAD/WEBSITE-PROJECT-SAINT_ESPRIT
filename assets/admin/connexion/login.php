<?php
session_start();
require_once '../includes/db_connect.php';

if(isset($_SESSION['admin_id'])) {
    header('Location: ../pages/dashboard.php');
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Ici vous pouvez ajouter votre logique de vérification
    if($username == 'secretariat' && $password == 'sec123') {
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_username'] = $username;
        header('Location: ../pages/dashboard.php');
        exit();
    } else {
        $error = 'Nom d\'utilisateur ou mot de passe incorrect';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Paroisse Saint Esprit</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Effet de particules en arrière-plan */
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><path d="M50 15 L61 40 L88 44 L67 59 L73 86 L50 72 L27 86 L33 59 L12 44 L39 40 Z" fill="white"/></svg>');
            background-size: 50px 50px;
            pointer-events: none;
        }

        .container-fluid {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1400px;
            padding: 20px;
        }

        .login-wrapper {
            background: white;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
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

        /* Colonne de gauche avec l'image */
        .image-side {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            padding: 60px 40px;
            height: 100%;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .image-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(139,0,0,0.3) 0%, rgba(165,42,42,0.3) 100%);
            z-index: 1;
        }

        .image-side img {
            width: 100%;
            max-width: 400px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            position: relative;
            z-index: 2;
            transition: transform 0.3s ease;
            border: 5px solid rgba(255,215,0,0.3);
        }

        .image-side img:hover {
            transform: scale(1.02);
        }

        .image-side h3 {
            color: white;
            font-size: 28px;
            font-weight: 600;
            margin-top: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .image-side p {
            color: rgba(255,255,255,0.9);
            font-size: 16px;
            margin-top: 10px;
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 300px;
        }

        /* Effet de croix dans le fond */
        .image-side .cross-pattern {
            position: absolute;
            font-size: 200px;
            color: rgba(255,255,255,0.1);
            transform: rotate(45deg);
            bottom: -50px;
            right: -50px;
            z-index: 0;
        }

        /* Colonne de droite avec le formulaire */
        .form-side {
            padding: 60px 50px;
            background: white;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h2 {
            color: #8B0000;
            font-weight: 700;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .login-header h2 i {
            color: #8B0000;
            margin-right: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 15px;
            font-weight: 300;
        }

        .welcome-text {
            font-size: 14px;
            color: #8B0000;
            background: rgba(139,0,0,0.1);
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            margin-top: 10px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            color: #333;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
        }

        .form-group label i {
            color: #8B0000;
            width: 20px;
        }

        .form-control {
            height: 55px;
            border: 2px solid #e1e1e1;
            border-radius: 15px;
            padding: 0 20px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            border-color: #8B0000;
            box-shadow: 0 0 0 3px rgba(139,0,0,0.1);
            background: white;
            outline: none;
        }

        .form-control::placeholder {
            color: #aaa;
            font-weight: 300;
        }

        .btn-login {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            border: none;
            color: white;
            padding: 16px;
            border-radius: 15px;
            width: 100%;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            margin-top: 10px;
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(139,0,0,0.3);
            background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .btn-login i {
            margin-right: 8px;
            transition: transform 0.3s ease;
        }

        .btn-login:hover i {
            transform: translateX(5px);
        }

        /* Animation de chargement */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-login.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Alerte personnalisée */
        .alert-custom {
            background: rgba(139,0,0,0.1);
            border-left: 4px solid #8B0000;
            color: #8B0000;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .alert-custom i {
            font-size: 20px;
            margin-right: 10px;
        }

        /* Informations de test */
        .test-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-top: 25px;
            text-align: center;
        }

        .test-info small {
            color: #666;
            font-size: 13px;
            display: block;
            margin-bottom: 8px;
        }

        .test-info .credentials {
            background: white;
            border-radius: 8px;
            padding: 8px;
            font-size: 14px;
            color: #8B0000;
            font-weight: 600;
            border: 1px dashed #8B0000;
        }

        .test-info .credentials i {
            margin: 0 5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .image-side {
                min-height: 300px;
                padding: 40px 20px;
            }
            
            .form-side {
                padding: 40px 30px;
            }
            
            .image-side img {
                max-width: 250px;
            }
            
            .login-header h2 {
                font-size: 28px;
            }
        }

        /* Petits plus décoratifs */
        .floating-icon {
            position: absolute;
            color: rgba(139,0,0,0.1);
            font-size: 20px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .floating-icon:nth-child(1) { top: 10%; left: 10%; }
        .floating-icon:nth-child(2) { bottom: 10%; right: 10%; animation-delay: 1s; }
        .floating-icon:nth-child(3) { top: 20%; right: 20%; animation-delay: 2s; }
    </style>
</head>
<body>
    <!-- Éléments flottants décoratifs -->
    <i class="fas fa-cross floating-icon"></i>
    <i class="fas fa-church floating-icon"></i>
    <i class="fas fa-dove floating-icon"></i>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="login-wrapper">
                    <div class="row no-gutters">
                        <!-- Colonne de gauche avec image -->
                        <div class="col-lg-6">
                            <div class="image-side">
                                <i class="fas fa-cross cross-pattern"></i>
                                <!-- Image du dossier services -->
                                <img src="../../images/header/grot.jpg" alt="Paroisse Saint Esprit">
                                <h3>Bienvenue</h3>
                                <p>Accédez à votre espace d'administration pour gérer les sacrements et les activités paroissiales</p>
                                <!-- Petites statistiques fictives -->
                                <div class="row mt-4 text-center" style="z-index: 2; width: 100%;">
                                    <div class="col-4">
                                        <h4 style="color: white; font-weight: 700;">150+</h4>
                                        <small style="color: rgba(255,255,255,0.8);">Baptêmes</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 style="color: white; font-weight: 700;">80+</h4>
                                        <small style="color: rgba(255,255,255,0.8);">Communions</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 style="color: white; font-weight: 700;">10</h4>
                                        <small style="color: rgba(255,255,255,0.8);">CEV</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Colonne de droite avec formulaire -->
                        <div class="col-lg-6">
                            <div class="form-side">
                                <div class="login-header">
                                    <h2><i class="fas fa-church"></i>PAROISSE S.E</h2>
                                    <p>Espace Administrateur</p>
                                    <span class="welcome-text"><i class="fas fa-lock"></i> Accès sécurisé</span>
                                </div>
                                
                                <?php if($error): ?>
                                    <div class="alert-custom">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <?php echo $error; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" action="" id="loginForm">
                                    <div class="form-group">
                                        <label><i class="fas fa-user"></i> Nom d'utilisateur</label>
                                        <input type="text" class="form-control" name="username" required 
                                               placeholder="Entrez votre nom d'utilisateur" autocomplete="off">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label><i class="fas fa-lock"></i> Mot de passe</label>
                                        <input type="password" class="form-control" name="password" required 
                                               placeholder="Entrez votre mot de passe">
                                    </div>
                                    
                                    <button type="submit" class="btn-login" id="submitBtn">
                                        <i class="fas fa-sign-in-alt"></i> Se connecter
                                    </button>
                                </form>

                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inclusion du fichier test.js -->
    <script src="../../js/test.js"></script>
    
    <!-- Script supplémentaire pour animations -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            
            if(loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    submitBtn.classList.add('loading');
                    submitBtn.innerHTML = '<i class="fas fa-spinner"></i> Connexion en cours...';
                });
            }
            
            // Animation pour les champs de formulaire
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if(!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });
            
            // Vérification simple avant soumission
            loginForm.addEventListener('submit', function(e) {
                const username = document.querySelector('input[name="username"]').value;
                const password = document.querySelector('input[name="password"]').value;
                
                if(!username || !password) {
                    e.preventDefault();
                    alert('Veuillez remplir tous les champs');
                    submitBtn.classList.remove('loading');
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Se connecter';
                }
            });
        });
    </script>
</body>
</html>
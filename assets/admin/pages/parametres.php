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
        
        // Ici, vous devriez vérifier le mot de passe depuis la base de données
        // Pour l'exemple, on utilise une vérification simple
        if($current_password == 'sec123') {
            if($new_password == $confirm_password) {
                // Validation de la force du mot de passe
                if(strlen($new_password) >= 6) {
                    $message = '✅ Mot de passe modifié avec succès !';
                } else {
                    $error = '❌ Le nouveau mot de passe doit contenir au moins 6 caractères';
                }
            } else {
                $error = '❌ Les nouveaux mots de passe ne correspondent pas';
            }
        } else {
            $error = '❌ Mot de passe actuel incorrect';
        }
    }
}

// Récupérer les informations de l'administrateur (à adapter selon votre base de données)
$admin_username = $_SESSION['admin_username'];
$last_login = date('d/m/Y H:i:s'); // À remplacer par la vraie dernière connexion
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Administration Paroisse</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Montserrat', sans-serif;
        }
        
        body {
            background: #f8f9fc;
            margin: 0;
            padding: 0;
        }
        
        /* Main content adjustment pour la sidebar */
        .main-content {
            margin-left: 280px;
            padding: 20px 30px;
        }
        
        /* En-tête de page */
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
            font-weight: 400;
            background: #f0f0f0;
            padding: 5px 12px;
            border-radius: 20px;
            margin-left: 15px;
        }
        
        .page-title i {
            color: #8B0000;
            margin-right: 10px;
        }
        
        /* Cartes */
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 25px;
        }
        
        .settings-card {
            background: white;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: all 0.3s ease;
            border: none;
            height: fit-content;
        }
        
        .settings-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(139,0,0,0.15);
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            padding: 20px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: none;
        }
        
        .card-header-custom i {
            font-size: 28px;
            background: rgba(255,255,255,0.2);
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
        }
        
        .card-header-custom h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .card-header-custom p {
            margin: 5px 0 0;
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .card-body-custom {
            padding: 30px;
        }
        
        /* Formulaire */
        .password-form .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .password-form label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        
        .password-form label i {
            color: #8B0000;
            margin-right: 8px;
            width: 20px;
        }
        
        .password-form .input-wrapper {
            position: relative;
        }
        
        .password-form .form-control {
            border: 2px solid #e1e1e1;
            border-radius: 15px;
            padding: 12px 20px;
            padding-right: 45px;
            height: auto;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        
        .password-form .form-control:focus {
            border-color: #8B0000;
            box-shadow: 0 0 0 3px rgba(139,0,0,0.1);
            background: white;
            outline: none;
        }
        
        .password-form .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #8B0000;
            cursor: pointer;
            font-size: 1.2rem;
            background: white;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .password-form .toggle-password:hover {
            background: #f0f0f0;
        }
        
        .password-strength {
            margin-top: 10px;
            height: 5px;
            background: #e1e1e1;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s;
        }
        
        .strength-weak { background: #dc3545; width: 33.33%; }
        .strength-medium { background: #ffc107; width: 66.66%; }
        .strength-strong { background: #28a745; width: 100%; }
        
        .password-requirements {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
            font-size: 0.85rem;
        }
        
        .password-requirements p {
            margin-bottom: 8px;
            color: #666;
        }
        
        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .password-requirements li {
            margin-bottom: 5px;
            color: #999;
        }
        
        .password-requirements li.valid {
            color: #28a745;
        }
        
        .password-requirements li i {
            margin-right: 8px;
            width: 18px;
        }
        
        .btn-save {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(139,0,0,0.3);
            color: white;
        }
        
        .btn-save i {
            transition: transform 0.3s;
        }
        
        .btn-save:hover i {
            transform: rotate(360deg);
        }
        
        /* Informations profil */
        .profile-info {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 25px rgba(139,0,0,0.3);
        }
        
        .profile-avatar i {
            font-size: 50px;
            color: white;
        }
        
        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .profile-role {
            color: #8B0000;
            font-weight: 500;
            background: rgba(139,0,0,0.1);
            display: inline-block;
            padding: 5px 15px;
            border-radius: 25px;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }
        
        .info-item {
            background: #f8f9fc;
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .info-item:hover {
            background: rgba(139,0,0,0.05);
            transform: translateY(-3px);
        }
        
        .info-item i {
            font-size: 24px;
            color: #8B0000;
            margin-bottom: 8px;
        }
        
        .info-item .label {
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-item .value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }
        
        /* À propos */
        .about-section {
            background: linear-gradient(135deg, #f8f9fc 0%, white 100%);
            border-radius: 20px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .about-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .about-item:last-child {
            border-bottom: none;
        }
        
        .about-item i {
            width: 35px;
            height: 35px;
            background: rgba(139,0,0,0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8B0000;
        }
        
        .version-badge {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            display: inline-block;
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 15px;
        }
        
        /* Alertes */
        .alert-custom {
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            border: none;
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
        
        .alert-custom.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-custom.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .alert-custom i {
            font-size: 24px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .page-header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .settings-grid {
                grid-template-columns: 1fr;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include '../includes/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <!-- En-tête de page -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-cog"></i>
                Paramètres
                <span><i class="far fa-clock mr-1"></i><?php echo date('d/m/Y H:i'); ?></span>
            </h1>
            <div>
                <span class="badge badge-light p-3" style="border-radius: 15px;">
                    <i class="fas fa-user-shield mr-2 text-danger"></i>
                    Session active
                </span>
            </div>
        </div>

        <!-- Notifications -->
        <?php if($message): ?>
            <div class="alert-custom success">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Succès !</strong><br>
                    <?php echo $message; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="alert-custom error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Erreur !</strong><br>
                    <?php echo $error; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Grille des paramètres -->
        <div class="settings-grid">
            <!-- Carte : Modification du mot de passe -->
            <div class="settings-card">
                <div class="card-header-custom">
                    <i class="fas fa-key"></i>
                    <div>
                        <h3>Sécurité du compte</h3>
                        <p>Modifiez votre mot de passe régulièrement</p>
                    </div>
                </div>
                <div class="card-body-custom">
                    <form method="POST" class="password-form" id="passwordForm">
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i>Mot de passe actuel</label>
                            <div class="input-wrapper">
                                <input type="password" class="form-control" name="current_password" id="current_password" required placeholder="Entrez votre mot de passe actuel">
                                <span class="toggle-password" onclick="togglePassword('current_password')">
                                    <i class="far fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i>Nouveau mot de passe</label>
                            <div class="input-wrapper">
                                <input type="password" class="form-control" name="new_password" id="new_password" required placeholder="6 caractères minimum" onkeyup="checkPasswordStrength()">
                                <span class="toggle-password" onclick="togglePassword('new_password')">
                                    <i class="far fa-eye"></i>
                                </span>
                            </div>
                            <div class="password-strength">
                                <div class="password-strength-bar" id="strengthBar"></div>
                            </div>
                            <div class="password-requirements">
                                <p><i class="fas fa-shield-alt"></i> Exigences :</p>
                                <ul id="requirements">
                                    <li id="req-length"><i class="far fa-circle"></i> Au moins 6 caractères</li>
                                    <li id="req-number"><i class="far fa-circle"></i> Au moins 1 chiffre</li>
                                    <li id="req-uppercase"><i class="far fa-circle"></i> Au moins 1 majuscule</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-check-circle"></i>Confirmer le mot de passe</label>
                            <div class="input-wrapper">
                                <input type="password" class="form-control" name="confirm_password" id="confirm_password" required placeholder="Confirmez le nouveau mot de passe" onkeyup="checkPasswordMatch()">
                                <span class="toggle-password" onclick="togglePassword('confirm_password')">
                                    <i class="far fa-eye"></i>
                                </span>
                            </div>
                            <small id="passwordMatch" class="form-text text-muted"></small>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn-save">
                            <i class="fas fa-save"></i>
                            Mettre à jour le mot de passe
                        </button>
                    </form>
                </div>
            </div>

            <!-- Carte : Informations administrateur -->
            <div class="settings-card">
                <div class="card-header-custom">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <h3>Profil administrateur</h3>
                        <p>Vos informations personnelles</p>
                    </div>
                </div>
                <div class="card-body-custom">
                    <div class="profile-info">
                        <div class="profile-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="profile-name"><?php echo htmlspecialchars($admin_username); ?></div>
                        <div class="profile-role">
                            <i class="fas fa-crown mr-1"></i>Administrateur
                        </div>
                        
                        <div class="info-grid">
                            <div class="info-item">
                                <i class="fas fa-calendar-alt"></i>
                                <div class="label">Dernière connexion</div>
                                <div class="value"><?php echo $last_login; ?></div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <div class="label">Session</div>
                                <div class="value">Active</div>
                            </div>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>

        <!-- Conseils de sécurité -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card" style="border-radius: 20px; border: none; background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%); color: white;">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5><i class="fas fa-shield-alt mr-2"></i> Conseils de sécurité</h5>
                                <p class="mb-0">
                                    <i class="fas fa-check-circle mr-2"></i>Changez votre mot de passe tous les 3 mois<br>
                                    <i class="fas fa-check-circle mr-2"></i>Utilisez un mot de passe unique et complexe<br>
                                    <i class="fas fa-check-circle mr-2"></i>Ne partagez jamais vos identifiants
                                </p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fas fa-lock fa-4x" style="opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour afficher/masquer le mot de passe
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = event.currentTarget.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Vérifier la force du mot de passe
        function checkPasswordStrength() {
            const password = document.getElementById('new_password').value;
            const strengthBar = document.getElementById('strengthBar');
            
            // Vérifier les critères
            const hasLength = password.length >= 6;
            const hasNumber = /\d/.test(password);
            const hasUppercase = /[A-Z]/.test(password);
            
            // Mettre à jour les indicateurs
            document.getElementById('req-length').innerHTML = `<i class="fas fa-${hasLength ? 'check-circle text-success' : 'circle'}"></i> Au moins 6 caractères`;
            document.getElementById('req-number').innerHTML = `<i class="fas fa-${hasNumber ? 'check-circle text-success' : 'circle'}"></i> Au moins 1 chiffre`;
            document.getElementById('req-uppercase').innerHTML = `<i class="fas fa-${hasUppercase ? 'check-circle text-success' : 'circle'}"></i> Au moins 1 majuscule`;
            
            // Calculer la force
            let strength = 0;
            if (hasLength) strength++;
            if (hasNumber) strength++;
            if (hasUppercase) strength++;
            
            // Mettre à jour la barre
            strengthBar.className = 'password-strength-bar';
            if (strength === 1) strengthBar.classList.add('strength-weak');
            else if (strength === 2) strengthBar.classList.add('strength-medium');
            else if (strength === 3) strengthBar.classList.add('strength-strong');
        }

        // Vérifier si les mots de passe correspondent
        function checkPasswordMatch() {
            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('confirm_password').value;
            const matchMsg = document.getElementById('passwordMatch');
            
            if (confirmPass.length > 0) {
                if (newPass === confirmPass) {
                    matchMsg.innerHTML = '<i class="fas fa-check-circle text-success"></i> Les mots de passe correspondent';
                    matchMsg.style.color = '#28a745';
                } else {
                    matchMsg.innerHTML = '<i class="fas fa-times-circle text-danger"></i> Les mots de passe ne correspondent pas';
                    matchMsg.style.color = '#dc3545';
                }
            } else {
                matchMsg.innerHTML = '';
            }
        }

        // Validation du formulaire avant soumission
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('confirm_password').value;
            
            if (newPass !== confirmPass) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas !');
            }
            
            if (newPass.length < 6) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 6 caractères !');
            }
        });

        // Auto-hide des alertes après 5 secondes
        setTimeout(function() {
            document.querySelectorAll('.alert-custom').forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>
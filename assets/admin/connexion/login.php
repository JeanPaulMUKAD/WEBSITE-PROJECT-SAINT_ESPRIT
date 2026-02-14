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
    
    // Pour le test, vérification simple (à remplacer par password_verify en production)
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
    <title>Connexion Admin - Paroisse</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            color: #333;
            font-weight: 600;
        }
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h2><i class="fas fa-church mr-2"></i>PAROISSE S.E</h2>
            <p>Espace Administrateur</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-user mr-2"></i>Nom d'utilisateur</label>
                <input type="text" class="form-control" name="username" required 
                       placeholder="Nom d'utilisateur">
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock mr-2"></i>Mot de passe</label>
                <input type="password" class="form-control" name="password" required 
                       placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
            </button>
        </form>
       
    </div>
</body>
</html>
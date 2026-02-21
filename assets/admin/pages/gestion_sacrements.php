<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

require_once '../includes/db_connect.php';

// Traitement du formulaire d'ajout
$notification = '';
$notification_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $query = "INSERT INTO sacrements (nom_complet, lieu_naissance, date_naissance, nom_pere, nom_mere, nom_parrain, date_bapteme, date_communion, date_confirmation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param(
            $stmt,
            "sssssssss",
            $_POST['nom_complet'],
            $_POST['lieu_naissance'],
            $_POST['date_naissance'],
            $_POST['nom_pere'],
            $_POST['nom_mere'],
            $_POST['nom_parrain'],
            $_POST['date_bapteme'],
            $_POST['date_communion'],
            $_POST['date_confirmation']
        );

        if (mysqli_stmt_execute($stmt)) {
            $notification = 'Sacrement ajouté avec succès !';
            $notification_type = 'success';
        } else {
            $notification = 'Erreur lors de l\'ajout du sacrement.';
            $notification_type = 'error';
        }
    }
}

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = $_POST['id'];
    $query = "UPDATE sacrements SET nom_complet=?, lieu_naissance=?, date_naissance=?, nom_pere=?, nom_mere=?, nom_parrain=?, date_bapteme=?, date_communion=?, date_confirmation=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "sssssssssi",
        $_POST['nom_complet'],
        $_POST['lieu_naissance'],
        $_POST['date_naissance'],
        $_POST['nom_pere'],
        $_POST['nom_mere'],
        $_POST['nom_parrain'],
        $_POST['date_bapteme'],
        $_POST['date_communion'],
        $_POST['date_confirmation'],
        $id
    );

    if (mysqli_stmt_execute($stmt)) {
        $notification = 'Sacrement modifié avec succès !';
        $notification_type = 'success';
    } else {
        $notification = 'Erreur lors de la modification.';
        $notification_type = 'error';
    }
}

// Récupération des données avec filtres
$where = "1=1";
$current_filter = isset($_GET['filter']) ? $_GET['filter'] : '';

if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];
    if ($filter == 'jour') {
        $where .= " AND DATE(created_at) = CURDATE()";
    } elseif ($filter == 'semaine') {
        $where .= " AND YEARWEEK(created_at) = YEARWEEK(CURDATE())";
    } elseif ($filter == 'mois') {
        $where .= " AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
    } elseif ($filter == 'annee') {
        $where .= " AND YEAR(created_at) = YEAR(CURDATE())";
    }
}

if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where .= " AND nom_complet LIKE '%$search%'";
}

$query = "SELECT * FROM sacrements WHERE $where ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Statistiques rapides
$total_baptemes = mysqli_query($conn, "SELECT COUNT(*) as total FROM sacrements WHERE date_bapteme IS NOT NULL");
$total_baptemes = mysqli_fetch_assoc($total_baptemes)['total'];

$total_ce_mois = mysqli_query($conn, "SELECT COUNT(*) as total FROM sacrements WHERE MONTH(created_at) = MONTH(CURDATE())");
$total_ce_mois = mysqli_fetch_assoc($total_ce_mois)['total'];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Sacrements - Administration Paroisse</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background: #f8f9fc;
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
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
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

        .page-actions {
            display: flex;
            gap: 15px;
        }

        .btn-export {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 0, 0, 0.3);
            color: white;
        }

        /* Statistiques rapides */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card-small {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s;
        }

        .stat-card-small:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(139, 0, 0, 0.15);
        }

        .stat-icon-small {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon-small i {
            font-size: 24px;
            color: white;
        }

        .stat-info-small h4 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
        }

        .stat-info-small p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        /* Formulaire d'ajout */
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .form-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-header i {
            font-size: 24px;
            color: #8B0000;
            background: rgba(139, 0, 0, 0.1);
            padding: 12px;
            border-radius: 12px;
        }

        .form-header h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
        }

        .form-header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }

        .form-group label i {
            color: #8B0000;
            margin-right: 8px;
        }

        .form-control {
            border: 2px solid #e1e1e1;
            border-radius: 12px;
            padding: 12px 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #8B0000;
            box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 0, 0, 0.3);
            color: white;
        }

        .btn-submit i {
            margin-right: 8px;
        }

        /* Filtres */
        .filters-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
        }

        .filter-btn:not(.active) {
            background: #f0f0f0;
            color: #666;
        }

        .filter-btn.active {
            background: #8B0000;
            color: white;
            box-shadow: 0 5px 15px rgba(139, 0, 0, 0.3);
        }

        .filter-btn:hover:not(.active) {
            background: #e0e0e0;
            color: #333;
            text-decoration: none;
        }

        .search-box {
            display: flex;
            gap: 10px;
        }

        .search-box input {
            border: 2px solid #e1e1e1;
            border-radius: 12px;
            padding: 10px 15px;
            width: 250px;
        }

        .search-box button {
            background: #8B0000;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .search-box button:hover {
            background: #A52A2A;
        }

        /* Tableau */
        .table-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            border: none;
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
        }

        .table-title h5 {
            margin: 0;
            font-weight: 600;
            color: #333;
        }

        .btn-print {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .btn-print:hover {
            background: #218838;
            transform: translateY(-2px);
            color: white;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: #f8f9fc;
            color: #333;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-top: none;
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            color: #555;
        }

        .badge-sacrement {
            background: rgba(139, 0, 0, 0.1);
            color: #8B0000;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        /* Styles améliorés pour les boutons d'action */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-action {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-action::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.5s, height 0.5s;
        }

        .btn-action:hover::before {
            width: 100px;
            height: 100px;
        }

        .btn-action i {
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }

        .btn-view {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            box-shadow: 0 3px 8px rgba(23, 162, 184, 0.3);
        }

        .btn-view:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
        }

        .btn-edit {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #333;
            box-shadow: 0 3px 8px rgba(255, 193, 7, 0.3);
        }

        .btn-edit:hover {
            transform: translateY(-3px) rotate(5deg);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
            color: #333;
        }

        /* Tooltip personnalisé */
        .btn-action {
            position: relative;
        }

        .btn-action[data-tooltip] {
            position: relative;
        }

        .btn-action[data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            margin-bottom: 5px;
            z-index: 1000;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-action[data-tooltip]:after {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .btn-action[data-tooltip]:hover:before,
        .btn-action[data-tooltip]:hover:after {
            opacity: 1;
            visibility: visible;
            bottom: 120%;
        }

        /* Modal stylisé */
        .modal-content {
            border-radius: 25px;
            border: none;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            border-radius: 25px 25px 0 0;
            padding: 20px 25px;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .modal-header::before {
            content: '\f02e';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: -20px;
            bottom: -20px;
            font-size: 100px;
            opacity: 0.1;
            color: white;
            transform: rotate(15deg);
        }

        .modal-header .modal-title {
            font-weight: 600;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-header .modal-title i {
            font-size: 1.8rem;
        }

        .modal-header .close {
            color: white;
            opacity: 0.8;
            text-shadow: none;
            font-size: 2rem;
            transition: all 0.3s;
        }

        .modal-header .close:hover {
            opacity: 1;
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 30px;
        }

        .modal-footer {
            border-top: 2px solid #f0f0f0;
            padding: 20px 25px;
        }

        /* Carte de détails */
        .detail-card {
            background: #f8f9fc;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #8B0000;
        }

        .detail-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #e0e0e0;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 1.2rem;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
        }

        /* Formulaire de modification dans la modal */
        .edit-form .form-group {
            margin-bottom: 15px;
        }

        .edit-form label {
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .edit-form label i {
            color: #8B0000;
            margin-right: 5px;
            width: 20px;
        }

        .edit-form .form-control {
            border: 2px solid #e1e1e1;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.95rem;
        }

        .edit-form .form-control:focus {
            border-color: #8B0000;
            box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
        }

        .btn-save-edit {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-save-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 0, 0, 0.3);
        }

        /* Animation pour les modales */
        .modal.fade .modal-dialog {
            transform: scale(0.8);
            transition: transform 0.3s ease-in-out;
        }

        .modal.show .modal-dialog {
            transform: scale(1);
        }

        /* Notification */
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

        .alert-custom {
            background: white;
            border-left: 4px solid;
            border-radius: 10px;
            padding: 15px 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .alert-custom.success {
            border-left-color: #28a745;
        }

        .alert-custom.error {
            border-left-color: #dc3545;
        }

        .alert-custom i {
            font-size: 24px;
        }

        .alert-custom.success i {
            color: #28a745;
        }

        .alert-custom.error i {
            color: #dc3545;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
            }

            .filters-section {
                flex-direction: column;
            }

            .search-box input {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/sidebar_admin.php'; ?>

    <div class="main-content">
        <!-- Notification -->
        <?php if ($notification): ?>
            <div class="notification">
                <div class="alert-custom <?php echo $notification_type; ?>">
                    <i
                        class="fas fa-<?php echo $notification_type == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <div>
                        <strong><?php echo $notification_type == 'success' ? 'Succès' : 'Erreur'; ?></strong><br>
                        <small><?php echo $notification; ?></small>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- En-tête de page -->
        <div class="page-header">
            <div>
                <h1 class="page-title">
                    Gestion des Sacrements
                    <span><i class="far fa-calendar-alt mr-1"></i><?php echo date('d/m/Y'); ?></span>
                </h1>
            </div>

        </div>

        <!-- Statistiques rapides -->
        <div class="stats-grid">
            <div class="stat-card-small">
                <div class="stat-icon-small">
                    <i class="fas fa-cross"></i>
                </div>
                <div class="stat-info-small">
                    <h4><?php echo $total_baptemes; ?></h4>
                    <p>Total baptêmes</p>
                </div>
            </div>
            <div class="stat-card-small">
                <div class="stat-icon-small">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info-small">
                    <h4><?php echo $total_ce_mois; ?></h4>
                    <p>Ce mois-ci</p>
                </div>
            </div>
            <div class="stat-card-small">
                <div class="stat-icon-small">
                    <i class="fas fa-church"></i>
                </div>
                <div class="stat-info-small">
                    <h4><?php echo mysqli_num_rows($result); ?></h4>
                    <p>Affichés</p>
                </div>
            </div>
        </div>

        <!-- Formulaire d'ajout -->
        <div class="form-card">
            <div class="form-header">
                <i class="fas fa-plus-circle"></i>
                <div>
                    <h3>Ajouter un nouveau sacrement</h3>
                    <p>Remplissez les informations ci-dessous pour enregistrer un nouveau sacrement</p>
                </div>
            </div>

            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Nom complet *</label>
                            <input type="text" class="form-control" name="nom_complet" required
                                placeholder="Entrez le nom complet">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> Lieu de naissance</label>
                            <input type="text" class="form-control" name="lieu_naissance"
                                placeholder="Lieu de naissance">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-birthday-cake"></i> Date de naissance</label>
                            <input type="date" class="form-control" name="date_naissance">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-male"></i> Nom du père</label>
                            <input type="text" class="form-control" name="nom_pere" placeholder="Nom du père">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-female"></i> Nom de la mère</label>
                            <input type="text" class="form-control" name="nom_mere" placeholder="Nom de la mère">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-hand-holding-heart"></i> Nom du parrain</label>
                            <input type="text" class="form-control" name="nom_parrain" placeholder="Nom du parrain">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-water"></i> Date de baptême</label>
                            <input type="date" class="form-control" name="date_bapteme">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-bread-slice"></i> Date de communion</label>
                            <input type="date" class="form-control" name="date_communion">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-dove"></i> Date de confirmation</label>
                            <input type="date" class="form-control" name="date_confirmation">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Enregistrer le sacrement
                </button>
            </form>
        </div>

        <!-- Filtres et recherche -->
        <div class="filters-section">
            <div class="filter-group">
                <a href="?filter=jour" class="filter-btn <?php echo $current_filter == 'jour' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-day mr-1"></i>Jour
                </a>
                <a href="?filter=semaine"
                    class="filter-btn <?php echo $current_filter == 'semaine' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-week mr-1"></i>Semaine
                </a>
                <a href="?filter=mois" class="filter-btn <?php echo $current_filter == 'mois' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-alt mr-1"></i>Mois
                </a>
                <a href="?filter=annee" class="filter-btn <?php echo $current_filter == 'annee' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar mr-1"></i>Année
                </a>
                <a href="?" class="filter-btn <?php echo !$current_filter ? 'active' : ''; ?>">
                    <i class="fas fa-list mr-1"></i>Tous
                </a>
            </div>
            <div class="search-box">
                <form method="GET" class="d-flex">
                    <input type="text" name="search" placeholder="Rechercher par nom..."
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>

        <!-- Tableau des sacrements -->
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <i class="fas fa-table"></i>
                    <h5>Liste des sacrements</h5>
                </div>
                <button onclick="exportToPDF()" class="btn-export">
                    <i class="fas fa-file-pdf mr-2"></i>Exporter PDF
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Naissance</th>
                            <th>Parents / Parrain</th>
                            <th>Baptême</th>
                            <th>Communion</th>
                            <th>Confirmation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><span class="badge-sacrement">#<?php echo $row['id']; ?></span></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['nom_complet']); ?></strong>
                                </td>
                                <td>
                                    <?php if ($row['lieu_naissance'] || $row['date_naissance']): ?>
                                        <?php echo $row['lieu_naissance'] ? htmlspecialchars($row['lieu_naissance']) : '-'; ?><br>
                                        <small
                                            class="text-muted"><?php echo $row['date_naissance'] ? date('d/m/Y', strtotime($row['date_naissance'])) : '-'; ?></small>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['nom_pere'] || $row['nom_mere'] || $row['nom_parrain']): ?>
                                        <?php if ($row['nom_pere']): ?><small><i class="fas fa-male text-info"></i>
                                                <?php echo htmlspecialchars($row['nom_pere']); ?></small><br><?php endif; ?>
                                        <?php if ($row['nom_mere']): ?><small><i class="fas fa-female text-danger"></i>
                                                <?php echo htmlspecialchars($row['nom_mere']); ?></small><br><?php endif; ?>
                                        <?php if ($row['nom_parrain']): ?><small><i
                                                    class="fas fa-hand-holding-heart text-success"></i>
                                                <?php echo htmlspecialchars($row['nom_parrain']); ?></small><?php endif; ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['date_bapteme']): ?>
                                        <span class="badge-sacrement">
                                            <i class="fas fa-check-circle text-success mr-1"></i>
                                            <?php echo date('d/m/Y', strtotime($row['date_bapteme'])); ?>
                                        </span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['date_communion']): ?>
                                        <span class="badge-sacrement">
                                            <i class="fas fa-check-circle text-success mr-1"></i>
                                            <?php echo date('d/m/Y', strtotime($row['date_communion'])); ?>
                                        </span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['date_confirmation']): ?>
                                        <span class="badge-sacrement">
                                            <i class="fas fa-check-circle text-success mr-1"></i>
                                            <?php echo date('d/m/Y', strtotime($row['date_confirmation'])); ?>
                                        </span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-view"
                                            onclick="viewDetails(<?php echo $row['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-action btn-edit" onclick="editRecord(<?php echo $row['id']; ?>)">
                                            <i class="fas fa-edit"></i>
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

    <!-- Modal Détails -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        Détails du sacrement
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detailsContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Modification -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>
                        Modifier le sacrement
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="editContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialisation de DataTable
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
                },
                "order": [[0, "desc"]],
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Tous"]]
            });

            // Auto-hide notification après 5 secondes
            setTimeout(function () {
                $('.notification').fadeOut('slow');
            }, 5000);
        });

        function viewDetails(id) {
            // Animation de chargement
            $('#detailsContent').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-danger" style="width: 3rem; height: 3rem;" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                    <p class="mt-3 text-muted">Chargement des détails...</p>
                </div>
            `);
            $('#detailsModal').modal('show');

            // Requête AJAX pour obtenir les détails
            $.ajax({
                url: 'get_sacrement_details.php',
                type: 'GET',
                data: { id: id },
                success: function (data) {
                    $('#detailsContent').html(data);
                },
                error: function () {
                    $('#detailsContent').html(`
                        <div class="alert alert-danger text-center">
                            <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                            <p>Erreur lors du chargement des détails.</p>
                        </div>
                    `);
                }
            });
        }

        function editRecord(id) {
            // Animation de chargement
            $('#editContent').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-danger" style="width: 3rem; height: 3rem;" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                    <p class="mt-3 text-muted">Chargement du formulaire...</p>
                </div>
            `);
            $('#editModal').modal('show');

            // Requête AJAX pour obtenir le formulaire de modification
            $.ajax({
                url: 'edit_sacrement_form.php',
                type: 'GET',
                data: { id: id },
                success: function (data) {
                    $('#editContent').html(data);
                },
                error: function () {
                    $('#editContent').html(`
                        <div class="alert alert-danger text-center">
                            <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                            <p>Erreur lors du chargement du formulaire.</p>
                        </div>
                    `);
                }
            });
        }

        function exportToExcel() {
            window.location.href = 'export_sacrements.php';
        }

        // Animation pour les champs du formulaire
        $('.form-control').on('focus', function () {
            $(this).parent().addClass('focused');
        }).on('blur', function () {
            if (!$(this).val()) {
                $(this).parent().removeClass('focused');
            }
        });

        function exportToPDF() {
            // Récupérer les données du tableau
            const table = $('#dataTable').DataTable();
            const data = table.rows().data().toArray().map(row => ({
                id: row[0]?.replace('#', '') || '',
                nom: row[1] || '',
                bapteme: row[4]?.replace(/<[^>]*>/g, '') || '-',
                communion: row[5]?.replace(/<[^>]*>/g, '') || '-',
                confirmation: row[6]?.replace(/<[^>]*>/g, '') || '-'
            }));

            // Envoyer au serveur
            fetch('export_sacrements_fpdf.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    data: data,
                    filter: '<?php echo $current_filter; ?>',
                    date: '<?php echo date('d/m/Y H:i:s'); ?>'
                })
            })
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'sacrements_' + new Date().toISOString().slice(0, 10) + '.pdf';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                    alert('PDF généré avec succès !');
                })
                .catch(error => {
                    alert('Erreur : ' + error);
                });
        }
    </script>
    <script src="../assets/js/admin.js"></script>
</body>

</html>
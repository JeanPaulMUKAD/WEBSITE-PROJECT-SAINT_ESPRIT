<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

require_once '../includes/db_connect.php';

// Traitement du formulaire d'ajout
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if($_POST['action'] == 'add') {
        $query = "INSERT INTO sacrements (nom_complet, lieu_naissance, date_naissance, nom_pere, nom_mere, nom_parrain, date_bapteme, date_communion, date_confirmation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssssss", 
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
        mysqli_stmt_execute($stmt);
        header('Location: gestion_sacrements.php?success=1');
        exit();
    }
}

// Récupération des données avec filtres
$where = "1=1";
if(isset($_GET['filter'])) {
    $filter = $_GET['filter'];
    if($filter == 'jour') {
        $where .= " AND DATE(created_at) = CURDATE()";
    } elseif($filter == 'semaine') {
        $where .= " AND YEARWEEK(created_at) = YEARWEEK(CURDATE())";
    } elseif($filter == 'mois') {
        $where .= " AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
    } elseif($filter == 'annee') {
        $where .= " AND YEAR(created_at) = YEAR(CURDATE())";
    }
}

if(isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where .= " AND nom_complet LIKE '%$search%'";
}

$query = "SELECT * FROM sacrements WHERE $where ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Sacrements - Administration</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header_admin.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar_admin.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Gestion des Sacrements</h1>
                </div>

                <!-- Formulaire d'ajout -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-plus-circle mr-1"></i>
                        Ajouter un nouveau sacrement
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="add">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>Nom complet</label>
                                    <input type="text" class="form-control" name="nom_complet" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Lieu de naissance</label>
                                    <input type="text" class="form-control" name="lieu_naissance">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Date de naissance</label>
                                    <input type="date" class="form-control" name="date_naissance">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Nom du père</label>
                                    <input type="text" class="form-control" name="nom_pere">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Nom de la mère</label>
                                    <input type="text" class="form-control" name="nom_mere">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Nom du parrain</label>
                                    <input type="text" class="form-control" name="nom_parrain">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Date de baptême</label>
                                    <input type="date" class="form-control" name="date_bapteme">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Date de communion</label>
                                    <input type="date" class="form-control" name="date_communion">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Date de confirmation</label>
                                    <input type="date" class="form-control" name="date_confirmation">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Enregistrer
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Filtres et recherche -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="btn-group" role="group">
                            <a href="?filter=jour" class="btn btn-outline-primary <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'jour') ? 'active' : ''; ?>">Jour</a>
                            <a href="?filter=semaine" class="btn btn-outline-primary <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'semaine') ? 'active' : ''; ?>">Semaine</a>
                            <a href="?filter=mois" class="btn btn-outline-primary <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'mois') ? 'active' : ''; ?>">Mois</a>
                            <a href="?filter=annee" class="btn btn-outline-primary <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'annee') ? 'active' : ''; ?>">Année</a>
                            <a href="?" class="btn btn-outline-secondary">Tous</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <form method="GET" class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Rechercher par nom..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tableau des sacrements -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-table mr-1"></i>
                        Liste des sacrements
                        <button onclick="window.print()" class="btn btn-sm btn-success float-right">
                            <i class="fas fa-print mr-1"></i>Imprimer
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom complet</th>
                                        <th>Naissance</th>
                                        <th>Parents</th>
                                        <th>Parrain</th>
                                        <th>Baptême</th>
                                        <th>Communion</th>
                                        <th>Confirmation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['nom_complet']); ?></td>
                                        <td>
                                            <?php echo $row['lieu_naissance'] ?><br>
                                            <small><?php echo $row['date_naissance']; ?></small>
                                        </td>
                                        <td>
                                            P: <?php echo htmlspecialchars($row['nom_pere']); ?><br>
                                            M: <?php echo htmlspecialchars($row['nom_mere']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['nom_parrain']); ?></td>
                                        <td><?php echo $row['date_bapteme']; ?></td>
                                        <td><?php echo $row['date_communion']; ?></td>
                                        <td><?php echo $row['date_confirmation']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewDetails(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="editRecord(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Détails -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails du sacrement</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detailsContent">
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
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
                },
                "order": [[0, "desc"]]
            });
        });

        function viewDetails(id) {
            // Ici, vous pouvez faire une requête AJAX pour obtenir les détails
            $.get('get_sacrement_details.php', {id: id}, function(data) {
                $('#detailsContent').html(data);
                $('#detailsModal').modal('show');
            });
        }

        function editRecord(id) {
            window.location.href = 'edit_sacrement.php?id=' + id;
        }
    </script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>
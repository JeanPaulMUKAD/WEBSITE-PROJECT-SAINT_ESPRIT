<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

require_once '../includes/db_connect.php';

// Récupération des visiteurs
$query = "SELECT * FROM visiteurs ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des CEV - Administration</title>
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
                    <h1 class="h2">Gestion des CEV</h1>
                </div>

                <!-- Statistiques rapides -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total CEV</h5>
                                <h3><?php echo mysqli_num_rows($result); ?></h3>
                                <small>Membres inscrits</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">CEV actifs</h5>
                                <h3><?php 
                                    mysqli_data_seek($result, 0);
                                    $active = mysqli_query($conn, "SELECT COUNT(*) as total FROM visiteurs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
                                    $active_count = mysqli_fetch_assoc($active);
                                    echo $active_count['total'];
                                ?></h3>
                                <small>30 derniers jours</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau des visiteurs -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-users mr-1"></i>
                        Liste des membres CEV
                        <button onclick="exportToExcel()" class="btn btn-sm btn-success float-right ml-2">
                            <i class="fas fa-file-excel mr-1"></i>Exporter
                        </button>
                        <button onclick="window.print()" class="btn btn-sm btn-info float-right">
                            <i class="fas fa-print mr-1"></i>Imprimer
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Adresse</th>
                                        <th>CEV</th>
                                        <th>Téléphone</th>
                                        <th>Date d'inscription</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    mysqli_data_seek($result, 0);
                                    while($row = mysqli_fetch_assoc($result)): 
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($row['mail']); ?></td>
                                        <td><?php echo htmlspecialchars($row['adresse']); ?></td>
                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($row['cev']); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
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

        function exportToExcel() {
            window.location.href = 'export_cev.php';
        }
    </script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>
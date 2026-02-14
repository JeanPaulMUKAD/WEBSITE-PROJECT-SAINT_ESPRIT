<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

require_once '../includes/db_connect.php';

// Statistiques mensuelles
$current_year = date('Y');
$monthly_stats = [];
for($i = 1; $i <= 12; $i++) {
    $month = sprintf("%02d", $i);
    $query = "SELECT 
                COUNT(CASE WHEN MONTH(date_bapteme) = $i AND YEAR(date_bapteme) = $current_year THEN 1 END) as baptemes,
                COUNT(CASE WHEN MONTH(date_communion) = $i AND YEAR(date_communion) = $current_year THEN 1 END) as communions,
                COUNT(CASE WHEN MONTH(date_confirmation) = $i AND YEAR(date_confirmation) = $current_year THEN 1 END) as confirmations
              FROM sacrements";
    $result = mysqli_query($conn, $query);
    $monthly_stats[$i] = mysqli_fetch_assoc($result);
}

// Statistiques CEV
$cev_stats = mysqli_query($conn, "SELECT cev, COUNT(*) as total FROM visiteurs GROUP BY cev");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administration</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header_admin.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar_admin.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Tableau de bord</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-download"></i> Exporter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cartes de statistiques -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Baptêmes</h5>
                                <h2><?php echo array_sum(array_column($monthly_stats, 'baptemes')); ?></h2>
                                <small>Cette année</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Communions</h5>
                                <h2><?php echo array_sum(array_column($monthly_stats, 'communions')); ?></h2>
                                <small>Cette année</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Confirmations</h5>
                                <h2><?php echo array_sum(array_column($monthly_stats, 'confirmations')); ?></h2>
                                <small>Cette année</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title">CEV</h5>
                                <h2><?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM visiteurs")); ?></h2>
                                <small>Membres inscrits</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graphiques -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-line mr-1"></i>
                                Évolution mensuelle des sacrements
                            </div>
                            <div class="card-body">
                                <canvas id="sacrementsChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-pie mr-1"></i>
                                Répartition par CEV
                            </div>
                            <div class="card-body">
                                <canvas id="cevChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Graphique des sacrements
        const ctx1 = document.getElementById('sacrementsChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [{
                    label: 'Baptêmes',
                    data: [<?php 
                        for($i=1; $i<=12; $i++) {
                            echo $monthly_stats[$i]['baptemes'] . ',';
                        }
                    ?>],
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Communions',
                    data: [<?php 
                        for($i=1; $i<=12; $i++) {
                            echo $monthly_stats[$i]['communions'] . ',';
                        }
                    ?>],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Confirmations',
                    data: [<?php 
                        for($i=1; $i<=12; $i++) {
                            echo $monthly_stats[$i]['confirmations'] . ',';
                        }
                    ?>],
                    borderColor: 'rgb(153, 102, 255)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Graphique des CEV
        const ctx2 = document.getElementById('cevChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: [<?php 
                    while($cev = mysqli_fetch_assoc($cev_stats)) {
                        echo "'" . $cev['cev'] . "',";
                    }
                ?>],
                datasets: [{
                    data: [<?php 
                        mysqli_data_seek($cev_stats, 0);
                        while($cev = mysqli_fetch_assoc($cev_stats)) {
                            echo $cev['total'] . ',';
                        }
                    ?>],
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>
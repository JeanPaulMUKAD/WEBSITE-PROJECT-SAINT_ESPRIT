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

// Total des membres
$total_membres = mysqli_query($conn, "SELECT COUNT(*) as total FROM visiteurs");
$total_membres = mysqli_fetch_assoc($total_membres)['total'];

// Dernières activités
$recent_activities = mysqli_query($conn, "SELECT nom_complet, date_bapteme, created_at FROM sacrements ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administration Paroisse</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: #f8f9fc;
        }
        
        /* Sidebar styling */
        .sidebar {
            background: linear-gradient(180deg, #8B0000 0%, #A52A2A 100%);
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 100;
            transition: all 0.3s;
            padding-top: 70px;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 25px;
            margin: 5px 15px;
            border-radius: 10px;
            transition: all 0.3s;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 25px;
            font-size: 1.1rem;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.15);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border-left: 3px solid #FFD700;
        }
        
        .sidebar .nav-link.logout {
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.2);
            border-radius: 0;
            color: #FFE4E4;
        }
        
        .sidebar .nav-link.logout:hover {
            background: rgba(255,0,0,0.3);
        }
        
        /* Main content */
        .main-content {
            margin-left: 250px;
            padding: 20px 30px;
        }
        
        /* Header styling */
        .top-header {
            background: white;
            border-radius: 15px;
            padding: 15px 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin: 0;
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
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-badge {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .user-badge i {
            margin-right: 8px;
        }
        
        .date-display {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Cards styling */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(139,0,0,0.15);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #8B0000, #FF6B6B);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .stat-icon i {
            font-size: 30px;
            color: white;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: #333;
            line-height: 1.2;
            margin-bottom: 5px;
        }
        
        .stat-change {
            color: #28a745;
            font-size: 0.9rem;
            background: #e8f5e9;
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
        }
        
        /* Chart cards */
        .chart-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            border: none;
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .chart-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .chart-title i {
            color: #8B0000;
            margin-right: 10px;
        }
        
        .chart-legend {
            display: flex;
            gap: 20px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            color: #666;
        }
        
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            margin-right: 5px;
        }
        
        /* Activity list */
        .activity-list {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            background: #f0f0f0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .activity-icon i {
            color: #8B0000;
        }
        
        .activity-details {
            flex: 1;
        }
        
        .activity-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 3px;
        }
        
        .activity-time {
            font-size: 0.8rem;
            color: #999;
        }
        
        .activity-badge {
            background: #e8f5e9;
            color: #28a745;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar.show {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar améliorée avec déconnexion -->
    <nav id="sidebarMenu" class="sidebar">
        <div class="sidebar-sticky">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gestion_sacrements.php' ? 'active' : ''; ?>" href="gestion_sacrements.php">
                        <i class="fas fa-cross"></i>
                        Gestion des Sacrements
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gestion_cev.php' ? 'active' : ''; ?>" href="gestion_cev.php">
                        <i class="fas fa-users"></i>
                        Gestion des CEV
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'parametres.php' ? 'active' : ''; ?>" href="parametres.php">
                        <i class="fas fa-cog"></i>
                        Paramètres
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link logout" href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main content -->
    <div class="main-content">
        <!-- Header amélioré -->
        <div class="top-header">
            <div>
                <h1 class="page-title">
                    Tableau de bord 
                    <span><i class="far fa-calendar-alt mr-1"></i><?php echo date('d/m/Y'); ?></span>
                </h1>
            </div>
            <div class="user-info">
                <div class="date-display">
                    <i class="far fa-clock mr-1"></i>
                    <?php echo date('H:i'); ?>
                </div>
                <div class="user-badge">
                    <i class="fas fa-user-circle"></i>
                    <?php echo $_SESSION['admin_username']; ?>
                </div>
            </div>
        </div>

        <!-- Cartes de statistiques améliorées -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-cross"></i>
                    </div>
                    <div class="stat-label">Baptêmes</div>
                    <div class="stat-value"><?php echo array_sum(array_column($monthly_stats, 'baptemes')); ?></div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-up mr-1"></i>+12% ce mois
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-bread-slice"></i>
                    </div>
                    <div class="stat-label">Communions</div>
                    <div class="stat-value"><?php echo array_sum(array_column($monthly_stats, 'communions')); ?></div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-up mr-1"></i>+5% ce mois
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dove"></i>
                    </div>
                    <div class="stat-label">Confirmations</div>
                    <div class="stat-value"><?php echo array_sum(array_column($monthly_stats, 'confirmations')); ?></div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-down mr-1"></i>-2% ce mois
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-label">Membres CEV</div>
                    <div class="stat-value"><?php echo $total_membres; ?></div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-up mr-1"></i>+8 nouveaux
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques améliorés -->
        <div class="row">
            <div class="col-md-8">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">
                            <i class="fas fa-chart-line"></i>
                            Évolution mensuelle des sacrements
                        </h5>
                        <div class="chart-legend">
                            <div class="legend-item">
                                <span class="legend-color" style="background: #8B0000;"></span>
                                Baptêmes
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background: #FF6B6B;"></span>
                                Communions
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background: #FFB6B6;"></span>
                                Confirmations
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="sacrementsChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">
                            <i class="fas fa-chart-pie"></i>
                            Répartition par CEV
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="cevChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activités récentes -->
        <div class="row">
            <div class="col-12">
                <div class="activity-list">
                    <h5 class="chart-title mb-4">
                        <i class="fas fa-history"></i>
                        Activités récentes
                    </h5>
                    <?php while($activity = mysqli_fetch_assoc($recent_activities)): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-cross"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-name">
                                <?php echo htmlspecialchars($activity['nom_complet']); ?>
                            </div>
                            <div class="activity-time">
                                <i class="far fa-clock mr-1"></i>
                                <?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?>
                            </div>
                        </div>
                        <div class="activity-badge">
                            <i class="fas fa-check-circle mr-1"></i>
                            Baptême
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Graphique des sacrements avec couleurs bordeaux
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
                    borderColor: '#8B0000',
                    backgroundColor: 'rgba(139, 0, 0, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#8B0000',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }, {
                    label: 'Communions',
                    data: [<?php 
                        for($i=1; $i<=12; $i++) {
                            echo $monthly_stats[$i]['communions'] . ',';
                        }
                    ?>],
                    borderColor: '#FF6B6B',
                    backgroundColor: 'rgba(255, 107, 107, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#FF6B6B',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }, {
                    label: 'Confirmations',
                    data: [<?php 
                        for($i=1; $i<=12; $i++) {
                            echo $monthly_stats[$i]['confirmations'] . ',';
                        }
                    ?>],
                    borderColor: '#FFB6B6',
                    backgroundColor: 'rgba(255, 182, 182, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#FFB6B6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Graphique des CEV avec couleurs bordeaux
        const ctx2 = document.getElementById('cevChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: [<?php 
                    mysqli_data_seek($cev_stats, 0);
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
                        '#8B0000',
                        '#A52A2A',
                        '#CD5C5C',
                        '#DC143C',
                        '#B22222',
                        '#E9967A',
                        '#FA8072'
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>
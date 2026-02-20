<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

require_once '../includes/db_connect.php';

// Vérifier si les colonnes existent
$check_created_at = mysqli_query($conn, "SHOW COLUMNS FROM visiteurs LIKE 'created_at'");
$has_created_at = mysqli_num_rows($check_created_at) > 0;

$check_nature = mysqli_query($conn, "SHOW COLUMNS FROM visiteurs LIKE 'nature'");
$has_nature = mysqli_num_rows($check_nature) > 0;

if (!$has_created_at || !$has_nature) {
    die("❌ Les colonnes nécessaires n'existent pas. Veuillez d'abord exécuter les requêtes SQL d'ajout.");
}

// Récupération des visiteurs avec created_at et nature
$query = "SELECT * FROM visiteurs ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Erreur SQL : " . mysqli_error($conn));
}

// Statistiques
$total_membres = mysqli_num_rows($result);

// Membres actifs (30 derniers jours)
$active_query = "SELECT COUNT(*) as total FROM visiteurs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$active_result = mysqli_query($conn, $active_query);
$active_membres = $active_result ? mysqli_fetch_assoc($active_result)['total'] : 0;

// Nombre de CEV distincts
$cev_query = "SELECT COUNT(DISTINCT cev) as total FROM visiteurs WHERE cev IS NOT NULL AND cev != ''";
$cev_distinct_result = mysqli_query($conn, $cev_query);
$cev_distinct = $cev_distinct_result ? mysqli_fetch_assoc($cev_distinct_result)['total'] : 0;

// Nouveaux membres ce mois-ci
$nouveaux_mois_query = "SELECT COUNT(*) as total FROM visiteurs WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
$nouveaux_mois_result = mysqli_query($conn, $nouveaux_mois_query);
$nouveaux_mois = $nouveaux_mois_result ? mysqli_fetch_assoc($nouveaux_mois_result)['total'] : 0;

// Statistiques par nature
$nature_stats = [];
$nature_query = "SELECT nature, COUNT(*) as total FROM visiteurs GROUP BY nature";
$nature_result = mysqli_query($conn, $nature_query);
if($nature_result) {
    while($row = mysqli_fetch_assoc($nature_result)) {
        $nature_stats[$row['nature']] = $row['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des CEV - Administration</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: #f8f9fc; margin: 0; padding: 0; }
        
        .main-content {
            margin-left: 280px;
            padding: 20px 30px;
        }
        
        /* En-tête */
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
            background: #f0f0f0;
            padding: 5px 12px;
            border-radius: 20px;
            margin-left: 15px;
        }
        
        /* Statistiques */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(139,0,0,0.15);
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
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .stat-icon i { font-size: 32px; color: white; }
        .stat-value { font-size: 2.2rem; font-weight: 700; color: #333; line-height: 1.2; }
        .stat-label { color: #666; font-size: 0.95rem; margin: 0; }
        .stat-trend {
            font-size: 0.85rem;
            color: #28a745;
            background: #e8f5e9;
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            margin-top: 8px;
        }
        
        /* Badges pour nature */
        .badge-nature {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .badge-membre { background: #28a745; color: white; }
        .badge-visiteur { background: #ffc107; color: #333; }
        .badge-catechumene { background: #17a2b8; color: white; }
        .badge-fidele { background: #8B0000; color: white; }
        .badge-default { background: #6c757d; color: white; }
        
        /* Tableau */
        .table-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
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
            background: rgba(139,0,0,0.1);
            padding: 10px;
            border-radius: 10px;
        }
        
        .table-title h5 {
            margin: 0;
            font-weight: 600;
            color: #333;
        }
        
        .table-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-excel {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .btn-excel:hover {
            background: #218838;
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-print {
            background: #17a2b8;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .btn-print:hover {
            background: #138496;
            transform: translateY(-2px);
            color: white;
        }
        
        .badge-cev {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .badge-new {
            background: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            margin-left: 8px;
        }
        
        .member-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .member-avatar {
            width: 40px;
            height: 40px;
            background: #f0f0f0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .member-avatar i {
            color: #8B0000;
            font-size: 20px;
        }
        
        /* Filtres */
        .filters-section {
            background: white;
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 8px 16px;
            border-radius: 25px;
            border: none;
            background: #f0f0f0;
            color: #666;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .filter-btn:hover {
            background: #8B0000;
            color: white;
        }
        
        .filter-btn.active {
            background: #8B0000;
            color: white;
        }
        
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 15px; }
            .page-header { flex-direction: column; text-align: center; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include '../includes/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <!-- En-tête -->
        <div class="page-header">
            <h1 class="page-title">
                Gestion des CEV
                <span><i class="far fa-calendar-alt mr-1"></i><?php echo date('d/m/Y'); ?></span>
            </h1>
            <button class="btn btn-primary" onclick="ajouterMembre()">
                <i class="fas fa-user-plus mr-2"></i>Nouveau membre
            </button>
        </div>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div>
                    <div class="stat-value"><?php echo $total_membres; ?></div>
                    <div class="stat-label">Total membres</div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up mr-1"></i>+<?php echo $nouveaux_mois; ?> ce mois
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                <div>
                    <div class="stat-value"><?php echo $active_membres; ?></div>
                    <div class="stat-label">Membres actifs</div>
                    <div class="stat-trend">
                        <i class="fas fa-clock mr-1"></i>30 derniers jours
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-church"></i></div>
                <div>
                    <div class="stat-value"><?php echo $cev_distinct; ?></div>
                    <div class="stat-label">CEV différentes</div>
                    <div class="stat-trend">
                        <i class="fas fa-map-marker-alt mr-1"></i>Groupes actifs
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-tag"></i></div>
                <div>
                    <div class="stat-value"><?php echo count($nature_stats); ?></div>
                    <div class="stat-label">Types de membres</div>
                    <div class="stat-trend">
                        <i class="fas fa-users mr-1"></i>Natures différentes
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtres par nature -->
        <div class="filters-section">
            <button class="filter-btn active" onclick="filterNature('all')">Tous</button>
            <button class="filter-btn" onclick="filterNature('Membre')">Membres</button>
            <button class="filter-btn" onclick="filterNature('Visiteur')">Visiteurs</button>
            <button class="filter-btn" onclick="filterNature('Catéchumène')">Catéchumènes</button>
            <button class="filter-btn" onclick="filterNature('Fidèle')">Fidèles</button>
        </div>
        
        <!-- Tableau avec nature -->
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <i class="fas fa-users"></i>
                    <h5>Liste des membres CEV</h5>
                </div>
                <div class="table-actions">
                    <button onclick="exportToExcel()" class="btn-excel">
                        <i class="fas fa-file-excel mr-1"></i>Exporter
                    </button>
                    <button onclick="window.print()" class="btn-print">
                        <i class="fas fa-print mr-1"></i>Imprimer
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Membre</th>
                            <th>Contact</th>
                            <th>Adresse</th>
                            <th>CEV</th>
                            <th>Nature</th>
                            <th>Inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        mysqli_data_seek($result, 0);
                        while($row = mysqli_fetch_assoc($result)): 
                            $date_inscription = strtotime($row['created_at']);
                            $est_nouveau = $date_inscription > strtotime('-7 days');
                            
                            // Déterminer la classe CSS pour la nature
                            $nature_class = 'badge-default';
                            $nature_text = htmlspecialchars($row['nature'] ?? 'Non défini');
                            
                            if($nature_text == 'Membre') $nature_class = 'badge-membre';
                            elseif($nature_text == 'Visiteur') $nature_class = 'badge-visiteur';
                            elseif($nature_text == 'Catéchumène') $nature_class = 'badge-catechumene';
                            elseif($nature_text == 'Fidèle') $nature_class = 'badge-fidele';
                        ?>
                        <tr data-nature="<?php echo $nature_text; ?>">
                            <td>#<?php echo $row['id']; ?></td>
                            <td>
                                <div class="member-info">
                                    <div class="member-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($row['nom']); ?></strong>
                                        <?php if($est_nouveau): ?>
                                        <span class="badge-new">Nouveau</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><i class="fas fa-envelope text-muted mr-1"></i><?php echo htmlspecialchars($row['mail']) ?: '-'; ?></div>
                                <div><i class="fas fa-phone text-muted mr-1"></i><?php echo htmlspecialchars($row['phone']) ?: '-'; ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($row['adresse']) ?: '-'; ?></td>
                            <td>
                                <?php if($row['cev']): ?>
                                <span class="badge-cev">
                                    <i class="fas fa-cross mr-1"></i>
                                    <?php echo htmlspecialchars($row['cev']); ?>
                                </span>
                                <?php else: ?> - <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-nature <?php echo $nature_class; ?>">
                                    <i class="fas fa-tag mr-1"></i>
                                    <?php echo $nature_text; ?>
                                </span>
                            </td>
                            <td>
                                <div><i class="far fa-calendar-alt mr-1"></i><?php echo date('d/m/Y', $date_inscription); ?></div>
                                <small class="text-muted">
                                    <i class="far fa-clock mr-1"></i>
                                    <?php 
                                    $diff = time() - $date_inscription;
                                    $jours = floor($diff / (60*60*24));
                                    echo "Il y a $jours jours";
                                    ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-info" onclick="voirDetails(<?php echo $row['id']; ?>)" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" onclick="modifierMembre(<?php echo $row['id']; ?>)" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="supprimerMembre(<?php echo $row['id']; ?>)" title="Supprimer">
                                        <i class="fas fa-trash"></i>
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
                <div class="modal-header" style="background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-user-circle mr-2"></i>
                        Détails du membre
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" style="color: white;">
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script>
        var table;
        
        $(document).ready(function() {
            table = $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
                },
                "order": [[6, "desc"]], // Tri par date d'inscription
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Tous"]]
            });
        });

        function filterNature(nature) {
            // Mettre à jour les boutons actifs
            $('.filter-btn').removeClass('active');
            $(event.target).addClass('active');
            
            // Filtrer le tableau
            if(nature === 'all') {
                table.column(5).search('').draw(); // Colonne nature (index 5)
            } else {
                table.column(5).search('^' + nature + '$', true, false).draw();
            }
        }

        function voirDetails(id) {
            $('#detailsContent').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-3x text-danger mb-3"></i><p>Chargement des détails...</p></div>');
            $('#detailsModal').modal('show');
            
            // Requête AJAX pour obtenir les détails
            $.get('get_membre_details.php', {id: id}, function(data) {
                $('#detailsContent').html(data);
            }).fail(function() {
                $('#detailsContent').html('<div class="alert alert-danger">Erreur lors du chargement des détails.</div>');
            });
        }

        function modifierMembre(id) {
            window.location.href = 'edit_cev.php?id=' + id;
        }

        function supprimerMembre(id) {
            if(confirm('Êtes-vous sûr de vouloir supprimer ce membre ? Cette action est irréversible.')) {
                window.location.href = 'delete_cev.php?id=' + id;
            }
        }

        function ajouterMembre() {
            window.location.href = 'ajouter_cev.php';
        }

        function exportToExcel() {
            window.location.href = 'export_cev.php';
        }
    </script>
</body>
</html>
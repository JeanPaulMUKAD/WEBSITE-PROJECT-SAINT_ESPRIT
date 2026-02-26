<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// === CONNEXION DIRECTE À LA BASE DE DONNÉES ===
$host = '127.0.0.1:3306';
$user = 'u913148723_JeanPaul';
$password = 'KdANeUq7;';
$database = 'u913148723_authentic';

$conn = mysqli_connect($host, $user, $password, $database);

// Vérifier la connexion
if (!$conn) {
    die("❌ Erreur de connexion à la base de données : " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// Récupérer TOUTES les actualités (y compris les homélies et autres catégories)
$query = "SELECT * FROM actualites WHERE publie = 1 ORDER BY 
          CASE 
            WHEN categorie = 'homelie' THEN 1
            WHEN categorie = 'evenement' THEN 2
            WHEN categorie = 'fete' THEN 3
            WHEN categorie = 'anniversaire' THEN 4
            ELSE 5
          END, date_evenement DESC, created_at DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("❌ Erreur SQL : " . mysqli_error($conn));
}

$actualites = [];
while ($row = mysqli_fetch_assoc($result)) {
    $actualites[] = $row;
}

// Regrouper les actualités par catégorie
$categories = [];
foreach ($actualites as $actu) {
    $cat = $actu['categorie'] ?? 'general';
    if (!isset($categories[$cat])) {
        $categories[$cat] = [];
    }
    $categories[$cat][] = $actu;
}
?>

<!-- Page Content - Background conservé -->
<div class="page-heading actuality-heading header-text">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-content">
                    <h4 class="wow fadeInDown" data-wow-delay="0.8s" data-wow-duration="1.2s">restons branché</h4>
                    <h2 class="wow fadeInDown" data-wow-delay="0.6s" data-wow-duration="1.2s">pour en savoir plus</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Affichage dynamique de TOUTES les actualités par catégorie -->
<?php if (!empty($actualites)): ?>
    
    <!-- NEUVAINE / HOMELIES -->
    <?php if (!empty($categories['homelie'])): ?>
        <?php foreach ($categories['homelie'] as $index => $actu): 
            $image_position = ($index % 2 == 0) ? 'col-md-6' : 'col-md-6 order-md-2';
            $text_position = ($index % 2 == 0) ? 'col-md-6' : 'col-md-6 order-md-1';
            // Ajouter un slash au début pour que le chemin soit absolu depuis la racine
            $image_path = !empty($actu['image']) ? '/' . ltrim($actu['image'], '/') : 'assets/images/services/default.jpg';
            // ID unique pour le collapse
            $collapse_id = 'collapse_' . $actu['id'];
            
            // Déterminer le texte à afficher
            $display_text = !empty($actu['description']) ? $actu['description'] : (strlen($actu['contenu']) > 200 ? substr($actu['contenu'], 0, 200) . '...' : $actu['contenu']);
        ?>
        <div class="best-features about-features">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-heading">
                            <h3 class="section-heading wow fadeInLeft" data-wow-delay="1s" data-wow-duration="1.2s"><?php echo htmlspecialchars($actu['titre']); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="<?php echo $image_position; ?>">
                        <div class="right-image wow fadeInUp" data-wow-delay="0.8s" data-wow-duration="1.2s">
                            <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($actu['titre']); ?>">
                        </div>
                    </div>
                    <div class="<?php echo $text_position; ?>">
                        <div class="left-content wow fadeInUp" data-wow-delay="0.8s" data-wow-duration="1.2s">
                            <h4><?php echo htmlspecialchars($actu['titre']); ?></h4>
                            <?php if (!empty($actu['date_evenement'])): ?>
                                <p class="text-muted"><i class="far fa-calendar-alt mr-2"></i><?php echo date('d F Y', strtotime($actu['date_evenement'])); ?></p>
                            <?php endif; ?>
                            
                            <!-- Texte affiché (description ou extrait) -->
                            <p><?php echo nl2br(htmlspecialchars($display_text)); ?></p>
                            
                            <!-- Bouton Lire la suite - visible s'il y a un contenu complet -->
                            <?php if (!empty($actu['contenu']) && (empty($actu['description']) || strlen($actu['contenu']) > 200)): ?>
                                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#<?php echo $collapse_id; ?>" aria-expanded="false" aria-controls="<?php echo $collapse_id; ?>" style="background: #8B0000; border: none; margin-top: 15px;">
                                    <i class="fas fa-chevron-down mr-2"></i> Lire la suite
                                </button>
                                
                                <!-- Contenu complet caché -->
                                <div class="collapse" id="<?php echo $collapse_id; ?>" style="margin-top: 20px;">
                                    <div style="background: #f9f9f9; padding: 20px; border-radius: 10px; border-left: 4px solid #8B0000;">
                                        <?php echo nl2br(htmlspecialchars($actu['contenu'])); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- RENCONTRES / EVENEMENTS -->
    <?php if (!empty($categories['evenement'])): ?>
        <?php foreach ($categories['evenement'] as $index => $actu): 
            $image_position = ($index % 2 == 0) ? 'col-md-6' : 'col-md-6 order-md-2';
            $text_position = ($index % 2 == 0) ? 'col-md-6' : 'col-md-6 order-md-1';
            $image_path = !empty($actu['image']) ? '/' . ltrim($actu['image'], '/') : 'assets/images/services/default.jpg';
            $collapse_id = 'collapse_' . $actu['id'];
            $display_text = !empty($actu['description']) ? $actu['description'] : (strlen($actu['contenu']) > 200 ? substr($actu['contenu'], 0, 200) . '...' : $actu['contenu']);
        ?>
        <div class="best-features about-features">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-heading">
                            <h3 class="section-heading wow fadeInLeft" data-wow-delay="1s" data-wow-duration="1.2s"><?php echo htmlspecialchars($actu['titre']); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="<?php echo $image_position; ?>">
                        <div class="right-image wow fadeInUp" data-wow-delay="0.8s" data-wow-duration="1.2s">
                            <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($actu['titre']); ?>">
                        </div>
                    </div>
                    <div class="<?php echo $text_position; ?>">
                        <div class="left-content wow fadeInUp" data-wow-delay="0.8s" data-wow-duration="1.2s">
                            <h4><?php echo htmlspecialchars($actu['titre']); ?></h4>
                            <?php if (!empty($actu['date_evenement'])): ?>
                                <p class="text-muted"><i class="far fa-calendar-alt mr-2"></i><?php echo date('d F Y', strtotime($actu['date_evenement'])); ?></p>
                            <?php endif; ?>
                            
                            <p><?php echo nl2br(htmlspecialchars($display_text)); ?></p>
                            
                            <?php if (!empty($actu['contenu']) && (empty($actu['description']) || strlen($actu['contenu']) > 200)): ?>
                                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#<?php echo $collapse_id; ?>" aria-expanded="false" style="background: #8B0000; border: none; margin-top: 15px;">
                                    <i class="fas fa-chevron-down mr-2"></i> Lire la suite
                                </button>
                                
                                <div class="collapse" id="<?php echo $collapse_id; ?>" style="margin-top: 20px;">
                                    <div style="background: #f9f9f9; padding: 20px; border-radius: 10px; border-left: 4px solid #8B0000;">
                                        <?php echo nl2br(htmlspecialchars($actu['contenu'])); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- FETES (Pâques, etc.) -->
    <?php if (!empty($categories['fete'])): ?>
        <?php foreach ($categories['fete'] as $index => $actu): 
            $image_position = ($index % 2 == 0) ? 'col-md-6' : 'col-md-6 order-md-2';
            $text_position = ($index % 2 == 0) ? 'col-md-6' : 'col-md-6 order-md-1';
            $image_path = !empty($actu['image']) ? '/' . ltrim($actu['image'], '/') : 'assets/images/services/default.jpg';
            $collapse_id = 'collapse_' . $actu['id'];
            $display_text = !empty($actu['description']) ? $actu['description'] : (strlen($actu['contenu']) > 200 ? substr($actu['contenu'], 0, 200) . '...' : $actu['contenu']);
        ?>
        <div class="best-features about-features">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-heading">
                            <h3 class="section-heading wow fadeInLeft" data-wow-delay="1s" data-wow-duration="1.2s"><?php echo htmlspecialchars($actu['titre']); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="<?php echo $image_position; ?>">
                        <div class="right-image wow fadeInUp" data-wow-delay="0.8s" data-wow-duration="1.2s">
                            <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($actu['titre']); ?>">
                        </div>
                    </div>
                    <div class="<?php echo $text_position; ?>">
                        <div class="left-content wow fadeInUp" data-wow-delay="0.8s" data-wow-duration="1.2s">
                            <h4><?php echo htmlspecialchars($actu['titre']); ?></h4>
                            <?php if (!empty($actu['date_evenement'])): ?>
                                <p class="text-muted"><i class="far fa-calendar-alt mr-2"></i><?php echo date('d F Y', strtotime($actu['date_evenement'])); ?></p>
                            <?php endif; ?>
                            
                            <p><?php echo nl2br(htmlspecialchars($display_text)); ?></p>
                            
                            <?php if (!empty($actu['contenu']) && (empty($actu['description']) || strlen($actu['contenu']) > 200)): ?>
                                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#<?php echo $collapse_id; ?>" aria-expanded="false" style="background: #8B0000; border: none; margin-top: 15px;">
                                    <i class="fas fa-chevron-down mr-2"></i> Lire la suite
                                </button>
                                
                                <div class="collapse" id="<?php echo $collapse_id; ?>" style="margin-top: 20px;">
                                    <div style="background: #f9f9f9; padding: 20px; border-radius: 10px; border-left: 4px solid #8B0000;">
                                        <?php echo nl2br(htmlspecialchars($actu['contenu'])); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- ANNIVERSAIRES -->
    <?php if (!empty($categories['anniversaire'])): ?>
        <?php foreach ($categories['anniversaire'] as $index => $actu): 
            $image_position = ($index % 2 == 0) ? 'col-md-6' : 'col-md-6 order-md-2';
            $text_position = ($index % 2 == 0) ? 'col-md-6' : 'col-md-6 order-md-1';
            $image_path = !empty($actu['image']) ? '/' . ltrim($actu['image'], '/') : 'assets/images/services/default.jpg';
            $collapse_id = 'collapse_' . $actu['id'];
            $display_text = !empty($actu['description']) ? $actu['description'] : (strlen($actu['contenu']) > 200 ? substr($actu['contenu'], 0, 200) . '...' : $actu['contenu']);
        ?>
        <div class="best-features about-features">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-heading">
                            <h3 class="section-heading wow fadeInLeft" data-wow-delay="1s" data-wow-duration="1.2s"><?php echo htmlspecialchars($actu['titre']); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="<?php echo $image_position; ?>">
                        <div class="right-image wow fadeInUp" data-wow-delay="0.8s" data-wow-duration="1.2s">
                            <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($actu['titre']); ?>">
                        </div>
                    </div>
                    <div class="<?php echo $text_position; ?>">
                        <div class="left-content wow fadeInUp" data-wow-delay="0.8s" data-wow-duration="1.2s">
                            <h4><?php echo htmlspecialchars($actu['titre']); ?></h4>
                            <?php if (!empty($actu['date_evenement'])): ?>
                                <p class="text-muted"><i class="far fa-calendar-alt mr-2"></i><?php echo date('d F Y', strtotime($actu['date_evenement'])); ?></p>
                            <?php endif; ?>
                            
                            <p><?php echo nl2br(htmlspecialchars($display_text)); ?></p>
                            
                            <?php if (!empty($actu['contenu']) && (empty($actu['description']) || strlen($actu['contenu']) > 200)): ?>
                                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#<?php echo $collapse_id; ?>" aria-expanded="false" style="background: #8B0000; border: none; margin-top: 15px;">
                                    <i class="fas fa-chevron-down mr-2"></i> Lire la suite
                                </button>
                                
                                <div class="collapse" id="<?php echo $collapse_id; ?>" style="margin-top: 20px;">
                                    <div style="background: #f9f9f9; padding: 20px; border-radius: 10px; border-left: 4px solid #8B0000;">
                                        <?php echo nl2br(htmlspecialchars($actu['contenu'])); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- AUTRES CATEGORIES -->
    <?php foreach ($categories as $cat => $items): 
        if (in_array($cat, ['homelie', 'evenement', 'fete', 'anniversaire'])) continue;
    ?>
        <?php foreach ($items as $index => $actu): 
            $image_position = ($index % 2 == 0) ? 'col-md-6' : 'col-md-6 order-md-2';
            $text_position = ($index % 2 == 0) ? 'col-md-6' : 'col-md-6 order-md-1';
            $image_path = !empty($actu['image']) ? '/' . ltrim($actu['image'], '/') : 'assets/images/services/default.jpg';
            $collapse_id = 'collapse_' . $actu['id'];
            $display_text = !empty($actu['description']) ? $actu['description'] : (strlen($actu['contenu']) > 200 ? substr($actu['contenu'], 0, 200) . '...' : $actu['contenu']);
        ?>
        <div class="best-features about-features">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-heading">
                            <h3 class="section-heading wow fadeInLeft" data-wow-delay="1s" data-wow-duration="1.2s"><?php echo htmlspecialchars($actu['titre']); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="<?php echo $image_position; ?>">
                        <div class="right-image wow fadeInUp" data-wow-delay="0.8s" data-wow-duration="1.2s">
                            <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($actu['titre']); ?>">
                        </div>
                    </div>
                    <div class="<?php echo $text_position; ?>">
                        <div class="left-content wow fadeInUp" data-wow-delay="0.8s" data-wow-duration="1.2s">
                            <h4><?php echo htmlspecialchars($actu['titre']); ?></h4>
                            <?php if (!empty($actu['date_evenement'])): ?>
                                <p class="text-muted"><i class="far fa-calendar-alt mr-2"></i><?php echo date('d F Y', strtotime($actu['date_evenement'])); ?></p>
                            <?php endif; ?>
                            
                            <p><?php echo nl2br(htmlspecialchars($display_text)); ?></p>
                            
                            <?php if (!empty($actu['contenu']) && (empty($actu['description']) || strlen($actu['contenu']) > 200)): ?>
                                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#<?php echo $collapse_id; ?>" aria-expanded="false" style="background: #8B0000; border: none; margin-top: 15px;">
                                    <i class="fas fa-chevron-down mr-2"></i> Lire la suite
                                </button>
                                
                                <div class="collapse" id="<?php echo $collapse_id; ?>" style="margin-top: 20px;">
                                    <div style="background: #f9f9f9; padding: 20px; border-radius: 10px; border-left: 4px solid #8B0000;">
                                        <?php echo nl2br(htmlspecialchars($actu['contenu'])); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
    
<?php else: ?>
    <!-- Message si aucune actualité -->
    <div class="best-features about-features">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center py-5">
                    <h4>Aucune actualité pour le moment</h4>
                    <p>Revenez plus tard pour découvrir les nouvelles de la paroisse.</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Ajout de Bootstrap JS pour le collapse -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Animation de la flèche lors du clic
    $('.btn-primary').on('click', function() {
        var icon = $(this).find('i');
        if ($(this).attr('aria-expanded') === 'true') {
            icon.css('transform', 'rotate(0deg)');
        } else {
            icon.css('transform', 'rotate(180deg)');
        }
    });
</script>
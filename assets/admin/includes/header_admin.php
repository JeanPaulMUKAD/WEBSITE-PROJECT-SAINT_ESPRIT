<?php
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}
?>
<!-- Header minimal - juste un espace pour le contenu principal -->
<style>
/* Ajustement du contenu principal pour la sidebar fixe */
.main-content {
    margin-left: 280px;
    padding: 20px 30px;
    min-height: 100vh;
    background: #f8f9fc;
    font-family: 'Montserrat', sans-serif;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
    }
}
</style>
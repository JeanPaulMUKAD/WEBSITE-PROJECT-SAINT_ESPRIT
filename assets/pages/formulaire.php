<?php
// Configuration de la base de données
$host = "127.0.0.1:3306"; // ou l'adresse de votre serveur de base de données
$dbname = "u913148723_authentic";
$username = "u913148723_JeanPaul";
$password = "KdANeUq7;";

try {
    // Établir la connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['submit'])) {
        // Récupérer les données du formulaire
        $nom = $_GET['nom'];
        $mail = $_GET['mail'];
        $adresse = $_GET['adresse'];
        $cev = $_GET['cev'];
        $phone = $_GET['phone'];
        $nature = $_GET['nature'];

        // Vérifier si l'utilisateur existe déjà par le nom
        $checkSql = "SELECT COUNT(*) FROM visiteurs WHERE nom = :nom";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindParam(':nom', $nom);
        $checkStmt->execute();
        $userExists = $checkStmt->fetchColumn() > 0;

        if ($userExists) {
            echo '<div style="color: red; text-align: center; font-size: 15px; font-family: \'Poppins\', sans-serif;">Cet utilisateur est déjà enregistré.</div>';
        } else {
            // Préparer et exécuter la requête d'insertion avec le champ nature
            $sql = "INSERT INTO visiteurs (nom, mail, adresse, cev, phone, nature) VALUES (:nom, :mail, :adresse, :cev, :phone, :nature)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':mail', $mail);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindParam(':cev', $cev);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':nature', $nature); // NOUVEAU champ nature

            // Exécuter la requête
            if ($stmt->execute()) {
                // Envoyer un e-mail de confirmation
                $to = $mail;
                $subject = "Confirmation d'enregistrement";
                $message = "Bonjour $nom,\n\nMerci de vous être enregistré. Votre inscription a été réussie.\n\nCordialement,\nL'équipe de la Paroisse et Aumonerie Universitaire Catholique Saint Esprit.";
                $headers = "From: contact@paroisseuniversitairestespritlushi.com";

                // Envoi de l'e-mail
                if (mail($to, $subject, $message, $headers)) {
                    echo '<div style="color: green; text-align: center; font-size: 15px; font-family: \'Poppins\', sans-serif;">Les données ont été enregistrées avec succès. Un e-mail de confirmation a été envoyé à votre adresse mail.</div>';
                } else {
                    echo '<div style="color: orange; text-align: center; font-size: 15px; font-family: \'Poppins\', sans-serif;">Les données ont été enregistrées, mais l\'envoi de l\'e-mail a échoué.</div>';
                }
            } else {
                echo '<div style="color: red; text-align: center; font-size: 15px; font-family: \'Poppins\', sans-serif;">Une erreur s\'est produite lors de l\'enregistrement des données.</div>';
            }
        }
    }
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAINT-ESPRIT/ENREG.</title>
</head>
<body>
</body>
</html>
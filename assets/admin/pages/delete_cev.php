<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

require_once '../includes/db_connect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Vérifier si le membre existe
    $check_query = "SELECT id FROM visiteurs WHERE id = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "i", $id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Supprimer le membre
        $delete_query = "DELETE FROM visiteurs WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $id);
        
        if (mysqli_stmt_execute($delete_stmt)) {
            header('Location: gestion_cev.php?success=deleted');
            exit();
        } else {
            header('Location: gestion_cev.php?error=delete_failed');
            exit();
        }
    } else {
        header('Location: gestion_cev.php?error=notfound');
        exit();
    }
} else {
    header('Location: gestion_cev.php?error=invalid_id');
    exit();
}
?>
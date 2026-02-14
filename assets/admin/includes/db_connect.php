<?php
$host = '127.0.0.1:3306';
$user = 'u913148723_JeanPaul';
$password = 'KdANeUq7;';
$database = 'u913148723_authentic';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Définir le charset
mysqli_set_charset($conn, "utf8mb4");
?>
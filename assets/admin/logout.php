<?php
session_start();
session_destroy();
header('Location: connexion/login.php');
exit();
?>
<?php
session_start();

unset($_SESSION['utilisateur']);
session_destroy();

header("Location: Authentification.php");
exit();
?>

<?php
$conn=new mysqli("localhost","root","","dataware3");
if($conn->connect_error){
    die("La connexion à la base de données a échoué : " . $connexion->connect_error);}

?>
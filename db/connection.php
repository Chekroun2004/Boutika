<?php
//connection
$host = 'localhost';
$db = 'boutique_en_ligne';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}
?>

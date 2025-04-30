<?php
//index.php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: views/home.php');
} else {
    header('Location: views/login.php');
}
?>

<?php
//logout
session_start();
session_destroy();
header('Location: ../views/login.php');
exit();
?>
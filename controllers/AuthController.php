<?php
// AuthController.php
session_start();
require_once '../db/connection.php';
require_once '../models/Cart.php';

if (!class_exists('Cart')) {
  die("❌ Erreur : la classe Cart n'a pas été trouvée. Vérifiez le fichier Cart.php.");
}

// ============================== INSCRIPTION ==============================
if (isset($_POST['register'])) {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  if (empty($name) || empty($email) || empty($password)) {
    $_SESSION['error'] = "⚠️ TOUS LES CHAMPS SONT REQUIS.";
    header("Location: ../views/register.php");
    exit;
  }


  $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $_SESSION['error'] = "⚠️ CET EMAIL EST DÉJÀ UTILISÉ.";
    $stmt->close();
    header("Location: ../views/register.php");
    exit;
  }
  $stmt->close();


  $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
  $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $hashedPassword);

  if ($stmt->execute()) {
    $_SESSION['message'] = "✅ INSCRIPTION RÉUSSIE. CONNECTEZ-VOUS MAINTENANT.";
    header("Location: ../views/login.php");
    exit;
  } else {
    $_SESSION['error'] = "❌ ERREUR LORS DE L'INSCRIPTION : " . $stmt->error;
    header("Location: ../views/register.php");
    exit;
  }
}

// ============================== CONNEXION (utilisateur + admin) ==============================
if (isset($_POST['login'])) {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);


  if (empty($email) || empty($password)) {
    $_SESSION['error'] = "⚠️ VEUILLEZ REMPLIR TOUS LES CHAMPS.";
    header("Location: ../views/login.php");
    exit;
  }

 
  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    $_SESSION['error'] = "❗ EMAIL INEXISTANT - INSCRIVEZ-VOUS.";
    header("Location: ../views/login.php");
    exit;
  }

  $user = $result->fetch_assoc();


  if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = "❌ MOT DE PASSE INCORRECT.";
    header("Location: ../views/login.php");
    exit;
  }


  session_regenerate_id(true);
  $_SESSION['user'] = $user;
  $_SESSION['is_admin'] = isset($user['role']) && $user['role'] === 'admin';


  if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $productId => $quantity) {
      Cart::addToCart($user['id'], $productId, $quantity);
    }
    unset($_SESSION['cart']);
  }

 
  if ($_SESSION['is_admin']) {
    header("Location: ../views/admin/dashboard.php");
  } else {
    header("Location: ../views/home.php");
  }
  exit;
}

  header("Location: ../views/login.php");
exit;

?>
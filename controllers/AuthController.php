<?php
// AuthController.php
session_start();
require_once '../db/connection.php';
require_once '../models/Cart.php';

// Vérifier si la classe Cart est bien incluse
if (!class_exists('Cart')) {
  die("❌ Erreur : la classe Cart n'a pas été trouvée. Vérifiez le fichier Cart.php.");
}

// ============================== INSCRIPTION ==============================
if (isset($_POST['register'])) {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);
//chokran reda/ walid
  // Champs requis
  if (empty($name) || empty($email) || empty($password)) {
    $_SESSION['error'] = "⚠️ TOUS LES CHAMPS SONT REQUIS.";
    header("Location: ../views/register.php");
    exit;
  }

  // Vérifier si l'utilisateur existe déjà
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

  // Enregistrer l'utilisateur
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

  // Champs requis
  if (empty($email) || empty($password)) {
    $_SESSION['error'] = "⚠️ VEUILLEZ REMPLIR TOUS LES CHAMPS.";
    header("Location: ../views/login.php");
    exit;
  }

  // 🔐 Informations admin codées en dur (tu peux les déplacer dans une table plus tard)
  $adminEmail = "admin@site.com";
  $adminPassword = "admin123";

  // Vérification des identifiants admin
  if ($email === $adminEmail && $password === $adminPassword) {
    $_SESSION['user'] = [
      'email' => $adminEmail,
      'name' => 'Administrateur'
    ];
    $_SESSION['is_admin'] = true;
    header("Location: ../views/admin/dashboard.php");
    exit;
  }

  // Vérification dans la base de données (utilisateur normal)
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

  // Vérifier mot de passe
  if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = "❌ MOT DE PASSE INCORRECT.";
    header("Location: ../views/login.php");
    exit;
  }

  // Connexion réussie utilisateur
  session_regenerate_id(true);
  $_SESSION['user'] = $user;
  $_SESSION['is_admin'] = false;

  // Fusion du panier anonyme avec celui de l'utilisateur connecté
  if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $productId => $quantity) {
      Cart::addToCart($user['id'], $productId, $quantity);
    }
    unset($_SESSION['cart']);
  }

  header("Location: ../views/home.php");
  exit;
}

// Redirection par défaut
header("Location: ../views/login.php");
exit;

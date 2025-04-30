<?php
//Cart.php
require_once '../db/connection.php';

class Cart
{
    public static function getCartByUserId($userId)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT p.id, p.name, p.price, SUM(c.quantity) AS quantity, SUM(p.price * c.quantity) AS total_price 
                               FROM cart c 
                               JOIN products p ON c.product_id = p.id 
                               WHERE c.user_id = ? 
                               GROUP BY p.id");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function removeFromCart($userId, $productId)
    {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $userId, $productId);
        return $stmt->execute();
    }

    public static function addToCart($userId, $productId, $quantity)
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity)
                               VALUES (?, ?, ?)
                               ON DUPLICATE KEY UPDATE quantity = quantity + ?");
        $stmt->bind_param("iiii", $userId, $productId, $quantity, $quantity);
        return $stmt->execute();
    }

    // Nouvelle méthode pour obtenir un item spécifique du panier
    public static function getCartItem($userId, $productId)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $userId, $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Nouvelle méthode pour mettre à jour la quantité d'un item
    public static function updateCartItem($userId, $productId, $quantity)
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $quantity, $userId, $productId);
        return $stmt->execute();
    }

    // Nouvelle méthode pour vider le panier (utile après commande)
    public static function clearCart($userId)
    {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }
}

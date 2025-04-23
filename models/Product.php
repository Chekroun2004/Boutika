<?php
//Product
require_once __DIR__ . '/../db/connection.php';

class Product
{
    public static function getAllProducts()
    {
        global $conn;
        $result = $conn->query("SELECT * FROM products");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getProductById($productId)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function addProduct($name, $description, $price, $category, $image, $stock)
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, image, stock) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdssi", $name, $description, $price, $category, $image, $stock);
        return $stmt->execute();
    }

    public static function updateProduct($id, $name, $description, $price, $category, $image, $stock)
    {
        global $conn;
        if ($image) {
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, category=?, image=?, stock=? WHERE id=?");
            $stmt->bind_param("ssdssii", $name, $description, $price, $category, $image, $stock, $id);
        } else {
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, category=?, stock=? WHERE id=?");
            $stmt->bind_param("ssdiii", $name, $description, $price, $category, $stock, $id);
        }
        return $stmt->execute();
    }

    public static function deleteProduct($id)
    {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

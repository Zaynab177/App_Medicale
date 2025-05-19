<?php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    public function getAllProducts() {
        $query = "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 ORDER BY p.name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getProductsByCategory($categoryId) {
        $query = "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 WHERE p.category_id = :category_id 
                 ORDER BY p.name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getProductById($id) {
        $query = "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 WHERE p.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function addProduct($name, $description, $categoryId, $quantity, $expirationDate, $price) {
        $query = "INSERT INTO products (name, description, category_id, quantity, expiration_date, price) 
                 VALUES (:name, :description, :category_id, :quantity, :expiration_date, :price)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':expiration_date', $expirationDate);
        $stmt->bindParam(':price', $price);
        
        return $stmt->execute();
    }
    
    public function updateProduct($id, $name, $description, $categoryId, $quantity, $expirationDate, $price) {
        $query = "UPDATE products 
                 SET name = :name, description = :description, category_id = :category_id, 
                     quantity = :quantity, expiration_date = :expiration_date, price = :price 
                 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':expiration_date', $expirationDate);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function updateProductQuantity($id, $quantity) {
        $query = "UPDATE products SET quantity = :quantity WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function deleteProduct($id) {
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function searchProducts($keyword, $categoryId = null) {
        $sql = "SELECT p.*, c.name as category_name 
               FROM products p 
               LEFT JOIN categories c ON p.category_id = c.id 
               WHERE p.name LIKE :keyword OR p.description LIKE :keyword";
        
        if ($categoryId) {
            $sql .= " AND p.category_id = :category_id";
        }
        
        $sql .= " ORDER BY p.name ASC";
        
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%" . $keyword . "%";
        $stmt->bindParam(':keyword', $searchTerm);
        
        if ($categoryId) {
            $stmt->bindParam(':category_id', $categoryId);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getExpiringProducts($days = 30) {
        $date = date('Y-m-d', strtotime("+{$days} days"));
        
        $query = "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 WHERE p.expiration_date <= :date AND p.expiration_date >= CURDATE() AND p.quantity > 0
                 ORDER BY p.expiration_date ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getLowStockProducts($threshold = 10) {
        $query = "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 WHERE p.quantity <= :threshold
                 ORDER BY p.quantity ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':threshold', $threshold);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
?>
<?php
require_once __DIR__ . '/../config/database.php';

class Category {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    public function getAllCategories() {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getCategoryById($id) {
        $query = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function addCategory($name, $description) {
        $query = "INSERT INTO categories (name, description) VALUES (:name, :description)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        
        return $stmt->execute();
    }
    
    public function updateCategory($id, $name, $description) {
        $query = "UPDATE categories SET name = :name, description = :description WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function deleteCategory($id) {
        $query = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function getCategoryProductCount($id) {
        $query = "SELECT COUNT(*) FROM products WHERE category_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
}
?>
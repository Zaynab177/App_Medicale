<?php
require_once __DIR__ . '/../config/database.php';

class Inventory {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    public function addMovement($productId, $userId, $movementType, $quantity, $notes = '') {
        try {
            // Début de la transaction
            $this->db->beginTransaction();
            
            // Insérer le mouvement
            $query = "INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes) 
                     VALUES (:product_id, :user_id, :movement_type, :quantity, :notes)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':movement_type', $movementType);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':notes', $notes);
            $stmt->execute();
            
            // Mettre à jour la quantité du produit
            $productQuery = "SELECT quantity FROM products WHERE id = :id";
            $productStmt = $this->db->prepare($productQuery);
            $productStmt->bindParam(':id', $productId);
            $productStmt->execute();
            $currentQuantity = $productStmt->fetchColumn();
            
            if ($movementType === 'entrée') {
                $newQuantity = $currentQuantity + $quantity;
            } else {
                $newQuantity = $currentQuantity - $quantity;
                
                // Vérifier que la quantité ne devient pas négative
                if ($newQuantity < 0) {
                    $this->db->rollBack();
                    return false;
                }
            }
            
            $updateQuery = "UPDATE products SET quantity = :quantity WHERE id = :id";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bindParam(':quantity', $newQuantity);
            $updateStmt->bindParam(':id', $productId);
            $updateStmt->execute();
            
            // Valider la transaction
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->db->rollBack();
            return false;
        }
    }
    
    public function getMovements($limit = null) {
        $query = "SELECT m.*, p.name as product_name, u.username 
                 FROM inventory_movements m
                 JOIN products p ON m.product_id = p.id
                 JOIN users u ON m.user_id = u.id
                 ORDER BY m.date DESC";
                 
        if ($limit) {
            $query .= " LIMIT :limit";
        }
        
        $stmt = $this->db->prepare($query);
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getMovementsByProduct($productId) {
        $query = "SELECT m.*, p.name as product_name, u.username 
                 FROM inventory_movements m
                 JOIN products p ON m.product_id = p.id
                 JOIN users u ON m.user_id = u.id
                 WHERE m.product_id = :product_id
                 ORDER BY m.date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getStockStatistics() {
        // Nombre total de produits
        $totalProductsQuery = "SELECT COUNT(*) FROM products";
        $totalProductsStmt = $this->db->prepare($totalProductsQuery);
        $totalProductsStmt->execute();
        $totalProducts = $totalProductsStmt->fetchColumn();
        
        // Valeur totale du stock
        $totalValueQuery = "SELECT SUM(quantity * price) FROM products";
        $totalValueStmt = $this->db->prepare($totalValueQuery);
        $totalValueStmt->execute();
        $totalValue = $totalValueStmt->fetchColumn();
        
        // Produits en rupture de stock
        $outOfStockQuery = "SELECT COUNT(*) FROM products WHERE quantity = 0";
        $outOfStockStmt = $this->db->prepare($outOfStockQuery);
        $outOfStockStmt->execute();
        $outOfStock = $outOfStockStmt->fetchColumn();
        
        // Produits expirant dans les 30 jours
        $expiringQuery = "SELECT COUNT(*) FROM products WHERE expiration_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND expiration_date >= CURDATE()";
        $expiringStmt = $this->db->prepare($expiringQuery);
        $expiringStmt->execute();
        $expiring = $expiringStmt->fetchColumn();
        
        return [
            'total_products' => $totalProducts,
            'total_value' => $totalValue,
            'out_of_stock' => $outOfStock,
            'expiring_soon' => $expiring
        ];
    }
    
    public function getMovementsSummary($days = 30) {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));
        
        $query = "SELECT DATE(date) as day, 
                  SUM(CASE WHEN movement_type = 'entrée' THEN quantity ELSE 0 END) as total_in,
                  SUM(CASE WHEN movement_type = 'sortie' THEN quantity ELSE 0 END) as total_out
                  FROM inventory_movements
                  WHERE date >= :start_date
                  GROUP BY DATE(date)
                  ORDER BY DATE(date) ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getRecentMovements($limit = 10) {
        $query = "SELECT m.*, p.name as product_name, u.username 
                 FROM inventory_movements m
                 JOIN products p ON m.product_id = p.id
                 JOIN users u ON m.user_id = u.id
                 ORDER BY m.date DESC
                 LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
?>
<?php
session_start();
require_once __DIR__ . '/../models/Inventory.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Vérifier l'authentification
requireLogin();

// Action handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'add_movement') {
        handleAddMovement();
    }
}

// Redirect to inventory page if no action is specified
header("Location: /views/inventory/index.php");
exit();

function handleAddMovement() {
     // Verify permissions
    if (!hasRole(['admin', 'pharmacien', 'gestionnaire_stock'])) {
        $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour gérer les mouvements de stock";
        header("Location: /views/inventory/index.php");
        exit();
    }
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $movementType = isset($_POST['movement_type']) ? $_POST['movement_type'] : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    
    // Validation
    if ($productId <= 0) {
        $_SESSION['error'] = "Veuillez sélectionner un produit";
        header("Location: /views/inventory/add_movement.php");
        exit();
    }
    
    if ($movementType !== 'entrée' && $movementType !== 'sortie') {
        $_SESSION['error'] = "Type de mouvement invalide";
        header("Location: /views/inventory/add_movement.php");
        exit();
    }
    
    if ($quantity <= 0) {
        $_SESSION['error'] = "La quantité doit être supérieure à 0";
        header("Location: /views/inventory/add_movement.php");
        exit();
    }
    
    // Vérifier si le produit a assez de stock pour une sortie
    if ($movementType === 'sortie') {
        $productModel = new Product();
        $product = $productModel->getProductById($productId);
        
        if (!$product) {
            $_SESSION['error'] = "Produit introuvable";
            header("Location: /views/inventory/add_movement.php");
            exit();
        }
        
        if ($product['quantity'] < $quantity) {
            $_SESSION['error'] = "Stock insuffisant. Quantité disponible: {$product['quantity']}";
            header("Location: /views/inventory/add_movement.php");
            exit();
        }
    }
    
    // Add movement
    $inventoryModel = new Inventory();
    $result = $inventoryModel->addMovement($productId, $_SESSION['user_id'], $movementType, $quantity, $notes);
    
    if ($result) {
        $_SESSION['success'] = "Mouvement de stock enregistré avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de l'enregistrement du mouvement de stock";
    }
    
    header("Location: /views/inventory/index.php");
    exit();
}
?>
<?php
// controllers/product_controller.php
session_start();
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

// Vérifier l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/auth/login.php");
    exit();
}

// Function to check user role
function hasRole($allowedRoles) {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    
    return in_array($_SESSION['user_role'], $allowedRoles);
}

// Action handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'add') {
        handleAddProduct();
    } elseif ($action === 'update') {
        handleUpdateProduct();
    } elseif ($action === 'delete') {
        handleDeleteProduct();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'delete' && isset($_GET['id'])) {
        handleDeleteProduct($_GET['id']);
    }
}

// Redirect to products page if no action is specified
header("Location: /views/products/index.php");
exit();

function handleAddProduct() {
    // Verify permissions
    if (!hasRole(['admin', 'pharmacien'])) {
        $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour ajouter un produit";
        header("Location: /views/products/index.php");
        exit();
    }
    
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $categoryId = isset($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $expirationDate = isset($_POST['expiration_date']) ? $_POST['expiration_date'] : null;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    
    // Validation
    if (empty($name)) {
        $_SESSION['error'] = "Le nom du produit est obligatoire";
        header("Location: /views/products/add.php");
        exit();
    }
    
    if ($price <= 0) {
        $_SESSION['error'] = "Le prix doit être supérieur à 0";
        header("Location: /views/products/add.php");
        exit();
    }
    
    // Add product
    $productModel = new Product();
    $result = $productModel->addProduct($name, $description, $categoryId, $quantity, $expirationDate, $price);
    
    if ($result) {
        $_SESSION['success'] = "Produit ajouté avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de l'ajout du produit";
    }
    
    header("Location: /views/products/index.php");
    exit();
}

function handleUpdateProduct() {
    // Verify permissions
    if (!hasRole(['admin', 'pharmacien'])) {
        $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour modifier un produit";
        header("Location: /views/products/index.php");
        exit();
    }
    
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $categoryId = isset($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $expirationDate = isset($_POST['expiration_date']) ? $_POST['expiration_date'] : null;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    
    // Validation
    if ($id <= 0) {
        $_SESSION['error'] = "ID de produit invalide";
        header("Location: /views/products/index.php");
        exit();
    }
    
    if (empty($name)) {
        $_SESSION['error'] = "Le nom du produit est obligatoire";
        header("Location: /views/products/edit.php?id=$id");
        exit();
    }
    
    if ($price <= 0) {
        $_SESSION['error'] = "Le prix doit être supérieur à 0";
        header("Location: /views/products/edit.php?id=$id");
        exit();
    }
    
    // Update product
    $productModel = new Product();
    $result = $productModel->updateProduct($id, $name, $description, $categoryId, $quantity, $expirationDate, $price);
    
    if ($result) {
        $_SESSION['success'] = "Produit mis à jour avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour du produit";
    }
    
    header("Location: /views/products/index.php");
    exit();
}

function handleDeleteProduct($id = null) {
    // Verify permissions
    if (!hasRole(['admin', 'pharmacien'])) {
        $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour supprimer un produit";
        header("Location: /views/products/index.php");
        exit();
    }
    
    if ($id === null) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    } else {
        $id = intval($id);
    }
    
    if ($id <= 0) {
        $_SESSION['error'] = "ID de produit invalide";
        header("Location: /views/products/index.php");
        exit();
    }
    
    // Delete product
    $productModel = new Product();
    $result = $productModel->deleteProduct($id);
    
    if ($result) {
        $_SESSION['success'] = "Produit supprimé avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression du produit";
    }
    
    header("Location: /views/products/index.php");
    exit();
}
?>
<?php
session_start();
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Vérifier l'authentification
requireLogin();

// Action handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'add') {
        handleAddCategory();
    } elseif ($action === 'update') {
        handleUpdateCategory();
    } elseif ($action === 'delete') {
        handleDeleteCategory();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'delete' && isset($_GET['id'])) {
        handleDeleteCategory($_GET['id']);
    }
}

// Redirect to categories page if no action is specified
header("Location: /views/categories/index.php");
exit();

function handleAddCategory() {
    // Verify permissions
    if (!hasRole(['admin', 'pharmacien'])) {
        $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour ajouter une catégorie";
        header("Location: /views/categories/index.php");
        exit();
    }
    
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    // Validation
    if (empty($name)) {
        $_SESSION['error'] = "Le nom de la catégorie est obligatoire";
        header("Location: /views/categories/add.php");
        exit();
    }
    
    // Add category
    $categoryModel = new Category();
    $result = $categoryModel->addCategory($name, $description);
    
    if ($result) {
        $_SESSION['success'] = "Catégorie ajoutée avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de l'ajout de la catégorie";
    }
    
    header("Location: /views/categories/index.php");
    exit();
}

function handleUpdateCategory() {
    // Verify permissions
    if (!hasRole(['admin', 'pharmacien'])) {
        $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour modifier une catégorie";
        header("Location: /views/categories/index.php");
        exit();
    }
    
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    // Validation
    if ($id <= 0) {
        $_SESSION['error'] = "ID de catégorie invalide";
        header("Location: /views/categories/index.php");
        exit();
    }
    
    if (empty($name)) {
        $_SESSION['error'] = "Le nom de la catégorie est obligatoire";
        header("Location: /views/categories/edit.php?id=$id");
        exit();
    }
    
    // Update category
    $categoryModel = new Category();
    $result = $categoryModel->updateCategory($id, $name, $description);
    
    if ($result) {
        $_SESSION['success'] = "Catégorie mise à jour avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour de la catégorie";
    }
    
    header("Location: /views/categories/index.php");
    exit();
}

function handleDeleteCategory($id = null) {
    // Verify permissions
    if (!hasRole(['admin', 'pharmacien'])) {
        $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour supprimer une catégorie";
        header("Location: /views/categories/index.php");
        exit();
    }
    
    if ($id === null) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    } else {
        $id = intval($id);
    }
    
    if ($id <= 0) {
        $_SESSION['error'] = "ID de catégorie invalide";
        header("Location: /views/categories/index.php");
        exit();
    }
    
    // Check if category is in use
    $categoryModel = new Category();
    $productCount = $categoryModel->getCategoryProductCount($id);
    
    if ($productCount > 0) {
        $_SESSION['error'] = "Impossible de supprimer cette catégorie car elle est utilisée par $productCount produit(s)";
        header("Location: /views/categories/index.php");
        exit();
    }
    
    // Delete category
    $result = $categoryModel->deleteCategory($id);
    
    if ($result) {
        $_SESSION['success'] = "Catégorie supprimée avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression de la catégorie";
    }
    
    header("Location: /views/categories/index.php");
    exit();
}
?>
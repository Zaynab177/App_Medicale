<?php
// views/products/add.php
session_start();
$pageTitle = "Ajouter un produit";

// Vérifier l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/auth/login.php");
    exit();
}

// Vérifier les autorisations
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'pharmacien'])) {
    $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour accéder à cette page";
    header("Location: /views/products/index.php");
    exit();
}

require_once __DIR__ . '/../../models/Category.php';

// Récupérer toutes les catégories
$categoryModel = new Category();
$categories = $categoryModel->getAllCategories();

// Vérifier si le fichier header existe
$headerPath = __DIR__ . '/../../includes/header.php';
if (file_exists($headerPath)) {
    include_once $headerPath;
} else {
    // Header de secours si le fichier n'existe pas
    echo '<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . $pageTitle . ' - PharmaScan</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="/">PharmaScan</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="/">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/views/products/index.php">Produits</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <span class="nav-link">Bienvenue, ' . (isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Utilisateur') . '</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/controllers/auth_controller.php?action=logout">Déconnexion</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>';
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Ajouter un produit</h1>
        <a href="/views/products/index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informations du produit</h5>
        </div>
        <div class="card-body">
            <form action="/controllers/product_controller.php" method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nom du produit *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Catégorie</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">-- Sélectionnez une catégorie --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Quantité</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="0">
                    </div>
                    <div class="col-md-4">
                        <label for="expiration_date" class="form-label">Date d'expiration</label>
                        <input type="date" class="form-control" id="expiration_date" name="expiration_date">
                    </div>
                    <div class="col-md-4">
                        <label for="price" class="form-label">Prix (€) *</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0.01" required>
                            <span class="input-group-text">€</span>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
// Footer de secours si le fichier n'existe pas
$footerPath = __DIR__ . '/../../includes/footer.php';
if (file_exists($footerPath)) {
    include_once $footerPath;
} else {
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>';
}
?>
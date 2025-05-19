<?php
// views/products/index.php
session_start();
$pageTitle = "Gestion des produits";

// Vérifier l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/auth/login.php");
    exit();
}

require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Category.php';

// Initialiser le modèle de produit
$productModel = new Product();
$categoryModel = new Category();

// Récupérer les paramètres de recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

// Récupérer tous les produits ou effectuer une recherche
if (!empty($search)) {
    $products = $productModel->searchProducts($search, $categoryId);
} elseif ($categoryId) {
    $products = $productModel->getProductsByCategory($categoryId);
} else {
    $products = $productModel->getAllProducts();
}

// Récupérer toutes les catégories pour le filtre
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
                            <a class="nav-link active" href="/views/products/index.php">Produits</a>
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
        <h1>Gestion des produits</h1>
        <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'pharmacien'])): ?>
        <a href="/views/products/add.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Ajouter un produit
        </a>
        <?php endif; ?>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Recherche et filtrage</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="/views/products/index.php" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Rechercher un produit...">
                </div>
                <div class="col-md-4">
                    <label for="category_id" class="form-label">Catégorie</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo ($categoryId == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Liste des produits</h5>
        </div>
        <div class="card-body">
            <?php if (count($products) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Quantité</th>
                                <th>Date d'expiration</th>
                                <th>Prix</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'Non catégorisé'); ?></td>
                                    <td>
                                        <?php if ($product['quantity'] <= 10): ?>
                                            <span class="badge bg-danger"><?php echo $product['quantity']; ?></span>
                                        <?php else: ?>
                                            <?php echo $product['quantity']; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($product['expiration_date'])): ?>
                                            <?php 
                                                $expDate = new DateTime($product['expiration_date']);
                                                $today = new DateTime();
                                                $diff = $today->diff($expDate);
                                                $daysRemaining = $expDate > $today ? $diff->days : -$diff->days;
                                                
                                                if ($daysRemaining < 0) {
                                                    echo '<span class="badge bg-danger">Expiré</span> ';
                                                } elseif ($daysRemaining < 30) {
                                                    echo '<span class="badge bg-warning">Expire bientôt</span> ';
                                                }
                                                
                                                echo $expDate->format('d/m/Y');
                                            ?>
                                        <?php else: ?>
                                            Non défini
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo number_format($product['price'], 2, ',', ' '); ?> €</td>
                                    <td>
                                       
                                        <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'pharmacien'])): ?>
                                            <a href="/views/products/edit.php?id=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $product['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            
                                            <!-- Modal de confirmation de suppression -->
                                            <div class="modal fade" id="deleteModal<?php echo $product['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirmation de suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer le produit <strong><?php echo htmlspecialchars($product['name']); ?></strong> ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <a href="/controllers/product_controller.php?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-danger">Supprimer</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    Aucun produit trouvé.
                </div>
            <?php endif; ?>
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
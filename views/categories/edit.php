<?php
// views/categories/edit.php
session_start();
$pageTitle = "Modifier une catégorie";

// Vérifier l'authentification
require_once __DIR__ . '/../../includes/auth_check.php';
requireLogin();

// Vérifier les autorisations
if (!hasRole(['admin', 'pharmacien'])) {
    $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour accéder à cette page";
    header("Location: /views/categories/index.php");
    exit();
}

// Vérifier si l'ID de la catégorie est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID de catégorie non spécifié";
    header("Location: /views/categories/index.php");
    exit();
}

$categoryId = intval($_GET['id']);

require_once __DIR__ . '/../../models/Category.php';

// Récupérer les détails de la catégorie
$categoryModel = new Category();
$category = $categoryModel->getCategoryById($categoryId);

// Vérifier si la catégorie existe
if (!$category) {
    $_SESSION['error'] = "Catégorie non trouvée";
    header("Location: /views/categories/index.php");
    exit();
}

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
                <a class="navbar-brand" href="/pharmascan/">PharmaScan</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="/pharmascan/">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pharmascan/views/products/index.php">Produits</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/pharmascan/views/categories/index.php">Catégories</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <span class="nav-link">Bienvenue, ' . (isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Utilisateur') . '</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pharmascan/controllers/auth_controller.php?action=logout">Déconnexion</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>';
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Modifier une catégorie</h1>
        <a href="/views/categories/index.php" class="btn btn-secondary">
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
            <h5 class="mb-0">Informations de la catégorie</h5>
        </div>
        <div class="card-body">
            <form action="/controllers/category_controller.php" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nom de la catégorie *</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                    <div class="form-text">Le nom de la catégorie doit être unique.</div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
                    <div class="form-text">Une brève description de la catégorie (facultatif).</div>
                </div>
                
                <?php
                // Afficher le nombre de produits associés à cette catégorie
                $productCount = $categoryModel->getCategoryProductCount($category['id']);
                if ($productCount > 0):
                ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Cette catégorie est actuellement associée à 
                    <strong><?php echo $productCount; ?></strong> produit(s).
                </div>
                <?php endif; ?>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/views/categories/index.php" class="btn btn-secondary me-md-2">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour
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
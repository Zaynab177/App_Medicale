<?php
// views/categories/add.php
session_start();
$pageTitle = "Ajouter une catégorie";

// Vérifier l'authentification
require_once __DIR__ . '/../../includes/auth_check.php';
requireLogin();

// Vérifier les autorisations
if (!hasRole(['admin', 'pharmacien'])) {
    $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour accéder à cette page";
    header("Location: /pharmascan/views/categories/index.php");
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
        <h1>Ajouter une catégorie</h1>
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
                <input type="hidden" name="action" value="add">
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nom de la catégorie *</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <div class="form-text">Le nom de la catégorie doit être unique.</div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    <div class="form-text">Une brève description de la catégorie (facultatif).</div>
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
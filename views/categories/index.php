<?php
// views/categories/index.php
session_start();
$pageTitle = "Gestion des catégories";

// Vérifier l'authentification
require_once __DIR__ . '/../../includes/auth_check.php';
requireLogin();

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
        <h1>Gestion des catégories</h1>
        <?php if (hasRole(['admin', 'pharmacien'])): ?>
        <a href="/views/categories/add.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Ajouter une catégorie
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
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Liste des catégories</h5>
        </div>
        <div class="card-body">
            <?php if (empty($categories)): ?>
                <div class="alert alert-info">
                    Aucune catégorie trouvée.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Nombre de produits</th>
                                <?php if (hasRole(['admin', 'pharmacien'])): ?>
                                <th class="text-center">Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td>
                                        <?php 
                                            echo !empty($category['description']) 
                                                ? htmlspecialchars($category['description']) 
                                                : '<em class="text-muted">Aucune description</em>';
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $productCount = $categoryModel->getCategoryProductCount($category['id']);
                                            echo $productCount;
                                        ?>
                                    </td>
                                    <?php if (hasRole(['admin', 'pharmacien'])): ?>
                                    <td class="text-center">
                                        <a href="/views/categories/edit.php?id=<?php echo $category['id']; ?>" 
                                           class="btn btn-sm btn-primary me-2" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($productCount == 0): ?>
                                        <button type="button" class="btn btn-sm btn-danger" title="Supprimer"
                                                onclick="confirmDelete(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars(addslashes($category['name'])); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-danger" disabled title="Cette catégorie est utilisée par des produits">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la catégorie <strong id="categoryName"></strong> ?</p>
                <p class="text-danger mb-0">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" action="/controllers/category_controller.php" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" id="categoryId" name="id" value="">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, name) {
        document.getElementById('categoryName').textContent = name;
        document.getElementById('categoryId').value = id;
        
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>

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
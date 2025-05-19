<?php
// views/users/edit.php
session_start();
$pageTitle = "Modifier un utilisateur";

// Vérifier l'authentification
require_once __DIR__ . '/../../includes/auth_check.php';
requireLogin();

// Vérifier les autorisations (seul l'admin peut accéder à cette page)
if (!hasRole(['admin'])) {
    $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour accéder à cette page";
    header("Location: /");
    exit();
}

// Vérifier si l'ID de l'utilisateur est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID d'utilisateur non spécifié";
    header("Location: /views/users/index.php");
    exit();
}

$userId = intval($_GET['id']);

require_once __DIR__ . '/../../models/User.php';

// Récupérer les détails de l'utilisateur
$userModel = new User();
$user = $userModel->getUserById($userId);

// Vérifier si l'utilisateur existe
if (!$user) {
    $_SESSION['error'] = "Utilisateur non trouvé";
    header("Location: /views/users/index.php");
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
                            <a class="nav-link" href="/pharmascan/views/categories/index.php">Catégories</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/pharmascan/views/users/index.php">Utilisateurs</a>
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
        <h1>Modifier un utilisateur</h1>
        <a href="/views/users/index.php" class="btn btn-secondary">
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
            <h5 class="mb-0">Informations de l'utilisateur</h5>
        </div>
        <div class="card-body">
            <form action="/controllers/user_controller.php" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                
                <div class="mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur *</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required minlength="3">
                    <div class="form-text">Le nom d'utilisateur doit être unique et contenir au moins 3 caractères.</div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    <div class="form-text">L'adresse email doit être unique et valide.</div>
                </div>
                
                <div class="mb-3">
                    <label for="role" class="form-label">Rôle *</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Administrateur</option>
                        <option value="pharmacien" <?php echo ($user['role'] === 'pharmacien') ? 'selected' : ''; ?>>Pharmacien</option>
                        <option value="technicien" <?php echo ($user['role'] === 'technicien') ? 'selected' : ''; ?>>Technicien</option>
                    </select>
                    <div class="form-text">
                        <strong>Administrateur</strong>: Accès complet à toutes les fonctionnalités<br>
                        <strong>Pharmacien</strong>: Accès à la gestion des produits et des catégories<br>
                        <strong>Technicien</strong>: Accès limité à la consultation des produits
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Date de création</label>
                    <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>" readonly>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/views/users/index.php" class="btn btn-secondary me-md-2">
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
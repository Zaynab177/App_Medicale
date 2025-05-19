<?php 
// index.php - Page d'accueil
session_start();
$pageTitle = "Accueil";

// Redirection si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/auth/login.php");
    exit();
}

// Inclusion du header
$headerPath = __DIR__ . '/includes/header.php';
if (file_exists($headerPath)) {
    include_once $headerPath;
} else {
    echo '<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . $pageTitle . ' - PharmaScan</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="/">PharmaScan</a>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <span class="nav-link">Bienvenue, ' . htmlspecialchars($_SESSION['username']) . '</span>
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

<div class="container mt-5">
    <h3 class="mb-4">Bonjour <?php echo htmlspecialchars($_SESSION['username']); ?> !</h3>
    <p class="mb-4">Vous êtes connecté en tant que <strong><?php echo htmlspecialchars($_SESSION['user_role']); ?></strong>.</p>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Authentification (réservée aux admins) -->
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <div class="col-md-6">
                <div class="card h-100 border-primary">
                    <div class="card-header bg-primary text-white">Authentification</div>
                    <div class="card-body">
                        <p>Gérez l'inscription, la connexion et les rôles des utilisateurs.</p>
                        <a href="/views/users/index.php" class="btn btn-outline-primary">Gestion des utilisateurs</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Produits -->
        <div class="col-md-6">
            <div class="card h-100 border-success">
                <div class="card-header bg-success text-white">Gestion des produits</div>
                <div class="card-body">
                    <p>Ajoutez, modifiez ou supprimez des produits. Recherchez par catégorie ou suivez les dates d'expiration.</p>
                    <a href="/views/products/index.php" class="btn btn-outline-success">Voir les produits</a>
                </div>
            </div>
        </div>

        <!-- Stock -->
        <div class="col-md-6">
            <div class="card h-100 border-warning">
                <div class="card-header bg-warning text-dark">Gestion du stock</div>
                <div class="card-body">
                    <p>Consultez les entrées/sorties, l'historique des mouvements et les statistiques globales.</p>
                    <a href="/views/inventory/index.php" class="btn btn-outline-warning">Voir le tableau de bord</a>
                </div>
            </div>
        </div>

        <!-- Catégories -->
        <div class="col-md-6">
            <div class="card h-100 border-info">
                <div class="card-header bg-info text-white">Gestion des catégories</div>
                <div class="card-body">
                    <p>Créez, modifiez ou supprimez les catégories de produits.</p>
                    <a href="/views/categories/index.php" class="btn btn-outline-info">Gérer les catégories</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Inclusion du footer
$footerPath = __DIR__ . '/includes/footer.php';
if (file_exists($footerPath)) {
    include_once $footerPath;
} else {
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>';
}
?>

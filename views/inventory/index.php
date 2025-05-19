<?php
session_start();
require_once __DIR__ . '/../../models/Inventory.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../includes/auth_check.php';

// Vérifier l'authentification
requireLogin();

// Initialiser le modèle d'inventaire
$inventoryModel = new Inventory();

// Obtenir les statistiques du stock
$statistics = $inventoryModel->getStockStatistics();

// Obtenir les mouvements récents
$recentMovements = $inventoryModel->getRecentMovements(15);

// Obtenir le résumé des mouvements des 30 derniers jours
$movementsSummary = $inventoryModel->getMovementsSummary(30);

// Titre de la page
$pageTitle = "Gestion de l'inventaire";

// Inclure l'en-tête
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?php echo $pageTitle; ?></h1>
    
    <?php include __DIR__ . '/../../includes/alerts.php'; ?>
    
    <!-- Boutons d'action -->
    <div class="mb-4">
        <?php if (hasRole(['admin', 'pharmacien', 'gestionnaire_stock'])): ?>
        <a href="/views/inventory/add_movement.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un mouvement
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h4><?php echo number_format($statistics['total_products']); ?></h4>
                    <div>Produits en stock</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <h4><?php echo number_format($statistics['total_value'], 2); ?> €</h4>
                    <div>Valeur totale du stock</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <h4><?php echo number_format($statistics['expiring_soon']); ?></h4>
                    <div>Produits expirant bientôt</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <h4><?php echo number_format($statistics['out_of_stock']); ?></h4>
                    <div>Produits en rupture</div>
                </div>
            </div>
        </div>
    </div>
    
    
    
    <!-- Tableau des mouvements récents -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Mouvements récents
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="recentMovementsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Produit</th>
                                    <th>Type</th>
                                    <th>Quantité</th>
                                    <th>Utilisateur</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentMovements as $movement): ?>
                                <tr class="<?php echo $movement['movement_type'] === 'entrée' ? 'table-success' : 'table-danger'; ?>">
                                    <td><?php echo date('d/m/Y H:i', strtotime($movement['date'])); ?></td>
                                    <td><?php echo htmlspecialchars($movement['product_name']); ?></td>
                                    <td><?php echo $movement['movement_type'] === 'entrée' ? 'Entrée' : 'Sortie'; ?></td>
                                    <td><?php echo number_format($movement['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($movement['username']); ?></td>
                                    <td><?php echo htmlspecialchars($movement['notes']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Préparer les données pour le graphique
const movementData = <?php echo json_encode($movementsSummary); ?>;
const dates = movementData.map(item => item.day);
const entriesData = movementData.map(item => item.total_in);
const exitsData = movementData.map(item => item.total_out);

// Initialiser le graphique
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('inventoryMovementsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: 'Entrées',
                    data: entriesData,
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Sorties',
                    data: exitsData,
                    borderColor: 'rgba(220, 53, 69, 1)',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantité'
                    }
                }
            }
        }
    });
});

// Initialiser le tableau de données
$(document).ready(function() {
    $('#recentMovementsTable').DataTable({
        language: {
            url: '/assets/js/dataTables.french.json'
        },
        order: [[0, 'desc']]
    });
});
</script>

<?php
// Inclure le pied de page
require_once __DIR__ . '/../../includes/footer.php';
?>
<?php
session_start();
require_once __DIR__ . '/../../models/Inventory.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../includes/auth_check.php';

// Vérifier l'authentification
requireLogin();

// Vérifier les autorisations
if (!hasRole(['admin', 'pharmacien', 'gestionnaire_stock'])) {
    $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour gérer les mouvements de stock";
    header("Location: /views/inventory/index.php");
    exit();
}

// Récupérer la liste des produits
$productModel = new Product();
$productsList = $productModel->getAllProducts();

// Titre de la page
$pageTitle = "Ajouter un mouvement d'inventaire";

// Inclure l'en-tête
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?php echo $pageTitle; ?></h1>
    
    <?php include __DIR__ . '/../../includes/alerts.php'; ?>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-warehouse me-1"></i>
            Formulaire d'ajout de mouvement
        </div>
        <div class="card-body">
            <form action="/controllers/inventory_controller.php" method="post" id="addMovementForm" class="needs-validation" novalidate>
                <input type="hidden" name="action" value="add_movement">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="product_id" class="form-label">Produit</label>
                        <select class="form-select" id="product_id" name="product_id" required>
                            <option value="">Sélectionner un produit</option>
                            <?php foreach ($productsList as $product): ?>
                            <option value="<?php echo $product['id']; ?>" data-quantity="<?php echo $product['quantity']; ?>">
                                <?php echo htmlspecialchars($product['name']); ?> 
                                (Stock: <?php echo $product['quantity']; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner un produit
                        </div>
                    </div>
                    <div class="col-md-6" id="stockInfoDiv">
                        <div class="alert alert-info" id="currentStockInfo">
                            <strong>Stock actuel :</strong> <span id="currentStockValue">0</span>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="movement_type" class="form-label">Type de mouvement</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="movement_type" id="movement_type_in" value="entrée" checked>
                            <label class="form-check-label" for="movement_type_in">
                                <i class="fas fa-arrow-down text-success"></i> Entrée
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="movement_type" id="movement_type_out" value="sortie">
                            <label class="form-check-label" for="movement_type_out">
                                <i class="fas fa-arrow-up text-danger"></i> Sortie
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="quantity" class="form-label">Quantité</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                        <div class="invalid-feedback">
                            Veuillez entrer une quantité valide (supérieure à 0)
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                
                <div class="alert alert-danger" id="insufficientStockAlert" style="display: none;">
                    <strong>Attention !</strong> La quantité demandée dépasse le stock disponible.
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="/views/inventory/index.php" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Enregistrer le mouvement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const movementTypeIn = document.getElementById('movement_type_in');
    const movementTypeOut = document.getElementById('movement_type_out');
    const currentStockValue = document.getElementById('currentStockValue');
    const insufficientStockAlert = document.getElementById('insufficientStockAlert');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('addMovementForm');
    
    // Fonction de validation du formulaire
    function validateForm() {
        const productId = productSelect.value;
        const quantity = parseInt(quantityInput.value) || 0;
        const currentStock = getCurrentStock();
        const isOutMovement = movementTypeOut.checked;
        
        if (productId && quantity > 0 && isOutMovement && quantity > currentStock) {
            insufficientStockAlert.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        } else {
            insufficientStockAlert.style.display = 'none';
            submitBtn.disabled = false;
            return true;
        }
    }
    
    // Obtenir le stock actuel du produit sélectionné
    function getCurrentStock() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        return selectedOption ? parseInt(selectedOption.dataset.quantity) || 0 : 0;
    }
    
    // Mettre à jour l'affichage du stock
    function updateStockDisplay() {
        const currentStock = getCurrentStock();
        currentStockValue.textContent = currentStock;
    }
    
    // Événements pour la validation
    productSelect.addEventListener('change', function() {
        updateStockDisplay();
        validateForm();
    });
    
    quantityInput.addEventListener('input', validateForm);
    
    movementTypeIn.addEventListener('change', validateForm);
    movementTypeOut.addEventListener('change', validateForm);
    
    // Validation du formulaire avant soumission
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity() || !validateForm()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        form.classList.add('was-validated');
    });
    
    // Bootstrap validation
    Array.from(form.elements).forEach(input => {
        input.addEventListener('input', function() {
            if (input.checkValidity()) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }
        });
    });
});
</script>

<?php
// Inclure le pied de page
require_once __DIR__ . '/../../includes/footer.php';
?>
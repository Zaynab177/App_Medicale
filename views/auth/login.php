<?php
// views/auth/login.php
session_start();
$pageTitle = "Connexion";
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root {
                --primary-color: #3498db;
                --primary-hover: #2980b9;
                --secondary-color:rgb(12, 14, 16);
                --text-color: #333;
            }
            
            body {
               background-color:rgb(16, 23, 32);
                font-family: "Segoe UI", Roboto, "Helvetica Neue", sans-serif;
                color: var(--text-color);
            }
            
            .login-card {
                border: none;
                border-radius: 12px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                transition: transform 0.3s;
            }
            
            .login-card:hover {
                transform: translateY(-5px);
            }
            
            .card-header {
                background-image: linear-gradient(135deg,rgb(8, 64, 101), #2980b9);
                padding: 1.5rem;
                border-bottom: none;
            }
            
            .card-body {
                padding: 2rem;
            }
            
            .form-control {
                border-radius: 8px;
                padding: 12px;
                border: 1px solid #e1e5eb;
                background-color: #f8f9fa;
                transition: all 0.3s;
            }
            
            .form-control:focus {
                box-shadow: 0 0 0 3px rgba(86, 165, 208, 0.79);
                border-color: var(--primary-color);
                background-color: #fff;
            }
            
            .btn-primary {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
                border-radius: 8px;
                padding: 12px;
                font-weight: 600;
                transition: all 0.3s;
            }
            
            .btn-primary:hover {
                background-color: var(--primary-hover);
                border-color: var(--primary-hover);
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            
            .input-group-text {
                background-color: #f8f9fa;
                border: 1px solid #e1e5eb;
                border-radius: 8px 0 0 8px;
            }
            
            .card-footer {
                background-color: #fff;
                border-top: 1px solid #f1f1f1;
                padding: 1.5rem;
            }
            
            .card-footer a {
                color: var(--primary-color);
                font-weight: 600;
                text-decoration: none;
                transition: color 0.3s;
            }
            
            .card-footer a:hover {
                color: var(--primary-hover);
                text-decoration: underline;
            }
            
            .logo-container {
                text-align: center;
                margin-bottom: 1.5rem;
            }
            
            .logo {
                max-width: 80px;
                margin-bottom: 1rem;
            }
            
            .alert {
                border-radius: 8px;
                border: none;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }
            
            .toggle-password {
                cursor: pointer;
                color: #6c757d;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark" style="background-image: linear-gradient(135deg, #3498db, #2980b9);">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/">
                    <i class="fas fa-clinic-medical me-2"></i>PharmaScan
                </a>
            </div>
        </nav>';
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="login-card card">
                <div class="card-header text-white text-center">
                    <div class="logo-container">
                        <i class="fas fa-clinic-medical fa-3x mb-3"></i>
                    </div>
                    <h3 class="mb-0 fw-bold">Bienvenue sur PharmaScan</h3>
                    <p class="mb-0 mt-2 opacity-75">Connectez-vous à votre compte</p>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php 
                                echo $_SESSION['error']; 
                                unset($_SESSION['error']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php 
                                echo $_SESSION['success']; 
                                unset($_SESSION['success']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="/controllers/auth_controller.php" method="POST">
                        <input type="hidden" name="action" value="login">
                        
                        <div class="mb-4">
                            <label for="username" class="form-label fw-medium">
                                <i class="fas fa-user me-2 text-primary"></i>Nom d'utilisateur
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" 
                                       placeholder="Entrez votre nom d'utilisateur" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label fw-medium">
                                <i class="fas fa-lock me-2 text-primary"></i>Mot de passe
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Entrez votre mot de passe" required>
                                <span class="input-group-text toggle-password" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Se souvenir de moi</label>
                            <a href="/views/auth/forgot_password.php" class="float-end text-decoration-none">
                                Mot de passe oublié?
                            </a>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">Vous n'avez pas de compte? 
                        <a href="/views/auth/register.php" class="ms-1">
                            <i class="fas fa-user-plus me-1"></i>Inscrivez-vous
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>

<?php 
// Footer de secours si le fichier n'existe pas
$footerPath = __DIR__ . '/../../includes/footer.php';
if (file_exists($footerPath)) {
    include_once $footerPath;
} else {
    echo '<footer class="mt-5 py-4 text-center text-muted">
        <div class="container">
            <p>&copy; ' . date("Y") . ' PharmaScan. Tous droits réservés.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>';
}
?>
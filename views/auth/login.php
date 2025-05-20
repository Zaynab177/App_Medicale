<?php
// views/auth/login.php
session_start();
$pageTitle = "Connexion";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - PharmaScan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --secondary-color: #f8f9fa;
            --accent-color: #48cae4;
            --text-color: #333;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background: linear-gradient(135deg, #f6f8fc, #e9ecef);
            font-family: 'Poppins', 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            background: #fff;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }
        
        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
        }
        
        .login-container {
            display: flex;
            align-items: stretch;
            border-radius: 16px;
            overflow: hidden;
            background: white;
            box-shadow: var(--card-shadow);
        }
        
        .login-image {
            display: none;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-form {
            padding: 3rem;
            background: white;
        }
        
        .login-form h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .login-form p.subtitle {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            background-color: #f8f9fa;
            transition: all 0.3s;
            margin-bottom: 1.5rem;
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
            border-color: var(--primary-color);
            background-color: #fff;
        }
        
        .password-container {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
            display: block;
            width: 100%;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .form-check {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            align-items: center;
        }
        
        .form-check-label {
            margin-left: 0.5rem;
            color: #6c757d;
        }
        
        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .forgot-password:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: #6c757d;
        }
        
        .divider::before, .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .divider span {
            padding: 0 10px;
        }
        
        .social-login {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            color: #6c757d;
            transition: all 0.3s;
        }
        
        .social-btn:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .register-link a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }
        
        .footer {
            margin-top: auto;
            background: #fff;
            box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.05);
            padding: 1.5rem 0;
            text-align: center;
            color: #6c757d;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .alert-danger {
            background-color: #ffe9e9;
            color: #d62828;
        }
        
        .alert-success {
            background-color: #e3f9e5;
            color: #38b000;
        }
        
        /* Responsive Design */
        @media (min-width: 992px) {
            .login-image {
                display: flex;
                flex: 1;
            }
            
            .login-form {
                flex: 1;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-capsules me-2"></i>PharmaScan
            </a>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="login-container">
                        <div class="login-image">
                            <div>
                                <h2 class="mb-4 fw-bold">Bienvenue sur PharmaScan</h2>
                                <p class="mb-4">La solution complète pour la gestion de votre pharmacie. Accédez à votre espace et gérez vos médicaments en toute simplicité.</p>
                                <div class="d-flex align-items-center mb-4">
                                    <i class="fas fa-check-circle me-3" style="font-size: 1.5rem;"></i>
                                    <div>Gestion d'inventaire avancée</div>
                                </div>
                                <div class="d-flex align-items-center mb-4">
                                    <i class="fas fa-check-circle me-3" style="font-size: 1.5rem;"></i>
                                    <div>Suivi des ordonnances facilité</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle me-3" style="font-size: 1.5rem;"></i>
                                    <div>Analyse des ventes et rapports</div>
                                </div>
                            </div>
                        </div>
                        <div class="login-form">
                            <h2>Connexion</h2>
                            <p class="subtitle">Connectez-vous pour accéder à votre tableau de bord</p>

                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php 
                                        echo $_SESSION['error']; 
                                        unset($_SESSION['error']);
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <?php 
                                        echo $_SESSION['success']; 
                                        unset($_SESSION['success']);
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <form action="/controllers/auth_controller.php" method="POST">
                                <input type="hidden" name="action" value="login">
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label fw-medium">Nom d'utilisateur</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           placeholder="Entrez votre nom d'utilisateur" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-medium">Mot de passe</label>
                                    <div class="password-container">
                                        <input type="password" class="form-control" id="password" name="password" 
                                               placeholder="Entrez votre mot de passe" required>
                                        <span class="toggle-password" onclick="togglePassword()">
                                            <i class="fas fa-eye" id="toggleIcon"></i>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="form-check">
                                    <div>
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">Se souvenir de moi</label>
                                    </div>
                                    
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                                </button>
                            </form>
                            
                            <div class="divider">
                                <span>Ou</span>
                            </div>
                            
                            <div class="social-login">
                                <a href="#" class="social-btn">
                                    <i class="fab fa-google"></i>
                                </a>
                                <a href="#" class="social-btn">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-btn">
                                    <i class="fab fa-apple"></i>
                                </a>
                            </div>
                            
                            <div class="register-link">
                                <p>Vous n'avez pas de compte? 
                                    <a href="/views/auth/register.php">
                                        Inscrivez-vous
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date("Y"); ?> PharmaScan. Tous droits réservés.</p>
        </div>
    </footer>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
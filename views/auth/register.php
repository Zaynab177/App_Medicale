<?php
// views/auth/register.php
session_start();
$pageTitle = "Inscription";
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
            padding: 3rem 0;
        }
        
        .register-container {
            display: flex;
            align-items: stretch;
            border-radius: 16px;
            overflow: hidden;
            background: white;
            box-shadow: var(--card-shadow);
        }
        
        .register-image {
            display: none;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .register-form {
            padding: 3rem;
            background: white;
            flex: 1;
        }
        
        .register-form h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .register-form p.subtitle {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            background-color: #f8f9fa;
            transition: all 0.3s;
            margin-bottom: 1rem;
        }
        
        .form-control:focus, .form-select:focus {
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
        
        .form-text {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: -0.5rem;
            margin-bottom: 1rem;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .login-link a:hover {
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
        
        .progress-container {
            margin-bottom: 2rem;
        }
        
        .steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        
        .step {
            text-align: center;
            position: relative;
            flex: 1;
        }
        
        .step-number {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin: 0 auto 0.5rem;
            position: relative;
            z-index: 2;
            transition: all 0.3s;
        }
        
        .step.active .step-number {
            background-color: var(--primary-color);
            color: white;
        }
        
        .step-label {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .progress-line {
            height: 3px;
            background-color: #e9ecef;
            position: relative;
            margin-bottom: 2rem;
            margin-top: -20px;
        }
        
        .progress-line::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 33.33%;
            background-color: var(--primary-color);
            transition: width 0.3s;
        }
        
        .strength-meter {
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
            overflow: hidden;
        }
        
        .strength-meter-fill {
            height: 100%;
            width: 0;
            transition: width 0.3s, background-color 0.3s;
        }
        
        .strength-text {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        /* Responsive Design */
        @media (min-width: 992px) {
            .register-image {
                display: flex;
                flex: 1;
            }
            
            .register-form {
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
                    <div class="register-container">
                        <div class="register-image">
                            <div>
                                <h2 class="mb-4 fw-bold">Rejoignez PharmaScan</h2>
                                <p class="mb-4">Créez votre compte pour accéder à toutes les fonctionnalités de gestion de pharmacie. Une solution complète pour les professionnels de la santé.</p>
                                
                                <div class="d-flex align-items-center mb-4">
                                    <i class="fas fa-shield-alt me-3" style="font-size: 1.5rem;"></i>
                                    <div>Sécurité des données garantie</div>
                                </div>
                                <div class="d-flex align-items-center mb-4">
                                    <i class="fas fa-user-md me-3" style="font-size: 1.5rem;"></i>
                                    <div>Adapté aux besoins des pharmaciens</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-tachometer-alt me-3" style="font-size: 1.5rem;"></i>
                                    <div>Interface intuitive et performante</div>
                                </div>
                            </div>
                        </div>
                        <div class="register-form">
                            <h2>Créer un compte</h2>
                            <p class="subtitle">Commencez dès aujourd'hui avec PharmaScan</p>

                            <div class="progress-container">
                                <div class="steps">
                                    <div class="step active">
                                        <div class="step-number">1</div>
                                        <div class="step-label">Informations</div>
                                    </div>
                                    <div class="step">
                                        <div class="step-number">2</div>
                                        <div class="step-label">Vérification</div>
                                    </div>
                                    <div class="step">
                                        <div class="step-number">3</div>
                                        <div class="step-label">Finalisation</div>
                                    </div>
                                </div>
                                <div class="progress-line"></div>
                            </div>

                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php 
                                        echo $_SESSION['error']; 
                                        unset($_SESSION['error']);
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <form action="/controllers/auth_controller.php" method="POST">
                                <input type="hidden" name="action" value="register">
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label fw-medium">Nom d'utilisateur</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           placeholder="Choisissez un nom d'utilisateur" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-medium">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="Entrez votre adresse email" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-medium">Mot de passe</label>
                                    <div class="password-container">
                                        <input type="password" class="form-control" id="password" name="password" 
                                               placeholder="Créez un mot de passe sécurisé" required minlength="6"
                                               oninput="checkPasswordStrength(this.value)">
                                        <span class="toggle-password" onclick="togglePassword('password', 'toggleIcon1')">
                                            <i class="fas fa-eye" id="toggleIcon1"></i>
                                        </span>
                                    </div>
                                    <div class="strength-meter mt-2">
                                        <div class="strength-meter-fill" id="strength-meter-fill"></div>
                                    </div>
                                    <div class="strength-text">
                                        <span>Faible</span>
                                        <span>Moyen</span>
                                        <span>Fort</span>
                                    </div>
                                    <div class="form-text">Le mot de passe doit contenir au moins 6 caractères.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label fw-medium">Confirmer le mot de passe</label>
                                    <div class="password-container">
                                        <input type="password" class="form-control" id="confirm_password" 
                                               name="confirm_password" placeholder="Confirmez votre mot de passe" required
                                               oninput="checkPasswordMatch()">
                                        <span class="toggle-password" onclick="togglePassword('confirm_password', 'toggleIcon2')">
                                            <i class="fas fa-eye" id="toggleIcon2"></i>
                                        </span>
                                    </div>
                                    <div id="password-match-message" class="form-text"></div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="role" class="form-label fw-medium">Rôle</label>
                                    <select class="form-select" id="role" name="role">
                                        <option value="technicien" selected>Technicien</option>
                                        <option value="pharmacien">Pharmacien</option>
                                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                        <option value="admin">Administrateur</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>S'inscrire
                                </button>
                            </form>
                            
                            <div class="login-link">
                                <p>Vous avez déjà un compte? 
                                    <a href="/views/auth/login.php">
                                        <i class="fas fa-sign-in-alt me-1"></i>Connectez-vous
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
    function togglePassword(fieldId, iconId) {
        const passwordField = document.getElementById(fieldId);
        const toggleIcon = document.getElementById(iconId);
        
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
    
    function checkPasswordStrength(password) {
        const meter = document.getElementById('strength-meter-fill');
        let strength = 0;
        
        // Vérifier la longueur
        if (password.length >= 6) strength += 1;
        if (password.length >= 10) strength += 1;
        
        // Vérifier la présence de différents types de caractères
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;
        
        // Appliquer le résultat au mètre
        const percentage = (strength / 5) * 100;
        meter.style.width = percentage + '%';
        
        // Changer la couleur selon la force
        if (percentage < 30) {
            meter.style.backgroundColor = '#dc3545'; // Rouge
        } else if (percentage < 70) {
            meter.style.backgroundColor = '#ffc107'; // Jaune
        } else {
            meter.style.backgroundColor = '#28a745'; // Vert
        }
    }
    
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const message = document.getElementById('password-match-message');
        
        if (confirmPassword === '') {
            message.textContent = '';
            return;
        }
        
        if (password === confirmPassword) {
            message.textContent = 'Les mots de passe correspondent.';
            message.style.color = '#28a745';
        } else {
            message.textContent = 'Les mots de passe ne correspondent pas.';
            message.style.color = '#dc3545';
        }
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
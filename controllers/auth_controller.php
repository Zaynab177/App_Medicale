<?php
// controllers/auth_controller.php
session_start();
require_once __DIR__ . '/../models/User.php';

// Action handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'login') {
        handleLogin();
    } elseif ($action === 'register') {
        handleRegister();
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'logout') {
    handleLogout();
}

// Redirect to home page if no action is specified
header("Location: /index.php");
exit();

function handleLogin() {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires";
        header("Location: /views/auth/login.php");
        exit();
    }
    
    $userModel = new User();
    $user = $userModel->login($username, $password);
    
    if ($user) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['success'] = "Connexion réussie";
        
        header("Location: /index.php");
        exit();
    } else {
        $_SESSION['error'] = "Nom d'utilisateur ou mot de passe incorrect";
        header("Location: /views/auth/login.php");
        exit();
    }
}

function handleRegister() {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $role = isset($_POST['role']) ? $_POST['role'] : 'technicien';
    
    // Validation
    if (empty($username) || empty($password) || empty($confirmPassword) || empty($email)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires";
        header("Location: /views/auth/register.php");
        exit();
    }
    
    if ($password !== $confirmPassword) {
        $_SESSION['error'] = "Les mots de passe ne correspondent pas";
        header("Location: /views/auth/register.php");
        exit();
    }
    
    if (strlen($password) < 6) {
        $_SESSION['error'] = "Le mot de passe doit contenir au moins 6 caractères";
        header("Location: /views/auth/register.php");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Format d'email invalide";
        header("Location: /views/auth/register.php");
        exit();
    }
    
    $userModel = new User();
    
    if ($userModel->usernameExists($username)) {
        $_SESSION['error'] = "Ce nom d'utilisateur est déjà utilisé";
        header("Location: /views/auth/register.php");
        exit();
    }
    
    if ($userModel->emailExists($email)) {
        $_SESSION['error'] = "Cette adresse email est déjà utilisée";
        header("Location: /views/auth/register.php");
        exit();
    }
    
    // Register user
    $result = $userModel->register($username, $password, $email, $role);
    
    if ($result) {
        $_SESSION['success'] = "Compte créé avec succès. Vous pouvez maintenant vous connecter";
        header("Location: /views/auth/login.php");
        exit();
    } else {
        $_SESSION['error'] = "Erreur lors de la création du compte";
        header("Location: /views/auth/register.php");
        exit();
    }
}

function handleLogout() {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header("Location: /views/auth/login.php");
    exit();
}
?>
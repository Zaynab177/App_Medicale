<?php
session_start();

// Vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Vérifier si l'utilisateur a un rôle spécifique
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    if (is_array($role)) {
        return in_array($_SESSION['user_role'], $role);
    }
    
    return $_SESSION['user_role'] === $role;
}

// Rediriger si l'utilisateur n'est pas connecté
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page";
        header("Location: /pharmascan/views/auth/login.php");
        exit();
    }
}

// Rediriger si l'utilisateur n'a pas le rôle requis
function requireRole($role) {
    requireLogin();
    
    if (!hasRole($role)) {
        $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour accéder à cette page";
        header("Location: /pharmascan/index.php");
        exit();
    }
}
?>
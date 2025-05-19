<?php
// controllers/user_controller.php
session_start();
require_once __DIR__ . '/../models/User.php';

// Vérifier l'authentification
require_once __DIR__ . '/../includes/auth_check.php';
requireLogin();

// Vérifier les autorisations (seul l'admin peut accéder à ce contrôleur)
if (!hasRole(['admin'])) {
    $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour effectuer cette action";
    header("Location: /");
    exit();
}

$userModel = new User();
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

switch ($action) {
    case 'create':
        // Validation des données
        if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email']) || empty($_POST['role'])) {
            $_SESSION['error'] = "Tous les champs sont obligatoires";
            header("Location: /views/users/create.php");
            exit();
        }
        
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $email = trim($_POST['email']);
        $role = $_POST['role'];
        
        // Validation supplémentaire
        if (strlen($username) < 3) {
            $_SESSION['error'] = "Le nom d'utilisateur doit contenir au moins 3 caractères";
            header("Location: /views/users/create.php");
            exit();
        }
        
        if (strlen($password) < 6) {
            $_SESSION['error'] = "Le mot de passe doit contenir au moins 6 caractères";
            header("Location: /views/users/create.php");
            exit();
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "L'adresse email n'est pas valide";
            header("Location: /views/users/create.php");
            exit();
        }
        
        // Vérifier si le nom d'utilisateur ou l'email existe déjà
        if ($userModel->usernameExists($username)) {
            $_SESSION['error'] = "Ce nom d'utilisateur existe déjà";
            header("Location: /views/users/create.php");
            exit();
        }
        
        if ($userModel->emailExists($email)) {
            $_SESSION['error'] = "Cette adresse email existe déjà";
            header("Location: /views/users/create.php");
            exit();
        }
        
        // Enregistrer le nouvel utilisateur
        if ($userModel->register($username, $password, $email, $role)) {
            $_SESSION['success'] = "L'utilisateur a été créé avec succès";
            header("Location: /views/users/index.php");
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la création de l'utilisateur";
            header("Location: /views/users/create.php");
        }
        break;
        
    case 'update':
        // Validation des données
        if (!isset($_POST['id']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['role'])) {
            $_SESSION['error'] = "Données invalides pour la mise à jour";
            header("Location: /views/users/index.php");
            exit();
        }
        
        $id = intval($_POST['id']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $role = $_POST['role'];
        
        // Récupérer l'utilisateur actuel pour les vérifications
        $currentUser = $userModel->getUserById($id);
        if (!$currentUser) {
            $_SESSION['error'] = "Utilisateur non trouvé";
            header("Location: /views/users/index.php");
            exit();
        }
        
        // Vérifier si le nom d'utilisateur existe déjà (sauf pour l'utilisateur actuel)
        if ($username !== $currentUser['username'] && $userModel->usernameExists($username)) {
            $_SESSION['error'] = "Ce nom d'utilisateur existe déjà";
            header("Location: /views/users/edit.php?id=" . $id);
            exit();
        }
        
        // Vérifier si l'email existe déjà (sauf pour l'utilisateur actuel)
        if ($email !== $currentUser['email'] && $userModel->emailExists($email)) {
            $_SESSION['error'] = "Cette adresse email existe déjà";
            header("Location: /views/users/edit.php?id=" . $id);
            exit();
        }
        
        // Mettre à jour l'utilisateur
        if ($userModel->updateUser($id, $username, $email, $role)) {
            $_SESSION['success'] = "L'utilisateur a été mis à jour avec succès";
            header("Location: /views/users/index.php");
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour de l'utilisateur";
            header("Location: /views/users/edit.php?id=" . $id);
        }
        break;
        
    case 'update_password':
        // Validation des données
        if (!isset($_POST['id']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
            $_SESSION['error'] = "Données invalides pour la mise à jour du mot de passe";
            header("Location: /views/users/index.php");
            exit();
        }
        
        $id = intval($_POST['id']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        
        // Vérifier si les mots de passe correspondent
        if ($password !== $confirmPassword) {
            $_SESSION['error'] = "Les mots de passe ne correspondent pas";
            header("Location: /views/users/change_password.php?id=" . $id);
            exit();
        }
        
        // Vérifier la complexité du mot de passe
        if (strlen($password) < 6) {
            $_SESSION['error'] = "Le mot de passe doit contenir au moins 6 caractères";
            header("Location: /views/users/change_password.php?id=" . $id);
            exit();
        }
        
        // Mettre à jour le mot de passe
        if ($userModel->updatePassword($id, $password)) {
            $_SESSION['success'] = "Le mot de passe a été mis à jour avec succès";
            header("Location: /views/users/index.php");
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour du mot de passe";
            header("Location: /views/users/change_password.php?id=" . $id);
        }
        break;
        
    case 'delete':
        // Validation des données
        if (!isset($_POST['id'])) {
            $_SESSION['error'] = "ID d'utilisateur non spécifié";
            header("Location: /views/users/index.php");
            exit();
        }
        
        $id = intval($_POST['id']);
        
        // Empêcher la suppression de son propre compte
        if ($id === $_SESSION['user_id']) {
            $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte";
            header("Location: /views/users/index.php");
            exit();
        }
        
        // Supprimer l'utilisateur
        if ($userModel->deleteUser($id)) {
            $_SESSION['success'] = "L'utilisateur a été supprimé avec succès";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression de l'utilisateur";
        }
        
        header("Location: /views/users/index.php");
        break;
        
    default:
        // Action non reconnue, rediriger vers la liste des utilisateurs
        header("Location: /views/users/index.php");
        break;
}
?>
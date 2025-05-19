# 💊 PharmaScan

**PharmaScan** est un système web de gestion de stock pharmaceutique développé pour aider les pharmacies à suivre efficacement leurs produits, dates d’expiration, entrées/sorties de stock, et rôles utilisateurs.

---

## 📌 Fonctionnalités

- 🧑‍⚕️ Gestion multi-rôles : Administrateur, Pharmacien, Technicien
- 📦 Suivi des produits et des quantités
- 📊 Tableau de bord avec statistiques
- ⚠️ Alertes pour dates d’expiration proches et faibles stocks
- 🗂️ Gestion des catégories
- 📝 Historique complet des mouvements de stock
- 🔎 Filtres et recherche dans les listings

---

## 🧱 Architecture Technique

- **Modèle-Vue-Contrôleur (MVC)** en PHP
- Backend : PHP 8.0+
- Frontend : HTML5, CSS3, Bootstrap 5, JavaScript
- Base de données : MySQL 8.0
- Gestion de version : Git + GitHub

---

## 📁 Structure du projet

├── config/ # Connexion à la base de données
├── includes/ # Fichiers partagés (header, footer, auth)
├── models/ # Classes des entités métier (User, Product...)
├── controllers/ # Logique métier par module
├── views/ # Interfaces utilisateur
│ ├── auth/ # Connexion / inscription
│ ├── products/ # Gestion des produits
│ ├── categories/ # Gestion des catégories
│ ├── inventory/ # Mouvements de stock
│ └── dashboard.php # Tableau de bord
└── index.php # Point d’entrée



## ⚙️ Installation

1. **Cloner le dépôt**
git clone https://github.com/Zaynab177/App_Medicale.git
cd App_Medicale
Configurer la base de données

Créer une base de données MySQL (pharmascan)

Configurer les accès à la BDD

Modifier les informations dans config/database.php

Lancer l’application

Démarrer votre serveur local (ex: XAMPP, Laragon)

Accéder à l’application via http://localhost/pharmascan

👥 Équipe
Zaynab – Chef de projet / Authentification / Architecture

Saad – Backend / Base de données / Optimisations

Sohayb – Frontend / UI / Intégration responsive

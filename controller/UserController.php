<?php
require_once 'model/UserManager.php';
require_once 'model/User.php';

class UserController {
    
    // Afficher le formulaire d'inscription
    public static function registration() {
        require_once 'view/registrationView.php';
    }

    // Traiter l'inscription
    public static function add() {
        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $firstname = trim($_POST['firstname'] ?? '');
            $surname = trim($_POST['surname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validation des champs
            if (strlen($firstname) > 100) {
                $errors[] = "Le prénom ne doit pas dépasser 100 caractères.";
            }
            if (strlen($surname) > 100) {
                $errors[] = "Le nom ne doit pas dépasser 100 caractères.";
            }
            if (strlen($email) > 100) {
                $errors[] = "L'email ne doit pas dépasser 100 caractères.";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'email n'est pas valide.";
            }
            if (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[\W]/', $password)) {
                $errors[] = "Le mot de passe doit contenir au moins 8 caractères, un chiffre et un caractère spécial.";
            }
            if (UserManager::existsEmail($email)) {
                $errors[] = "Cet email est déjà utilisé.";
            }

            // Si pas d'erreurs, on ajoute l'utilisateur
            if (empty($errors)) {
                $user = new User(0, $firstname, $surname, $email, $password);
                if (UserManager::add($user)) {
                    $errors[] = "Compte enregistré.";
                } else {
                    $errors[] = "Une erreur est survenue lors de l'inscription.";
                }
            }
        }

        // Afficher à nouveau le formulaire avec les erreurs
        require_once 'view/registrationView.php';
    }
}

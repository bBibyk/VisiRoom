<?php
require_once 'model/UserManager.php';
require_once 'model/User.php';

class UserController {
    
    // Afficher le formulaire d'inscription
    public static function registration() {
        require_once 'view/registrationView.php';
    }

    public static function subscribe() {
        require_once 'view/subscribeView.php';
    }

    public static function updateSub(){
        if(!isset($_SESSION["email"]) || empty($_SESSION["email"])){
            header("location: ../connection");
        }

        $user = UserManager::getByEmail($_SESSION['email']);
        $user->setSub($_GET["sub"]);
        UserManager::update($user);

        require_once 'view/subscribeView.php';
    }

    // Traiter l'inscription
    public static function add() {
        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $firstname = trim($_POST['firstname'] ?? '');
            $surname = trim($_POST['surname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordComfirmation = $_POST['passwordComfirmation'] ?? '';

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
            if ($passwordComfirmation != $password) {
                $errors[] = "Mots de passe non identiques";
            }
            if (UserManager::existsEmail($email)) {
                $errors[] = "Cet email est déjà utilisé.";
            }

            // Si pas d'erreurs, on ajoute l'utilisateur
            if (empty($errors)) {
                $user = new User(0, $firstname, $surname, 'F', $email, $password);
                if (UserManager::add($user)) {
                    $_SESSION["email"] = $user->getEmail();
                    header('location: analysis');
                } else {
                    $errors[] = "Une erreur est survenue lors de l'inscription.";
                }
            }
        }

        // Afficher à nouveau le formulaire avec les erreurs
        require_once 'view/registrationView.php';
    }

    public static function connection(){
        require_once 'view/connectionView.php';
    }

    public static function deconnection(){
        $_SESSION = [];
        header("location: analysis");
    }

    public static function get(){
        $errors = [];

        $user = UserManager::getbyEmail($_POST["email"]);

        if(!empty($user) && $user != null){
            if(password_verify($_POST["password"], $user->getPassword())){
                $_SESSION["email"] = $user->getEmail();
                header('location: analysis');
            }
        }

        require_once 'view/connectionView.php';
    }
}

<?php
session_start();
// ---------------------------------------------------------------------------//
//     logique de rédirection en fonction du role apres la connexion          //
// ---------------------------------------------------------------------------//

require("../../vendor/autoload.php");

use src\Connectbd;
use src\User;

$cnx = Connectbd::getConnection();


// Fonction de redirection
function redirect($url, $message = '') {
    if (!empty($message)) {
        $url .= '?message=' . urlencode($message);
    }
    header("Location: $url");
    exit();
}

if (isset($_POST['validate'])) {
    $connect = strtolower(htmlspecialchars($_POST['validate']));
    $manager= new User($cnx);

    switch ($connect) {

        case 'login':
                if (
                    isset($_POST['email']) && !empty($_POST['email']) &&
                    isset($_POST['password']) && !empty($_POST['password'])
                ) {
                    $email = htmlspecialchars($_POST['email']);
                    $password = htmlspecialchars($_POST['password']);
    
                    $data = [
                        'email'  => $email,
                        'password'  => $password
                    ];
    
                    $result = $manager->verify($data);
    
                    if ($result["success"]) {
                        $_SESSION['email'] = $data['email'];
                        $_SESSION['role'] = $result['role'];
                        $_SESSION['country'] = $result['country'];
                        $_SESSION['is_active'] = $result['is_active'];

                        header('location:../dashboard.php?message='. $message);
                    } else {
                        $message = $result['message'];
                        header('location:login.php?message=' . $message);
                    }
                } else {
                    echo "On ne peut pas se connecter";
                }
                break;    
        case 'create':
            if (
                !isset($_POST['username']) || empty(trim($_POST['username'])) ||
                !isset($_POST['password']) || empty($_POST['password']) ||
                !isset($_POST['numero']) || empty(trim($_POST['numero'])) ||
                !isset($_POST['country']) || empty(trim($_POST['country']))
            ) {
                redirect('../managements/add_personnel.php', "Veuillez remplir tous les champs.");
            }

            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $numero = trim($_POST['numero']);
            $country = trim($_POST['country']);

            if (strlen($username) < 3 || strlen($username) > 20) {
                redirect('../managements/add_personnel.php', "Le nom d'utilisateur doit contenir entre 3 et 20 caractères.");
            }

        // Validation du mot de passe avec une expression régulière
            $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
            if (!preg_match($passwordPattern, $password)) {
                redirect('../managements/add_personnel.php', "Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.");
            }

        // Hachage du mot de passe pour plus de sécurité
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Préparation des données
            $data = [
                'username' => $username,
                'password' => $hashedPassword,
                'numero' => $numero,
                'country' => $country
            ];

        // Insertion dans la base de données via le manager
            if ($manager->create($data)) {
                redirect('login.php', "Inscription réussie !");
            } else {
                redirect('../managements/add_personnel.php', "Une erreur est survenue lors de l'inscription.");
            }
            break;
            
        default:
            echo "On est pas bon";
    }
} else {
    echo "Il manque le POST['connect']";
}
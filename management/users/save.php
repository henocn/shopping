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
function redirect($url, $message = '')
{
    if (!empty($message)) {
        $url .= '?message=' . urlencode($message);
    }
    header("Location: $url");
    exit();
}

if (isset($_POST['validate'])) {
    $connect = strtolower(htmlspecialchars($_POST['validate']));
    $manager = new User($cnx);

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

                    header('location:../dashboard.php?message=' . $message);
                } else {
                    $message = $result['message'];
                    header('location:login.php?message=' . $message);
                }
            } else {
                echo "On ne peut pas se connecter";
            }
            break;
            
        case 'ajouter':
            if (
                !isset($_POST['email']) || empty(trim($_POST['email'])) ||
                !isset($_POST['country']) || empty(trim($_POST['country'])) ||
                !isset($_POST['role']) || empty(trim($_POST['role']))
            ) {
                redirect('add.php', "Veuillez remplir tous les champs.");
            }

            $email = trim($_POST['email']);
            $role = trim($_POST['role']);
            $country = trim($_POST['country']);

            if ($manager->email_exists($email)) {
                redirect('add.php', "L'email existe déjà. Veuillez en choisir un autre.");
            }


            $data = [
                'email' => $email,
                'password' => "user1234",
                'role' => $role,
                'country' => $country
            ];

            if ($manager->create($data)) {
                redirect('add.php', "Inscription réussie !");
            } else {
                redirect('add.php', "Une erreur est survenue lors de l'inscription.");
            }
            break;

        default:
            echo "On est pas bon";
    }
} else {
    echo "Il manque le POST['connect']";
}

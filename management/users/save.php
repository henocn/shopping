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
                isset($_POST['password']) && !empty($_POST['password']) &&
                isset($_POST['redirect'])
            ) {
                $email = htmlspecialchars($_POST['email']);
                $password = htmlspecialchars($_POST['password']);
                $redirect = htmlspecialchars($_POST['redirect']);

                $data = [
                    'email'  => $email,
                    'password'  => $password
                ];

                $result = $manager->verify($data);

                if ($result["success"]) {
                    $_SESSION['user_id'] = $result['id'];
                    $_SESSION['email'] = $data['email'];
                    $_SESSION['role'] = $result['role'];
                    $_SESSION['country'] = $result['country'];
                    $_SESSION['is_active'] = $result['is_active'];

                    header('Location: ' . ($redirect ?: "/shopping/management/dashboard.php"));
                } else {
                    header('Location: login.php?error=' . $result['message'] . ($redirect ? '&redirect=' . $redirect : ''));
                }
            } else {
                echo "On ne peut pas se connecter";
            }
            break;

        case 'ajouter':
            if (
                !isset($_POST['email']) || empty(trim($_POST['email'])) ||
                !isset($_POST['name']) || empty(trim($_POST['name'])) ||
                !isset($_POST['country']) || empty(trim($_POST['country'])) ||
                !isset($_POST['role'])
            ) {
                redirect('index.php', "Veuillez remplir tous les champs.");
            }

            $email = trim($_POST['email']);
            $name = trim($_POST['name']);
            $role = trim($_POST['role']);
            $country = trim($_POST['country']);

            if ($manager->email_exists($email)) {
                redirect('index.php', "L'email existe déjà. Veuillez en choisir un autre.");
            }


            $data = [
                'email' => $email,
                'name' => $name,
                'password' => "user1234",
                'role' => $role,
                'country' => $country
            ];

            if ($manager->create($data)) {
                redirect('index.php', "Inscription réussie !");
            } else {
                redirect('index.php', "Une erreur est survenue lors de l'inscription.");
            }
            break;

        case 'suspend':
            if (
                isset($_POST['user_id']) && is_numeric($_POST['user_id'])
            ) {
                $user_id = (int)$_POST['user_id'];

                if ($manager->switchaccountStatus($user_id)) {
                    redirect('index.php', "Opération réussie !");
                } else {
                    redirect('index.php', "Erreur lors de la mise à jour du statut.");
                }
            } else {
                redirect('index.php', "Données invalides pour la mise à jour du statut.");
            }
            break;

        case 'delete':
            if (
                isset($_POST['user_id']) && is_numeric($_POST['user_id'])
            ) {
                $user_id = (int)$_POST['user_id'];

                if ($manager->deleteUser($user_id)) {
                    redirect('index.php', "Utilisateur supprimé avec succès !");
                } else {
                    redirect('index.php', "Erreur lors de la suppression de l'utilisateur.");
                }
            } else {
                redirect('index.php', "Données invalides pour la suppression de l'utilisateur.");
            }
            break;

        case 'change_password':
            if (
                isset($_POST['current_password']) && !empty($_POST['current_password']) &&
                isset($_POST['new_password']) && !empty($_POST['new_password']) &&
                isset($_POST['confirm_password']) && !empty($_POST['confirm_password']) &&
                isset($_SESSION['user_id'])
            ) {
                $current_password = htmlspecialchars($_POST['current_password']);
                $new_password = htmlspecialchars($_POST['new_password']);
                $confirm_password = htmlspecialchars($_POST['confirm_password']);

                if ($new_password !== $confirm_password) {
                    header('Location: change-pass.php?error=passwords_not_match' . ($redirect ? '&redirect=' . $redirect : ''));
                    exit();
                }

                $result = $manager->changePassword($_SESSION['user_id'], $current_password, $new_password);

                if ($result["success"]) {
                    header('Location: change-pass.php?success=1' . ($redirect ? '&redirect=' . $redirect : ''));
                } else {
                    header('Location: change-pass.php?error=' . $result['message'] . ($redirect ? '&redirect=' . $redirect : ''));
                }
            } else {
                header('Location: change-pass.php?error=missing_fields');
            }
            break;

        default:
            echo "On est pas bon";
    }
} else {
    echo "Il manque le POST['connect']";
}

<?php
session_start();

require("../../vendor/autoload.php");

use src\Connectbd;
use src\Product;
use src\Order;
use src\Pack;

$cnx = Connectbd::getConnection();


if (isset($_POST['valider'])) {
    $connect = strtolower(htmlspecialchars($_POST['valider']));
    $productManager = new Product($cnx);
    $packManager = new Pack($cnx);
    $orderManager = new Order($cnx);


    switch ($connect) {

        case 'commander':
            if (
                isset($_POST['product_id']) &&
                isset($_POST['client_name']) &&
                isset($_POST['client_country']) &&
                isset($_POST['client_phone'])
            ) {

                $packId = !empty($_POST['pack_id']) ? htmlspecialchars($_POST['pack_id']) : null;
                $productId = htmlspecialchars($_POST['product_id']);

                if($packId != null) {
                    $pack = $packManager->getPackById($packId);
                }

                //$pack = $packManager->getPackById($packId);
                $product = $productManager->getProducts($productId);

                $data = [
                    'product_id'    => $productId,
                    'pack_id'       => $packId,
                    'client_name'   => $_POST['client_name'],
                    'client_country' => htmlspecialchars($_POST['client_country']),
                    'client_adress' => htmlspecialchars($_POST['client_adress']),
                    'client_phone'  => htmlspecialchars($_POST['client_phone']),
                    'client_note'   => htmlspecialchars($_POST['client_note']),
                    'purchase_price'    => $product['purchase_price'],
                    'total_price'   => !empty($pack['price_reduction']) ? $pack['price_reduction'] : $product['selling_price'],
                    'quantity'      => !empty($pack['quantity']) ? $pack['quantity'] : 1,
                    'manager_id'   => $product['manager_id'],
                ];



                if ($orderManager->CreateOrder($data)) {
                    $_SESSION['order_message'] = "Votre commande a été passée avec succès. Nous vous contacterons bientôt.";
                    header("Location: ../../index.php?id=" . $productId);
                } else {
                    $_SESSION['order_message'] = "Une erreur est survenue lors de la passation de votre commande. Veuillez réessayer.";
                    header("Location: ../../index.php?id=" . $productId);
                }
            }
            break;

        case 'update':
            if (isset($_POST['order_id'])) {
                $data = [
                    'id'           => htmlspecialchars($_POST['order_id']),
                    'quantity'     => htmlspecialchars($_POST['quantity'] ?? ''),
                    'total_price'  => htmlspecialchars($_POST['total_price'] ?? ''),
                    'newstat'      => htmlspecialchars($_POST['newstat'] ?? ''),
                    'manager_note' => htmlspecialchars($_POST['manager_note'] ?? ''),
                    'updated_at'   => date('Y-m-d H:i:s'),
                    ];

                $orderManager->updateOrder($data);

                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'fetch') {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'newstat'  => $data['newstat']
                    ]);
                    exit;
                }

                $message = urlencode(" commande a été mis à jour avec succès.");
                header("Location: index.php?message=" . $message);
                exit;
            } else {
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'fetch') {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'error'   => 'Données manquantes pour mettre à jour la commande'
                    ]);
                    exit;
                }

                $message = urlencode("Données manquantes pour mettre à jour le statut de la commande.");
                header("Location: index.php?message=" . $message);
                exit;
            }
            break;

        default:
            header("Location: /error.php?code=400");
    }
} else {
    header("Location: /error.php?code=400");
}

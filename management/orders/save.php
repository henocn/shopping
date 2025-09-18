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
                isset($_POST['pack_id']) &&
                isset($_POST['client_name']) &&
                isset($_POST['client_phone']))
            {

                $packId = htmlspecialchars($_POST['pack_id']);
                $productId = htmlspecialchars($_POST['product_id']);

                $pack = $packManager->getPackById($packId);
                $product = $productManager->getProducts($productId);

                $data = [
                    'product_id' => $productId,
                    'pack_id' => $packId,
                    'client_name' => htmlspecialchars($_POST['client_name']),
                    'client_country' => "TD",
                    'client_adress' => htmlspecialchars($_POST['client_adress']),
                    'client_phone' => htmlspecialchars($_POST['client_phone']),
                    'client_note' => htmlspecialchars($_POST['client_note']),
                    'unit_price' => $product['price'],
                    'total_price' => $pack['price_reduction'],
                    'quantity' => $pack['quantity']
                ];
    
                if ($orderManager->CreateOrder($data)) {
                    //header("Location : ../../index.php?id=". $productId . "&command=success");
                    echo "success";
                } else {
                    //header("Location : ../../index.php?id=". $productId . "&command=error");
                    echo "error";
                }
            }
            break;

        case 'update':
            if (isset($_POST['order_id'])) {
                $data = [
                    'id'           => htmlspecialchars($_POST['order_id']),
                    'quantity'     => htmlspecialchars($_POST['quantity'] ?? ''),
                    'total_price'  => htmlspecialchars($_POST['total_price'] ?? ''),
                    'manager_note' => htmlspecialchars($_POST['manager_note'] ?? ''),
                    'updated_at'   => date('Y-m-d H:i:s'),
                    'status'       => htmlspecialchars($_POST['status'] ?? 'processing'),
                    'action'       => htmlspecialchars($_POST['action'] ?? 'move'),
                ];

                $orderManager->updateOrder($data);

                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'fetch') {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'status'  => $data['status'],
                        'action'  => $data['action']
                    ]);
                    exit;
                }

                $message = urlencode("Le statut de la commande a été mis à jour avec succès.");
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

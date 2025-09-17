<?php
session_start();

require("../../vendor/autoload.php");

use src\Connectbd;
use src\Product;
use src\Order;
use src\Pack;

$cnx = Connectbd::getConnection();


if (isset($_POST['valider']) || isset($_GET['valider'])) {
    $connect = strtolower(htmlspecialchars($_POST['valider'] ?? $_GET['valider'] ?? ''));
    $manager = new Product($cnx);
    $order = new Order($cnx);


    switch ($connect) {

        case 'commander':
            if (
                isset($_POST['product_id']) &&
                isset($_POST['pack_id']) &&
                isset($_POST['quantity']) &&
                isset($_POST['unit_price']) &&
                isset($_POST['total_price']) &&
                isset($_POST['client_name']) &&
                isset($_POST['client_country']) &&
                isset($_POST['client_phone']) &&
                isset($_POST['manager_id']) &&
                isset($_POST['client_adress']) &&
                (isset($_POST['client_note']) || empty($_POST['client_note']))
            ) {

                $data = [
                    'product_id' => htmlspecialchars($_POST['product_id']),
                    'pack_id' => htmlspecialchars($_POST['pack_id']),
                    'quantity' => htmlspecialchars($_POST['quantity']),
                    'unit_price' => htmlspecialchars($_POST['unit_price']),
                    'total_price' => htmlspecialchars($_POST['total_price']),
                    'client_name' => htmlspecialchars($_POST['client_name']),
                    'client_country' => htmlspecialchars($_POST['client_country']),
                    'client_adress' => htmlspecialchars($_POST['client_adress']),
                    'client_phone' => htmlspecialchars($_POST['client_phone']),
                    'manager_id' => htmlspecialchars($_POST['manager_id']),
                    'client_note' => htmlspecialchars($_POST['client_note']),
                    'status' => 'processing',
                    'action' => 'call'
                ];
                try {
                    if ($order->CreateOrder($data)) {
                        echo json_encode(['success' => true, 'message' => 'Merci ! Votre commande a été enregistrée avec succès !']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Échec enregistrement de votre commande']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
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

                $order->updateOrder($data);

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

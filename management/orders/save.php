<?php
session_start();

require("../../vendor/autoload.php");

use src\Connectbd;
use src\Product;
use src\Order;
use src\Pack;
use src\Depense;

$cnx = Connectbd::getConnection();


if (isset($_POST['valider'])) {
    $connect = strtolower(htmlspecialchars($_POST['valider']));
    $productManager = new Product($cnx);
    $packManager = new Pack($cnx);
    $orderManager = new Order($cnx);
    $depenseManager = new Depense($cnx);


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
                
                // Récupérer le prix de vente depuis la table product_countries
                $productCountries = $productManager->getProductCountries($productId);
                $sellingPrice = !empty($productCountries) ? $productCountries[0]['selling_price'] : 0;
                
                // Récupérer le premier manager associé au produit (s'il existe)
                $productManagers = $productManager->getProductManagers($productId);
                $managerId = !empty($productManagers) ? $productManagers[0]['id'] : null;

                $data = [
                    'product_id'    => $productId,
                    'pack_id'       => $packId,
                    'client_name'   => $_POST['client_name'],
                    'client_country' => htmlspecialchars($_POST['client_country']),
                    'client_adress' => htmlspecialchars($_POST['client_adress']),
                    'client_phone'  => htmlspecialchars($_POST['client_phone']),
                    'client_note'   => htmlspecialchars($_POST['client_note']),
                    'purchase_price'    => $product['purchase_price'],
                    'total_price'   => !empty($pack['price']) ? $pack['price'] : $sellingPrice,
                    'quantity'      => !empty($pack['quantity']) ? $pack['quantity'] : 1,
                    'manager_id'   => $managerId,
                ];



                if ($orderManager->CreateOrder($data)) {
                    $_SESSION['order_message'] = "Votre commande a été passée avec succès. Nous vous contacterons bientôt.";
                    // header("Location: ../../index.php?id=" . $productId);
                    header("Location: ../../index2.php?id=" . $productId);
                } else {
                    $_SESSION['order_message'] = "Une erreur est survenue lors de la passation de votre commande. Veuillez réessayer.";
                    // header("Location: ../../index.php?id=" . $productId);
                    header("Location: ../../index2.php?id=" . $productId);
                }
            }
            break;

        case 'update':
            if (isset($_POST['order_id'])) {
                $orderId = (int)($_POST['order_id'] ?? 0);
                $existingOrder = $orderManager->getOrderById($orderId);

                if (!$existingOrder) {
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'fetch') {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => false,
                            'error'   => 'Commande introuvable'
                        ]);
                        exit;
                    }

                    $message = urlencode("Commande introuvable.");
                    header("Location: index.php?message=" . $message);
                    exit;
                }

                $newStatus = htmlspecialchars($_POST['newstat'] ?? '');
                $updatedQuantity = (int)($_POST['quantity'] ?? $existingOrder['quantity']);
                $updatedTotal = (float)($_POST['total_price'] ?? $existingOrder['total_price']);
                $managerNote = htmlspecialchars($_POST['manager_note'] ?? '');

                $data = [
                    'id'           => $orderId,
                    'quantity'     => $updatedQuantity,
                    'total_price'  => $updatedTotal,
                    'newstat'      => $newStatus,
                    'manager_note' => $managerNote,
                    'updated_at'   => date('Y-m-d H:i:s'),
                ];

                $orderManager->updateOrder($data);

                $shouldDecreaseStock = $newStatus === 'deliver' && $existingOrder['newstat'] !== 'deliver';

                if ($shouldDecreaseStock && !empty($existingOrder['product_id'])) {
                    $productManager->decrementQuantity((int)$existingOrder['product_id'], $updatedQuantity);
                }

                // Enregistrer les frais de livraison si fournis
                if ($newStatus === 'deliver' && isset($_POST['delivery_fee']) && $_POST['delivery_fee'] > 0) {
                    $depenseData = [
                        'type'        => 'products',
                        'product_id'  => (int)$existingOrder['product_id'],
                        'cout'        => (int)$_POST['delivery_fee'],
                        'date'        => date('Y-m-d H:i:s'),
                        'descrption'  => 'Livraison'
                    ];
                    $depenseManager->createDepense($depenseData);
                }

                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'fetch') {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'newstat'  => $data['newstat']
                    ]);
                    exit;
                }

                $message = urlencode("Commande mise à jour avec succès.");
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
        default:
            header("Location: /error.php?code=400");
    }
} else {
    header("Location: /error.php?code=400");
}

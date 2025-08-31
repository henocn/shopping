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
    $manager = new Product($cnx);
    $order = new Order($cnx);

    switch ($connect) {

        case 'commander':
            if(
                  isset($_POST['product_id']) &&
                  isset($_POST['pack_id']) &&
                  isset($_POST['quantity']) &&
                  isset($_POST['total_price']) &&
                  isset($_POST['client_name']) &&
                  isset($_POST['client_country']) &&  
                  isset($_POST['client_adress']) &&
                  isset($_POST['client_note']) || 
                  empty($_POST['client_note'])
            ){
                  
                  $data = [
                        'product_id' => htmlspecialchars($_POST['product_id']),
                        'pack_id' => htmlspecialchars($_POST['pack_id']),
                        'quantity' => htmlspecialchars($_POST['quantity']),
                        'total_price' => htmlspecialchars($_POST['total_price']),
                        'client_name' => htmlspecialchars($_POST['client_name']),
                        'client_country' => htmlspecialchars($_POST['client_country']),
                        'client_adress' => htmlspecialchars($_POST['client_adress']),
                        'client_note' => htmlspecialchars($_POST['client_note']),
                        'status' => 'processing'
                  ];
                  $order->CreateCommande($data);
                  
            } else {
                  echo "Veuillez remplir tous les champs obligatoires";
            }
            break;

        default:
            echo "On est pas bon";
    }
} else {
    echo "Il manque le POST['connect']";
}

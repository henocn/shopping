<?php
session_start();

require("../../vendor/autoload.php");

use src\Connectbd;
use src\Product;

$cnx = Connectbd::getConnection();


if (isset($_POST['valider'])) {
    $connect = strtolower(htmlspecialchars($_POST['valider']));
    $manager = new Product($cnx);

    switch ($connect) {

        case 'upload':
            if (
                isset($_FILES['image'], $_POST['name'], $_POST['price'], $_POST['quantity'], $_POST['description'], $_POST['status']) &&
                $_FILES['image']['error'] === UPLOAD_ERR_OK &&
                is_numeric($_POST['price']) &&
                is_numeric($_POST['quantity'])
            ) {
                $data = [
                    'name' => htmlspecialchars($_POST['name']),
                    'price' => (int)$_POST['price'],
                    'quantity' => (int)$_POST['quantity'],
                    'image' => basename($_FILES['image']['name']),
                    'description' => htmlspecialchars($_POST['description']),
                    'status' => (int)$_POST['status'],
                ];

                $uploadDir = '../../uploads/main/';
                $filePath = $uploadDir . $data['image'];

                if (
                    move_uploaded_file($_FILES['image']['tmp_name'], $filePath)
                ) {
                    $manager->createProduct($data); 
                    $message = "Product add success !";
                    //header('Location:index.php?message=' . urlencode($message));
                }
            } else {
                echo "On est dans la suppression a échoué";
            }
            break;
        case 'update':

        default:
            echo "On est pas bon";
    }
} else {
    echo "Il manque le POST['connect']";
}

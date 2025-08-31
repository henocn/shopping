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
                isset(
                    $_FILES['image'], $_POST['name'], $_POST['price'], $_POST['quantity'],
                    $_POST['description'], $_POST['status']
                ) &&
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
    
                $uploadDir = __DIR__ . '/../../uploads/main/';
                $carouselDir = __DIR__ . '/../../uploads/carousel/';

                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                if (!file_exists($carouselDir)) {
                    mkdir($carouselDir, 0755, true);
                }

                $filePath = $uploadDir . basename($_FILES['image']['name']);

                for ($i = 1; $i <= 5; $i++) {
                    $fieldName = 'carousel' . $i;
                    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
                        $data[$fieldName] = basename($_FILES[$fieldName]['name']);
                    } else {
                        $data[$fieldName] = '';
                    }
                }

                if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {

                    for ($i = 1; $i <= 5; $i++) {
                        $fieldName = 'carousel' . $i;
                        if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
                            $carouselPath = $carouselDir . basename($_FILES[$fieldName]['name']);

                            $counter = 1;
                            $originalName = pathinfo($_FILES[$fieldName]['name'], PATHINFO_FILENAME);
                            $extension = pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION);

                            while (file_exists($carouselPath)) {
                                $newFilename = $originalName . '_' . $counter . '.' . $extension;
                                $carouselPath = $carouselDir . $newFilename;
                                $counter++;
                            }

                            if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $carouselPath)) {
                                if ($counter > 1) {
                                    $data[$fieldName] = basename($carouselPath);
                                }
                            } else {
                                echo "Erreur lors du téléchargement de l'image carousel $i<br>";
                            }
                        }
                    }

                    $manager->createProduct($data);
                    $message = "Product add success !";
                    header('Location:index.php?message=' . urlencode($message));
                    exit;
                } else {
                    echo "Erreur lors du téléchargement de l'image principale. Vérifiez les permissions du répertoire.";
                }
            } else {
                echo "Erreur: Données manquantes ou invalides";
            }
            break;
        case 'update':

            break;
        default:
            echo "On est pas bon";
    }
} else {
    echo "Il manque le POST['connect']";
}

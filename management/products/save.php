<?php
session_start();

require("../../vendor/autoload.php");

use src\Connectbd;
use src\Product;

$cnx = Connectbd::getConnection();


if (isset($_POST['valider'])) {
    $connect = strtolower(htmlspecialchars($_POST['valider']));
    $manager = new Product($cnx);

    var_dump($connect);

    switch ($connect) {

        case 'upload':
            if (
                isset(
                    $_FILES['image'],
                    $_POST['name'],
                    $_POST['price'],
                    $_POST['quantity'],
                    $_POST['description'],
                    $_POST['status']
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
        case 'video':
            if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
                $productId = intval($_POST['product_id']);
                $videoDir = __DIR__ . '/../../uploads/videos/';


                if ($productId > 0) {
                   

                    for ($i = 1; $i <= 3; $i++) {
                        if (isset($_FILES["video$i"]) && $_FILES["video$i"]['error'] === UPLOAD_ERR_OK) {
                            $fileTmp  = $_FILES["video$i"]['tmp_name'];
                            $fileName = basename($_FILES["video$i"]['name']);
                            $videoPath = $videoDir . $fileName;

                            $description = trim($_POST["video{$i}_description"]);

                            if (move_uploaded_file($fileTmp, $videoPath)) {
                                $videoData = [
                                    'product_id' => $productId,
                                    'video_url'  => $fileName,
                                    'texte'      => $description
                                ];

                                $manager->createVideos($videoData);
                            } else {
                                echo "Erreur lors du téléchargement de la vidéo $i<br>";
                            }
                        }
                    }

                    $message = "Vidéos ajoutées avec succès !";
                    header('Location:index.php?message=' . urlencode($message));
                    exit;
                } else {
                    echo "ID produit invalide<br>";
                }
            }
            break;
        case 'packs':
            if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
                $productId = intval($_POST['product_id']);

                if ($productId > 0) {

                    for ($i = 1; $i <= 6; $i++) {
                        $packKey = "pack$i";

                        if (!empty(trim($_POST["{$packKey}_titre"]))) {
                            $titre = trim($_POST["{$packKey}_titre"]);
                            $description = trim($_POST["{$packKey}_description"] ?? '');
                            $quantity = intval($_POST["{$packKey}_quantity"] ?? 1);
                            $priceReduction = floatval($_POST["{$packKey}_price_reduction"] ?? 0);
                            $priceNormal = floatval($_POST["{$packKey}_price_normal"] ?? 0);

                            
                            if (empty($titre)) {
                                echo "Le titre du pack $i ne peut pas être vide<br>";
                                continue;
                            }

                            if ($priceNormal <= 0) {
                                echo "Le prix normal du pack $i doit être supérieur à 0<br>";
                                continue;
                            }

                            
                            $packData = [
                                'product_id' => $productId,
                                'titre' => $titre,
                                'description' => $description,
                                'quantity' => $quantity,
                                'price_reduction' => $priceReduction,
                                'price_normal' => $priceNormal
                            ];

                            $manager->createPacks($packData);
                        }
                    }

                    $message = "Packs ajoutés avec succès !";
                    header('Location: index.php?message=' . urlencode($message));
                    exit;
                } else {
                    echo "ID produit invalide<br>";
                }
            }
            break;
        case 'caracteristics':
            if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
                $productId = intval($_POST['product_id']);
                $imageDir = __DIR__ . '/../../uploads/caracteristics/';

                if ($productId > 0) {

                    for ($i = 1; $i <= 6; $i++) {
                        if (isset($_FILES["image$i"]) && $_FILES["image$i"]['error'] === UPLOAD_ERR_OK) {
                            $fileTmp  = $_FILES["image$i"]['tmp_name'];
                            $fileName = basename($_FILES["image$i"]['name']);
                            $imagePath = $imageDir . $fileName;

                            $titre       = trim($_POST["image{$i}_titre"]);
                            $description = trim($_POST["image{$i}_description"]);

                            if (move_uploaded_file($fileTmp, $imagePath)) {
                                $imageData = [
                                    'product_id'  => $productId,
                                    'title'       => $titre,
                                    'image'       => $fileName,
                                    'description' => $description
                                ];

                                $manager->createCaracteristics($imageData);
                            } else {
                                echo "Erreur lors du téléchargement de l'image $i<br>";
                            }
                        }
                    }

                    $message = "Caractéristiques ajoutées avec succès !";
                    header('Location:index.php?message=' . urlencode($message));
                    exit;
                } else {
                    echo "ID produit invalide<br>";
                }
            }
            break;

        default:
            echo "On est pas bon";
    }
} else {
    echo "Il manque le POST['connect']";
}

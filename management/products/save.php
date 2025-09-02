<?php
session_start();
require("../../vendor/autoload.php");

use src\Connectbd;
use src\Product;

$cnx = Connectbd::getConnection();
$manager = new Product($cnx);

if (!isset($_POST['valider'])) {
    header('Location: index.php?error=' . urlencode("Action non spécifiée"));
    exit;
}

$action = $_POST['valider'];

switch($action) {
    case 'upstatus':
        if (isset($_POST['product_id'], $_POST['new_status']) && 
            is_numeric($_POST['product_id']) && 
            in_array($_POST['new_status'], [0, 1])) {
            
            try {
                $manager->updateProductStatus($_POST['product_id'], $_POST['new_status']);
                $message = "Statut du produit mis à jour avec succès !";
                header('Location: index.php?message=' . urlencode($message));
                exit;
            } catch (Exception $e) {
                $message = "Erreur lors de la mise à jour du statut : " . $e->getMessage();
                header('Location: index.php?error=' . urlencode($message));
                exit;
            }
        }
        break;
        
    case 'delete':
        if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
            try {
                $manager->deleteProduct($_POST['product_id']);
                $message = "Produit supprimé avec succès !";
                header('Location: index.php?message=' . urlencode($message));
                exit;
            } catch (Exception $e) {
                $message = "Erreur lors de la suppression : " . $e->getMessage();
                header('Location: index.php?error=' . urlencode($message));
                exit;
            }
        }
        break;
        
    case 'Enregistrer le produit':
        // Création des dossiers d'upload si nécessaire
        $uploadDirs = [
            'main' => __DIR__ . '/../../uploads/main/',
            'carousel' => __DIR__ . '/../../uploads/carousel/',
            'characteristics' => __DIR__ . '/../../uploads/characteristics/',
            'videos' => __DIR__ . '/../../uploads/videos/'
        ];

        foreach ($uploadDirs as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        try {
            // Traitement de l'image principale
            $mainImageName = '';
            if (isset($_FILES['mainImage']) && $_FILES['mainImage']['error'] === UPLOAD_ERR_OK) {
                $mainImageName = time() . '_' . basename($_FILES['mainImage']['name']);
                move_uploaded_file(
                    $_FILES['mainImage']['tmp_name'],
                    $uploadDirs['main'] . $mainImageName
                );
            }

            // Traitement des images du carousel
            $carouselImages = ['', '', '', '', ''];
            if (isset($_FILES['carouselImages'])) {
                foreach ($_FILES['carouselImages']['tmp_name'] as $key => $tmp_name) {
                    if (isset($_FILES['carouselImages']['error'][$key]) && 
                        $_FILES['carouselImages']['error'][$key] === UPLOAD_ERR_OK && 
                        $key < 5) {
                        $fileName = time() . '_' . basename($_FILES['carouselImages']['name'][$key]);
                        if (move_uploaded_file($tmp_name, $uploadDirs['carousel'] . $fileName)) {
                            $carouselImages[$key] = $fileName;
                        }
                    }
                }
            }

            // Création du produit principal
            $productData = [
                'name' => htmlspecialchars($_POST['name']),
                'price' => floatval($_POST['price']),
                'quantity' => intval($_POST['quantity']),
                'image' => $mainImageName,
                'description' => $_POST['description'],
                'status' => 1,  // Actif par défaut
                'carousel1' => $carouselImages[0],
                'carousel2' => $carouselImages[1],
                'carousel3' => $carouselImages[2],
                'carousel4' => $carouselImages[3],
                'carousel5' => $carouselImages[4]
            ];

            $manager->createProduct($productData);
            $productId = $manager->GetLastProductId();

            if ($productId) {
                // Traitement des caractéristiques
                if (isset($_POST['characteristic_title'])) {
                    foreach ($_POST['characteristic_title'] as $key => $title) {
                        if (!empty($title)) {
                            $characteristicImage = '';
                            if (isset($_FILES['characteristic_image']['tmp_name'][$key]) && 
                                $_FILES['characteristic_image']['error'][$key] === UPLOAD_ERR_OK) {
                                
                                $characteristicImage = time() . '_' . basename($_FILES['characteristic_image']['name'][$key]);
                                move_uploaded_file(
                                    $_FILES['characteristic_image']['tmp_name'][$key],
                                    $uploadDirs['characteristics'] . $characteristicImage
                                );
                            }

                            $characteristicData = [
                                'product_id' => $productId,
                                'title' => htmlspecialchars($title),
                                'image' => $characteristicImage,
                                'description' => htmlspecialchars($_POST['characteristic_description'][$key] ?? '')
                            ];

                            $manager->createCaracteristics($characteristicData);
                        }
                    }
                }

                // Traitement des vidéos
                if (isset($_FILES['video'])) {
                    foreach ($_FILES['video']['tmp_name'] as $key => $tmp_name) {
                        if ($_FILES['video']['error'][$key] === UPLOAD_ERR_OK) {
                            $videoName = time() . '_' . basename($_FILES['video']['name'][$key]);
                            if (move_uploaded_file($tmp_name, $uploadDirs['videos'] . $videoName)) {
                                $videoData = [
                                    'product_id' => $productId,
                                    'video_url' => $videoName,
                                    'texte' => htmlspecialchars($_POST['video_text'][$key] ?? '')
                                ];
                                
                                $manager->createVideos($videoData);
                            }
                        }
                    }
                }

                $message = "Produit ajouté avec succès !";
                header('Location: index.php?message=' . urlencode($message));
                exit;
            } else {
                throw new Exception("Erreur lors de la création du produit");
            }

        } catch (Exception $e) {
            $message = "Erreur lors de l'ajout du produit : " . $e->getMessage();
            header('Location: add.php?error=' . urlencode($message));
            exit;
        }
        break;

    default:
        header('Location: index.php?error=' . urlencode("Action non reconnue"));
        exit;
}

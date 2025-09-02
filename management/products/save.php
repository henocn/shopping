<?php
session_start();
require("../../vendor/autoload.php");

use src\Connectbd;
use src\Product;

$cnx = Connectbd::getConnection();
$manager = new Product($cnx);

var_dump($_POST['valider']);

if (!isset($_POST['valider'])) {
    header('Location: index.php?error=' . urlencode("Action non spécifiée"));
    exit;
}


$action = $_POST['valider'];

switch ($action) {
    case 'upstatus':
        if (
            isset($_POST['product_id'], $_POST['new_status']) &&
            is_numeric($_POST['product_id']) &&
            in_array($_POST['new_status'], [0, 1])
        ) {

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
                    if (
                        isset($_FILES['carouselImages']['error'][$key]) &&
                        $_FILES['carouselImages']['error'][$key] === UPLOAD_ERR_OK &&
                        $key < 5
                    ) {
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
                            if (
                                isset($_FILES['characteristic_image']['tmp_name'][$key]) &&
                                $_FILES['characteristic_image']['error'][$key] === UPLOAD_ERR_OK
                            ) {

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
    case 'Mettre a jour le produit':
        // Code pour mettre à jour un produit
        if ( isset($_POST['productId'])) {
            $productId = $_POST['productId'];

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
                // Récupérer les données existantes du produit
                $existingProduct = $manager->getProducts($productId);

                // Traitement de l'image principale
                $mainImageName = $_POST['existing_main_image'] ?? '';

                // Supprimer l'image principale si demandé
                if (isset($_POST['delete_main_image']) && !empty($mainImageName)) {
                    $filePath = $uploadDirs['main'] . $mainImageName;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $mainImageName = '';
                }

                // Uploader une nouvelle image principale
                if (isset($_FILES['mainImage']) && $_FILES['mainImage']['error'] === UPLOAD_ERR_OK) {
                    // Supprimer l'ancienne image si elle existe
                    if (!empty($mainImageName)) {
                        $oldFilePath = $uploadDirs['main'] . $mainImageName;
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }

                    $mainImageName = time() . '_' . basename($_FILES['mainImage']['name']);
                    move_uploaded_file(
                        $_FILES['mainImage']['tmp_name'],
                        $uploadDirs['main'] . $mainImageName
                    );
                }

                // Traitement des images du carousel
                $carouselImages = ['', '', '', '', ''];
                $existingCarousel = $_POST['existing_carousel_images'] ?? [];

                // Gérer la suppression des images du carousel
                if (isset($_POST['delete_carousel_images'])) {
                    foreach ($_POST['delete_carousel_images'] as $imageToDelete) {
                        $filePath = $uploadDirs['carousel'] . $imageToDelete;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }

                        // Retirer l'image du tableau existant
                        $key = array_search($imageToDelete, $existingCarousel);
                        if ($key !== false) {
                            unset($existingCarousel[$key]);
                        }
                    }
                }

                // Réindexer et remplir le tableau carouselImages avec les images existantes
                $existingCarousel = array_values($existingCarousel);
                for ($i = 0; $i < 5; $i++) {
                    if (isset($existingCarousel[$i])) {
                        $carouselImages[$i] = $existingCarousel[$i];
                    }
                }

                // Traiter les nouvelles images du carousel
                if (isset($_FILES['carouselImages'])) {
                    $newIndex = count($existingCarousel);
                    foreach ($_FILES['carouselImages']['tmp_name'] as $key => $tmp_name) {
                        if ($_FILES['carouselImages']['error'][$key] === UPLOAD_ERR_OK && $newIndex < 5) {
                            $fileName = time() . '_' . basename($_FILES['carouselImages']['name'][$key]);
                            if (move_uploaded_file($tmp_name, $uploadDirs['carousel'] . $fileName)) {
                                $carouselImages[$newIndex] = $fileName;
                                $newIndex++;
                            }
                        }
                    }
                }

                // Mise à jour du produit principal
                $productData = [
                    'name' => htmlspecialchars($_POST['name']),
                    'price' => floatval($_POST['price']),
                    'quantity' => intval($_POST['quantity']),
                    'image' => $mainImageName,
                    'description' => $_POST['description'],
                    'carousel1' => $carouselImages[0],
                    'carousel2' => $carouselImages[1],
                    'carousel3' => $carouselImages[2],
                    'carousel4' => $carouselImages[3],
                    'carousel5' => $carouselImages[4]
                ];

                $manager->updateProduct($productId, $productData);

                // Traitement des caractéristiques existantes
                if (isset($_POST['existing_char_id'])) {
                    foreach ($_POST['existing_char_id'] as $index => $charId) {
                        // Vérifier si la caractéristique doit être supprimée
                        if (isset($_POST['delete_characteristic']) && in_array($charId, $_POST['delete_characteristic'])) {
                            // Récupérer l'image associée pour la supprimer
                            $existingChar = $manager->getCaracteristicById($charId);
                            if (!empty($existingChar['image'])) {
                                $filePath = $uploadDirs['characteristics'] . $existingChar['image'];
                                if (file_exists($filePath)) {
                                    unlink($filePath);
                                }
                            }
                            // Supprimer la caractéristique
                            $manager->deleteCaracteristic($charId);
                            continue;
                        }

                        $charImage = $_POST['existing_char_image'][$index] ?? '';

                        // Supprimer l'image si demandé
                        if (isset($_POST['delete_char_image']) && in_array($charId, $_POST['delete_char_image']) && !empty($charImage)) {
                            $filePath = $uploadDirs['characteristics'] . $charImage;
                            if (file_exists($filePath)) {
                                unlink($filePath);
                            }
                            $charImage = '';
                        }

                        // Traiter la nouvelle image
                        if (isset($_FILES['char_image']['tmp_name'][$index]) && $_FILES['char_image']['error'][$index] === UPLOAD_ERR_OK) {
                            // Supprimer l'ancienne image si elle existe
                            if (!empty($charImage)) {
                                $oldFilePath = $uploadDirs['characteristics'] . $charImage;
                                if (file_exists($oldFilePath)) {
                                    unlink($oldFilePath);
                                }
                            }

                            $charImage = time() . '_' . basename($_FILES['char_image']['name'][$index]);
                            move_uploaded_file(
                                $_FILES['char_image']['tmp_name'][$index],
                                $uploadDirs['characteristics'] . $charImage
                            );
                        }

                        // Mettre à jour la caractéristique
                        $characteristicData = [
                            'title' => htmlspecialchars($_POST['existing_char_title'][$index]),
                            'image' => $charImage,
                            'description' => htmlspecialchars($_POST['existing_char_description'][$index] ?? '')
                        ];

                        $manager->updateCaracteristic($charId, $characteristicData);
                    }
                }

                // Traitement des nouvelles caractéristiques
                if (isset($_POST['characteristic_title'])) {
                    foreach ($_POST['characteristic_title'] as $key => $title) {
                        if (!empty($title)) {
                            $characteristicImage = '';
                            if (
                                isset($_FILES['characteristic_image']['tmp_name'][$key]) &&
                                $_FILES['characteristic_image']['error'][$key] === UPLOAD_ERR_OK
                            ) {

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

                // Traitement des vidéos existantes
                if (isset($_POST['existing_video_id'])) {
                    foreach ($_POST['existing_video_id'] as $index => $videoId) {
                        // Vérifier si la vidéo doit être supprimée
                        if (isset($_POST['delete_video']) && in_array($videoId, $_POST['delete_video'])) {
                            // Récupérer le fichier vidéo associé pour le supprimer
                            $existingVideo = $manager->getVideoById($videoId);
                            if (!empty($existingVideo['video_url']) && !filter_var($existingVideo['video_url'], FILTER_VALIDATE_URL)) {
                                $filePath = $uploadDirs['videos'] . $existingVideo['video_url'];
                                if (file_exists($filePath)) {
                                    unlink($filePath);
                                }
                            }
                            // Supprimer la vidéo
                            $manager->deleteVideo($videoId);
                            continue;
                        }

                        $videoUrl = $_POST['existing_video_url'][$index] ?? '';

                        // Supprimer le fichier vidéo si demandé
                        if (
                            isset($_POST['delete_video_file']) && in_array($videoId, $_POST['delete_video_file']) &&
                            !empty($videoUrl) && !filter_var($videoUrl, FILTER_VALIDATE_URL)
                        ) {
                            $filePath = $uploadDirs['videos'] . $videoUrl;
                            if (file_exists($filePath)) {
                                unlink($filePath);
                            }
                            $videoUrl = '';
                        }

                        // Traiter la nouvelle vidéo (fichier)
                        if (isset($_FILES['video']['tmp_name'][$index]) && $_FILES['video']['error'][$index] === UPLOAD_ERR_OK) {
                            // Supprimer l'ancien fichier s'il existe
                            if (!empty($videoUrl) && !filter_var($videoUrl, FILTER_VALIDATE_URL)) {
                                $oldFilePath = $uploadDirs['videos'] . $videoUrl;
                                if (file_exists($oldFilePath)) {
                                    unlink($oldFilePath);
                                }
                            }

                            $videoUrl = time() . '_' . basename($_FILES['video']['name'][$index]);
                            move_uploaded_file(
                                $_FILES['video']['tmp_name'][$index],
                                $uploadDirs['videos'] . $videoUrl
                            );
                        }

                        // Mettre à jour la vidéo
                        $videoData = [
                            'video_url' => $videoUrl,
                            'texte' => htmlspecialchars($_POST['existing_video_text'][$index] ?? '')
                        ];

                        $manager->updateVideo($videoId, $videoData);
                    }
                }

                // Traitement des nouvelles vidéos
                if (isset($_FILES['new_video'])) {
                    foreach ($_FILES['new_video']['tmp_name'] as $key => $tmp_name) {
                        if ($_FILES['new_video']['error'][$key] === UPLOAD_ERR_OK) {
                            $videoName = time() . '_' . basename($_FILES['new_video']['name'][$key]);
                            if (move_uploaded_file($tmp_name, $uploadDirs['videos'] . $videoName)) {
                                $videoData = [
                                    'product_id' => $productId,
                                    'video_url' => $videoName,
                                    'texte' => htmlspecialchars($_POST['new_video_text'][$key] ?? '')
                                ];

                                $manager->createVideos($videoData);
                            }
                        }
                    }
                }

                $message = "Produit mis à jour avec succès !";
                header('Location: index.php?message=' . urlencode($message));
                exit;
            } catch (Exception $e) {
                $message = "Erreur lors de la mise à jour du produit : " . $e->getMessage();
                header('Location: update.php?id=' . $productId . '&error=' . urlencode($message));
                exit;
            }
        }
        break;

    default:
        header('Location: index.php?error=' . urlencode("Action non reconnue"));
        exit;
}

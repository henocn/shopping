<?php
require '../../vendor/autoload.php';
require '../../utils/middleware.php';

verifyConnection("/management/products/update.php");
checkAdminAccess($_SESSION['user_id']);
checkIsActive($_SESSION['user_id']);


use src\Connectbd;
use src\Product;
use src\User;

$cnx = Connectbd::getConnection();
$manager = new Product($cnx);

$userManager = new User($cnx);
$helpers = $userManager->getUsersByRole(0);

// Récupérer la liste des pays
$countryStmt = $cnx->prepare("SELECT id, code, name FROM countries ORDER BY name ASC");
$countryStmt->execute();
$countries = $countryStmt->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_GET['id'])) {
    die("Produit introuvable.");
}

$productId = intval($_GET['id']);
$productInfo = $manager->getAllProductInfoById($productId);

$product = $productInfo['product'];
$videos = $productInfo['videos'];
$caracteristics = $productInfo['caracteristics'];
$packs = $productInfo['packs'];

// Récupérer les managers et pays associés au produit
$productManagers = $manager->getProductManagers($productId);
$productCountries = $manager->getProductCountries($productId);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/index.css" rel="stylesheet">
    <link href="../../assets/css/products.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link href="../../assets/css/add-product.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        .existing-media {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .existing-media img {
            max-width: 100px;
            max-height: 100px;
            margin-right: 10px;
        }

        .delete-checkbox {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <?php include '../../includes/navbar.php'; ?>

    <main class="container my-4">
        <h2 class="mb-4 text-center">Modifier le produit</h2>


        <form id="productForm" enctype="multipart/form-data" class="form-container" method="POST" action="save.php">
            <!-- Champs cachés pour les images -->
            <input type="file" id="mainImageInput" name="mainImage" style="display: none;" accept="image/*">
            <input type="file" id="carouselImagesInput" name="carouselImages[]" style="display: none;" accept="image/*" multiple>

            <div class="floating-actions">
                <button type="button" class="floating-btn" onclick="toggleSection('carousel')" title="Modifier les images">
                    <i class='bx bx-images'></i>
                </button>
                <button type="button" class="floating-btn" onclick="toggleSection('characteristics')" title="Modifier les caractéristiques">
                    <i class='bx bx-list-plus'></i>
                </button>
                <button type="button" class="floating-btn" onclick="toggleSection('videos')" title="Modifier les vidéos">
                    <i class='bx bx-video-plus'></i>
                </button>
                <button type="button" class="floating-btn" onclick="toggleSection('packs')" title="Ajouter des packs">
                    <i class='bx bx-package'></i>
                </button>

            </div>

            <!-- Informations de base -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-purchase-tag'></i> Nom du produit (FR)
                        </label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-purchase-tag'></i> Nom du produit (AR)
                        </label>
                        <input type="text" class="form-control" name="ar_name" value="<?= htmlspecialchars($product['ar_name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-dollar'></i> Prix d'achat
                        </label>
                        <input type="number" class="form-control" name="purchase_price" value="<?= $product['purchase_price'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-dollar'></i> Livraison
                        </label>
                        <input type="number" class="form-control" name="shipping_price" value="<?= $product['shipping_price'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-package'></i> Quantité
                        </label>
                        <input type="number" class="form-control" name="quantity" value="<?= $product['quantity'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-user'></i> Assistants de vente (sélection multiple)
                        </label>
                        <select class="form-select" name="manager_ids[]" multiple required>
                            <?php
                            $selectedManagerIds = array_column($productManagers, 'id');
                            foreach ($helpers as $helper) { ?>
                                <option value="<?= $helper['id'] ?>" <?= in_array($helper['id'], $selectedManagerIds) ? 'selected' : '' ?>><?= $helper['name'] ?> (<?= $helper['country'] ?>)</option>
                            <?php } ?>
                        </select>
                        <small class="form-text text-muted">Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs assistants</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-flag'></i> Pays de vente (sélection multiple)
                        </label>
                        <div style="border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 10px; max-height: 200px; overflow-y: auto;">
                            <?php 
                            $selectedCountryIds = array_column($productCountries, 'id');
                            $countryPrices = [];
                            foreach ($productCountries as $pc) {
                                $countryPrices[$pc['id']] = $pc['selling_price'];
                            }
                            foreach ($countries as $country) { 
                                $isSelected = in_array($country['id'], $selectedCountryIds);
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="country_ids[]" value="<?= $country['id'] ?>" id="country_<?= $country['id'] ?>" <?= $isSelected ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="country_<?= $country['id'] ?>">
                                        <?= htmlspecialchars($country['code'] . ' - ' . $country['name']) ?>
                                    </label>
                                    <input type="number" class="form-control form-control-sm" placeholder="Prix de vente" name="country_prices[<?= $country['id'] ?>]" value="<?= $countryPrices[$country['id']] ?? '' ?>" style="<?= $isSelected ? 'display:block' : 'display:none' ?>; width: 120px; margin-left: 20px;" id="price_<?= $country['id'] ?>">
                                </div>
                            <?php } ?>
                        </div>
                        <small class="form-text text-muted">Sélectionnez les pays et renseignez les prix de vente</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-image'></i> Image principale
                        </label>

                        <!-- Affichage de l'image existante -->
                        <?php if (!empty($product['image'])): ?>
                            <div class="existing-media mb-3">
                                <img src="../../uploads/main/<?= $product['image'] ?>" alt="Image principale">
                                <span><?= $product['image'] ?></span>
                                <div class="form-check delete-checkbox">
                                    <input class="form-check-input" type="checkbox" name="delete_main_image" id="deleteMainImage">
                                    <label class="form-check-label" for="deleteMainImage">
                                        Supprimer cette image
                                    </label>
                                </div>
                                <input type="hidden" name="existing_main_image" value="<?= $product['image'] ?>">
                            </div>
                        <?php endif; ?>

                        <div class="custom-file-input" id="mainImageUpload">
                            <i class='bx bx-upload'></i>
                            <p>Cliquez ou déposez une nouvelle image ici</p>
                        </div>
                        <div id="mainImagePreview"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-text'></i> Description (FR)
                        </label>
                        <textarea id="summernote" class="form-control" name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-text'></i> Description (AR)
                        </label>
                        <textarea id="summernote-ar" class="form-control" name="ar_description" rows="4"><?= htmlspecialchars($product['ar_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Carousel (Images additionnelles) -->
            <div class="card mb-4" id="carouselSection" style="display: none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Images additionnelles</h5>
                    <button type="button" class="btn-close" onclick="toggleSection('carousel')"></button>
                </div>
                <div class="card-body">
                    <!-- Affichage des images existantes du carousel -->
                    <div class="mb-3">
                        <label class="form-label">Images existantes</label>
                        <?php
                        $carouselImages = [
                            $product['carousel1'],
                            $product['carousel2'],
                            $product['carousel3'],
                            $product['carousel4'],
                            $product['carousel5']
                        ];

                        foreach ($carouselImages as $index => $image):
                            if (!empty($image)):
                        ?>
                                <div class="existing-media mb-2">
                                    <img src="../../uploads/carousel/<?= $image ?>" alt="Image carousel <?= $index + 1 ?>">
                                    <span><?= $image ?></span>
                                    <div class="form-check delete-checkbox">
                                        <input class="form-check-input" type="checkbox" name="delete_carousel_images[]" value="<?= $image ?>" id="deleteCarousel<?= $index ?>">
                                        <label class="form-check-label" for="deleteCarousel<?= $index ?>">
                                            Supprimer cette image
                                        </label>
                                    </div>
                                    <input type="hidden" name="existing_carousel_images[]" value="<?= $image ?>">
                                </div>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-images'></i> Nouvelles images du carousel (5 images maximum)
                        </label>
                        <div class="custom-file-input" id="carouselImageUpload">
                            <i class='bx bx-upload'></i>
                            <p>Cliquez ou déposez les images ici</p>
                        </div>
                        <div class="carousel-preview" id="carouselPreview"></div>
                    </div>
                </div>
            </div>

            <!-- Caractéristiques -->
            <div class="card mb-4" id="characteristicsSection" style="display: none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Caractéristiques</h5>
                    <button type="button" class="btn-close" onclick="toggleSection('characteristics')"></button>
                </div>
                <div class="card-body">
                    <!-- Affichage des caractéristiques existantes -->
                    <div id="existingCharacteristics">
                        <?php foreach ($caracteristics as $index => $char): ?>
                            <div class="characteristic-item mb-3 p-3 border rounded">
                                <input type="hidden" name="existing_char_id[]" value="<?= $char['id'] ?>">
                                <div class="mb-3">
                                    <label class="form-label">Titre</label>
                                    <input type="text" class="form-control" name="existing_char_title[]" value="<?= htmlspecialchars($char['title']) ?>" required>
                                </div>

                                <?php if (!empty($char['image'])): ?>
                                    <div class="existing-media mb-2">
                                        <img src="../../uploads/characteristics/<?= $char['image'] ?>" alt="Image caractéristique">
                                        <span><?= $char['image'] ?></span>
                                        <div class="form-check delete-checkbox">
                                            <input class="form-check-input" type="checkbox" name="delete_char_image[]" value="<?= $char['id'] ?>" id="deleteCharImage<?= $index ?>">
                                            <label class="form-check-label" for="deleteCharImage<?= $index ?>">
                                                Supprimer cette image
                                            </label>
                                        </div>
                                        <input type="hidden" name="existing_char_image[]" value="<?= $char['image'] ?>">
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label">Nouvelle image (optionnel)</label>
                                    <input type="file" class="form-control" name="char_image[]">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="existing_char_description[]" rows="3"><?= htmlspecialchars($char['description']) ?></textarea>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="delete_characteristic[]" value="<?= $char['id'] ?>" id="deleteChar<?= $index ?>">
                                    <label class="form-check-label" for="deleteChar<?= $index ?>">
                                        Supprimer cette caractéristique
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="btn mb-3" onclick="addCharacteristic()" style="background: var(--secondary); color: white;">
                        <i class='bx bx-plus'></i> Ajouter une caractéristique
                    </button>
                    <div id="characteristicsList"></div>
                </div>
            </div>
            <!-- Vidéos -->
            <div class="card mb-4" id="videosSection" style="display: none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Vidéos</h5>
                    <button type="button" class="btn-close" onclick="toggleSection('videos')"></button>
                </div>
                <div class="card-body">
                    <!-- Affichage des vidéos existantes -->
                    <div id="existingVideos">
                        <?php foreach ($videos as $index => $video): ?>
                            <div class="characteristic-item mb-3 p-3 border rounded">
                                <input type="hidden" name="existing_video_id[]" value="<?= $video['id'] ?>">

                                <?php if (!empty($video['video_url']) && !filter_var($video['video_url'], FILTER_VALIDATE_URL)): ?>
                                    <div class="existing-media mb-2">
                                        <span>Vidéo: <?= $video['video_url'] ?></span>
                                        <div class="form-check delete-checkbox">
                                            <input class="form-check-input" type="checkbox" name="delete_video_file[]" value="<?= $video['id'] ?>" id="deleteVideoFile<?= $index ?>">
                                            <label class="form-check-label" for="deleteVideoFile<?= $index ?>">
                                                Supprimer cette vidéo
                                            </label>
                                        </div>
                                        <input type="hidden" name="existing_video_url[]" value="<?= $video['video_url'] ?>">
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label"><?= filter_var($video['video_url'], FILTER_VALIDATE_URL) ? 'URL Vidéo' : 'Nouvelle vidéo (fichier ou URL)' ?></label>
                                    <?php if (filter_var($video['video_url'], FILTER_VALIDATE_URL)): ?>
                                        <input type="url" class="form-control" name="existing_video_url[]" value="<?= $video['video_url'] ?>">
                                    <?php else: ?>
                                        <input type="file" class="form-control" name="existing_video_file[]" accept="video/*">
                                    <?php endif; ?>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Texte</label>
                                    <textarea class="form-control" name="existing_video_text[]" rows="3"><?= htmlspecialchars($video['texte']) ?></textarea>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="delete_video[]" value="<?= $video['id'] ?>" id="deleteVideo<?= $index ?>">
                                    <label class="form-check-label" for="deleteVideo<?= $index ?>">
                                        Supprimer cette vidéo
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="btn mb-3" onclick="addVideo()" style="background: var(--secondary); color: white;">
                        <i class='bx bx-plus'></i> Ajouter une vidéo
                    </button>
                    <div id="videosList"></div>
                </div>
            </div>

            <!-- Packs -->
            <div class="card mb-4" id="packsSection" style="display: none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Packs</h5>
                    <button type="button" class="btn-close" onclick="toggleSection('packs')"></button>
                </div>
                <div class="card-body">
                    <!-- Packs existants -->
                    <div id="existingPacks">
                        <?php foreach ($packs as $index => $pack): ?>
                            <div class="pack-item mb-3 p-3 border rounded">
                                <input type="hidden" name="existing_pack_id[]" value="<?= $pack['id'] ?>">

                                <!-- Nom -->
                                <div class="mb-3">
                                    <label class="form-label">Nom du Pack</label>
                                    <input type="text" class="form-control" name="existing_pack_name[]"
                                        value="<?= htmlspecialchars($pack['name']) ?>" required>
                                </div>

                                <!-- Image existante -->
                                <?php if (!empty($pack['image'])): ?>
                                    <div class="existing-media mb-2">
                                        <img src="../../uploads/packs/<?= $pack['image'] ?>" alt="Image pack" style="max-height:100px;">
                                        <span><?= $pack['image'] ?></span>
                                        <div class="form-check delete-checkbox">
                                            <input class="form-check-input" type="checkbox"
                                                name="delete_pack_image[]"
                                                value="<?= $pack['id'] ?>"
                                                id="deletePackImage<?= $index ?>">
                                            <label class="form-check-label" for="deletePackImage<?= $index ?>">
                                                Supprimer cette image
                                            </label>
                                        </div>
                                        <input type="hidden" name="existing_pack_image[]" value="<?= $pack['image'] ?>">
                                    </div>
                                <?php endif; ?>

                                <!-- Nouvelle image -->
                                <div class="mb-3">
                                    <label class="form-label">Nouvelle image (optionnel)</label>
                                    <input type="file" class="form-control pack-image-input"
                                        name="existing_pack_image_file[]" accept="image/*"
                                        onchange="previewPackImage(this)">
                                    <div class="pack-image-preview mt-2"></div>
                                </div>

                                <!-- Quantité -->
                                <div class="mb-3">
                                    <label class="form-label">Quantité</label>
                                    <input type="number" class="form-control"
                                        name="existing_pack_quantity[]"
                                        value="<?= $pack['quantity'] ?>" min="1">
                                </div>

                                <!-- Réduction -->
                                <div class="mb-3">
                                    <label class="form-label">Prix du pack</label>
                                    <input type="number" class="form-control"
                                        name="existing_pack_price[]"
                                        value="<?= $pack['price'] ?>" step="0.01">
                                </div>

                                <!-- Supprimer pack -->
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox"
                                        name="delete_pack[]"
                                        value="<?= $pack['id'] ?>"
                                        id="deletePack<?= $index ?>">
                                    <label class="form-check-label" for="deletePack<?= $index ?>">
                                        Supprimer ce pack
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Bouton pour ajouter un pack -->
                    <button type="button" class="btn mb-3" onclick="addPack()"
                        style="background: var(--secondary); color: white;">
                        <i class='bx bx-package'></i> Ajouter un pack
                    </button>

                    <!-- Zone pour packs ajoutés dynamiquement -->
                    <div id="packsList"></div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">

                <input type="hidden" name="productId" value="<?= $productId ?>">

                <a href="index.php" class="btn btn-secondary me-md-2">Annuler</a>
                <input type="submit" value="Mettre a jour le produit" name="valider" class="btn" style="background: var(--primary); color: white;">
            </div>
        </form>
    </main>

    <?php include '../../includes/footer.php'; ?>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/add-product.js"></script>
    
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#summernote').summernote();
            $('#summernote-ar').summernote();
            
            // Toggle price input visibility based on checkbox
            document.querySelectorAll('input[name="country_ids[]"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const priceInput = document.getElementById('price_' + this.value);
                    if (this.checked) {
                        priceInput.style.display = 'block';
                        priceInput.required = true;
                    } else {
                        priceInput.style.display = 'none';
                        priceInput.required = false;
                    }
                });
            });
        });
    </script>


</body>

</html>
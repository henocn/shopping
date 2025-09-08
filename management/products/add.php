<?php
require '../../vendor/autoload.php';
require '../../utils/middleware.php';

verifyConnection("/shopping/management/products/add.php");
checkAdminAccess($_SESSION['user_id']);
checkIsActive($_SESSION['user_id']);

use src\Connectbd;
use src\User;

$cnx = Connectbd::getConnection();
$userManager = new User($cnx);
$helpers = $userManager->getUsersByRole(0);


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/index.css" rel="stylesheet">
    <link href="../../assets/css/products.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link href="../../assets/css/add-product.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
</head>

<body>
    <?php include '../../includes/navbar.php'; ?>

    <main class="container my-4">
        <h2 class="mb-4 text-center">Ajouter un nouveau produit</h2>

        <form id="productForm" enctype="multipart/form-data" class="form-container" method="POST" action="save.php">
            <!-- Champs cachés pour les images -->
            <input type="file" id="mainImageInput" name="mainImage" style="display: none;" accept="image/*">
            <input type="file" id="carouselImagesInput" name="carouselImages[]" style="display: none;" accept="image/*" multiple>

            <div class="floating-actions">
                <button type="button" class="floating-btn" onclick="toggleSection('carousel')" title="Ajouter des images">
                    <i class='bx bx-images'></i>
                </button>
                <button type="button" class="floating-btn" onclick="toggleSection('characteristics')" title="Ajouter des caractéristiques">
                    <i class='bx bx-list-plus'></i>
                </button>
                <button type="button" class="floating-btn" onclick="toggleSection('videos')" title="Ajouter des vidéos">
                    <i class='bx bx-video-plus'></i>
                </button>
                <button type="button" class="floating-btn" onclick="toggleSection('packs')" title="Ajouter des packs">
                    <i class='bx bx-package-plus'></i>
                </button>
            </div>

            <!-- Informations de base -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-purchase-tag'></i> Nom du produit
                        </label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-dollar'></i> Prix
                        </label>
                        <input type="number" class="form-control" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-flag'></i> Pays de vente
                        </label>
                        <select class="form-select" name="country" required>
                            <option value= '' selected>------------</option>
                            <option value="GN">🇬🇳 Guinée</option>
                            <option value="TD">🇹🇩 Tchad</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-user'></i> Assistante de vente
                        </label>
                        <select class="form-select" name="manager_id" required>
                            <option value= '' selected>------------</option>
                            <?php
                            foreach ($helpers as $helper) { ?>
                                <option value=<?= $helper['id'] ?>><?= $helper['name'] ?> (<?= $helper['country'] ?>)</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-image'></i> Image principale
                        </label>
                        <div class="custom-file-input" id="mainImageUpload">
                            <i class='bx bx-upload'></i>
                            <p>Cliquez ou déposez l'image ici</p>
                        </div>
                        <div id="mainImagePreview"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-text'></i> Description
                        </label>
                        <textarea id="description" class="form-control" name="description" rows="4"></textarea>
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
                    <div class="mb-3">
                        <label class="form-label">
                            <i class='bx bx-images'></i> Images du carousel (5 images maximum)
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
                    <div id="characteristics">
                        <button type="button" class="btn mb-3" onclick="addCharacteristic()" style="background: var(--secondary); color: white;">
                            <i class='bx bx-plus'></i> Ajouter une caractéristique
                        </button>
                        <div id="characteristicsList"></div>
                    </div>
                </div>
            </div>

            <!-- Vidéos -->
            <div class="card mb-4" id="videosSection" style="display: none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Vidéos</h5>
                    <button type="button" class="btn-close" onclick="toggleSection('videos')"></button>
                </div>
                <div class="card-body">
                    <div id="videos">
                        <button type="button" class="btn mb-3" onclick="addVideo()" style="background: var(--secondary); color: white;">
                            <i class='bx bx-plus'></i> Ajouter une vidéo
                        </button>
                        <div id="videosList"></div>
                    </div>
                </div>
            </div>

            <!-- Packs -->
            <div class="card mb-4" id="packsSection" style="display: none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Packs</h5>
                    <button type="button" class="btn-close" onclick="toggleSection('packs')"></button>
                </div>
                <div class="card-body">
                    <div id="packs">
                        <button type="button" class="btn mb-3" onclick="addPack()" style="background: var(--secondary); color: white;">
                            <i class='bx bx-package'></i> Ajouter un pack
                        </button>
                        <div id="packsList"></div>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <input type="submit" value="Enregistrer le produit" name="valider" class="btn" style="background: var(--primary); color: white;">
            </div>
        </form>
    </main>

    <?php include '../../includes/footer.php'; ?>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/add-product.js"></script>
</body>

</html>
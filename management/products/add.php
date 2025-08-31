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
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>

    <main class="container my-4">
        <h2 class="mb-4 text-center">Ajouter un nouveau produit</h2>

        <div class="step-indicator">
            <div class="step active">
                <div class="step-circle">1</div>
                <div>Informations</div>
            </div>
            <div class="step">
                <div class="step-circle">2</div>
                <div>Carousel</div>
            </div>
            <div class="step">
                <div class="step-circle">3</div>
                <div>Caractéristiques</div>
            </div>
            <div class="step">
                <div class="step-circle">4</div>
                <div>Vidéos</div>
            </div>
        </div>

        <form id="productForm" enctype="multipart/form-data" class="form-container">
            <!-- Champs cachés pour les images -->
            <input type="file" id="mainImageInput" name="mainImage" style="display: none;" accept="image/*">
            <input type="file" id="carouselImagesInput" name="carouselImages[]" style="display: none;" accept="image/*" multiple>
            
            <!-- Étape 1: Informations de base -->
            <div class="step-content active" id="step1">
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
                        <i class='bx bx-box'></i> Quantité
                    </label>
                    <input type="number" class="form-control" name="quantity" required>
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
                    <textarea class="form-control" name="description" rows="4"></textarea>
                </div>
                <div class="nav-buttons">
                    <div></div>
                    <button type="button" class="btn" onclick="nextStep(1)" style="background: var(--primary); color: white;">
                        Suivant <i class='bx bx-right-arrow-alt'></i>
                    </button>
                </div>
            </div>

            <!-- Étape 2: Carousel -->
            <div class="step-content" id="step2">
                <div class="mb-3">
                    <label class="form-label">
                        <i class='bx bx-images'></i> Images du carousel (5 images)
                    </label>
                    <div class="custom-file-input" id="carouselImageUpload">
                        <i class='bx bx-upload'></i>
                        <p>Cliquez ou déposez les images ici</p>
                    </div>
                    <div class="carousel-preview" id="carouselPreview"></div>
                </div>
                <div class="nav-buttons">
                    <button type="button" class="btn" onclick="prevStep(2)" style="background: var(--paper); color: var(--purple);">
                        <i class='bx bx-left-arrow-alt'></i> Précédent
                    </button>
                    <button type="button" class="btn" onclick="nextStep(2)" style="background: var(--primary); color: white;">
                        Suivant <i class='bx bx-right-arrow-alt'></i>
                    </button>
                </div>
            </div>

            <!-- Étape 3: Caractéristiques -->
            <div class="step-content" id="step3">
                <div id="characteristics">
                    <button type="button" class="btn mb-3" onclick="addCharacteristic()" style="background: var(--secondary); color: white;">
                        <i class='bx bx-plus'></i> Ajouter une caractéristique
                    </button>
                    <div id="characteristicsList"></div>
                </div>
                <div class="nav-buttons">
                    <button type="button" class="btn" onclick="prevStep(3)" style="background: var(--paper); color: var(--purple);">
                        <i class='bx bx-left-arrow-alt'></i> Précédent
                    </button>
                    <button type="button" class="btn" onclick="nextStep(3)" style="background: var(--primary); color: white;">
                        Suivant <i class='bx bx-right-arrow-alt'></i>
                    </button>
                </div>
            </div>

            <!-- Étape 4: Vidéos -->
            <div class="step-content" id="step4">
                <div id="videos">
                    <button type="button" class="btn mb-3" onclick="addVideo()" style="background: var(--secondary); color: white;">
                        <i class='bx bx-plus'></i> Ajouter une vidéo
                    </button>
                    <div id="videosList"></div>
                </div>
                <div class="nav-buttons">
                    <button type="button" class="btn" onclick="prevStep(4)" style="background: var(--paper); color: var(--purple);">
                        <i class='bx bx-left-arrow-alt'></i> Précédent
                    </button>
                    <button type="submit" class="btn" style="background: var(--primary); color: white;">
                        <i class='bx bx-check'></i> Enregistrer le produit
                    </button>
                </div>
            </div>
        </form>
    </main>

    <?php include '../../includes/footer.php'; ?>

    <script src="../../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../../assets/js/add-product.js"></script>
</body>
</html>

<?php
require 'vendor/autoload.php';

use src\Connectbd;
use src\Product;

$cnx = Connectbd::getConnection();
$productManager = new Product($cnx);


if (!isset($_GET['id'])) {
    $product = $productManager->getRandomProduct();
    $productId = intval($product['product_id']);
} else {
    $productId = intval($_GET['id']);
}


$product = $productManager->getProducts($productId);



if (!$product) {
    header('Location: error.php?code=404');
    exit;
}

$characteristics = $productManager->getProductCharacteristics($productId);
$videos = $productManager->getProductVideos($productId);
$packs = $productManager->getProductPacks($productId);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']); ?></title>
    <meta property="og:title" content="<?= htmlspecialchars($product['name']); ?>">
    <meta property="og:description" content="<?= htmlspecialchars($product['description']); ?>">
    <meta property="og:image" content="uploads/main/<?= $product['image']; ?>">

    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/product.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" rel="stylesheet">

    <!-- Vibrant.js -->
    <script src="https://cdn.jsdelivr.net/npm/node-vibrant@3.1.6/dist/vibrant.min.js"></script>

    <style id="dynamicStyles"></style>
</head>

<body>
    <!-- HEADER -->
    <nav class="navbar">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="#">
                <div class="logo-container">
                    <span class="logo-text">LUXE</span>
                    <span class="logo-text-accent">MARKET</span>
                </div>
            </a>
        </div>
    </nav>

    <!-- HERO -->
    <header class="product-hero">
        <img src="uploads/main/<?= $product['image']; ?>"
            alt="<?= htmlspecialchars($product['name']); ?>"
            class="hero-image" id="mainImage">
        <div class="hero-overlay">
            <h1><?= htmlspecialchars($product['name']); ?></h1>
            <div class="hero-price"><?= $product['price'] ?> FCFA</div>
            <div class="hero-cta">
                <button class="btn-hero btn-hero-primary" onclick="openOrderForm()">
                    <i class='bx bx-cart'></i>
                    Commander maintenant
                </button>
                <button class="btn-hero btn-hero-secondary" onclick="document.querySelector('.carousel-section').scrollIntoView({behavior: 'smooth'})">
                    <i class='bx bx-images'></i>
                    Voir les photos
                </button>
            </div>
        </div>
    </header>

    <!-- SECTION LUXEMARKET -->
    <section class="luxemarket-intro">
        <div class="container">
            <div class="intro-content">
                <h2 class="intro-title">LUXEMARKET</h2>
                <p class="intro-description">
                    Découvrez l'excellence du shopping en ligne avec LUXEMARKET.
                    Nous vous proposons une sélection premium de produits de qualité,
                    soigneusement choisis pour répondre à vos besoins les plus exigeants.
                    Une expérience d'achat unique, sécurisée et personnalisée.
                </p>
                <button class="btn-intro" onclick="openOrderForm()">
                    <i class='bx bx-shopping-bag'></i>
                    Commander maintenant
                </button>
            </div>
        </div>
    </section>

    <!-- CAROUSEL -->
    <section class="carousel-section">
        <div class="carousel-container">
            <div class="swiper mainSwiper">
                <div class="swiper-wrapper">
                    <?php if (!empty($product['image'])): ?>
                        <div class="swiper-slide">
                            <img src="uploads/main/<?= $product['image']; ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                        </div>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if (!empty($product['carousel' . $i])): ?>
                            <div class="swiper-slide">
                                <img src="uploads/carousel/<?= $product['carousel' . $i]; ?>" alt="Vue <?= $i; ?>">
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>

            <div class="swiper thumbSwiper">
                <div class="swiper-wrapper">
                    <?php if (!empty($product['image'])): ?>
                        <div class="swiper-slide">
                            <img src="uploads/main/<?= $product['image']; ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                        </div>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if (!empty($product['carousel' . $i])): ?>
                            <div class="swiper-slide">
                                <img src="uploads/carousel/<?= $product['carousel' . $i]; ?>" alt="Vue <?= $i; ?>">
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- INFOS PRODUIT -->
    <section class="product-info">
        <div class="product-description">
            <?= $product['description']; ?>
        </div>

        <!-- Boutons de partage social -->
        <div class="social-sharing">
            <h3>Partager ce produit :</h3>
            <button class="share-btn share-facebook" onclick="shareProduct('facebook')">
                <i class='bx bxl-facebook'></i>
            </button>
            <button class="share-btn share-twitter" onclick="shareProduct('twitter')">
                <i class='bx bxl-twitter'></i>
            </button>
            <button class="share-btn share-whatsapp" onclick="shareProduct('whatsapp')">
                <i class='bx bxl-whatsapp'></i>
            </button>
            <button class="share-btn share-copy" onclick="copyLink()">
                <i class='bx bx-link'></i>
            </button>
        </div>
    </section>

    <!-- CARACTÉRISTIQUES -->
    <?php if (!empty($characteristics)): ?>
        <section class="product-features">
            <h2>Caractéristiques</h2>
            <div class="features-grid">
                <?php foreach ($characteristics as $c): ?>
                    <div class="feature-card">
                        <?php if (!empty($c['image'])): ?>
                            <img src="uploads/characteristics/<?= $c['image']; ?>" alt="<?= htmlspecialchars($c['title']); ?>">
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($c['title']); ?></h3>
                        <p><?= htmlspecialchars($c['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- PACKS -->
    <?php if (!empty($packs)): ?>
        <section class="product-packs">
            <div class="container">
                <h2 class="section-title">Packs disponibles</h2>
                <p class="section-subtitle">Choisissez le pack qui correspond le mieux à vos besoins</p>
                
                <div class="packs-grid">
                    <?php foreach ($packs as $pack): ?>
                        <div class="pack-card" data-pack-id="<?= $pack['id']; ?>" onclick="selectPack(<?= $pack['id']; ?>, <?= $pack['price_reduction']; ?>, <?= $pack['quantity']; ?>)">
                            <!-- Image du pack -->
                            <?php if (!empty($pack['image'])): ?>
                                <div class="pack-image">
                                    <img src="uploads/packs/<?= $pack['image']; ?>" alt="<?= htmlspecialchars($pack['titre']); ?>" loading="lazy">
                                </div>
                            <?php endif; ?>
                            
                            <div class="pack-content">
                                <div class="pack-header">
                                    <h3 class="pack-title"><?= htmlspecialchars($pack['titre']); ?></h3>
                                    <div class="pack-quantity">
                                        <i class='bx bx-package'></i>
                                        <?= $pack['quantity']; ?> unités
                                    </div>
                                </div>
                                
                                <div class="pack-pricing">
                                <div class="price-comparison">
                                    <div class="price-normal">
                                        <span class="price-label">Prix normal</span>
                                        <span class="price-value"><?= number_format($pack['price_normal']); ?> FCFA</span>
                                    </div>
                                    <div class="price-reduction">
                                        <span class="price-label">Prix pack</span>
                                        <span class="price-value highlight"><?= number_format($pack['price_reduction']); ?> FCFA</span>
                                    </div>
                                </div>
                                
                                <div class="savings">
                                    <i class='bx bx-trending-down'></i>
                                    <span>Économisez <?= number_format($pack['price_normal'] - $pack['price_reduction']); ?> FCFA</span>
                                </div>
                                </div>
                            </div>
                            
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- VIDÉOS -->
    <?php if (!empty($videos)): ?>
        <section class="product-videos">
            <h2>Découvrez en vidéo</h2>
            <div class="videos-grid">
                <?php foreach ($videos as $v): ?>
                    <div class="video-card">
                        <video controls autoplay preload="metadata">
                            <source src="uploads/videos/<?= $v['video_url']; ?>" type="video/mp4">
                            Votre navigateur ne supporte pas la lecture vidéo.
                        </video>
                        <?php if (!empty($v['texte'])): ?>
                            <h3><?= htmlspecialchars($v['texte']); ?></h3>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- FOOTER -->
    <footer class="footer">
        <p>© <?= date('Y'); ?> - Votre boutique. Tous droits réservés.</p>
    </footer>

    <!-- BOUTON FIXE COMMANDER -->
    <button class="fixed-order-btn" onclick="openOrderForm()">
        <i class='bx bx-cart'></i> Commander
    </button>

    <!-- MODAL COMMANDE -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal">

                <!-- Header -->
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title" id="orderModalLabel">
                        <i class='bx bx-cart-alt'></i> Commander : <?= htmlspecialchars($product['name']); ?>
                    </h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Fermer">
                        <i class='bx bx-x'></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body custom-modal-body">
                    <form id="orderForm" action="management/orders/save.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">

                        <input type="hidden" name="quantity" value="1" id="selectedQuantity">
                        <input type="hidden" name="pack_id" value="" id="selectedPackId">
                        <input type="hidden" name="unit_price" value="<?= $product['price']; ?>" id="selectedUnitPrice">
                        <input type="hidden" name="total_price" value="<?= $product['price']; ?>" id="selectedTotalPrice">
                        <input type="hidden" name="client_country" value="<?= $product['country']; ?>">
                        <input type="hidden" name="manager_id" value="<?= $product['manager_id']; ?>">

                        <!-- Sélection de pack -->
                        <?php if (!empty($packs)): ?>
                        <div class="form-group">
                            <label class="form-label">Choisir un pack</label>
                            <select class="form-control-custom" name="pack_selection" id="packSelection" onchange="updatePackSelection()">
                                <option value="">Sélectionner un pack (optionnel)</option>
                                <?php foreach ($packs as $pack): ?>
                                    <option value="<?= $pack['id']; ?>" 
                                            data-price="<?= $pack['price_reduction']; ?>" 
                                            data-quantity="<?= $pack['quantity']; ?>"
                                            data-title="<?= htmlspecialchars($pack['titre']); ?>">
                                        <?= htmlspecialchars($pack['titre']); ?> - <?= $pack['quantity']; ?> unités - <?= number_format($pack['price_reduction']); ?> FCFA
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <!-- Pack sélectionné -->
                        <div class="selected-pack-info" id="selectedPackInfo" style="display: none;">
                            <div class="pack-summary">
                                <h4><i class='bx bx-package'></i> Pack sélectionné</h4>
                                <div class="pack-details">
                                    <span id="packTitle"></span>
                                    <span id="packQuantity"></span>
                                    <span id="packPrice"></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nom complet</label>
                            <input type="text" class="form-control-custom" name="client_name" placeholder="Votre nom complet" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" class="form-control-custom" name="client_phone" placeholder="Votre numéro de téléphone" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Adresse de livraison</label>
                            <input type="text" class="form-control-custom" name="client_adress" placeholder="Ville, Quartier" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Note evantuelles</label>
                            <textarea class="form-control-custom" name="client_note" rows="2" placeholder="Votre adresse complète"></textarea>
                        </div>
                        <input type="hidden" name="manager_id" value="<?= $product['manager_id']; ?>">
                        <input type="hidden" name="valider" value="commander">

                        <div class="modal-footer-custom">
                            <button type="submit" class="btn-submit-order">
                                <i class='bx bx-check-circle'></i>
                                <span>Valider la commande</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/product.js"></script>
    <script src="assets/js/theme.js"></script>
    
    <script>
        // Gestion de la sélection des packs
        function selectPack(packId, packPrice, quantity) {
            // Retirer la sélection précédente
            document.querySelectorAll('.pack-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Ajouter la sélection à la carte cliquée
            event.currentTarget.classList.add('selected');
            
            // Mettre à jour le dropdown
            const select = document.getElementById('packSelection');
            if (select) {
                select.value = packId;
            }
            
            // Mettre à jour les champs cachés
            document.getElementById('selectedPackId').value = packId;
            document.getElementById('selectedQuantity').value = quantity;
            document.getElementById('selectedUnitPrice').value = packPrice;
            document.getElementById('selectedTotalPrice').value = packPrice;
            
            // Afficher les informations du pack sélectionné
            const packInfo = document.getElementById('selectedPackInfo');
            const packTitle = document.getElementById('packTitle');
            const packQuantity = document.getElementById('packQuantity');
            const packPriceElement = document.getElementById('packPrice');
            
            const selectedCard = event.currentTarget;
            const title = selectedCard.querySelector('.pack-title').textContent;
            
            packTitle.textContent = title;
            packQuantity.textContent = quantity + ' unités';
            packPriceElement.textContent = new Intl.NumberFormat('fr-FR').format(packPrice) + ' FCFA';
            
            packInfo.style.display = 'block';
            
            // Ouvrir automatiquement le modal de commande
            openOrderForm();
        }
        
        // Fonction pour ouvrir le formulaire de commande
        function openOrderForm() {
            const modal = new bootstrap.Modal(document.getElementById('orderModal'));
            modal.show();
        }
        
        // Fonction pour mettre à jour la sélection de pack via le dropdown
        function updatePackSelection() {
            const select = document.getElementById('packSelection');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value === '') {
                // Aucun pack sélectionné
                document.getElementById('selectedPackId').value = '';
                document.getElementById('selectedQuantity').value = '1';
                document.getElementById('selectedUnitPrice').value = '<?= $product['price']; ?>';
                document.getElementById('selectedTotalPrice').value = '<?= $product['price']; ?>';
                document.getElementById('selectedPackInfo').style.display = 'none';
                
                // Retirer la sélection des cartes
                document.querySelectorAll('.pack-card').forEach(card => {
                    card.classList.remove('selected');
                });
            } else {
                // Pack sélectionné
                const packId = selectedOption.value;
                const packPrice = selectedOption.dataset.price;
                const packQuantity = selectedOption.dataset.quantity;
                const packTitle = selectedOption.dataset.title;
                
                // Mettre à jour les champs cachés
                document.getElementById('selectedPackId').value = packId;
                document.getElementById('selectedQuantity').value = packQuantity;
                document.getElementById('selectedUnitPrice').value = packPrice;
                document.getElementById('selectedTotalPrice').value = packPrice;
                
                // Afficher les informations du pack sélectionné
                const packInfo = document.getElementById('selectedPackInfo');
                const packTitleElement = document.getElementById('packTitle');
                const packQuantityElement = document.getElementById('packQuantity');
                const packPriceElement = document.getElementById('packPrice');
                
                packTitleElement.textContent = packTitle;
                packQuantityElement.textContent = packQuantity + ' unités';
                packPriceElement.textContent = new Intl.NumberFormat('fr-FR').format(packPrice) + ' FCFA';
                
                packInfo.style.display = 'block';
                
                // Mettre à jour la sélection visuelle des cartes
                document.querySelectorAll('.pack-card').forEach(card => {
                    card.classList.remove('selected');
                    if (card.dataset.packId === packId) {
                        card.classList.add('selected');
                    }
                });
            }
        }
    </script>

</body>

</html>
<?php
session_start();
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

if (isset($_SESSION['order_message'])) {
    $order_message = $_SESSION['order_message'];
    unset($_SESSION['order_message']);
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

    <meta property="og:title" content="<?= htmlspecialchars($product['name']); ?>" />
    <meta property="og:description"
        content="<?= htmlspecialchars(substr(strip_tags($product['description']), 0, 150)); ?>..." />
    <meta property="og:image" content="https://luxemarket.click/uploads/main/<?= $product['image']; ?>" />
    <meta property="og:url" content="https://luxemarket.click/product.php?id=<?= $product['id']; ?>" />
    <meta property="og:type" content="product" />
    <meta property="og:site_name" content="LuxeMarket" />
    <meta property="og:locale" content="fr_FR" />

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?= htmlspecialchars($product['name']); ?>" />
    <meta name="twitter:description"
        content="<?= htmlspecialchars(substr(strip_tags($product['description']), 0, 150)); ?>..." />
    <meta name="twitter:image" content="https://luxemarket.click/uploads/main/<?= $product['image']; ?>" />
    <meta name="twitter:site" content="@MaxoraMarket" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;500;700&display=swap">
    <link rel="stylesheet" href="assets/css/index2.css">
</head>

<body>

    <!-- Header/Navbar -->
    <header class="yc-header">
        <nav class="yc-navbar container">

            <div class="logo">
                <a href="/" aria-label="home">
                    <img src="assets/images/logo.jpg" alt="TUBKAL MARKET">
                </a>
            </div>
            <div>
                <span>Livraison gratuite.</span>
            </div>
            <div class="corner">
                <button class="commander-btn" onclick="location.href='#product_details'">Commander</button>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <section class="container product-layout">

            <!-- IMAGES -->
            <div class="product-images">
                <div class="main-image-wrapper">
                    <img id="main-image" src="uploads/main/<?= htmlspecialchars($product['image']); ?>">
                </div>

                <div class="carousel-grid">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if (!empty($product['carousel' . $i])): ?>
                            <div class="carousel-item">
                                <img src="uploads/carousel/<?= htmlspecialchars($product['carousel' . $i]) ?>"
                                    onclick="document.getElementById('main-image').src=this.src">
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- DETAILS + DESCRIPTION -->
            <div>
                <div class="product-details" id="product_details">
                    <h1 class="product-name"><?= htmlspecialchars($product['name']); ?></h1>
                    <h2 class="product-price"><?= number_format($product['selling_price'], 0, '', ' ') ?> CFA</h2>

                    <form class="express-checkout-form" method="POST" action="management/orders/save.php">
                        <div class="express-checkout-fields">
                            <input type="text" name="client_name" class="form-control-custom" placeholder="Nom complet"
                                required>
                            <div class="phone-input-wrapper">
                                <select name="client_country" class="form-control-country" required>
                                    <option value="TD" data-length="8">🇹🇩 +235</option>
                                    <option value="ML" data-length="8">🇲🇱 +223</option>
                                    <option value="GA" data-length="8">🇬🇦 +241</option>

                                </select>
                                <input type="tel" name="client_phone" class="form-control-custom" placeholder="Numéro"
                                    required>
                            </div>
                            <input type="text" name="client_adress" class="form-control-custom"
                                placeholder="Ville, Quartier" required>
                            <textarea name="client_note" class="form-control-custom" rows="2"
                                placeholder="Note éventuelle"></textarea>

                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                            <input type="hidden" name="valider" value="commander">

                        </div>
                        <div class="modal-footer-custom">
                            <button type="submit" class="btn-submit-order">
                                <i class='bx bx-check-circle'></i>
                                <span>Valider la commande</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="product-description">
                    <?= nl2br($product['description']); ?>
                </div>
            </div>

        </section>
    </main>


    <!-- Footer -->
    <footer>
        <div class="columns container">
            <div class="column logo">
                <img src="assets/images/logo.jpg" alt="TUBKAL MARKET" width="110" height="70">
            </div>
            <div class="column">
                <h1>À propos</h1>
                <a href="#">À propos de nous</a>
                <a href="#">Modes de paiement</a>
                <a href="#">Livraison</a>
            </div>

            <div class="column">
                <h1>A propos</h1>
                <h5>Nous somme une boutique en ligne</h5>
                <h5>Nous proposons des services d'achat</h5>
                <h5>Politique de confidentialité</h5>
            </div>
        </div>
        <div class="copyright-wrapper">
            <p><strong>Tous les droits réservés © Maxora Market 2025</strong></p>
        </div>
    </footer>

    <script>
        // Quantity controls
        document.querySelector('.increment-button').addEventListener('click', function () {
            const qty = document.getElementById('quantity');
            qty.value = parseInt(qty.value) + 1;
        });

        document.querySelector('.decrement-button').addEventListener('click', function () {
            const qty = document.getElementById('quantity');
            if (parseInt(qty.value) > 1) qty.value = parseInt(qty.value) - 1;
        });

        // Form submission
        document.getElementById('express-checkout-form').addEventListener('submit', function (e) {
            e.preventDefault();
            alert('Formulaire envoyé: ' + document.getElementById('first_name').value);
        });
    </script>
</body>

</html>
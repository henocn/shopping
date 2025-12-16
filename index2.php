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
    <meta name="twitter:site" content="@LuxeMarket" />

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
        <section class="container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; padding: 30px 0;">
            <!-- Product Images -->
            <div class="product-images">
                <div style="width: 100%; background-color: #f0f0f0; border-radius: 8px; overflow: hidden;">
                    <img id="main-image" src="uploads/main/<?= $product['image']; ?>"
                        alt="<?= htmlspecialchars($product['name']); ?> style=" width: 100%; height: auto; display:
                        block;">
                </div>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 15px;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if (!empty($product['carousel' . $i])): ?>
                            <img src="uploads/carousel/<?= htmlspecialchars($product['carousel' . $i]) ?>" alt="Image <?= $i ?>"
                                style="width: 100%; cursor: pointer; border-radius: 4px;"
                                onclick="document.getElementById('main-image').src=this.src">
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>

            </div>

            <!-- Product Details -->
            <div class="product-details" id="product_details">
                <h1 class="product-name"><?= htmlspecialchars($product['name']); ?></h1>

                <div class="product-price-container">
                    <h2 class="product-price"><?= number_format($product['selling_price'], 0, '', ' ') ?> CFA</h2>
                    <div
                        style="background: #5db2b9; color: white; padding: 5px 10px; border-radius: 4px; display: inline-block; font-size: 14px; margin-bottom: 20px;">
                        20% de réduction</div>
                </div>

                <div class="product-quantity">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Quantité</label>
                    <div class="quantity-field">
                        <button class="decrement-button">−</button>
                        <input type="number" class="quantity-input" id="quantity" name="quantity" min="1" value="1">
                        <button class="increment-button">+</button>
                    </div>
                </div>

                <form class="express-checkout-form" id="express-checkout-form" onsubmit="return false;">
                    <div class="express-checkout-fields">
                        <div style="color: var(--yc-neutral-color); margin-bottom: 10px; font-weight: 500;">Pour
                            commander, veuillez remplir ce formulaire et nous vous contacterons dans les plus brefs
                            délais !</div>

                        <div class="express-checkout-field">
                            <input type="text" name="first_name" id="first_name" placeholder="Nom complet" required>
                        </div>

                        <div class="express-checkout-field">
                            <input type="tel" name="phone" id="phone"
                                placeholder="Téléphone (joignable pour vous appeler)" required>
                        </div>

                        <div class="express-checkout-field">
                            <input type="text" name="city" id="city" placeholder="Ville" required>
                        </div>

                        <button type="submit" class="express-checkout-button" id="commander">Achetez maintenant</button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Product Description -->
        <section class="container" style="margin: 40px 0;">
            <div class="product-description">
                <p>La description du produit sera affichée ici depuis votre backend.</p>
                <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Nisi, ea ratione? Neque illum ea, facere
                    um quibusdam. Exercitationem non facilis saepe dolores distinctio hic accusantium
                    perspiciatis recusandae amet, delectus perferendis! Veniam, harum corrupti. Voluptates consequatur
                    maxime dolorem pariatur! Eum fugiat corrupti qui vero!</p>
            </div>
        </section>

        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="columns container">
            <div class="column logo">
                <img src="logo.jpg" alt="TUBKAL MARKET" width="110" height="70">
            </div>
            <div class="column">
                <h1>À propos</h1>
                <a href="/pages/about-us">À propos de nous</a>
                <a href="/pages/how-to-pay">Modes de paiement</a>
                <a href="/pages/shipping-delivery">Livraison</a>
            </div>

            <div class="column">
                <h1>A propos</h1>
                <h5>Nous somme une boutique en ligne</h5>
                <h5>Nous proposons des services d'achat</h5>
                <h5>Politique de confidentialité</h5>
            </div>
        </div>
        <div class="copyright-wrapper">
            <p><strong>Tous les droits réservés © Tubkal Market 2025</strong></p>
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
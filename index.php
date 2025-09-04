<?php
require 'vendor/autoload.php';

use src\Connectbd;
use src\Product;

if (!isset($_GET['id'])) {
    header('Location: error.php?message=' . urlencode('Aucun produit spécifié'));
    exit;
}

$productId = intval($_GET['id']);

$cnx = Connectbd::getConnection();
$productManager = new Product($cnx);

$product = $productManager->getProducts($productId);

if (!$product) {
    header('Location: error.php?message=' . urlencode('Produit non trouvé'));
    exit;
}

$characteristics = $productManager->getProductCharacteristics($productId);
$videos = $productManager->getProductVideos($productId);
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

    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/product.css" rel="stylesheet">
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
                <img src="assets/images/logo.png" alt="Logo">
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
            <div class="hero-price"><?= number_format($product['price'], 2); ?> €</div>
        </div>
    </header>

    <!-- CAROUSEL -->
    <section class="carousel-section">
        <div class="container">
            <div class="swiper mainSwiper">
                <div class="swiper-wrapper">
                    <?php if (!empty($product['image'])): ?>
                        <div class="swiper-slide">
                            <img src="uploads/main/<?= $product['image']; ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                        </div>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if (!empty($product['carousel'.$i])): ?>
                            <div class="swiper-slide">
                                <img src="uploads/carousel/<?= $product['carousel'.$i]; ?>" alt="Vue <?= $i; ?>">
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
                        <?php if (!empty($product['carousel'.$i])): ?>
                            <div class="swiper-slide">
                                <img src="uploads/carousel/<?= $product['carousel'.$i]; ?>" alt="Vue <?= $i; ?>">
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- INFOS PRODUIT -->
    <section class="product-info container">
        <div class="stock-status <?= $product['quantity'] > 0 ? 'in-stock' : 'out-stock'; ?>">
            <?= $product['quantity'] > 0 ? 'En stock' : 'Rupture de stock'; ?>
        </div>
        <div class="product-description">
            <?= $product['description']; ?>
        </div>
    </section>

    <!-- CARACTÉRISTIQUES -->
    <?php if (!empty($characteristics)): ?>
    <section class="product-features container">
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

    <!-- VIDÉOS -->
    <?php if (!empty($videos)): ?>
    <section class="product-videos container">
        <h2>Découvrez en vidéo</h2>
        <div class="videos-grid">
            <?php foreach ($videos as $v): ?>
                <div class="video-card">
                    <video controls>
                        <source src="uploads/videos/<?= $v['video_url']; ?>" type="video/mp4">
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

    <!-- JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="assets/js/product.js"></script>
    <script src="assets/js/theme.js"></script>
</body>
</html>

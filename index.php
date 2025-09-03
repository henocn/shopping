<?php
require 'vendor/autoload.php';

use src\Connectbd;
use src\Product;

// Vérifier si un ID de produit est fourni
if (!isset($_GET['id'])) {
    header('Location: error.php?message=' . urlencode('Aucun produit spécifié'));
    exit;
}

$productId = intval($_GET['id']);
$cnx = Connectbd::getConnection();
$productManager = new Product($cnx);

// Récupérer le produit
$product = $productManager->getProducts($productId);
if (!$product) {
    header('Location: error.php?message=' . urlencode('Produit non trouvé'));
    exit;
}

// Caractéristiques
$characteristics = $productManager->getProductCharacteristics($productId);

// Vidéos
$videos = $productManager->getProductVideos($productId);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <meta property="og:title" content="<?php echo htmlspecialchars($product['name']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($product['description']); ?>">
    <meta property="og:image" content="uploads/main/<?php echo $product['image']; ?>">

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Swiper -->
    <link href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Styles produit -->
    <link href="assets/css/product.css" rel="stylesheet">

    <!-- Color Thief -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.0/color-thief.umd.js"></script>
    <style id="dynamicStyles"></style>
</head>
<body>
    <!-- Header -->
    <header class="navbar py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand fw-bold" href="#">
                <img src="assets/images/logo.png" alt="Logo" height="40">
            </a>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="product-hero" 
        style="background-image: url('uploads/main/<?php echo $product['image']; ?>');">
        <div class="overlay"></div>
        <div class="hero-content text-center">
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="product-price"><?php echo number_format($product['price'], 2); ?> €</p>
            <button class="btn btn-primary" onclick="openOrderForm()">Commander</button>
        </div>
        <img src="uploads/main/<?php echo $product['image']; ?>" 
             alt="<?php echo htmlspecialchars($product['name']); ?>" 
             class="main-image d-none">
    </section>

    <!-- Carrousel -->
    <?php if (!empty($product['carousel1']) || !empty($product['carousel2']) || !empty($product['carousel3']) || !empty($product['carousel4']) || !empty($product['carousel5'])): ?>
    <section class="carousel-section py-5">
        <div class="container">
            <div class="swiper mainSwiper mb-3">
                <div class="swiper-wrapper">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if (!empty($product['carousel'.$i])): ?>
                            <div class="swiper-slide">
                                <img src="uploads/carousel/<?php echo $product['carousel'.$i]; ?>" alt="Vue <?php echo $i; ?>">
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>

            <div class="swiper thumbSwiper">
                <div class="swiper-wrapper">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if (!empty($product['carousel'.$i])): ?>
                            <div class="swiper-slide">
                                <img src="uploads/carousel/<?php echo $product['carousel'.$i]; ?>" alt="Miniature <?php echo $i; ?>">
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Description -->
    <section class="product-info py-5">
        <div class="container">
            <div class="stock-status <?php echo $product['quantity'] > 0 ? 'in-stock' : 'out-stock'; ?>">
                <?php echo $product['quantity'] > 0 ? 'En stock' : 'Rupture de stock'; ?>
            </div>
            <div class="product-description">
                <?php echo $product['description']; ?>
            </div>
        </div>
    </section>

    <!-- Caractéristiques -->
    <?php if (!empty($characteristics)): ?>
    <section class="product-features py-5">
        <div class="container">
            <h2 class="section-title">Caractéristiques</h2>
            <div class="features-grid">
                <?php foreach ($characteristics as $c): ?>
                <div class="feature-card">
                    <?php if (!empty($c['image'])): ?>
                    <img src="uploads/characteristics/<?php echo $c['image']; ?>" alt="<?php echo htmlspecialchars($c['title']); ?>">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($c['title']); ?></h3>
                    <p><?php echo htmlspecialchars($c['description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Vidéos -->
    <?php if (!empty($videos)): ?>
    <section class="product-videos py-5">
        <div class="container">
            <h2 class="section-title">Découvrez en vidéo</h2>
            <div class="videos-grid">
                <?php foreach ($videos as $video): ?>
                <div class="video-card">
                    <video controls>
                        <source src="uploads/videos/<?php echo $video['video_url']; ?>" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture de vidéos.
                    </video>
                    <?php if (!empty($video['texte'])): ?>
                    <h3><?php echo htmlspecialchars($video['texte']); ?></h3>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer text-center py-4">
        <div class="container">
            <p>© <?php echo date('Y'); ?> - Tous droits réservés</p>
        </div>
    </footer>

    <!-- Modal commande -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="orderForm" action="assistant/order.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Commander <?php echo htmlspecialchars($product['name']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                        <div class="mb-3">
                            <label for="name" class="form-label">Votre nom</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantité</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" max="<?php echo $product['quantity']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message (optionnel)</label>
                            <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Envoyer la commande</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="assets/js/product.js"></script>
</body>
</html>

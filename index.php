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

// Récupérer les détails du produit
$product = $productManager->getProducts($productId);

if (!$product) {
    header('Location: error.php?message=' . urlencode('Produit non trouvé'));
    exit;
}

// Récupérer les caractéristiques
$characteristics = $productManager->getProductCharacteristics($productId);

// Récupérer les vidéos
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
    
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/product.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.0/color-thief.umd.js"></script>
    <style id="dynamicStyles"></style>
<body>
        <nav class="navbar">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="#">
                <img src="assets/images/logo.png" alt="Logo">
            </a>
            <button class="order-btn" onclick="openOrderForm()">
                Commander maintenant
            </button>
        </div>
    </nav>

    <main>
        <div class="product-hero">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="product-gallery">
                            <div class="swiper mainSwiper">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <img src="uploads/main/<?php echo $product['image']; ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             class="main-image">
                                    </div>
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        $carouselImage = $product['carousel'.$i];
                                        if (!empty($carouselImage)) {
                                            echo '<div class="swiper-slide">';
                                            echo '<img src="uploads/carousel/' . $carouselImage . '" alt="Vue ' . $i . '">';
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                            <div class="swiper thumbSwiper mt-3">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <img src="uploads/main/<?php echo $product['image']; ?>" 
                                             alt="Miniature">
                                    </div>
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        $carouselImage = $product['carousel'.$i];
                                        if (!empty($carouselImage)) {
                                            echo '<div class="swiper-slide">';
                                            echo '<img src="uploads/carousel/' . $carouselImage . '" alt="Miniature ' . $i . '">';
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="product-info">
                            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                            <div class="product-price"><?php echo number_format($product['price'], 2); ?> €</div>
                            <div class="product-stock">
                                <?php echo $product['quantity'] > 0 ? 'En stock' : 'Rupture de stock'; ?>
                            </div>
                            
                            <div class="product-description mt-4">
                                <?php echo $product['description']; ?>
                            </div>

                            <div class="product-actions mt-4">
                                <button class="order-btn w-100" onclick="openOrderForm()">
                                    Commander maintenant
                                </button>
                            </div>

                            <div class="share-buttons mt-4">
                                <button class="btn-share" onclick="shareProduct('facebook')">
                                    <i class='bx bxl-facebook'></i>
                                </button>
                                <button class="btn-share" onclick="shareProduct('twitter')">
                                    <i class='bx bxl-twitter'></i>
                                </button>
                                <button class="btn-share" onclick="copyLink()">
                                    <i class='bx bx-link'></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($characteristics)): ?>
        <section class="product-features">
            <div class="container">
                <h2 class="section-title">Caractéristiques</h2>
                <div class="features-grid">
                    <?php foreach ($characteristics as $characteristic): ?>
                    <div class="feature-card">
                        <?php if (!empty($characteristic['image'])): ?>
                        <img src="uploads/characteristics/<?php echo $characteristic['image']; ?>" 
                             alt="<?php echo htmlspecialchars($characteristic['title']); ?>">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($characteristic['title']); ?></h3>
                        <p><?php echo htmlspecialchars($characteristic['description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if (!empty($videos)): ?>
        <section class="product-videos">
            <div class="container">
                <h2 class="section-title">Découvrez en vidéo</h2>
                <div class="videos-grid">
                    <?php foreach ($videos as $video): ?>
                    <div class="video-card">
                        <div class="video-wrapper">
                            <video controls>
                                <source src="uploads/videos/<?php echo $video['video_url']; ?>" type="video/mp4">
                                Votre navigateur ne supporte pas la lecture de vidéos.
                            </video>
                        </div>
                        <?php if (!empty($video['texte'])): ?>
                        <h3><?php echo htmlspecialchars($video['texte']); ?></h3>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <!-- Modal de commande -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Commander <?php echo htmlspecialchars($product['name']); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="orderForm" action="assistant/order.php" method="POST">
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
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   min="1" value="1" max="<?php echo $product['quantity']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message (optionnel)</label>
                            <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Envoyer la commande</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        // Gestionnaire de thème dynamique
        const colorThief = new ColorThief();
        const mainImage = document.querySelector('.main-image');

        function getLuminance(r, g, b) {
            const a = [r, g, b].map(v => {
                v /= 255;
                return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
            });
            return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
        }

        function getContrastRatio(l1, l2) {
            const lightest = Math.max(l1, l2);
            const darkest = Math.min(l1, l2);
            return (lightest + 0.05) / (darkest + 0.05);
        }

        function rgbToHsl(r, g, b) {
            r /= 255;
            g /= 255;
            b /= 255;
            const max = Math.max(r, g, b);
            const min = Math.min(r, g, b);
            let h, s, l = (max + min) / 2;

            if (max === min) {
                h = s = 0;
            } else {
                const d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                    case g: h = (b - r) / d + 2; break;
                    case b: h = (r - g) / d + 4; break;
                }
                h /= 6;
            }

            return [h * 360, s * 100, l * 100];
        }

        function adjustColor(color, lightness) {
            const [h, s, l] = rgbToHsl(...color);
            return `hsl(${h}, ${s}%, ${lightness}%)`;
        }

        function generateColorPalette(color) {
            const [h, s, l] = rgbToHsl(...color);
            return {
                primary: `hsl(${h}, ${s}%, ${l}%)`,
                primaryLight: `hsl(${h}, ${s}%, ${Math.min(l + 15, 95)}%)`,
                primaryDark: `hsl(${h}, ${s}%, ${Math.max(l - 15, 10)}%)`,
                surface: `hsl(${h}, ${Math.max(s - 50, 5)}%, 98%)`,
                surfaceAlt: `hsl(${h}, ${Math.max(s - 45, 8)}%, 95%)`,
                text: `hsl(${h}, ${Math.min(s + 10, 100)}%, 10%)`,
                textLight: `hsl(${h}, ${Math.min(s + 5, 100)}%, 30%)`,
            };
        }

        function applyTheme(palette) {
            const styleSheet = document.getElementById('dynamicStyles');
            styleSheet.textContent = `
                :root {
                    --primary: ${palette.primary};
                    --primary-light: ${palette.primaryLight};
                    --primary-dark: ${palette.primaryDark};
                    --surface: ${palette.surface};
                    --surface-alt: ${palette.surfaceAlt};
                    --text: ${palette.text};
                    --text-light: ${palette.textLight};
                }
            `;
        }

        // Attendre que l'image soit chargée
        mainImage.addEventListener('load', function() {
            try {
                const dominantColor = colorThief.getColor(mainImage);
                const palette = generateColorPalette(dominantColor);
                applyTheme(palette);
            } catch (e) {
                console.error('Erreur lors de l\'extraction des couleurs:', e);
            }
        });

        if (mainImage.complete) {
            mainImage.dispatchEvent(new Event('load'));
        }

        // Configuration Swiper
        var thumbSwiper = new Swiper(".thumbSwiper", {
            spaceBetween: 10,
            slidesPerView: 4,
            watchSlidesProgress: true,
        });

        var mainSwiper = new Swiper(".mainSwiper", {
            spaceBetween: 10,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            thumbs: {
                swiper: thumbSwiper,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });

        // Fonctions de partage
        function shareProduct(platform) {
            const url = window.location.href;
            const title = '<?php echo htmlspecialchars($product['name']); ?>';
            
            if (platform === 'facebook') {
                window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
            } else if (platform === 'twitter') {
                window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank');
            }
        }

        function copyLink() {
            navigator.clipboard.writeText(window.location.href)
                .then(() => alert('Lien copié !'));
        }

        function openOrderForm() {
            var orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
            orderModal.show();
        }

        // Gestion du formulaire de commande
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Votre commande a été envoyée avec succès ! Nous vous contacterons bientôt.');
                    bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
                } else {
                    alert('Une erreur est survenue : ' + data.message);
                }
            })
            .catch(error => {
                alert('Une erreur est survenue lors de l\'envoi de la commande.');
            });
        });
    </script>
</body>
</html>

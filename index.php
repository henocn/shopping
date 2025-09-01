<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nike Air Max 2023 - Edition Limitée</title>
    <meta property="og:title" content="Nike Air Max 2023 - Edition Limitée">
    <meta property="og:description" content="Découvrez la nouvelle Nike Air Max 2023, confort et style réunis dans une édition limitée exclusive.">
    <meta property="og:image" content="assets/images/products/example/main.jpg">
    
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/index.css" rel="stylesheet">
    <link href="assets/css/navbar.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main>
        <div class="product-hero" id="colorThemeContainer">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="product-gallery">
                            <div class="swiper mainSwiper">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <img src="assets/images/products/example/main.jpg" alt="Nike Air Max 2023" class="main-image">
                                    </div>
                                    <div class="swiper-slide">
                                        <img src="assets/images/products/example/carousel-1.jpg" alt="Vue latérale">
                                    </div>
                                    <div class="swiper-slide">
                                        <img src="assets/images/products/example/carousel-2.jpg" alt="Vue de dos">
                                    </div>
                                    <div class="swiper-slide">
                                        <img src="assets/images/products/example/carousel-3.jpg" alt="Vue de dessus">
                                    </div>
                                </div>
                            </div>
                            <div class="swiper thumbSwiper mt-3">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <img src="assets/images/products/example/main.jpg" alt="Miniature">
                                    </div>
                                    <div class="swiper-slide">
                                        <img src="assets/images/products/example/carousel-1.jpg" alt="Miniature">
                                    </div>
                                    <div class="swiper-slide">
                                        <img src="assets/images/products/example/carousel-2.jpg" alt="Miniature">
                                    </div>
                                    <div class="swiper-slide">
                                        <img src="assets/images/products/example/carousel-3.jpg" alt="Miniature">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="product-info">
                            <h1 class="product-title">Nike Air Max 2023</h1>
                            <div class="product-price">199.99 €</div>
                            <div class="product-stock">En stock</div>
                            
                            <div class="product-description mt-4">
                                <p>La nouvelle Nike Air Max 2023 redéfinit le confort et le style. Cette édition limitée combine des matériaux premium avec une technologie d'amorti révolutionnaire.</p>
                            </div>

                            <div class="product-actions mt-4">
                                <div class="quantity-selector mb-3">
                                    <button class="btn-quantity" onclick="updateQuantity(-1)">-</button>
                                    <input type="number" id="quantity" value="1" min="1" max="10">
                                    <button class="btn-quantity" onclick="updateQuantity(1)">+</button>
                                </div>
                                <button class="btn-add-cart">
                                    <i class='bx bx-cart-add'></i>
                                    Ajouter au panier
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

        <section class="product-features">
            <div class="container">
                <h2 class="section-title">Caractéristiques</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <img src="assets/images/products/example/characteristics/feature1.jpg" alt="Semelle Air">
                        <h3>Semelle Air</h3>
                        <p>Technologie d'amorti avancée pour un confort optimal tout au long de la journée.</p>
                    </div>
                    <div class="feature-card">
                        <img src="assets/images/products/example/characteristics/feature2.jpg" alt="Matériaux recyclés">
                        <h3>Matériaux recyclés</h3>
                        <p>Fabriqué avec 20% de matériaux recyclés, pour un impact environnemental réduit.</p>
                    </div>
                    <div class="feature-card">
                        <img src="assets/images/products/example/characteristics/feature3.jpg" alt="Design unique">
                        <h3>Design unique</h3>
                        <p>Motifs exclusifs et finitions premium pour un style incomparable.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="product-videos">
            <div class="container">
                <h2 class="section-title">Découvrez en vidéo</h2>
                <div class="videos-grid">
                    <div class="video-card">
                        <div class="video-wrapper">
                            <iframe src="https://www.youtube.com/embed/video1" frameborder="0" allowfullscreen></iframe>
                        </div>
                        <h3>Présentation détaillée</h3>
                    </div>
                    <div class="video-card">
                        <div class="video-wrapper">
                            <iframe src="https://www.youtube.com/embed/video2" frameborder="0" allowfullscreen></iframe>
                        </div>
                        <h3>Test et avis</h3>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>

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
    <meta property="og:description" content="<?= htmlspecialchars(substr(strip_tags($product['description']), 0, 150)); ?>..." />
    <meta property="og:image" content="https://luxemarket.click/uploads/main/<?= $product['image']; ?>" />
    <meta property="og:url" content="https://luxemarket.click/product.php?id=<?= $product['id']; ?>" />
    <meta property="og:type" content="product" />
    <meta property="og:site_name" content="LuxeMarket" />
    <meta property="og:locale" content="fr_FR" />

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?= htmlspecialchars($product['name']); ?>" />
    <meta name="twitter:description" content="<?= htmlspecialchars(substr(strip_tags($product['description']), 0, 150)); ?>..." />
    <meta name="twitter:image" content="https://luxemarket.click/uploads/main/<?= $product['image']; ?>" />
    <meta name="twitter:site" content="@LuxeMarket" />

    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/product.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" rel="stylesheet">
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
            <div class="hero-price"><?= $product['selling_price'] ?> FCFA</div>
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
                        <div class="pack-card" data-pack-id="<?= $pack['id']; ?>" onclick="selectPack(<?= $pack['id']; ?>, <?= $pack['price']; ?>, <?= $pack['quantity']; ?>)">
                            <!-- Image du pack -->
                            <?php if (!empty($pack['image'])): ?>
                                <div class="pack-image">
                                    <img src="uploads/packs/<?= $pack['image']; ?>" alt="<?= htmlspecialchars($pack['name']); ?>" loading="lazy">
                                </div>
                            <?php endif; ?>

                            <div class="pack-content">
                                <div class="pack-header">
                                    <h3 class="pack-title"><?= htmlspecialchars($pack['name']); ?></h3>
                                    <div class="pack-quantity">
                                        <i class='bx bx-package'></i>
                                        <?= $pack['quantity']; ?> unités
                                    </div>
                                </div>

                                <div class="pack-pricing">
                                    <div class="price-comparison">
                                        <div class="price-reduction">
                                            <span class="price-value highlight"><?= number_format($pack['price'], 0, ' ', ' '); ?> FCFA</span>
                                        </div>
                                        <div class="price-normal">
                                            <span class="price-value"><?= number_format($product['selling_price'] * $pack['quantity'], 0, ' ', ' '); ?> FCFA</span>
                                        </div>
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

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div id="liveToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div id="toastMessage" class="toast-body">
                    <?= isset($order_message) ? htmlspecialchars($order_message) : ''; ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>


    <!-- MODAL COMMANDE -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal">
                <!-- Body -->
                <div class="modal-body custom-modal-body">
                    <form id="orderForm" action="management/orders/save.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                        <input type="hidden" name="pack_id" value="" id="selectedPackId">

                        <!-- Sélection de pack -->
                        <?php if (!empty($packs)): ?>
                            <div class="form-group">
                                <label class="form-label">Choisir un pack</label>
                                <select class="form-control-custom" name="pack_selection" id="packSelection" onchange="updatePackSelection()">
                                    <option value="">Sélectionner un pack (optionnel)</option>
                                    <?php foreach ($packs as $pack): ?>
                                        <option value="<?= $pack['id']; ?>"
                                            data-price="<?= $pack['price']; ?>"
                                            data-quantity="<?= $pack['quantity']; ?>"
                                            data-name="<?= htmlspecialchars($pack['name']); ?>">
                                            <?= htmlspecialchars($pack['name']); ?> - <?= $pack['quantity']; ?> unités - <?= number_format($pack['price'], 0, ' ', ' '); ?> FCFA
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

                        <!-- Téléphone avec pays -->
                        <div class="form-group">
                            <label class="form-label">Téléphone</label>
                            <div style="display: flex; gap: 5px;">
                                <select class="form-control-custom" name="client_country" style="width: 30%;" required>
                                    <option value="TD" data-length="8">🇹🇩 +235</option>
                                    <option value="GN" data-length="9">🇬🇳 +224</option>
                                </select>
                                <input type="tel" name="client_phone" class="form-control-custom" placeholder="Numéro sans indicatif" required style="width: 70%;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Adresse de livraison</label>
                            <input type="text" class="form-control-custom" name="client_adress" placeholder="Ville, Quartier" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Note evantuelles</label>
                            <textarea class="form-control-custom" name="client_note" rows="2" placeholder="Note évantuelle"></textarea>
                        </div>
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

    <script src="assets/js/tracking-manager.js" defer></script>
    <script src="assets/js/product.js"></script>
    <!-- <script src="assets/js/theme.js"></script> -->
    <script src="assets/js/pack.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                trackEvent('QualifiedVisit', {
                    content_ids: ['<?= $product['id']; ?>'],
                    content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                    value: <?= $product['selling_price']; ?>,
                    currency: 'XOF'
                });
            }, 5000);

            const orderForm = document.getElementById('orderForm');
            const orderModal = document.getElementById('orderModal');
            let formStarted = false;
            let formSubmitted = false;
            let formStartTime = null;
            let abandonTimer = null;

            if (orderForm) {
                const formFields = orderForm.querySelectorAll('input[type="text"], input[type="tel"], textarea, select');
                let fieldsCompleted = 0;
                const totalFields = formFields.length;

                formFields.forEach((field, index) => {
                    field.addEventListener('input', function() {
                        if (!formStarted && this.value.length > 2) {
                            formStarted = true;
                            formStartTime = Date.now();

                            trackEvent('FormStarted', {
                                content_ids: ['<?= $product['id']; ?>'],
                                content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                value: <?= $product['selling_price']; ?>,
                                currency: 'XOF'
                            });

                            abandonTimer = setTimeout(function() {
                                if (formStarted && !formSubmitted) {
                                    trackEvent('FormInactive', {
                                        content_ids: ['<?= $product['id']; ?>'],
                                        content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                        value: <?= $product['selling_price']; ?>,
                                        currency: 'XOF',
                                        time_spent: Math.round((Date.now() - formStartTime) / 1000)
                                    });
                                }
                            }, 300000);
                        }

                        if (this.value.length > 2) {
                            const currentFieldsCompleted = Array.from(formFields).filter(f => f.value.length > 2).length;

                            if (currentFieldsCompleted > fieldsCompleted) {
                                fieldsCompleted = currentFieldsCompleted;
                                const progressPercent = Math.round((fieldsCompleted / totalFields) * 100);

                                if (progressPercent === 25) {
                                    trackEvent('FormProgress25', {
                                        content_ids: ['<?= $product['id']; ?>'],
                                        content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                        value: <?= $product['selling_price']; ?>,
                                        currency: 'XOF',
                                        progress: 25
                                    });
                                } else if (progressPercent === 50) {
                                    trackEvent('FormProgress50', {
                                        content_ids: ['<?= $product['id']; ?>'],
                                        content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                        value: <?= $product['selling_price']; ?>,
                                        currency: 'XOF',
                                        progress: 50
                                    });
                                } else if (progressPercent === 75) {
                                    trackEvent('FormProgress75', {
                                        content_ids: ['<?= $product['id']; ?>'],
                                        content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                        value: <?= $product['selling_price']; ?>,
                                        currency: 'XOF',
                                        progress: 75
                                    });
                                } else if (progressPercent === 100) {
                                    trackEvent('FormCompleted', {
                                        content_ids: ['<?= $product['id']; ?>'],
                                        content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                        value: <?= $product['selling_price']; ?>,
                                        currency: 'XOF',
                                        progress: 100
                                    });
                                }
                            }
                        }

                        if (abandonTimer) {
                            clearTimeout(abandonTimer);
                            abandonTimer = setTimeout(function() {
                                if (formStarted && !formSubmitted) {
                                    trackEvent('FormInactive', {
                                        content_ids: ['<?= $product['id']; ?>'],
                                        content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                        value: <?= $product['selling_price']; ?>,
                                        currency: 'XOF',
                                        time_spent: Math.round((Date.now() - formStartTime) / 1000)
                                    });
                                }
                            }, 300000);
                        }
                    });

                    field.addEventListener('focus', function() {
                        trackEvent('FormFieldFocus', {
                            content_ids: ['<?= $product['id']; ?>'],
                            content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                            value: <?= $product['selling_price']; ?>,
                            currency: 'XOF',
                            field_name: this.name || this.id || 'unknown',
                            field_index: index
                        });
                    });
                });

                // Détecter la fermeture du modal = formulaire abandonné
                if (orderModal) {
                    orderModal.addEventListener('hidden.bs.modal', function() {
                        if (formStarted && !formSubmitted) {
                            const timeSpent = formStartTime ? Math.round((Date.now() - formStartTime) / 1000) : 0;

                            trackEvent('FormAbandoned', {
                                content_ids: ['<?= $product['id']; ?>'],
                                content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                value: <?= $product['selling_price']; ?>,
                                currency: 'XOF',
                                time_spent: timeSpent,
                                abandonment_point: 'modal_close'
                            });
                        }
                    });
                }

                window.addEventListener('beforeunload', function() {
                    if (formStarted && !formSubmitted) {
                        const timeSpent = formStartTime ? Math.round((Date.now() - formStartTime) / 1000) : 0;

                        trackEvent('FormAbandoned', {
                            content_ids: ['<?= $product['id']; ?>'],
                            content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                            value: <?= $product['selling_price']; ?>,
                            currency: 'XOF',
                            time_spent: timeSpent,
                            abandonment_point: 'page_leave'
                        });
                    }
                });

                orderForm.addEventListener('submit', function(e) {
                    formSubmitted = true;

                    // Déterminer si un pack est sélectionné et calculer dynamiquement la valeur
                    var packIdInput = document.getElementById('selectedPackId');
                    var packSelect = document.getElementById('packSelection');
                    var selectedPackId = packIdInput ? (packIdInput.value || '') : '';
                    var purchasePayload = {
                        currency: 'XOF'
                    };

                    if (selectedPackId && packSelect) {
                        // Retrouver l'option correspondant au pack sélectionné
                        var option = Array.from(packSelect.options).find(function(opt) {
                            return opt.value === selectedPackId;
                        });
                        if (option) {
                            var packPrice = parseInt(option.dataset.price, 10) || 0;
                            var packQty = parseInt(option.dataset.quantity, 10) || 1;

                            purchasePayload.content_ids = [selectedPackId];
                            purchasePayload.content_type = 'product';
                            purchasePayload.contents = [{
                                id: selectedPackId,
                                quantity: packQty,
                                item_price: packPrice
                            }];
                            purchasePayload.num_items = packQty; // total d'unités dans le pack
                            purchasePayload.value = packPrice;
                        }
                    }

                    if (!purchasePayload.content_ids) {
                        // Pas de pack: utiliser le produit simple
                        purchasePayload.content_ids = ['<?= $product['id']; ?>'];
                        purchasePayload.content_type = 'product';
                        purchasePayload.contents = [{
                            id: '<?= $product['id']; ?>',
                            quantity: 1,
                            item_price: <?= $product['selling_price']; ?>
                        }];
                        purchasePayload.num_items = 1;
                        purchasePayload.value = <?= $product['selling_price']; ?>;
                    }

                    // Envoyer uniquement l'événement Purchase (conseillé par Facebook)
                    trackEvent('Purchase', purchasePayload);
                });
            }
        });

        function openOrderForm() {
            // InitiateCheckout au clic sur bouton Commander (specs Facebook)
            trackEvent('InitiateCheckout', {
                content_ids: ['<?= $product['id']; ?>'],
                contents: [{
                    'id': '<?= $product['id']; ?>',
                    'quantity': 1,
                    'item_price': <?= $product['selling_price']; ?>
                }],
                currency: 'XOF',
                num_items: 1,
                value: <?= $product['selling_price']; ?>
            });

            var modal = new bootstrap.Modal(document.getElementById('orderModal'));
            modal.show();
        }
    </script>

</body>

</html>
<?php
session_start();
require 'vendor/autoload.php';

use src\Connectbd;
use src\Product;
use src\Country;

$cnx = Connectbd::getConnection();
$productManager = new Product($cnx);
$countryManager = new Country($cnx);


$lang = isset($_GET['lang']) && $_GET['lang'] === 'ar' ? 'ar' : 'fr';

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
$productCountries = $productManager->getProductCountries($productId);

if (!$product) {
    header('Location: error.php?code=404');
    exit;
}

$characteristics = $productManager->getProductCharacteristics($productId);
$videos = $productManager->getProductVideos($productId);
$packs = $productManager->getProductPacks($productId);

$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($basePath === '/') {
    $basePath = '';
}

function countryCodeToFlagEntity($code)
{
    $code = strtoupper(trim($code));
    if (!preg_match('/^[A-Z]{2}$/', $code)) {
        return '';
    }
    $first = 127397 + ord($code[0]);
    $second = 127397 + ord($code[1]);
    return '&#' . $first . ';&#' . $second . ';';
}

// Récupérer le prix du pays (à partir de la première association)
$productCountries = $productManager->getProductCountries($productId);
$displayPrice = !empty($productCountries) ? $productCountries[0]['selling_price'] : 0;

// Sélectionner le titre et la description selon la langue
$displayTitle = $lang === 'ar' && !empty($product['ar_name']) ? $product['ar_name'] : $product['name'];
$displayDescription = $lang === 'ar' && !empty($product['ar_description']) ? $product['ar_description'] : $product['description'];

// Textes statiques selon la langue
$texts = [
    'fr' => [
        'commander' => 'Commander',
        'order_button' => 'Valider la commande',
        'order_button_text' => 'Valider la commande',
        'fullname' => 'Nom complet',
        'number' => 'Numéro',
        'address' => 'Ville, Quartier',
        'note' => 'Note éventuelle',
        'about' => 'À propos',
        'about_us' => 'À propos de nous',
        'payment' => 'Modes de paiement',
        'shipping' => 'Livraison',
        'online_store' => 'Nous sommes une boutique en ligne',
        'buy_services' => 'Nous proposons des services d\'achat',
        'privacy' => 'Politique de confidentialité',
        'copyright' => 'Tous les droits réservés © Maxora Market 2025',
        'lang_switch' => 'العربية',
        'phone_label' => 'Téléphone',
        'address_label' => 'Adresse'
    ],
    'ar' => [
        'commander' => 'اطلب الآن',
        'order_button' => 'تأكيد الطلب',
        'order_button_text' => 'تأكيد الطلب',
        'fullname' => 'الاسم الكامل',
        'number' => 'الرقم',
        'address' => 'المدينة، الحي',
        'note' => 'ملاحظة اختيارية',
        'about' => 'حول',
        'about_us' => 'حول متجرنا',
        'payment' => 'طرق الدفع',
        'shipping' => 'التوصيل',
        'online_store' => 'نحن متجر إلكتروني',
        'buy_services' => 'نقدم خدمات الشراء',
        'privacy' => 'سياسة الخصوصية',
        'copyright' => 'جميع الحقوق محفوظة © Maxora Market 2025',
        'lang_switch' => 'Français',
        'phone_label' => 'هاتف',
        'address_label' => 'عنوان'
    ]
];

$t = $texts[$lang];

// Déterminer le lien de changement de langue
$otherLang = $lang === 'ar' ? 'fr' : 'ar';
$langSwitchUrl = '?id=' . $productId . '&lang=' . $otherLang;
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($displayTitle); ?></title>
    <meta property="og:title" content="<?= htmlspecialchars($displayTitle); ?>" />
    <meta property="og:description"
        content="<?= htmlspecialchars(substr(strip_tags($displayDescription), 0, 150)); ?>..." />
    <meta property="og:image" content="https://maxora.cloud/uploads/main/<?= $product['image']; ?>" />
    <meta property="og:url" content="https://maxora.cloud/index2.php?id=<?= $product['id'] ?>&lang=<?= $lang ?>" />
    <meta property="og:type" content="product" />
    <meta property="og:site_name" content="MaxoraMarket" />
    <meta property="og:locale" content="<?= $lang === 'ar' ? 'ar_AR' : 'fr_FR' ?>" />

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?= htmlspecialchars($displayTitle); ?>" />
    <meta name="twitter:description"
        content="<?= htmlspecialchars(substr(strip_tags($displayDescription), 0, 150)); ?>..." />
    <meta name="twitter:image" content="https://maxora.cloud/uploads/main/<?= $product['image']; ?>" />
    <meta name="twitter:site" content="@MaxoraMarket" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;500;700&display=swap">
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/index2.css">
    <?php if ($lang === 'ar'): ?>
        <style>
            body {
                direction: rtl;
            }

            .yc-navbar {
                flex-direction: row-reverse;
            }

            .corner {
                order: -1;
            }

            .product-layout {
                flex-direction: row-reverse;
            }
        </style>
    <?php endif; ?>
</head>

<body>

    <header class="yc-header">
        <nav class="yc-navbar container">
            <div class="logo">
                <a href="/" aria-label="home">
                    <img src="<?= $basePath ?>/assets/images/logo.jpg" alt="TUBKAL MARKET">
                </a>
            </div>
            <div class="corner">
                <a href="<?= $langSwitchUrl ?>" style="margin-right: 10px; text-decoration: none; color: inherit;">
                    <?= $t['lang_switch'] ?>
                </a>
                <button class="commander-btn" onclick="location.href='#product_details'"><?= $t['commander'] ?></button>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <section class="container product-layout">
            <!-- Images -->
            <div class="product-images">
                <div class="main-image-wrapper">
                    <img id="main-image" src="uploads/main/<?= htmlspecialchars($product['image']); ?>">
                </div>
                <div class="carousel-grid">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if (!empty($product['carousel' . $i])): ?>
                            <div class="carousel-item">
                                <img src="uploads/carousel/<?= htmlspecialchars($product['carousel' . $i]); ?>"
                                    onclick="document.getElementById('main-image').src=this.src">
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Details + Form -->
            <div class="product-details" id="product_details">
                <h1 class="product-name"><?= htmlspecialchars($displayTitle); ?></h1>
                <h2 class="product-price"><?= ($displayPrice) ?> CFA</h2>
                <form class="express-checkout-form" method="POST" action="management/orders/save.php">
                    <div class="express-checkout-fields">
                        <input type="text" name="client_name" class="form-control-custom"
                            placeholder="<?= $t['fullname'] ?>" required>
                        <div class="phone-input-wrapper">
                            <select name="client_country" class="form-control-country" required>
                                <?php foreach ($productCountries as $ctry): ?>
                                    <?php $flag = countryCodeToFlagEntity($ctry['code'] ?? ''); ?>
                                    <option value="<?= htmlspecialchars($ctry['id']); ?>">
                                        <?= $flag ? $flag . ' ' : '' ?><?= htmlspecialchars($ctry['phone_code']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="tel" name="client_phone" class="form-control-custom"
                                placeholder="<?= $t['number'] ?>" required>
                        </div>
                        <input type="text" name="client_adress" class="form-control-custom"
                            placeholder="<?= $t['address'] ?>" required>
                        <textarea name="client_note" class="form-control-custom" rows="2"
                            placeholder="<?= $t['note'] ?>"></textarea>

                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                        <input type="hidden" name="lang" value="<?= $lang; ?>">
                        <input type="hidden" name="valider" value="commander">
                    </div>
                    <div class="modal-footer-custom">
                        <button type="submit" class="btn-submit-order">
                            <i class='bx bx-check-circle'></i>
                            <span><?= $t['order_button_text'] ?></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Description -->
            <div class="product-description">
                <?= $displayDescription; ?>
            </div>

            <div class="toast-container">
                <div id="liveToast" class="toast align-items-center text-white border-0" role="alert"
                    aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                    <div class="d-flex">
                        <div id="toastMessage" class="toast-body">
                            <?= isset($order_message) ? htmlspecialchars($order_message) : ''; ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>

        </section>

    </main>

    <footer>
        <div class="columns container">
            <div class="column logo">
                <img src="<?= $basePath ?>/assets/images/logo.jpg" alt="MAXORA MARKET" width="110" height="70">
            </div>
            <div class="column">
                <h1><?= $t['about'] ?></h1>
                <a href="#"><?= $t['about_us'] ?></a>
                <a href="#"><?= $t['payment'] ?></a>
                <a href="#"><?= $t['shipping'] ?></a>
            </div>
            <div class="column">
                <h1><?= $t['about'] ?></h1>
                <h5><?= $t['online_store'] ?></h5>
                <h5><?= $t['buy_services'] ?></h5>
                <h5><?= $t['privacy'] ?></h5>
            </div>
        </div>
        <div class="copyright-wrapper">
            <p><strong><?= $t['copyright'] ?></strong></p>
        </div>
    </footer>
     
    <script src="<?= $basePath ?>/assets/js/tracking-manager.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= $basePath ?>/assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/index2.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                trackEvent('QualifiedVisit', {
                    content_ids: ['<?= $product['id']; ?>'],
                    content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                    value: <?= $displayPrice; ?>,
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
                                value: <?= $displayPrice; ?>,
                                currency: 'XOF'
                            });

                            abandonTimer = setTimeout(function() {
                                if (formStarted && !formSubmitted) {
                                    trackEvent('FormInactive', {
                                        content_ids: ['<?= $product['id']; ?>'],
                                        content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                        value: <?= $displayPrice; ?>,
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
                                        value: <?= $displayPrice; ?>,
                                        currency: 'XOF',
                                        progress: 25
                                    });
                                } else if (progressPercent === 50) {
                                    trackEvent('FormProgress50', {
                                        content_ids: ['<?= $product['id']; ?>'],
                                        content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                        value: <?= $displayPrice; ?>,
                                        currency: 'XOF',
                                        progress: 50
                                    });
                                } else if (progressPercent === 75) {
                                    trackEvent('FormProgress75', {
                                        content_ids: ['<?= $product['id']; ?>'],
                                        content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                        value: <?= $displayPrice; ?>,
                                        currency: 'XOF',
                                        progress: 75
                                    });
                                } else if (progressPercent === 100) {
                                    trackEvent('FormCompleted', {
                                        content_ids: ['<?= $product['id']; ?>'],
                                        content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                        value: <?= $displayPrice; ?>,
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
                                        value: <?= $displayPrice; ?>,
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
                            value: <?= $displayPrice; ?>,
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
                                value: <?= $displayPrice; ?>,
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
                            value: <?= $displayPrice; ?>,
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
                            item_price: <?= $displayPrice; ?>
                        }];
                        purchasePayload.num_items = 1;
                        purchasePayload.value = <?= $displayPrice; ?>;
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
                    'item_price': <?= $displayPrice; ?>
                }],
                currency: 'XOF',
                num_items: 1,
                value: <?= $displayPrice; ?>
            });

            var modal = new bootstrap.Modal(document.getElementById('orderModal'));
            modal.show();
        }
    </script>                                
</body>

</html>
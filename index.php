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
        'copyright' => 'Tous les droits réservés © luxemarket Market 2025',
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
        'copyright' => 'جميع الحقوق محفوظة © luxemarket Market 2025',
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
    <meta property="og:image" content="https://luxemarket.cloud/uploads/main/<?= $product['image']; ?>" />
    <meta property="og:url" content="https://luxemarket.cloud/index1.php?id=<?= $product['id'] ?>&lang=<?= $lang ?>" />
    <meta property="og:type" content="product" />
    <meta property="og:site_name" content="luxemarketMarket" />
    <meta property="og:locale" content="<?= $lang === 'ar' ? 'ar_AR' : 'fr_FR' ?>" />

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?= htmlspecialchars($displayTitle); ?>" />
    <meta name="twitter:description"
        content="<?= htmlspecialchars(substr(strip_tags($displayDescription), 0, 150)); ?>..." />
    <meta name="twitter:image" content="https://luxemarket.cloud/uploads/main/<?= $product['image']; ?>" />
    <meta name="twitter:site" content="@luxemarketMarket" />

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
                    <img src="<?= $basePath ?>/assets/images/idx121.png" alt="TUBKAL MARKET">
                </a>
            </div>
            <div class="corner">
                <a href="<?= $langSwitchUrl ?>" style="margin-right: 10px; text-decoration: none; color: inherit;">
                    <?= $t['lang_switch'] ?>
                </a>
                <button class="commander-btn" onclick="location.href='#product_details'">
                    <?= $t['commander'] ?>
                </button>
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
                <img src="<?= $basePath ?>/assets/images/logo.jpg" alt="luxemarket MARKET" width="110" height="70">
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

    <script src="<?= $basePath ?>/assets/js/tracking-manager.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= $basePath ?>/assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/index2.js"></script>

    <script>
        (function() {
            function safeParseInt(value) {
                const parsed = parseInt(value, 10);
                return Number.isNaN(parsed) ? 0 : parsed;
            }

            function getCookie(name) {
                const cookies = document.cookie ? document.cookie.split('; ') : [];
                for (let i = 0; i < cookies.length; i += 1) {
                    const parts = cookies[i].split('=');
                    const key = decodeURIComponent(parts.shift());
                    if (key === name) {
                        return decodeURIComponent(parts.join('='));
                    }
                }
                return '';
            }

            function setCookie(name, value, maxAgeSeconds) {
                let maxAge = '';
                if (maxAgeSeconds) {
                    maxAge = '; max-age=' + Math.max(0, Math.floor(maxAgeSeconds));
                }
                document.cookie = encodeURIComponent(name) + '=' + encodeURIComponent(value) + maxAge + '; path=/';
            }

            function readOrderState(key) {
                let stored = null;
                try {
                    stored = localStorage.getItem(key);
                } catch (error) {
                    stored = null;
                }

                if (!stored) {
                    return { count: 0, expiresAt: null };
                }

                try {
                    const parsed = JSON.parse(stored);
                    if (parsed && typeof parsed === 'object') {
                        return {
                            count: safeParseInt(parsed.count),
                            expiresAt: typeof parsed.expiresAt === 'number' ? parsed.expiresAt : null,
                            isLegacy: typeof parsed.expiresAt !== 'number'
                        };
                    }
                    return { count: safeParseInt(parsed), expiresAt: null, isLegacy: true };
                } catch (error) {
                    return { count: safeParseInt(stored), expiresAt: null, isLegacy: true };
                }
            }

            function getOrderCount(id, windowMs) {
                const key = 'order_limit_' + id;
                const state = readOrderState(key);
                if (state.expiresAt && Date.now() > state.expiresAt) {
                    try {
                        localStorage.removeItem(key);
                    } catch (error) {}
                }
                if (!state.expiresAt && state.count > 0 && state.isLegacy) {
                    const seededExpiresAt = Date.now() + windowMs;
                    try {
                        localStorage.setItem(key, JSON.stringify({
                            count: state.count,
                            expiresAt: seededExpiresAt
                        }));
                    } catch (error) {}
                    return Math.max(state.count, safeParseInt(getCookie(key)));
                }

                const localCount = state.expiresAt && Date.now() > state.expiresAt ? 0 : state.count;
                const cookieCount = safeParseInt(getCookie(key));
                return Math.max(localCount, cookieCount);
            }

            function setOrderCount(id, count, windowMs) {
                const key = 'order_limit_' + id;
                const expiresAt = Date.now() + windowMs;
                try {
                    localStorage.setItem(key, JSON.stringify({
                        count: count,
                        expiresAt: expiresAt
                    }));
                } catch (error) {}
                setCookie(key, String(count), windowMs / 1000);
            }

            function getBlockedProducts(windowMs) {
                try {
                    const stored = localStorage.getItem('order_limit_blocked_products');
                    if (!stored) return [];
                    const parsed = JSON.parse(stored);
                    if (Array.isArray(parsed)) {
                        return parsed;
                    }
                    if (!parsed || typeof parsed !== 'object') return [];
                    if (parsed.expiresAt && Date.now() > parsed.expiresAt) {
                        localStorage.removeItem('order_limit_blocked_products');
                        return [];
                    }
                    return Array.isArray(parsed.ids) ? parsed.ids : [];
                } catch (error) {
                    return [];
                }
            }

            function setBlockedProducts(list, windowMs) {
                try {
                    localStorage.setItem('order_limit_blocked_products', JSON.stringify({
                        ids: list,
                        expiresAt: Date.now() + windowMs
                    }));
                } catch (error) {}
            }

            function markBlockedProduct(id, windowMs) {
                const blocked = getBlockedProducts(windowMs);
                const normalizedId = String(id);
                if (blocked.indexOf(normalizedId) === -1) {
                    blocked.push(normalizedId);
                    setBlockedProducts(blocked, windowMs);
                }
            }

            function isProductBlocked(id, windowMs) {
                const blocked = getBlockedProducts(windowMs);
                return blocked.indexOf(String(id)) !== -1;
            }

            function applyLimitState(id, limit, windowMs) {
                const count = getOrderCount(id, windowMs);
                if (count >= limit) {
                    markBlockedProduct(id, windowMs);
                }

                if (isProductBlocked(id, windowMs)) {
                    document.querySelectorAll('.commander-btn, .btn-submit-order').forEach(function(btn) {
                        btn.disabled = true;
                        btn.setAttribute('aria-disabled', 'true');
                        btn.style.display = 'none';
                    });
                }
            }

            window.createOrderLimit = function(productId, options) {
                const limit = options && options.limit ? options.limit : 2;
                const doubleClickGuardMs = options && options.doubleClickGuardMs ? options.doubleClickGuardMs : 2500;
                const windowMs = options && options.windowMs ? options.windowMs : 48 * 60 * 60 * 1000;
                let lastSubmitAt = 0;

                function canSubmit() {
                    const now = Date.now();
                    if (now - lastSubmitAt < doubleClickGuardMs) {
                        return false;
                    }
                    lastSubmitAt = now;

                    if (getOrderCount(productId, windowMs) >= limit || isProductBlocked(productId, windowMs)) {
                        applyLimitState(productId, limit, windowMs);
                        return false;
                    }
                    return true;
                }

                function registerSubmit() {
                    const nextCount = getOrderCount(productId, windowMs) + 1;
                    setOrderCount(productId, nextCount, windowMs);
                    if (nextCount >= limit) {
                        markBlockedProduct(productId, windowMs);
                    }
                    applyLimitState(productId, limit, windowMs);
                }

                return {
                    applyLimitState: function() {
                        applyLimitState(productId, limit, windowMs);
                    },
                    canSubmit: canSubmit,
                    registerSubmit: registerSubmit
                };
            };
        })();
    </script>

    <script>
        function trackWhenReady(eventName, eventData, attempts) {
            var defaultAttemptsByEvent = {
                Purchase: 40,
                InitiateCheckout: 30,
                QualifiedVisit: 20,
                FormAbandoned: 20,
                FormStarted: 20,
                FormCompleted: 20,
                FormProgress25: 15,
                FormProgress50: 15,
                FormProgress75: 15,
                FormInactive: 15,
                FormFieldFocus: 10
            };
            var fallbackAttempts = 20;
            var remaining = typeof attempts === 'number'
                ? attempts
                : (defaultAttemptsByEvent[eventName] || fallbackAttempts);
            if (typeof trackEvent === 'function' && (!window.trackingManager || window.trackingManager.isReady)) {
                trackEvent(eventName, eventData);
                return;
            }
            if (remaining <= 0) return;
            setTimeout(function() {
                trackWhenReady(eventName, eventData, remaining - 1);
            }, 200);
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                trackWhenReady('QualifiedVisit', {
                    content_ids: ['<?= $product['id']; ?>'],
                    content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                    value: <?= $displayPrice; ?>,
                    currency: 'XOF'
                });
            }, 5000);

            const productId = '<?= $product['id']; ?>';
            const orderLimitApi = window.createOrderLimit(productId, {
                limit: 2,
                doubleClickGuardMs: 2500,
                windowMs: 48 * 60 * 60 * 1000
            });
            orderLimitApi.applyLimitState();

            const orderForm = document.querySelector('.express-checkout-form');
            const orderModal = document.getElementById('orderModal');
            let formStarted = false;
            let formSubmitted = false;
            let formStartTime = null;
            let abandonTimer = null;
            let formAbandonedSent = false;

            const sendFormAbandoned = function(abandonmentPoint) {
                if (!formStarted || formSubmitted || formAbandonedSent) return;
                formAbandonedSent = true;
                const timeSpent = formStartTime ? Math.round((Date.now() - formStartTime) / 1000) : 0;
                trackWhenReady('FormAbandoned', {
                    content_ids: ['<?= $product['id']; ?>'],
                    content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                    value: <?= $displayPrice; ?>,
                    currency: 'XOF',
                    time_spent: timeSpent,
                    abandonment_point: abandonmentPoint
                });
            };

            if (orderForm) {
                const formFields = orderForm.querySelectorAll('input[type="text"], input[type="tel"], textarea, select');
                let fieldsCompleted = 0;
                const totalFields = formFields.length;

                formFields.forEach((field, index) => {
                    field.addEventListener('input', function() {
                        if (!formStarted && this.value.length > 2) {
                            formStarted = true;
                            formStartTime = Date.now();

                            trackWhenReady('FormStarted', {
                                content_ids: ['<?= $product['id']; ?>'],
                                content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                value: <?= $displayPrice; ?>,
                                currency: 'XOF'
                            });

                            abandonTimer = setTimeout(function() {
                                if (formStarted && !formSubmitted) {
                                    trackWhenReady('FormInactive', {
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
                                    trackWhenReady('FormProgress25', {
                                        content_ids: ['<?= $product['id']; ?>'],
                                        content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                        value: <?= $displayPrice; ?>,
                                        currency: 'XOF',
                                        progress: 25
                                    });
                                } else if (progressPercent === 50) {
                                    trackWhenReady('FormProgress50', {
                                        content_ids: ['<?= $product['id']; ?>'],
                                        content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                        value: <?= $displayPrice; ?>,
                                        currency: 'XOF',
                                        progress: 50
                                    });
                                } else if (progressPercent === 75) {
                                    trackWhenReady('FormProgress75', {
                                        content_ids: ['<?= $product['id']; ?>'],
                                        content_name: '<?= htmlspecialchars($product['name'], ENT_QUOTES); ?>',
                                        value: <?= $displayPrice; ?>,
                                        currency: 'XOF',
                                        progress: 75
                                    });
                                } else if (progressPercent === 100) {
                                    trackWhenReady('FormCompleted', {
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
                                    trackWhenReady('FormInactive', {
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
                        trackWhenReady('FormFieldFocus', {
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
                        sendFormAbandoned('modal_close');
                    });
                }

                window.addEventListener('beforeunload', function() {
                    sendFormAbandoned('page_leave');
                });

                document.addEventListener('visibilitychange', function() {
                    if (document.visibilityState === 'hidden') {
                        sendFormAbandoned('page_hide');
                    }
                });

                orderForm.addEventListener('submit', function(e) {
                    if (!orderLimitApi.canSubmit()) {
                        e.preventDefault();
                        return;
                    }

                    orderLimitApi.registerSubmit();

                    formSubmitted = true;
                    e.preventDefault();

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
                    trackWhenReady('Purchase', purchasePayload);

                    var submitUrl = orderForm.getAttribute('action') || window.location.href;
                    var formData = new FormData(orderForm);

                    var sendRequest = function() {
                        fetch(submitUrl, {
                            method: 'POST',
                            body: formData,
                            credentials: 'same-origin',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(function(response) {
                                if (response.redirected) {
                                    window.location.href = response.url;
                                    return;
                                }
                                return response.text().then(function() {
                                    window.location.href = response.url || window.location.href;
                                });
                            })
                            .catch(function() {
                                orderForm.submit();
                            });
                    };

                    setTimeout(sendRequest, 300);
                });
            }
        });

        function openOrderForm() {
            // InitiateCheckout au clic sur bouton Commander (specs Facebook)
            trackWhenReady('InitiateCheckout', {
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

            var modalElement = document.getElementById('orderModal');
            if (modalElement && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }
    </script>
    
</body>

</html>
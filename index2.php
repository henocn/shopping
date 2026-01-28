<?php
session_start();
require 'vendor/autoload.php';

use src\Connectbd;
use src\Product;

$cnx = Connectbd::getConnection();
$productManager = new Product($cnx);

// Déterminer la langue (FR ou AR) via query param
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

if (!$product) {
    header('Location: error.php?code=404');
    exit;
}

$characteristics = $productManager->getProductCharacteristics($productId);
$videos = $productManager->getProductVideos($productId);
$packs = $productManager->getProductPacks($productId);

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
    <link rel="stylesheet" href="assets/css/index2.css">
    <?php if ($lang === 'ar'): ?>
    <style>
        body { direction: rtl; }
        .yc-navbar { flex-direction: row-reverse; }
        .corner { order: -1; }
        .product-layout { flex-direction: row-reverse; }
    </style>
    <?php endif; ?>
</head>

<body>

    <header class="yc-header">
        <nav class="yc-navbar container">
            <div class="logo">
                <a href="/" aria-label="home">
                    <img src="assets/images/logo.jpg" alt="TUBKAL MARKET">
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
                        <input type="text" name="client_name" class="form-control-custom" placeholder="<?= $t['fullname'] ?>"
                            required>
                        <div class="phone-input-wrapper">
                            <select name="client_country" class="form-control-country" required>
                                <option value="TD" data-length="8">🇹🇩 +235</option>
                                <option value="ML" data-length="8">🇲🇱 +223</option>
                                <option value="GA" data-length="8">🇬🇦 +241</option>
                            </select>
                            <input type="tel" name="client_phone" class="form-control-custom" placeholder="<?= $t['number'] ?>"
                                required>
                        </div>
                        <input type="text" name="client_adress" class="form-control-custom"
                            placeholder="<?= $t['address'] ?>" required>
                        <textarea name="client_note" class="form-control-custom" rows="2"
                            placeholder="<?= $t['note'] ?>"></textarea>

                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
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
                <img src="assets/images/logo.jpg" alt="MAXORA MARKET" width="110" height="70">
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/index2.js"></script>

</body>

</html>
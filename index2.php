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
                    <img src="assets/images/logo.jpg" alt="TUBKAL MARKET" >
                </a>
            </div>
            <div >
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
                    <img id="main-image" src="https://cdn.youcan.shop/stores/ea3b708cb01dfb9d8ff19514de60a01b/products/ThzIavHvPJ9kiGaHu5mlBNXPMSloB9x5BAVx5ZCB.webp" alt="Produit" style="width: 100%; height: auto; display: block;">
                </div>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 15px;">
                    <img src="https://cdn.youcan.shop/stores/ea3b708cb01dfb9d8ff19514de60a01b/products/ThzIavHvPJ9kiGaHu5mlBNXPMSloB9x5BAVx5ZCB.webp" alt="Image 1" style="width: 100%; cursor: pointer; border-radius: 4px;" onclick="document.getElementById('main-image').src=this.src">
                    <img src="https://cdn.youcan.shop/stores/ea3b708cb01dfb9d8ff19514de60a01b/products/QnsnXknCI7G2nW2kt98o8GRt9XGzT4rf5vxhB4Di.webp" alt="Image 2" style="width: 100%; cursor: pointer; border-radius: 4px;" onclick="document.getElementById('main-image').src=this.src">
                    <img src="https://cdn.youcan.shop/stores/ea3b708cb01dfb9d8ff19514de60a01b/products/Bg6UcmgjZW5XUqrV9EG7Xn93BOX2oZTcD7Pb12sG.webp" alt="Image 3" style="width: 100%; cursor: pointer; border-radius: 4px;" onclick="document.getElementById('main-image').src=this.src">
                    <img src="https://cdn.youcan.shop/stores/ea3b708cb01dfb9d8ff19514de60a01b/products/puuymKKcXFAM3aMLMAlcHAK4J2OX814STxbON97M.webp" alt="Image 4" style="width: 100%; cursor: pointer; border-radius: 4px;" onclick="document.getElementById('main-image').src=this.src">
                </div>
            </div>

            <!-- Product Details -->
            <div class="product-details" id="product_details">
                <h1 class="product-name">Nouveau support de téléphone magnétique pliable</h1>
                
                <div class="product-price-container">
                    <h2 class="product-price">23 500 CFA</h2>
                    <div style="background: #5db2b9; color: white; padding: 5px 10px; border-radius: 4px; display: inline-block; font-size: 14px; margin-bottom: 20px;">20% de réduction</div>
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
                        <div style="color: var(--yc-neutral-color); margin-bottom: 10px; font-weight: 500;">Pour commander, veuillez remplir ce formulaire et nous vous contacterons dans les plus brefs délais !</div>
                        
                        <div class="express-checkout-field">
                            <input type="text" name="first_name" id="first_name" placeholder="Nom complet" required>
                        </div>
                        
                        <div class="express-checkout-field">
                            <input type="tel" name="phone" id="phone" placeholder="Téléphone (joignable pour vous appeler)" required>
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
                <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Nisi, ea ratione? Neque illum ea, facere dolorem dolorum, repudiandae minima tempora atque ratione eveniet recusandae debitis officiis aspernatur vero maiores? Exercitationem voluptas minus nemo, adipisci facilis, quidem quisquam inventore assumenda earum blanditiis totam vitae nesciunt illo ab in nobis quibusdam nisi aliquam quaerat. Fugit sit quaerat quasi natus laboriosam explicabo sequi at necessitatibus quisquam ab ducimus odit culpa tempore cupiditate maiores optio, vero iusto minus! Tempore mollitia unde explicabo cum inventore consequuntur assumenda aspernatur, repudiandae, sed harum odit iusto facere officiis? Eos amet consequuntur officiis nostrum dolore, magni maiores quaerat laboriosam voluptate quasi, excepturi praesentium hic ipsam. Aliquid quis, voluptatum quo fugiat similique quas quidem deserunt non, natus quasi praesentium accusantium. Porro harum amet accusamus delectus placeat, laborum maiores obcaecati earum rerum, fuga dignissimos saepe minima tenetur ex? Dolor odit deleniti corrupti sunt aliquam architecto vel eos sit? Aut provident, minima enim hic numquam voluptatem nisi magni quae incidunt qui ducimus ipsa neque cupiditate veniam obcaecati in odit dolorem sed tempore. Odit, ut, ullam voluptate veritatis dolor assumenda dolores numquam nostrum doloribus amet, tempora quia perferendis labore sunt dolorem beatae minus. Dolorem praesentium quis minima minus hic quas iusto facere dolor corrupti est voluptatem repellat aspernatur explicabo voluptates, quidem harum quos ratione. Asperiores perspiciatis neque ullam at cumque est ex praesentium quod tempore natus unde eos voluptas, ducimus culpa odit magni commodi blanditiis nulla ipsa, sit perferendis, laudantium nostrum ipsam aliquam? Delectus minima inventore, perferendis qui officiis iusto molestias modi pariatur necessitatibus est et dolores tempora velit, voluptatibus excepturi, quaerat sapiente culpa animi non asperiores blanditiis accusamus molestiae cupiditate. Rerum unde aperiam illum nobis a sint dolore optio animi. Sequi dolor animi modi laboriosam fuga maxime rerum odit. Distinctio, unde dolor. Doloribus ipsum asperiores ducimus aut nemo facere culpa minima corporis itaque nesciunt aliquam officia laborum unde, enim neque alias non odio. Maiores aperiam porro tempore, doloremque libero minima exercitationem, ipsum ea voluptatem eum provident accusamus? Nulla aut, repudiandae labore suscipit magni mollitia ab libero illum, veniam laborum pariatur reiciendis. Laudantium libero ab eveniet praesentium impedit rerum illum natus delectus officia minus voluptatum tenetur, necessitatibus odit hic temporibus placeat. Veritatis dolores rem illum, sequi illo assumenda deleniti labore quidem exercitationem perferendis, eius asperiores eos molestias officia repudiandae doloremque, quis animi? Recusandae dolor esse ducimus quibusdam aperiam, ut expedita autem sapiente, molestias consequuntur libero eligendi consectetur nulla vero quis? Nihil accusamus ut voluptates blanditiis dolores libero. Maiores, dolor esse voluptas placeat saepe provident veritatis quidem quo ea est. Ab quibusdam dolores facilis doloribus assumenda praesentium vitae quidem, sapiente esse. Nobis doloremque atque, laborum voluptates autem obcaecati similique dolores qui reprehenderit corrupti vero labore eius hic pariatur odit aspernatur voluptatem dolorem, et inventore sunt ipsa sapiente magnam nam enim! Eaque itaque nihil architecto ratione hic voluptates cupiditate, adipisci earum! Blanditiis veniam voluptates velit saepe ea, eum autem culpa? Ducimus amet, cupiditate animi qui dignissimos eveniet eligendi non culpa mollitia numquam iure, soluta enim. A, optio explicabo ut maxime tenetur placeat porro eligendi eos suscipit mollitia similique repellat facere quia laborum quis molestias voluptatem esse obcaecati et voluptatibus aut assumenda excepturi voluptatum. Obcaecati enim eius sit, adipisci quo repudiandae consequatur cumque fugiat doloremque. Quod velit, sequi incidunt dolor explicabo quidem error repudiandae ad ab inventore corrupti doloribus quo voluptatum eum architecto aut sint? Repudiandae, sequi repellat? Quidem dicta fugiat dolorem ratione obcaecati repellat odio perspiciatis odit expedita suscipit voluptatibus, pariatur nobis qui aliquid quia! Ipsam velit eos cum dolorem atque harum tempora voluptas modi assumenda eligendi ipsa dignissimos neque enim magni rem, aliquid nulla a, quod maxime. Numquam, tempore blanditiis! Consectetur libero harum optio officiis autem, assumenda deleniti quia molestiae vero vel accusamus quaerat, obcaecati iusto, dolores tempora ad dignissimos. Laborum possimus, incidunt enim laboriosam dolores sed sequi, quasi illum, distinctio et corporis sapiente! Eveniet dolorum eaque corporis accusamus facilis non unde, quae deserunt maiores quos praesentium laudantium iure magni expedita culpa alias? Omnis hic reiciendis magnam pariatur natus vel, delectus minima modi corporis, quas nemo ea aspernatur vitae assumenda facilis, itaque quaerat accusantium eveniet dolorem cum quibusdam. Exercitationem non facilis saepe dolores distinctio hic accusantium perspiciatis recusandae amet, delectus perferendis! Veniam, harum corrupti. Voluptates consequatur maxime dolorem pariatur! Eum fugiat corrupti qui vero!</p>
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
        document.querySelector('.increment-button').addEventListener('click', function() {
            const qty = document.getElementById('quantity');
            qty.value = parseInt(qty.value) + 1;
        });

        document.querySelector('.decrement-button').addEventListener('click', function() {
            const qty = document.getElementById('quantity');
            if (parseInt(qty.value) > 1) qty.value = parseInt(qty.value) - 1;
        });

        // Form submission
        document.getElementById('express-checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Formulaire envoyé: ' + document.getElementById('first_name').value);
        });
    </script>
</body>
</html>


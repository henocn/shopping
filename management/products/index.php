<?php

require '../../vendor/autoload.php';
require '../../utils/middleware.php';

verifyConnection("/management/products/");
checkAdminAccess($_SESSION['user_id']);
checkIsActive($_SESSION['user_id']);

use src\Connectbd;
use src\Product;

$cnx = Connectbd::getConnection();

$product = new Product($cnx);

$products = $product->getAllProducts();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/index.css" rel="stylesheet">
    <link href="../../assets/css/products.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <?php include '../../includes/navbar.php'; ?>

    <main class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Liste des produits</h2>
            <a href="add.php" class="btn btn-primary" style="background-color: var(--purple); border: none;">
                <i class='bx bx-plus'></i> Ajouter
            </a>
        </div>

        <div class="table-container">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Nom (FR)</th>
                        <th>Nom (AR)</th>
                        <th>Prix d'achat</th>
                        <th>Managers</th>
                        <th>Pays</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($products as $prod):
                        // Récupérer les managers et pays pour ce produit
                        $managers = $product->getProductManagers($prod['product_id']);
                        $countries = $product->getProductCountries($prod['product_id']);
                    ?>
                        <tr>
                            <td><?php echo $prod['product_id']; ?></td>
                            <td>
                                <img src="../../uploads/main/<?php echo htmlspecialchars($prod['image']); ?>"
                                    alt="<?php echo htmlspecialchars($prod['name']); ?>"
                                    class="product-image">
                            </td>
                            <td><?php echo htmlspecialchars($prod['name']); ?></td>
                            <td><?php echo htmlspecialchars($prod['ar_name'] ?? ''); ?></td>
                            <td><?php echo $prod['purchase_price']; ?> FCFA</td>
                            <td>
                                <?php if (!empty($managers)): ?>
                                    <?php foreach ($managers as $mgr): ?>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($mgr['name']); ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($countries)): ?>
                                    <?php foreach ($countries as $ctry): ?>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($ctry['code']); ?> (<?php echo $ctry['selling_price']; ?> FCFA)</span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="position:relative;">
                                <button type="button" class="action-btn context-menu-btn" data-id="<?php echo $prod['product_id']; ?>">
                                    <i class='bx bx-dots-vertical-rounded'></i>
                                </button>
                                <div class="context-menu" id="contextMenu<?php echo $prod['product_id']; ?>" style="display:none; position:absolute; right:0; top:40px; z-index:1000; min-width:180px; background:var(--paper); border-radius:12px; box-shadow:0 4px 16px rgba(0,0,0,0.12);">
                                    <!-- for product sharing link copy using format dynamic-host-domaine-name/index.php?id=product_id -->
                                    <a href="javascript:void(0);" class="menu-item d-flex align-items-center gap-2" style="padding:10px 18px; text-decoration:none;" onclick="copyProductLink(<?php echo $prod['product_id']; ?>)">
                                        <i class='bx bx-link'></i> Share Product
                                    </a>
                                    <a href="update.php?id=<?php echo $prod['product_id']; ?>" class="menu-item d-flex align-items-center gap-2" style="padding:10px 18px; color:var(--purple); text-decoration:none;">
                                        <i class='bx bx-edit'></i> Update Product
                                    </a>
                                    <button class="menu-item d-flex align-items-center gap-2" style="padding:10px 18px; color:#dc3545; background:none; border:none; width:100%; text-align:left; cursor:pointer;" onclick="deleteProduct(<?php echo $prod['product_id']; ?>)">
                                        <i class='bx bx-trash'></i> Delete Product
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                </tbody>
            </table>
        </div>
    </main>

    <?php include '../../includes/footer.php'; ?>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyProductLink(productId) {
            const shareUrl = "<?php echo $_SERVER['HTTP_HOST']; ?>/index.php?id=" + productId;
            navigator.clipboard.writeText(shareUrl);
            showNotification("Lien copié dans le presse-papiers !", "success");
        }


        function deleteProduct(productId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'save.php';
                
                const validerInput = document.createElement('input');
                validerInput.type = 'hidden';
                validerInput.name = 'valider';
                validerInput.value = 'delete';
                
                const productIdInput = document.createElement('input');
                productIdInput.type = 'hidden';
                productIdInput.name = 'product_id';
                productIdInput.value = productId;
                
                form.appendChild(validerInput);
                form.appendChild(productIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        document.querySelectorAll('.context-menu-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                document.querySelectorAll('.context-menu').forEach(function(menu) {
                    menu.style.display = 'none';
                });
                var menu = document.getElementById('contextMenu' + btn.getAttribute('data-id'));
                menu.style.display = 'block';
            });
        });

        document.addEventListener('click', function() {
            document.querySelectorAll('.context-menu').forEach(function(menu) {
                menu.style.display = 'none';
            });
        });

    </script>
</body>

</html>
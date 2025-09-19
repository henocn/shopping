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
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Manager</th>
                        <th>Pays</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($products as $product):
                    ?>
                        <tr class="<?php echo $product['status'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                            <td><?php echo $product['product_id']; ?></td>
                            <td>
                                <img src="../../uploads/main/<?php echo $product['image']; ?>"
                                    alt="<?php echo $product['name']; ?>"
                                    class="product-image">
                            </td>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $product['price']; ?> FCFA</td>
                            <td><?php echo $product['country']; ?></td>
                            <td><?php echo $product['manager_name']; ?></td>
                            <td style="position:relative;">
                                <button type="button" class="action-btn context-menu-btn" data-id="<?php echo $product['product_id']; ?>">
                                    <i class='bx bx-dots-vertical-rounded'></i>
                                </button>
                                <div class="context-menu" id="contextMenu<?php echo $product['product_id']; ?>" style="display:none; position:absolute; right:0; top:40px; z-index:1000; min-width:180px; background:var(--paper); border-radius:12px; box-shadow:0 4px 16px rgba(0,0,0,0.12);">
                                    <!-- for product sharing link copy using format dynamic-host-domaine-name/index.php?id=product_id -->
                                    <a href="javascript:void(0);" class="menu-item d-flex align-items-center gap-2" style="padding:10px 18px; text-decoration:none;" onclick="copyProductLink(<?php echo $product['product_id']; ?>)">
                                        <i class='bx bx-link'></i> Share Product
                                    </a>
                                    <a href="update.php?id=<?php echo $product['product_id']; ?>" class="menu-item d-flex align-items-center gap-2" style="padding:10px 18px; color:var(--purple); text-decoration:none;">
                                        <i class='bx bx-edit'></i> Update Product
                                    </a>
                                    <form action="save.php" method="post">
                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                        <input type="hidden" name="new_status" value="<?php echo $product['status'] == 1 ? 0 : 1; ?>">
                                        <input type="hidden" name="valider" value="upstatus">
                                        <button type="submit" class="menu-item d-flex align-items-center gap-2" style="padding:10px 18px; background:none; border:none; width:100%; text-align:left; cursor:pointer;">
                                            <i class='bx bx-power-off'></i> <?php echo $product['status'] == 1 ? 'Disable' : 'Enable'; ?> Product
                                        </button>
                                    </form>
                                    <button class="menu-item d-flex align-items-center gap-2" style="padding:10px 18px; color:#dc3545; background:none; border:none; width:100%; text-align:left; cursor:pointer;" onclick="deleteProduct(<?php echo $product['product_id']; ?>)">
                                        <i class='bx bx-trash'></i> Delete Product
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
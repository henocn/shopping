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
            <h2 class="mb-0">Products List</h2>
            <a href="add.php" class="btn btn-primary" style="background-color: var(--purple); border: none;">
                <i class='bx bx-plus'></i> Add Product
            </a>
        </div>

        <div class="table-container">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Simulation des données de la base de données
                    $products = [
                        [
                            'id' => 1,
                            'name' => 'Smartphone Pro Max',
                            'price' => 999,
                            'quantity' => 50,
                            'image' => 'phone.jpg',
                            'description' => 'Latest flagship smartphone with amazing features',
                            'status' => 1
                        ],
                        [
                            'id' => 2,
                            'name' => 'Wireless Earbuds',
                            'price' => 199,
                            'quantity' => 100,
                            'image' => 'earbuds.jpg',
                            'description' => 'High-quality wireless earbuds with noise cancellation',
                            'status' => 0
                        ],
                        [
                            'id' => 3,
                            'name' => 'Smart Watch Series 5',
                            'price' => 299,
                            'quantity' => 75,
                            'image' => 'watch.jpg',
                            'description' => 'Advanced smartwatch with health monitoring',
                            'status' => 1
                        ]
                    ];

                    foreach ($products as $product):
                        $rowClass = $product['status'] == 1 ? 'status-active' : 'status-inactive';
                    ?>
                        <tr class="<?php echo $rowClass; ?>">
                            <td>
                                <img src="../../../assets/images/products/<?php echo $product['image']; ?>"
                                    alt="<?php echo $product['name']; ?>"
                                    class="product-image">
                            </td>
                            <td><?php echo $product['name']; ?></td>
                            <td>$<?php echo number_format($product['price']); ?></td>
                            <td><?php echo $product['quantity']; ?></td>
                            <td>
                                <?php echo mb_strimwidth($product['description'], 0, 50, "..."); ?>
                            </td>
                            <td style="position:relative;">
                                <button type="button" class="action-btn context-menu-btn" data-id="<?php echo $product['id']; ?>">
                                    <i class='bx bx-dots-vertical-rounded'></i>
                                </button>
                                <div class="context-menu" id="contextMenu<?php echo $product['id']; ?>" style="display:none; position:absolute; right:0; top:40px; z-index:10; min-width:180px; background:var(--paper); border-radius:12px; box-shadow:0 4px 16px rgba(0,0,0,0.12);">
                                    <a href="update.php?id=<?php echo $product['id']; ?>" class="menu-item d-flex align-items-center gap-2" style="padding:10px 18px; color:var(--purple); text-decoration:none;">
                                        <i class='bx bx-edit'></i> Update Product
                                    </a>
                                    <button class="menu-item d-flex align-items-center gap-2" style="padding:10px 18px; color:#ff9800; background:none; border:none; width:100%; text-align:left;" onclick="toggleStatus(<?php echo $product['id']; ?>)">
                                        <i class='bx bx-power-off'></i> <?php echo $product['status'] == 1 ? 'Disable' : 'Enable'; ?> Product
                                    </button>
                                    <button class="menu-item d-flex align-items-center gap-2" style="padding:10px 18px; color:#dc3545; background:none; border:none; width:100%; text-align:left;" onclick="deleteProduct(<?php echo $product['id']; ?>)">
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

    <script src="../../../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleStatus(productId) {
            console.log('Toggle status for product:', productId);
        }

        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                console.log('Delete product:', productId);
            }
        }

        // Menu contextuel WhatsApp style
        document.querySelectorAll('.context-menu-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                // Fermer tous les autres menus
                document.querySelectorAll('.context-menu').forEach(function(menu) {
                    menu.style.display = 'none';
                });
                // Ouvrir le menu du bouton cliqué
                var menu = document.getElementById('contextMenu' + btn.getAttribute('data-id'));
                menu.style.display = 'block';
            });
        });

        // Fermer le menu si on clique ailleurs
        document.addEventListener('click', function() {
            document.querySelectorAll('.context-menu').forEach(function(menu) {
                menu.style.display = 'none';
            });
        });
    </script>
</body>

</html>
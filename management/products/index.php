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
                            <td>
                                <button type="button" class="action-btn" data-bs-toggle="modal" data-bs-target="#actionModal<?php echo $product['id']; ?>">
                                    <i class='bx bx-dots-vertical-rounded'></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal pour chaque produit -->
                        <div class="modal fade" id="actionModal<?php echo $product['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Actions for <?php echo $product['name']; ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <a href="update.php?id=<?php echo $product['id']; ?>" class="modal-action-btn btn-update">
                                            <i class='bx bx-edit'></i>
                                            Update Product
                                        </a>
                                        <button class="modal-action-btn btn-disable" onclick="toggleStatus(<?php echo $product['id']; ?>)">
                                            <i class='bx bx-power-off'></i>
                                            <?php echo $product['status'] == 1 ? 'Disable' : 'Enable'; ?> Product
                                        </button>
                                        <button class="modal-action-btn btn-delete" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                            <i class='bx bx-trash'></i>
                                            Delete Product
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    </script>
</body>

</html>
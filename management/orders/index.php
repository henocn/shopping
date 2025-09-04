<?php

require '../../vendor/autoload.php';

use src\Connectbd;
use src\Order;

$cnx = Connectbd::getConnection();

$order = new Order($cnx);

$orders = $order->GetOrders();
//var_dump($orders);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders management</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/index.css" rel="stylesheet">
    <link href="../../assets/css/orders.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <?php include '../../includes/navbar.php'; ?>

    <main class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Liste des commandes</h2>
        </div>

        <div class="table-container">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Client</th>
                        <th>Pays</th>
                        <th>Adresse</th>
                        <th>Quantité</th>
                        <th>PU</th>
                        <th>Pack</th>
                        <th>Status</th>
                        <th>Client Note</th>
                        <th>Manager Note</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($orders as $order):
                    ?>
                        <tr class="<?php echo $order['status'] == "processing" ? 'status-active' : 'status-inactive'; ?>">
                            <td><?php echo $order['product_name']; ?></td>
                            <td><?php echo $order['client_name']; ?></td>
                            <td><?php echo $order['client_country']; ?></td>
                            <td><?php echo $order['client_adress']; ?></td>
                            <td><?php echo $order['quantity']; ?></td>
                            <td><?php echo $order['unit_price']; ?></td>
                            <td><?php echo $order['pack_name']; ?></td>
                            <td><?php echo $order['status']; ?></td>
                            <td><?php echo $order['client_note']; ?></td>
                            <td><?php echo $order['manager_note']; ?></td>
                            <td>
                                <div class="context-menu-container">
                                    <button class="btn btn-sm btn-secondary context-menu-btn" data-id="<?php echo $order['order_id']; ?>">
                                        <i class='bx bx-dots-vertical-rounded'></i>
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
        function deleteorder(orderId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'save.php';

                const validerInput = document.createElement('input');
                validerInput.type = 'hidden';
                validerInput.name = 'valider';
                validerInput.value = 'delete';

                const orderIdInput = document.createElement('input');
                orderIdInput.type = 'hidden';
                orderIdInput.name = 'order_id';
                orderIdInput.value = orderId;

                form.appendChild(validerInput);
                form.appendChild(orderIdInput);
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
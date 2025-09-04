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
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">
</head>

<body>
    <?php include '../../includes/navbar.php'; ?>

    <main class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Liste des commandes</h2>
        </div>

        <div class="table-container">
            <table id="ordersTable" class="table table-striped dt-responsive nowrap" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th class="no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr class="<?php echo $order['status'] == "processing" ? 'status-active' : 'status-inactive'; ?>">
                            <td><?php echo $order['product_id']; ?></td>
                            <td>
                                <img src="../../uploads/main/<?php echo $order['product_image']; ?>"
                                    alt="<?php echo $order['product_name']; ?>"
                                    class="product-image"><?php echo $order['product_name']; ?>
                            </td>
                            <td><?php echo $order['client_name']; ?></td>
                            <td><?php echo $order['status']; ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-info" type="button" data-bs-toggle="modal"
                                        data-bs-target="#detailsModal<?php echo $order['order_id']; ?>">
                                        <i class='bx bx-info-circle'></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteorder(<?php echo $order['order_id']; ?>)">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal pour les détails -->
                        <div class="modal fade" id="detailsModal<?php echo $order['order_id']; ?>" tabindex="-1" aria-labelledby="detailsModalLabel<?php echo $order['order_id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailsModalLabel<?php echo $order['order_id']; ?>">
                                            Détails de la commande - <?php echo $order['product_name']; ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="orderForm<?php echo $order['order_id']; ?>" onsubmit="updateOrder(event, <?php echo $order['order_id']; ?>)">
                                            <div class="row mb-2">
                                                <div class="col-sm-4 fw-bold">Pays</div>
                                                <div class="col-sm-8"><?php echo $order['client_country']; ?></div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-sm-4 fw-bold">Adresse</div>
                                                <div class="col-sm-8"><?php echo $order['client_adress']; ?></div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-sm-4 fw-bold">
                                                    <label for="quantity<?php echo $order['order_id']; ?>" class="form-label">Quantité</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control" id="quantity<?php echo $order['order_id']; ?>"
                                                        name="quantity" value="<?php echo $order['quantity']; ?>" min="1" required>
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-sm-4 fw-bold">Prix Unitaire</div>
                                                <div class="col-sm-8"><?php echo $order['unit_price']; ?> FCFA</div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-sm-4 fw-bold">Pack</div>
                                                <div class="col-sm-8"><?php echo $order['pack_name']; ?></div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-sm-4 fw-bold">Note Client</div>
                                                <div class="col-sm-8"><?php echo $order['client_note']; ?></div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-sm-4 fw-bold">
                                                    <label for="status<?php echo $order['order_id']; ?>" class="form-label">Status</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <select class="form-select" id="status<?php echo $order['order_id']; ?>" name="status" required>
                                                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>En cours</option>
                                                        <option value="validated" <?php echo $order['status'] == 'validated' ? 'selected' : ''; ?>>Traitement</option>
                                                        <option value="canceled" <?php echo $order['status'] == 'canceled' ? 'selected' : ''; ?>>Annulé</option>
                                                        <option value="rejected" <?php echo $order['status'] == 'rejected' ? 'selected' : ''; ?>>Validé</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-sm-4 fw-bold">
                                                    <label for="action<?php echo $order['order_id']; ?>" class="form-label">Action</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <select class="form-select" id="action<?php echo $order['order_id']; ?>" name="action" required>
                                                        <option value="">Sélectionner une action</option>
                                                        <option value="done" <?php echo isset($order['action']) && $order['action'] == 'done' ? 'selected' : ''; ?>>Terminé</option>
                                                        <option value="remind" <?php echo isset($order['action']) && $order['action'] == 'remind' ? 'selected' : ''; ?>>Relancer</option>
                                                        <option value="call" <?php echo isset($order['action']) && $order['action'] == 'call' ? 'selected' : ''; ?>>Appeler</option>
                                                        <option value="unreachable" <?php echo isset($order['action']) && $order['action'] == 'unreachable' ? 'selected' : ''; ?>>Injoignable</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-sm-4 fw-bold">
                                                    <label for="managerNote<?php echo $order['order_id']; ?>" class="form-label">Note Manager</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" id="managerNote<?php echo $order['order_id']; ?>"
                                                        name="manager_note" rows="3"><?php echo $order['manager_note']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                <button type="submit" class="btn purple-bg magenta-color">Enregistrer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include '../../includes/footer.php'; ?>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#ordersTable').DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                },
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
                columnDefs: [{
                    targets: 'no-sort',
                    orderable: false
                }]
            });
        });

        function updateOrder(event, orderId) {
            event.preventDefault();
            const form = document.getElementById(`orderForm${orderId}`);
            const formData = new FormData(form);
            formData.append('order_id', orderId);
            formData.append('valider', 'update');

            fetch('save.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Commande mise à jour avec succès');
                        location.reload();
                    } else {
                        alert('Erreur lors de la mise à jour de la commande');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de la mise à jour de la commande');
                });
        }

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
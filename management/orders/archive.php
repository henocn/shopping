<?php
require '../../vendor/autoload.php';
require '../../utils/middleware.php';

verifyConnection("/management/orders/");
checkIsActive($_SESSION['user_id']);

use src\Connectbd;
use src\Order;

$cnx = Connectbd::getConnection();
$orderManager = new Order($cnx);

$deliveredOrders = [];
$totalRevenue = 0;

if (isset($_SESSION['role']) && isset($_SESSION['user_id'])) {
      if ((int)$_SESSION['role'] === 1) {
            // Admin : voir toutes les commandes livrées
            $deliveredOrders = $orderManager->getAllDeliveredOrders();
      } else {
            // Assistant : voir uniquement ses commandes livrées
            $deliveredOrders = $orderManager->getDeliveredOrdersByUserId((int)$_SESSION['user_id']);
      }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Archives des Commandes Livrées</title>
      <link rel="preload" href="https://unpkg.com/boxicons@2.1.4/fonts/boxicons.woff2" as="font" type="font/woff2" crossorigin>
      <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
      <link href="../../assets/css/index.css" rel="stylesheet">
      <link href="../../assets/css/navbar.css" rel="stylesheet">
      <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
      <link href="../../assets/css/orders.css" rel="stylesheet">
      <style>
            .stats-card {
                  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                  color: white;
                  border-radius: 5px;
                  padding: 10px;
                  margin-bottom: 10px;
            }
            .search-box {
                  margin-bottom: 20px;
            }
            .table-container {
                  background: white;
                  border-radius: 10px;
                  padding: 20px;
                  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
      </style>
</head>

<body>

      <?php include '../../includes/navbar.php'; ?>

      <main class="container-fluid my-4">
            
            <!-- En-tête avec bouton retour -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                  <h4>
                        <i class='bx bx-archive me-2'></i>
                        Archives des Commandes Livrées
                  </h4>
                  <a href="index.php" class="btn btn-outline-secondary">
                        <i class='bx bx-arrow-back me-2'></i>Retour aux commandes
                  </a>
            </div>

            <!-- Statistiques -->
            <div class="row">
                  <div class="col-md-4">
                        <div class="stats-card">
                              <h5><i class='bx bx-package me-2'></i>Total</h5>
                              <h4><?= count($deliveredOrders) ?></h4>
                        </div>
                  </div>
            </div>

            <!-- Barre de recherche -->
            <div class="search-box">
                  <input type="text" id="searchInput" class="form-control" placeholder="Rechercher par client, téléphone, produit...">
            </div>

            <!-- Tableau des commandes livrées -->
            <div class="table-container">
                  <?php if (empty($deliveredOrders)): ?>
                        <div class="text-center py-5">
                              <i class='bx bx-box' style='font-size: 64px; color: #ccc;'></i>
                              <p class="text-muted mt-3">Aucune commande livrée pour le moment.</p>
                        </div>
                  <?php else: ?>
                        <div class="table-responsive">
                              <table class="table table-hover" id="ordersTable">
                                    <thead>
                                          <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Client</th>
                                                <th scope="col">Produit</th>
                                                <th scope="col">Quantité</th>
                                                <th scope="col">Total</th>
                                                <th scope="col">Statut</th>
                                                <th scope="col">Date</th>
                                          </tr>
                                    </thead>
                                    <tbody>
                                          <?php foreach ($deliveredOrders as $order): ?>
                                                <tr>
                                                      <td><strong>#<?= htmlspecialchars($order['order_id']) ?></strong></td>
                                                      <td><?= htmlspecialchars($order['client_name']) ?></td>
                                                      <td>
                                                            <?= htmlspecialchars($order['product_name']) ?>
                                                            <?php if (!empty($order['pack_name'])): ?>
                                                                  <br><small class="text-muted">(<?= htmlspecialchars($order['pack_name']) ?>)</small>
                                                            <?php endif; ?>
                                                      </td>
                                                      <td><?= (int)$order['quantity'] ?></td>
                                                      <td><strong><?= number_format($order['total_price'], 0, ',', ' ') ?> FCFA</strong></td>
                                                      <td><span class="badge bg-success">Livré</span></td>
                                                      <td><?= date('d/m/Y H:i', strtotime($order['updated_at'])) ?></td>
                                                </tr>
                                          <?php endforeach; ?>
                                    </tbody>
                              </table>
                        </div>
                  <?php endif; ?>
            </div>

      </main>

      <?php include '../../includes/footer.php'; ?>

      <script src="../../assets/js/bootstrap.bundle.min.js"></script>
      <script>
            // Fonction de recherche
            document.getElementById('searchInput').addEventListener('keyup', function() {
                  const searchTerm = this.value.toLowerCase();
                  const tableRows = document.querySelectorAll('#ordersTable tbody tr');
                  
                  tableRows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                  });
            });

            // Initialiser les tooltips Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                  return new bootstrap.Tooltip(tooltipTriggerEl);
            });
      </script>

</body>

</html>

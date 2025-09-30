<?php
require '../../vendor/autoload.php';
require '../../utils/middleware.php';

verifyConnection("/management/orders/");
checkIsActive($_SESSION['user_id']);

use src\Connectbd;
use src\Order;

$cnx = Connectbd::getConnection();
$orderManager = new Order($cnx);

$orders = [];
$deliveredToday = [];

if (isset($_SESSION['role']) && isset($_SESSION['user_id'])) {
      if ((int)$_SESSION['role'] === 1) {
            $orders = $orderManager->getAllOrders();
            $deliveredToday = $orderManager->getOrdersToDay();
      } else {
            $orders = $orderManager->getOrdersByUserId((int)$_SESSION['user_id']);
            $deliveredToday = $orderManager->getOrdersToDay();
      }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Gestion des Commandes</title>
      <link rel="preload" href="https://unpkg.com/boxicons@2.1.4/fonts/boxicons.woff2" as="font" type="font/woff2" crossorigin>
      <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
      <link href="../../assets/css/index.css" rel="stylesheet">
      <link href="../../assets/css/navbar.css" rel="stylesheet">
      <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
      <link href="../../assets/css/order.css" rel="stylesheet">
</head>

<body>

      <?php include '../../includes/navbar.php'; ?>

      <main class="container-fluid my-4">

            <!-- Navigation par onglets -->
            <ul class="nav nav-tabs" id="ordersTabs" role="tablist">
                  <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-to-process" data-bs-toggle="tab" data-bs-target="#pane-to-process" type="button" role="tab">
                              <i class='bx bx-time-five me-2'></i>À traiter
                        </button>
                  </li>
                  <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-remind" data-bs-toggle="tab" data-bs-target="#pane-remind" type="button" role="tab">
                              <i class='bx bx-bell me-1'></i>Rappeler
                        </button>
                  </li>
                  <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-processing" data-bs-toggle="tab" data-bs-target="#pane-processing" type="button" role="tab">
                              <i class='bx bx-truck me-2'></i>Programmées
                        </button>
                  </li>
                  <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-delivered" data-bs-toggle="tab" data-bs-target="#pane-delivered" type="button" role="tab">
                              <i class='bx bx-check-circle me-2'></i>Livrées aujourd'hui
                        </button>
                  </li>
            </ul>

            <?php

            $groupedOrders = [
                  'to-process' => [],  // new + unreachable
                  'remind' => [],
                  'processing' => [],
                  'delivered' => []
            ];

            // Regrouper les commandes normales
            foreach ($orders as $o) {
                  if (isset($o['newstat'])) {
                        switch ($o['newstat']) {
                              case 'new':
                              case 'unreachable':
                                    $groupedOrders['to-process'][] = $o;
                                    break;
                              case 'remind':
                                    $groupedOrders['remind'][] = $o;
                                    break;
                              case 'processing':
                                    $groupedOrders['processing'][] = $o;
                                    break;
                        }
                  }
            }

            // Ajouter les commandes livrées du jour
            $groupedOrders['delivered'] = $deliveredToday;

            ?>

            <!-- Contenu des onglets -->
            <div class="tab-content" id="ordersTabsContent">
                  <!-- Onglet À traiter -->
                  <div class="tab-pane fade show active" id="pane-to-process" role="tabpanel">
                        <div class="row mt-3">
                              <div class="col-12">
                                    <h6>Commandes à traiter (<?= count($groupedOrders['to-process']) ?>)</h6>
                                    <?php if (empty($groupedOrders['to-process'])): ?>
                                          <p class="text-muted">Aucune commande à traiter.</p>
                                    <?php else: ?>
                                          <div class="table-responsive">
                                                <table class="table">
                                                      <thead>
                                                            <tr>
                                                                  <th scope="col">ID</th>
                                                                  <th scope="col">Client</th>
                                                                  <th scope="col">Numéro</th>
                                                                  <th scope="col">Pays</th>
                                                                  <th scope="col">Notes</th>
                                                                  <th scope="col">Produit</th>
                                                                  <th scope="col">Quantité</th>
                                                                  <th scope="col">Prix_Unitaire</th>
                                                                  <th scope="col">Prix_Total</th>
                                                                  <th scope="col">Mes_Notes</th>
                                                                  <th scope="col">Actions</th>
                                                            </tr>
                                                      </thead>
                                                      <tbody>
                                                            <?php foreach ($groupedOrders['to-process'] as $order): ?>
                                                                  <tr class="<?php echo $order['newstat'] == 'unreachable' ? 'table-danger' : ''; ?>">
                                                                        <td>#<?= htmlspecialchars($order['order_id']) ?></td>
                                                                        <td><?= htmlspecialchars($order['client_name']) ?></td>
                                                                        <td><?= htmlspecialchars($order['client_phone']) ?></td>
                                                                        <td><?= htmlspecialchars($order['client_country']) ?></td>
                                                                        <td><?= htmlspecialchars($order['client_note'] ?? '') ?></td>
                                                                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                                                                        <td><?= (int)$order['quantity'] ?></td>
                                                                        <td><?= number_format($order['unit_price'] ?? 0, 0, ',', ' ') ?> FCFA</td>
                                                                        <td><?= number_format($order['total_price'], 0, ',', ' ') ?> FCFA</td>
                                                                        <td><?= htmlspecialchars($order['manager_note'] ?? '') ?></td>
                                                                        <td>
                                                                              <button class="btn btn-outline-primary btn-sm" type="button"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#orderModal<?= (int)$order['order_id'] ?>">
                                                                                    <i class='bx bx-edit'></i>
                                                                              </button>
                                                                        </td>
                                                                  </tr>
                                                            <?php endforeach; ?>
                                                      </tbody>
                                                </table>
                                          </div>
                                    <?php endif; ?>
                              </div>
                        </div>
                  </div>

                  <!-- Onglet Rappeler -->
                  <div class="tab-pane fade" id="pane-remind" role="tabpanel">
                        <div class="row mt-3">
                              <div class="col-12">
                                    <h4>Commandes à rappeler (<?= count($groupedOrders['remind']) ?>)</h4>
                                    <?php if (empty($groupedOrders['remind'])): ?>
                                          <p class="text-muted">Aucune commande à rappeler.</p>
                                    <?php else: ?>
                                          <div class="table-responsive">
                                                <table class="table table-striped">
                                                      <thead>
                                                            <tr>
                                                                  <th>ID</th>
                                                                  <th>Client</th>
                                                                  <th>Numéro</th>
                                                                  <th>Pays</th>
                                                                  <th>Produit</th>
                                                                  <th>Quantité</th>
                                                                  <th>Prix_Total</th>
                                                                  <th>Mes_Notes</th>
                                                                  <th>Actions</th>
                                                            </tr>
                                                      </thead>
                                                      <tbody>
                                                            <?php foreach ($groupedOrders['remind'] as $order): ?>
                                                                  <tr class="newsat-remind">
                                                                        <td>#<?= $order['order_id'] ?></td>
                                                                        <td><?= htmlspecialchars($order['client_name']) ?></td>
                                                                        <td><?= htmlspecialchars($order['client_phone']) ?></td>
                                                                        <td><?= htmlspecialchars($order['client_country']) ?></td>
                                                                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                                                                        <td><?= (int)$order['quantity'] ?></td>
                                                                        <td><?= number_format($order['total_price'], 0, ',', ' ') ?> FCFA</td>
                                                                        <td><?= htmlspecialchars($order['manager_note'] ?? '') ?></td>
                                                                        <td>
                                                                              <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#orderModal<?= (int)$order['order_id'] ?>">
                                                                                    <i class='bx bx-edit'></i>
                                                                              </button>
                                                                        </td>
                                                                  </tr>
                                                            <?php endforeach; ?>
                                                      </tbody>
                                                </table>
                                          </div>
                                    <?php endif; ?>
                              </div>
                        </div>
                  </div>

                  <!-- Onglet Programmées -->
                  <div class="tab-pane fade" id="pane-processing" role="tabpanel">
                        <div class="row mt-3">
                              <div class="col-12">
                                    <h4>Commandes programmées (<?= count($groupedOrders['processing']) ?>)</h4>
                                    <?php if (empty($groupedOrders['processing'])): ?>
                                          <p class="text-muted">Aucune commande programmée.</p>
                                    <?php else: ?>
                                          <div class="table-responsive">
                                                <table class="table table-striped">
                                                      <thead>
                                                            <tr>
                                                                  <th>ID</th>
                                                                  <th>Client</th>
                                                                  <th>Numero</th>
                                                                  <th>Produit</th>
                                                                  <th>Quantité</th>
                                                                  <th>Total</th>
                                                                  <th>Statut</th>
                                                                  <th>Date</th>
                                                                  <th>Actions</th>
                                                            </tr>
                                                      </thead>
                                                      <tbody>
                                                            <?php foreach ($groupedOrders['processing'] as $order): ?>
                                                                  <tr>
                                                                        <td>#<?= $order['order_id'] ?></td>
                                                                        <td><?= htmlspecialchars($order['client_name']) ?></td>
                                                                        <td><?= htmlspecialchars($order['client_phone']) ?></td>
                                                                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                                                                        <td><?= $order['quantity'] ?></td>
                                                                        <td><?= number_format($order['total_price'], 2) ?> FCFA</td>
                                                                        <td>
                                                                              <span class="badge bg-primary"><?= $order['newstat'] ?></span>
                                                                        </td>
                                                                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                                                        <td>
                                                                              <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#orderModal<?= (int)$order['order_id'] ?>">
                                                                                    <i class='bx bx-edit'></i>
                                                                              </button>
                                                                        </td>
                                                                  </tr>
                                                            <?php endforeach; ?>
                                                      </tbody>
                                                </table>
                                          </div>
                                    <?php endif; ?>
                              </div>
                        </div>
                  </div>

                  <!-- Onglet Livrées aujourd'hui -->
                  <div class="tab-pane fade" id="pane-delivered" role="tabpanel">
                        <div class="row mt-3">
                              <div class="col-12">
                                    <h4>Commandes livrées aujourd'hui (<?= count($groupedOrders['delivered']) ?>)</h4>
                                    <?php if (empty($groupedOrders['delivered'])): ?>
                                          <p class="text-muted">Aucune commande livrée aujourd'hui.</p>
                                    <?php else: ?>
                                          <div class="table-responsive">
                                                <table class="table table-striped">
                                                      <thead>
                                                            <tr>
                                                                  <th>ID</th>
                                                                  <th>Client</th>
                                                                  <th>Produit</th>
                                                                  <th>Quantité</th>
                                                                  <th>Total</th>
                                                                  <th>Statut</th>
                                                                  <th>Date</th>
                                                            </tr>
                                                      </thead>
                                                      <tbody>
                                                            <?php foreach ($groupedOrders['delivered'] as $order): ?>
                                                                  <tr>
                                                                        <td>#<?= $order['order_id'] ?></td>
                                                                        <td><?= htmlspecialchars($order['client_name']) ?></td>
                                                                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                                                                        <td><?= $order['quantity'] ?></td>
                                                                        <td><?= number_format($order['total_price'], 2) ?> FCFA</td>
                                                                        <td>
                                                                              <span class="badge bg-success">Livré</span>
                                                                        </td>
                                                                        <td><?= date('d/m/Y H:i', strtotime($order['updated_at'])) ?></td>
                                                                  </tr>
                                                            <?php endforeach; ?>
                                                      </tbody>
                                                </table>
                                          </div>
                                    <?php endif; ?>
                              </div>
                        </div>
                  </div>
            </div>

      </main>

      <?php foreach ($orders as $order): ?>
            <?php $modalId = 'orderModal' . (int)$order['order_id']; ?>
            <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-labelledby="<?= $modalId ?>Label" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                              <div class="modal-header">
                                    <h5 class="modal-title" id="<?= $modalId ?>Label">
                                          <i class='bx bx-edit-alt me-2'></i>
                                          Commande #<?= $order['order_id'] ?> - <?= htmlspecialchars($order['product_name']) ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                              </div>
                              <form action="save.php" method="POST" id="orderForm<?= $order['order_id'] ?>">
                                    <div class="modal-body">
                                          <!-- Gestion de la commande -->
                                          <div class="row mb-4">
                                                <div class="col-md-12">
                                                      <h6 class="text-muted mb-3"><i class='bx bx-cog me-2'></i>Gestion de la commande</h6>
                                                </div>
                                                <div class="col-md-6">
                                                      <div class="mb-3">
                                                            <label for="modalQuantity<?= $order['order_id'] ?>" class="form-label fw-bold">Quantité</label>
                                                            <input type="number" class="form-control" id="modalQuantity<?= $order['order_id'] ?>" name="quantity" value="<?= (int)$order['quantity'] ?>" min="1" required>
                                                      </div>
                                                </div>
                                                <div class="col-md-6">
                                                      <div class="mb-3">
                                                            <label for="modalTotal<?= $order['order_id'] ?>" class="form-label fw-bold">Prix total (FCFA)</label>
                                                            <input type="number" class="form-control" id="modalTotal<?= $order['order_id'] ?>" name="total_price" value="<?= (int)$order['total_price'] ?>" min="0" required>
                                                      </div>
                                                </div>

                                                <!-- Actions disponibles -->
                                                <div class="col-12">
                                                      <div class="mb-3">
                                                            <label for="actionSelect<?= $order['order_id'] ?>" class="form-label fw-bold">Action à effectuer</label>
                                                            <select class="form-select" id="actionSelect<?= $order['order_id'] ?>" name="newstat" required>
                                                                  <?php
                                                                  $actions = [];
                                                                  switch ($order['newstat']) {
                                                                        case 'new':
                                                                        case 'unreachable':
                                                                              $actions = [
                                                                                    ['value' => 'processing', 'label' => 'Programmer'],
                                                                                    ['value' => 'remind', 'label' => 'Rappeler'],
                                                                                    ['value' => 'unreachable', 'label' => 'Injoignable'],
                                                                                    ['value' => 'canceled', 'label' => 'Annuler']
                                                                              ];
                                                                              break;
                                                                        case 'remind':
                                                                              $actions = [
                                                                                    ['value' => 'processing', 'label' => 'Programmer'],
                                                                                    ['value' => 'remind', 'label' => 'Rappeler'],
                                                                                    ['value' => 'unreachable', 'label' => 'Injoignable'],
                                                                                    ['value' => 'canceled', 'label' => 'Annuler']
                                                                              ];
                                                                              break;
                                                                        case 'processing':
                                                                              $actions = [
                                                                                    ['value' => 'deliver', 'label' => 'Livré'],
                                                                                    ['value' => 'canceled', 'label' => 'Annuler']
                                                                              ];
                                                                              break;
                                                                  }
                                                                  ?>
                                                                  <option value="">-- Choisir une action --</option>
                                                                  <?php foreach ($actions as $action): ?>
                                                                        <option name="newstat" value="<?= $action['value'] ?>" <?= $order['newstat'] == $action['value'] ? 'selected' : '' ?>>
                                                                              <?= $action['label'] ?>
                                                                        </option>
                                                                  <?php endforeach; ?>
                                                            </select>
                                                            <div class="form-text">
                                                                  <small class="text-muted">
                                                                        <i class="bx bx-info-circle me-1"></i>
                                                                        Statut actuel : <strong><?= ucfirst($order['newstat']) ?></strong>
                                                                  </small>
                                                            </div>
                                                      </div>
                                                </div>

                                                <div class="col-12">
                                                      <div class="mb-3">
                                                            <label for="modalManagerNote<?= $order['order_id'] ?>" class="form-label fw-bold">Note manager</label>
                                                            <textarea class="form-control" id="modalManagerNote<?= $order['order_id'] ?>" name="manager_note" rows="3" placeholder="Ajoutez vos notes sur cette commande..."><?= htmlspecialchars($order['manager_note'] ?? '') ?></textarea>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                                    <div class="modal-footer">
                                          <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                          <input type="hidden" name="valider" value="update">
                                          <input type="hidden" name="updated_at" value="<?= date('Y-m-d H:i:s') ?>">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class='bx bx-x me-2'></i>Annuler
                                          </button>
                                          <button type="submit" class="btn btn-primary">
                                                <i class='bx bx-save me-2'></i>Enregistrer
                                          </button>
                                    </div>
                              </form>
                        </div>
                  </div>
            </div>
      <?php endforeach; ?>

      <?php include '../../includes/footer.php'; ?>

      <script src="../../assets/js/bootstrap.bundle.min.js"></script>
      <script src="../../assets/js/order.js"></script>

</body>

</html>
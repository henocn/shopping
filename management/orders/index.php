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
            $deliveredToday = $orderManager->getOrdersToDayByUserId((int)$_SESSION['user_id']);
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
      <link href="../../assets/css/orders.css" rel="stylesheet">
</head>

<body>

      <?php include '../../includes/navbar.php'; ?>

      <main class="container-fluid my-4">

            <!-- En-tête avec bouton Archives -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                  <h4><i class='bx bx-package me-2'></i>Gestion des Commandes</h4>
                  <a href="archive.php" class="btn btn-outline-primary">
                        <i class='bx bx-archive me-2'></i>Voir les Archives
                  </a>
            </div>

            <?php

            $groupedOrders = [
                  'to-process' => [],  // new + unreachable + remind + processing
                  'delivered' => []
            ];

            // Regrouper toutes les commandes non livrées dans "à traiter"
            foreach ($orders as $o) {
                  if (isset($o['newstat'])) {
                        if (in_array($o['newstat'], ['new', 'unreachable', 'remind', 'processing'])) {
                              $groupedOrders['to-process'][] = $o;
                        }
                  }
            }

            // Ajouter les commandes livrées du jour
            $groupedOrders['delivered'] = $deliveredToday;

            ?>

            <!-- Navigation par onglets -->
            <ul class="nav nav-tabs" id="ordersTabs" role="tablist">
                  <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-to-process" data-bs-toggle="tab" data-bs-target="#pane-to-process" type="button" role="tab">
                              <i class='bx bx-time-five me-2'></i>À traiter
                              <span class="badge bg-danger ms-2"><?= count($groupedOrders['to-process']) ?></span>
                        </button>
                  </li>
                  <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-delivered" data-bs-toggle="tab" data-bs-target="#pane-delivered" type="button" role="tab">
                              <i class='bx bx-check-circle me-2'></i>Livrées aujourd'hui
                              <span class="badge bg-success ms-2"><?= count($groupedOrders['delivered']) ?></span>
                        </button>
                  </li>
            </ul>

            <!-- Contenu des onglets -->
            <div class="tab-content" id="ordersTabsContent">
                  <!-- Onglet À traiter -->
                  <div class="tab-pane fade show active" id="pane-to-process" role="tabpanel">
                        <div class="row mt-3">
                              <div class="col-12">
                                    <!-- Champ de recherche/filtrage compact -->
                                    <div class="card mb-3 search-compact">
                                          <div class="card-body p-2">
                                                <div class="row g-2 align-items-end">
                                                      <div class="col-md-7">
                                                            <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="🔍 Rechercher par nom, téléphone ou produit...">
                                                      </div>
                                                      <div class="col-md-5">
                                                            <select class="form-select form-select-sm" id="statusFilter">
                                                                  <option >--Filtrer--</option>
                                                                  <option value="all">Tous</option>
                                                                  <option value="new">Nouvelles</option>
                                                                  <option value="unreachable">Injoignables</option>
                                                                  <option value="remind">Rappeler</option>
                                                                  <option value="processing">Programmées</option>
                                                            </select>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>

                                    <h6>Commandes à traiter (<span id="order-count"><?= count($groupedOrders['to-process']) ?></span>)</h6>
                                    <?php if (empty($groupedOrders['to-process'])): ?>
                                          <p class="text-muted">Aucune commande à traiter.</p>
                                    <?php else: ?>
                                          <div class="table-responsive">
                                                <table class="table" id="orders-table">
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
                                                                  <th scope="col">Passer le</th>
                                                            </tr>
                                                      </thead>
                                                      <tbody>
                                                            <?php foreach ($groupedOrders['to-process'] as $order):
                                                                  $statusClass = 'order-row-default';
                                                                  switch ($order['newstat']) {
                                                                        case 'unreachable':
                                                                              $statusClass = 'order-row-unreachable';
                                                                              break;
                                                                        case 'remind':
                                                                              $statusClass = 'order-row-remind';
                                                                              break;
                                                                        case 'processing':
                                                                              $statusClass = 'order-row-processing';
                                                                              break;
                                                                  }
                                                            ?>
                                                                  <tr class="order-row <?= $statusClass ?>"
                                                                      data-status="<?= $order['newstat'] ?>"
                                                                      data-client="<?= htmlspecialchars(strtolower($order['client_name'])) ?>"
                                                                      data-phone="<?= htmlspecialchars($order['client_phone']) ?>"
                                                                      data-product="<?= htmlspecialchars(strtolower($order['product_name'])) ?>">
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
                                                                              <?php if ($order['newstat'] === 'processing'): ?>
                                                                                    <!-- Boutons directs pour les commandes programmées -->
                                                                                    <div class="order-action-group">
                                                                                          <form method="POST" action="save.php" id="quickDeliverForm<?= $order['order_id'] ?>">
                                                                                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                                                                                <input type="hidden" name="quantity" value="<?= $order['quantity'] ?>">
                                                                                                <input type="hidden" name="total_price" value="<?= $order['total_price'] ?>">
                                                                                                <input type="hidden" name="newstat" value="deliver">
                                                                                                <input type="hidden" name="manager_note" value="<?= htmlspecialchars($order['manager_note'] ?? '') ?>">
                                                                                                <input type="hidden" name="updated_at" value="<?= date('Y-m-d H:i:s') ?>">
                                                                                                <input type="hidden" name="valider" value="update">
                                                                                                <input type="hidden" name="delivery_fee" value="0">
                                                                                                <button type="button" class="btn btn-success btn-sm quick-deliver-btn" data-order-id="<?= $order['order_id'] ?>" title="Livrer">
                                                                                                      <i class='bx bx-check'></i>
                                                                                                      <span>Livrer</span>
                                                                                                </button>
                                                                                          </form>
                                                                                          <form method="POST" action="save.php" onsubmit="return confirm('Annuler cette commande ?');">
                                                                                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                                                                                <input type="hidden" name="quantity" value="<?= $order['quantity'] ?>">
                                                                                                <input type="hidden" name="total_price" value="<?= $order['total_price'] ?>">
                                                                                                <input type="hidden" name="newstat" value="canceled">
                                                                                                <input type="hidden" name="manager_note" value="<?= htmlspecialchars($order['manager_note'] ?? '') ?>">
                                                                                                <input type="hidden" name="updated_at" value="<?= date('Y-m-d H:i:s') ?>">
                                                                                                <input type="hidden" name="valider" value="update">
                                                                                                <button type="submit" class="btn btn-danger btn-sm" title="Annuler">
                                                                                                      <i class='bx bx-x'></i>
                                                                                                      <span>Annuler</span>
                                                                                                </button>
                                                                                          </form>
                                                                                    </div>
                                                                              <?php else: ?>
                                                                                    <!-- Bouton modal pour les autres statuts -->
                                                                                    <button class="btn btn-outline-primary btn-sm" type="button"
                                                                                          data-bs-toggle="modal"
                                                                                          data-bs-target="#orderModal<?= (int)$order['order_id'] ?>">
                                                                                          <i class='bx bx-edit'></i>
                                                                                    </button>
                                                                              <?php endif; ?>
                                                                        </td>
                                                                        <td><?= date('d/m/Y à H:i', strtotime($order['created_at'])) ?></td>
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
                                    <h6>Commandes livrées aujourd'hui (<?= count($groupedOrders['delivered']) ?>)</h6>
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
                                                                        <td><?= number_format($order['total_price']) ?> FCFA</td>
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
                                                                                    ['value' => 'deliver', 'label' => 'Livrer'],
                                                                                    ['value' => 'processing', 'label' => 'Programmer'],
                                                                                    ['value' => 'remind', 'label' => 'Rappeler'],
                                                                                    ['value' => 'unreachable', 'label' => 'Injoignable'],
                                                                                    ['value' => 'canceled', 'label' => 'Annuler']
                                                                              ];
                                                                              break;
                                                                        case 'remind':
                                                                              $actions = [
                                                                                    ['value' => 'deliver', 'label' => 'Livrer'],
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
                                          <input type="hidden" name="delivery_fee" id="deliveryFee<?= $order['order_id'] ?>" value="0">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class='bx bx-x me-2'></i>Annuler
                                          </button>
                                          <button type="button" class="btn btn-primary" id="submitBtn<?= $order['order_id'] ?>">
                                                <i class='bx bx-save me-2'></i>Enregistrer
                                          </button>
                                    </div>
                              </form>
                        </div>
                  </div>
            </div>
      <?php endforeach; ?>

      <!-- Modal pour les frais de livraison -->
      <?php foreach ($orders as $order): ?>
            <div class="modal fade" id="deliveryFeeModal<?= $order['order_id'] ?>" tabindex="-1" aria-labelledby="deliveryFeeModalLabel<?= $order['order_id'] ?>" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                              <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="deliveryFeeModalLabel<?= $order['order_id'] ?>">
                                          <i class='bx bx-package me-2'></i>Frais de Livraison
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                              </div>
                              <div class="modal-body">
                                          <p class="text-muted mb-3">Commande #<?= $order['order_id'] ?> - <?= htmlspecialchars($order['client_name']) ?></p>
                                          <div class="mb-3">
                                                <label for="deliveryFeeInput<?= $order['order_id'] ?>" class="form-label fw-bold">
                                                      Frais de livraison (FCFA)
                                                </label>
                                                <input type="number" 
                                                       class="form-control form-control-lg" 
                                                       id="deliveryFeeInput<?= $order['order_id'] ?>" 
                                                       placeholder="Entrez les frais de livraison" 
                                                       min="0" 
                                                       value="0">
                                                <div class="form-text">
                                                      <i class='bx bx-info-circle me-1'></i>
                                                      Laissez 0 si aucun frais de livraison
                                                </div>
                                          </div>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class='bx bx-x me-2'></i>Annuler
                                          </button>
                                          <button type="button" class="btn btn-success" onclick="confirmDelivery(<?= $order['order_id'] ?>)">
                                                <i class='bx bx-check me-2'></i>Confirmer la livraison
                                          </button>
                                    </div>
                              </div>
                        </div>
                  </div>
      <?php endforeach; ?>

      <?php include '../../includes/footer.php'; ?>

      <script src="../../assets/js/bootstrap.bundle.min.js"></script>
      <script src="../../assets/js/order.js"></script>
      <script src="../../assets/js/reload.js"></script>
      <script src="../../assets/js/filter-orders.js"></script>
      
      <script>
            let currentDeliveryContext = null;
            let deliveryModalConfirming = false;

            // Gérer le clic sur le bouton Enregistrer du modal principal
            document.querySelectorAll('[id^="submitBtn"]').forEach(button => {
                  button.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const orderId = this.id.replace('submitBtn', '');
                        const form = document.getElementById('orderForm' + orderId);
                        const selectedAction = document.getElementById('actionSelect' + orderId).value;

                        if (selectedAction === 'deliver') {
                              currentDeliveryContext = { type: 'modal', orderId: orderId };

                              const mainModalElement = document.getElementById('orderModal' + orderId);
                              const mainModal = bootstrap.Modal.getInstance(mainModalElement);
                              if (mainModal) {
                                    mainModal.hide();
                              }

                              setTimeout(() => {
                                    const deliveryModalElement = document.getElementById('deliveryFeeModal' + orderId);
                                    if (deliveryModalElement) {
                                          const feeInput = document.getElementById('deliveryFeeInput' + orderId);
                                          if (feeInput) {
                                                feeInput.value = '0';
                                                feeInput.focus();
                                          }
                                          const existingModal = bootstrap.Modal.getInstance(deliveryModalElement);
                                          const deliveryModal = existingModal || new bootstrap.Modal(deliveryModalElement);
                                          deliveryModal.show();
                                          attachDeliveryModalHandler(orderId, deliveryModalElement);
                                    }
                              }, 250);
                        } else {
                              form.submit();
                        }
                  });
            });

            // Boutons rapides "Livrer"
            document.querySelectorAll('.quick-deliver-btn').forEach(button => {
                  button.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const orderId = this.dataset.orderId;
                        currentDeliveryContext = { type: 'quick', orderId: orderId };

                        const deliveryModalElement = document.getElementById('deliveryFeeModal' + orderId);
                        if (deliveryModalElement) {
                              const feeInput = document.getElementById('deliveryFeeInput' + orderId);
                              if (feeInput) {
                                    feeInput.value = '0';
                                    feeInput.focus();
                              }
                              const existingModal = bootstrap.Modal.getInstance(deliveryModalElement);
                              const deliveryModal = existingModal || new bootstrap.Modal(deliveryModalElement);
                              deliveryModal.show();
                              attachDeliveryModalHandler(orderId, deliveryModalElement);
                        }
                  });
            });

            // Confirmer la livraison avec les frais
            function confirmDelivery(orderId) {
                  const feeInput = document.getElementById('deliveryFeeInput' + orderId);
                  const deliveryFee = feeInput ? parseInt(feeInput.value || '0', 10) : 0;

                  const deliveryModalElement = document.getElementById('deliveryFeeModal' + orderId);
                  const deliveryModal = bootstrap.Modal.getInstance(deliveryModalElement);
                  if (deliveryModal) {
                        deliveryModalConfirming = true;
                        deliveryModal.hide();
                  }

                  if (currentDeliveryContext && currentDeliveryContext.type === 'quick') {
                        const quickForm = document.getElementById('quickDeliverForm' + orderId);
                        if (quickForm) {
                              const feeField = quickForm.querySelector('input[name="delivery_fee"]');
                              if (feeField) {
                                    feeField.value = deliveryFee;
                              }
                              setTimeout(() => quickForm.submit(), 200);
                        }
                  } else {
                        const feeField = document.getElementById('deliveryFee' + orderId);
                        if (feeField) {
                              feeField.value = deliveryFee;
                        }
                        setTimeout(() => {
                              const form = document.getElementById('orderForm' + orderId);
                              if (form) {
                                    form.submit();
                              }
                        }, 200);
                  }

                  currentDeliveryContext = null;
                  deliveryModalConfirming = false;
            }

            window.confirmDelivery = confirmDelivery;

            function attachDeliveryModalHandler(orderId, modalElement) {
                  if (!modalElement || modalElement.dataset.handlerAttached === '1') {
                        return;
                  }

                  modalElement.addEventListener('hidden.bs.modal', function () {
                        if (deliveryModalConfirming) {
                              deliveryModalConfirming = false;
                              currentDeliveryContext = null;
                              return;
                        }

                        if (currentDeliveryContext && currentDeliveryContext.type === 'modal' && currentDeliveryContext.orderId === orderId) {
                              const mainModalElement = document.getElementById('orderModal' + orderId);
                              if (mainModalElement) {
                                    const existingMainModal = bootstrap.Modal.getInstance(mainModalElement);
                                    const mainModal = existingMainModal || new bootstrap.Modal(mainModalElement);
                                    mainModal.show();
                              }
                        }

                        currentDeliveryContext = null;
                  });

                  modalElement.dataset.handlerAttached = '1';
            }
      </script>

</body>

</html>
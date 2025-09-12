<?php
require '../../vendor/autoload.php';
require '../../utils/middleware.php';

verifyConnection("/shopping/management/orders/");
checkIsActive($_SESSION['user_id']);

use src\Connectbd;
use src\Order;

$cnx = Connectbd::getConnection();
$orderManager = new Order($cnx);

$orders = [];

if (isset($_SESSION['role']) && isset($_SESSION['user_id'])) {
    if ((int)$_SESSION['role'] === 1) {
        $orders = $orderManager->getAllOrders();
    } else {
        $orders = $orderManager->getOrdersByUserId((int)$_SESSION['user_id']);
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes</title>
    <link rel="preload" href="https://unpkg.com/boxicons@2.1.4/fonts/boxicons.woff2" as="font" type="font/woff2" crossorigin>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/index.css" rel="stylesheet">
    <link href="../../assets/css/orders.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="../../assets/css/order.css" rel="stylesheet">
    <!--<link rel="stylesheet" href="../../assets/css/index.css">-->
</head>

<body>
    <?php include '../../includes/navbar.php'; ?>

    <main class="container-fluid my-4">

        <!-- Navigation par onglets harmonisée -->
        <ul class="nav nav-tabs" id="ordersTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-status-processing" data-bs-toggle="tab" data-bs-target="#pane-status-processing" type="button" role="tab">
                    <i class='bx bx-time-five me-2'></i>En cours
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-status-validated" data-bs-toggle="tab" data-bs-target="#pane-status-validated" type="button" role="tab">
                    <i class='bx bx-check-circle me-2'></i>Validé
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-status-canceled" data-bs-toggle="tab" data-bs-target="#pane-status-canceled" type="button" role="tab">
                    <i class='bx bx-x-circle me-2'></i>Annulé
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-status-rejected" data-bs-toggle="tab" data-bs-target="#pane-status-rejected" type="button" role="tab">
                    <i class='bx bx-shield-x me-2'></i>Rejeté
                </button>
            </li>

            <!-- Séparateur visuel -->
            <li class="nav-item ms-auto" role="presentation">
                <span class="nav-link text-muted border-0 pe-0">Actions :</span>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-action-remind" data-bs-toggle="tab" data-bs-target="#pane-action-remind" type="button" role="tab">
                    <i class='bx bx-bell me-2'></i>Relancer
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-action-call" data-bs-toggle="tab" data-bs-target="#pane-action-call" type="button" role="tab">
                    <i class='bx bx-phone me-2'></i>Appeler
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-action-unreachable" data-bs-toggle="tab" data-bs-target="#pane-action-unreachable" type="button" role="tab">
                    <i class='bx bx-phone-off me-2'></i>Injoignable
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-action-done" data-bs-toggle="tab" data-bs-target="#pane-action-done" type="button" role="tab">
                    <i class='bx bx-check-double me-2'></i>Terminé
                </button>
            </li>
        </ul>

        <div class="tab-content pt-3" id="ordersTabsContent">
            <!-- Panneaux par statut -->
            <?php
            $statuses = ['processing' => 'warning', 'validated' => 'primary', 'canceled' => 'secondary', 'rejected' => 'danger'];
            $actions = ['remind' => 'info', 'call' => 'dark', 'unreachable' => 'light', 'done' => 'success'];
            $groupedByStatus = ['processing' => [], 'validated' => [], 'canceled' => [], 'rejected' => []];
            $groupedByAction = ['remind' => [], 'call' => [], 'unreachable' => [], 'done' => []];
            foreach ($orders as $o) {
                if (isset($groupedByStatus[$o['status']])) $groupedByStatus[$o['status']][] = $o;
                if (!empty($o['action']) && isset($groupedByAction[$o['action']])) $groupedByAction[$o['action']][] = $o;
            }
            ?>

            <?php foreach ($statuses as $statusKey => $badgeColor): ?>
                <div class="tab-pane fade <?= $statusKey === 'processing' ? 'show active' : '' ?>" id="pane-status-<?= $statusKey ?>" role="tabpanel" aria-labelledby="tab-status-<?= $statusKey ?>">
                    <div class="row g-3" id="list-status-<?= $statusKey ?>">
                        <?php if (empty($groupedByStatus[$statusKey])): ?>
                            <div class="col-12">
                                <div class="empty-state">
                                    <i class='bx bx-package'></i>
                                    <h5>Aucune commande</h5>
                                    <p class="mb-0">Aucune commande dans cette catégorie pour le moment.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($groupedByStatus[$statusKey] as $order): ?>
                                <?php
                                $badgeClass = [
                                    'processing' => 'warning',
                                    'validated'  => 'success',
                                    'canceled'   => 'secondary',
                                    'rejected'   => 'danger',
                                ][$order['status']] ?? 'light';
                                ?>
                                <div class="col-12 col-md-6 col-xl-4" id="order-card-<?= $order['order_id'] ?>" data-status="<?= htmlspecialchars($order['status']) ?>" data-action="<?= htmlspecialchars($order['action'] ?? '') ?>">
                                    <div class="card h-100 order-card">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start mb-3">
                                                <img src="../../uploads/main/<?= !empty($order['product_image']) ? htmlspecialchars($order['product_image']) : 'default.jpg' ?>"
                                                    alt="<?= htmlspecialchars($order['product_name']) ?>"
                                                    class="product-image me-3"
                                                    onerror="this.src='../../assets/image/default.jpg'">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <h6 class="mb-1 fw-bold text-primary">#<?= $order['order_id'] ?></h6>
                                                        <span class="status-badge badge bg-<?= $badgeClass ?> text-uppercase">
                                                            <?= htmlspecialchars($order['status']) ?>
                                                        </span>
                                                    </div>
                                                    <p class="mb-1 fw-semibold"><?= htmlspecialchars($order['product_name']) ?></p>
                                                    <small class="text-muted d-block">
                                                        <i class='bx bx-user me-1'></i><?= htmlspecialchars($order['client_name']) ?>
                                                    </small>
                                                    <small class="text-muted d-block">
                                                        <i class='bx bx-map me-1'></i><?= htmlspecialchars($order['client_country']) ?>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="text-center">
                                                    <small class="text-muted d-block">Quantité</small>
                                                    <span class="fw-bold"><?= (int)$order['quantity'] ?></span>
                                                </div>
                                                <div class="text-center">
                                                    <small class="text-muted d-block">Prix unitaire</small>
                                                    <span class="fw-bold text-success"><?= number_format($order['unit_price'] ?? 0, 0, ',', ' ') ?> FCFA</span>
                                                </div>
                                                <div class="text-center">
                                                    <small class="text-muted d-block">Total</small>
                                                    <span class="fw-bold text-primary"><?= number_format($order['total_price'], 0, ',', ' ') ?> FCFA</span>
                                                </div>
                                            </div>

                                            <div class="d-grid">
                                                <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#detailsModal<?= $order['order_id'] ?>">
                                                    <i class='bx bx-edit-alt me-2'></i>Modifier la commande
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal de détails/édition harmonisée -->
                                    <div class="modal fade" id="detailsModal<?= $order['order_id'] ?>" tabindex="-1" aria-labelledby="detailsModalLabel<?= $order['order_id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="detailsModalLabel<?= $order['order_id'] ?>">
                                                        <i class='bx bx-edit-alt me-2'></i>
                                                        Commande #<?= $order['order_id'] ?> - <?= htmlspecialchars($order['product_name']) ?>
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="save.php" method="POST" id="orderForm<?= $order['order_id'] ?>" onsubmit="updateOrder(event, <?= $order['order_id'] ?>)">

                                                        <!-- Informations client (lecture seule) -->
                                                        <div class="row mb-4">
                                                            <div class="col-md-12">
                                                                <h6 class="text-muted mb-3"><i class='bx bx-user me-2'></i>Informations Client</h6>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Nom du client</label>
                                                                    <p class="form-control-plaintext"><?= htmlspecialchars($order['client_name']) ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Numero du client</label>
                                                                    <p class="form-control-plaintext"><?= htmlspecialchars($order['client_phone']) ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Pays</label>
                                                                    <p class="form-control-plaintext"><?= htmlspecialchars($order['client_country']) ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Adresse</label>
                                                                    <p class="form-control-plaintext"><?= htmlspecialchars($order['client_adress']) ?></p>
                                                                </div>
                                                            </div>
                                                            <?php if (!empty($order['client_note'])): ?>
                                                                <div class="col-md-12">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Note du client</label>
                                                                        <p class="form-control-plaintext bg-light p-2 rounded"><?= htmlspecialchars($order['client_note']) ?></p>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>

                                                        <!-- Détails produit -->
                                                        <div class="row mb-4">
                                                            <div class="col-md-12">
                                                                <h6 class="text-muted mb-3"><i class='bx bx-package me-2'></i>Détails Produit</h6>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Pack sélectionné</label>
                                                                    <p class="form-control-plaintext"><?= htmlspecialchars($order['pack_name'] ?: 'Pack standard') ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Prix unitaire</label>
                                                                    <p class="form-control-plaintext text-success fw-bold"><?= number_format($order['unit_price'] ?? 0, 0, ',', ' ') ?> FCFA</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Champs modifiables -->
                                                        <div class="row mb-4">
                                                            <div class="col-md-12">
                                                                <h6 class="text-muted mb-3"><i class='bx bx-cog me-2'></i>Gestion de la Commande</h6>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="quantity<?= $order['order_id'] ?>" class="form-label fw-bold">Quantité</label>
                                                                    <input type="number" class="form-control" id="quantity<?= $order['order_id'] ?>"
                                                                        name="quantity" value="<?= $order['quantity'] ?>" min="1" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="total_price<?= $order['order_id'] ?>" class="form-label fw-bold">Prix Total (FCFA)</label>
                                                                    <input type="number" class="form-control" id="total_price<?= $order['order_id'] ?>"
                                                                        name="total_price" value="<?= $order['total_price'] ?>" min="0" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="status<?= $order['order_id'] ?>" class="form-label fw-bold">Statut</label>
                                                                    <select class="form-select" id="status<?= $order['order_id'] ?>" name="status" required>
                                                                        <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>En cours</option>
                                                                        <option value="validated" <?= $order['status'] == 'validated' ? 'selected' : '' ?>>Validé</option>
                                                                        <option value="canceled" <?= $order['status'] == 'canceled' ? 'selected' : '' ?>>Annulé</option>
                                                                        <option value="rejected" <?= $order['status'] == 'rejected' ? 'selected' : '' ?>>Rejeté</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="action<?= $order['order_id'] ?>" class="form-label fw-bold">Action</label>
                                                                    <select class="form-select" id="action<?= $order['order_id'] ?>" name="action" required>
                                                                        <option value="">Sélectionner une action</option>
                                                                        <option value="remind" <?= isset($order['action']) && $order['action'] == 'remind' ? 'selected' : '' ?>>Relancer</option>
                                                                        <option value="call" <?= isset($order['action']) && $order['action'] == 'call' ? 'selected' : '' ?>>Appeler</option>
                                                                        <option value="unreachable" <?= isset($order['action']) && $order['action'] == 'unreachable' ? 'selected' : '' ?>>Injoignable</option>
                                                                        <option value="done" <?= isset($order['action']) && $order['action'] == 'done' ? 'selected' : '' ?>>Terminé</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label for="managerNote<?= $order['order_id'] ?>" class="form-label fw-bold">Note Manager</label>
                                                                    <textarea class="form-control" id="managerNote<?= $order['order_id'] ?>"
                                                                        name="manager_note" rows="3" placeholder="Ajoutez vos notes sur cette commande..."><?= htmlspecialchars($order['manager_note'] ?? '') ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                                            <input type="hidden" name="valider" value="update">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <i class='bx bx-x me-2'></i>Annuler
                                                            </button>
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class='bx bx-save me-2'></i>Enregistrer les modifications
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Panneaux par action -->
            <?php foreach ($actions as $actionKey => $badgeColor): ?>
                <div class="tab-pane fade" id="pane-action-<?= $actionKey ?>" role="tabpanel" aria-labelledby="tab-action-<?= $actionKey ?>">
                    <div class="row g-3" id="list-action-<?= $actionKey ?>">
                        <?php if (empty($groupedByAction[$actionKey])): ?>
                            <div class="col-12">
                                <div class="empty-state">
                                    <i class='bx bx-task'></i>
                                    <h5>Aucune action</h5>
                                    <p class="mb-0">Aucune commande nécessitant cette action pour le moment.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($groupedByAction[$actionKey] as $order): ?>
                                <?php
                                $badgeClass = [
                                    'processing' => 'warning',
                                    'validated'  => 'success',
                                    'canceled'   => 'secondary',
                                    'rejected'   => 'danger',
                                ][$order['status']] ?? 'light';

                                $actionBadgeClass = [
                                    'remind' => 'info',
                                    'call' => 'dark',
                                    'unreachable' => 'warning',
                                    'done' => 'success',
                                ][$actionKey] ?? 'secondary';
                                ?>
                                <div class="col-12 col-md-6 col-xl-4" id="order-card-action-<?= $order['order_id'] ?>" data-status="<?= htmlspecialchars($order['status']) ?>" data-action="<?= htmlspecialchars($order['action'] ?? '') ?>">
                                    <div class="card h-100 order-card">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start mb-3">
                                                <img src="../../uploads/main/<?= !empty($order['product_image']) ? htmlspecialchars($order['product_image']) : 'default.jpg' ?>"
                                                    alt="<?= htmlspecialchars($order['product_name']) ?>"
                                                    class="product-image me-3"
                                                    onerror="this.src='../../assets/image/default.jpg'">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <h6 class="mb-1 fw-bold text-primary">#<?= $order['order_id'] ?></h6>
                                                        <div class="d-flex gap-1">
                                                            <span class="status-badge badge bg-<?= $badgeClass ?> text-uppercase">
                                                                <?= htmlspecialchars($order['status']) ?>
                                                            </span>
                                                            <span class="status-badge badge bg-<?= $actionBadgeClass ?> text-uppercase">
                                                                <?= htmlspecialchars($actionKey) ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <p class="mb-1 fw-semibold"><?= htmlspecialchars($order['product_name']) ?></p>
                                                    <small class="text-muted d-block">
                                                        <i class='bx bx-user me-1'></i><?= htmlspecialchars($order['client_name']) ?>
                                                    </small>
                                                    <small class="text-muted d-block">
                                                        <i class='bx bx-map me-1'></i><?= htmlspecialchars($order['client_country']) ?>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="text-center">
                                                    <small class="text-muted d-block">Quantité</small>
                                                    <span class="fw-bold"><?= (int)$order['quantity'] ?></span>
                                                </div>
                                                <div class="text-center">
                                                    <small class="text-muted d-block">Prix unitaire</small>
                                                    <span class="fw-bold text-success"><?= number_format($order['unit_price'] ?? 0, 0, ',', ' ') ?> FCFA</span>
                                                </div>
                                                <div class="text-center">
                                                    <small class="text-muted d-block">Total</small>
                                                    <span class="fw-bold text-primary"><?= number_format($order['total_price'], 0, ',', ' ') ?> FCFA</span>
                                                </div>
                                            </div>

                                            <div class="d-grid">
                                                <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#detailsModalAction<?= $order['order_id'] ?>">
                                                    <i class='bx bx-edit-alt me-2'></i>Modifier la commande
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal de détails/édition pour l'onglet action -->
                                    <div class="modal fade" id="detailsModalAction<?= $order['order_id'] ?>" tabindex="-1" aria-labelledby="detailsModalActionLabel<?= $order['order_id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="detailsModalActionLabel<?= $order['order_id'] ?>">
                                                        <i class='bx bx-task me-2'></i>
                                                        Commande #<?= $order['order_id'] ?> - <?= htmlspecialchars($order['product_name']) ?>
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="save.php" method="POST" id="orderFormAction<?= $order['order_id'] ?>" onsubmit="updateOrder(event, <?= $order['order_id'] ?>)">

                                                        <!-- Informations client (lecture seule) -->
                                                        <div class="row mb-4">
                                                            <div class="col-md-12">
                                                                <h6 class="text-muted mb-3"><i class='bx bx-user me-2'></i>Informations Client</h6>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Nom du client</label>
                                                                    <p class="form-control-plaintext"><?= htmlspecialchars($order['client_name']) ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Numero du client</label>
                                                                    <p class="form-control-plaintext"><?= htmlspecialchars($order['client_phone']) ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Pays</label>
                                                                    <p class="form-control-plaintext"><?= htmlspecialchars($order['client_country']) ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Adresse</label>
                                                                    <p class="form-control-plaintext"><?= htmlspecialchars($order['client_adress']) ?></p>
                                                                </div>
                                                            </div>
                                                            <?php if (!empty($order['client_note'])): ?>
                                                                <div class="col-md-12">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Note du client</label>
                                                                        <p class="form-control-plaintext bg-light p-2 rounded"><?= htmlspecialchars($order['client_note']) ?></p>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>

                                                        <!-- Détails produit -->
                                                        <div class="row mb-4">
                                                            <div class="col-md-12">
                                                                <h6 class="text-muted mb-3"><i class='bx bx-package me-2'></i>Détails Produit</h6>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Pack sélectionné</label>
                                                                    <p class="form-control-plaintext"><?= htmlspecialchars($order['pack_name'] ?: 'Pack standard') ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Prix unitaire</label>
                                                                    <p class="form-control-plaintext text-success fw-bold"><?= number_format($order['unit_price'] ?? 0, 0, ',', ' ') ?> FCFA</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Champs modifiables -->
                                                        <div class="row mb-4">
                                                            <div class="col-md-12">
                                                                <h6 class="text-muted mb-3"><i class='bx bx-cog me-2'></i>Gestion de la Commande</h6>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Quantité</label>
                                                                    <input type="number" class="form-control" name="quantity" value="<?= (int)$order['quantity'] ?>" min="1" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Prix total (FCFA)</label>
                                                                    <input type="number" class="form-control" name="total_price" value="<?= (int)$order['total_price'] ?>" min="0" step="1" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Statut</label>
                                                                    <select class="form-select" name="status" required>
                                                                        <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>En cours</option>
                                                                        <option value="validated" <?= $order['status'] == 'validated' ? 'selected' : '' ?>>Validé</option>
                                                                        <option value="canceled" <?= $order['status'] == 'canceled' ? 'selected' : '' ?>>Annulé</option>
                                                                        <option value="rejected" <?= $order['status'] == 'rejected' ? 'selected' : '' ?>>Rejeté</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Action</label>
                                                                    <select class="form-select" name="action" required>
                                                                        <option value="remind" <?= (!empty($order['action']) && $order['action'] == 'remind') ? 'selected' : '' ?>>Relancer</option>
                                                                        <option value="call" <?= (!empty($order['action']) && $order['action'] == 'call') ? 'selected' : '' ?>>Appeler</option>
                                                                        <option value="unreachable" <?= (!empty($order['action']) && $order['action'] == 'unreachable') ? 'selected' : '' ?>>Injoignable</option>
                                                                        <option value="done" <?= (!empty($order['action']) && $order['action'] == 'done') ? 'selected' : '' ?>>Terminé</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Note manager</label>
                                                                    <textarea class="form-control" name="manager_note" rows="3" placeholder="Ajoutez vos notes sur cette commande..."><?= htmlspecialchars($order['manager_note'] ?? '') ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                                            <input type="hidden" name="valider" value="update">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <i class='bx bx-x me-2'></i>Annuler
                                                            </button>
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class='bx bx-save me-2'></i>Enregistrer les modifications
                                                            </button>
                                                        </div>

                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include '../../includes/footer.php'; ?>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/order.js"></script>

</body>

</html>
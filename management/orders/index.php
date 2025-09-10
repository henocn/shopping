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
    <!-- Optimisation du chargement des polices -->
    <link rel="preload" href="https://unpkg.com/boxicons@2.1.4/fonts/boxicons.woff2" as="font" type="font/woff2" crossorigin>
    
    <!-- CSS Bootstrap et personnalisé -->
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/index.css" rel="stylesheet">
    <link href="../../assets/css/orders.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <style>
        /* Variables CSS pour la cohérence */
        :root {
            --primary-gradient: linear-gradient(135deg, #007bff, #0056b3);
            --border-radius: 8px;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 15px rgba(0,0,0,0.15);
            --transition: all 0.3s ease;
        }

        /* Amélioration des onglets */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 2rem;
        }
        
        .nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            background: none;
            color: #6c757d;
            font-weight: 500;
            padding: 1rem 1.5rem;
            transition: var(--transition);
            position: relative;
        }
        
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: #dee2e6;
            background-color: #f8f9fa;
            color: #495057;
        }
        
        .nav-tabs .nav-link.active {
            background: var(--primary-gradient);
            color: white !important;
            border-bottom-color: #ffc107;
            font-weight: 600;
            box-shadow: var(--shadow-sm);
        }

        /* Amélioration des cartes de commandes */
        .order-card {
            border: none;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            border-radius: var(--border-radius);
            overflow: hidden;
            position: relative;
        }
        
        .order-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #dee2e6;
            transition: var(--transition);
        }
        
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Couleurs des cartes par statut */
        .order-card[data-status="processing"]::before { background: #ffc107; }
        .order-card[data-status="validated"]::before { background: #28a745; }
        .order-card[data-status="canceled"]::before { background: #6c757d; }
        .order-card[data-status="rejected"]::before { background: #dc3545; }

        /* Couleurs des cartes par action */
        .order-card[data-action="remind"]::before { background: #17a2b8; }
        .order-card[data-action="call"]::before { background: #343a40; }
        .order-card[data-action="unreachable"]::before { background: #f8f9fa; border-right: 1px solid #dee2e6; }
        .order-card[data-action="done"]::before { background: #28a745; }

        /* Images produits */
        .product-image {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #f8f9fa;
            transition: var(--transition);
        }

        .order-card:hover .product-image {
            border-color: #007bff;
        }

        /* Badges de statut */
        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 0.4em 0.8em;
            border-radius: 50px;
        }

        /* Boutons harmonisés */
        .btn-outline-primary {
            border-color: #007bff;
            color: #007bff;
            transition: var(--transition);
        }

        .btn-outline-primary:hover {
            background: var(--primary-gradient);
            border-color: #0056b3;
            color: white;
            transform: translateY(-1px);
        }

        /* Modales améliorées */
        .modal-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 2px solid #dee2e6;
            padding: 1.5rem;
        }

        .modal-title {
            color: #495057;
            font-weight: 600;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            border-top: 2px solid #dee2e6;
            padding: 1.5rem;
            background-color: #f8f9fa;
        }

        /* États vides */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
            background: #f8f9fa;
            border-radius: var(--border-radius);
            border: 1px dashed #dee2e6;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* En-tête de page */
        .page-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #dee2e6;
        }

        .page-title {
            color: #495057;
            font-weight: 700;
            margin: 0;
        }

        .orders-counter {
            background: var(--primary-gradient);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: var(--shadow-sm);
        }

        /* Formulaires */
        .form-control, .form-select {
            border-radius: 6px;
            border: 1px solid #ced4da;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Fallback pour les icônes en cas de chargement lent */
        .bx:before {
            font-family: 'boxicons', Arial, sans-serif;
        }

        .bx-edit-alt:before { content: '✏️'; }
        .bx-trash:before { content: '🗑️'; }
        .bx-info-circle:before { content: 'ℹ️'; }

        /* Responsive design amélioré */
        @media (max-width: 768px) {
            .nav-tabs {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .nav-tabs .nav-link {
                flex: 1;
                text-align: center;
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
                margin-bottom: 0.25rem;
            }
            
            .page-header {
                text-align: center;
                padding: 1.5rem;
            }
            
            .orders-counter {
                margin-top: 1rem;
                display: inline-block;
            }
            
            .modal-dialog {
                margin: 1rem;
            }
            
            .modal-body {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .order-card .d-flex {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .btn-outline-primary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <?php include '../../includes/navbar.php'; ?>

    <main class="container-fluid my-4">
        <!-- En-tête de page harmonisé -->
        <div class="page-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h1 class="page-title">
                    <i class='bx bx-package me-2'></i>
                    Gestion des Commandes
                </h1>
                <div class="orders-counter" id="total-orders">
                    <i class='bx bx-list-ul me-2'></i>
                    Total: <span id="orders-count"><?= count($orders) ?></span> commandes
                </div>
            </div>
        </div>

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

    <!-- Scripts optimisés -->
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Gestion optimisée des commandes avec AJAX
        
        // Cache des éléments DOM pour de meilleures performances
        const domCache = {
            ordersCount: document.getElementById('orders-count'),
            totalOrders: document.getElementById('total-orders')
        };

        // Mise à jour du compteur de commandes
        function updateOrdersCount() {
            const allCards = document.querySelectorAll('[id^="order-card-"]');
            if (domCache.ordersCount) {
                domCache.ordersCount.textContent = allCards.length;
            }
        }

        // Fonction pour trouver la carte de commande
        function findOrderCard(orderId) {
            return document.getElementById('order-card-' + orderId) || 
                   document.getElementById('order-card-action-' + orderId);
        }

        // Déplacement optimisé des cartes entre les listes
        function moveCardToStatusList(orderId, status, action) {
            const card = findOrderCard(orderId);
            if (!card) {
                console.warn(`Carte de commande ${orderId} introuvable`);
                return;
            }

            // Mise à jour des attributs de données
            card.setAttribute('data-status', status);
            if (action) {
                card.setAttribute('data-action', action);
            }

            // Trouver la liste de destination pour le statut
            const statusList = document.getElementById('list-status-' + status);
            if (statusList) {
                // Animation de disparition puis réapparition
                card.style.opacity = '0.5';
                card.style.transform = 'scale(0.95)';
                
                setTimeout(() => {
                    statusList.appendChild(card);
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                    
                    // Mise à jour du badge de statut dans la carte
                    updateOrderCardBadge(card, status);
                }, 200);
            }

            // Si une action est définie, aussi déplacer vers la liste d'action
            if (action) {
                const actionList = document.getElementById('list-action-' + action);
                if (actionList) {
                    // Cloner la carte pour la liste d'action si nécessaire
                    const actionCard = card.cloneNode(true);
                    actionCard.id = 'order-card-action-' + orderId;
                    actionList.appendChild(actionCard);
                }
            }
        }

        // Mise à jour du badge de statut dans une carte
        function updateOrderCardBadge(card, status) {
            const badge = card.querySelector('.status-badge');
            if (badge) {
                const badgeClasses = {
                    'processing': 'bg-warning',
                    'validated': 'bg-success',
                    'canceled': 'bg-secondary',
                    'rejected': 'bg-danger'
                };
                
                // Nettoyer les anciennes classes de badge
                badge.className = badge.className.replace(/bg-\w+/g, '');
                badge.classList.add('status-badge', 'badge', 'text-uppercase');
                badge.classList.add(badgeClasses[status] || 'bg-light');
                badge.textContent = status;
            }
        }

        // Fermeture propre des modales
        function closeModal(modalId) {
            const modalEl = document.getElementById(modalId);
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
                
                // Nettoyage des artifacts de modal
                setTimeout(() => {
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                        backdrop.remove();
                    });
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }, 300);
            }
        }

        // Fonction principale de mise à jour des commandes
        function updateOrder(event, orderId) {
            event.preventDefault();
            
            // Indicateur de chargement
            const submitButton = event.target.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>Enregistrement...';
            submitButton.disabled = true;

            const form = event.target;
            const formData = new FormData(form);
            
            // Assurer que l'ID de commande est inclus
            formData.set('order_id', orderId);
            formData.set('valider', 'update');

            fetch('save.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'fetch'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (!data || !data.success) {
                    throw new Error(data?.message || "Erreur lors de la mise à jour");
                }

                // Mise à jour réussie
                console.log('Commande mise à jour:', data);
                
                // Déplacer la carte vers la bonne liste
                moveCardToStatusList(orderId, data.status, data.action);
                
                // Fermer les modales
                closeModal('detailsModal' + orderId);
                closeModal('detailsModalAction' + orderId);
                
                // Notification de succès
                showNotification('Commande mise à jour avec succès', 'success');
                
                // Mise à jour du compteur
                updateOrdersCount();
            })
            .catch(error => {
                console.error('Erreur lors de la mise à jour:', error);
                showNotification('Erreur lors de la mise à jour: ' + error.message, 'error');
            })
            .finally(() => {
                // Restaurer le bouton
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        }

        // Système de notifications
        function showNotification(message, type = 'info') {
            // Créer l'élément de notification
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 1060; max-width: 300px;';
            notification.innerHTML = `
                <i class='bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-circle'} me-2'></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto-suppression après 5 secondes
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Calcul automatique du prix total
        function setupPriceCalculation() {
            document.addEventListener('input', function(e) {
                if (e.target.name === 'quantity') {
                    const form = e.target.closest('form');
                    const orderId = form.querySelector('input[name="order_id"]').value;
                    const totalPriceInput = form.querySelector('input[name="total_price"]');
                    
                    // Récupérer le prix unitaire depuis les données de la page
                    const orderCard = findOrderCard(orderId);
                    if (orderCard) {
                        const unitPriceElement = orderCard.querySelector('.text-success');
                        if (unitPriceElement) {
                            const unitPriceText = unitPriceElement.textContent.replace(/[^\d]/g, '');
                            const unitPrice = parseInt(unitPriceText) || 0;
                            const quantity = parseInt(e.target.value) || 1;
                            
                            if (totalPriceInput) {
                                totalPriceInput.value = unitPrice * quantity;
                            }
                        }
                    }
                }
            });
        }

        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            // Mise à jour du compteur initial
            updateOrdersCount();
            
            // Configuration du calcul automatique des prix
            setupPriceCalculation();
            
            // Améliorer l'accessibilité des onglets
            const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabButtons.forEach(button => {
                button.addEventListener('shown.bs.tab', function(e) {
                    console.log('Onglet activé:', e.target.id);
                });
            });
            
            console.log('Gestion des commandes initialisée');
        });

        // Exposition globale pour compatibilité
        window.updateOrder = updateOrder;
        window.moveCardToStatusList = moveCardToStatusList;
        window.showNotification = showNotification;
    </script>
</body>

</html>
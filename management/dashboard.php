<?php
require '../vendor/autoload.php';
require '../utils/middleware.php';

verifyConnection("/shopping/management/");
checkAdminAccess($_SESSION['user_id']);
checkIsActive($_SESSION['user_id']);

use src\Connectbd;
use src\Product;
use src\User;
use src\Order;

$cnx = Connectbd::getConnection();

$productObj = new Product($cnx);
$userObj = new User($cnx);
$orderObj = new Order($cnx);

// Récupérer les statistiques
$totalProducts = $productObj->getTotalProducts();
$availableProducts = $productObj->getAvailableProducts();
$unavailableProducts = $totalProducts - $availableProducts;

$totalUsers = $userObj->getTotalUsers();
$activeUsers = $userObj->getActiveUsers();
$inactiveUsers = $totalUsers - $activeUsers;

$totalOrders = $orderObj->getTotalOrders();
$processingOrders = $orderObj->getOrdersByStatus('processing');
$validatedOrders = $orderObj->getOrdersByStatus('validated');
$canceledOrders = $orderObj->getOrdersByStatus('canceled');
?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/index.css" rel="stylesheet" />
    <link href="../assets/css/navbar.css" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .stat-card {
            border-radius: 15px;
            border: none;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bg-purple-light {
            background-color: rgba(var(--purple-rgb), 0.1);
        }
        .bg-success-light {
            background-color: rgba(25, 135, 84, 0.1);
        }
        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.1);
        }
        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1);
        }
        .dashboard-title {
            color: var(--purple);
            font-weight: bold;
            border-bottom: 3px solid var(--purple);
            display: inline-block;
            padding-bottom: 5px;
        }
    </style>
</head>

<body class="d-flex flex-column h-100">
    <?php include '../includes/navbar.php'; ?>

    <main class="flex-shrink-0">
        <div class="container my-4">
            <h2 class="dashboard-title mb-4">Tableau de bord</h2>

            <!-- Statistiques des produits -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body paper-bg border-style-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-2">Total Produits</p>
                                    <h3 class="mb-0"><?php echo $totalProducts; ?></h3>
                                </div>
                                <div class="stat-icon bg-purple-light border-style-1">
                                    <i class='bx bx-package' style="color: var(--purple); font-size: 24px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body paper-bg border-style-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-2">Produits Disponibles</p>
                                    <h3 class="mb-0"><?php echo $availableProducts; ?></h3>
                                </div>
                                <div class="stat-icon bg-success-light border-style-1">
                                    <i class='bx bx-check-circle' style="color: var(--bs-success); font-size: 24px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body paper-bg border-style-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-2">Produits Inisponibles</p>
                                    <h3 class="mb-0"><?php echo $unavailableProducts; ?></h3>
                                </div>
                                <div class="stat-icon bg-danger-light border-style-1">
                                    <i class='bx bx-x-circle' style="color: var(--bs-danger); font-size: 24px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body paper-bg border-style-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-2">Total Commandes</p>
                                    <h3 class="mb-0"><?php echo $totalOrders; ?></h3>
                                </div>
                                <div class="stat-icon bg-warning-light border-style-1">
                                    <i class='bx bx-cart' style="color: var(--bs-blue); font-size: 24px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques des utilisateurs et commandes -->
            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body border-style-1">
                            <h5 class="card-title mb-4">État des Utilisateurs</h5>
                            <div class="d-flex justify-content-around text-center">
                                <div>
                                    <div class="stat-icon mx-auto bg-purple-light mb-2 border-style-2">
                                        <i class='bx bx-user-check' style="color: var(--purple); font-size: 24px;"></i>
                                    </div>
                                    <h4><?php echo $activeUsers; ?></h4>
                                    <p class="text-muted">Actifs</p>
                                </div>
                                <div>
                                    <div class="stat-icon mx-auto bg-danger-light mb-2 border-style-2">
                                        <i class='bx bx-user-x' style="color: var(--bs-danger); font-size: 24px;"></i>
                                    </div>
                                    <h4><?php echo $inactiveUsers; ?></h4>
                                    <p class="text-muted">Inactifs</p>
                                </div>
                                <div>
                                    <div class="stat-icon mx-auto bg-warning-light mb-2 border-style-2">
                                        <i class='bx bx-group' style="color: var(--bs-warning); font-size: 24px;"></i>
                                    </div>
                                    <h4><?php echo $totalUsers; ?></h4>
                                    <p class="text-muted">Total</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body border-style-1">
                            <h5 class="card-title mb-4">État des Commandes</h5>
                            <div class="d-flex justify-content-around text-center">
                                <div>
                                    <div class="stat-icon mx-auto bg-warning-light mb-2 border-style-2">
                                        <i class='bx bx-time' style="color: var(--bs-warning); font-size: 24px;"></i>
                                    </div>
                                    <h4><?php echo $processingOrders; ?></h4>
                                    <p class="text-muted">En cours</p>
                                </div>
                                <div>
                                    <div class="stat-icon mx-auto bg-success-light mb-2 border-style-2">
                                        <i class='bx bx-check-double' style="color: var(--bs-success); font-size: 24px;"></i>
                                    </div>
                                    <h4><?php echo $validatedOrders; ?></h4>
                                    <p class="text-muted">Validées</p>
                                </div>
                                <div>
                                    <div class="stat-icon mx-auto bg-danger-light mb-2 border-style-2">
                                        <i class='bx bx-x' style="color: var(--bs-danger); font-size: 24px;"></i>
                                    </div>
                                    <h4><?php echo $canceledOrders; ?></h4>
                                    <p class="text-muted">Annulées</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
<script src="../assets/js/bootstrap.bundle.min.js"></script>

</html>
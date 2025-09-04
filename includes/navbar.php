
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">

        <!-- Brand -->
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2 magenta-color" href="#">
            <h5 class="fw-bold primary-color">
                <i class='bx bx-store'></i> MyShop
            </h5>
        </a>


        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu centré -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav gap-2 mx-4">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" href="/shopping/management/dashboard.php">
                        <i class='bx bx-home'></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'products') !== false) ? 'active' : ''; ?>" href="/shopping/management/products/index.php">
                        <i class='bx bx-box'></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'orders') !== false) ? 'active' : ''; ?>" href="/shopping/management/orders/index.php">
                        <i class='bx bx-cart'></i> Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'users') !== false) ? 'active' : ''; ?>" href="/shopping/management/users/index.php">
                        <i class='bx bx-user'></i> Users
                    </a>
                </li>
            </ul>
        </div>

        <!-- Actions -->
        <div class="d-flex align-items-center">
            <a href="users/change-pass.php" class="btn btn-login">Change Password</a>
            <a href="users/logout.php" class="btn btn-signup">Logout</a>
        </div>
    </div>
</nav>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
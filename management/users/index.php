<?php
require '../../vendor/autoload.php';
require '../../utils/middleware.php';

verifyConnection("/management/users/");
checkAdminAccess($_SESSION['role']);
checkIsActive($_SESSION['user_id']);

use src\Connectbd;
use src\User;

$cnx = Connectbd::getConnection();

$user = new User($cnx);

$users = $user->getAllUsers();


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/index.css" rel="stylesheet">
    <link href="../../assets/css/users.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <?php include '../../includes/navbar.php'; ?>

    <main class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Liste des utilisateurs</h2>
            <button class="btn" style="background-color: var(--purple); color: white; border: none;" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class='bx bx-plus'></i> Ajouter
            </button>
        </div>

        <!-- Modal Ajout Utilisateur -->
        <div class="modal fade" id="addUserModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="background: var(--magenta); border-radius: 15px;">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class='bx bx-user-plus'></i> Nouvel Utilisateur
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="save.php" method="POST">
                            <div class="mb-3 position-relative">
                                <label class="form-label" style="color: var(--purple);">
                                    <i class='bx bx-envelope'></i> Email
                                </label>
                                <input type="email" class="form-control" name="email" required
                                    style="border-color: var(--purple); border-radius: 10px; padding-left: 35px;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" style="color: var(--purple);">
                                    <i class='bx bx-flag'></i> Pays
                                </label>
                                <select class="form-select" name="country" required style="border-color: var(--purple); border-radius: 10px;">
                                    <option value="BJ">🇧🇯 Bénin</option>
                                    <option value="BF">🇧🇫 Burkina Faso</option>
                                    <option value="CV">🇨🇻 Cap-Vert</option>
                                    <option value="CI">🇨🇮 Côte d'Ivoire</option>
                                    <option value="GM">🇬🇲 Gambie</option>
                                    <option value="GH">🇬🇭 Ghana</option>
                                    <option value="GN">🇬🇳 Guinée</option>
                                    <option value="GW">🇬🇼 Guinée-Bissau</option>
                                    <option value="LR">🇱🇷 Libéria</option>
                                    <option value="ML">🇲🇱 Mali</option>
                                    <option value="NE">🇳🇪 Niger</option>
                                    <option value="NG">🇳🇬 Nigeria</option>
                                    <option value="SN">🇸🇳 Sénégal</option>
                                    <option value="SL">🇸🇱 Sierra Leone</option>
                                    <option value="TG" selected>🇹🇬 Togo</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" style="color: var(--purple);">
                                    <i class='bx bx-user-check'></i> Rôle
                                </label>
                                <select class="form-select" name="role" required style="border-color: var(--purple); border-radius: 10px;">
                                    <option value="0">Manager</option>
                                    <option value="1">Admin</option>
                                </select>
                            </div>

                            <div class="modal-footer border-0">
                                <button type="button" class="btn" data-bs-dismiss="modal"
                                    style="background: var(--paper); color: var(--purple);">Annuler</button>
                                <input type="submit" class="btn" name="validate" value="Ajouter"
                                    style="background: var(--purple); color: var(--paper);" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 5%;">Id</th>
                        <th style="width: 35%;">Email</th>
                        <th style="width: 15%;">Pays</th>
                        <th class="text-center" style="width: 10%;">Status</th>
                        <th style="width: 15%;">Role</th>
                        <th class="text-center" style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($users as $user):
                    ?>
                        <tr class="<?php echo $user['is_active'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                            <td class="text-center"><?php echo $user['id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class='bx bxs-user-circle me-2' style="font-size: 2rem; color: var(--purple);"></i>
                                    <a href="mailto:<?php echo $user['email']; ?>" class="text-decoration-none" style="color: var(--purple);"><?php echo $user['email']; ?></a>
                                </div>
                            </td>
                            <td><?php echo $user['country']; ?></td>
                            <td class="text-center"><?php echo $user['is_active'] == 1 ? '<i class="bx bxs-check-circle" style="color: green;"></i>' : '<i class="bx bxs-x-circle" style="color: red;"></i>'; ?></td>
                            <td>
                                <span style="color: var(--purple); font-weight: bold;"><?php echo $user['role'] == 0 ? 'Secondaire' : 'Admin'; ?></span>
                            </td>
                            <td class="text-center">

                                <form action="save.php" method="post" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="validate" value="suspend">
                                    <button type="submit" class="btn btn-link p-0" style="color: var(--purple);">
                                        <i class='bx bxs-user-x' style="font-size: 1.5rem;" title="Suspend"></i>
                                    </button>
                                </form>
                                <form action="save.php" method="post" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="validate" value="delete">
                                    <button type="submit" class="btn btn-link p-0" style="color: var(--secondary);">
                                        <i class='bx bxs-trash' style="font-size: 1.5rem;" title="Delete"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include '../../includes/footer.php'; ?>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
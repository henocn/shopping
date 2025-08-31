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
            <a href="add.php" class="btn btn-primary" style="background-color: var(--purple); border: none;">
                <i class='bx bx-plus'></i> Ajouter
            </a>
        </div>

        <div class="table-container">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Id</th>
                        <th>Email</th>
                        <th>Pays</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Simulation des données de la base de données
                    $users = [
                        [
                            'id' => 1,
                            'email' => 'john@example.com',
                            'is_active' => 1,
                            'country' => "TG",
                            'role' => 'Admin',
                        ],
                        [
                            'id' => 2,
                            'email' => 'jane@example.com',
                            'is_active' => 1,
                            'country' => "GH",
                            'role' => 'Superadmin',
                        ],
                        [
                            'id' => 3,
                            'email' => 'bob@example.com',
                            'is_active' => 0,
                            'country' => "BF",
                            'role' => 'Manager',
                        ],
                        [
                            'id' => 4,
                            'email' => 'alice@example.com',
                            'is_active' => 1,
                            'country' => "BF",
                            'role' => 'Manager',
                        ],
                        [
                            'id' => 5,
                            'email' => 'charlie@example.com',
                            'is_active' => 1,
                            'country' => "BF",
                            'role' => 'Manager',
                        ],
                        [
                            'id' => 6,
                            'email' => 'dave@example.com',
                            'is_active' => 0,
                            'country' => "BF",
                            'role' => 'Manager',
                        ]
                    ];

                    foreach ($users as $user):
                    ?>
                        <tr class="<?php echo $user['is_active'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                            <td><?php echo $user['id']; ?></td>
                            <td style="display: flex; align-items: center;">
                                <i class='bx bxs-user-circle' style="font-size: 2rem; color: var(--purple); margin-right: 0.5rem;"></i>
                                <a href="mailto:<?php echo $user['email']; ?>" style="text-decoration: none; color: var(--purple);"><?php echo $user['email']; ?></a>
                            </td>
                            <td><?php echo $user['country']; ?></td>
                            <td><?php echo $user['is_active'] == 1 ? '<i class="bx bxs-check-circle" style="color: green;"></i>' : '<i class="bx bxs-x-circle" style="color: red;"></i>'; ?></td>
                            <td><?php echo $user['role']; ?></td>
                            <td>
                                <i class='bx bxs-edit' style="font-size: 1.5rem; color: var(--purple);" title="Edit"></i>
                                <i class='bx bxs-trash' style="font-size: 1.5rem; color: var(--secondary);" title="Delete"></i>
                                <i class='bx bxs-user-x' style="font-size: 1.5rem; color: var(--purple);" title="Suspend"></i>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include '../../includes/footer.php'; ?>

    <script src="../../../assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
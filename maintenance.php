<?php
// Variables pour les horaires de maintenance
$maintenance_start = "2026-01-03 13:00:00"; // Date et heure de début
$maintenance_end = "2026-01-03 14:30:00";   // Date et heure de fin

// Conversion en format lisible
$start_time = date('d/m/Y à H:i', strtotime($maintenance_start));
$end_time = date('d/m/Y à H:i', strtotime($maintenance_end));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance en cours</title>
    <style>
        body {
            background: #f8f9fa;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .card {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1.5rem;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .icon {
            font-size: 3rem;
            color: #ffc107;
            margin-bottom: 1rem;
        }
        .title {
            color: #333;
            margin-bottom: 1rem;
        }
        .time-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
        .contact {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🔧</div>
        <h1 class="title">Maintenance en cours</h1>
        <p>Notre service est actuellement en maintenance. Nous revenons bientôt !</p>

        <div class="time-info">
            <p><strong>Début:</strong> <?php echo $start_time; ?></p>
            <p><strong>Fin prévue:</strong> <?php echo $end_time; ?></p>
        </div>
    </div>
</body>
</html>

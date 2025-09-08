<?php
session_start();

// Récupérer le code d'erreur depuis l'URL
$error_code = isset($_GET['code']) ? (int)$_GET['code'] : 500;
var_dump($error_code);

// Définir les messages d'erreur par défaut
$error_messages = [
    400 => [
        'title' => 'Mauvaise requête',
        'description' => 'La requête envoyée au serveur est invalide ou malformée.',
        'icon' => 'bx-error'
    ],
    401 => [
        'title' => 'Non autorisé',
        'description' => 'Vous devez vous connecter pour accéder à cette page.',
        'icon' => 'bx-lock'
    ],
    403 => [
        'title' => 'Accès interdit',
        'description' => 'Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource.',
        'icon' => 'bx-shield-x'
    ],
    404 => [
        'title' => 'Page non trouvée',
        'description' => 'La page que vous recherchez n\'existe pas ou a été déplacée.',
        'icon' => 'bx-search-alt'
    ],
    500 => [
        'title' => 'Erreur serveur interne',
        'description' => 'Une erreur interne s\'est produite sur le serveur.',
        'icon' => 'bx-error-circle'
    ],
];

$current_error = $error_messages[$error_code] ?? $error_messages[500];
http_response_code($error_code);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Erreur <?= $error_code ?> - <?= $current_error['title'] ?></title>
  <link href="/shopping/assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="/shopping/assets/css/index.css" rel="stylesheet">
  <link href="/shopping/assets/css/login.css" rel="stylesheet">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>
    .error-code {
      font-size: 4rem;
      font-weight: bold;
      color: var(--purple);
      margin-bottom: 0.5rem;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .error-icon {
      font-size: 3rem;
      color: var(--purple);
      margin-bottom: 1rem;
    }

    .error-title {
      color: var(--purple);
      font-size: 1.75rem;
      margin-bottom: 0.5rem;
      font-weight: 600;
    }

    .error-description {
      color: var(--secondary);
      font-size: 1rem;
      margin-bottom: 2rem;
      line-height: 1.6;
    }

    .error-actions {
      display: flex;
      gap: 1rem;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 1rem;
    }

    .btn-error {
      background: var(--purple);
      color: white;
      border: none;
      border-radius: 12px;
      padding: 0.8rem 1.5rem;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.9rem;
    }

    .btn-error:hover {
      background: var(--secondary);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(154, 82, 255, 0.2);
      color: white;
    }

    .btn-error.secondary {
      background: transparent;
      color: var(--purple);
      border: 2px solid var(--purple);
    }

    .btn-error.secondary:hover {
      background: var(--purple);
      color: white;
    }

    @media (max-width: 576px) {
      .error-code {
        font-size: 3rem;
      }
      
      .error-icon {
        font-size: 2.5rem;
      }
      
      .error-title {
        font-size: 1.5rem;
      }
      
      .error-actions {
        flex-direction: column;
        align-items: center;
      }
      
      .btn-error {
        width: 100%;
        max-width: 200px;
        justify-content: center;
      }
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="login-header">
      <div class="logo">
        <i class='bx <?= $current_error['icon'] ?>'></i>
      </div>
      <div class="error-code"><?= $error_code ?></div>
      <h1 class="error-title"><?= $current_error['title'] ?></h1>
      <p class="error-description"><?= $current_error['description'] ?></p>
    </div>

    <div class="error-actions">
      <a href="javascript:history.back()" class="btn-error secondary">
        <i class='bx bx-arrow-back'></i>
        Retour
      </a>
    </div>
  </div>

  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script>
    // Animation d'entrée
    document.addEventListener('DOMContentLoaded', function() {
      const container = document.querySelector('.login-container');
      container.style.opacity = '0';
      container.style.transform = 'translateY(20px)';
      
      setTimeout(() => {
        container.style.transition = 'all 0.6s ease';
        container.style.opacity = '1';
        container.style.transform = 'translateY(0)';
      }, 100);
    });

    // Animation des boutons
    document.querySelectorAll('.btn-error').forEach(btn => {
      btn.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px) scale(1.05)';
      });
      
      btn.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
      });
    });
  </script>
</body>

</html>
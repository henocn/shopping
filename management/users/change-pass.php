<?php
require_once("../../utils/middleware.php");

verifyConnection("/management/login.php");

?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Changer le mot de passe</title>
  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../assets/css/index.css" rel="stylesheet">
  <link href="../../assets/css/login.css" rel="stylesheet">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
  <div class="login-container">
    <div class="login-header">
      <div class="logo">
        <i class='bx bx-lock-alt'></i>
      </div>
      <h1>Changer le mot de passe</h1>
      <p>Entrez votre mot de passe actuel et le nouveau mot de passe</p>
    </div>

    <div class="error-message" id="errorMessage">
      <?= isset($message) ? $message : ''; ?>
    </div>

    <div class="success-message" id="successMessage">
      <?= isset($success) ? $success : ''; ?>
    </div>

    <!-- ajouter un GET redirect si redirect n'est pas empty -->
    <form action="save.php" method="POST" id="changePassForm">
      <div class="form-floating">
        <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Mot de passe actuel" required>
        <label for="current_password">Mot de passe actuel</label>
      </div>
      <div class="form-floating">
        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Nouveau mot de passe" required>
        <label for="new_password">Nouveau mot de passe</label>
      </div>
      <div class="form-floating">
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
        <label for="confirm_password">Confirmer le mot de passe</label>
      </div>
      <div>
        <input type="hidden" name="validate" value="change_password">
      </div>
      <button type="submit" class="btn btn-login">
        Changer le mot de passe
      </button>
    </form>
  </div>

  <script src="../../assets/js/bootstrap.bundle.min.js"></script>
  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const success = urlParams.get('success');
    const errorMessage = document.getElementById('errorMessage');
    const successMessage = document.getElementById('successMessage');

    if (error) {
      errorMessage.style.display = 'block';
      switch (error) {
        case 'current_password_wrong':
          errorMessage.textContent = "Le mot de passe actuel est incorrect";
          break;
        case 'passwords_not_match':
          errorMessage.textContent = "Les nouveaux mots de passe ne correspondent pas";
          break;
        case 'same_password':
          errorMessage.textContent = "Le nouveau mot de passe doit être différent de l'actuel";
          break;
        case 'update_failed':
          errorMessage.textContent = "Erreur lors de la mise à jour du mot de passe";
          break;
        default:
          errorMessage.textContent = "Un erreur s'est produite, réessayez s'il vous plaît";
      }
    }

    if (success) {
      successMessage.style.display = 'block';
      successMessage.textContent = "Mot de passe changé avec succès !";
    }

    // Animation simple des champs de formulaire
    document.querySelectorAll('.form-control').forEach(input => {
      input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'translateY(-2px)';
      });

      input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'translateY(0)';
      });
    });

    // Validation côté client
    document.getElementById('changePassForm').addEventListener('submit', function(e) {
      const newPassword = document.getElementById('new_password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      
      if (newPassword !== confirmPassword) {
        e.preventDefault();
        errorMessage.style.display = 'block';
        errorMessage.textContent = "Les nouveaux mots de passe ne correspondent pas";
        return false;
      }
      
      if (newPassword.length < 6) {
        e.preventDefault();
        errorMessage.style.display = 'block';
        errorMessage.textContent = "Le nouveau mot de passe doit contenir au moins 6 caractères";
        return false;
      }
    });
  </script>
</body>

</html>

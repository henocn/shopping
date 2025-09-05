<?php

  if(isset($_SESSION) && !empty($_SESSION)){
    header('location:../dashboard.php');
  }

?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../assets/css/index.css" rel="stylesheet">
  <link href="../../assets/css/login.css" rel="stylesheet">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
  <div class="login-container">
    <div class="login-header">
      <div class="logo">
        <i class='bx bx-shopping-bag'></i>
      </div>
      <h1>Bienvenue de nouveau!</h1>
      <p>Entrez vos informations de connexion</p>
    </div>

    <div class="error-message" id="errorMessage">
      <?=$message; ?>
    </div>

    <form action="save.php" method="POST" id="loginForm">
      <div class="form-floating">
        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
        <label for="email">Addresse email</label>
      </div>
      <div class="form-floating">
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
        <label for="password">Mot de passe</label>
      </div>
      <div>
        <input type="hidden" name="validate" value="login">
      </div>
      <button type="submit" class="btn btn-login">
        Connecter
      </button>
    </form>
  </div>

  <script src="../../assets/js/bootstrap.bundle.min.js"></script>
  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const errorMessage = document.getElementById('errorMessage');

    if (error) {
      errorMessage.style.display = 'block';
      switch (error) {
        case 'failed':
          errorMessage.textContent = "Mot de passe ou email invalide";
          break;
        default:
          errorMessage.textContent = "Un erreur s'est produite, réessayez s'il vous plaît";
      }
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
  </script>
</body>

</html>
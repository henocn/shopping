<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #1d3557, #457b9d);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      padding: 40px;
      width: 100%;
      max-width: 400px;
    }
    .form-control:focus {
      border-color: #457b9d;
      box-shadow: 0 0 0 0.2rem rgba(69, 123, 157, 0.25);
    }
    .btn-custom {
      background-color: #1d3557;
      border: none;
    }
    .btn-custom:hover {
      background-color: #457b9d;
    }
  </style>
</head>

<body>
  <div class="login-card">
    <h2 class="text-center mb-4">Connexion</h2>
    <form action="save.php" method="POST">
        <!-- Username -->
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="username" class="form-control" id="username" name="username" placeholder="Votre nom utilisateur" required>
      </div>

      <!-- Mot de passe -->
      <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Votre mot de passe" required>
      </div>

      <!-- Bouton -->
      <div class="d-grid">
        <input type="hidden" name="validate" value="connexion">
        <button type="submit" class="btn btn-custom text-white">Se connecter</button>
      </div>
    </form>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

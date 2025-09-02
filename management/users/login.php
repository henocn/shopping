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
      <h1>Welcome Back!</h1>
      <p>Please enter your credentials</p>
    </div>

    <div class="error-message" id="errorMessage">
      <!-- Les messages d'erreur seront affichés ici -->
    </div>

    <form action="save.php" method="POST" id="loginForm">
      <div class="form-floating">
        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
        <label for="email">Email address</label>
      </div>
      <div class="form-floating">
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
        <label for="password">Password</label>
      </div>
      <div>
        <input type="hidden" name="validate" value="login">
      </div>
      <button type="submit" class="btn btn-login">
        Sign In
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
        case 'invalid':
          errorMessage.textContent = 'Invalid email or password';
          break;
        case 'empty':
          errorMessage.textContent = 'Please fill in all fields';
          break;
        default:
          errorMessage.textContent = 'An error occurred. Please try again';
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
<?php 
  session_start();
  $error = null;
  if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header('Location: main.php');
    exit();
  }
  if (isset($_GET['error'])) {
    $error = "Je wachtwoord en/of gebruikersnaam is incorrect.";
  }
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="css/custom.css">
    <link rel="stylesheet" href="css/custom_login.css">

    <title>Lustrum Gotcha - Login</title>
</head>
<body class="text-center">
    <form class="form-signin" action="login_minerva.php" method="POST">
      <img class="mb-4" src='img/cropped-ML_logo_ver-1-1536x1244.png' alt="" width="100" height="100">
      <h1 class="h3 mb-3 font-weight-normal">Welkom bij Gotcha!</h1>
      <?php if ($error) { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Fout!</strong> <?php echo $error;?>
        </div>
      <?php } ?>
      <label for="inputBeer" class="sr-only">Debiteurennummer</label>
      <input type="text" name="inputBeer" class="form-control" placeholder="Debiteurennummer" required autofocus>
      <label for="inputWachtwoord" class="sr-only">Wachtwoord</label>
      <input type="password" name="inputWachtwoord" class="form-control" placeholder="Wachtwoord" required>
      <div class="checkbox mb-3">
        <!--<label>
          <input type="checkbox" value="y" name="remember-me"> Blijf ingelogd
        </label>-->
      </div>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Inloggen</button>
      <p class="mt-5 mb-3">&copy; 42e Lustrum</p>
    </form>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
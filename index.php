<?php 
  session_start();
  if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header('Location: main.php');
    exit();
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

    <title>Tover Gotcha - Login</title>
</head>
<body class="text-center">
    <form class="form-signin" action="login.php" method="POST">
      <img class="mb-4" src="https://getbootstrap.com/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
      <h1 class="h3 mb-3 font-weight-normal">Tover presenteert vol trots... Gotcha!</h1>
      <label for="inputBeer" class="sr-only">Beernummer</label>
      <input type="text" name="inputBeer" class="form-control" placeholder="Beernummer" required autofocus>
      <label for="inputWachtwoord" class="sr-only">Password</label>
      <input type="password" name="inputWachtwoord" class="form-control" placeholder="Wachtwoord" required>
      <div class="checkbox mb-3">
        <!--<label>
          <input type="checkbox" value="y" name="remember-me"> Blijf ingelogd
        </label>-->
      </div>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Inloggen</button>
      <p class="mt-5 mb-3">&copy; Communicacie 2018</p>
    </form>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
<?php
include_once('includes/settings.php');
include_once('includes/config.php');

session_start();
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
  header("location: index.php");
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

  <title>Tover Gotcha!</title>
</head>
<body>
  <div class="container my-5">
    <div class="row">
      <div class="col-lg-8 bg-light">
<?php
// admin screen
if (isset($_SESSION["beer"]) && $_SESSION["beer"] === "admin") {
?>

        <div class="row">
          <div class="col-12 p-3">
            <h2>Beheerpaneel</h2>
            Vanaf hier kan je het spel klaarzetten. <?php 
            if ($peopleWithoutTargetOrCode == 1) { 
              echo "Er is op dit moment nog één persoon zonder target of code. Resetten en opnieuw aanmaken!";
            } elseif ($peopleWithoutTargetOrCode == 0) {
              echo "Er is niemand zonder code of target.<br> <strong>Het spel kan beginnen!</strong>";
            } else {
              echo "Er zijn op dit moment nog " . $peopleWithoutTargetOrCode . " mensen zonder target of code. Resetten en opnieuw aanmaken!";
            } ?><br>
            <a href="begin_game.php?function=reset_all" class="btn btn-danger">Reset targets en codes</a><a href="begin_game.php?function=generate_all" class="btn btn-success ml-3">Genereer targets en codes</a>
          </div>
        </div>

<?php
}
// main dashboard for alive players
if ($_SESSION["is_playing"] === true && $_SESSION["is_dead"] === false && $gameStarted === true) {
?>
        <div class="row">
          <div class="col-12 p-3">
            <h2>Je target is <strong class="px-2 secret" id="person" onclick="toggleSpoiler(this.id)" data-secret="<?php echo $_SESSION["target"]; ?>">klik om te tonen</strong></h2>
            <p>Je iemand weet dat hij jouw target is, kan hij je ontwijken. Dus houd je target geheim tot je je slag slaat!</p>
            <p>Je hebt nog tot <strong><?php printEndOfRound($week); ?></strong> om ten minste één persoon te vermoorden, anders lig je uit het spel.</p>
          </div>
        </div>
        <div class="row">
          <div class="col-12 p-3">
            <h2>Je geheime code is <strong class="px-2 secret" id="code" onclick="toggleSpoiler(this.id)" data-secret="<?php echo $_SESSION["own_code"]; ?>">klik om te tonen</strong></h2>
            Als je target deze code ontdekt kan hij je doodmaken, dus houd hem geheim tot je vermoord bent!
          </div>
        </div>
        <div class="row">
          <div class="col-12 p-3">
            <h2> Ik heb iemand vermoord! </h2>
            Als je iemand hebt vermoord, voer dan hieronder zijn of haar code in en druk op verzenden. Je krijgt dan direct te horen wie je hierna moet vermoorden.
            <form class="form-inline pt-3" id="submit-code-form" action="">
              <div class="row">
                <div class="col">
                  <input type="text" class="form-control" id="secret-code" name="secret-code" placeholder="1815GM27" required>
                </div>
                <div class="col pl-0">
                  <button type="submit" id="submit-button" class="btn btn-primary">Verzenden</button>
                  <img class="loading invisible ml-3" id="submitted-form-loader" src="img/loader.gif">
                </div>
              </div>
            </form>          
          </div>
        </div>
<?php
// player already dead.
} if ($_SESSION["is_playing"] && $_SESSION["is_dead"]) {
  $killer = null;
  $date = null;
  $stmt = $pdo->prepare("SELECT p.name, k.time FROM kills k INNER JOIN players p ON k.`killer_id` = p.id WHERE k.deceased_id = :my_id");
  $stmt->execute((array(":my_id" => $_SESSION["id"])));
  $result=$stmt->fetch();
  if ($result) {
    $killer = $result["name"];
    $date = $result["time"];
  }
?>
  <div class="row">
          <div class="col-12 p-3">   
            <h2> Vermoord :( </h2>
            Je bent helaas vermoord door <strong><?php echo $killer; ?></strong> op <strong><?php echo $date;?></strong>.
          </div>
        </div>
<?php
// if player has played and game has started. show kills
} if ($_SESSION["is_playing"] === true && $gameStarted === true) {
?>
        <div class="row">
          <div class="col-12 p-3">   
            <h2> Overzicht </h2>
            Je bent betrokken geweest bij de volgende moorden:
            <?php
            // TODO fix murders
              $myId = $_SESSION["id"];
              $stmt = $pdo->prepare("SELECT k.deceased_id, k.killer_id, k.time, p1.name AS killername, p2.name AS deceasedname FROM kills k JOIN players p1 ON p1.id = k.killer_id JOIN players p2 ON p2.id = k.deceased_id WHERE deceased_id=? OR killer_id=?");
              $stmt->execute(array($myId,$myId));
              $results = $stmt->fetchAll();
              if ($results) {
                echo "<ul>";
                foreach ($results as $row) {
                  if ($row["killer_id"] == $myId) {
                    echo "<li>Je hebt <strong>" . $row["deceasedname"] . "</strong> vermoord op <strong>" . $row["time"] . "</strong>.</li>";
                  } else {
                    echo "<li>Je bent vermoord door <strong>" . $row["killername"] . "</strong> op <strong>" . $row["time"] . "</strong>.</li>";
                  }
                }
                echo "</ul>";
              }
            ?>
          </div>
        </div>
<?php
// signup before game has started
} if ($_SESSION["is_playing"] === false && $gameStarted === false) {
?>
        <div class="row">
          <div class="col-12 p-3">          
            <h2> Aanmelden </h2>
            <p>Gotcha is nog niet begonnen. Het begint op <strong><?php printStartDate() ?></strong>. Wil je je aanmelden? (Kosten: &euro; 10,- op je beer)
            <a class="btn btn-success" href="subscribe.php"> Aanmelden </a>
          </div>
        </div>

<?php
// sign off before game has started
} if ($_SESSION["is_playing"] === true && $gameStarted === false) {
?>
        <div class="row">
          <div class="col-12 p-3">         
            <h2> Afmelden </h2>
            Je bent aangemeld voor Gotcha. Het begint op <strong><?php printStartDate() ?></strong>. Wil je je afmelden?
            <a class="btn btn-danger" href="unsubscribe.php"> Afmelden </a>
          </div>
        </div>

<?php
// not playing -> too late for sign up
} if ($_SESSION["is_playing"] === false && $gameStarted === true) {
?>
      
        <div class="row">
          <div class="col-12 p-3">
            <h2> Helaas Pindakaas </h2>
            Gotcha is al begonnen... Volgend jaar kan je wel meedoen!
          </div>
        </div>

<?php
} // highscores
?>

      </div>
      <div class="col-lg-4 bg-light p-3">
        <h2 class="text-center">Beste moordenaars</h2>
<?php
if ($gameStarted) {
  $stmt = $pdo->prepare("SELECT p.name, k.killer_id, COUNT(*) as nr FROM kills k, players p WHERE p.id=k.killer_id GROUP BY killer_id ORDER BY nr LIMIT 10");
  $stmt->execute();
  $results=$stmt->fetchAll();
  if ($results) {
    echo '<ol class="list-group d-flex pl-3">';
    foreach($results as $topkiller) {
      if ($topkiller['killer_id'] === $_SESSION['id']) {
        echo '<li class="list-item border-bottom its-me">' . $topkiller['name'];
        echo '<span class="float-right font-weight-bold">'. $topkiller['nr'] .' 💀</span></li>';
      } else {
        echo '<li class="list-item border-bottom">' . $topkiller['name'];
        echo '<span class="float-right font-weight-bold">'. $topkiller['nr'] .' 💀</span></li>';
      }
    }
    echo '</ol>';
  } else {
    echo "Er is nog niemand vermoord.";
  }
?>
        <!--<ol class="list-group d-flex pl-3">
          <li class="list-item border-bottom its-me">Player 1
            <span class="float-right font-weight-bold">5 💀</span>
          </li>

          <li class="list-item border-bottom">Player 2
            <span class="float-right font-weight-bold">4 💀</span>
          </li>

          <li class="list-item border-bottom">Player 3
            <span class="float-right font-weight-bold">3 💀</span>
          </li>

          <li class="list-item border-bottom">Player 4
            <span class="float-right font-weight-bold">2 💀</span>
          </li>

          <li class="list-item border-bottom">Player 5
            <span class="float-right font-weight-bold">1 💀</span>
          </li>

          <li class="list-item border-bottom">Player 6
            <span class="float-right font-weight-bold">0 💀</span>
          </li>
          <li class="list-item border-bottom">Player 6
            <span class="float-right font-weight-bold">0 💀</span>
          </li>
          <li class="list-item border-bottom">Player 6
            <span class="float-right font-weight-bold">0 💀</span>
          </li>
          <li class="list-item border-bottom">Player 6
            <span class="float-right font-weight-bold">0 💀</span>
          </li>
          <li class="list-item border-bottom">Player 6
            <span class="float-right font-weight-bold">0 💀</span>
          </li>
        </ol>-->
<?php
} else {
?>
        Hier verschijnen de beste moordenaars, zodra het spel begonnen is!
<?php
}
?>
        <div style="width:100%; height: auto">
          <a class="btn btn-lg btn-primary btn-sm btn-block mt-3" href="logout.php" >Uitloggen</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

  <script>
    function toggleSpoiler(id) {
      var id = $("#" + id);
      if (id.text() == "klik om te tonen") {
        id.removeClass('secret px-2').text(id.data('secret')).css("background-color", "inherit").css("color", "inherit");
      } else {
        id.addClass('secret px-2').text("klik om te tonen").css("background-color", "#212529").css("color", "#ffe2e2");
      }
    }

    $("#submit-code-form").submit(function(event) {
      event.preventDefault();
      $("#submitted-form-loader").toggleClass('invisible');
      $("#submit-button").prop('disabled', true);
      $.ajax("verify_code.php", {
        type: "POST",
        data: $("#submit-code-form").serialize(),
        success: function(result) {successCallback(result);},
        error: function(result) {errorCallback(result);},
        complete: function(result) {
          $("#submitted-form-loader").toggleClass('invisible');
          $("#submit-button").prop('disabled', false);
        },
      });
    })

    function errorCallback(result) {
      console.error("much error wow");
      console.log(result.responseText);
    }

    function successCallback(result) {
      console.log("Succes!");
      console.log(result);
    }

  </script>
</body>
</html>
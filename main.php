<?php
session_start();
include_once('includes/settings.php');
include_once('includes/config.php');
include_once('includes/session_functions.php');

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
  header("location: index.php");
  exit();
}
update_session($_SESSION["beer"], $pdo);
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
if ($gameFinished) {
?>
   <div class="row">
          <div class="col-12 p-3">
            <h2>Het spel is voorbij!</h2>
            Er leven nog maar twee mensen: <ul>
            <?php 
              $sql = "SELECT name FROM `players` WHERE `is_playing` = 1 AND `id_to_kill` != -1";
              $results = $pdo->query($sql)->fetchAll();
              foreach($results as $row) {
                echo "<li>" . $row["name"] . "</li>";
              } ?>
            </ul>
          </div>
        </div>
<?php
}
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
              echo "Er is niemand zonder code of target.<br> <strong>Het spel kan beginnen!</strong> Je hoeft nu niets meer te doen (tenzij er nieuwe mensen bijkomen). ";
            } else {
              echo "Er zijn op dit moment nog <strong>" . $peopleWithoutTargetOrCode . " mensen zonder target of code</strong>. Resetten en opnieuw aanmaken!";
            } ?><br>
            <a href="begin_game.php?function=reset_all" class="btn btn-danger my-2">Reset targets en codes</a>
            <a href="begin_game.php?function=reset_kills" class="btn btn-danger my-2">Reset Kills</a>
            <a href="begin_game.php?function=generate_all" class="btn btn-success my-2">Genereer targets en codes</a>
          </div>
        </div>

<?php
}
// main dashboard for alive players
if ($_SESSION["is_playing"] === true && $_SESSION["is_dead"] === false && $gameStarted === true && $gameFinished === false) {
?> 
        <div class="row">
          <div class="col-12 p-3">
            <h2>Je target is <strong class="px-2 secret" id="person" onclick="toggleSpoiler(this.id)" data-secret="<?php echo $_SESSION["target"]; ?>">klik om te tonen</strong></h2>
            <p>Als iemand weet dat hij jouw target is, kan hij je ontwijken. Dus houd je target geheim tot je je slag slaat!</p>
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
        <!-- ERROR MODAL -->
        <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="Foutmelding!" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content panel-danger">
              <div class="modal-header panel-heading">
                <h5 class="modal-title" id="errorModalTitle">Foutmelding!</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                Er ging iets mis tijdens het controleren van je code. Probeer het later nog eens.
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Sluiten</button>
              </div>
            </div>
          </div>
        </div>
        <!-- SUCCESS MODAL -->
        <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="Succes!" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content panel-success">
              <div class="modal-header panel-heading">
                <h5 class="modal-title" id="errorModalTitle">Succes!</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                Je hebt je target vermoord! Je nieuwe target is bekend.
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Sluiten</button>
              </div>
            </div>
          </div>
        </div>
<?php
// player already dead.
} if ($_SESSION["is_playing"] && $_SESSION["is_dead"]) {
  $killer = null;
  $formattedDate = null;
  $stmt = $pdo->prepare("SELECT p.name as 'killer', k.time FROM kills k LEFT JOIN players p ON k.`killer_id` = p.id WHERE k.deceased_id = :my_id");
  $stmt->execute((array(":my_id" => $_SESSION["id"])));
  $result=$stmt->fetch();
  if ($result) {
    $phpdate = strtotime($result["time"]);
    $formattedDate = "<strong>" .date("j F", $phpdate) . "</strong> om <strong>" . date("H:i", $phpdate) . "</strong>";
    if (isset($result["killer"])) {
      $killer = $result["killer"];
    } else {
      $killer = "het eind van de ronde (omdat je de afgelopen week geen kills had)";
    }
  }
?>
  <div class="row">
          <div class="col-12 p-3">   
            <h2> Vermoord :( </h2>
            Je bent helaas vermoord door <strong><?php echo $killer; ?></strong> op <?php echo $formattedDate;?>.
          </div>
        </div>
<?php
// if player has played and game has started. show kills and killfeed
} if ($_SESSION["is_playing"] === true && $gameStarted === true) {
?>
        <div class="row">
          <div class="col-12 p-3">   
            <h2> Overzicht </h2>
            Je bent betrokken geweest bij de volgende moorden:
            <?php
            // TODO fix murders
              $myId = $_SESSION["id"];
              $stmt = $pdo->prepare("SELECT k.deceased_id, k.killer_id, k.time, p1.name AS killername, p2.name AS deceasedname FROM kills k LEFT JOIN players p1 ON p1.id = k.killer_id JOIN players p2 ON p2.id = k.deceased_id WHERE deceased_id=? OR killer_id=?");
              $stmt->execute(array($myId,$myId));
              $results = $stmt->fetchAll();
              if ($results) {
                echo "<ul>";
                foreach ($results as $row) {
                  $phpdate = strtotime($row["time"]);
                  $formattedDate = "<strong>" .date("j F", $phpdate) . "</strong> om <strong>" . date("H:i",$phpdate) . "</strong>";
                  if ($row["killer_id"] == $myId) {
                    echo "<li>Je hebt <strong>" . $row["deceasedname"] . "</strong> vermoord op ". $formattedDate .".</li>";
                  } else {
                    if (isset($row["killername"])) {
                      $killer = $row["killername"];
                    } else {
                      $killer = "het eind van de ronde (omdat je de afgelopen week geen kills had)";
                    }
                    echo "<li>Je bent vermoord door <strong>" . $killer . "</strong> op " . $formattedDate . ".</li>";
                  }
                }
                echo "</ul>";
              }
            ?>
          </div>
        </div>
        <!-- KILL FEED -->
        <div class="row">
          <div class="col-12 p-3">   
            <h2> Killfeed </h2>
            De killfeed is uitgeschakeld omdat we bijna aan het einde van het spel toe zijn!
            <!--Dit zijn de laatste tien moorden:-->
            <?php
              if (false) {
                  $sql = "SELECT k.name as `killer`, d.name as `deceased`, time FROM `kills` INNER JOIN `players` k ON kills.killer_id=k.id INNER JOIN `players` d ON kills.deceased_id = d.id WHERE killer_id != -1 ORDER BY `time` DESC LIMIT 10 ";
                  $results = $pdo->query($sql)->fetchAll();
                  if ($results) {
                    echo "<ul>";
                    foreach ($results as $row) {
                      $phpdate = strtotime($row["time"]);
                      echo "<li><strong>" . $row["killer"] . "</strong> heeft <strong>" . $row["deceased"] . "</strong> vermoord op <strong>" . date("j F", $phpdate) . "</strong> om <strong>" . date("H:i",$phpdate) . "</strong>.</li>";
                    }
                    echo "</ul>";
                  } else {
                    echo "Er is nog niemand vermoord...";
                  }
              }
            ?>
          </div>
        </div>
<?php
// signup before game has started
} if ($_SESSION["is_playing"] === false && $gameStarted === false && $_SESSION["beer"] !== "admin") {
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
            Je bent aangemeld voor Gotcha. Het begint op <strong><?php printStartDate() ?></strong>. <!--Wil je je afmelden?
            <a class="btn btn-danger" href="unsubscribe.php"> Afmelden </a>-->
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
  $stmt = $pdo->prepare("SELECT p.name, k.killer_id, COUNT(*) as nr FROM kills k, players p WHERE p.id=k.killer_id GROUP BY killer_id ORDER BY nr DESC LIMIT 10");
  $stmt->execute();
  $results=$stmt->fetchAll();
  $max = 9999; // arbitrarily high number
  $index = 0; // displayed index
  $actualIndex = 0; // actual index in list
  if ($results) {
    echo '<ol class="list-group d-flex pl-3">';
    foreach($results as $topkiller) {
      $actualIndex += 1;
      $nr = $topkiller["nr"];
      if ($nr < $max) {
        $index = $actualIndex;
        $max = $nr;
      }
      if ($topkiller['killer_id'] === $_SESSION['id']) {
        echo '<li class="list-item border-bottom its-me" value="' . $index .'">' . $topkiller['name'];
        echo '<span class="float-right font-weight-bold">'. $topkiller['nr'] .' 💀</span></li>';
      } else {
        echo '<li class="list-item border-bottom" value="' . $index .'">' . $topkiller['name'];
        echo '<span class="float-right font-weight-bold">'. $topkiller['nr'] .' 💀</span></li>';
      }
    }
    echo '</ol>';
  } else {
    echo "Er is nog niemand vermoord.";
  }
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
    // Ajax submit of code
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
    });

    // forced refresh on succesful kill
    $('#successModal').on('hidden.bs.modal', function () {
      location.reload();
    });

    function errorCallback(result) {
      var error = JSON.parse(result.responseText)["error"];
      console.error(error);
      $("#errorModal").modal();
      $("#errorModal .modal-body").text(error);
    }

    function successCallback(result) {
      var target = JSON.parse(result)["new_target"];
      $("#successModal").modal();
      $("#successModal .modal-body").html("Je hebt je target vermoord! Je nieuwe target is <strong>" + target + "</strong>.");
    }

    function toggleSpoiler(id) {
      var id = $("#" + id);
      if (id.text() == "klik om te tonen") {
        id.removeClass('secret px-2').text(id.data('secret')).css("background-color", "inherit").css("color", "inherit");
      } else {
        id.addClass('secret px-2').text("klik om te tonen").css("background-color", "#212529").css("color", "#ffe2e2");
      }
    }

  </script>
</body>
</html>

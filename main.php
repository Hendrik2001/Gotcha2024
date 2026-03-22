<?php
session_start();
include_once('includes/settings.php');
include_once('includes/config.php');
include_once('includes/session_functions.php');


if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
  header("location: index.php");
  exit();
}
update_session($_SESSION["beer"], $db);
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
              foreach($users as $uid => $row) {
                if (($row['is_playing'] ?? false) && !empty($row['target_id']) && $row['target_id'] !== '-1' && ($row['status'] ?? 'alive') === 'alive') {
                    echo "<li>" . htmlspecialchars($row["name"] ?? 'Onbekend') . "</li>";
                }
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
            <a href="kill_at_end_of_round.php" class="btn btn-danger my-2">Stop Ronde</a>


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
            <p>Je hebt nog tot <strong><?php printEndOfRound($week); ?></strong> om ten minste één persoon te elimineren, anders lig je uit het spel.</p>
          </div>
        </div>
        <div class="row">
          <div class="col-12 p-3">
            <h2>Je geheime code is <strong class="px-2 secret" id="code" onclick="toggleSpoiler(this.id)" data-secret="<?php echo $_SESSION["own_code"]; ?>">klik om te tonen</strong></h2>
            Als je target deze code ontdekt kan hij je doodmaken, dus houd hem geheim tot je geëlimineerd bent!
          </div>
        </div>
        <div class="row">
          <div class="col-12 p-3">
            <h2> Ik heb iemand geëlimineerd! </h2>
            Als je iemand hebt geëlimineerd, voer dan hieronder zijn of haar code in en druk op verzenden. Je krijgt dan direct te horen wie je hierna moet elimineren.
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
                Je hebt je target geëlimineerd! Je nieuwe target is bekend.
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
  
  $kills = $db->getReference('kills')->orderByChild('deceased_id')->equalTo($_SESSION["id"])->getValue();
  
  if ($kills && is_array($kills) && count($kills) > 0) {
    $kill = reset($kills);
    $phpdate = $kill["time"] ?? time();
    $formattedDate = "<strong>" .date("j F", $phpdate) . "</strong> om <strong>" . date("H:i", $phpdate) . "</strong>";
    
    $killerId = $kill["killer_id"] ?? -1;
    if ($killerId !== -1) {
      $killerData = $db->getReference('users/' . $killerId)->getValue();
      $killer = $killerData["name"] ?? "Onbekend";
    } else {
      $killer = "het eind van de ronde (omdat je de afgelopen week geen kills had)";
    }
  }
?>
  <div class="row">
          <div class="col-12 p-3">   
            <h2> Geëlimineerd :( </h2>
            Je bent helaas geëlimineerd door <strong><?php echo $killer; ?></strong> op <?php echo $formattedDate;?>.
          </div>
        </div>
<?php
// if player has played and game has started. show kills and killfeed
} if ($_SESSION["is_playing"] === true && $gameStarted === true) {
?>
        <div class="row">
          <div class="col-12 p-3">   
            <h2> Overzicht </h2>
            Je bent betrokken geweest bij de volgende eliminaties:
            <?php
              $myId = $_SESSION["id"];
              $myKills = [];
              $allKills = $db->getReference('kills')->getValue() ?? [];
              foreach ($allKills as $k) {
                  if (($k['killer_id'] ?? '') === $myId || ($k['deceased_id'] ?? '') === $myId) {
                      $myKills[] = $k;
                  }
              }

              if (count($myKills) > 0) {
                echo "<ul>";
                foreach ($myKills as $row) {
                  $phpdate = $row["time"] ?? time();
                  $formattedDate = "<strong>" .date("j F", $phpdate) . "</strong> om <strong>" . date("H:i",$phpdate) . "</strong>";
                  if (($row["killer_id"] ?? '') === $myId) {
                    $deceasedData = $db->getReference('users/' . $row["deceased_id"])->getValue();
                    $deceasedname = $deceasedData['name'] ?? 'Onbekend';
                    echo "<li>Je hebt <strong>" . htmlspecialchars($deceasedname) . "</strong> geëlimineerd op ". $formattedDate .".</li>";
                  } else {
                    $killerId = $row["killer_id"] ?? -1;
                    if ($killerId !== -1 && $killerId !== "-1") {
                      $killerData = $db->getReference('users/' . $killerId)->getValue();
                      $killerName = $killerData["name"] ?? 'Onbekend';
                    } else {
                      $killerName = "het eind van de ronde (omdat je de afgelopen week niemand geëlimineerd hebt)";
                    }
                    echo "<li>Je bent geëlimineerd door <strong>" . htmlspecialchars($killerName) . "</strong> op " . $formattedDate . ".</li>";
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
            <h2> Ranglijst </h2>
            <!--De ranglijst is uitgeschakeld omdat we bijna aan het einde van het spel toe zijn!-->
            <!--Dit zijn de laatste tien moorden:-->
            <?php
              if (true) {
                  $allKills = $db->getReference('kills')->orderByChild('time')->limitToLast(10)->getValue() ?? [];
                  // Convert to array and sort DESC
                  usort($allKills, function($a, $b) {
                      return ($b['time'] ?? 0) - ($a['time'] ?? 0);
                  });

                  if (count($allKills) > 0) {
                    echo "<ul>";
                    foreach ($allKills as $row) {
                      $killerId = $row['killer_id'] ?? -1;
                      if ($killerId == -1 || $killerId == "-1") continue;
                      
                      $phpdate = $row["time"];
                      $kData = $db->getReference('users/' . $killerId)->getValue();
                      $dData = $db->getReference('users/' . $row['deceased_id'])->getValue();
                      
                      $kName = htmlspecialchars($kData['name'] ?? 'Onbekend');
                      $dName = htmlspecialchars($dData['name'] ?? 'Onbekend');
                      
                      echo "<li><strong>" . $kName . "</strong> heeft <strong>" . $dName . "</strong> geëlimineerd op <strong>" . date("j F", $phpdate) . "</strong> om <strong>" . date("H:i",$phpdate) . "</strong>.</li>";
                    }
                    echo "</ul>";
                  } else {
                    echo "Er is nog niemand geëlimineerd...";
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
            <p>Gotcha is nog niet begonnen. Het begint op <strong><?php printStartDate() ?></strong>. Wil je je aanmelden? (Kosten: &euro; 10,- op je tentrekening)
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
            <h2> Helaas </h2>
            Gotcha is al begonnen...
          </div>
        </div>

<?php
} // highscores
?>

      </div>
      <div class="col-lg-4 bg-light p-3">
        <h2 class="text-center">Beste jagers</h2>
<?php
if ($gameStarted) {
  $allUsersForRank = $db->getReference('users')->orderByChild('kill_count')->limitToLast(10)->getValue() ?? [];
  // Sort descending
  uasort($allUsersForRank, function($a, $b) {
      return ($b['kill_count'] ?? 0) - ($a['kill_count'] ?? 0);
  });
  
  $max = 9999;
  $index = 0;
  $actualIndex = 0;
  
  $hasKills = false;
  foreach($allUsersForRank as $u) { 
      if (($u['kill_count'] ?? 0) > 0) $hasKills = true; 
  }

  if ($hasKills) {
    echo '<ol class="list-group d-flex pl-3">';
    foreach($allUsersForRank as $uid => $topkiller) {
      $nr = $topkiller["kill_count"] ?? 0;
      if ($nr == 0) continue;
      
      $actualIndex += 1;
      if ($nr < $max) {
        $index = $actualIndex;
        $max = $nr;
      }
      
      $isMe = ($uid === $_SESSION['id']);
      $meClass = $isMe ? ' its-me' : '';
      
      echo '<li class="list-item border-bottom' . $meClass . '" value="' . $index .'">' . htmlspecialchars($topkiller['name'] ?? 'Onbekend');
      echo '<span class="float-right font-weight-bold">'. $nr .' 💀</span></li>';
    }
    echo '</ol>';
  } else {
    echo "Er is nog niemand geëlimineerd.";
  }
} else {
?>
        Hier verschijnen de beste jagers, zodra het spel begonnen is!
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
        console.log("Response Text:", result.responseText);
    
        try {
            var error = JSON.parse(result.responseText)["error"];
            console.error(error);
            $("#errorModal").modal();
            $("#errorModal .modal-body").text(error);
        } catch (e) {
            console.error("Error parsing JSON response: ", e);
            // Handle parsing error, maybe log the raw responseText
            $("#errorModal").modal();
            $("#errorModal .modal-body").text("An error occurred. Please try again later.");
        }
    }
    // function errorCallback(result) {
    //   var error = JSON.parse(result.responseText)["error"];
    //   console.error(error);
    //   $("#errorModal").modal();
    //   $("#errorModal .modal-body").text(error);
    // }
    
    // function errorCallback(result) {
    //     $echo result
    //   try {
    //     // Try parsing the response as JSON
    //     var error = JSON.parse(result.responseText)["error"];
    //     // If the parsing is successful, handle the error as before
    //     console.error(error);
    //     $("#errorModal").modal();
    //     $("#errorModal .modal-body").text(error);
    //   } catch (e) {
    //     // If parsing fails, handle the parsing error
    //     console.error("Error parsing JSON response: ", e);
    //     // You can update the error modal to show a generic error message
    //     $("#errorModal").modal();
    //     $("#errorModal .modal-body").text(error);
    //   }
    // }    
    function successCallback(result) {
      var target = JSON.parse(result)["new_target"];
      $("#successModal").modal();
      $("#successModal .modal-body").html("Je hebt je target geëlimineerd! Je nieuwe target is <strong>" + target + "</strong>.");
    }
    // function successCallback(result) {
    //   try {
    //     // Attempt to parse the response as JSON
    //     var parsedResult = JSON.parse(result);
    //     var newTarget = parsedResult["new_target"];
    //     // Assuming the response is valid, update the success modal as before
    //     $("#successModal").modal();
    //     $("#successModal .modal-body").html("Je hebt je target vermoord! Je nieuwe target is <strong>" + newTarget + "</strong>.");
    //   } catch (e) {
    //     // If parsing fails, log the error and handle it
    //     console.error("Error parsing JSON response: ", e);
    //     // Here, you could show an error or a generic success message
    //     $("#successModal").modal();
    //     $("#successModal .modal-body").text("Your action was successful, but the response could not be processed.");
    //   }
    // }


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

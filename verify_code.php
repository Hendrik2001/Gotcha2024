<?php
session_start();
include_once("includes/config.php");
include_once("includes/settings.php");
include_once("includes/session_functions.php");

$everything_ok = false;
$error = "Onbekende foutmelding!";
$new_target = "Nog steeds de soccie"; 

function is_dead($db) {
	update_session($_SESSION["beer"], $db);
	return $_SESSION["is_dead"];
}

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
	$error = "Je bent niet ingelogd :(";
} elseif (!isset($_POST["secret-code"])) {
	$error = "Je hebt geen code ingevuld :(";
} elseif (is_dead($db)) {
	$error = "Je bent al dood...";
} else {
	$code = strtoupper($_POST["secret-code"]);
	$myId = $_SESSION["id"];

    $myRef = $db->getReference('users/' . $myId);
    $myObj = $myRef->getValue();
    
	if ($myObj) {
        $idKilled = $myObj['target_id'] ?? '-1';
        
        if ($idKilled === '-1' || empty($idKilled)) {
            $error = "Je hebt momenteel geen target.";
        } else {
            $killedRef = $db->getReference('users/' . $idKilled);
            $killedObj = $killedRef->getValue();
            
            if ($killedObj) {
                $codeKilled = strtoupper($killedObj['secret_code'] ?? '');
                $newTargetId = $killedObj['target_id'] ?? '-1';
                
                if ($code === $codeKilled && $code !== '') {
                    // VERIFIED KILL!
                    
                    // Add kill to kills collection
                    $time = time(); 
                    $db->getReference('kills')->push([
                        'killer_id' => $myId,
                        'deceased_id' => $idKilled,
                        'time' => $time,
                        'round_number' => $week ?? 1
                    ]);
                    
                    // Update my target and kill count
                    $myKills = ($myObj['kill_count'] ?? 0) + 1;
                    $myRef->update([
                        'target_id' => $newTargetId,
                        'kill_count' => $myKills
                    ]);
                    
                    // Update deceased person status
                    $killedRef->update([
                        'target_id' => '-1',
                        'status' => 'dead'
                    ]);
                    
                    // Get name of new target
                    if ($newTargetId !== '-1' && $newTargetId !== '') {
                        $newTargetObj = $db->getReference('users/' . $newTargetId)->getValue();
                        $new_target = $newTargetObj['name'] ?? 'Onbekend';
                        $everything_ok = true;
                    } else {
                        $new_target = "Niemand, je hebt gewonnen!";
                        $everything_ok = true;
                    }
                } else {
                    $error = "De code die je hebt ingevuld is incorrect.";
                }
            } else {
                $error = "Target niet gevonden.";
            }
        }
	} else {
		$error = "Er ging iets mis bij het controleren van je kill. Probeer het later nog eens.";
	}
}

if ($everything_ok) {
	header('HTTP/1.1 200 KILL CONFIRMED. NEW TARGET ACQUIRED.');
	die(json_encode(array("new_target" => $new_target)));
} else {
	header('HTTP/1.1 500 KAPPEN MET CHOKEN');
    die(json_encode(array("error" => $error)));
}
?>
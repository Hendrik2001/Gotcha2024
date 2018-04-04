<?php
session_start();
include_once("includes/config.php");
include_once("includes/settings.php");
include_once("includes/session_functions.php");
$everything_ok = false;
$error = "Onbekende foutmelding!";
$new_target = "Nog steeds de soccie"; 

// verify that the person may kill people (i.e. not dead)
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
	$error = "Je bent niet ingelogd :(" . print_r($_SESSION);
} elseif (!isset($_POST["secret-code"])) {
	$error = "Je hebt geen code ingevuld :(";
} elseif (is_dead($pdo)) {
	$error = "Je bent al dood...";
}
else {
	$code = strtoupper($_POST["secret-code"]);
	$myId = $_SESSION["id"];
	$sql = "SELECT my.id_to_kill AS id_killed, your.id_to_kill, your.name, your.own_code FROM players AS my JOIN players as your ON my.id_to_kill = your.id WHERE my.id = :myId";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(["myId" => $myId]);
	$result=$stmt->fetch();

	if ($result) {
		$newTargetId = $result["id_to_kill"];
		$nameKilled = $result["name"];
		$codeKilled = $result["own_code"];
		$idKilled = $result["id_killed"];

		if ($code === $codeKilled) {
			// valid kill
			// add to kills table
			$time = date("Y-m-d H:i:s");
			$sql = "INSERT INTO `kills`(`killer_id`, `deceased_id`, `time`) VALUES (:myId, :killedId, :time)";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(["myId"=>$myId, "killedId"=>$idKilled, "time"=>$time]);
			// update player table with new target
			$sql = "UPDATE players SET `id_to_kill`=:newTarget WHERE id=:myId";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(["newTarget"=>$newTargetId, "myId"=>$myId]);
			// remove target from dead person
			$sql = "UPDATE players SET id_to_kill=-1 WHERE id=:killedId";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(["killedId" => $idKilled]);

			// retrieve name of new target
			$sql = "SELECT name FROM players WHERE id=:newTargetId";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(["newTargetId"=>$newTargetId]);
			$result = $stmt->fetch();
			if($result) {
				$new_target = $result["name"];
				$everything_ok = true;
			}
		} else {
			$error = "De code die je hebt ingevuld is incorrect.";
		}
	} else {
		$error = "Er ging iets mis bij het controleren van je kill. Probeer het later nog eens.";
	}
	//verify code & update indien nodig (make sure to ignore case or force uppercase)
}

//todo
if ($everything_ok) {
	header('HTTP/1.1 200 KILL CONFIRMED. NEW TARGET ACQUIRED.');
	die(json_encode(array("new_target" => $new_target)));
} else {
	header('HTTP/1.1 500 KAPPEN MET CHOKEN');
    die(json_encode(array("error" => $error)));
}

function is_dead($pdo) {
	update_session($_SESSION["beer"], $pdo);
	return $_SESSION["is_dead"];
}

?>
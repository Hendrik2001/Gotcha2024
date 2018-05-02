<?php
// naive approach
// generate a secret code for everyone and set it (make sure it is unique)
// read all ids WHERE isplaying == true into an array
// shuffle it (make sure nobody has his own number)
// insert them all
// 
include_once("includes/config.php");

session_start();

// make sure we are logged in as admin
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true || !isset($_SESSION["beer"]) || $_SESSION["beer"] !== "admin") {
  header("location: index.php");
  exit();
}

if (isset($_GET["function"])) {
	switch ($_GET["function"]) {
		case "reset_codes":
			reset_codes($pdo);
			break;
		case "reset_targets":
			reset_targets($pdo);
			break;
		case "reset_all":
			reset_targets($pdo);
			reset_codes($pdo);
			break;
		case "generate_codes":
			generate_codes(15, $pdo, true);
			break;
		case "generate_targets":
			generate_targets($pdo);
			break;
		case "generate_all":
			generate_codes(15, $pdo, true);
			generate_targets($pdo);
			break;
		case "reset_kills":
			reset_kills($pdo);
			break;
	}
} else {
	echo "Gebruik één van de volgende functies: <pre>generate_codes, generate_targets, generate_all reset_codes, reset_targets, reset_all </pre><br>";
}

echo "<a href=\"main.php\">Klik hier om terug naar de hoofdpagina te gaan</a>";

// creates a circular linked list of targets
function generate_targets($pdo) {
	echo "Beginnen met het aanmaken van de targets...<br>";
	$ids = [];
	$sql = "SELECT id FROM `players` WHERE `is_playing`=1 AND `id` NOT IN (SELECT deceased_id FROM kills)";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	$result = $stmt->fetchAll();
	if ($result === false) {
		echo "<span style='color:red'>Er ging iets mis tijdens het ophalen van alle spelers die mee doen...</span><br>";
		die();
	}
	echo "Ids aan het inladen... <br>";
	foreach ($result as $row) {
		$ids[] = $row["id"];
	}

	$usableIds = $ids;
	echo "Targets aan het schudden...<br>";
	shuffle($usableIds);
	$current = array_pop($usableIds);
	$first = $current;

	$sql = "UPDATE `players` SET `id_to_kill`=:target WHERE `id`=:id";
	$stmt = $pdo->prepare($sql);

	echo "Targets aan het toewijzen... <br>";
	while (count($usableIds) > 0) {
		$next = array_pop($usableIds);
		$params = ["id"=>$current, "target" => $next];
		$result = $stmt->execute($params);
		if ($result === false) {
			echo "<span style='color:red'>Er ging iets mis tijdens toewijzen van het target van id " . $next . "</span><br>";
			die();
		}
		$current = $next;
	}

	// finish circular linked list
	echo "Cirkel rond maken... <br>";
	$params = ["id" => $current, "target" => $first];
	$result = $stmt->execute($params);
	if ($result === false) {
		echo "<span style='color:red'>Er ging iets mis tijdens met rondmaken van de cirkel. ID: " . $next . "</span><br>";
		die();
	}

	echo "<span style='color:green'>Alle targets zijn toegewezen.</span><br>";
}

/*
 * Generate a random and unique string (= code) for every player.
 */
function generate_codes($length = 10, $pdo, $useFancyPasswords = false) {
	reset_codes($pdo);

	echo "Beginnen met aanmaken van codes... <br>";
	$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$max = strlen($characters) - 1;

	if ($useFancyPasswords) {
		$characters = ["GM","27","1815","VAP","MUFI","1815","VO","MF","CORPS","KROEG","LID","MEUI","PAIP","PLOEG","HOEK","FOET","HO","OH","BEER","UB","CZ","HP","RHINO","BUIS","LAIR","ICE","BAV","KRAT","BIER","BLAD","KNOR","PAND","MENSA","SUB3D","CLUB","JC","HUIS","PANDA","SUSHI","KAAL","BACO","EKZ","HENK","PK","PIANO","STUCA","JORIS","JAS","DAS","VINDI","BAL","RIET","ADT","IT","CTTIT","SOA","KEI","PILS","GK","SFEER","GAST","AMIGO","PIK","LEDEN","MUTUA","LID","FIDES","VALTHO","RAPTAP","TGIF","OOTJE","RAKEN","BRAK","VOLAF","CHICK","PEUK","SOCCIE","SENAAT","POLIT"];
		$max = count($characters) - 1;
	}


	$sql = "SELECT id, own_code FROM players WHERE is_playing=1";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	$result = $stmt->fetchAll();

	if ($result === false) {
		echo "<span style='color:red'>Er ging iets mis tijdens het ophalen van alle spelers die mee doen...</span><br>";
		die();
	}
	echo "Beginnen met het aanmaken van nieuwe codes... <br>";

	echo "Er worden " . $stmt->rowCount() . " codes gegenereerd...<br>";

	// TODO er één grote query van maken?
	$insertStmt = $pdo->prepare("UPDATE `players` SET `own_code`=:code WHERE `id`=:id");

	$string = '';
	$currentCodes = [];
	foreach($result as $row) {

		do {
			$string = '';
			for ($i = 0; strlen($string) < $length; $i++) {
				$string .= $characters[mt_rand(0, $max)];
				if ($useFancyPasswords) {
					$string .= "-";
				}
			}
			// remove last -
			if ($useFancyPasswords) {
				$string = substr($string, 0, -1);
			}
		} while (in_array($string, $currentCodes));
		// unique string is generated now
		$currentCodes[] = $string;

		$params = ["code" => $string, "id" => $row["id"]];
		$result = $insertStmt->execute($params);

		if ($result === false) {
			echo "<span style='color:red'>Er ging iets mis tijdens het schrijven van de nieuwe codes naar de database.</span>";
			die();
		}

	}
	echo "<span style='color:green'>De codes zijn geüpdated.</span><br>";
}

function reset_codes($pdo){
	echo "Beginnen met resetten oude codes...<br>";
	$sql = "UPDATE `players` SET `own_code`=NULL";
	$result = $pdo->exec($sql);
	if ($result === false) {
		echo "<span style=\"color: red\">Probleem tijdens resetten van de codes.</span>";
	}
	echo "<span style='color:green'>Oude codes zijn gereset.</span><br>";
}

function reset_targets($pdo) {
	echo "Beginnen met resetten oude targets...<br>";
	$sql = "UPDATE `players` SET `id_to_kill`=NULL";
	$result = $pdo->exec($sql);
	if ($result === false) {
		echo "<span style=\"color: red\">Probleem tijdens resetten van de targets.</span>";
	}
	echo "<span style='color:green'>Oude targets zijn gereset.</span><br>";
}

function reset_kills($pdo) {
	echo "Beginnen met resetten van de kills...<br>";
	$sql = "DELETE FROM kills";
	$result = $pdo->exec($sql);
	if ($result === false) {
		echo "<span style=\"color: red\">Probleem tijdens het resetten van de kills.</span>";
	}
	echo "<span style='color:green'>Alle kills zijn gereset.</span><br>";
}
?>
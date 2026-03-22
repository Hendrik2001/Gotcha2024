<?php
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
            reset_codes($db);
			break;
		case "reset_targets":
			reset_targets($db);
			break;
		case "reset_all":
			reset_targets($db);
			reset_codes($db);
			break;
		case "generate_codes":
			generate_codes(15, $db, true);
			break;
		case "generate_targets":
			generate_targets($db);
			break;
		case "generate_all":
			generate_codes(15, $db, true);
			generate_targets($db);
			break;
		case "reset_kills":
			reset_kills($db);
			break;
	}
} else {
	echo "Gebruik één van de volgende functies: <pre>generate_codes, generate_targets, generate_all reset_codes, reset_targets, reset_all, reset_kills</pre><br>";
}

echo "<a href=\"main.php\">Klik hier om terug naar de hoofdpagina te gaan</a>";

function generate_targets($db) {
	echo "Beginnen met het aanmaken van de targets...<br>";
	$ids = [];
	
    $users = $db->getReference('users')->getValue() ?? [];
    foreach ($users as $uid => $user) {
        if (($user['is_playing'] ?? false) == true && ($user['status'] ?? 'alive') === 'alive') {
            $ids[] = $uid;
        }
    }

	if (count($ids) < 2) {
		echo "<span style='color:red'>Er zijn niet genoeg spelers (minstens 2 nodig).</span><br>";
		return;
	}

	echo "Targets aan het schudden...<br>";
	shuffle($ids);
    
    $first = $ids[0];
    
	echo "Targets aan het toewijzen... <br>";
    for ($i = 0; $i < count($ids); $i++) {
        $current = $ids[$i];
        $next = ($i === count($ids) - 1) ? $first : $ids[$i + 1];
        
        $db->getReference('users/' . $current . '/target_id')->set($next);
    }

	echo "<span style='color:green'>Alle targets zijn toegewezen (cirkel rond gemaakt).</span><br>";
}

function generate_codes($length = 10, $db, $useFancyPasswords = false) {
	reset_codes($db);

	echo "Beginnen met aanmaken van codes... <br>";
	$characters = ['1814', '1726', 'VO', 'CORPS', 'TENT', 'LID', 'EILAND', 'OVAAL', 'LEESTAFEL', 'HAARD', 'WANDTAP', 'FOYER', 'LUIFEL','BORDES', 'PAS', 'SJAARS', 'NES', 'FEUT', 'BAK', 'PITCHER', 'BUFFET', 'BIER', 'STRAALJAGER', 'GELUK', 'LUSTRUM','DAKJE', 'CLUB', 'HUIS', 'HIFI', 'KAAL', 'UIL', 'JAS', 'DAS', 'ZOOIEN', 'SOA', 'LEIDEN', 'LESDEUX', 'MINERVA', 'SOCIËTEIT', 'VIRTUS', 'CONCORDIA', 'FIDES'];

    $users = $db->getReference('users')->getValue() ?? [];
    $playingCount = 0;
    foreach ($users as $u) {
        if (($u['is_playing'] ?? false) == true) {
            $playingCount++;
        }
    }
	echo "Er worden " . $playingCount . " codes gegenereerd...<br>";

	$currentCodes = [];
    foreach($users as $uid => $row) {
        if (($row['is_playing'] ?? false) != true) continue;

		do {
			$string = '';
			for ($i = 0; strlen($string) < $length; $i++) {
				$string .= $characters[mt_rand(0, count($characters) - 1)];
				if ($useFancyPasswords) {
					$string .= "-";
				}
			}
			if ($useFancyPasswords) {
				$string = substr($string, 0, -1);
			}
		} while (in_array($string, $currentCodes));
        
		$currentCodes[] = $string;
        $db->getReference('users/' . $uid . '/secret_code')->set($string);
	}
	echo "<span style='color:green'>De codes zijn geüpdated.</span><br>";
}

function reset_codes($db){
	echo "Beginnen met resetten oude codes...<br>";
    $users = $db->getReference('users')->getValue() ?? [];
    foreach ($users as $uid => $u) {
        $db->getReference('users/' . $uid . '/secret_code')->set('');
    }
	echo "<span style='color:green'>Oude codes zijn gereset.</span><br>";
}

function reset_targets($db) {
	echo "Beginnen met resetten oude targets...<br>";
    $users = $db->getReference('users')->getValue() ?? [];
    foreach ($users as $uid => $u) {
        $db->getReference('users/' . $uid . '/target_id')->set('');
    }
	echo "<span style='color:green'>Oude targets zijn gereset.</span><br>";
}

function reset_kills($db) {
	echo "Beginnen met resetten van de kills...<br>";
    $db->getReference('kills')->remove();
    
    // Also reset kill_counts and statuses
    $users = $db->getReference('users')->getValue() ?? [];
    foreach ($users as $uid => $u) {
        $db->getReference('users/' . $uid . '/kill_count')->set(0);
        $db->getReference('users/' . $uid . '/status')->set('alive');
    }
	echo "<span style='color:green'>Alle kills en statussen zijn gereset.</span><br>";
}
?>
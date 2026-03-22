<?php

/*
 *	Retrieve and set session vars, such as name, id, etc
 */
function update_session($beer, $db) {

	// default values
	$id = null;
	$name = null;
	$target = null;
	$own_code = null;
	$is_dead = false;
	$is_playing = false;

	// In Firebase, we find the user by their debiteurennummer (beer)
    // The query returns an array of matching users (should just be one match)
    $usersRef = $db->getReference('users')->orderByChild('debiteurennummer')->equalTo((string)$beer)->getSnapshot();
    $userData = $usersRef->getValue();

	if ($userData && is_array($userData) && count($userData) > 0) {
        // The key of the first element is the user's Firebase ID
        $id = array_key_first($userData); 
        $player = $userData[$id];

		$name = $player["name"] ?? null;
		$own_code = $player["secret_code"] ?? null;
		$is_playing = ($player["is_playing"] ?? false);

        // retrieve target's name
		$target_id = $player["target_id"] ?? null;
        if ($target_id && $target_id !== '-1') {
            // Fetch target directly by their Firebase ID
            $targetSnapshot = $db->getReference('users/' . $target_id)->getSnapshot();
            $targetData = $targetSnapshot->getValue();
            if ($targetData) {
                $target = $targetData['name'] ?? null;
            }
        }

		// check if dead
        if (($player["status"] ?? 'alive') === 'dead') {
            $is_dead = true;
        }

        // If they were marked admin, set that in session too
        if (($player['role'] ?? 'player') === 'admin') {
            $_SESSION['is_admin'] = true;
        }
	}


	// set session vars
	$_SESSION["login"] = true;
	$_SESSION["id"] = $id;
	$_SESSION["beer"] = $beer;
	$_SESSION["name"] = $name;
	$_SESSION["target"] = $target;
	$_SESSION["own_code"] = $own_code;
	$_SESSION["is_playing"] = $is_playing;
	$_SESSION["is_dead"] = $is_dead;
}

?>
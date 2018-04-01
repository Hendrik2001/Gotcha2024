<?php

/*
 *	Retrieve and set session vars, such as name, id, etc
 */
function handle_session($beer, $pdo) {

	// default values
	$id = null;
	$name = null;
	$target = null;
	$own_code = null;
	$is_dead = null;
	$is_playing = false;

	// retrieve player data if signed up
	$stmt = $pdo->prepare("SELECT id, name, own_code, id_to_kill, is_dead, is_playing FROM players WHERE beer=:beer");
	$stmt->execute([":beer" => $beer]);
	$player = $stmt->fetch();

	if ($player) {
		$name = $player["name"];
		$is_dead = $player["is_dead"] === "1" ? true : false;
		$own_code = $player["own_code"];
		$is_playing = $player["is_playing"] === "1" ? true : false;
		$id = $player["id"];

		// retrieve target
		$target_id = $player["id_to_kill"];
		$stmt = $pdo->prepare("SELECT name FROM players WHERE id=:target_id");
		$stmt -> execute(["target_id"=>$target_id]);
		$target = $stmt->fetchColumn();
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
<?php
include_once("config.php");

session_start();
// Check if we are logged in or are trying to login 
if (!isset($_POST["inputBeer"]) || !isset($_POST["inputWachtwoord"])) {
	header("Location: index.php");
    exit();
} elseif (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    header("Location: main.php");
    exit();
} else {
	$beer = $_POST["inputBeer"];
	$pass = $_POST["inputWachtwoord"];
	//$remember = $_POST["remember-me"];

	$loggedin = login_to_site($beer, $pass);
	if ($loggedin !== false) {
		handle_session($beer, $pdo);
		header("Location: main.php");
		exit();
	}
}

// TODO: login to vindicat.nl
function login_to_site($beer, $pass) {
	//if (strtolower($beer) === "admin" && $pass === "GM1815") {
		return true;
	//} else {
	//	return false;
	//}
}

/*
 *	Retrieve and set session vars, such as name, id, etc
 */
function handle_session($beer, $pdo) {

	$name = "mijn naam";
	$target = "Als je dit leest is er iets misgegaan!";
	$own_code = "Als je dit leest is er iets misgegaan!";

	$stmt = $pdo->prepare("SELECT id, name, own_code, id_to_kill, is_dead, is_playing FROM players WHERE beer=:beer");
	$stmt->execute(["beer" => $beer]);
	$player = $stmt->fetch();

	if ($player) {
		$name = $player["name"];
		$is_dead = $player["is_dead"] === "1" ? true : false;
		$own_code = $player["own_code"];
		$is_playing = $player["is_playing"] === "1" ? true : false;
		$target_id = $player["id_to_kill"];

		$stmt = $pdo->prepare("SELECT name FROM players WHERE id=:id");
		$stmt -> execute(["id"=>$target_id]);
		$target = $stmt->fetchColumn();
	} else {
		$is_playing = false;
	}

	$_SESSION["beer"] = $beer;
	$_SESSION["name"] = $name;
	$_SESSION["login"] = true;
	$_SESSION["target"] = $target;
	$_SESSION["own_code"] = $own_code;
	$_SESSION["is_playing"] = $is_playing;
	$_SESSION["is_dead"] = $is_dead;
}

?>
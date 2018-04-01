<?php
include_once('includes/config.php');
include_once('includes/settings.php');
include_once('includes/session_functions.php');

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
	} else {
		header("Location: index.php?error=1");
		exit();
	}
}

// TODO: login to vindicat.nl
function login_to_site($beer, $pass) {
	// log in as admin or with vindicat site
	if (strtolower($beer) === "admin" && $pass === "ToverGotchaAdmin") {
		return true;
	} elseif ($beer === "admin" && $pass !== "ToverGotchaAdmin") {
		return false;
	}
	return true;
}

?>
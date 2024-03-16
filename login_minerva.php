<?php
require __DIR__ . '/vendor/autoload.php';
include_once('includes/config.php');
include_once('includes/settings.php');
include_once('includes/session_functions.php');


function loginToSite($username = null, $password = null)
{
	if ($username == "admin" && $password == "ToverGotchaAdmin") {
		return [true, null, "Zieke winnaar"];
	}
	if ($username != null) {
		$data = array(
			'login_form[login]' => $username,
			'login_form[password]' => $password,
			'login_form[remember_me]' => '1'
		);

		$ch = curl_init('https://leden.lsvminerva.nl/index.php?id=1034&action=login');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
		));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Consider security implications in production
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($ch);
		curl_close($ch);

		// Check login success based on $result
		// Example (adjust based on actual response):
		$isloggedin = strstr($result, '<a href=persoonlijke-pagina>Ga naar je persoonlijke pagina</a>') !== false;
		return [$isloggedin, $result, "Not a valid account"];
	}
	return [false, '', "No Username given"];
}


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

	list($loggedin, $result, $errer) = loginToSite($beer, $pass);
	if ($loggedin !== false) {
		update_session($beer, $pdo);
		header("Location: main.php");
		exit();
	} else {
		header("Location: index.php?error=1");
		exit();
	}
}



?>

<?php
require __DIR__ . '/vendor/autoload.php';
include_once('includes/config.php');
include_once('includes/settings.php');
include_once('includes/session_functions.php');


function loginToSite($username = null, $password = null)
{
    if ($username = "admin" && $password = "ToverGotchaAdmin") {
        return [true, null, "Zieke winnaar"];
    }
    $session = new Requests_Session('http://www.vindicat.nl/');
    $session->headers['Accept'] = 'text/html';

    //login
    if ($username != null) {
        $postData = ['beernummer' => $username, 'password' => $password];
        $r = $session->post('mijn-vindicat/login', [], $postData);
        // strstr returns false or part of string!
        $islogedin = strstr(
                $r->body, '<a href="/mijn-vindicat/logout">Uitloggen</a>'
            ) !== false;

        return [$islogedin, $session, "Not a valid account"];
    }

    return [false, $session, "No Username given"];

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

	list($loggedin, $session, $errer) = loginToSite($beer, $pass);
	if ($loggedin !== false) {
		update_session($beer, $db);
		header("Location: main.php");
		exit();
	} else {
		header("Location: index.php?error=1");
		exit();
	}
}


?>

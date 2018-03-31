<?php
$everything_ok = false;
$error = "Onbekende foutmelding!";
$new_target = "Nog steeds de soccie"; 

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
	$error = "Je bent niet ingelogd :(";
} elseif (!isset($_POST["secret-code"])) {
	$error = "Je hebt geen code ingevuld :(";
} else {
	//verify code & update indien nodig
}

//todo
if ($everything_ok) {
	header('HTTP/1.1 200 KILL CONFIRMED. NEW TARGET ACQUIRED.');
	die(json_encode(array("new_target" => $new_target)));
} else {
	header('HTTP/1.1 500 KAPPEN MET CHOKEN');
    die(json_encode(array("error" => $error)));
}

?>
<?php
include_once('includes/settings.php');
include_once('includes/config.php');
include_once('includes/session_functions.php');

session_start();
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
  header("location: index.php");
  exit();
}
if ($gameStarted === true) {
	header("location: main.php");
	exit();
}

// retrieve name from members
if (!isset($_SESSION["beer"])) {
	die("Je beernummer is niet opgeslagen tijdens het inloggen. Log opnieuw in en probeer het nog een keer. <br> Als het probleem blijft bestaan, neem dan contact op met Tover of met de commucicacie.");
}
$beer = $_SESSION["beer"];
$stmt = $pdo->prepare('SELECT `name` FROM `members` WHERE `beer`=:beer');
$stmt->execute([":beer" => $beer]);
$name = $stmt->fetchColumn();
if ($name === false) {
	die("Het lijkt erop dat je niet in de ledenlijst van de kroeg staat. Neem contact op met Tover of met de communicacie om dit te fixen.");
}

// insert into players list
$stmt = $pdo->prepare('INSERT INTO `players`(`beer`, `name`, `own_code`, `id_to_kill`, `is_playing`) VALUES (:beer, :name, NULL, NULL, 1) ON DUPLICATE KEY UPDATE `is_playing`=1');
$success = $stmt->execute([':beer' => $beer, ':name' => $name]);
if ($success) {
	//update session variables and redirect
	update_session($beer, $pdo);
	header("location: main.php");
	exit();
} else {
	die("Er ging iets mis tijdens het aanmelden. Log opnieuw in en probeer het nog een keer. <br> Als het probleem bijft bestaan, neem dan contact op met Tover of met de communicacie.");
}

?>
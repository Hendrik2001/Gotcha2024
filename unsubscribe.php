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
if (!isset($_SESSION["id"])) {
	die("Je bent niet meer ingelogd. Log opnieuw in en meld je dan af.");
}

$stmt = $pdo->prepare("UPDATE `players` SET `is_playing`=0 WHERE `id`=:id");
$result = $stmt->execute(["id" => $_SESSION["id"]]);
if ($result === false) {
	die("Er ging iets mis tijdens het afmelden. Log opnieuw in en probeer het dan nog een keer. <br> Als het dan nog niet werkt, neem dan contact op met Tover of met de communicacie.");
} else {
	// successful update
	handle_session($_SESSION["beer"], $pdo);
	header("location: main.php");
	exit();
}

?>
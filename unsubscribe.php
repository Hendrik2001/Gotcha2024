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

try {
    $db->getReference('users/' . $_SESSION["id"] . '/is_playing')->set(false);
    update_session($_SESSION["beer"], $db);
	header("location: main.php");
	exit();
} catch (Exception $e) {
    die("Er ging iets mis tijdens het afmelden: " . $e->getMessage());
}
?>
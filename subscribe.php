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

if (!isset($_SESSION["beer"])) {
	die("Je beernummer is niet opgeslagen tijdens het inloggen. Log opnieuw in en probeer het nog een keer.");
}
$beer = $_SESSION["beer"];

// Look up user or create one
$usersRef = $db->getReference('users')->orderByChild('debiteurennummer')->equalTo((string)$beer)->getSnapshot();
$userData = $usersRef->getValue();

$name = "Speler {$beer}";
// Let's see if they already exist
if ($userData && is_array($userData) && count($userData) > 0) {
    $uid = array_key_first($userData);
    $playerData = $userData[$uid];
    $name = $playerData['name'] ?? $name;
    
    // Update existing user
    $db->getReference('users/'.$uid.'/is_playing')->set(true);
} else {
    // Insert new user
    $db->getReference('users')->push([
        'debiteurennummer' => (string)$beer,
        'name' => $name,
        'status' => 'alive',
        'is_playing' => true,
        'target_id' => '',
        'secret_code' => '',
        'kill_count' => 0,
        'role' => 'player'
    ]);
}

update_session($beer, $db);
header("location: main.php");
exit();

?>
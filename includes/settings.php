<?php
include_once("config.php");
// sets gamestarted to true or false depending on the current time
// todo maybe add this to some database or whatever?
$gameStarted = false;
$gameFinished = false;


// check of er mensen zonder code zijn die wel meespelen
$peopleWithoutTargetOrCode = -1;
$sql = "SELECT count(*) as `not_ready` FROM `players` WHERE `is_playing`=1 AND (`own_code` IS NULL OR `id_to_kill` IS NULL)";
$result = $pdo->query($sql);
$peopleWithoutTargetOrCode = $result->fetchColumn();

$timestampStart = 1524355140; //04/21/2018 @ 23:59pm (UTC) (zorg dat deze goed staat voor de functie hieronder)
if (time() > $timestampStart) {
	if ($result !== false && $peopleWithoutTargetOrCode == 0) {
		$gameStarted = true;
	}
}

$oneWeek = 7*24*60*60;
$endOfCurrentRound = $timestampStart + $oneWeek;
$week = 1;

while ($endOfCurrentRound < time()) {
	$endOfCurrentRound += $oneWeek;
	$week += 1;
}

function printEndOfRound($week) {
	$day = [21, 28, 5, 12, 19, 26, 2, 9];
	$month = ["april", "april", "mei", "mei", "mei", "mei", "juni", "juni"];
	echo "zaterdag " . $day[$week] . " " . $month[$week] . " om 23:59";
}

function printStartDate() {
	echo "zaterdag 21 april om 23:59";
}



// function isDead($playerId) {
//     $pdo = $GLOBALS['pdo'];
//     $sql = "SELECT count(*) as `is_killed` FROM `kills` WHERE `deceased_id`= ?";
//     $stmt = $pdo->prepare($sql);
//     $stmt->execute(array($playerId));
//     return $stmt->fetch()["is_killed"] != 0;
// }

// echo(isDead(2));


 $gameStarted = true;

// count number of active and alive players
 $sql = "SELECT count(*) AS alive FROM `players` WHERE `is_playing` = 1 AND `id_to_kill` != -1";
 $result = $pdo->query($sql);
 if ($gameStarted && $result && $result->fetch()["alive"] <= 2) {
 	$gameFinished = true;
 }
// todo settings like gamestarted
// round finished, tijd to reset
?>

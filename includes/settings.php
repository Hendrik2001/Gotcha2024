<?php
$gameStarted = false;

$timestampStart = 1524355140; //04/21/2018 @ 23:59pm (UTC) (zorg dat deze goed staat voor de functie hieronder)
if (time() > $timestampStart) {
	$gameStarted = true;
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



//$gameStarted = true;
// todo settings like gamestarted
// round finished, tijd to reset
?>
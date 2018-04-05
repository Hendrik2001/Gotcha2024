<?php
$
// if (php_sapi_name() !== "cli") {
// 	die("This script needs to be ran from the command line.");
// }

$startOfRound = date("Y-m-d H:i:s", time() - 7*24*60*60); // a week ago
//die($startOfRound);
// select all alive players who have no kills or for who the last kill was before the start of this round
$sql = "SELECT players.id, players.name, max(kills.time) AS lastkill FROM players LEFT JOIN kills ON players.id = kills.killer_id WHERE players.is_playing = 1 AND players.id NOT IN (SELECT deceased_id FROM kills) GROUP BY players.id HAVING (lastkill < ? OR lastkill IS NULL)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$startOfRound]);
$results = $stmt->fetchAll();

if ($results) {
	foreach($results as $row) {
		// update
	}
}
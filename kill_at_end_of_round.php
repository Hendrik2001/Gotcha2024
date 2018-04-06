<?php
include_once("includes/config.php");
// if (php_sapi_name() !== "cli") {
// 	die("This script needs to be ran from the command line.");
// }

$startOfRound = date("Y-m-d H:i:s", time() - 7*24*60*60); // a week ago
echo "Start of last round ".  $startOfRound . "<br>";
//die($startOfRound);
// select all alive players who have no kills or for who the last kill was before the start of this round
$sql = "SELECT players.id, players.name, players.id_to_kill, max(kills.time) AS lastkill FROM players LEFT JOIN kills ON players.id = kills.killer_id WHERE players.is_playing = 1 AND players.id NOT IN (SELECT deceased_id FROM kills) GROUP BY players.id HAVING (lastkill < ? OR lastkill IS NULL)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$startOfRound]);
$results = $stmt->fetchAll();
echo "Retrieved all players to remove from game. (n = " . count($results) . ")<br>";

if ($results) {
	$sqlAddKill = "INSERT INTO kills (`killer_id`, `deceased_id`, `time`) VALUES (-1,:deceased_id,:time)";
	$stmtAddKill = $pdo->prepare($sqlAddKill);

	$sqlUpdateTarget = "UPDATE players SET `id_to_kill` = :idToKill WHERE `id_to_kill` = :id";
	$stmtUpdateTarget = $pdo->prepare($sqlUpdateTarget);

	$sqlRemoveOwnTarget = "UPDATE players SET `id_to_kill` = -1 WHERE `id`= :id";
	$stmtRemoveOwnTarget = $pdo->prepare($sqlRemoveOwnTarget);

	echo "SQL statements are prepared.<br>";
	foreach($results as $row) {
		// update
		$name = $row["name"];
		$id = $row["id"];
		$idToKill = $row["id_to_kill"];
		if ($stmtAddKill->execute(["deceased_id"=>$id, "time"=>date("Y-m-d H:i:s")]) === false) {
			die("Er ging iets mis tijdens het toevoegen van de kill op " . $name . " (id = " . $id . ").");
		}
		if ($stmtUpdateTarget->execute(["idToKill" => $idToKill, "id"=>$id]) === false) {
			die("Er ging iets mis tijdens het doorschuiven van het doelwit van " . $name . " (id = " . $id . ").");
		}
		if ($stmtRemoveOwnTarget->execute(["id"=>$id]) === false) {
			die("Er ging iets mis tijdens het wissen van het eigen target van " . $name . " (id = " . $id . ").");
		}
		echo "Removed " .$name . " from the game<br>";

	}
} else {
	echo "Nothing to remove. <br>"; 
}

echo "<span style='color:green'>Done</span>";
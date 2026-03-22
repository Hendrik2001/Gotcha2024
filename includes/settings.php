<?php
include_once("config.php");

// Fetch settings from Firebase
$configNode = $db->getReference('game_settings/config')->getValue();

$gameStarted = $configNode['game_started'] ?? false;
$gameFinished = $configNode['game_finished'] ?? false;
$timestampStart = $configNode['start_date'] ?? time();
$week = $configNode['current_week'] ?? 1;

$peopleWithoutTargetOrCode = 0;
$activePlayers = 0;
$alivePlayers = 0;

// Fetch all users to calculate game stats
$users = $db->getReference('users')->getValue() ?? [];

foreach ($users as $uid => $user) {
    if (($user['is_playing'] ?? false) === true) {
        $activePlayers++;
        if (($user['status'] ?? 'alive') === 'alive') {
            $alivePlayers++;
        }
        
        // If a player is missing a target or their secret code, they aren't fully ready
        if (empty($user['secret_code']) || empty($user['target_id'])) {
            $peopleWithoutTargetOrCode++;
        }
    }
}

if (time() > $timestampStart) {
    if ($peopleWithoutTargetOrCode == 0 && $activePlayers > 0) {
        $gameStarted = true;
    }
}

$oneWeek = 7 * 24 * 60 * 60;
$endOfCurrentRound = $timestampStart + $oneWeek;

// Update the current week dynamically depending on the start time
while ($endOfCurrentRound < time()) {
    $endOfCurrentRound += $oneWeek;
    $week += 1;
}

function printEndOfRound($week) {
    // Basic array for printing some hardcoded dates for next rounds
    $day = [25, 1, 8, 15, 22, 29, 6, 13, 20, 27];
    $month = ["maart", "april", "april", "april", "april", "april", "mei", "mei", "mei", "mei"];
    $weekIndex = ($week >= count($day)) ? count($day) - 1 : $week;
    echo "maandag " . $day[$weekIndex] . " " . $month[$weekIndex] . " om 12:00";
}

function printStartDate() {
    echo "maandag 1 mei om 23:59";
}

// If 2 or fewer people are alive, the game finishes!
if ($gameStarted && $alivePlayers <= 2 && $activePlayers > 2) {
    $gameFinished = true;
}
?>

<?php
include_once("includes/config.php");

$startOfRound = time() - 7*24*60*60; // a week ago
echo "Start of last round ".  date("Y-m-d H:i:s", $startOfRound) . "<br>";

$users = $db->getReference('users')->getValue() ?? [];
$kills = $db->getReference('kills')->getValue() ?? [];

$toRemove = [];

foreach ($users as $uid => $user) {
    if (($user['is_playing'] ?? false) && ($user['status'] ?? 'alive') === 'alive' && ($user['role'] ?? 'player') !== 'admin') {
        
        // Find their last kill
        $lastKillTime = null;
        foreach ($kills as $k) {
            if (($k['killer_id'] ?? '') === $uid) {
                if ($lastKillTime === null || ($k['time'] ?? 0) > $lastKillTime) {
                    $lastKillTime = ($k['time'] ?? 0);
                }
            }
        }
        
        if ($lastKillTime === null || $lastKillTime < $startOfRound) {
            $toRemove[] = $uid;
        }
    }
}

echo "Retrieved all players to remove from game. (n = " . count($toRemove) . ")<br>";

if (count($toRemove) > 0) {
    foreach ($toRemove as $uid) {
        $user = $users[$uid] ?? [];
        $name = $user['name'] ?? 'Onbekend';
        $idToKill = $user['target_id'] ?? '-1';
        
        // Add kill record (killer_id = -1 means they died due to timeout)
        $db->getReference('kills')->push([
            'killer_id' => -1,
            'deceased_id' => $uid,
            'time' => time()
        ]);
        
        // Find the person who was targeting this user, and point them to their target
        foreach ($users as $otherUid => $otherUser) {
            if (($otherUser['target_id'] ?? '') === $uid) {
                $db->getReference('users/' . $otherUid . '/target_id')->set($idToKill);
            }
        }
        
        // Mark this user as dead
        $db->getReference('users/' . $uid)->update([
            'target_id' => '-1',
            'status' => 'dead'
        ]);
        
        echo "Removed " . $name . " from the game<br>";
    }
} else {
    echo "Nothing to remove. <br>"; 
}

echo "<br><span style='color:green'>Done</span>";
?>
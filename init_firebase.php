<?php
require __DIR__.'/vendor/autoload.php';

use Kreait\Firebase\Factory;

echo "Connecting to Firebase Realtime Database...\n";

try {
    $factory = (new Factory)
        ->withServiceAccount(__DIR__.'/firebase-credentials.json')
        ->withDatabaseUri('https://gotcha-f1da4-default-rtdb.europe-west1.firebasedatabase.app');
        
    $db = $factory->createDatabase();

    echo "Connected! Creating game_settings config...\n";
    
    // Create the game_settings node
    $db->getReference('game_settings/config')->set([
        'registration_open' => true,
        'game_started' => false,
        'game_finished' => false,
        'start_date' => time(),
        'current_week' => 1
    ]);
    echo " -> Created game_settings/config\n";

    echo "Creating users list and a default admin user...\n";
    // Create an admin user under users/admin
    $db->getReference('users/admin')->set([
        'debiteurennummer' => 'admin',
        'name' => 'Tover Gotcha Admin',
        'status' => 'alive',
        'is_playing' => false,
        'target_id' => '',
        'secret_code' => '',
        'kill_count' => 0,
        'role' => 'admin'
    ]);
    echo " -> Created users/admin\n";

    echo "\nFirebase Realtime Database Initialized Successfully! You can check your Firebase Console now.\n";
} catch (\Exception $e) {
    echo "Error initializing Firebase: " . $e->getMessage() . "\n";
}

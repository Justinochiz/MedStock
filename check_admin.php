<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'tomonjustine74@gmail.com')->first();

if ($user) {
    echo "\n✓ ADMIN USER FOUND:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Role: " . $user->role . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n✓ YES, tomonjustine74@gmail.com is an ADMIN!\n\n";
} else {
    echo "\n✗ tomonjustine74@gmail.com NOT FOUND\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\nAll users in database:\n";
    $allUsers = \App\Models\User::all();
    foreach ($allUsers as $u) {
        echo "- " . $u->email . " (Role: " . $u->role . ")\n";
    }
    echo "\n";
}

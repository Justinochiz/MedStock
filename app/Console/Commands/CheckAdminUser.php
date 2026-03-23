<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckAdminUser extends Command
{
    protected $signature = 'admin:check {email=admin@medstock.com : Email to verify as admin}';

    protected $description = 'Check whether a user exists and has admin role';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $user = User::where('email', $email)->first();

        if ($user !== null) {
            $this->newLine();
            $this->info('ADMIN USER FOUND');
            $this->line('--------------------');
            $this->line('ID: ' . $user->id);
            $this->line('Name: ' . $user->name);
            $this->line('Email: ' . $user->email);
            $this->line('Role: ' . $user->role);
            $this->line('--------------------');

            if ((string) $user->role === 'admin') {
                $this->info('YES, this user is an ADMIN.');
            } else {
                $this->warn('User exists but is not an admin.');
            }

            return self::SUCCESS;
        }

        $this->newLine();
        $this->error($email . ' NOT FOUND');
        $this->line('--------------------');
        $this->line('All users in database:');

        $allUsers = User::select('email', 'role')->orderBy('id')->get();
        if ($allUsers->isEmpty()) {
            $this->warn('- No users found -');
        } else {
            foreach ($allUsers as $listedUser) {
                $this->line('- ' . $listedUser->email . ' (Role: ' . $listedUser->role . ')');
            }
        }

        return self::FAILURE;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdminUser extends Command
{
    protected $signature = 'admin:make {user : User ID or email}';

    protected $description = 'Promote a user to admin role by ID or email';

    public function handle(): int
    {
        $identifier = (string) $this->argument('user');

        $user = ctype_digit($identifier)
            ? User::find((int) $identifier)
            : User::where('email', $identifier)->first();

        if ($user === null) {
            $this->error('User not found for identifier: ' . $identifier);
            return self::FAILURE;
        }

        $user->role = 'admin';
        $user->save();

        $this->newLine();
        $this->info('User role updated successfully.');
        $this->line('------------------------------');
        $this->line('ID: ' . $user->id);
        $this->line('Name: ' . $user->name);
        $this->line('Email: ' . $user->email);
        $this->line('Role: ' . $user->role . ' (ADMIN)');
        $this->line('------------------------------');

        return self::SUCCESS;
    }
}

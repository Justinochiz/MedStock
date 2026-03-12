<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Models\User;
use App\Models\Customer;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker): void
    {
        // Create admin user only
        $admin = new User();
        $admin->name = 'Admin';
        $admin->email = 'admin@medstock.com';
        $admin->password = bcrypt('password');
        $admin->role = 'admin';
        $admin->save();
    }
}

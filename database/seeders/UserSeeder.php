<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'phone' => '+84123456789',
            'email_verified_at' => now(),
            'password' => Hash::make('123'),
            'role' => 'admin',
            'status' => 'active',
            'date_of_birth' => '1985-01-01',
            'gender' => 'male',
            'address' => 'Ho Chi Minh City, Vietnam',
            'last_login_at' => now(),
        ]);

        // Create Toan
        User::create([
            'name' => 'Toan User',
            'email' => 'toan@example.com',
            'phone' => '+84987654321',
            'email_verified_at' => now(),
            'password' => Hash::make('111'),
            'role' => 'moderator',
            'status' => 'active',
            'date_of_birth' => '2003-12-27',
            'gender' => 'female',
            'address' => 'Hanoi, Vietnam',
        ]);

        // Create Regular Users
        User::factory(10)->regular()->verified()->create();
        User::factory(5)->regular()->unverified()->create();
        User::factory(3)->regular()->inactive()->create();
        User::factory(2)->regular()->suspended()->create();

        // Create Additional Admins and Moderators
        User::factory(2)->admin()->create();
        User::factory(3)->moderator()->create();
    }
}

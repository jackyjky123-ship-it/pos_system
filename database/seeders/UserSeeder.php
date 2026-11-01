<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::UpdateOrCreate(
            [
                'email' => 'admin@example.com',
            ],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password:12345'),
                'email_verified_at' => now(),
            ]
        );
    }
}

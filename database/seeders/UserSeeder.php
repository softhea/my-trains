<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create SuperAdmin (Protected)
        $superadmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make(value: 'parolasuperadmin'),
            'phone' => '+1-555-0001',
            'city' => 'Bucuresti',
            'role_id' => 1,
            'is_protected' => true,
        ]);

        // Create Admin Users
        $admin1 = User::create([
            'name' => 'John Smith',
            'email' => 'admin1@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make(value: 'parolaadmin'),
            'phone' => '+1-555-0002',
            'city' => 'Los Angeles',
            'role_id' => 2,
            'is_protected' => false,
        ]);

        $admin2 = User::create([
            'name' => 'Sarah Johnson',
            'email' => 'admin2@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make(value: 'parolaadmin'),
            'phone' => '+1-555-0003',
            'city' => 'Chicago',
            'role_id' => 2,
            'is_protected' => false,
        ]);

        // Create Regular Users
        $users = [
            [
                'name' => 'Michael Brown',
                'email' => 'michael@trains.local',
                'phone' => '+1-555-0004',
                'city' => 'Houston',
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily@trains.local',
                'phone' => '+1-555-0005',
                'city' => 'Phoenix',
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'email_verified_at' => now(),
                'password' => Hash::make(value: 'parolauser'),
                'phone' => $user['phone'],
                'city' => $user['city'],
                'role_id' => 3,
                'is_protected' => false,
            ]);
        }
    }
}

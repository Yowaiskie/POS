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
        $users = [
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'employee_id' => 'EMP-001',
                'phone' => '+63 912 345 6789',
                'position' => 'Admin',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Staff User',
                'username' => 'staff',
                'employee_id' => 'EMP-004',
                'phone' => '+63 905 111 2233',
                'position' => 'Staff',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['username' => $user['username']], $user);
        }
    }
}

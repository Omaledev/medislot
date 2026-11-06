<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Primary Platform Super Administrator
        User::firstOrCreate(
            ['email' => 'superadmin@medislot.ng'],
            [
                'hospital_id' => null,
                'department_id' => null,
                'name' => 'MediSlot Platform Founder',
                'password' => $password,
                'role' => 'superadmin',
                'phone' => '+234 800 000 0001',
                'title' => 'Platform Operator',
                'status' => 'active',
                'email_verified_at' => Carbon::now()->subMonths(3),
                'created_at' => Carbon::now()->subMonths(3),
            ]
        );

        // 2. Secondary Platform Systems Engineer
        User::firstOrCreate(
            ['email' => 'operations@medislot.ng'],
            [
                'hospital_id' => null,
                'department_id' => null,
                'name' => 'Kareem Abdullahi',
                'password' => $password,
                'role' => 'superadmin',
                'phone' => '+234 800 000 0002',
                'title' => 'Lead Systems Operator',
                'status' => 'active',
                'email_verified_at' => Carbon::now()->subMonths(2),
                'created_at' => Carbon::now()->subMonths(2),
            ]
        );
    }
}

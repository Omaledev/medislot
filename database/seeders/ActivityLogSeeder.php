<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Hospital;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $riverside = Hospital::where('slug', 'riverside')->first();
        $admin = User::where('email', 'hannah@riverside.example')->first();
        $superadmin = User::where('email', 'superadmin@medislot.ng')->first();

        if ($riverside && $admin) {
            $logs = [
                [
                    'action' => 'Admission created',
                    'desc' => 'General Ward · Chidi Eze admitted (MS-00418)',
                    'created_at' => Carbon::today()->setTime(10, 42),
                ],
                [
                    'action' => 'Patient discharged',
                    'desc' => 'Maternity · Bed released',
                    'created_at' => Carbon::today()->setTime(10, 31),
                ],
                [
                    'action' => 'Bed capacity updated',
                    'desc' => 'ICU capacity verified (1 available)',
                    'created_at' => Carbon::today()->setTime(9, 58),
                ],
                [
                    'action' => 'Lab test result entry',
                    'desc' => 'Laboratory · Blood glucose entered for Amina Yusuf (LR-0412)',
                    'created_at' => Carbon::today()->setTime(9, 50),
                ],
                [
                    'action' => 'Medication dispensed',
                    'desc' => 'Pharmacy · Paracetamol dispensed for Ada Nwosu (RX-0085)',
                    'created_at' => Carbon::today()->setTime(8, 50),
                ],
                [
                    'action' => 'Outpatient visit registered',
                    'desc' => 'Reception · Ada Nwosu registered for Paediatrics',
                    'created_at' => Carbon::today()->setTime(8, 5),
                ],
                [
                    'action' => 'Staff roster shift started',
                    'desc' => 'General Medicine · Morning shift clinicians checked in',
                    'created_at' => Carbon::today()->setTime(7, 30),
                ],
                [
                    'action' => 'Admission created',
                    'desc' => 'Paediatric Ward · Ada Nwosu admitted (MS-00411)',
                    'created_at' => Carbon::yesterday()->setTime(7, 45),
                ],
                [
                    'action' => 'Emergency service status toggle',
                    'desc' => 'Trauma Unit status confirmed accepting 24/7',
                    'created_at' => Carbon::now()->subDays(2)->setTime(16, 20),
                ],
                [
                    'action' => 'Inventory restock',
                    'desc' => 'Pharmacy · Metformin 500mg (Batch B-2315) received into stock',
                    'created_at' => Carbon::now()->subDays(4)->setTime(11, 0),
                ],
            ];

            foreach ($logs as $log) {
                ActivityLog::firstOrCreate(
                    [
                        'hospital_id' => $riverside->id,
                        'description' => $log['desc'],
                    ],
                    [
                        'user_id' => $admin->id,
                        'action' => $log['action'],
                        'created_at' => $log['created_at'],
                    ]
                );
            }
        }

        // Global Platform Logs
        if ($superadmin) {
            ActivityLog::firstOrCreate(
                ['description' => 'Platform Super Admin console initialized and security baseline established.'],
                [
                    'hospital_id' => null,
                    'user_id' => $superadmin->id,
                    'action' => 'platform_initialized',
                    'created_at' => Carbon::now()->subMonths(3),
                ]
            );

            ActivityLog::firstOrCreate(
                ['description' => 'Multi-tenant routing verification test executed successfully across active hospital domains.'],
                [
                    'hospital_id' => null,
                    'user_id' => $superadmin->id,
                    'action' => 'multitenant_verified',
                    'created_at' => Carbon::now()->subWeeks(1)->setTime(14, 0),
                ]
            );
        }
    }
}

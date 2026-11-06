<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Hospital;
use App\Models\HospitalService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HospitalServiceSeeder extends Seeder
{
    public function run(): void
    {
        $riverside = Hospital::where('slug', 'riverside')->first();
        $stAnne = Hospital::where('slug', 'st-anne')->first();
        $apex = Hospital::where('slug', 'apex-specialist-hospital')->first();
        $cedarcrest = Hospital::where('slug', 'cedarcrest-abuja')->first();

        if ($riverside) {
            $deptGeneralMed = Department::where('hospital_id', $riverside->id)->where('slug', 'general-medicine')->first();
            $deptLaboratory = Department::where('hospital_id', $riverside->id)->where('slug', 'laboratory')->first();
            $deptPharmacy = Department::where('hospital_id', $riverside->id)->where('slug', 'pharmacy')->first();
            $deptMaternity = Department::where('hospital_id', $riverside->id)->where('slug', 'maternity')->first();
            $deptSurgery = Department::where('hospital_id', $riverside->id)->where('slug', 'surgery')->first();
            $deptEmergency = Department::where('hospital_id', $riverside->id)->where('slug', 'emergency')->first();

            $services = [
                ['name' => 'Emergency Department', 'dept' => $deptEmergency?->id, 'status' => 'available', 'emergency' => true, 'visible' => true],
                ['name' => 'Maternity', 'dept' => $deptMaternity?->id, 'status' => 'available', 'emergency' => true, 'visible' => true],
                ['name' => 'General Medicine', 'dept' => $deptGeneralMed?->id, 'status' => 'available', 'emergency' => false, 'visible' => true],
                ['name' => 'Surgery', 'dept' => $deptSurgery?->id, 'status' => 'unavailable', 'emergency' => true, 'visible' => true],
                ['name' => 'Laboratory', 'dept' => $deptLaboratory?->id, 'status' => 'limited', 'emergency' => false, 'visible' => true],
                ['name' => 'Pharmacy', 'dept' => $deptPharmacy?->id, 'status' => 'available', 'emergency' => false, 'visible' => false],
                ['name' => 'ICU', 'dept' => $deptEmergency?->id, 'status' => 'limited', 'emergency' => true, 'visible' => true],
                ['name' => 'Paediatrics', 'dept' => $deptGeneralMed?->id, 'status' => 'unknown', 'emergency' => true, 'visible' => false],
            ];

            foreach ($services as $srv) {
                HospitalService::firstOrCreate(
                    ['hospital_id' => $riverside->id, 'name' => $srv['name']],
                    [
                        'department_id' => $srv['dept'],
                        'status' => $srv['status'],
                        'is_emergency_capable' => $srv['emergency'],
                        'is_public_visible' => $srv['visible'],
                        'last_updated_at' => Carbon::now()->subMinutes(3),
                    ]
                );
            }
        }

        if ($stAnne) {
            HospitalService::firstOrCreate(
                ['hospital_id' => $stAnne->id, 'name' => 'General Emergency'],
                [
                    'status' => 'available',
                    'is_emergency_capable' => true,
                    'is_public_visible' => true,
                    'last_updated_at' => Carbon::now()->subMinutes(18),
                ]
            );
            HospitalService::firstOrCreate(
                ['hospital_id' => $stAnne->id, 'name' => 'Surgery'],
                [
                    'status' => 'available',
                    'is_emergency_capable' => true,
                    'is_public_visible' => true,
                    'last_updated_at' => Carbon::now()->subMinutes(18),
                ]
            );
        }

        if ($apex) {
            HospitalService::firstOrCreate(
                ['hospital_id' => $apex->id, 'name' => 'Trauma Resuscitation'],
                [
                    'status' => 'available',
                    'is_emergency_capable' => true,
                    'is_public_visible' => true,
                    'last_updated_at' => Carbon::now()->subMinutes(45),
                ]
            );
        }

        if ($cedarcrest) {
            HospitalService::firstOrCreate(
                ['hospital_id' => $cedarcrest->id, 'name' => 'Cardiac Emergency Intervention'],
                [
                    'status' => 'available',
                    'is_emergency_capable' => true,
                    'is_public_visible' => true,
                    'last_updated_at' => Carbon::now()->subMinutes(12),
                ]
            );
        }
    }
}

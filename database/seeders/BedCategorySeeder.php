<?php

namespace Database\Seeders;

use App\Models\BedCategory;
use App\Models\Department;
use App\Models\Hospital;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BedCategorySeeder extends Seeder
{
    public function run(): void
    {
        $riverside = Hospital::where('slug', 'riverside')->first();
        $stAnne = Hospital::where('slug', 'st-anne')->first();
        $apex = Hospital::where('slug', 'apex-specialist-hospital')->first();
        $cedarcrest = Hospital::where('slug', 'cedarcrest-abuja')->first();
        $juth = Hospital::where('slug', 'juth-jos')->first();

        // 1. Riverside Wards
        if ($riverside) {
            $deptGeneralMed = Department::where('hospital_id', $riverside->id)->where('slug', 'general-medicine')->first();
            $deptMaternity = Department::where('hospital_id', $riverside->id)->where('slug', 'maternity')->first();
            $deptEmergency = Department::where('hospital_id', $riverside->id)->where('slug', 'emergency')->first();

            BedCategory::updateOrCreate(
                ['hospital_id' => $riverside->id, 'name' => 'Emergency Bay'],
                [
                    'department_id' => $deptEmergency?->id,
                    'care_category' => 'emergency',
                    'is_emergency_intake' => true,
                    'total_beds' => 8,
                    'occupied_beds' => 8,
                    'out_of_service_beds' => 0,
                    'available_beds' => 0,
                    'status' => 'full',
                    'last_updated_at' => Carbon::now()->subMinutes(3),
                ]
            );

            BedCategory::updateOrCreate(
                ['hospital_id' => $riverside->id, 'name' => 'General Ward'],
                [
                    'department_id' => $deptGeneralMed?->id,
                    'care_category' => 'general',
                    'is_emergency_intake' => false,
                    'total_beds' => 30,
                    'occupied_beds' => 24,
                    'out_of_service_beds' => 2,
                    'available_beds' => 4,
                    'status' => 'limited',
                    'last_updated_at' => Carbon::now()->subMinutes(3),
                ]
            );

            BedCategory::updateOrCreate(
                ['hospital_id' => $riverside->id, 'name' => 'Maternity'],
                [
                    'department_id' => $deptMaternity?->id,
                    'care_category' => 'maternity',
                    'is_emergency_intake' => false,
                    'restriction_scope' => 'female_obstetric_only',
                    'restriction_label' => 'Restricted: Labour & Delivery Only',
                    'total_beds' => 12,
                    'occupied_beds' => 8,
                    'out_of_service_beds' => 1,
                    'available_beds' => 3,
                    'status' => 'available',
                    'last_updated_at' => Carbon::now()->subMinutes(3),
                ]
            );

            BedCategory::updateOrCreate(
                ['hospital_id' => $riverside->id, 'name' => 'ICU'],
                [
                    'department_id' => $deptEmergency?->id,
                    'care_category' => 'icu',
                    'is_emergency_intake' => true,
                    'total_beds' => 6,
                    'occupied_beds' => 6,
                    'out_of_service_beds' => 0,
                    'available_beds' => 0,
                    'status' => 'full',
                    'last_updated_at' => Carbon::now()->subMinutes(3),
                ]
            );

            BedCategory::updateOrCreate(
                ['hospital_id' => $riverside->id, 'name' => 'Paediatric'],
                [
                    'department_id' => $deptGeneralMed?->id,
                    'care_category' => 'pediatric',
                    'is_emergency_intake' => false,
                    'restriction_scope' => 'pediatric_under_18',
                    'restriction_label' => 'Restricted: Children Under 14 Only',
                    'total_beds' => 16,
                    'occupied_beds' => 9,
                    'out_of_service_beds' => 1,
                    'available_beds' => 6,
                    'status' => 'available',
                    'last_updated_at' => Carbon::now()->subHours(1),
                ]
            );
        }

        // 2. St. Anne Wards
        if ($stAnne) {
            BedCategory::updateOrCreate(
                ['hospital_id' => $stAnne->id, 'name' => 'General Ward'],
                [
                    'care_category' => 'general',
                    'is_emergency_intake' => false,
                    'total_beds' => 20,
                    'occupied_beds' => 20,
                    'out_of_service_beds' => 0,
                    'available_beds' => 0,
                    'status' => 'full',
                    'last_updated_at' => Carbon::now()->subMinutes(18),
                ]
            );

            BedCategory::updateOrCreate(
                ['hospital_id' => $stAnne->id, 'name' => 'Emergency Bay'],
                [
                    'care_category' => 'emergency',
                    'is_emergency_intake' => true,
                    'total_beds' => 8,
                    'occupied_beds' => 6,
                    'out_of_service_beds' => 0,
                    'available_beds' => 2,
                    'status' => 'available',
                    'last_updated_at' => Carbon::now()->subMinutes(35),
                ]
            );
        }

        // 3. Apex Specialist Wards
        if ($apex) {
            BedCategory::updateOrCreate(
                ['hospital_id' => $apex->id, 'name' => 'Trauma Inpatient Unit'],
                [
                    'care_category' => 'emergency',
                    'is_emergency_intake' => true,
                    'total_beds' => 15,
                    'occupied_beds' => 7,
                    'out_of_service_beds' => 1,
                    'available_beds' => 7,
                    'status' => 'available',
                    'last_updated_at' => Carbon::now()->subMinutes(45),
                ]
            );

            BedCategory::updateOrCreate(
                ['hospital_id' => $apex->id, 'name' => 'Orthopaedic Surgery Ward'],
                [
                    'care_category' => 'specialized',
                    'is_emergency_intake' => false,
                    'restriction_scope' => 'inpatient_elective',
                    'restriction_label' => 'Restricted: Orthopaedic Surgical Patients Only',
                    'total_beds' => 10,
                    'occupied_beds' => 7,
                    'out_of_service_beds' => 0,
                    'available_beds' => 3,
                    'status' => 'available',
                    'last_updated_at' => Carbon::now()->subHours(2),
                ]
            );
        }

        // 4. Cedarcrest Wards
        if ($cedarcrest) {
            BedCategory::updateOrCreate(
                ['hospital_id' => $cedarcrest->id, 'name' => 'Critical Care Unit'],
                [
                    'care_category' => 'icu',
                    'is_emergency_intake' => true,
                    'total_beds' => 8,
                    'occupied_beds' => 4,
                    'out_of_service_beds' => 0,
                    'available_beds' => 4,
                    'status' => 'available',
                    'last_updated_at' => Carbon::now()->subMinutes(12),
                ]
            );

            BedCategory::updateOrCreate(
                ['hospital_id' => $cedarcrest->id, 'name' => 'Executive Suites'],
                [
                    'care_category' => 'specialized',
                    'is_emergency_intake' => false,
                    'restriction_scope' => 'private_suite',
                    'restriction_label' => 'Restricted: Private VIP Inpatient Only',
                    'total_beds' => 12,
                    'occupied_beds' => 6,
                    'out_of_service_beds' => 1,
                    'available_beds' => 5,
                    'status' => 'available',
                    'last_updated_at' => Carbon::now()->subHours(3),
                ]
            );
        }

        // 5. JUTH Wards
        if ($juth) {
            BedCategory::updateOrCreate(
                ['hospital_id' => $juth->id, 'name' => 'Accident & Emergency Red Zone'],
                [
                    'care_category' => 'emergency',
                    'is_emergency_intake' => true,
                    'total_beds' => 25,
                    'occupied_beds' => 18,
                    'out_of_service_beds' => 2,
                    'available_beds' => 5,
                    'status' => 'available',
                    'last_updated_at' => Carbon::now()->subMinutes(8),
                ]
            );

            BedCategory::updateOrCreate(
                ['hospital_id' => $juth->id, 'name' => 'Male Medical Ward'],
                [
                    'care_category' => 'general',
                    'is_emergency_intake' => false,
                    'restriction_scope' => 'male_only',
                    'restriction_label' => 'Restricted: Adult Males Only',
                    'total_beds' => 35,
                    'occupied_beds' => 30,
                    'out_of_service_beds' => 0,
                    'available_beds' => 5,
                    'status' => 'available',
                    'last_updated_at' => Carbon::now()->subHours(4),
                ]
            );
        }
    }
}

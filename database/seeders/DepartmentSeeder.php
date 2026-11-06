<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Hospital;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $riverside = Hospital::where('slug', 'riverside')->first();
        $stAnne = Hospital::where('slug', 'st-anne')->first();
        $apex = Hospital::where('slug', 'apex-specialist-hospital')->first();
        $cedarcrest = Hospital::where('slug', 'cedarcrest-abuja')->first();
        $juth = Hospital::where('slug', 'juth-jos')->first();

        // Riverside Departments
        if ($riverside) {
            $riversideDepts = [
                ['name' => 'General Medicine', 'slug' => 'general-medicine', 'type' => 'clinical', 'desc' => 'Outpatient consultation and routine internal medicine.', 'status' => 'active'],
                ['name' => 'Reception', 'slug' => 'reception', 'type' => 'administrative', 'desc' => 'Patient triage, registration and queue orchestration.', 'status' => 'active'],
                ['name' => 'Laboratory', 'slug' => 'laboratory', 'type' => 'diagnostic', 'desc' => 'Haematology, clinical chemistry and parasitology diagnostics.', 'status' => 'active'],
                ['name' => 'Pharmacy', 'slug' => 'pharmacy', 'type' => 'support', 'desc' => 'Medication dispensing, inventory tracking and prescription verification.', 'status' => 'active'],
                ['name' => 'Maternity', 'slug' => 'maternity', 'type' => 'clinical', 'desc' => 'Ante-natal, labour and post-natal maternal care.', 'status' => 'active'],
                ['name' => 'Surgery', 'slug' => 'surgery', 'type' => 'clinical', 'desc' => 'Surgical theatre and recovery.', 'status' => 'inactive'],
                ['name' => 'Emergency', 'slug' => 'emergency', 'type' => 'clinical', 'desc' => 'Emergency and critical trauma response.', 'status' => 'active'],
            ];

            foreach ($riversideDepts as $d) {
                Department::firstOrCreate(
                    ['hospital_id' => $riverside->id, 'slug' => $d['slug']],
                    [
                        'name' => $d['name'],
                        'type' => $d['type'],
                        'description' => $d['desc'],
                        'status' => $d['status'],
                    ]
                );
            }
        }

        // St Anne Departments
        if ($stAnne) {
            $stAnneDepts = [
                ['name' => 'General Medicine', 'slug' => 'general-medicine', 'type' => 'clinical', 'desc' => 'Specialist consultations.', 'status' => 'active'],
                ['name' => 'Reception', 'slug' => 'reception', 'type' => 'administrative', 'desc' => 'Front desk and triage.', 'status' => 'active'],
                ['name' => 'Emergency', 'slug' => 'emergency', 'type' => 'clinical', 'desc' => 'Trauma and acute care.', 'status' => 'active'],
                ['name' => 'Surgery', 'slug' => 'surgery', 'type' => 'clinical', 'desc' => 'Major surgical procedures.', 'status' => 'active'],
                ['name' => 'Laboratory', 'slug' => 'laboratory', 'type' => 'diagnostic', 'desc' => 'Clinical diagnostics.', 'status' => 'active'],
                ['name' => 'Pharmacy', 'slug' => 'pharmacy', 'type' => 'support', 'desc' => 'Dispensary.', 'status' => 'active'],
            ];

            foreach ($stAnneDepts as $d) {
                Department::firstOrCreate(
                    ['hospital_id' => $stAnne->id, 'slug' => $d['slug']],
                    [
                        'name' => $d['name'],
                        'type' => $d['type'],
                        'description' => $d['desc'],
                        'status' => $d['status'],
                    ]
                );
            }
        }

        // Apex Departments
        if ($apex) {
            $apexDepts = [
                ['name' => 'Orthopaedics & Trauma', 'slug' => 'orthopaedics', 'type' => 'clinical', 'desc' => 'Musculoskeletal trauma, bone grafting and arthroscopy.', 'status' => 'active'],
                ['name' => 'Emergency', 'slug' => 'emergency', 'type' => 'clinical', 'desc' => 'Accident & trauma stabilization.', 'status' => 'active'],
                ['name' => 'Reception', 'slug' => 'reception', 'type' => 'administrative', 'desc' => 'Front desk reception.', 'status' => 'active'],
                ['name' => 'Pharmacy', 'slug' => 'pharmacy', 'type' => 'support', 'desc' => 'Inpatient and outpatient pharmacy.', 'status' => 'active'],
            ];

            foreach ($apexDepts as $d) {
                Department::firstOrCreate(
                    ['hospital_id' => $apex->id, 'slug' => $d['slug']],
                    [
                        'name' => $d['name'],
                        'type' => $d['type'],
                        'description' => $d['desc'],
                        'status' => $d['status'],
                    ]
                );
            }
        }

        // Cedarcrest Departments
        if ($cedarcrest) {
            $cedarcrestDepts = [
                ['name' => 'Emergency & Trauma', 'slug' => 'emergency', 'type' => 'clinical', 'desc' => '24/7 Level 1 Trauma Center.', 'status' => 'active'],
                ['name' => 'Critical Care Unit', 'slug' => 'critical-care', 'type' => 'clinical', 'desc' => 'Intensive care telemetry.', 'status' => 'active'],
                ['name' => 'Cardiology', 'slug' => 'cardiology', 'type' => 'clinical', 'desc' => 'Cardiovascular interventions.', 'status' => 'active'],
                ['name' => 'Reception', 'slug' => 'reception', 'type' => 'administrative', 'desc' => 'Patient arrivals and concierge.', 'status' => 'active'],
            ];

            foreach ($cedarcrestDepts as $d) {
                Department::firstOrCreate(
                    ['hospital_id' => $cedarcrest->id, 'slug' => $d['slug']],
                    [
                        'name' => $d['name'],
                        'type' => $d['type'],
                        'description' => $d['desc'],
                        'status' => $d['status'],
                    ]
                );
            }
        }

        // JUTH Departments
        if ($juth) {
            $juthDepts = [
                ['name' => 'Accident & Emergency', 'slug' => 'emergency', 'type' => 'clinical', 'desc' => 'Tertiary emergency response.', 'status' => 'active'],
                ['name' => 'Internal Medicine', 'slug' => 'general-medicine', 'type' => 'clinical', 'desc' => 'Inpatient and outpatient medicine.', 'status' => 'active'],
                ['name' => 'Paediatrics', 'slug' => 'paediatrics', 'type' => 'clinical', 'desc' => 'Neonatal and child health.', 'status' => 'active'],
            ];

            foreach ($juthDepts as $d) {
                Department::firstOrCreate(
                    ['hospital_id' => $juth->id, 'slug' => $d['slug']],
                    [
                        'name' => $d['name'],
                        'type' => $d['type'],
                        'description' => $d['desc'],
                        'status' => $d['status'],
                    ]
                );
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic modular domain data.
     */
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            HospitalSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            BedCategorySeeder::class,
            HospitalServiceSeeder::class,
            PatientSeeder::class,
            VisitSeeder::class,
            ConsultationNoteSeeder::class,
            LaboratorySeeder::class,
            PharmacySeeder::class,
            AdmissionSeeder::class,
            ActivityLogSeeder::class,
            HospitalApiKeySeeder::class,
        ]);
    }
}

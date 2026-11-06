<?php

namespace Database\Seeders;

use App\Models\Hospital;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Primary Hospital: Riverside General Hospital
        Hospital::firstOrCreate(
            ['slug' => 'riverside'],
            [
                'name' => 'Riverside General Hospital',
                'type' => 'General Hospital',
                'email' => 'info@riverside.example',
                'phone' => '0800 000 0000',
                'country' => 'Nigeria',
                'state' => 'Plateau',
                'lga' => 'Jos North',
                'address' => '14 Marina Road, Jos North',
                'registration_number' => 'RC-998231',
                'website' => 'https://riverside.example',
                'emergency_status' => 'accepting',
                'location_for_directions' => '14 Marina Road · pin set',
                'warn_unupdated_hours' => 1,
                'mark_stale_hours' => 3,
                'status' => 'active',
                'created_at' => Carbon::now()->subMonths(4),
                'last_reported_at' => Carbon::now()->subMinutes(3),
            ]
        );

        // 2. St. Anne Specialist Hospital
        Hospital::firstOrCreate(
            ['slug' => 'st-anne'],
            [
                'name' => 'St. Anne Specialist Hospital',
                'type' => 'Specialist Hospital',
                'email' => 'contact@st-anne.example',
                'phone' => '0800 111 2222',
                'country' => 'Nigeria',
                'state' => 'Plateau',
                'lga' => 'Jos North',
                'address' => '7 Hill Street, Jos North',
                'registration_number' => 'RC-884210',
                'website' => 'https://stanne.example',
                'emergency_status' => 'limited',
                'location_for_directions' => '7 Hill Street, Jos North',
                'warn_unupdated_hours' => 1,
                'mark_stale_hours' => 3,
                'status' => 'active',
                'created_at' => Carbon::now()->subMonths(2),
                'last_reported_at' => Carbon::now()->subMinutes(18),
            ]
        );

        // 3. Apex Specialist Hospital (with custom white-label domain)
        Hospital::firstOrCreate(
            ['slug' => 'apex-specialist-hospital'],
            [
                'name' => 'Apex Specialist Hospital',
                'type' => 'Specialist Hospital',
                'email' => 'admin@apexspecialist.org',
                'phone' => '+234 803 456 7890',
                'country' => 'Nigeria',
                'state' => 'Plateau',
                'lga' => 'Jos South',
                'address' => '24 Rayfield Road, Jos South',
                'registration_number' => 'RC-772194',
                'website' => 'https://apexspecialist.org',
                'custom_domain' => 'his.apexspecialist.org',
                'custom_domain_status' => 'active',
                'custom_domain_verified_at' => Carbon::now()->subWeeks(3),
                'emergency_status' => 'accepting',
                'location_for_directions' => '24 Rayfield Road, Rayfield, Jos',
                'warn_unupdated_hours' => 2,
                'mark_stale_hours' => 4,
                'status' => 'active',
                'created_at' => Carbon::now()->subWeeks(6),
                'last_reported_at' => Carbon::now()->subMinutes(45),
            ]
        );

        // 4. Cedarcrest Hospitals Abuja
        Hospital::firstOrCreate(
            ['slug' => 'cedarcrest-abuja'],
            [
                'name' => 'Cedarcrest Hospitals Abuja',
                'type' => 'Tertiary Specialty Hospital',
                'email' => 'info@cedarcrest.example',
                'phone' => '+234 809 123 4567',
                'country' => 'Nigeria',
                'state' => 'FCT',
                'lga' => 'Abuja Municipal',
                'address' => 'Plot 1205 Sam Mbakwe Avenue, Gudu District, Abuja',
                'registration_number' => 'RC-654321',
                'website' => 'https://cedarcrest.example',
                'emergency_status' => 'accepting',
                'location_for_directions' => 'Plot 1205 Sam Mbakwe Ave, Gudu, Abuja',
                'warn_unupdated_hours' => 1,
                'mark_stale_hours' => 3,
                'status' => 'active',
                'created_at' => Carbon::now()->subMonths(1),
                'last_reported_at' => Carbon::now()->subMinutes(12),
            ]
        );

        // 5. Jos University Teaching Hospital (JUTH)
        Hospital::firstOrCreate(
            ['slug' => 'juth-jos'],
            [
                'name' => 'Jos University Teaching Hospital',
                'type' => 'Teaching Hospital',
                'email' => 'enquiries@juth.example',
                'phone' => '+234 802 987 6543',
                'country' => 'Nigeria',
                'state' => 'Plateau',
                'lga' => 'Jos East',
                'address' => 'Permanent Site, Lamingo, Jos',
                'registration_number' => 'FED-HOSP-0042',
                'website' => 'https://juth.example',
                'emergency_status' => 'accepting',
                'location_for_directions' => 'Lamingo Dam Road, Jos',
                'warn_unupdated_hours' => 2,
                'mark_stale_hours' => 4,
                'status' => 'active',
                'created_at' => Carbon::now()->subMonths(5),
                'last_reported_at' => Carbon::now()->subMinutes(8),
            ]
        );
    }
}

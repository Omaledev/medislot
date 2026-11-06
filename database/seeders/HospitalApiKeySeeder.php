<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\HospitalApiKey;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HospitalApiKeySeeder extends Seeder
{
    public function run(): void
    {
        $riverside = Hospital::where('slug', 'riverside')->first();
        $stAnne = Hospital::where('slug', 'st-anne')->first();
        $apex = Hospital::where('slug', 'apex-specialist-hospital')->first();
        $cedarcrest = Hospital::where('slug', 'cedarcrest-abuja')->first();

        if ($riverside) {
            HospitalApiKey::firstOrCreate(
                ['hospital_id' => $riverside->id, 'name' => 'Epic Systems ADT Webhook Receiver'],
                [
                    'key_prefix' => 'ms_live_rvs',
                    'key_hash' => hash('sha256', 'ms_live_rvs_epic_webhook_key_2026'),
                    'permissions' => ['adt:admit', 'adt:discharge', 'beds:sync'],
                    'last_used_at' => Carbon::now()->subMinutes(14),
                    'expires_at' => Carbon::now()->addYear(),
                    'created_at' => Carbon::now()->subMonths(3),
                ]
            );

            HospitalApiKey::firstOrCreate(
                ['hospital_id' => $riverside->id, 'name' => 'Cerner Millennium Bed Telemetry Sync'],
                [
                    'key_prefix' => 'ms_live_crn',
                    'key_hash' => hash('sha256', 'ms_live_crn_bed_sync_key_2026'),
                    'permissions' => ['beds:sync', 'beds:read'],
                    'last_used_at' => Carbon::now()->subHours(2),
                    'expires_at' => Carbon::now()->addMonths(6),
                    'created_at' => Carbon::now()->subMonths(2),
                ]
            );
        }

        if ($stAnne) {
            HospitalApiKey::firstOrCreate(
                ['hospital_id' => $stAnne->id, 'name' => 'Bahmni Hospital Management Connector'],
                [
                    'key_prefix' => 'ms_live_sta',
                    'key_hash' => hash('sha256', 'ms_live_sta_bahmni_key_2026'),
                    'permissions' => ['adt:admit', 'adt:discharge'],
                    'last_used_at' => Carbon::now()->subDays(1),
                    'expires_at' => Carbon::now()->addYear(),
                    'created_at' => Carbon::now()->subMonth(),
                ]
            );
        }

        if ($apex) {
            HospitalApiKey::firstOrCreate(
                ['hospital_id' => $apex->id, 'name' => 'OpenMRS Clinical Admissions Sync'],
                [
                    'key_prefix' => 'ms_live_apx',
                    'key_hash' => hash('sha256', 'ms_live_apx_openmrs_key_2026'),
                    'permissions' => ['adt:admit', 'adt:discharge', 'beds:sync'],
                    'last_used_at' => Carbon::now()->subHours(5),
                    'expires_at' => Carbon::now()->addYear(),
                    'created_at' => Carbon::now()->subWeeks(4),
                ]
            );
        }

        if ($cedarcrest) {
            HospitalApiKey::firstOrCreate(
                ['hospital_id' => $cedarcrest->id, 'name' => 'Philips IntelliSpace Critical Care Stream'],
                [
                    'key_prefix' => 'ms_live_cdr',
                    'key_hash' => hash('sha256', 'ms_live_cdr_philips_key_2026'),
                    'permissions' => ['beds:sync'],
                    'last_used_at' => Carbon::now()->subMinutes(40),
                    'expires_at' => Carbon::now()->addMonths(9),
                    'created_at' => Carbon::now()->subWeeks(3),
                ]
            );
        }
    }
}

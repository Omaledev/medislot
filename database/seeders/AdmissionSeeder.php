<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\BedCategory;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AdmissionSeeder extends Seeder
{
    public function run(): void
    {
        $riverside = Hospital::where('slug', 'riverside')->first();
        if (! $riverside) {
            return;
        }

        $doctorBello = User::where('email', 'bello@riverside.example')->first();
        $doctorEze = User::where('email', 'eze@riverside.example')->first();
        $doctorOkon = User::where('email', 'okon@riverside.example')->first();

        $chidi = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000098')->first();
        $mary = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000121')->first();
        $ibrahim = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000119')->first();
        $ada = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000112')->first();
        $emmanuel = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000135')->first();

        $wardGeneral = BedCategory::where('hospital_id', $riverside->id)->where('name', 'General Ward')->first();
        $wardMaternity = BedCategory::where('hospital_id', $riverside->id)->where('name', 'Maternity')->first();
        $wardICU = BedCategory::where('hospital_id', $riverside->id)->where('name', 'ICU')->first();
        $wardPaediatric = BedCategory::where('hospital_id', $riverside->id)->where('name', 'Paediatric')->first();

        // 1. Chidi Eze: General Ward
        if ($chidi && $wardGeneral) {
            Admission::firstOrCreate(
                ['hospital_id' => $riverside->id, 'admission_number' => 'MS-00418'],
                [
                    'patient_id' => $chidi->id,
                    'bed_category_id' => $wardGeneral->id,
                    'admitted_at' => Carbon::yesterday()->setTime(9, 20),
                    'status' => 'admitted',
                    'notes' => 'Inpatient observation for deep joint effusion.',
                    'admitted_by' => $doctorBello?->id,
                    'created_at' => Carbon::yesterday()->setTime(9, 20),
                ]
            );
        }

        // 2. Mary Danjuma: Maternity
        if ($mary && $wardMaternity) {
            Admission::firstOrCreate(
                ['hospital_id' => $riverside->id, 'admission_number' => 'MS-00397'],
                [
                    'patient_id' => $mary->id,
                    'bed_category_id' => $wardMaternity->id,
                    'admitted_at' => Carbon::now()->subDays(2)->setTime(22, 5),
                    'status' => 'admitted',
                    'notes' => 'Labour ward observation and fetal heart rate monitoring.',
                    'admitted_by' => $doctorEze?->id,
                    'created_at' => Carbon::now()->subDays(2)->setTime(22, 5),
                ]
            );
        }

        // 3. Ibrahim Musa: ICU
        if ($ibrahim && $wardICU) {
            Admission::firstOrCreate(
                ['hospital_id' => $riverside->id, 'admission_number' => 'MS-00352'],
                [
                    'patient_id' => $ibrahim->id,
                    'bed_category_id' => $wardICU->id,
                    'admitted_at' => Carbon::now()->subDays(3)->setTime(14, 10),
                    'status' => 'critical_care',
                    'notes' => 'Continuous telemetry, SpO2 monitoring and oxygen therapy.',
                    'admitted_by' => $doctorBello?->id,
                    'created_at' => Carbon::now()->subDays(3)->setTime(14, 10),
                ]
            );
        }

        // 4. Ada Nwosu: Paediatric
        if ($ada && $wardPaediatric) {
            Admission::firstOrCreate(
                ['hospital_id' => $riverside->id, 'admission_number' => 'MS-00411'],
                [
                    'patient_id' => $ada->id,
                    'bed_category_id' => $wardPaediatric->id,
                    'admitted_at' => Carbon::yesterday()->setTime(7, 45),
                    'status' => 'admitted',
                    'notes' => 'Paediatric recovery post bronchodilator treatment.',
                    'admitted_by' => $doctorOkon?->id,
                    'created_at' => Carbon::yesterday()->setTime(7, 45),
                ]
            );
        }

        // 5. Emmanuel Yakubu: General Ward
        if ($emmanuel && $wardGeneral) {
            Admission::firstOrCreate(
                ['hospital_id' => $riverside->id, 'admission_number' => 'MS-00425'],
                [
                    'patient_id' => $emmanuel->id,
                    'bed_category_id' => $wardGeneral->id,
                    'admitted_at' => Carbon::now()->subDays(3)->setTime(10, 0),
                    'status' => 'admitted',
                    'notes' => 'Diabetic ketoacidosis prevention protocol and insulin titration.',
                    'admitted_by' => $doctorBello?->id,
                    'created_at' => Carbon::now()->subDays(3)->setTime(10, 0),
                ]
            );
        }

        // 6. Past Discharged Admission (Historical)
        if ($chidi && $wardGeneral) {
            Admission::firstOrCreate(
                ['hospital_id' => $riverside->id, 'admission_number' => 'MS-00288'],
                [
                    'patient_id' => $chidi->id,
                    'bed_category_id' => $wardGeneral->id,
                    'admitted_at' => Carbon::now()->subDays(20)->setTime(11, 0),
                    'discharged_at' => Carbon::now()->subDays(17)->setTime(14, 30),
                    'status' => 'discharged',
                    'notes' => 'Management of acute gouty arthritis flare. Discharged stable on Allopurinol.',
                    'admitted_by' => $doctorBello?->id,
                    'created_at' => Carbon::now()->subDays(20)->setTime(11, 0),
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VisitSeeder extends Seeder
{
    public function run(): void
    {
        $riverside = Hospital::where('slug', 'riverside')->first();
        if (! $riverside) {
            return;
        }

        $deptGeneralMed = Department::where('hospital_id', $riverside->id)->where('slug', 'general-medicine')->first();
        $deptMaternity = Department::where('hospital_id', $riverside->id)->where('slug', 'maternity')->first();

        $doctorBello = User::where('email', 'bello@riverside.example')->first();
        $doctorEze = User::where('email', 'eze@riverside.example')->first();
        $doctorOkon = User::where('email', 'okon@riverside.example')->first();
        $receptionist = User::where('email', 'joy@riverside.example')->first();

        $amina = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000124')->first();
        $ibrahim = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000119')->first();
        $grace = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000127')->first();
        $ada = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000112')->first();
        $mary = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000121')->first();
        $chidi = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000098')->first();
        $fatima = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000130')->first();
        $emmanuel = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000135')->first();
        $blessing = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000140')->first();

        // 1. Amina Yusuf - Today
        if ($amina) {
            Visit::firstOrCreate(
                ['hospital_id' => $riverside->id, 'visit_number' => 'V-0214'],
                [
                    'patient_id' => $amina->id,
                    'department_id' => $deptGeneralMed?->id,
                    'clinician_id' => $doctorBello?->id,
                    'visit_type' => 'Walk-in',
                    'reason_for_visit' => 'Fever and headache',
                    'priority' => 'Routine',
                    'status' => 'in_consultation',
                    'arrived_at' => Carbon::today()->setTime(9, 12),
                    'created_by' => $receptionist?->id,
                    'created_at' => Carbon::today()->setTime(9, 12),
                ]
            );
        }

        // 2. Ibrahim Musa - Today
        if ($ibrahim) {
            Visit::firstOrCreate(
                ['hospital_id' => $riverside->id, 'visit_number' => 'V-0215'],
                [
                    'patient_id' => $ibrahim->id,
                    'department_id' => $deptGeneralMed?->id,
                    'clinician_id' => $doctorBello?->id,
                    'visit_type' => 'Walk-in',
                    'reason_for_visit' => 'Chest pain follow-up',
                    'priority' => 'Urgent',
                    'status' => 'waiting',
                    'arrived_at' => Carbon::today()->setTime(8, 25),
                    'created_by' => $receptionist?->id,
                    'created_at' => Carbon::today()->setTime(8, 25),
                ]
            );
        }

        // 3. Grace Okoro - Today
        if ($grace) {
            Visit::firstOrCreate(
                ['hospital_id' => $riverside->id, 'visit_number' => 'V-0216'],
                [
                    'patient_id' => $grace->id,
                    'department_id' => $deptGeneralMed?->id,
                    'clinician_id' => $doctorBello?->id,
                    'visit_type' => 'Walk-in',
                    'reason_for_visit' => 'Abdominal pain',
                    'priority' => 'Routine',
                    'status' => 'waiting',
                    'arrived_at' => Carbon::today()->setTime(9, 30),
                    'created_by' => $receptionist?->id,
                    'created_at' => Carbon::today()->setTime(9, 30),
                ]
            );
        }

        // 4. Ada Nwosu - Today (Completed)
        if ($ada) {
            Visit::firstOrCreate(
                ['hospital_id' => $riverside->id, 'visit_number' => 'V-0217'],
                [
                    'patient_id' => $ada->id,
                    'department_id' => $deptGeneralMed?->id,
                    'clinician_id' => $doctorOkon?->id,
                    'visit_type' => 'Walk-in',
                    'reason_for_visit' => 'Cough for 3 days',
                    'priority' => 'Routine',
                    'status' => 'completed',
                    'arrived_at' => Carbon::today()->setTime(8, 5),
                    'completed_at' => Carbon::today()->setTime(8, 45),
                    'created_by' => $receptionist?->id,
                    'created_at' => Carbon::today()->setTime(8, 5),
                ]
            );
        }

        // 5. Mary Danjuma - Today
        if ($mary) {
            Visit::firstOrCreate(
                ['hospital_id' => $riverside->id, 'visit_number' => 'V-0209'],
                [
                    'patient_id' => $mary->id,
                    'department_id' => $deptMaternity?->id,
                    'clinician_id' => $doctorEze?->id,
                    'visit_type' => 'Walk-in',
                    'reason_for_visit' => 'Routine antenatal checkup',
                    'priority' => 'Routine',
                    'status' => 'in_consultation',
                    'arrived_at' => Carbon::today()->setTime(8, 40),
                    'created_by' => $receptionist?->id,
                    'created_at' => Carbon::today()->setTime(8, 40),
                ]
            );
        }

        // 6. Chidi Eze - Yesterday (Cancelled)
        if ($chidi) {
            Visit::firstOrCreate(
                ['hospital_id' => $riverside->id, 'visit_number' => 'V-0210'],
                [
                    'patient_id' => $chidi->id,
                    'department_id' => $deptGeneralMed?->id,
                    'clinician_id' => null,
                    'visit_type' => 'Walk-in',
                    'reason_for_visit' => 'Severe knee swelling',
                    'priority' => 'Routine',
                    'status' => 'cancelled',
                    'cancellation_reason' => 'Patient left before being seen',
                    'arrived_at' => Carbon::yesterday()->setTime(7, 50),
                    'created_by' => $receptionist?->id,
                    'created_at' => Carbon::yesterday()->setTime(7, 50),
                ]
            );
        }

        // 7. Fatima Garba - Yesterday (Completed)
        if ($fatima) {
            Visit::firstOrCreate(
                ['hospital_id' => $riverside->id, 'visit_number' => 'V-0211'],
                [
                    'patient_id' => $fatima->id,
                    'department_id' => $deptGeneralMed?->id,
                    'clinician_id' => $doctorBello?->id,
                    'visit_type' => 'Walk-in',
                    'reason_for_visit' => 'Routine medical screening',
                    'priority' => 'Routine',
                    'status' => 'completed',
                    'arrived_at' => Carbon::yesterday()->setTime(11, 45),
                    'completed_at' => Carbon::yesterday()->setTime(12, 30),
                    'created_by' => $receptionist?->id,
                    'created_at' => Carbon::yesterday()->setTime(11, 45),
                ]
            );
        }

        // 8. Emmanuel Yakubu - 3 Days Ago (Completed & Admitted)
        if ($emmanuel) {
            Visit::firstOrCreate(
                ['hospital_id' => $riverside->id, 'visit_number' => 'V-0205'],
                [
                    'patient_id' => $emmanuel->id,
                    'department_id' => $deptGeneralMed?->id,
                    'clinician_id' => $doctorBello?->id,
                    'visit_type' => 'Emergency',
                    'reason_for_visit' => 'Hyperglycaemia and dizzy spells',
                    'priority' => 'Urgent',
                    'status' => 'completed',
                    'arrived_at' => Carbon::now()->subDays(3)->setTime(8, 20),
                    'completed_at' => Carbon::now()->subDays(3)->setTime(9, 45),
                    'created_by' => $receptionist?->id,
                    'created_at' => Carbon::now()->subDays(3)->setTime(8, 20),
                ]
            );
        }

        // 9. Blessing Adeleke - Today
        if ($blessing) {
            Visit::firstOrCreate(
                ['hospital_id' => $riverside->id, 'visit_number' => 'V-0218'],
                [
                    'patient_id' => $blessing->id,
                    'department_id' => $deptGeneralMed?->id,
                    'clinician_id' => $doctorBello?->id,
                    'visit_type' => 'Walk-in',
                    'reason_for_visit' => 'Skin rash and itching',
                    'priority' => 'Routine',
                    'status' => 'waiting',
                    'arrived_at' => Carbon::today()->setTime(10, 15),
                    'created_by' => $receptionist?->id,
                    'created_at' => Carbon::today()->setTime(10, 15),
                ]
            );
        }
    }
}

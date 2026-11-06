<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\LaboratoryRequest;
use App\Models\LaboratoryResult;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LaboratorySeeder extends Seeder
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
        $labScientist = User::where('email', 'samuel@riverside.example')->first();

        $amina = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000124')->first();
        $ibrahim = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000119')->first();
        $grace = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000127')->first();
        $mary = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000121')->first();

        $visitAmina = Visit::where('hospital_id', $riverside->id)->where('visit_number', 'V-0214')->first();
        $visitGrace = Visit::where('hospital_id', $riverside->id)->where('visit_number', 'V-0216')->first();
        $visitIbrahim = Visit::where('hospital_id', $riverside->id)->where('visit_number', 'V-0215')->first();
        $visitMary = Visit::where('hospital_id', $riverside->id)->where('visit_number', 'V-0209')->first();

        // 1. Amina: Blood Glucose (Chemical Pathology)
        if ($visitAmina && $amina) {
            LaboratoryRequest::firstOrCreate(
                ['hospital_id' => $riverside->id, 'request_number' => 'LR-0412'],
                [
                    'visit_id' => $visitAmina->id,
                    'patient_id' => $amina->id,
                    'requester_id' => $doctorBello?->id,
                    'category' => 'Chemical Pathology',
                    'test_name' => 'Blood glucose',
                    'clinical_reason' => 'Fever and headache',
                    'priority' => 'Urgent',
                    'instructions' => 'Stat random blood sugar evaluation.',
                    'status' => 'result_entry',
                    'sample_type' => 'Blood',
                    'sample_collected_at' => Carbon::today()->setTime(10, 5),
                    'sample_collected_by' => $labScientist?->id,
                    'created_at' => Carbon::today()->setTime(9, 50),
                ]
            );
        }

        // 2. Grace: Urinalysis (Parasitology)
        if ($visitGrace && $grace) {
            LaboratoryRequest::firstOrCreate(
                ['hospital_id' => $riverside->id, 'request_number' => 'LR-0411'],
                [
                    'visit_id' => $visitGrace->id,
                    'patient_id' => $grace->id,
                    'requester_id' => $doctorBello?->id,
                    'category' => 'Parasitology',
                    'test_name' => 'Urinalysis',
                    'clinical_reason' => 'Abdominal pain',
                    'priority' => 'Routine',
                    'instructions' => 'Dipstick and microscopy.',
                    'status' => 'new',
                    'sample_type' => 'Urine',
                    'created_at' => Carbon::today()->setTime(9, 35),
                ]
            );
        }

        // 3. Ibrahim: Full Blood Count (Haematology)
        if ($visitIbrahim && $ibrahim) {
            LaboratoryRequest::firstOrCreate(
                ['hospital_id' => $riverside->id, 'request_number' => 'LR-0409'],
                [
                    'visit_id' => $visitIbrahim->id,
                    'patient_id' => $ibrahim->id,
                    'requester_id' => $doctorOkon?->id,
                    'category' => 'Haematology',
                    'test_name' => 'Full blood count',
                    'clinical_reason' => 'Chest pain follow-up',
                    'priority' => 'Urgent',
                    'instructions' => 'Automated CBC with differential.',
                    'status' => 'processing',
                    'sample_type' => 'Blood',
                    'sample_collected_at' => Carbon::today()->setTime(9, 10),
                    'sample_collected_by' => $labScientist?->id,
                    'created_at' => Carbon::today()->setTime(8, 30),
                ]
            );
        }

        // 4. Mary: Pregnancy Test
        if ($visitMary && $mary) {
            LaboratoryRequest::firstOrCreate(
                ['hospital_id' => $riverside->id, 'request_number' => 'LR-0408'],
                [
                    'visit_id' => $visitMary->id,
                    'patient_id' => $mary->id,
                    'requester_id' => $doctorEze?->id,
                    'category' => 'General',
                    'test_name' => 'Pregnancy test',
                    'clinical_reason' => 'Antenatal booking',
                    'priority' => 'Routine',
                    'instructions' => 'Qualitative serum hCG test.',
                    'status' => 'new',
                    'sample_type' => 'Blood',
                    'created_at' => Carbon::today()->setTime(8, 45),
                ]
            );
        }

        // 5. Ibrahim: Malaria Parasite (Finalized Result)
        if ($visitIbrahim && $ibrahim) {
            $lrIbrahimMalaria = LaboratoryRequest::firstOrCreate(
                ['hospital_id' => $riverside->id, 'request_number' => 'LR-0405'],
                [
                    'visit_id' => $visitIbrahim->id,
                    'patient_id' => $ibrahim->id,
                    'requester_id' => $doctorBello?->id,
                    'category' => 'Parasitology',
                    'test_name' => 'Malaria parasite',
                    'clinical_reason' => 'Chills and low grade fever',
                    'priority' => 'Routine',
                    'instructions' => 'Thick and thin blood film.',
                    'status' => 'submitted',
                    'sample_type' => 'Blood',
                    'sample_collected_at' => Carbon::today()->setTime(8, 40),
                    'sample_collected_by' => $labScientist?->id,
                    'created_at' => Carbon::today()->setTime(8, 30),
                ]
            );

            LaboratoryResult::firstOrCreate(
                ['laboratory_request_id' => $lrIbrahimMalaria->id],
                [
                    'entered_by' => $labScientist?->id,
                    'result_value' => 'Positive (+)',
                    'unit' => 'trophozoites/uL',
                    'reference_range' => 'Negative',
                    'interpretation' => 'Plasmodium falciparum trophozoites seen in moderate density.',
                    'lab_notes' => 'Verified on dual Giemsa stained film.',
                    'status' => 'finalized',
                    'finalized_at' => Carbon::today()->setTime(9, 20),
                    'clinical_notes_by_doctor' => 'Confirmed malaria. Commence ACT therapy.',
                ]
            );
        }
    }
}

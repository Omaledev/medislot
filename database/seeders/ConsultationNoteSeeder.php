<?php

namespace Database\Seeders;

use App\Models\ConsultationNote;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ConsultationNoteSeeder extends Seeder
{
    public function run(): void
    {
        $riverside = Hospital::where('slug', 'riverside')->first();
        if (! $riverside) {
            return;
        }

        $doctorBello = User::where('email', 'bello@riverside.example')->first();
        $amina = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000124')->first();
        $visitAmina = Visit::where('hospital_id', $riverside->id)->where('visit_number', 'V-0214')->first();

        $emmanuel = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000135')->first();
        $visitEmmanuel = Visit::where('hospital_id', $riverside->id)->where('visit_number', 'V-0205')->first();

        // 1. Amina Yusuf Consultation Note
        if ($visitAmina && $amina && $doctorBello) {
            ConsultationNote::firstOrCreate(
                ['visit_id' => $visitAmina->id],
                [
                    'patient_id' => $amina->id,
                    'clinician_id' => $doctorBello->id,
                    'chief_complaint' => 'Fever and headache for two days',
                    'history_of_present_illness' => 'Patient reports sudden onset of moderate-to-high fever with accompanying rigors, body aches, and persistent throbbing frontotemporal headache for 48 hours.',
                    'relevant_medical_history' => 'Hypertension (managed on Amlodipine 5mg). Known penicillin allergy.',
                    'examination_findings' => 'Alert, mildly dehydrated, febrile (temp 38.8 C). BP 135/85 mmHg, PR 92 bpm, RR 18 cpm. Chest clear, abdomen soft, non-tender.',
                    'assessment_diagnosis' => 'Acute febrile illness, query Malaria parasite vs Upper Respiratory Tract Infection.',
                    'treatment_plan' => '1. Urgent malaria parasite film and random blood glucose. 2. Prescribe Artemether/Lumefantrine + Paracetamol 500mg. 3. Encourage fluid intake.',
                    'follow_up_instructions' => 'Review in 3 days with lab results or earlier if warning signs (vomiting, confusion) occur.',
                    'status' => 'draft',
                    'created_at' => Carbon::today()->setTime(9, 45),
                ]
            );
        }

        // 2. Emmanuel Yakubu Consultation Note (3 Days Ago)
        if ($visitEmmanuel && $emmanuel && $doctorBello) {
            ConsultationNote::firstOrCreate(
                ['visit_id' => $visitEmmanuel->id],
                [
                    'patient_id' => $emmanuel->id,
                    'clinician_id' => $doctorBello->id,
                    'chief_complaint' => 'Marked weakness, polydipsia and dizzy spells for 3 days',
                    'history_of_present_illness' => '61-year-old known diabetic presenting with progressive fatigue, excessive thirst, and frequent urination.',
                    'relevant_medical_history' => 'Type 2 Diabetes Mellitus x 8 years, Essential Hypertension x 12 years. Known Aspirin allergy.',
                    'examination_findings' => 'Pale, dry tongue and mucous membranes, BP 155/95 mmHg, PR 88 bpm. Random Blood Sugar: 18.4 mmol/L.',
                    'assessment_diagnosis' => 'Uncontrolled Type 2 Diabetes with moderate dehydration.',
                    'treatment_plan' => '1. Admit to Male Medical Ward for rehydration and insulin stabilization. 2. Stat Normal Saline 1L IV over 2 hours. 3. Daily fasting and post-prandial blood sugar profile.',
                    'follow_up_instructions' => 'Inpatient monitoring until blood glucose stabilises below 10 mmol/L.',
                    'status' => 'finalized',
                    'created_at' => Carbon::now()->subDays(3)->setTime(9, 15),
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\RequestMessage;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PharmacySeeder extends Seeder
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
        $pharmacist = User::where('email', 'peace@riverside.example')->first();

        $amina = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000124')->first();
        $ibrahim = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000119')->first();
        $grace = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000127')->first();
        $ada = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000112')->first();
        $mary = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000121')->first();
        $chidi = Patient::where('hospital_id', $riverside->id)->where('patient_id', 'MED-000098')->first();

        $visitAmina = Visit::where('hospital_id', $riverside->id)->where('visit_number', 'V-0214')->first();
        $visitGrace = Visit::where('hospital_id', $riverside->id)->where('visit_number', 'V-0216')->first();
        $visitAda = Visit::where('hospital_id', $riverside->id)->where('visit_number', 'V-0217')->first();
        $visitIbrahim = Visit::where('hospital_id', $riverside->id)->where('visit_number', 'V-0215')->first();
        $visitMary = Visit::where('hospital_id', $riverside->id)->where('visit_number', 'V-0209')->first();
        $visitChidi = Visit::where('hospital_id', $riverside->id)->where('visit_number', 'V-0210')->first();

        // 1. Medications Inventory
        $medParacetamol = Medication::firstOrCreate(
            ['hospital_id' => $riverside->id, 'code' => 'MED-0031'],
            [
                'name' => 'Paracetamol',
                'generic_name' => 'Acetaminophen',
                'strength' => '500 mg',
                'form' => 'Tablet',
                'unit_of_measurement' => 'Tablet',
                'quantity' => 420,
                'reorder_level' => 100,
                'expiry_date' => Carbon::createFromDate(2027, 3, 31),
                'batch_number' => 'B-2310',
                'status' => 'available',
            ]
        );

        $medIbuprofen = Medication::firstOrCreate(
            ['hospital_id' => $riverside->id, 'code' => 'MED-0045'],
            [
                'name' => 'Ibuprofen',
                'generic_name' => 'Ibuprofen',
                'strength' => '400 mg',
                'form' => 'Tablet',
                'unit_of_measurement' => 'Tablet',
                'quantity' => 6,
                'reorder_level' => 50,
                'expiry_date' => Carbon::createFromDate(2027, 1, 31),
                'batch_number' => 'B-2311',
                'status' => 'low_stock',
            ]
        );

        $medAmox = Medication::firstOrCreate(
            ['hospital_id' => $riverside->id, 'code' => 'MED-0052'],
            [
                'name' => 'Amoxicillin',
                'generic_name' => 'Amoxicillin trihydrate',
                'strength' => '500 mg',
                'form' => 'Capsule',
                'unit_of_measurement' => 'Capsule',
                'quantity' => 0,
                'reorder_level' => 40,
                'expiry_date' => Carbon::createFromDate(2027, 2, 28),
                'batch_number' => 'B-2312',
                'status' => 'out_of_stock',
            ]
        );

        $medOrs = Medication::firstOrCreate(
            ['hospital_id' => $riverside->id, 'code' => 'MED-0060'],
            [
                'name' => 'Oral rehydration salts',
                'generic_name' => 'Oral Rehydration Salts (WHO Formula)',
                'strength' => 'Sachet',
                'form' => 'Sachet',
                'unit_of_measurement' => 'Sachet',
                'quantity' => 180,
                'reorder_level' => 60,
                'expiry_date' => Carbon::createFromDate(2026, 12, 31),
                'batch_number' => 'B-2313',
                'status' => 'available',
            ]
        );

        $medCough = Medication::firstOrCreate(
            ['hospital_id' => $riverside->id, 'code' => 'MED-0018'],
            [
                'name' => 'Cough syrup',
                'generic_name' => 'Dextromethorphan',
                'strength' => '100 mg/5 ml',
                'form' => 'Syrup',
                'unit_of_measurement' => 'Syrup',
                'quantity' => 24,
                'reorder_level' => 20,
                'expiry_date' => Carbon::createFromDate(2026, 9, 30),
                'batch_number' => 'B-2314',
                'status' => 'expired',
            ]
        );

        $medMetformin = Medication::firstOrCreate(
            ['hospital_id' => $riverside->id, 'code' => 'MED-0072'],
            [
                'name' => 'Metformin',
                'generic_name' => 'Metformin hydrochloride',
                'strength' => '500 mg',
                'form' => 'Tablet',
                'unit_of_measurement' => 'Tablet',
                'quantity' => 200,
                'reorder_level' => 50,
                'expiry_date' => Carbon::createFromDate(2027, 6, 30),
                'batch_number' => 'B-2315',
                'status' => 'available',
            ]
        );

        // 2. Prescriptions
        // RX-0084: Mary Danjuma
        if ($visitMary && $mary) {
            $rx84 = Prescription::firstOrCreate(
                ['hospital_id' => $riverside->id, 'rx_number' => 'RX-0084'],
                [
                    'visit_id' => $visitMary->id,
                    'patient_id' => $mary->id,
                    'prescriber_id' => $doctorEze?->id,
                    'notes' => 'Antenatal pain and fever relief.',
                    'status' => 'new',
                    'created_at' => Carbon::today()->setTime(9, 10),
                ]
            );

            PrescriptionItem::firstOrCreate(
                ['prescription_id' => $rx84->id, 'medication_name' => 'Paracetamol'],
                [
                    'medication_id' => $medParacetamol->id,
                    'strength' => '500 mg',
                    'dosage' => '1 tablet',
                    'route' => 'Oral',
                    'frequency' => 'Three times daily',
                    'duration' => '3 days',
                    'quantity_prescribed' => 9,
                    'instructions' => 'After meals',
                    'status' => 'pending',
                ]
            );

            PrescriptionItem::firstOrCreate(
                ['prescription_id' => $rx84->id, 'medication_name' => 'Ibuprofen'],
                [
                    'medication_id' => $medIbuprofen->id,
                    'strength' => '400 mg',
                    'dosage' => '1 tablet',
                    'route' => 'Oral',
                    'frequency' => 'Twice daily',
                    'duration' => '5 days',
                    'quantity_prescribed' => 10,
                    'instructions' => 'With food',
                    'status' => 'pending',
                ]
            );
        }

        // RX-0087: Amina Yusuf (Clarification Needed - Allergy)
        if ($visitAmina && $amina) {
            $rx87 = Prescription::firstOrCreate(
                ['hospital_id' => $riverside->id, 'rx_number' => 'RX-0087'],
                [
                    'visit_id' => $visitAmina->id,
                    'patient_id' => $amina->id,
                    'prescriber_id' => $doctorBello?->id,
                    'notes' => 'Bacterial coverage for persistent pharyngitis.',
                    'status' => 'clarification_needed',
                    'clarification_note' => 'Patient has a penicillin allergy on record. Please confirm this medication.',
                    'created_at' => Carbon::today()->setTime(10, 0),
                ]
            );

            PrescriptionItem::firstOrCreate(
                ['prescription_id' => $rx87->id, 'medication_name' => 'Amoxicillin'],
                [
                    'medication_id' => $medAmox->id,
                    'strength' => '500 mg',
                    'dosage' => '1 capsule',
                    'route' => 'Oral',
                    'frequency' => 'Three times daily',
                    'duration' => '5 days',
                    'quantity_prescribed' => 15,
                    'instructions' => 'Complete the full course.',
                    'status' => 'pending',
                ]
            );

            RequestMessage::firstOrCreate(
                ['messageable_type' => Prescription::class, 'messageable_id' => $rx87->id],
                [
                    'hospital_id' => $riverside->id,
                    'sender_id' => $pharmacist?->id,
                    'message' => 'The patient has a penicillin allergy on record. Please confirm this medication.',
                    'created_at' => Carbon::today()->setTime(10, 12),
                ]
            );
        }

        // RX-0086: Ibrahim Musa (Processing)
        if ($visitIbrahim && $ibrahim) {
            $rx86 = Prescription::firstOrCreate(
                ['hospital_id' => $riverside->id, 'rx_number' => 'RX-0086'],
                [
                    'visit_id' => $visitIbrahim->id,
                    'patient_id' => $ibrahim->id,
                    'prescriber_id' => $doctorOkon?->id,
                    'status' => 'processing',
                    'accepted_by' => $pharmacist?->id,
                    'created_at' => Carbon::today()->setTime(8, 45),
                ]
            );

            PrescriptionItem::firstOrCreate(
                ['prescription_id' => $rx86->id, 'medication_name' => 'Paracetamol'],
                [
                    'medication_id' => $medParacetamol->id,
                    'strength' => '500 mg',
                    'dosage' => '2 tablets',
                    'route' => 'Oral',
                    'frequency' => 'Three times daily',
                    'duration' => '3 days',
                    'quantity_prescribed' => 18,
                    'instructions' => 'For fever reduction.',
                    'status' => 'pending',
                ]
            );
        }

        // RX-0083: Grace Okoro (Partially Dispensed)
        if ($visitGrace && $grace) {
            Prescription::firstOrCreate(
                ['hospital_id' => $riverside->id, 'rx_number' => 'RX-0083'],
                [
                    'visit_id' => $visitGrace->id,
                    'patient_id' => $grace->id,
                    'prescriber_id' => $doctorBello?->id,
                    'status' => 'partially_dispensed',
                    'accepted_by' => $pharmacist?->id,
                    'created_at' => Carbon::today()->setTime(9, 45),
                ]
            );
        }

        // RX-0085: Ada Nwosu (Dispensed)
        if ($visitAda && $ada) {
            Prescription::firstOrCreate(
                ['hospital_id' => $riverside->id, 'rx_number' => 'RX-0085'],
                [
                    'visit_id' => $visitAda->id,
                    'patient_id' => $ada->id,
                    'prescriber_id' => $doctorOkon?->id,
                    'status' => 'dispensed',
                    'accepted_by' => $pharmacist?->id,
                    'dispensed_by' => $pharmacist?->id,
                    'dispensed_at' => Carbon::today()->setTime(8, 50),
                    'created_at' => Carbon::today()->setTime(8, 30),
                ]
            );
        }

        // RX-0082: Chidi Eze (Accepted)
        if ($visitChidi && $chidi) {
            Prescription::firstOrCreate(
                ['hospital_id' => $riverside->id, 'rx_number' => 'RX-0082'],
                [
                    'visit_id' => $visitChidi->id,
                    'patient_id' => $chidi->id,
                    'prescriber_id' => $doctorBello?->id,
                    'status' => 'accepted',
                    'accepted_by' => $pharmacist?->id,
                    'created_at' => Carbon::yesterday()->setTime(8, 15),
                ]
            );
        }
    }
}

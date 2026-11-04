<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Admission;
use App\Models\BedCategory;
use App\Models\ConsultationNote;
use App\Models\Department;
use App\Models\Hospital;
use App\Models\HospitalService;
use App\Models\LaboratoryRequest;
use App\Models\LaboratoryResult;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\RequestMessage;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Primary Hospital: Riverside General Hospital
        $hospital = Hospital::create([
            'name' => 'Riverside General Hospital',
            'slug' => 'riverside',
            'type' => 'General Hospital',
            'email' => 'info@riverside.example',
            'phone' => '0800 000 0000',
            'country' => 'Nigeria',
            'state' => 'Plateau',
            'lga' => 'Jos North',
            'address' => '14 Marina Road',
            'registration_number' => 'RC-998231',
            'website' => 'https://riverside.example',
            'emergency_status' => 'accepting',
            'location_for_directions' => '14 Marina Road · pin set',
            'warn_unupdated_hours' => 1,
            'mark_stale_hours' => 3,
            'status' => 'active',
            'last_reported_at' => Carbon::now()->subMinutes(3),
        ]);

        // Second Hospital for Finder comparison
        $stAnne = Hospital::create([
            'name' => 'St. Anne Specialist Hospital',
            'slug' => 'st-anne',
            'type' => 'Specialist Hospital',
            'email' => 'contact@st-anne.example',
            'phone' => '0800 111 2222',
            'country' => 'Nigeria',
            'state' => 'Plateau',
            'lga' => 'Jos North',
            'address' => '7 Hill Street',
            'registration_number' => 'RC-884210',
            'website' => 'https://stanne.example',
            'emergency_status' => 'limited',
            'location_for_directions' => '7 Hill Street, Jos North',
            'warn_unupdated_hours' => 1,
            'mark_stale_hours' => 3,
            'status' => 'active',
            'last_reported_at' => Carbon::now()->subMinutes(18),
        ]);

        // 2. Departments for Riverside
        $deptGeneralMed = Department::create([
            'hospital_id' => $hospital->id,
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
            'type' => 'clinical',
            'description' => 'Outpatient consultation and routine internal medicine.',
            'status' => 'active',
        ]);

        $deptReception = Department::create([
            'hospital_id' => $hospital->id,
            'name' => 'Reception',
            'slug' => 'reception',
            'type' => 'administrative',
            'description' => 'Patient triage, registration and queue orchestration.',
            'status' => 'active',
        ]);

        $deptLaboratory = Department::create([
            'hospital_id' => $hospital->id,
            'name' => 'Laboratory',
            'slug' => 'laboratory',
            'type' => 'diagnostic',
            'description' => 'Haematology, clinical chemistry and parasitology diagnostics.',
            'status' => 'active',
        ]);

        $deptPharmacy = Department::create([
            'hospital_id' => $hospital->id,
            'name' => 'Pharmacy',
            'slug' => 'pharmacy',
            'type' => 'support',
            'description' => 'Medication dispensing, inventory tracking and prescription verification.',
            'status' => 'active',
        ]);

        $deptMaternity = Department::create([
            'hospital_id' => $hospital->id,
            'name' => 'Maternity',
            'slug' => 'maternity',
            'type' => 'clinical',
            'description' => 'Ante-natal, labour and post-natal maternal care.',
            'status' => 'active',
        ]);

        $deptSurgery = Department::create([
            'hospital_id' => $hospital->id,
            'name' => 'Surgery',
            'slug' => 'surgery',
            'type' => 'clinical',
            'description' => 'Surgical theatre and recovery.',
            'status' => 'inactive',
        ]);

        $deptEmergency = Department::create([
            'hospital_id' => $hospital->id,
            'name' => 'Emergency',
            'slug' => 'emergency',
            'type' => 'clinical',
            'description' => 'Emergency and critical trauma response.',
            'status' => 'active',
        ]);

        // 3. Staff Users
        $admin = User::create([
            'hospital_id' => $hospital->id,
            'department_id' => null,
            'name' => 'Hannah Okafor',
            'email' => 'hannah@riverside.example',
            'password' => $password,
            'role' => 'hospital_admin',
            'department_username' => 'admin/riverside',
            'phone' => '0803 000 0001',
            'title' => 'Hospital Admin',
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);

        $receptionist = User::create([
            'hospital_id' => $hospital->id,
            'department_id' => $deptReception->id,
            'name' => 'Joy Adeyemi',
            'email' => 'joy@riverside.example',
            'password' => $password,
            'role' => 'reception',
            'department_username' => 'reception/riverside',
            'phone' => '0803 000 0002',
            'title' => 'Reception',
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);
        $deptReception->update(['lead_user_id' => $receptionist->id]);

        $doctorBello = User::create([
            'hospital_id' => $hospital->id,
            'department_id' => $deptGeneralMed->id,
            'name' => 'Dr. Ibrahim Bello',
            'email' => 'bello@riverside.example',
            'password' => $password,
            'role' => 'consultant',
            'department_username' => 'consultant/riverside',
            'phone' => '0803 000 0003',
            'title' => 'Consultant Physician',
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);
        $deptGeneralMed->update(['lead_user_id' => $doctorBello->id]);

        $doctorEze = User::create([
            'hospital_id' => $hospital->id,
            'department_id' => $deptMaternity->id,
            'name' => 'Dr. Ngozi Eze',
            'email' => 'eze@riverside.example',
            'password' => $password,
            'role' => 'consultant',
            'department_username' => 'maternity/riverside',
            'phone' => '0803 000 0004',
            'title' => 'Obstetrician',
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);
        $deptMaternity->update(['lead_user_id' => $doctorEze->id]);

        $doctorOkon = User::create([
            'hospital_id' => $hospital->id,
            'department_id' => $deptGeneralMed->id,
            'name' => 'Dr. Okon',
            'email' => 'okon@riverside.example',
            'password' => $password,
            'role' => 'consultant',
            'department_username' => 'pediatrics/riverside',
            'phone' => '0803 000 0005',
            'title' => 'Paediatrician',
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);

        $labScientist = User::create([
            'hospital_id' => $hospital->id,
            'department_id' => $deptLaboratory->id,
            'name' => 'Samuel Ibe',
            'email' => 'samuel@riverside.example',
            'password' => $password,
            'role' => 'lab_scientist',
            'department_username' => 'laboratory/riverside',
            'phone' => '0803 000 0006',
            'title' => 'Laboratory Scientist',
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);
        $deptLaboratory->update(['lead_user_id' => $labScientist->id]);

        $pharmacist = User::create([
            'hospital_id' => $hospital->id,
            'department_id' => $deptPharmacy->id,
            'name' => 'Peace Umar',
            'email' => 'peace@riverside.example',
            'password' => $password,
            'role' => 'pharmacist',
            'department_username' => 'pharmacy/riverside',
            'phone' => '0803 000 0007',
            'title' => 'Pharmacist',
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);

        User::create([
            'hospital_id' => $hospital->id,
            'department_id' => $deptReception->id,
            'name' => 'Tunde Alabi',
            'email' => 'tunde@riverside.example',
            'password' => $password,
            'role' => 'reception',
            'department_username' => 'tunde/riverside',
            'phone' => '0803 000 0008',
            'title' => 'Reception Assistant',
            'status' => 'deactivated',
            'email_verified_at' => Carbon::now(),
        ]);

        // 4. Bed Categories
        $wardGeneral = BedCategory::create([
            'hospital_id' => $hospital->id,
            'department_id' => $deptGeneralMed->id,
            'name' => 'General Ward',
            'total_beds' => 30,
            'occupied_beds' => 24,
            'out_of_service_beds' => 2,
            'available_beds' => 4,
            'status' => 'limited',
            'last_updated_at' => Carbon::now()->subMinutes(3),
        ]);

        $wardMaternity = BedCategory::create([
            'hospital_id' => $hospital->id,
            'department_id' => $deptMaternity->id,
            'name' => 'Maternity',
            'total_beds' => 12,
            'occupied_beds' => 8,
            'out_of_service_beds' => 1,
            'available_beds' => 3,
            'status' => 'available',
            'last_updated_at' => Carbon::now()->subMinutes(3),
        ]);

        $wardICU = BedCategory::create([
            'hospital_id' => $hospital->id,
            'department_id' => $deptEmergency->id,
            'name' => 'ICU',
            'total_beds' => 6,
            'occupied_beds' => 5,
            'out_of_service_beds' => 0,
            'available_beds' => 1,
            'status' => 'limited',
            'last_updated_at' => Carbon::now()->subMinutes(3),
        ]);

        $wardPaediatric = BedCategory::create([
            'hospital_id' => $hospital->id,
            'department_id' => $deptGeneralMed->id,
            'name' => 'Paediatric',
            'total_beds' => 16,
            'occupied_beds' => 9,
            'out_of_service_beds' => 1,
            'available_beds' => 6,
            'status' => 'available',
            'last_updated_at' => Carbon::now()->subHours(1),
        ]);

        // Bed categories for St. Anne
        BedCategory::create([
            'hospital_id' => $stAnne->id,
            'name' => 'General Ward',
            'total_beds' => 20,
            'occupied_beds' => 20,
            'out_of_service_beds' => 0,
            'available_beds' => 0,
            'status' => 'full',
            'last_updated_at' => Carbon::now()->subMinutes(18),
        ]);

        // 5. Hospital Services
        $services = [
            ['name' => 'Emergency Department', 'dept' => $deptEmergency->id, 'status' => 'available', 'emergency' => true, 'visible' => true],
            ['name' => 'Maternity', 'dept' => $deptMaternity->id, 'status' => 'available', 'emergency' => true, 'visible' => true],
            ['name' => 'General Medicine', 'dept' => $deptGeneralMed->id, 'status' => 'available', 'emergency' => false, 'visible' => true],
            ['name' => 'Surgery', 'dept' => $deptSurgery->id, 'status' => 'unavailable', 'emergency' => true, 'visible' => true],
            ['name' => 'Laboratory', 'dept' => $deptLaboratory->id, 'status' => 'limited', 'emergency' => false, 'visible' => true],
            ['name' => 'Pharmacy', 'dept' => $deptPharmacy->id, 'status' => 'available', 'emergency' => false, 'visible' => false],
            ['name' => 'ICU', 'dept' => $deptEmergency->id, 'status' => 'limited', 'emergency' => true, 'visible' => true],
            ['name' => 'Paediatrics', 'dept' => $deptGeneralMed->id, 'status' => 'unknown', 'emergency' => true, 'visible' => false],
        ];

        foreach ($services as $srv) {
            HospitalService::create([
                'hospital_id' => $hospital->id,
                'department_id' => $srv['dept'],
                'name' => $srv['name'],
                'status' => $srv['status'],
                'is_emergency_capable' => $srv['emergency'],
                'is_public_visible' => $srv['visible'],
                'last_updated_at' => Carbon::now()->subMinutes(3),
            ]);
        }

        // St Anne Services
        HospitalService::create([
            'hospital_id' => $stAnne->id,
            'name' => 'General Emergency',
            'status' => 'available',
            'is_emergency_capable' => true,
            'is_public_visible' => true,
            'last_updated_at' => Carbon::now()->subMinutes(18),
        ]);
        HospitalService::create([
            'hospital_id' => $stAnne->id,
            'name' => 'Surgery',
            'status' => 'available',
            'is_emergency_capable' => true,
            'is_public_visible' => true,
            'last_updated_at' => Carbon::now()->subMinutes(18),
        ]);

        // 6. Patients
        $amina = Patient::create([
            'hospital_id' => $hospital->id,
            'patient_id' => 'MED-000124',
            'first_name' => 'Amina',
            'last_name' => 'Yusuf',
            'date_of_birth' => Carbon::createFromDate(1991, 3, 14),
            'age' => 34,
            'gender' => 'Female',
            'phone' => '0803 000 0124',
            'address' => '24 Rayfield Road, Jos',
            'emergency_contact' => 'Bashir Yusuf · 0802 000 0981',
            'blood_group' => 'O+',
            'genotype' => 'AA',
            'allergies' => 'Penicillin',
            'chronic_conditions' => 'Hypertension',
            'current_medications' => 'Amlodipine 5 mg daily',
            'registered_by' => $receptionist->id,
        ]);

        $ibrahim = Patient::create([
            'hospital_id' => $hospital->id,
            'patient_id' => 'MED-000119',
            'first_name' => 'Ibrahim',
            'last_name' => 'Musa',
            'date_of_birth' => Carbon::createFromDate(1973, 5, 20),
            'age' => 52,
            'gender' => 'Male',
            'phone' => '0803 000 0119',
            'address' => '12 Bukuru Expressway, Jos',
            'emergency_contact' => 'Fatima Musa · 0803 111 2233',
            'blood_group' => 'A+',
            'genotype' => 'AS',
            'allergies' => 'None',
            'chronic_conditions' => 'Asthma',
            'current_medications' => 'Salbutamol inhaler PRN',
            'registered_by' => $receptionist->id,
        ]);

        $mary = Patient::create([
            'hospital_id' => $hospital->id,
            'patient_id' => 'MED-000121',
            'first_name' => 'Mary',
            'last_name' => 'Danjuma',
            'date_of_birth' => Carbon::createFromDate(1998, 8, 12),
            'age' => 28,
            'gender' => 'Female',
            'phone' => '0803 000 0121',
            'address' => '5 Church Street, Anglo Jos',
            'emergency_contact' => 'Solomon Danjuma · 0809 444 5566',
            'blood_group' => 'B+',
            'genotype' => 'AA',
            'allergies' => 'None known',
            'chronic_conditions' => 'Pregnancy (28 weeks)',
            'current_medications' => 'Folic acid, Iron supplements',
            'registered_by' => $receptionist->id,
        ]);

        $grace = Patient::create([
            'hospital_id' => $hospital->id,
            'patient_id' => 'MED-000127',
            'first_name' => 'Grace',
            'last_name' => 'Okoro',
            'date_of_birth' => Carbon::createFromDate(1999, 11, 4),
            'age' => 27,
            'gender' => 'Female',
            'phone' => '0803 000 0127',
            'address' => '19 Lamingo Dam Road, Jos',
            'emergency_contact' => 'David Okoro · 0802 777 8899',
            'blood_group' => 'O+',
            'genotype' => 'AA',
            'allergies' => 'Sulfa drugs',
            'chronic_conditions' => 'None',
            'registered_by' => $receptionist->id,
        ]);

        $ada = Patient::create([
            'hospital_id' => $hospital->id,
            'patient_id' => 'MED-000112',
            'first_name' => 'Ada',
            'last_name' => 'Nwosu',
            'date_of_birth' => Carbon::createFromDate(2018, 2, 1),
            'age' => 8,
            'gender' => 'Female',
            'phone' => '0803 000 0112',
            'address' => '8 Plateau Club Road, Jos',
            'emergency_contact' => 'Chiamaka Nwosu (Mother) · 0805 333 2211',
            'blood_group' => 'O-',
            'genotype' => 'AA',
            'allergies' => 'None',
            'chronic_conditions' => 'None',
            'registered_by' => $receptionist->id,
        ]);

        $chidi = Patient::create([
            'hospital_id' => $hospital->id,
            'patient_id' => 'MED-000098',
            'first_name' => 'Chidi',
            'last_name' => 'Eze',
            'date_of_birth' => Carbon::createFromDate(1982, 7, 19),
            'age' => 44,
            'gender' => 'Male',
            'phone' => '0803 000 0098',
            'address' => '30 Secretariat Road, Jos',
            'emergency_contact' => 'Nkechi Eze · 0806 888 9900',
            'blood_group' => 'A+',
            'genotype' => 'AA',
            'allergies' => 'None',
            'chronic_conditions' => 'Osteoarthritis',
            'registered_by' => $receptionist->id,
        ]);

        // 7. Visits
        $visitAmina = Visit::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $amina->id,
            'department_id' => $deptGeneralMed->id,
            'clinician_id' => $doctorBello->id,
            'visit_number' => 'V-0214',
            'visit_type' => 'Walk-in',
            'reason_for_visit' => 'Fever and headache',
            'priority' => 'Routine',
            'status' => 'in_consultation',
            'arrived_at' => Carbon::today()->setTime(9, 12),
            'created_by' => $receptionist->id,
        ]);

        $visitIbrahim = Visit::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $ibrahim->id,
            'department_id' => $deptGeneralMed->id,
            'clinician_id' => $doctorBello->id,
            'visit_number' => 'V-0215',
            'visit_type' => 'Walk-in',
            'reason_for_visit' => 'Chest pain follow-up',
            'priority' => 'Urgent',
            'status' => 'waiting',
            'arrived_at' => Carbon::today()->setTime(8, 25),
            'created_by' => $receptionist->id,
        ]);

        $visitGrace = Visit::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $grace->id,
            'department_id' => $deptGeneralMed->id,
            'clinician_id' => $doctorBello->id,
            'visit_number' => 'V-0216',
            'visit_type' => 'Walk-in',
            'reason_for_visit' => 'Abdominal pain',
            'priority' => 'Routine',
            'status' => 'waiting',
            'arrived_at' => Carbon::today()->setTime(9, 30),
            'created_by' => $receptionist->id,
        ]);

        $visitAda = Visit::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $ada->id,
            'department_id' => $deptGeneralMed->id,
            'clinician_id' => $doctorOkon->id,
            'visit_number' => 'V-0217',
            'visit_type' => 'Walk-in',
            'reason_for_visit' => 'Cough for 3 days',
            'priority' => 'Routine',
            'status' => 'completed',
            'arrived_at' => Carbon::today()->setTime(8, 5),
            'completed_at' => Carbon::today()->setTime(8, 45),
            'created_by' => $receptionist->id,
        ]);

        $visitMary = Visit::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $mary->id,
            'department_id' => $deptMaternity->id,
            'clinician_id' => $doctorEze->id,
            'visit_number' => 'V-0209',
            'visit_type' => 'Walk-in',
            'reason_for_visit' => 'Routine antenatal checkup',
            'priority' => 'Routine',
            'status' => 'in_consultation',
            'arrived_at' => Carbon::today()->setTime(8, 40),
            'created_by' => $receptionist->id,
        ]);

        $visitChidi = Visit::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $chidi->id,
            'department_id' => $deptGeneralMed->id,
            'clinician_id' => null,
            'visit_number' => 'V-0210',
            'visit_type' => 'Walk-in',
            'reason_for_visit' => 'Severe knee swelling',
            'priority' => 'Routine',
            'status' => 'cancelled',
            'cancellation_reason' => 'Patient left before being seen',
            'arrived_at' => Carbon::today()->setTime(7, 50),
            'created_by' => $receptionist->id,
        ]);

        // 8. Consultation Notes for Amina
        ConsultationNote::create([
            'visit_id' => $visitAmina->id,
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
        ]);

        // 9. Laboratory Requests
        $lrAminaGlucose = LaboratoryRequest::create([
            'hospital_id' => $hospital->id,
            'visit_id' => $visitAmina->id,
            'patient_id' => $amina->id,
            'requester_id' => $doctorBello->id,
            'request_number' => 'LR-0412',
            'category' => 'Chemical Pathology',
            'test_name' => 'Blood glucose',
            'clinical_reason' => 'Fever and headache',
            'priority' => 'Urgent',
            'instructions' => 'Stat random blood sugar evaluation.',
            'status' => 'result_entry',
            'sample_type' => 'Blood',
            'sample_collected_at' => Carbon::today()->setTime(10, 5),
            'sample_collected_by' => $labScientist->id,
        ]);

        $lrGraceUrine = LaboratoryRequest::create([
            'hospital_id' => $hospital->id,
            'visit_id' => $visitGrace->id,
            'patient_id' => $grace->id,
            'requester_id' => $doctorBello->id,
            'request_number' => 'LR-0411',
            'category' => 'Parasitology',
            'test_name' => 'Urinalysis',
            'clinical_reason' => 'Abdominal pain',
            'priority' => 'Routine',
            'instructions' => 'Dipstick and microscopy.',
            'status' => 'new',
            'sample_type' => 'Urine',
        ]);

        $lrIbrahimFbc = LaboratoryRequest::create([
            'hospital_id' => $hospital->id,
            'visit_id' => $visitIbrahim->id,
            'patient_id' => $ibrahim->id,
            'requester_id' => $doctorOkon->id,
            'request_number' => 'LR-0409',
            'category' => 'Haematology',
            'test_name' => 'Full blood count',
            'clinical_reason' => 'Chest pain follow-up',
            'priority' => 'Urgent',
            'instructions' => 'Automated CBC with differential.',
            'status' => 'processing',
            'sample_type' => 'Blood',
            'sample_collected_at' => Carbon::today()->setTime(9, 10),
            'sample_collected_by' => $labScientist->id,
        ]);

        $lrMaryPreg = LaboratoryRequest::create([
            'hospital_id' => $hospital->id,
            'visit_id' => $visitMary->id,
            'patient_id' => $mary->id,
            'requester_id' => $doctorEze->id,
            'request_number' => 'LR-0408',
            'category' => 'General',
            'test_name' => 'Pregnancy test',
            'clinical_reason' => 'Antenatal booking',
            'priority' => 'Routine',
            'instructions' => 'Qualitative serum hCG test.',
            'status' => 'new',
            'sample_type' => 'Blood',
        ]);

        $lrIbrahimMalaria = LaboratoryRequest::create([
            'hospital_id' => $hospital->id,
            'visit_id' => $visitIbrahim->id,
            'patient_id' => $ibrahim->id,
            'requester_id' => $doctorBello->id,
            'request_number' => 'LR-0405',
            'category' => 'Parasitology',
            'test_name' => 'Malaria parasite',
            'clinical_reason' => 'Chills and low grade fever',
            'priority' => 'Routine',
            'instructions' => 'Thick and thin blood film.',
            'status' => 'submitted',
            'sample_type' => 'Blood',
            'sample_collected_at' => Carbon::today()->setTime(8, 40),
            'sample_collected_by' => $labScientist->id,
        ]);

        // Lab results
        LaboratoryResult::create([
            'laboratory_request_id' => $lrIbrahimMalaria->id,
            'entered_by' => $labScientist->id,
            'result_value' => 'Positive (+)',
            'unit' => 'trophozoites/uL',
            'reference_range' => 'Negative',
            'interpretation' => 'Plasmodium falciparum trophozoites seen in moderate density.',
            'lab_notes' => 'Verified on dual Giemsa stained film.',
            'status' => 'finalized',
            'finalized_at' => Carbon::today()->setTime(9, 20),
            'clinical_notes_by_doctor' => 'Confirmed malaria. Commence ACT therapy.',
        ]);

        // 10. Pharmacy Medications
        $medParacetamol = Medication::create([
            'hospital_id' => $hospital->id,
            'code' => 'MED-0031',
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
        ]);

        $medIbuprofen = Medication::create([
            'hospital_id' => $hospital->id,
            'code' => 'MED-0045',
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
        ]);

        $medAmox = Medication::create([
            'hospital_id' => $hospital->id,
            'code' => 'MED-0052',
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
        ]);

        $medOrs = Medication::create([
            'hospital_id' => $hospital->id,
            'code' => 'MED-0060',
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
        ]);

        $medCough = Medication::create([
            'hospital_id' => $hospital->id,
            'code' => 'MED-0018',
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
        ]);

        $medMetformin = Medication::create([
            'hospital_id' => $hospital->id,
            'code' => 'MED-0072',
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
        ]);

        // 11. Prescriptions
        $rx84 = Prescription::create([
            'hospital_id' => $hospital->id,
            'visit_id' => $visitMary->id,
            'patient_id' => $mary->id,
            'prescriber_id' => $doctorEze->id,
            'rx_number' => 'RX-0084',
            'notes' => 'Antenatal pain and fever relief.',
            'status' => 'new',
        ]);

        PrescriptionItem::create([
            'prescription_id' => $rx84->id,
            'medication_id' => $medParacetamol->id,
            'medication_name' => 'Paracetamol',
            'strength' => '500 mg',
            'dosage' => '1 tablet',
            'route' => 'Oral',
            'frequency' => 'Three times daily',
            'duration' => '3 days',
            'quantity_prescribed' => 9,
            'instructions' => 'After meals',
            'status' => 'pending',
        ]);

        PrescriptionItem::create([
            'prescription_id' => $rx84->id,
            'medication_id' => $medIbuprofen->id,
            'medication_name' => 'Ibuprofen',
            'strength' => '400 mg',
            'dosage' => '1 tablet',
            'route' => 'Oral',
            'frequency' => 'Twice daily',
            'duration' => '5 days',
            'quantity_prescribed' => 10,
            'instructions' => 'With food',
            'status' => 'pending',
        ]);

        $rx87 = Prescription::create([
            'hospital_id' => $hospital->id,
            'visit_id' => $visitAmina->id,
            'patient_id' => $amina->id,
            'prescriber_id' => $doctorBello->id,
            'rx_number' => 'RX-0087',
            'notes' => 'Bacterial coverage for persistent pharyngitis.',
            'status' => 'clarification_needed',
            'clarification_note' => 'Patient has a penicillin allergy on record. Please confirm this medication.',
        ]);

        PrescriptionItem::create([
            'prescription_id' => $rx87->id,
            'medication_id' => $medAmox->id,
            'medication_name' => 'Amoxicillin',
            'strength' => '500 mg',
            'dosage' => '1 capsule',
            'route' => 'Oral',
            'frequency' => 'Three times daily',
            'duration' => '5 days',
            'quantity_prescribed' => 15,
            'instructions' => 'Complete the full course.',
            'status' => 'pending',
        ]);

        $rx86 = Prescription::create([
            'hospital_id' => $hospital->id,
            'visit_id' => $visitIbrahim->id,
            'patient_id' => $ibrahim->id,
            'prescriber_id' => $doctorOkon->id,
            'rx_number' => 'RX-0086',
            'status' => 'processing',
            'accepted_by' => $pharmacist->id,
        ]);

        PrescriptionItem::create([
            'prescription_id' => $rx86->id,
            'medication_id' => $medParacetamol->id,
            'medication_name' => 'Paracetamol',
            'strength' => '500 mg',
            'dosage' => '2 tablets',
            'route' => 'Oral',
            'frequency' => 'Three times daily',
            'duration' => '3 days',
            'quantity_prescribed' => 18,
            'instructions' => 'For fever reduction.',
            'status' => 'pending',
        ]);

        $rx83 = Prescription::create([
            'hospital_id' => $hospital->id,
            'visit_id' => $visitGrace->id,
            'patient_id' => $grace->id,
            'prescriber_id' => $doctorBello->id,
            'rx_number' => 'RX-0083',
            'status' => 'partially_dispensed',
            'accepted_by' => $pharmacist->id,
        ]);

        $rx85 = Prescription::create([
            'hospital_id' => $hospital->id,
            'visit_id' => $visitAda->id,
            'patient_id' => $ada->id,
            'prescriber_id' => $doctorOkon->id,
            'rx_number' => 'RX-0085',
            'status' => 'dispensed',
            'accepted_by' => $pharmacist->id,
            'dispensed_by' => $pharmacist->id,
            'dispensed_at' => Carbon::today()->setTime(8, 50),
        ]);

        $rx82 = Prescription::create([
            'hospital_id' => $hospital->id,
            'visit_id' => $visitChidi->id,
            'patient_id' => $chidi->id,
            'prescriber_id' => $doctorBello->id,
            'rx_number' => 'RX-0082',
            'status' => 'accepted',
            'accepted_by' => $pharmacist->id,
        ]);

        // 12. Admissions
        Admission::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $chidi->id,
            'bed_category_id' => $wardGeneral->id,
            'admission_number' => 'MS-00418',
            'admitted_at' => Carbon::yesterday()->setTime(9, 20),
            'status' => 'admitted',
            'notes' => 'Inpatient observation for deep joint effusion.',
            'admitted_by' => $doctorBello->id,
        ]);

        Admission::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $mary->id,
            'bed_category_id' => $wardMaternity->id,
            'admission_number' => 'MS-00397',
            'admitted_at' => Carbon::now()->subDays(2)->setTime(22, 5),
            'status' => 'admitted',
            'notes' => 'Labour ward observation.',
            'admitted_by' => $doctorEze->id,
        ]);

        Admission::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $ibrahim->id,
            'bed_category_id' => $wardICU->id,
            'admission_number' => 'MS-00352',
            'admitted_at' => Carbon::now()->subDays(3)->setTime(14, 10),
            'status' => 'critical_care',
            'notes' => 'Continuous telemetry and oxygen therapy.',
            'admitted_by' => $doctorBello->id,
        ]);

        Admission::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $ada->id,
            'bed_category_id' => $wardPaediatric->id,
            'admission_number' => 'MS-00411',
            'admitted_at' => Carbon::yesterday()->setTime(7, 45),
            'status' => 'admitted',
            'notes' => 'Paediatric recovery post bronchodilator treatment.',
            'admitted_by' => $doctorOkon->id,
        ]);

        // 13. Request Messages
        RequestMessage::create([
            'hospital_id' => $hospital->id,
            'sender_id' => $pharmacist->id,
            'messageable_type' => Prescription::class,
            'messageable_id' => $rx87->id,
            'message' => 'The patient has a penicillin allergy on record. Please confirm this medication.',
            'created_at' => Carbon::today()->setTime(10, 12),
        ]);

        RequestMessage::create([
            'hospital_id' => $hospital->id,
            'sender_id' => $doctorBello->id,
            'messageable_type' => LaboratoryRequest::class,
            'messageable_id' => $lrAminaGlucose->id,
            'message' => 'Please expedite blood glucose result for Amina (LR-0412). Patient is awaiting consultation review.',
            'created_at' => Carbon::today()->setTime(9, 5),
        ]);

        // 14. Activity Logs
        ActivityLog::create([
            'hospital_id' => $hospital->id,
            'user_id' => $admin->id,
            'action' => 'Admission created',
            'description' => 'General Ward · Chidi Eze admitted (MS-00418)',
            'created_at' => Carbon::today()->setTime(10, 42),
        ]);

        ActivityLog::create([
            'hospital_id' => $hospital->id,
            'user_id' => $admin->id,
            'action' => 'Patient discharged',
            'description' => 'Maternity · Bed released',
            'created_at' => Carbon::today()->setTime(10, 31),
        ]);

        ActivityLog::create([
            'hospital_id' => $hospital->id,
            'user_id' => $admin->id,
            'action' => 'Bed capacity updated',
            'description' => 'ICU capacity verified',
            'created_at' => Carbon::today()->setTime(9, 58),
        ]);
    }
}

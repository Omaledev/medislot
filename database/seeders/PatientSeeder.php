<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $riverside = Hospital::where('slug', 'riverside')->first();
        $stAnne = Hospital::where('slug', 'st-anne')->first();
        $receptionist = User::where('email', 'joy@riverside.example')->first();

        if ($riverside) {
            // 1. Amina Yusuf
            Patient::firstOrCreate(
                ['hospital_id' => $riverside->id, 'patient_id' => 'MED-000124'],
                [
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
                    'registered_by' => $receptionist?->id,
                    'created_at' => Carbon::now()->subMonths(1)->setTime(9, 15),
                ]
            );

            // 2. Ibrahim Musa
            Patient::firstOrCreate(
                ['hospital_id' => $riverside->id, 'patient_id' => 'MED-000119'],
                [
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
                    'registered_by' => $receptionist?->id,
                    'created_at' => Carbon::now()->subMonths(2)->setTime(11, 20),
                ]
            );

            // 3. Mary Danjuma
            Patient::firstOrCreate(
                ['hospital_id' => $riverside->id, 'patient_id' => 'MED-000121'],
                [
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
                    'registered_by' => $receptionist?->id,
                    'created_at' => Carbon::now()->subWeeks(3)->setTime(8, 45),
                ]
            );

            // 4. Grace Okoro
            Patient::firstOrCreate(
                ['hospital_id' => $riverside->id, 'patient_id' => 'MED-000127'],
                [
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
                    'registered_by' => $receptionist?->id,
                    'created_at' => Carbon::now()->subWeeks(2)->setTime(14, 10),
                ]
            );

            // 5. Ada Nwosu
            Patient::firstOrCreate(
                ['hospital_id' => $riverside->id, 'patient_id' => 'MED-000112'],
                [
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
                    'registered_by' => $receptionist?->id,
                    'created_at' => Carbon::now()->subDays(10)->setTime(10, 0),
                ]
            );

            // 6. Chidi Eze
            Patient::firstOrCreate(
                ['hospital_id' => $riverside->id, 'patient_id' => 'MED-000098'],
                [
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
                    'registered_by' => $receptionist?->id,
                    'created_at' => Carbon::now()->subDays(5)->setTime(7, 30),
                ]
            );

            // 7. Fatima Garba
            Patient::firstOrCreate(
                ['hospital_id' => $riverside->id, 'patient_id' => 'MED-000130'],
                [
                    'first_name' => 'Fatima',
                    'last_name' => 'Garba',
                    'date_of_birth' => Carbon::createFromDate(1995, 6, 18),
                    'age' => 31,
                    'gender' => 'Female',
                    'phone' => '0803 000 0130',
                    'address' => '15 Bauchi Road, Jos',
                    'emergency_contact' => 'Abubakar Garba · 0803 555 6677',
                    'blood_group' => 'B+',
                    'genotype' => 'AA',
                    'allergies' => 'None',
                    'chronic_conditions' => 'None',
                    'registered_by' => $receptionist?->id,
                    'created_at' => Carbon::yesterday()->setTime(11, 45),
                ]
            );

            // 8. Emmanuel Yakubu
            Patient::firstOrCreate(
                ['hospital_id' => $riverside->id, 'patient_id' => 'MED-000135'],
                [
                    'first_name' => 'Emmanuel',
                    'last_name' => 'Yakubu',
                    'date_of_birth' => Carbon::createFromDate(1965, 12, 1),
                    'age' => 61,
                    'gender' => 'Male',
                    'phone' => '0803 000 0135',
                    'address' => '44 Tudun Wada, Jos',
                    'emergency_contact' => 'Ruth Yakubu · 0802 333 4455',
                    'blood_group' => 'O+',
                    'genotype' => 'AS',
                    'allergies' => 'Aspirin',
                    'chronic_conditions' => 'Type 2 Diabetes, Hypertension',
                    'current_medications' => 'Metformin 500mg, Lisinopril 10mg',
                    'registered_by' => $receptionist?->id,
                    'created_at' => Carbon::now()->subDays(3)->setTime(8, 20),
                ]
            );

            // 9. Blessing Adeleke
            Patient::firstOrCreate(
                ['hospital_id' => $riverside->id, 'patient_id' => 'MED-000140'],
                [
                    'first_name' => 'Blessing',
                    'last_name' => 'Adeleke',
                    'date_of_birth' => Carbon::createFromDate(2004, 4, 25),
                    'age' => 22,
                    'gender' => 'Female',
                    'phone' => '0803 000 0140',
                    'address' => '10 Dogon Dutse, Jos',
                    'emergency_contact' => 'Folake Adeleke · 0807 888 1122',
                    'blood_group' => 'A-',
                    'genotype' => 'AA',
                    'allergies' => 'None',
                    'chronic_conditions' => 'None',
                    'registered_by' => $receptionist?->id,
                    'created_at' => Carbon::today()->setTime(9, 50),
                ]
            );
        }

        // St Anne Patients
        if ($stAnne) {
            Patient::firstOrCreate(
                ['hospital_id' => $stAnne->id, 'patient_id' => 'STA-00101'],
                [
                    'first_name' => 'Kenneth',
                    'last_name' => 'Bamidele',
                    'date_of_birth' => Carbon::createFromDate(1989, 9, 15),
                    'age' => 37,
                    'gender' => 'Male',
                    'phone' => '0803 777 8888',
                    'address' => '11 Ahmadu Bello Way, Jos',
                    'emergency_contact' => 'Sandra Bamidele · 0803 111 9999',
                    'blood_group' => 'O+',
                    'genotype' => 'AA',
                    'allergies' => 'None',
                    'chronic_conditions' => 'None',
                    'created_at' => Carbon::now()->subDays(7),
                ]
            );
        }
    }
}

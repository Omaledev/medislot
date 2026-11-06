<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Hospital;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');
        $riverside = Hospital::where('slug', 'riverside')->first();
        $stAnne = Hospital::where('slug', 'st-anne')->first();
        $apex = Hospital::where('slug', 'apex-specialist-hospital')->first();
        $cedarcrest = Hospital::where('slug', 'cedarcrest-abuja')->first();

        if ($riverside) {
            $deptGeneralMed = Department::where('hospital_id', $riverside->id)->where('slug', 'general-medicine')->first();
            $deptReception = Department::where('hospital_id', $riverside->id)->where('slug', 'reception')->first();
            $deptLaboratory = Department::where('hospital_id', $riverside->id)->where('slug', 'laboratory')->first();
            $deptPharmacy = Department::where('hospital_id', $riverside->id)->where('slug', 'pharmacy')->first();
            $deptMaternity = Department::where('hospital_id', $riverside->id)->where('slug', 'maternity')->first();

            // 1. Riverside Hospital Admin
            User::firstOrCreate(
                ['email' => 'hannah@riverside.example'],
                [
                    'hospital_id' => $riverside->id,
                    'department_id' => null,
                    'name' => 'Hannah Okafor',
                    'password' => $password,
                    'role' => 'hospital_admin',
                    'department_username' => 'admin/riverside',
                    'phone' => '0803 000 0001',
                    'title' => 'Hospital Admin',
                    'status' => 'active',
                    'email_verified_at' => Carbon::now()->subMonths(4),
                    'created_at' => Carbon::now()->subMonths(4),
                ]
            );

            // 2. Receptionist
            $receptionist = User::firstOrCreate(
                ['email' => 'joy@riverside.example'],
                [
                    'hospital_id' => $riverside->id,
                    'department_id' => $deptReception?->id,
                    'name' => 'Joy Adeyemi',
                    'password' => $password,
                    'role' => 'reception',
                    'department_username' => 'reception/riverside',
                    'phone' => '0803 000 0002',
                    'title' => 'Reception Supervisor',
                    'status' => 'active',
                    'email_verified_at' => Carbon::now()->subMonths(4),
                    'created_at' => Carbon::now()->subMonths(4),
                ]
            );
            $deptReception?->update(['lead_user_id' => $receptionist->id]);

            // 3. Consultant Physician
            $doctorBello = User::firstOrCreate(
                ['email' => 'bello@riverside.example'],
                [
                    'hospital_id' => $riverside->id,
                    'department_id' => $deptGeneralMed?->id,
                    'name' => 'Dr. Ibrahim Bello',
                    'password' => $password,
                    'role' => 'consultant',
                    'department_username' => 'consultant/riverside',
                    'phone' => '0803 000 0003',
                    'title' => 'Consultant Physician',
                    'status' => 'active',
                    'email_verified_at' => Carbon::now()->subMonths(4),
                    'created_at' => Carbon::now()->subMonths(4),
                ]
            );
            $deptGeneralMed?->update(['lead_user_id' => $doctorBello->id]);

            // 4. Obstetrician
            $doctorEze = User::firstOrCreate(
                ['email' => 'eze@riverside.example'],
                [
                    'hospital_id' => $riverside->id,
                    'department_id' => $deptMaternity?->id,
                    'name' => 'Dr. Ngozi Eze',
                    'password' => $password,
                    'role' => 'consultant',
                    'department_username' => 'maternity/riverside',
                    'phone' => '0803 000 0004',
                    'title' => 'Consultant Obstetrician',
                    'status' => 'active',
                    'email_verified_at' => Carbon::now()->subMonths(3),
                    'created_at' => Carbon::now()->subMonths(3),
                ]
            );
            $deptMaternity?->update(['lead_user_id' => $doctorEze->id]);

            // 5. Paediatrician
            User::firstOrCreate(
                ['email' => 'okon@riverside.example'],
                [
                    'hospital_id' => $riverside->id,
                    'department_id' => $deptGeneralMed?->id,
                    'name' => 'Dr. Okon',
                    'password' => $password,
                    'role' => 'consultant',
                    'department_username' => 'pediatrics/riverside',
                    'phone' => '0803 000 0005',
                    'title' => 'Paediatrician',
                    'status' => 'active',
                    'email_verified_at' => Carbon::now()->subMonths(3),
                    'created_at' => Carbon::now()->subMonths(3),
                ]
            );

            // 6. Laboratory Scientist
            $labScientist = User::firstOrCreate(
                ['email' => 'samuel@riverside.example'],
                [
                    'hospital_id' => $riverside->id,
                    'department_id' => $deptLaboratory?->id,
                    'name' => 'Samuel Ibe',
                    'password' => $password,
                    'role' => 'lab_scientist',
                    'department_username' => 'laboratory/riverside',
                    'phone' => '0803 000 0006',
                    'title' => 'Chief Laboratory Scientist',
                    'status' => 'active',
                    'email_verified_at' => Carbon::now()->subMonths(4),
                    'created_at' => Carbon::now()->subMonths(4),
                ]
            );
            $deptLaboratory?->update(['lead_user_id' => $labScientist->id]);

            // 7. Pharmacist
            $pharmacist = User::firstOrCreate(
                ['email' => 'peace@riverside.example'],
                [
                    'hospital_id' => $riverside->id,
                    'department_id' => $deptPharmacy?->id,
                    'name' => 'Peace Umar',
                    'password' => $password,
                    'role' => 'pharmacist',
                    'department_username' => 'pharmacy/riverside',
                    'phone' => '0803 000 0007',
                    'title' => 'Head of Pharmacy',
                    'status' => 'active',
                    'email_verified_at' => Carbon::now()->subMonths(4),
                    'created_at' => Carbon::now()->subMonths(4),
                ]
            );
            $deptPharmacy?->update(['lead_user_id' => $pharmacist->id]);

            // 8. Deactivated Reception Assistant
            User::firstOrCreate(
                ['email' => 'tunde@riverside.example'],
                [
                    'hospital_id' => $riverside->id,
                    'department_id' => $deptReception?->id,
                    'name' => 'Tunde Alabi',
                    'password' => $password,
                    'role' => 'reception',
                    'department_username' => 'tunde/riverside',
                    'phone' => '0803 000 0008',
                    'title' => 'Reception Assistant',
                    'status' => 'deactivated',
                    'email_verified_at' => Carbon::now()->subMonths(3),
                    'created_at' => Carbon::now()->subMonths(3),
                ]
            );
        }

        // St Anne Users
        if ($stAnne) {
            User::firstOrCreate(
                ['email' => 'admin@st-anne.example'],
                [
                    'hospital_id' => $stAnne->id,
                    'name' => 'Dr. Patrick Danladi',
                    'password' => $password,
                    'role' => 'hospital_admin',
                    'department_username' => 'admin/st-anne',
                    'phone' => '+234 802 111 2233',
                    'title' => 'Medical Director',
                    'status' => 'active',
                    'email_verified_at' => Carbon::now()->subMonths(2),
                    'created_at' => Carbon::now()->subMonths(2),
                ]
            );

            User::firstOrCreate(
                ['email' => 'reception@st-anne.example'],
                [
                    'hospital_id' => $stAnne->id,
                    'name' => 'Fatima Bello',
                    'password' => $password,
                    'role' => 'reception',
                    'department_username' => 'reception/st-anne',
                    'phone' => '+234 802 222 3344',
                    'title' => 'Receptionist',
                    'status' => 'active',
                    'email_verified_at' => Carbon::now()->subMonths(2),
                    'created_at' => Carbon::now()->subMonths(2),
                ]
            );
        }

        // Apex Specialist Users
        if ($apex) {
            User::firstOrCreate(
                ['email' => 'admin@apexspecialist.org'],
                [
                    'hospital_id' => $apex->id,
                    'name' => 'Dr. Joshua Pam',
                    'password' => $password,
                    'role' => 'hospital_admin',
                    'department_username' => 'admin/apex',
                    'phone' => '+234 803 777 9999',
                    'title' => 'Chief Medical Officer',
                    'status' => 'active',
                    'email_verified_at' => Carbon::now()->subWeeks(6),
                    'created_at' => Carbon::now()->subWeeks(6),
                ]
            );
        }

        // Cedarcrest Users
        if ($cedarcrest) {
            User::firstOrCreate(
                ['email' => 'admin@cedarcrest.example'],
                [
                    'hospital_id' => $cedarcrest->id,
                    'name' => 'Dr. Felix Alao',
                    'password' => $password,
                    'role' => 'hospital_admin',
                    'department_username' => 'admin/cedarcrest',
                    'phone' => '+234 809 333 4444',
                    'title' => 'Hospital Administrator',
                    'status' => 'active',
                    'email_verified_at' => Carbon::now()->subMonths(1),
                    'created_at' => Carbon::now()->subMonths(1),
                ]
            );
        }
    }
}

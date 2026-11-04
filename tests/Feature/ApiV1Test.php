<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\BedCategory;
use App\Models\Department;
use App\Models\HospitalService;
use App\Models\LaboratoryRequest;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_swagger_documentation_page_renders(): void
    {
        $this->get('/api/v1/documentation')->assertStatus(200);
        $this->get('/api/v1/docs')->assertStatus(200)
            ->assertJsonPath('info.title', 'MediSlot HMS & Finder REST API v1');

        $this->get('/api/documentation')->assertRedirect('/api/v1/documentation');
        $this->get('/docs')->assertRedirect('/api/v1/docs');
    }

    public function test_auth_forgot_and_reset_password_via_api(): void
    {
        $forgotResponse = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'hannah@riverside.example',
        ]);

        $forgotResponse->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $token = $forgotResponse->json('token');
        $this->assertNotEmpty($token);

        $resetResponse = $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'hannah@riverside.example',
            'token' => $token,
            'password' => 'new-secret-password-123',
            'password_confirmation' => 'new-secret-password-123',
        ]);

        $resetResponse->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'hannah@riverside.example',
            'password' => 'new-secret-password-123',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);
    }

    public function test_auth_change_password_via_api(): void
    {
        $user = User::where('email', 'hannah@riverside.example')->first();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/auth/change-password', [
            'current_password' => 'password',
            'password' => 'sanctum-updated-password-123',
            'password_confirmation' => 'sanctum-updated-password-123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'hannah@riverside.example',
            'password' => 'sanctum-updated-password-123',
        ]);

        $loginResponse->assertStatus(200);
    }

    public function test_finder_hospitals_api_returns_paginated_list(): void
    {
        $response = $this->getJson('/api/v1/finder/hospitals');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'state', 'lga'],
                ],
            ]);
    }

    public function test_finder_hospital_detail_api_returns_hospital(): void
    {
        $response = $this->getJson('/api/v1/finder/hospitals/riverside');

        $response->assertStatus(200)
            ->assertJsonPath('data.slug', 'riverside')
            ->assertJsonPath('data.name', 'Riverside General Hospital');
    }

    public function test_auth_login_with_email_returns_token_and_user(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'hannah@riverside.example',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email', 'role'],
            ]);
    }

    public function test_auth_department_login_with_handle_returns_token(): void
    {
        $response = $this->postJson('/api/v1/auth/department-login', [
            'department_username' => 'reception/riverside',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.role', 'reception');
    }

    public function test_auth_me_returns_authenticated_user(): void
    {
        $user = User::where('email', 'hannah@riverside.example')->first();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('user.email', 'hannah@riverside.example');
    }

    public function test_admin_endpoints_require_admin_role(): void
    {
        $reception = User::where('role', 'reception')->first();

        $response = $this->actingAs($reception, 'sanctum')->getJson('/api/v1/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_dashboard_returns_metrics_for_admin(): void
    {
        $admin = User::where('role', 'hospital_admin')->first();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/admin/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'stats' => ['departments_count', 'staff_count', 'total_beds', 'available_beds'],
                'bed_availability',
                'needs_attention',
                'recent_activity',
            ]);
    }

    public function test_reception_can_register_patient_and_start_visit(): void
    {
        $reception = User::where('role', 'reception')->first();
        $hospital = $reception->hospital;
        $department = $hospital->departments()->first();

        // Register Patient
        $patientResponse = $this->actingAs($reception, 'sanctum')->postJson('/api/v1/reception/patients', [
            'first_name' => 'Fatima',
            'last_name' => 'Garba',
            'date_of_birth' => '1995-04-12',
            'gender' => 'Female',
            'phone' => '+2348039876543',
        ]);

        $patientResponse->assertStatus(201)
            ->assertJsonPath('data.first_name', 'Fatima');

        $patientId = $patientResponse->json('data.id');

        // Start Visit
        $visitResponse = $this->actingAs($reception, 'sanctum')->postJson('/api/v1/reception/visits', [
            'patient_id' => $patientId,
            'department_id' => $department->id,
            'visit_type' => 'Walk-in',
            'reason_for_visit' => 'Persistent migraine',
            'priority' => 'Urgent',
        ]);

        $visitResponse->assertStatus(201)
            ->assertJsonPath('data.priority', 'Urgent')
            ->assertJsonPath('data.status', 'waiting');
    }

    public function test_consultant_can_record_notes_order_labs_and_prescribe(): void
    {
        $consultant = User::where('role', 'consultant')->first();
        $visit = Visit::where('hospital_id', $consultant->hospital_id)->first();

        // Save Notes
        $notesResponse = $this->actingAs($consultant, 'sanctum')
            ->postJson("/api/v1/consultant/workspace/{$visit->id}/notes", [
                'chief_complaint' => 'Headache and fever',
                'assessment_diagnosis' => 'Clinical malaria',
                'treatment_plan' => 'Oral ACT and hydration',
                'status' => 'finalized',
            ]);

        $notesResponse->assertStatus(200)
            ->assertJsonPath('data.assessment_diagnosis', 'Clinical malaria');

        // Order Lab Request
        $labResponse = $this->actingAs($consultant, 'sanctum')
            ->postJson("/api/v1/consultant/workspace/{$visit->id}/lab-requests", [
                'category' => 'Parasitology',
                'test_name' => 'Malaria Parasite (MP) Film',
                'priority' => 'Urgent',
            ]);

        $labResponse->assertStatus(201)
            ->assertJsonPath('data.test_name', 'Malaria Parasite (MP) Film');

        // Prescribe Medication
        $rxResponse = $this->actingAs($consultant, 'sanctum')
            ->postJson("/api/v1/consultant/workspace/{$visit->id}/prescriptions", [
                'items' => [
                    [
                        'medication_name' => 'Artemether + Lumefantrine',
                        'dosage' => '4 tablets',
                        'frequency' => 'BD',
                        'duration' => '3 days',
                        'quantity_prescribed' => 24,
                    ],
                ],
            ]);

        $rxResponse->assertStatus(201)
            ->assertJsonPath('data.items.0.medication_name', 'Artemether + Lumefantrine');
    }

    public function test_laboratory_can_update_status_and_submit_results(): void
    {
        $labTech = User::where('role', 'lab_scientist')->first();
        $request = LaboratoryRequest::where('hospital_id', $labTech->hospital_id)->first();

        // Advance Status
        $statusResponse = $this->actingAs($labTech, 'sanctum')
            ->putJson("/api/v1/lab/requests/{$request->id}/status", [
                'status' => 'sample_collected',
                'sample_type' => 'Venous Blood',
            ]);

        $statusResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'sample_collected');

        // Submit Results
        $resultResponse = $this->actingAs($labTech, 'sanctum')
            ->postJson("/api/v1/lab/requests/{$request->id}/results", [
                'result_value' => '3,400 /uL',
                'unit' => '/uL',
                'interpretation' => 'Moderate trophozoites',
                'status' => 'finalized',
            ]);

        $resultResponse->assertStatus(200)
            ->assertJsonPath('data.result.result_value', '3,400 /uL');
    }

    public function test_pharmacy_can_manage_inventory_and_dispense(): void
    {
        $pharmacist = User::where('role', 'pharmacist')->first();

        // Add inventory
        $medResponse = $this->actingAs($pharmacist, 'sanctum')
            ->postJson('/api/v1/pharmacy/inventory', [
                'name' => 'Ciprofloxacin',
                'generic_name' => 'Ciprofloxacin Hydrochloride',
                'strength' => '500mg',
                'form' => 'Tablet',
                'unit_of_measurement' => 'tablets',
                'quantity' => 250,
                'reorder_level' => 30,
            ]);

        $medResponse->assertStatus(201)
            ->assertJsonPath('data.name', 'Ciprofloxacin');

        // Dispense prescription
        $prescription = Prescription::where('hospital_id', $pharmacist->hospital_id)->first();

        $dispenseResponse = $this->actingAs($pharmacist, 'sanctum')
            ->putJson("/api/v1/pharmacy/prescriptions/{$prescription->id}/dispense");

        $dispenseResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'dispensed');
    }

    public function test_auth_register_hospital_and_logout_api(): void
    {
        // 1. Register hospital
        $regResponse = $this->postJson('/api/v1/auth/register-hospital', [
            'hospital_name' => 'St. Luke Specialist Center',
            'hospital_type' => 'Specialist Hospital',
            'hospital_email' => 'contact@stluke.example',
            'hospital_phone' => '+2348030009988',
            'state' => 'Kaduna',
            'lga' => 'Kaduna North',
            'hospital_address' => '44 Independence Way',
            'admin_first_name' => 'Luke',
            'admin_last_name' => 'Bello',
            'admin_email' => 'admin@stluke.example',
            'admin_phone' => '+2348030009989',
            'admin_password' => 'secure-pass-1234',
        ]);

        $regResponse->assertStatus(201)
            ->assertJsonPath('hospital.name', 'St. Luke Specialist Center')
            ->assertJsonStructure(['token', 'user', 'hospital']);

        // 2. Logout
        $token = $regResponse->json('token');
        $logoutResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertStatus(200)
            ->assertJsonPath('message', 'Logged out successfully');
    }

    public function test_admin_crud_departments_and_staff_api(): void
    {
        $admin = User::where('role', 'hospital_admin')->first();

        // List departments
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/departments')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);

        // Create department
        $createDeptResponse = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/departments', [
                'name' => 'Radiology',
                'type' => 'diagnostic',
                'status' => 'active',
            ]);

        $createDeptResponse->assertStatus(201)
            ->assertJsonPath('data.name', 'Radiology');

        $deptId = $createDeptResponse->json('data.id');

        // Toggle department status
        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/admin/departments/{$deptId}/toggle")
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'inactive');

        // List staff
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/staff')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);

        // Create staff
        $createStaffResponse = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/staff', [
                'name' => 'Dr. Jane Smith',
                'email' => 'jane.smith@riverside.example',
                'role' => 'consultant',
                'department_id' => $deptId,
                'title' => 'Radiologist',
                'phone' => '+2348011122233',
                'password' => 'secure-password-123',
            ]);

        $createStaffResponse->assertStatus(201)
            ->assertJsonPath('data.name', 'Dr. Jane Smith')
            ->assertJsonPath('data.role', 'consultant');
    }

    public function test_admin_beds_admissions_services_settings_activity_api(): void
    {
        $admin = User::where('role', 'hospital_admin')->first();
        $bedCategory = BedCategory::where('hospital_id', $admin->hospital_id)->first();
        $admission = Admission::where('hospital_id', $admin->hospital_id)->first();
        $service = HospitalService::where('hospital_id', $admin->hospital_id)->first();

        // Beds list & update
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/beds')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/admin/beds/{$bedCategory->id}", [
                'total_beds' => 30,
                'out_of_service_beds' => 2,
            ])
            ->assertStatus(200)
            ->assertJsonPath('data.total_beds', 30);

        // Admissions list & discharge
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/admissions')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/admin/admissions/{$admission->id}/discharge")
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'discharged');

        // Services list & update
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/services')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/admin/services/{$service->id}", [
                'status' => 'limited',
                'is_emergency_capable' => true,
                'is_public_visible' => true,
            ])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'limited');

        // Settings get & update
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/settings')
            ->assertStatus(200)
            ->assertJsonStructure(['hospital']);

        $this->actingAs($admin, 'sanctum')
            ->putJson('/api/v1/admin/settings', [
                'name' => 'Riverside Advanced General Hospital',
                'address' => 'Plot 100 Hospital Road, Jos',
                'phone' => '+2347012345678',
                'email' => 'info@riverside.test',
                'emergency_status' => 'accepting',
                'warn_unupdated_hours' => 2,
                'mark_stale_hours' => 4,
            ])
            ->assertStatus(200)
            ->assertJsonPath('hospital.name', 'Riverside Advanced General Hospital');

        // Activity log
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/activity')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_reception_endpoints_api(): void
    {
        $reception = User::where('role', 'reception')->first();
        $patient = Patient::where('hospital_id', $reception->hospital_id)->first();
        $visit = Visit::where('hospital_id', $reception->hospital_id)->first();

        // Reception dashboard
        $this->actingAs($reception, 'sanctum')
            ->getJson('/api/v1/reception/dashboard')
            ->assertStatus(200)
            ->assertJsonStructure(['stats', 'queue', 'beds']);

        // Reception patients list & detail
        $this->actingAs($reception, 'sanctum')
            ->getJson('/api/v1/reception/patients')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);

        $this->actingAs($reception, 'sanctum')
            ->getJson("/api/v1/reception/patients/{$patient->id}")
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'visits']);

        // Reception visits list
        $this->actingAs($reception, 'sanctum')
            ->getJson('/api/v1/reception/visits?today=false')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);

        // Cancel visit
        $this->actingAs($reception, 'sanctum')
            ->putJson("/api/v1/reception/visits/{$visit->id}/cancel", [
                'reason' => 'Patient had to reschedule',
            ])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled');
    }

    public function test_consultant_endpoints_api(): void
    {
        $consultant = User::where('role', 'consultant')->first();
        $visit = Visit::where('hospital_id', $consultant->hospital_id)->first();

        // Dashboard
        $this->actingAs($consultant, 'sanctum')
            ->getJson('/api/v1/consultant/dashboard')
            ->assertStatus(200)
            ->assertJsonStructure(['stats', 'queue']);

        // Workspace
        $this->actingAs($consultant, 'sanctum')
            ->getJson("/api/v1/consultant/workspace/{$visit->id}")
            ->assertStatus(200)
            ->assertJsonStructure(['visit', 'patient', 'lab_requests', 'prescriptions']);
    }

    public function test_laboratory_endpoints_api(): void
    {
        $labTech = User::where('role', 'lab_scientist')->first();
        $request = LaboratoryRequest::where('hospital_id', $labTech->hospital_id)->first();

        // Lab dashboard
        $this->actingAs($labTech, 'sanctum')
            ->getJson('/api/v1/lab/dashboard')
            ->assertStatus(200)
            ->assertJsonStructure(['stats', 'requests']);

        // Lab requests list
        $this->actingAs($labTech, 'sanctum')
            ->getJson('/api/v1/lab/requests')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);

        // Lab request detail
        $this->actingAs($labTech, 'sanctum')
            ->getJson("/api/v1/lab/requests/{$request->id}")
            ->assertStatus(200)
            ->assertJsonStructure(['data']);

        // Lab reports
        $this->actingAs($labTech, 'sanctum')
            ->getJson('/api/v1/lab/reports')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_pharmacy_endpoints_and_clarification_api(): void
    {
        $pharmacist = User::where('role', 'pharmacist')->first();
        $prescription = Prescription::where('hospital_id', $pharmacist->hospital_id)->first();

        // Pharmacy dashboard
        $this->actingAs($pharmacist, 'sanctum')
            ->getJson('/api/v1/pharmacy/dashboard')
            ->assertStatus(200)
            ->assertJsonStructure(['stats', 'recent_prescriptions']);

        // Prescriptions list
        $this->actingAs($pharmacist, 'sanctum')
            ->getJson('/api/v1/pharmacy/prescriptions')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);

        // Prescription detail
        $this->actingAs($pharmacist, 'sanctum')
            ->getJson("/api/v1/pharmacy/prescriptions/{$prescription->id}")
            ->assertStatus(200)
            ->assertJsonStructure(['data']);

        // Accept prescription
        $this->actingAs($pharmacist, 'sanctum')
            ->putJson("/api/v1/pharmacy/prescriptions/{$prescription->id}/accept")
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'accepted');

        // Begin dispensing
        $this->actingAs($pharmacist, 'sanctum')
            ->putJson("/api/v1/pharmacy/prescriptions/{$prescription->id}/begin")
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'processing');

        // Clarify prescription
        $this->actingAs($pharmacist, 'sanctum')
            ->postJson("/api/v1/pharmacy/prescriptions/{$prescription->id}/clarify", [
                'note' => 'Please confirm unit dosage with clinician',
            ])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'clarification_needed');
    }

    public function test_messages_endpoints_api(): void
    {
        $consultant = User::where('role', 'consultant')->first();
        $prescription = Prescription::where('hospital_id', $consultant->hospital_id)->first();

        // Post message
        $sendResponse = $this->actingAs($consultant, 'sanctum')
            ->postJson('/api/v1/messages', [
                'messageable_type' => 'prescription',
                'messageable_id' => $prescription->id,
                'message' => 'Medication approved as specified in titration note.',
            ]);

        $sendResponse->assertStatus(201)
            ->assertJsonPath('message', 'Message sent successfully');

        // List messages
        $listResponse = $this->actingAs($consultant, 'sanctum')
            ->getJson('/api/v1/messages');

        $listResponse->assertStatus(200)
            ->assertJsonStructure(['data']);
    }
}

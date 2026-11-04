<?php

namespace Tests\Feature;

use App\Models\LaboratoryRequest;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WebPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_pages_render_successfully(): void
    {
        $this->get('/')->assertStatus(200)->assertSeeText('MediSlot');
        $this->get('/finder')->assertStatus(200)->assertSeeText('Find care. Know where to go.');
        $this->get('/finder/results')->assertStatus(200)->assertSeeText('Riverside General Hospital');
        $this->get('/finder/hospitals/riverside')->assertStatus(200)->assertSeeText('Riverside General Hospital');
    }

    public function test_auth_pages_render_successfully(): void
    {
        $this->get('/login')->assertStatus(200)->assertSeeText('Sign In');
        $this->get('/department/login')->assertStatus(200)->assertSeeText('Department Sign In');
        $this->get('/register')->assertStatus(200)->assertSeeText('Better Hospital Management Starts Here');

        $this->withSession([
            'register.step1' => [
                'hospital_name' => 'Apex Health',
                'hospital_type' => 'Specialist Hospital',
                'state' => 'Plateau',
                'lga' => 'Jos North',
                'address' => '5 Market Road',
            ],
        ])->get('/register/administrator')->assertStatus(200)->assertSeeText('Account Administrator');

        $this->withSession([
            'register.step1' => [
                'hospital_name' => 'Apex Health',
                'hospital_type' => 'Specialist Hospital',
                'state' => 'Plateau',
                'lga' => 'Jos North',
                'address' => '5 Market Road',
            ],
            'register.step2' => [
                'first_name' => 'Daniel',
                'last_name' => 'Okafor',
                'email' => 'daniel@apex.example',
                'phone' => '08021112233',
            ],
        ])->get('/register/review')->assertStatus(200)->assertSeeText('Apex Health');

        $this->get('/forgot-password')->assertStatus(200)->assertSeeText('Forgot Password?');
    }

    public function test_unauthenticated_users_redirected_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/reception/dashboard')->assertRedirect('/login');
        $this->get('/consultant/dashboard')->assertRedirect('/login');
        $this->get('/lab/dashboard')->assertRedirect('/login');
        $this->get('/pharmacy/dashboard')->assertRedirect('/login');
    }

    public function test_role_based_access_control(): void
    {
        $receptionist = User::where('role', 'reception')->first();

        // Reception cannot access Admin portal
        $this->actingAs($receptionist)->get('/admin/dashboard')->assertStatus(403);
    }

    public function test_admin_portal_views_render(): void
    {
        $admin = User::where('role', 'hospital_admin')->first();

        $this->actingAs($admin)->get('/admin/dashboard')->assertStatus(200)->assertSeeText('Hannah Okafor');
        $this->actingAs($admin)->get('/admin/departments')->assertStatus(200)->assertSeeText('General Medicine');
        $this->actingAs($admin)->get('/admin/staff')->assertStatus(200)->assertSeeText('Joy Adeyemi');
        $this->actingAs($admin)->get('/admin/beds')->assertStatus(200)->assertSeeText('General Ward');
        $this->actingAs($admin)->get('/admin/admissions')->assertStatus(200)->assertSeeText('Admissions');
        $this->actingAs($admin)->get('/admin/services')->assertStatus(200)->assertSeeText('Emergency Department');
        $this->actingAs($admin)->get('/admin/settings')->assertStatus(200)->assertSeeText('Hospital Settings');
        $this->actingAs($admin)->get('/admin/activity')->assertStatus(200)->assertSeeText('Hospital Activity Log');
    }

    public function test_reception_portal_views_render(): void
    {
        $receptionist = User::where('role', 'reception')->first();

        $this->actingAs($receptionist)->get('/reception/dashboard')->assertStatus(200)->assertSeeText('Reception desk');
        $this->actingAs($receptionist)->get('/reception/patients')->assertStatus(200)->assertSeeText('Amina Yusuf');
        $this->actingAs($receptionist)->get('/reception/patients/create')->assertStatus(200)->assertSeeText('Register patient');
        $this->actingAs($receptionist)->get('/reception/queue')->assertStatus(200)->assertSeeText("Today's queue");
        $this->actingAs($receptionist)->get('/reception/beds')->assertStatus(200)->assertSeeText('Bed availability');
    }

    public function test_consultant_portal_views_render(): void
    {
        $consultant = User::where('role', 'consultant')->first();
        $visit = Visit::where('hospital_id', $consultant->hospital_id)->first();

        $this->actingAs($consultant)->get('/consultant/dashboard')->assertStatus(200)->assertSeeText('Consultant workspace');
        $this->actingAs($consultant)->get('/consultant/patients')->assertStatus(200)->assertSeeText('My Patients');
        $this->actingAs($consultant)->get("/consultant/workspace/{$visit->id}")->assertStatus(200)->assertSeeText('Patient Workspace');
        $this->actingAs($consultant)->get('/consultant/history')->assertStatus(200)->assertSeeText('Consultation History');
        $this->actingAs($consultant)->get('/consultant/lab-requests')->assertStatus(200)->assertSeeText('Laboratory Requests');
        $this->actingAs($consultant)->get('/consultant/prescriptions')->assertStatus(200)->assertSeeText('Prescriptions');
        $this->actingAs($consultant)->get('/consultant/messages')->assertStatus(200)->assertSeeText('Clinical Messages');
        $this->actingAs($consultant)->get('/consultant/profile')->assertStatus(200)->assertSeeText('Doctor Profile');
    }

    public function test_laboratory_portal_views_render(): void
    {
        $labTech = User::where('role', 'lab_scientist')->first();
        $request = LaboratoryRequest::where('hospital_id', $labTech->hospital_id)->first();

        $this->actingAs($labTech)->get('/lab/dashboard')->assertStatus(200)->assertSeeText('Laboratory Dashboard');
        $this->actingAs($labTech)->get('/lab/requests')->assertStatus(200)->assertSeeText('Laboratory Requests');
        $this->actingAs($labTech)->get("/lab/requests/{$request->id}")->assertStatus(200)->assertSeeText('Diagnostic Result Entry');
        $this->actingAs($labTech)->get('/lab/samples')->assertStatus(200)->assertSeeText('Specimen Processing Pipeline');
        $this->actingAs($labTech)->get('/lab/reports')->assertStatus(200)->assertSeeText('Results & Diagnostic Reports');
        $this->actingAs($labTech)->get('/lab/messages')->assertStatus(200)->assertSeeText('Laboratory Communications');
        $this->actingAs($labTech)->get('/lab/profile')->assertStatus(200)->assertSeeText('Laboratory Department Profile');
    }

    public function test_pharmacy_portal_views_render(): void
    {
        $pharmacist = User::where('role', 'pharmacist')->first();
        $prescription = Prescription::where('hospital_id', $pharmacist->hospital_id)->first();

        $this->actingAs($pharmacist)->get('/pharmacy/dashboard')->assertStatus(200)->assertSeeText('Pharmacy Operations');
        $this->actingAs($pharmacist)->get('/pharmacy/prescriptions')->assertStatus(200)->assertSeeText('Prescription Orders');
        $this->actingAs($pharmacist)->get("/pharmacy/prescriptions/{$prescription->id}")->assertStatus(200)->assertSeeText('Prescribed Items & Regimen');
        $this->actingAs($pharmacist)->get('/pharmacy/dispensing')->assertStatus(200)->assertSeeText('Dispensing Queue');
        $this->actingAs($pharmacist)->get('/pharmacy/dispensing-history')->assertStatus(200)->assertSeeText('Dispensing History');
        $this->actingAs($pharmacist)->get('/pharmacy/inventory')->assertStatus(200)->assertSeeText('Medication Inventory');
        $this->actingAs($pharmacist)->get('/pharmacy/inventory/create')->assertStatus(200)->assertSeeText('Add New Medication to Inventory');
        $this->actingAs($pharmacist)->get('/pharmacy/messages')->assertStatus(200)->assertSeeText('Pharmacy Messages');
        $this->actingAs($pharmacist)->get('/pharmacy/profile')->assertStatus(200)->assertSeeText('Pharmacist Profile');
    }

    public function test_web_forgot_and_reset_password_workflow(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'hannah@riverside.example',
        ]);

        $response->assertRedirect('/forgot-password/sent');

        $this->get('/forgot-password/sent')
            ->assertStatus(200)
            ->assertSeeText('Reset Link Sent');

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', 'hannah@riverside.example')
            ->first();

        $this->assertNotNull($resetRecord);

        // View reset form
        $this->get("/reset-password/{$resetRecord->token}?email=hannah@riverside.example")
            ->assertStatus(200)
            ->assertSeeText('Reset Password');

        // Post new password
        $resetPost = $this->post('/reset-password', [
            'token' => $resetRecord->token,
            'email' => 'hannah@riverside.example',
            'password' => 'new-web-secret-999',
            'password_confirmation' => 'new-web-secret-999',
        ]);

        $resetPost->assertRedirect('/login')
            ->assertSessionHas('success');

        // Login with new password
        $loginPost = $this->post('/login', [
            'email' => 'hannah@riverside.example',
            'password' => 'new-web-secret-999',
        ]);

        $loginPost->assertRedirect('/admin/dashboard');
    }

    public function test_web_department_change_password(): void
    {
        $receptionist = User::where('role', 'reception')->first();

        $this->actingAs($receptionist)
            ->get('/department/change-password')
            ->assertStatus(200)
            ->assertSeeText('Change Department Password');

        $response = $this->actingAs($receptionist)
            ->post('/department/change-password', [
                'current_password' => 'password',
                'password' => 'new-dept-pass-456',
                'password_confirmation' => 'new-dept-pass-456',
            ]);

        $response->assertRedirect('/reception/dashboard')
            ->assertSessionHas('success');
    }
}

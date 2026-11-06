<?php

namespace Tests\Feature;

use App\Models\LaboratoryRequest;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewViewsAndApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_about_app_and_contact_views_render(): void
    {
        $this->get('/')->assertStatus(200)
            ->assertSeeText('Find the Right Care. Without the Guesswork.')
            ->assertSeeText('Register Hospital')
            ->assertSee('aria-label="Toggle navigation menu"', false)
            ->assertSee(route('register'))
            ->assertSee(route('finder'));

        $this->get('/about')->assertStatus(200)->assertSeeText('About MediSlot');
        $this->get('/app')->assertStatus(200)->assertSeeText('The MediSlot app');
        $this->get('/contact')->assertStatus(200)->assertSeeText('Contact MediSlot');

        $response = $this->post('/contact', [
            'fullName' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'Partnership inquiry',
            'message' => 'We would love to connect with your team.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_public_contact_api_endpoint(): void
    {
        $response = $this->postJson('/api/v1/public/contact', [
            'fullName' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Hospital setup question',
            'message' => 'Please provide more details on onboarding.',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Your message has been sent. We will be in touch soon.']);
    }

    public function test_verify_email_view_renders(): void
    {
        $response = $this->withSession([
            'register.step1' => ['hospital_name' => 'Test Hospital'],
            'register.step2' => ['first_name' => 'Ada', 'last_name' => 'Okafor', 'email' => 'ada@example.com'],
        ])->get('/register/verify-email');

        $response->assertStatus(200)->assertSeeText('Verify Your Email');
    }

    public function test_reception_edit_patient_and_settings_workflow(): void
    {
        $receptionist = User::where('role', 'reception')->first();
        $patient = Patient::where('hospital_id', $receptionist->hospital_id)->first();

        $this->actingAs($receptionist)
            ->get("/reception/patients/{$patient->id}/edit")
            ->assertStatus(200)
            ->assertSeeText('Edit patient details');

        $updateResp = $this->actingAs($receptionist)->put("/reception/patients/{$patient->id}", [
            'first_name' => 'UpdatedFirst',
            'last_name' => 'UpdatedLast',
            'gender' => 'Female',
            'phone' => '08099998888',
        ]);

        $updateResp->assertRedirect();
        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'first_name' => 'UpdatedFirst',
            'last_name' => 'UpdatedLast',
        ]);

        $this->actingAs($receptionist)
            ->get('/reception/settings')
            ->assertStatus(200)
            ->assertSeeText('Reception settings');

        $settingsResp = $this->actingAs($receptionist)->post('/reception/settings', [
            'name' => 'Reception Desk Lead',
            'email' => 'reception_updated@riverside.example',
            'phone' => '08011112222',
        ]);

        $settingsResp->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $receptionist->id,
            'name' => 'Reception Desk Lead',
        ]);
    }

    public function test_reception_api_updates_patient_and_settings(): void
    {
        $receptionist = User::where('role', 'reception')->first();
        $patient = Patient::where('hospital_id', $receptionist->hospital_id)->first();

        $this->actingAs($receptionist, 'sanctum')
            ->patchJson("/api/v1/reception/patients/{$patient->id}", [
                'first_name' => 'ApiUpdatedName',
            ])
            ->assertStatus(200)
            ->assertJsonPath('data.first_name', 'ApiUpdatedName');

        $this->actingAs($receptionist, 'sanctum')
            ->putJson('/api/v1/reception/settings', [
                'name' => 'New Receptionist Name',
            ])
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'New Receptionist Name');
    }

    public function test_laboratory_report_view_and_amendment(): void
    {
        $labTech = User::where('role', 'lab_scientist')->first();
        $request = LaboratoryRequest::where('hospital_id', $labTech->hospital_id)->first();

        $this->actingAs($labTech)
            ->get("/lab/reports/{$request->id}")
            ->assertStatus(200)
            ->assertSeeText('Report ·');

        $amendResp = $this->actingAs($labTech)->post("/lab/requests/{$request->id}/amend", [
            'amendment_reason' => 'Re-tested on secondary analyzer',
            'corrected_value' => '14.2 g/dL',
        ]);

        $amendResp->assertRedirect();

        // API amendment
        $this->actingAs($labTech, 'sanctum')
            ->postJson("/api/v1/lab/requests/{$request->id}/amend", [
                'amendment_reason' => 'Confirmed via API',
                'corrected_value' => '14.3 g/dL',
            ])
            ->assertStatus(200);
    }

    public function test_pharmacy_inventory_management_and_dispensing(): void
    {
        $pharmacist = User::where('role', 'pharmacist')->first();
        $medication = Medication::where('hospital_id', $pharmacist->hospital_id)->first();
        $prescription = Prescription::where('hospital_id', $pharmacist->hospital_id)->first();

        $this->actingAs($pharmacist)
            ->get("/pharmacy/inventory/{$medication->id}")
            ->assertStatus(200)
            ->assertSeeText($medication->name);

        $initialQty = $medication->quantity;

        // Receive stock
        $this->actingAs($pharmacist)
            ->post("/pharmacy/inventory/{$medication->id}/receive-stock", [
                'quantity_received' => 50,
                'batch_number' => 'B-NEW-99',
            ])
            ->assertRedirect();

        $this->assertEquals($initialQty + 50, $medication->fresh()->quantity);

        // Adjust stock
        $this->actingAs($pharmacist)
            ->post("/pharmacy/inventory/{$medication->id}/adjust-stock", [
                'adjustment_amount' => -10,
                'reason' => 'Damaged',
            ])
            ->assertRedirect();

        $this->assertEquals($initialQty + 40, $medication->fresh()->quantity);

        // Dispensing details view
        $this->actingAs($pharmacist)
            ->get("/pharmacy/dispensing/{$prescription->id}")
            ->assertStatus(200)
            ->assertSeeText('Dispense');
    }

    public function test_profile_updates_for_all_departments(): void
    {
        $consultant = User::where('role', 'consultant')->first();
        $labTech = User::where('role', 'lab_scientist')->first();
        $pharmacist = User::where('role', 'pharmacist')->first();
        $admin = User::where('role', 'hospital_admin')->first();

        Storage::fake('public');
        $fakePhoto = UploadedFile::fake()->image('profile.jpg', 300, 300);

        $this->actingAs($admin)->get('/admin/profile')->assertStatus(200)->assertSeeText('Administrator Profile');
        $this->actingAs($admin)->put('/admin/profile', [
            'name' => 'Updated Hospital Admin',
            'email' => 'admin_updated@riverside.example',
            'title' => 'Chief Executive Officer',
            'avatar' => $fakePhoto,
        ])->assertRedirect();

        $this->assertNotNull($admin->fresh()->avatar);
        Storage::disk('public')->assertExists($admin->fresh()->avatar);

        $this->actingAs($consultant)->put('/consultant/profile', [
            'name' => 'Dr. Updated Consultant',
            'email' => 'consultant_updated@riverside.example',
            'title' => 'Chief of Medicine',
        ])->assertRedirect();

        $this->actingAs($labTech)->put('/lab/profile', [
            'name' => 'Updated Lab Scientist',
            'email' => 'lab_updated@riverside.example',
            'title' => 'Senior Laboratory Officer',
        ])->assertRedirect();

        $this->actingAs($pharmacist)->put('/pharmacy/profile', [
            'name' => 'Updated Pharmacist',
            'email' => 'pharmacy_updated@riverside.example',
            'title' => 'Head of Pharmacy',
        ])->assertRedirect();

        $this->assertEquals('Updated Hospital Admin', $admin->fresh()->name);
        $this->assertEquals('Dr. Updated Consultant', $consultant->fresh()->name);
        $this->assertEquals('Updated Lab Scientist', $labTech->fresh()->name);
        $this->assertEquals('Updated Pharmacist', $pharmacist->fresh()->name);
    }

    public function test_openapi_documentation_contains_new_and_corrected_endpoints(): void
    {
        $this->get('/api/v1/documentation')->assertStatus(200);

        $specResponse = $this->get('/api/v1/docs');
        $specResponse->assertStatus(200);

        $spec = $specResponse->json();
        $this->assertIsArray($spec);
        $this->assertArrayHasKey('paths', $spec);

        $expectedEndpoints = [
            '/api/v1/public/contact',
            '/api/v1/reception/patients/{patient}',
            '/api/v1/reception/visits/{visit}',
            '/api/v1/reception/settings',
            '/api/v1/consultant/profile',
            '/api/v1/lab/requests/{laboratoryRequest}',
            '/api/v1/lab/requests/{laboratoryRequest}/amend',
            '/api/v1/lab/requests/{laboratoryRequest}/attachments',
            '/api/v1/lab/profile',
            '/api/v1/pharmacy/inventory/{medication}',
            '/api/v1/pharmacy/inventory/{medication}/receive-stock',
            '/api/v1/pharmacy/inventory/{medication}/adjust-stock',
            '/api/v1/pharmacy/profile',
            '/api/v1/integrations/status',
            '/api/v1/integrations/adt/admit',
            '/api/v1/integrations/adt/discharge',
            '/api/v1/integrations/adt/transfer',
            '/api/v1/integrations/beds/sync',
            '/api/v1/integrations/beds/allocate',
        ];

        foreach ($expectedEndpoints as $path) {
            $this->assertArrayHasKey($path, $spec['paths'], "Expected OpenAPI path [{$path}] is missing from documentation spec.");
        }
    }
}

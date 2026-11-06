<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $hospitalAdmin;

    protected Hospital $hospital;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->hospital = Hospital::first();

        $this->superAdmin = User::create([
            'hospital_id' => null,
            'name' => 'Platform Lead',
            'email' => 'lead@medislot.test',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->hospitalAdmin = User::where('role', 'hospital_admin')->first();
    }

    public function test_guest_is_redirected_from_superadmin(): void
    {
        $response = $this->get('/superadmin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_hospital_admin_is_forbidden_from_superadmin(): void
    {
        $response = $this->actingAs($this->hospitalAdmin)->get('/superadmin/dashboard');
        $response->assertStatus(403);
    }

    public function test_superadmin_can_access_dashboard_with_kpis(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/superadmin/dashboard');

        $response->assertStatus(200);
        $response->assertSeeText('Platform Command Center');
        $response->assertSeeText('Hospital Directory');
        $response->assertSeeText('Live Platform Activity');
    }

    public function test_superadmin_can_view_hospitals_directory_and_filter(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/superadmin/hospitals');

        $response->assertStatus(200);
        $response->assertSeeText('Hospital Directory');
        $response->assertSeeText($this->hospital->name);

        $searchResponse = $this->actingAs($this->superAdmin)->get('/superadmin/hospitals?q='.$this->hospital->name);
        $searchResponse->assertStatus(200);
        $searchResponse->assertSeeText($this->hospital->name);
    }

    public function test_superadmin_can_inspect_hospital_and_update_status(): void
    {
        $response = $this->actingAs($this->superAdmin)->get("/superadmin/hospitals/{$this->hospital->id}");

        $response->assertStatus(200);
        $response->assertSeeText($this->hospital->name);
        $response->assertSeeText('Facility Metadata');

        // Update status to inactive
        $updateResponse = $this->actingAs($this->superAdmin)->post("/superadmin/hospitals/{$this->hospital->id}/status", [
            'status' => 'inactive',
        ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('hospitals', [
            'id' => $this->hospital->id,
            'status' => 'inactive',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'hospital_id' => $this->hospital->id,
            'action' => 'hospital_status_changed',
        ]);
    }

    public function test_superadmin_can_view_global_activity_stream(): void
    {
        ActivityLog::create([
            'hospital_id' => $this->hospital->id,
            'user_id' => $this->hospitalAdmin->id,
            'action' => 'global_audit_test',
            'description' => 'A unique test activity description for global stream',
        ]);

        $response = $this->actingAs($this->superAdmin)->get('/superadmin/activity');

        $response->assertStatus(200);
        $response->assertSeeText('Global Platform Activity');
        $response->assertSeeText('A unique test activity description for global stream');
    }

    public function test_superadmin_can_view_integrations_monitor(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/superadmin/integrations');

        $response->assertStatus(200);
        $response->assertSeeText('EHR & External Hospital Integrations');
        $response->assertSeeText('Live MediSlot Connect Webhook Receivers');
    }

    public function test_superadmin_can_create_new_platform_operator(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/superadmin/operators', [
            'name' => 'Sarah Operations',
            'email' => 'sarah@medislot.test',
            'phone' => '+234 812 345 6789',
            'password' => 'SecurePass123!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'sarah@medislot.test',
            'role' => 'superadmin',
            'hospital_id' => null,
        ]);
    }
}

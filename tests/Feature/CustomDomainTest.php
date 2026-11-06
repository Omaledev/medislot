<?php

namespace Tests\Feature;

use App\Models\Hospital;
use App\Models\User;
use App\Services\CustomDomainService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomDomainTest extends TestCase
{
    use RefreshDatabase;

    protected Hospital $hospital;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->hospital = Hospital::first();
        $this->admin = User::where('hospital_id', $this->hospital->id)
            ->where('role', 'hospital_admin')
            ->first();
    }

    public function test_tenant_resolution_via_custom_domain(): void
    {
        $this->hospital->update([
            'custom_domain' => 'his.riversidetest.org',
            'custom_domain_status' => 'active',
            'custom_domain_verified_at' => now(),
        ]);

        $response = $this->get('http://his.riversidetest.org/login');

        $response->assertStatus(200);
        $response->assertSeeText($this->hospital->name);
    }

    public function test_tenant_resolution_via_subdomain(): void
    {
        $subdomainHost = "{$this->hospital->slug}.medislot.test";

        $response = $this->get("http://{$subdomainHost}/login");

        $response->assertStatus(200);
        $response->assertSeeText($this->hospital->name);
    }

    public function test_central_domain_renders_standard_view(): void
    {
        $response = $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Find the Right Care');
    }

    public function test_custom_domain_service_normalization(): void
    {
        $service = new CustomDomainService;

        $this->assertEquals('his.riverside.org', $service->normalizeDomain('https://his.riverside.org/'));
        $this->assertEquals('his.riverside.org', $service->normalizeDomain('http://HIS.RIVERSIDE.ORG:8000/path'));
        $this->assertTrue($service->isValidDomain('his.riversideclinic.org'));
        $this->assertFalse($service->isValidDomain('localhost'));
        $this->assertFalse($service->isValidDomain('medislot.com'));
    }

    public function test_admin_can_save_and_normalize_custom_domain(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/settings', [
            'name' => $this->hospital->name,
            'address' => $this->hospital->address,
            'phone' => $this->hospital->phone,
            'email' => $this->hospital->email,
            'emergency_status' => 'accepting',
            'warn_unupdated_hours' => 1,
            'mark_stale_hours' => 2,
            'custom_domain' => 'https://Portal.RiversideHealth.org/',
        ]);

        $response->assertRedirect();
        $this->hospital->refresh();

        $this->assertEquals('portal.riversidehealth.org', $this->hospital->custom_domain);
        $this->assertEquals('pending', $this->hospital->custom_domain_status);
        $this->assertNull($this->hospital->custom_domain_verified_at);
    }

    public function test_admin_cannot_claim_already_taken_domain(): void
    {
        $otherHospital = Hospital::where('id', '!=', $this->hospital->id)->first();
        $otherHospital->update(['custom_domain' => 'his.apex.org']);

        $response = $this->actingAs($this->admin)->post('/admin/settings', [
            'name' => $this->hospital->name,
            'address' => $this->hospital->address,
            'phone' => $this->hospital->phone,
            'email' => $this->hospital->email,
            'emergency_status' => 'accepting',
            'warn_unupdated_hours' => 1,
            'mark_stale_hours' => 2,
            'custom_domain' => 'his.apex.org',
        ]);

        $response->assertSessionHasErrors(['custom_domain']);
    }

    public function test_custom_domain_dns_verification_workflow(): void
    {
        $this->hospital->update([
            'custom_domain' => 'his.riversidetest.org',
            'custom_domain_status' => 'pending',
        ]);

        // 1. Verify Domain
        $verifyResponse = $this->actingAs($this->admin)->post('/admin/settings/domain/verify');
        $verifyResponse->assertRedirect();
        $verifyResponse->assertSessionHas('success');

        $this->hospital->refresh();
        $this->assertEquals('active', $this->hospital->custom_domain_status);
        $this->assertNotNull($this->hospital->custom_domain_verified_at);
        $this->assertTrue($this->hospital->isCustomDomainVerified());

        // 2. Disconnect Domain
        $deleteResponse = $this->actingAs($this->admin)->delete('/admin/settings/domain');
        $deleteResponse->assertRedirect();

        $this->hospital->refresh();
        $this->assertNull($this->hospital->custom_domain);
        $this->assertNull($this->hospital->custom_domain_verified_at);
    }
}

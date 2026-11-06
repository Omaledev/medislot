<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Hospital;
use App\Models\HospitalApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HospitalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected Hospital $hospital;

    protected string $apiKeyToken;

    protected HospitalApiKey $apiKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->hospital = Hospital::first();

        $result = HospitalApiKey::generate($this->hospital, 'Production EHR Webhook');
        $this->apiKey = $result['key'];
        $this->apiKeyToken = $result['plainTextToken'];
    }

    public function test_requires_valid_hospital_api_key(): void
    {
        $responseNoKey = $this->getJson('/api/v1/integrations/status');
        $responseNoKey->assertStatus(401)
            ->assertJson(['message' => 'Hospital API key is missing. Please provide via X-Hospital-Key or Authorization Bearer header.']);

        $responseInvalidKey = $this->withHeaders(['X-Hospital-Key' => 'ms_live_invalidtoken12345'])
            ->getJson('/api/v1/integrations/status');
        $responseInvalidKey->assertStatus(401)
            ->assertJson(['message' => 'Invalid or expired hospital API key.']);
    }

    public function test_integration_status_returns_hospital_and_ward_capacities(): void
    {
        $response = $this->withHeaders(['X-Hospital-Key' => $this->apiKeyToken])
            ->getJson('/api/v1/integrations/status');

        $response->assertStatus(200)
            ->assertJsonPath('hospital.id', $this->hospital->id)
            ->assertJsonPath('api_key.name', 'Production EHR Webhook')
            ->assertJsonStructure([
                'hospital' => ['id', 'name', 'total_beds', 'occupied_beds', 'available_beds'],
                'api_key' => ['name', 'prefix'],
                'wards',
            ]);
    }

    public function test_adt_admit_creates_admission_and_decrements_available_beds(): void
    {
        $bedCategory = $this->hospital->bedCategories()->first();
        $bedCategory->update([
            'external_code' => 'WARD_EHR_01',
            'total_beds' => 10,
            'occupied_beds' => 2,
            'out_of_service_beds' => 0,
        ]);
        $bedCategory->recalculateAvailability();

        $this->assertEquals(8, $bedCategory->fresh()->available_beds);

        $response = $this->withHeaders(['X-Hospital-Key' => $this->apiKeyToken])
            ->postJson('/api/v1/integrations/adt/admit', [
                'ward_code' => 'WARD_EHR_01',
                'external_admission_id' => 'EHR-ADM-9999',
                'external_patient_id' => 'EHR-PT-501',
                'patient_name' => 'Grace Hopper',
                'patient_gender' => 'Female',
                'notes' => 'Direct ER transfer',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('admission.external_admission_id', 'EHR-ADM-9999')
            ->assertJsonPath('admission.source', 'integration')
            ->assertJsonPath('ward.occupied_beds', 3)
            ->assertJsonPath('ward.available_beds', 7);

        $this->assertEquals(7, $bedCategory->fresh()->available_beds);
        $this->assertEquals(3, $bedCategory->fresh()->occupied_beds);

        $this->assertDatabaseHas('admissions', [
            'hospital_id' => $this->hospital->id,
            'external_admission_id' => 'EHR-ADM-9999',
            'status' => 'admitted',
            'source' => 'integration',
        ]);
    }

    public function test_adt_discharge_frees_bed(): void
    {
        $bedCategory = $this->hospital->bedCategories()->first();
        $bedCategory->update([
            'external_code' => 'WARD_EHR_02',
            'total_beds' => 10,
            'occupied_beds' => 3,
            'out_of_service_beds' => 0,
        ]);
        $bedCategory->recalculateAvailability();

        $patient = $this->hospital->patients()->first();

        $admission = Admission::create([
            'hospital_id' => $this->hospital->id,
            'patient_id' => $patient->id,
            'bed_category_id' => $bedCategory->id,
            'admission_number' => 'EHR-ADM-8888',
            'external_admission_id' => 'EHR-ADM-8888',
            'admitted_at' => now()->subDays(2),
            'status' => 'admitted',
            'source' => 'integration',
        ]);

        $response = $this->withHeaders(['X-Hospital-Key' => $this->apiKeyToken])
            ->postJson('/api/v1/integrations/adt/discharge', [
                'external_admission_id' => 'EHR-ADM-8888',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('admission.status', 'discharged')
            ->assertJsonPath('ward.occupied_beds', 2)
            ->assertJsonPath('ward.available_beds', 8);

        $this->assertEquals('discharged', $admission->fresh()->status);
        $this->assertEquals(8, $bedCategory->fresh()->available_beds);
    }

    public function test_adt_transfer_moves_patient_between_wards(): void
    {
        $wards = $this->hospital->bedCategories()->take(2)->get();
        $wardA = $wards[0];
        $wardB = $wards[1];

        $wardA->update(['external_code' => 'WARD_A', 'total_beds' => 10, 'occupied_beds' => 5]);
        $wardA->recalculateAvailability();

        $wardB->update(['external_code' => 'WARD_B', 'total_beds' => 10, 'occupied_beds' => 2]);
        $wardB->recalculateAvailability();

        $patient = $this->hospital->patients()->first();

        $admission = Admission::create([
            'hospital_id' => $this->hospital->id,
            'patient_id' => $patient->id,
            'bed_category_id' => $wardA->id,
            'admission_number' => 'EHR-ADM-7777',
            'external_admission_id' => 'EHR-ADM-7777',
            'admitted_at' => now()->subDays(1),
            'status' => 'admitted',
            'source' => 'integration',
        ]);

        $response = $this->withHeaders(['X-Hospital-Key' => $this->apiKeyToken])
            ->postJson('/api/v1/integrations/adt/transfer', [
                'external_admission_id' => 'EHR-ADM-7777',
                'to_ward_code' => 'WARD_B',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('from_ward.occupied_beds', 4)
            ->assertJsonPath('to_ward.occupied_beds', 3);

        $this->assertEquals($wardB->id, $admission->fresh()->bed_category_id);
    }

    public function test_bulk_snapshot_sync(): void
    {
        $bedCategory = $this->hospital->bedCategories()->first();
        $bedCategory->update(['external_code' => 'WARD_SYNC_01']);

        $response = $this->withHeaders(['X-Hospital-Key' => $this->apiKeyToken])
            ->putJson('/api/v1/integrations/beds/sync', [
                'wards' => [
                    [
                        'ward_code' => 'WARD_SYNC_01',
                        'total_beds' => 25,
                        'occupied_beds' => 20,
                        'out_of_service_beds' => 1,
                    ],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('synced_count', 1);

        $fresh = $bedCategory->fresh();
        $this->assertEquals(25, $fresh->total_beds);
        $this->assertEquals(20, $fresh->occupied_beds);
        $this->assertEquals(4, $fresh->available_beds);
    }

    public function test_smart_allocation_engine_reserves_bed(): void
    {
        $icu = $this->hospital->bedCategories()->where('name', 'like', '%ICU%')->first();
        $icu->update(['total_beds' => 5, 'occupied_beds' => 2]);
        $icu->recalculateAvailability();

        $response = $this->withHeaders(['X-Hospital-Key' => $this->apiKeyToken])
            ->postJson('/api/v1/integrations/beds/allocate', [
                'acuity' => 'critical',
                'gender' => 'female',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('allocated', true)
            ->assertJsonPath('bed_category.id', $icu->id)
            ->assertJsonStructure([
                'allocated',
                'hold_token',
                'expires_at',
                'bed_category' => ['id', 'name', 'available_beds', 'total_beds'],
                'instructions',
            ]);
    }

    public function test_admin_portal_integrations_workflow(): void
    {
        $admin = User::where('hospital_id', $this->hospital->id)
            ->where('role', 'hospital_admin')
            ->first();

        // 1. Visit Integrations Page
        $this->actingAs($admin)
            ->get('/admin/integrations')
            ->assertStatus(200)
            ->assertSeeText('EHR & External Hospital Integration')
            ->assertSeeText('Hospital Integration Keys');

        // 2. Generate New API Key
        $genResponse = $this->actingAs($admin)
            ->post('/admin/integrations/keys', [
                'name' => 'Cerner Production Connector',
            ]);

        $genResponse->assertRedirect();
        $genResponse->assertSessionHas('plainTextApiKey');
        $this->assertDatabaseHas('hospital_api_keys', [
            'hospital_id' => $this->hospital->id,
            'name' => 'Cerner Production Connector',
        ]);

        $createdKey = HospitalApiKey::where('name', 'Cerner Production Connector')->first();

        // 3. Update Ward Code Mappings
        $bed = $this->hospital->bedCategories()->first();
        $mapResponse = $this->actingAs($admin)
            ->put('/admin/integrations/wards', [
                'mappings' => [
                    $bed->id => 'CERNER_WARD_A',
                ],
            ]);

        $mapResponse->assertRedirect();
        $this->assertEquals('CERNER_WARD_A', $bed->fresh()->external_code);

        // 4. Revoke API Key
        $revokeResponse = $this->actingAs($admin)
            ->delete("/admin/integrations/keys/{$createdKey->id}");

        $revokeResponse->assertRedirect();
        $this->assertDatabaseMissing('hospital_api_keys', ['id' => $createdKey->id]);
    }
}

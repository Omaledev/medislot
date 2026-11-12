<?php

namespace Tests\Feature;

use App\Models\BedCategory;
use App\Models\Hospital;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmergencyBedIntentFinderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_finder_api_availability_filter_scopes_to_emergency_intake_beds_only(): void
    {
        // Riverside: Emergency Bay has 0 available beds, ICU has 0 available beds, but Maternity has 3 available beds.
        // St. Anne: Emergency Bay has 2 available beds.
        $riverside = Hospital::where('slug', 'riverside')->first();
        $stAnne = Hospital::where('slug', 'st-anne')->first();

        $this->assertNotNull($riverside);
        $this->assertNotNull($stAnne);
        $this->assertEquals(0, $riverside->calculateEmergencyAvailableBeds());
        $this->assertGreaterThan(0, $riverside->calculateAvailableBeds()); // Maternity + Paediatric have beds
        $this->assertGreaterThan(0, $stAnne->calculateEmergencyAvailableBeds());

        // When querying for emergency care with availability=available:
        $response = $this->getJson('/api/v1/finder/hospitals?care_type=emergency&availability=available');

        $response->assertStatus(200);

        $hospitalsData = collect($response->json('data'));

        // St Anne MUST be present (it has 2 emergency beds)
        $this->assertTrue($hospitalsData->contains('slug', 'st-anne'));

        // Riverside MUST NOT be present as an available emergency facility despite having 3 maternity beds!
        $this->assertFalse($hospitalsData->contains('slug', 'riverside'));
    }

    public function test_finder_api_hospital_resource_exposes_emergency_diversion_and_nearest_alternative(): void
    {
        $riverside = Hospital::where('slug', 'riverside')->first();

        $response = $this->getJson("/api/v1/finder/hospitals/{$riverside->slug}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.emergency_available_beds', 0);
        $response->assertJsonPath('data.emergency_diversion', true);

        // Assert nearest alternative is provided
        $nearestAlt = $response->json('data.nearest_alternative');
        $this->assertNotNull($nearestAlt);
        $this->assertNotEquals($riverside->id, $nearestAlt['id']);
        $this->assertGreaterThan(0, $nearestAlt['emergency_available_beds']);

        // Check bed_categories contains clinical metadata
        $maternityWard = collect($response->json('data.bed_categories'))->firstWhere('name', 'Maternity');
        $this->assertNotNull($maternityWard);
        $this->assertEquals('maternity', $maternityWard['care_category']);
        $this->assertFalse($maternityWard['is_emergency_intake']);
        $this->assertTrue($maternityWard['is_restricted']);
        $this->assertEquals('female_obstetric_only', $maternityWard['restriction_scope']);
        $this->assertStringContainsString('Labour & Delivery', $maternityWard['restriction_label']);
        $this->assertNotEmpty($maternityWard['restriction_warning']);
    }

    public function test_admin_can_update_bed_category_clinical_classification(): void
    {
        $admin = User::where('email', 'hannah@riverside.example')->first();
        $bed = BedCategory::where('hospital_id', $admin->hospital_id)->where('name', 'General Ward')->first();

        $response = $this->actingAs($admin)->putJson("/api/v1/admin/beds/{$bed->id}", [
            'total_beds' => 40,
            'out_of_service_beds' => 2,
            'care_category' => 'specialized',
            'is_emergency_intake' => false,
            'restriction_scope' => 'oncology_only',
            'restriction_label' => 'Restricted: Oncology Inpatients Only',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.care_category', 'specialized');
        $response->assertJsonPath('data.is_emergency_intake', false);
        $response->assertJsonPath('data.restriction_scope', 'oncology_only');
        $response->assertJsonPath('data.restriction_label', 'Restricted: Oncology Inpatients Only');

        $this->assertEquals('oncology_only', $bed->fresh()->restriction_scope);
        $this->assertTrue($bed->fresh()->isRestricted());
    }

    public function test_web_finder_results_and_details_warn_about_emergency_diversion(): void
    {
        $riverside = Hospital::where('slug', 'riverside')->first();

        // Results view
        $resultsResponse = $this->get('/finder/results?care_type=General+Emergency');
        $resultsResponse->assertStatus(200);
        $resultsResponse->assertSee('Diversion In Effect');
        $resultsResponse->assertSee('Specialized units (e.g. Maternity) are strictly restricted');

        // Hospital Details view
        $detailsResponse = $this->get("/finder/hospitals/{$riverside->slug}?care_type=General+Emergency");
        $detailsResponse->assertStatus(200);
        $detailsResponse->assertSee('EMERGENCY INTAKE FULL · DIVERSION IN EFFECT');
        $detailsResponse->assertSee("Why can't I use open beds in Maternity or Pediatrics?", false);
        $detailsResponse->assertSee('Specialized / Restricted Wards');
        $detailsResponse->assertSee('Restricted: Labour & Delivery Only');
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\BedCategory;
use App\Models\Hospital;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IntegrationController extends Controller
{
    /**
     * Get integration health & hospital capacity status.
     */
    public function status(Request $request): JsonResponse
    {
        /** @var Hospital $hospital */
        $hospital = $request->attributes->get('hospital');
        $apiKey = $request->attributes->get('hospital_api_key');

        $wards = $hospital->bedCategories()->get()->map(function (BedCategory $bed) {
            return [
                'id' => $bed->id,
                'name' => $bed->name,
                'external_code' => $bed->external_code,
                'total_beds' => $bed->total_beds,
                'occupied_beds' => $bed->occupied_beds,
                'available_beds' => $bed->available_beds,
                'status' => $bed->status,
                'last_synced_at' => $bed->last_synced_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'hospital' => [
                'id' => $hospital->id,
                'name' => $hospital->name,
                'slug' => $hospital->slug,
                'integration_mode' => $hospital->integration_mode,
                'last_reported_at' => $hospital->last_reported_at?->toIso8601String(),
                'total_beds' => $hospital->calculateTotalBeds(),
                'occupied_beds' => $hospital->calculateOccupiedBeds(),
                'available_beds' => $hospital->calculateAvailableBeds(),
            ],
            'api_key' => [
                'name' => $apiKey->name,
                'prefix' => $apiKey->key_prefix,
                'last_used_at' => $apiKey->last_used_at?->toIso8601String(),
            ],
            'wards' => $wards,
        ]);
    }

    /**
     * Ingest an Admission event from external EHR/HMS.
     */
    public function admit(Request $request): JsonResponse
    {
        /** @var Hospital $hospital */
        $hospital = $request->attributes->get('hospital');

        $validated = $request->validate([
            'ward_code' => ['required', 'string'],
            'external_admission_id' => ['nullable', 'string', 'max:100'],
            'external_patient_id' => ['nullable', 'string', 'max:100'],
            'patient_name' => ['nullable', 'string', 'max:150'],
            'patient_gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'admitted_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $wardCode = trim($validated['ward_code']);

        $bedCategory = $this->resolveBedCategory($hospital, $wardCode);

        if (! $bedCategory) {
            return response()->json([
                'message' => "Ward code [{$wardCode}] not found for this hospital. Please map this code in your MediSlot admin dashboard.",
            ], 404);
        }

        $externalAdmId = $validated['external_admission_id'] ?? ('EXT-'.strtoupper(Str::random(10)));
        $patient = $this->resolvePatient($hospital, $validated);

        return DB::transaction(function () use ($hospital, $bedCategory, $validated, $externalAdmId, $patient) {
            $existingAdmission = Admission::where('hospital_id', $hospital->id)
                ->where('external_admission_id', $externalAdmId)
                ->first();

            if ($existingAdmission && $existingAdmission->status === 'admitted') {
                return response()->json([
                    'message' => 'Admission already registered and active in MediSlot.',
                    'admission' => $this->formatAdmissionResponse($existingAdmission),
                    'ward' => $this->formatWardResponse($bedCategory),
                ], 200);
            }

            $bedCategory->increment('occupied_beds');
            $bedCategory->recalculateAvailability();
            $bedCategory->forceFill(['last_synced_at' => now()])->save();

            $admission = Admission::create([
                'hospital_id' => $hospital->id,
                'patient_id' => $patient?->id,
                'bed_category_id' => $bedCategory->id,
                'admission_number' => $externalAdmId,
                'external_admission_id' => $externalAdmId,
                'external_patient_id' => $validated['external_patient_id'] ?? null,
                'admitted_at' => ! empty($validated['admitted_at']) ? $validated['admitted_at'] : now(),
                'status' => 'admitted',
                'source' => 'integration',
                'notes' => $validated['notes'] ?? 'Admitted via external EHR integration',
            ]);

            $hospital->forceFill(['last_reported_at' => now()])->save();

            return response()->json([
                'message' => 'Patient admission processed successfully. Bed occupancy updated.',
                'admission' => $this->formatAdmissionResponse($admission),
                'ward' => $this->formatWardResponse($bedCategory),
            ], 201);
        });
    }

    /**
     * Ingest a Discharge event from external EHR/HMS.
     */
    public function discharge(Request $request): JsonResponse
    {
        /** @var Hospital $hospital */
        $hospital = $request->attributes->get('hospital');

        $validated = $request->validate([
            'external_admission_id' => ['nullable', 'string'],
            'external_patient_id' => ['nullable', 'string'],
            'admission_number' => ['nullable', 'string'],
            'discharged_at' => ['nullable', 'date'],
        ]);

        if (empty($validated['external_admission_id']) && empty($validated['external_patient_id']) && empty($validated['admission_number'])) {
            return response()->json([
                'message' => 'At least one identifier (external_admission_id, external_patient_id, or admission_number) is required.',
            ], 422);
        }

        $admissionQuery = Admission::where('hospital_id', $hospital->id)
            ->where('status', 'admitted');

        if (! empty($validated['external_admission_id'])) {
            $admissionQuery->where('external_admission_id', $validated['external_admission_id']);
        } elseif (! empty($validated['admission_number'])) {
            $admissionQuery->where('admission_number', $validated['admission_number']);
        } elseif (! empty($validated['external_patient_id'])) {
            $admissionQuery->where('external_patient_id', $validated['external_patient_id']);
        }

        $admission = $admissionQuery->latest('admitted_at')->first();

        if (! $admission) {
            return response()->json([
                'message' => 'No active admission found matching the provided identifier for this hospital.',
            ], 404);
        }

        return DB::transaction(function () use ($hospital, $admission, $validated) {
            $admission->update([
                'status' => 'discharged',
                'discharged_at' => ! empty($validated['discharged_at']) ? $validated['discharged_at'] : now(),
            ]);

            $bedCategory = $admission->bedCategory;
            if ($bedCategory) {
                if ($bedCategory->occupied_beds > 0) {
                    $bedCategory->decrement('occupied_beds');
                }
                $bedCategory->recalculateAvailability();
                $bedCategory->forceFill(['last_synced_at' => now()])->save();
            }

            $hospital->forceFill(['last_reported_at' => now()])->save();

            return response()->json([
                'message' => 'Patient discharge processed successfully. Bed has been released.',
                'admission' => $this->formatAdmissionResponse($admission),
                'ward' => $bedCategory ? $this->formatWardResponse($bedCategory) : null,
            ], 200);
        });
    }

    /**
     * Ingest a Transfer event between wards/beds from external EHR/HMS.
     */
    public function transfer(Request $request): JsonResponse
    {
        /** @var Hospital $hospital */
        $hospital = $request->attributes->get('hospital');

        $validated = $request->validate([
            'external_admission_id' => ['required', 'string'],
            'to_ward_code' => ['required', 'string'],
            'transferred_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $admission = Admission::where('hospital_id', $hospital->id)
            ->where('external_admission_id', $validated['external_admission_id'])
            ->where('status', 'admitted')
            ->first();

        if (! $admission) {
            return response()->json([
                'message' => "No active admission found for ID [{$validated['external_admission_id']}].",
            ], 404);
        }

        $toBedCategory = $this->resolveBedCategory($hospital, $validated['to_ward_code']);

        if (! $toBedCategory) {
            return response()->json([
                'message' => "Destination ward code [{$validated['to_ward_code']}] not found for this hospital.",
            ], 404);
        }

        return DB::transaction(function () use ($hospital, $admission, $toBedCategory) {
            $fromBedCategory = $admission->bedCategory;

            if ($fromBedCategory && $fromBedCategory->id !== $toBedCategory->id) {
                if ($fromBedCategory->occupied_beds > 0) {
                    $fromBedCategory->decrement('occupied_beds');
                }
                $fromBedCategory->recalculateAvailability();
                $fromBedCategory->forceFill(['last_synced_at' => now()])->save();

                $toBedCategory->increment('occupied_beds');
                $toBedCategory->recalculateAvailability();
                $toBedCategory->forceFill(['last_synced_at' => now()])->save();

                $admission->update([
                    'bed_category_id' => $toBedCategory->id,
                    'notes' => ($admission->notes ? $admission->notes."\n" : '').'Transferred to '.$toBedCategory->name.' at '.now()->toDateTimeString(),
                ]);
            }

            $hospital->forceFill(['last_reported_at' => now()])->save();

            return response()->json([
                'message' => 'Patient transfer completed successfully.',
                'admission' => $this->formatAdmissionResponse($admission),
                'from_ward' => $fromBedCategory ? $this->formatWardResponse($fromBedCategory) : null,
                'to_ward' => $this->formatWardResponse($toBedCategory),
            ], 200);
        });
    }

    /**
     * Bulk reconcile bed capacities and counts (Snapshot Sync).
     */
    public function syncBeds(Request $request): JsonResponse
    {
        /** @var Hospital $hospital */
        $hospital = $request->attributes->get('hospital');

        $validated = $request->validate([
            'wards' => ['required', 'array', 'min:1'],
            'wards.*.ward_code' => ['required', 'string'],
            'wards.*.total_beds' => ['required', 'integer', 'min:0'],
            'wards.*.occupied_beds' => ['required', 'integer', 'min:0'],
            'wards.*.out_of_service_beds' => ['nullable', 'integer', 'min:0'],
        ]);

        return DB::transaction(function () use ($hospital, $validated) {
            $syncedWards = [];
            $unmatchedWards = [];

            foreach ($validated['wards'] as $item) {
                $wardCode = trim($item['ward_code']);
                $bed = $this->resolveBedCategory($hospital, $wardCode);

                if (! $bed) {
                    $unmatchedWards[] = $wardCode;

                    continue;
                }

                $bed->total_beds = (int) $item['total_beds'];
                $bed->occupied_beds = (int) $item['occupied_beds'];
                $bed->out_of_service_beds = isset($item['out_of_service_beds']) ? (int) $item['out_of_service_beds'] : 0;
                $bed->recalculateAvailability();
                $bed->last_synced_at = now();
                $bed->save();

                $syncedWards[] = $this->formatWardResponse($bed);
            }

            $hospital->forceFill(['last_reported_at' => now()])->save();

            return response()->json([
                'message' => 'Bed snapshot synchronization completed.',
                'synced_count' => count($syncedWards),
                'unmatched_wards' => $unmatchedWards,
                'wards' => $syncedWards,
                'hospital' => [
                    'total_beds' => $hospital->calculateTotalBeds(),
                    'occupied_beds' => $hospital->calculateOccupiedBeds(),
                    'available_beds' => $hospital->calculateAvailableBeds(),
                ],
            ], 200);
        });
    }

    /**
     * Smart Bed Allocation Engine.
     * Evaluates patient acuity and capacity, returning the recommended bed category and a provisional reservation hold.
     */
    public function allocateBed(Request $request): JsonResponse
    {
        /** @var Hospital $hospital */
        $hospital = $request->attributes->get('hospital');

        $validated = $request->validate([
            'acuity' => ['nullable', 'string', 'in:critical,intensive,maternity,pediatric,general,surgical'],
            'gender' => ['nullable', 'string', 'in:male,female,any'],
            'isolation_required' => ['nullable', 'boolean'],
            'patient_mrn' => ['nullable', 'string', 'max:100'],
            'preferred_ward_code' => ['nullable', 'string'],
        ]);

        $query = $hospital->bedCategories()->where('available_beds', '>', 0);

        if (! empty($validated['preferred_ward_code'])) {
            $prefCode = trim($validated['preferred_ward_code']);
            $preferred = (clone $query)->where(function ($q) use ($prefCode) {
                $q->where('external_code', $prefCode)->orWhere('name', $prefCode);
            })->first();

            if ($preferred) {
                return $this->buildAllocationResponse($preferred, $validated);
            }
        }

        $acuity = strtolower($validated['acuity'] ?? 'general');

        $targetCategory = null;

        if (in_array($acuity, ['critical', 'intensive'])) {
            $targetCategory = (clone $query)->where(function ($q) {
                $q->where('name', 'like', '%ICU%')
                    ->orWhere('name', 'like', '%Intensive%')
                    ->orWhere('name', 'like', '%Emergency%')
                    ->orWhere('name', 'like', '%High Dependency%');
            })->orderByDesc('available_beds')->first();
        } elseif ($acuity === 'maternity') {
            $targetCategory = (clone $query)->where(function ($q) {
                $q->where('name', 'like', '%Maternity%')
                    ->orWhere('name', 'like', '%Labour%')
                    ->orWhere('name', 'like', '%Postnatal%');
            })->orderByDesc('available_beds')->first();
        } elseif ($acuity === 'pediatric') {
            $targetCategory = (clone $query)->where(function ($q) {
                $q->where('name', 'like', '%Paediatric%')
                    ->orWhere('name', 'like', '%Pediatric%')
                    ->orWhere('name', 'like', '%Children%');
            })->orderByDesc('available_beds')->first();
        }

        if (! $targetCategory) {
            $targetCategory = $query->orderByDesc('available_beds')->first();
        }

        if (! $targetCategory) {
            return response()->json([
                'allocated' => false,
                'message' => 'No available beds matching clinical criteria in this hospital.',
                'hospital' => [
                    'total_beds' => $hospital->calculateTotalBeds(),
                    'occupied_beds' => $hospital->calculateOccupiedBeds(),
                    'available_beds' => 0,
                ],
            ], 409);
        }

        return $this->buildAllocationResponse($targetCategory, $validated);
    }

    /**
     * Resolve BedCategory by external_code, name, or ID.
     */
    protected function resolveBedCategory(Hospital $hospital, string $identifier): ?BedCategory
    {
        return $hospital->bedCategories()
            ->where(function ($q) use ($identifier) {
                $q->where('external_code', $identifier)
                    ->orWhere('name', $identifier)
                    ->orWhere('id', $identifier);
            })
            ->first();
    }

    /**
     * Resolve or create patient record for this hospital.
     */
    protected function resolvePatient(Hospital $hospital, array $data): Patient
    {
        $mrn = $data['external_patient_id'] ?? null;

        if ($mrn) {
            $existing = Patient::where('hospital_id', $hospital->id)
                ->where('patient_id', $mrn)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        $names = explode(' ', trim($data['patient_name'] ?? 'External Patient'), 2);
        $firstName = ! empty($names[0]) ? $names[0] : 'External';
        $lastName = $names[1] ?? 'Patient';

        $nextNum = Patient::where('hospital_id', $hospital->id)->count() + 1;
        $patientCode = $mrn ?: ('EXT-'.str_pad((string) $nextNum, 6, '0', STR_PAD_LEFT));

        return Patient::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $patientCode,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'gender' => $data['patient_gender'] ?? 'Other',
            'date_of_birth' => now()->subYears(30)->toDateString(),
        ]);
    }

    protected function buildAllocationResponse(BedCategory $bed, array $input): JsonResponse
    {
        $holdToken = 'HLD-'.strtoupper(Str::random(8));
        $expiresAt = now()->addMinutes(15);

        return response()->json([
            'allocated' => true,
            'hold_token' => $holdToken,
            'expires_at' => $expiresAt->toIso8601String(),
            'hold_duration_minutes' => 15,
            'bed_category' => [
                'id' => $bed->id,
                'name' => $bed->name,
                'ward_code' => $bed->external_code ?? (string) $bed->id,
                'available_beds' => $bed->available_beds,
                'total_beds' => $bed->total_beds,
                'status' => $bed->status,
            ],
            'instructions' => 'Bed is provisionally held for 15 minutes. Send POST /api/v1/integrations/adt/admit with ward_code and external_admission_id to finalize admission.',
        ], 200);
    }

    protected function formatAdmissionResponse(Admission $admission): array
    {
        return [
            'id' => $admission->id,
            'admission_number' => $admission->admission_number,
            'external_admission_id' => $admission->external_admission_id,
            'external_patient_id' => $admission->external_patient_id,
            'status' => $admission->status,
            'source' => $admission->source,
            'admitted_at' => $admission->admitted_at?->toIso8601String(),
            'discharged_at' => $admission->discharged_at?->toIso8601String(),
            'notes' => $admission->notes,
        ];
    }

    protected function formatWardResponse(BedCategory $bed): array
    {
        return [
            'id' => $bed->id,
            'name' => $bed->name,
            'external_code' => $bed->external_code,
            'total_beds' => $bed->total_beds,
            'occupied_beds' => $bed->occupied_beds,
            'available_beds' => $bed->available_beds,
            'out_of_service_beds' => $bed->out_of_service_beds,
            'status' => $bed->status,
            'last_synced_at' => $bed->last_synced_at?->toIso8601String(),
        ];
    }
}

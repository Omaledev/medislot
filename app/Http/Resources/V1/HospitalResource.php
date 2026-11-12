<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospitalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'state' => $this->state,
            'lga' => $this->lga,
            'address' => $this->address,
            'registration_number' => $this->registration_number,
            'website' => $this->website,
            'emergency_status' => $this->emergency_status,
            'location_for_directions' => $this->location_for_directions,
            'total_beds' => $this->calculateTotalBeds(),
            'available_beds' => $this->calculateAvailableBeds(),
            'emergency_available_beds' => $this->calculateEmergencyAvailableBeds(),
            'emergency_diversion' => $this->isEmergencyDiverting(),
            'relevant_available_beds' => $this->calculateRelevantAvailableBeds($request->query('care_type')),
            'nearest_alternative' => $this->when($this->isEmergencyDiverting(), function () {
                $alt = $this->findNearestEmergencyAlternative();
                if (! $alt) {
                    return null;
                }

                return [
                    'id' => $alt->id,
                    'name' => $alt->name,
                    'slug' => $alt->slug,
                    'phone' => $alt->phone,
                    'address' => $alt->address,
                    'state' => $alt->state,
                    'emergency_available_beds' => $alt->calculateEmergencyAvailableBeds(),
                    'emergency_status' => $alt->emergency_status,
                ];
            }),
            'occupied_beds' => $this->calculateOccupiedBeds(),
            'last_reported_at' => $this->last_reported_at?->diffForHumans() ?? 'Unknown',
            'bed_categories' => BedCategoryResource::collection($this->whenLoaded('bedCategories')),
            'services' => HospitalServiceResource::collection($this->whenLoaded('services')),
            'departments' => DepartmentResource::collection($this->whenLoaded('departments')),
        ];
    }
}

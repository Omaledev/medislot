<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'date_of_birth' => $this->date_of_birth?->format('d M Y'),
            'age' => $this->age,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'address' => $this->address,
            'emergency_contact' => $this->emergency_contact,
            'blood_group' => $this->blood_group,
            'genotype' => $this->genotype,
            'allergies' => $this->allergies,
            'chronic_conditions' => $this->chronic_conditions,
            'current_medications' => $this->current_medications,
            'created_at' => $this->created_at?->format('d M Y H:i'),
        ];
    }
}

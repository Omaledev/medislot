<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'admission_number' => $this->admission_number,
            'patient_id' => $this->patient_id,
            'patient_code' => $this->patient?->patient_id,
            'patient_name' => $this->patient?->full_name,
            'ward' => $this->bedCategory?->name,
            'bed_category_id' => $this->bed_category_id,
            'status' => $this->status,
            'admitted_at' => $this->admitted_at?->format('d M, H:i'),
            'discharged_at' => $this->discharged_at?->format('d M, H:i'),
            'notes' => $this->notes,
        ];
    }
}

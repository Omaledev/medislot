<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'visit_number' => $this->visit_number,
            'patient_id' => $this->patient_id,
            'patient_code' => $this->patient?->patient_id,
            'patient_name' => $this->patient?->full_name,
            'age' => $this->patient?->age,
            'gender' => $this->patient?->gender,
            'department_id' => $this->department_id,
            'department_name' => $this->department?->name ?? '—',
            'clinician_id' => $this->clinician_id,
            'clinician_name' => $this->clinician?->name ?? 'Not assigned',
            'visit_type' => $this->visit_type,
            'reason_for_visit' => $this->reason_for_visit,
            'priority' => $this->priority,
            'status' => $this->status,
            'cancellation_reason' => $this->cancellation_reason,
            'arrival_time' => $this->arrived_at?->format('H:i'),
            'arrived_at' => $this->arrived_at?->format('d M Y H:i'),
        ];
    }
}

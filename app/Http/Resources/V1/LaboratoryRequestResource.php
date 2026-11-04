<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaboratoryRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_number' => $this->request_number,
            'visit_id' => $this->visit_id,
            'patient_id' => $this->patient_id,
            'patient_code' => $this->patient?->patient_id,
            'patient_name' => $this->patient?->full_name,
            'requester_name' => $this->requester?->name,
            'category' => $this->category,
            'test_name' => $this->test_name,
            'clinical_reason' => $this->clinical_reason,
            'priority' => $this->priority,
            'instructions' => $this->instructions,
            'status' => $this->status,
            'sample_type' => $this->sample_type,
            'sample_collected_at' => $this->sample_collected_at?->format('H:i'),
            'requested_at' => $this->created_at?->format('H:i'),
            'result' => $this->result ? [
                'result_value' => $this->result->result_value,
                'unit' => $this->result->unit,
                'reference_range' => $this->result->reference_range,
                'interpretation' => $this->result->interpretation,
                'lab_notes' => $this->result->lab_notes,
                'finalized_at' => $this->result->finalized_at?->format('d M Y H:i'),
                'entered_by' => $this->result->enteredByUser?->name,
            ] : null,
        ];
    }
}

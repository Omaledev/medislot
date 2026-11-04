<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'visit_id' => $this->visit_id,
            'patient_id' => $this->patient_id,
            'clinician_id' => $this->clinician_id,
            'clinician_name' => $this->clinician?->name,
            'chief_complaint' => $this->chief_complaint,
            'history_of_present_illness' => $this->history_of_present_illness,
            'relevant_medical_history' => $this->relevant_medical_history,
            'examination_findings' => $this->examination_findings,
            'assessment_diagnosis' => $this->assessment_diagnosis,
            'treatment_plan' => $this->treatment_plan,
            'follow_up_instructions' => $this->follow_up_instructions,
            'status' => $this->status,
            'finalized_at' => $this->finalized_at?->format('d M Y H:i'),
            'updated_at' => $this->updated_at?->format('H:i'),
        ];
    }
}

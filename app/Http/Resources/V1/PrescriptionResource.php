<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rx_number' => $this->rx_number,
            'visit_id' => $this->visit_id,
            'patient_id' => $this->patient_id,
            'patient_code' => $this->patient?->patient_id,
            'patient_name' => $this->patient?->full_name,
            'prescriber_name' => $this->prescriber?->name,
            'notes' => $this->notes,
            'status' => $this->status,
            'clarification_note' => $this->clarification_note,
            'created_at' => $this->created_at?->format('d M Y H:i'),
            'items' => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'medication_id' => $item->medication_id,
                'medication_name' => $item->medication_name,
                'strength' => $item->strength,
                'dosage' => $item->dosage,
                'route' => $item->route,
                'frequency' => $item->frequency,
                'duration' => $item->duration,
                'quantity_prescribed' => $item->quantity_prescribed,
                'quantity_dispensed' => $item->quantity_dispensed,
                'instructions' => $item->instructions,
                'status' => $item->status,
                'stock_status' => $item->medication?->status ?? 'available',
            ]),
        ];
    }
}

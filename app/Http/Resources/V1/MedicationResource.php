<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'generic_name' => $this->generic_name,
            'strength' => $this->strength,
            'form' => $this->form,
            'unit_of_measurement' => $this->unit_of_measurement,
            'quantity' => $this->quantity,
            'reorder_level' => $this->reorder_level,
            'expiry_date' => $this->expiry_date?->format('d M Y'),
            'batch_number' => $this->batch_number,
            'status' => $this->status,
        ];
    }
}

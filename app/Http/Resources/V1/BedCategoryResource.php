<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BedCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'total_beds' => $this->total_beds,
            'occupied_beds' => $this->occupied_beds,
            'out_of_service_beds' => $this->out_of_service_beds,
            'available_beds' => $this->available_beds,
            'status' => $this->status,
            'last_updated' => $this->last_updated_at?->diffForHumans() ?? 'Not updated',
        ];
    }
}

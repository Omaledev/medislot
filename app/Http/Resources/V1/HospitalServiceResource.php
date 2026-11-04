<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospitalServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'department_id' => $this->department_id,
            'department_name' => $this->department?->name,
            'status' => $this->status,
            'is_emergency_capable' => (bool) $this->is_emergency_capable,
            'is_public_visible' => (bool) $this->is_public_visible,
            'last_updated' => $this->last_updated_at?->diffForHumans() ?? 'Not updated',
        ];
    }
}

<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'description' => $this->description,
            'lead_user_id' => $this->lead_user_id,
            'lead_name' => $this->lead?->name ?? 'Not assigned',
            'staff_count' => $this->staff()->count(),
            'status' => $this->status,
        ];
    }
}

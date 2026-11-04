<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'title' => $this->title,
            'department_id' => $this->department_id,
            'department_name' => $this->department?->name ?? '—',
            'department_username' => $this->department_username,
            'phone' => $this->phone,
            'status' => $this->status,
            'created_at' => $this->created_at?->format('d M Y'),
        ];
    }
}

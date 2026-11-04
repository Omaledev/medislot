<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admission extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'admitted_at' => 'datetime',
            'discharged_at' => 'datetime',
        ];
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function bedCategory(): BelongsTo
    {
        return $this->belongsTo(BedCategory::class);
    }

    public function admittedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admitted_by');
    }

    public function dischargedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'discharged_by');
    }
}

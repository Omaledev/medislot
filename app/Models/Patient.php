<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'age' => 'integer',
        ];
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function registeredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class)->latest('arrived_at');
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function labRequests(): HasMany
    {
        return $this->hasMany(LaboratoryRequest::class)->latest();
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class)->latest();
    }

    public function consultationNotes(): HasMany
    {
        return $this->hasMany(ConsultationNote::class)->latest();
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}

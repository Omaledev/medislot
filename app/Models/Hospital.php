<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospital extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'last_reported_at' => 'datetime',
            'warn_unupdated_hours' => 'integer',
            'mark_stale_hours' => 'integer',
        ];
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function bedCategories(): HasMany
    {
        return $this->hasMany(BedCategory::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(HospitalService::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function medications(): HasMany
    {
        return $this->hasMany(Medication::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function labRequests(): HasMany
    {
        return $this->hasMany(LaboratoryRequest::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    public function calculateTotalBeds(): int
    {
        return (int) $this->bedCategories()->sum('total_beds');
    }

    public function calculateAvailableBeds(): int
    {
        return (int) $this->bedCategories()->sum('available_beds');
    }

    public function calculateOccupiedBeds(): int
    {
        return (int) $this->bedCategories()->sum('occupied_beds');
    }
}

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
            'custom_domain_verified_at' => 'datetime',
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

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function bedCategories(): HasMany
    {
        return $this->hasMany(BedCategory::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(HospitalApiKey::class);
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

    public function calculateEmergencyAvailableBeds(): int
    {
        return (int) $this->bedCategories()
            ->where('is_emergency_intake', true)
            ->sum('available_beds');
    }

    public function calculateRelevantAvailableBeds(?string $careType = null): int
    {
        if (empty($careType) || in_array(strtolower($careType), ['general emergency', 'accident / trauma', 'accident', 'trauma', 'emergency'])) {
            return $this->calculateEmergencyAvailableBeds();
        }

        if (str_contains(strtolower($careType), 'maternity')) {
            return (int) $this->bedCategories()
                ->where('care_category', 'maternity')
                ->sum('available_beds');
        }

        if (str_contains(strtolower($careType), 'child')) {
            return (int) $this->bedCategories()
                ->where('care_category', 'pediatric')
                ->sum('available_beds');
        }

        return $this->calculateAvailableBeds();
    }

    public function isEmergencyDiverting(): bool
    {
        return $this->emergency_status === 'not_accepting' || $this->calculateEmergencyAvailableBeds() === 0;
    }

    public function findNearestEmergencyAlternative(): ?Hospital
    {
        return static::where('id', '!=', $this->id)
            ->where('status', 'active')
            ->where('emergency_status', '!=', 'not_accepting')
            ->whereHas('bedCategories', fn ($q) => $q->where('is_emergency_intake', true)->where('available_beds', '>', 0))
            ->orderByRaw('CASE WHEN state = ? THEN 0 ELSE 1 END', [$this->state])
            ->first();
    }

    public function calculateOccupiedBeds(): int
    {
        return (int) $this->bedCategories()->sum('occupied_beds');
    }

    public function hasCustomDomain(): bool
    {
        return ! empty($this->custom_domain);
    }

    public function isCustomDomainVerified(): bool
    {
        return $this->custom_domain_status === 'active' && $this->custom_domain_verified_at !== null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BedCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'total_beds' => 'integer',
            'occupied_beds' => 'integer',
            'out_of_service_beds' => 'integer',
            'available_beds' => 'integer',
            'is_emergency_intake' => 'boolean',
            'last_updated_at' => 'datetime',
            'last_synced_at' => 'datetime',
        ];
    }

    public function isRestricted(): bool
    {
        return ! empty($this->restriction_scope);
    }

    public function getRestrictionWarningAttribute(): ?string
    {
        if ($this->restriction_scope === 'female_obstetric_only') {
            return 'Restricted unit reserved for labor, delivery, and postpartum mothers. Ineligible for general medical or trauma emergencies.';
        }

        if ($this->restriction_scope === 'pediatric_under_18') {
            return 'Restricted unit for infants and children under 14 years. Ineligible for adult emergencies.';
        }

        if ($this->isRestricted()) {
            return 'Restricted unit with specific clinical admission criteria. Not available for unscheduled general emergencies.';
        }

        return null;
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function recalculateAvailability(): void
    {
        $avail = max(0, $this->total_beds - $this->occupied_beds - $this->out_of_service_beds);
        $this->available_beds = $avail;

        if ($avail === 0) {
            $this->status = 'full';
        } elseif ($this->total_beds > 0 && ($avail / $this->total_beds) <= 0.25) {
            $this->status = 'limited';
        } else {
            $this->status = 'available';
        }

        $this->last_updated_at = now();
        $this->save();
    }
}

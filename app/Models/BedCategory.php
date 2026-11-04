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
            'last_updated_at' => 'datetime',
        ];
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medication extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reorder_level' => 'integer',
            'expiry_date' => 'date',
        ];
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function updateStatus(): void
    {
        if ($this->expiry_date && $this->expiry_date->isPast()) {
            $this->status = 'expired';
        } elseif ($this->quantity <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->quantity <= $this->reorder_level) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'available';
        }
        $this->save();
    }
}

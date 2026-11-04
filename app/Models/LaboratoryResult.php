<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaboratoryResult extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'finalized_at' => 'datetime',
        ];
    }

    public function labRequest(): BelongsTo
    {
        return $this->belongsTo(LaboratoryRequest::class, 'laboratory_request_id');
    }

    public function enteredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }
}

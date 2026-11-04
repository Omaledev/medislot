<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'hospital_id',
        'department_id',
        'name',
        'email',
        'password',
        'role',
        'department_username',
        'phone',
        'title',
        'status',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
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

    public function isHospitalAdmin(): bool
    {
        return $this->role === 'hospital_admin';
    }

    public function isReception(): bool
    {
        return $this->role === 'reception';
    }

    public function isConsultant(): bool
    {
        return $this->role === 'consultant';
    }

    public function isLabScientist(): bool
    {
        return $this->role === 'lab_scientist';
    }

    public function isPharmacist(): bool
    {
        return $this->role === 'pharmacist';
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'clinician_id');
    }

    public function consultationNotes(): HasMany
    {
        return $this->hasMany(ConsultationNote::class, 'clinician_id');
    }

    public function labRequests(): HasMany
    {
        return $this->hasMany(LaboratoryRequest::class, 'requester_id');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'prescriber_id');
    }
}

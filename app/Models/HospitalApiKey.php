<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class HospitalApiKey extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Generate a new API key for a hospital.
     *
     * @param  array<int, string>  $permissions
     * @return array{key: self, plainTextToken: string}
     */
    public static function generate(Hospital $hospital, string $name, array $permissions = ['*'], ?\DateTimeInterface $expiresAt = null): array
    {
        $random = Str::random(40);
        $plainTextToken = "ms_live_{$random}";
        $keyHash = hash('sha256', $plainTextToken);
        $keyPrefix = 'ms_live_'.substr($random, 0, 6);

        $key = self::create([
            'hospital_id' => $hospital->id,
            'name' => $name,
            'key_prefix' => $keyPrefix,
            'key_hash' => $keyHash,
            'permissions' => $permissions,
            'expires_at' => $expiresAt,
        ]);

        return [
            'key' => $key,
            'plainTextToken' => $plainTextToken,
        ];
    }

    /**
     * Locate an API key model from a plain-text token.
     */
    public static function findToken(string $token): ?self
    {
        $hash = hash('sha256', trim($token));

        $apiKey = self::with('hospital')->where('key_hash', $hash)->first();

        if (! $apiKey) {
            return null;
        }

        if ($apiKey->expires_at && $apiKey->expires_at->isPast()) {
            return null;
        }

        return $apiKey;
    }

    public function recordUsage(): void
    {
        $this->forceFill(['last_used_at' => now()])->save();
    }
}

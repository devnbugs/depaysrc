<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthenticationVerification extends Model
{
    protected $table = 'authentication_verifications';

    protected $fillable = [
        'user_id',
        'type',
        'context',
        'status',
        'reference_id',
        'attempted_at',
        'verified_at',
        'ip_address',
        'user_agent',
        'notes',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsVerified()
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now()
        ]);
    }

    public function markAsFailed()
    {
        $this->update(['status' => 'failed']);
    }

    public static function createVerification(
        int $userId,
        string $type,
        string $context,
        ?string $referenceId = null,
        ?string $notes = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'context' => $context,
            'reference_id' => $referenceId,
            'status' => 'pending',
            'attempted_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'notes' => $notes,
        ]);
    }
}

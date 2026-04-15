<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPasskey extends Model
{
    protected $table = 'user_passkeys';

    protected $fillable = [
        'user_id',
        'credential_id',
        'public_key',
        'credential_public_key',
        'counter',
        'transports',
        'aaguid',
        'name',
        'used_at',
        'registered_at',
        'backup_eligible',
        'backup_state',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'used_at' => 'datetime',
        'backup_eligible' => 'boolean',
        'backup_state' => 'boolean',
        'transports' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updateUsedAt()
    {
        $this->update(['used_at' => now()]);
    }
}

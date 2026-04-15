<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualCard extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'provider_payload' => 'array',
        'blocked_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'available_balance' => 'decimal:2',
        'ledger_balance' => 'decimal:2',
        'pan' => 'encrypted',
        'cvv2' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isBlocked(): bool
    {
        return (int) $this->status === 0;
    }
}

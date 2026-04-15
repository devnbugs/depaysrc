<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycPlan extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
        'monthly_limit' => 'decimal:2',
        'features' => 'array',
        'enabled' => 'boolean',
        'sort_order' => 'integer',
        'invoice_limit' => 'integer',
        'paystack_plan_id' => 'integer',
        'paystack_last_synced_at' => 'datetime',
    ];
}

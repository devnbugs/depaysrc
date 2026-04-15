<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycService extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
        'enabled' => 'boolean',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PsBank extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['primary', 'secondary'];

    /**
     * Cast attributes to specific types.
     *
     * @var array
     */
    protected $casts = [
        'primary' => 'array',
        'secondary' => 'array',
    ];
}

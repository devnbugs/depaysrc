<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internetbundle extends Model
{
    protected $fillable = [
        'name',
        'datatype',
        'network',
        'networkcode',
        'code',
        'plan',
        'cost',
		'validity',
		'providers',
        'image',
        'status',
        '_token',
    ];
    use HasFactory;
}

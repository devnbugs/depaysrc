<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketPrice extends Model
{
    use HasFactory;

    // Define the table name (optional, as Laravel will assume it’s plural of the model name)
    protected $table = 'market_prices';

    // Define the fillable fields for mass assignment
    protected $fillable = ['name', 'price', 'price_change', 'type', 'datatype'];

    // If you're using timestamps, they are enabled by default
    // public $timestamps = false; // Uncomment if you do not want timestamps
}

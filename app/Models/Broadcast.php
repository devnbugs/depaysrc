<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'broadcasts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'message',
        'usertype',
        'timeframe',
        'status',
    ];

    /**
     * Get broadcasts based on user type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $usertype
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfUserType($query, $usertype)
    {
        return $query->where('usertype', $usertype);
    }

    /**
     * Scope to filter active broadcasts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope to filter broadcasts by timeframe.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $timeframe
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTimeframe($query, $timeframe)
    {
        return $query->where('timeframe', $timeframe);
    }
}

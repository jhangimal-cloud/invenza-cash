<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CollectionTrackingStatus extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'color',
        'sort_order',
        'is_initial',
        'is_final',
        'stops_notifications',
        'is_active',
    ];

    protected $casts = [
        'is_initial' => 'boolean',
        'is_final' => 'boolean',
        'stops_notifications' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function trackings()
    {
        return $this->hasMany(CollectionTracking::class, 'status_id');
    }
}

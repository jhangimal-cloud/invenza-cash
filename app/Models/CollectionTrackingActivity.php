<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionTrackingActivity extends Model
{
    protected $fillable = [
        'company_id',
        'tracking_id',
        'user_id',
        'activity_type',
        'direction',
        'old_status_id',
        'new_status_id',
        'subject',
        'body',
        'promised_amount',
        'promised_payment_date',
        'activity_at',
        'next_follow_up_at',
    ];

    protected $casts = [
        'promised_amount' => 'decimal:2',
        'promised_payment_date' => 'date',
        'activity_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
    ];

    public function tracking()
    {
        return $this->belongsTo(CollectionTracking::class, 'tracking_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oldStatus()
    {
        return $this->belongsTo(CollectionTrackingStatus::class, 'old_status_id');
    }

    public function newStatus()
    {
        return $this->belongsTo(CollectionTrackingStatus::class, 'new_status_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CollectionTracking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'receivable_id',
        'tracking_code',
        'responsible_user_id',
        'status_id',
        'title',
        'balance_amount',
        'priority',
        'original_due_date',
        'next_follow_up_at',
        'last_activity_at',
        'closed_at',
        'internal_notes',
        'is_active',
    ];

    protected $casts = [
        'balance_amount' => 'decimal:2',
        'original_due_date' => 'date',
        'next_follow_up_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'closed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (CollectionTracking $tracking) {
            if (blank($tracking->tracking_code)) {
                $tracking->tracking_code = 'COB-' . now()->format('Ymd') . '-' . Str::upper(Str::random(8));
            }
        });
    }

    public function receivable()
    {
        return $this->belongsTo(Receivable::class);
    }

    public function status()
    {
        return $this->belongsTo(CollectionTrackingStatus::class, 'status_id');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function activities()
    {
        return $this->hasMany(CollectionTrackingActivity::class, 'tracking_id')->latest('activity_at')->latest();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    protected $fillable = [
        'company_id',
        'customer_name',
        'customer_email',
        'document_number',
        'external_reference',
        'total',
        'balance',
        'due_date',
        'status',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'balance' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tracking()
    {
        return $this->hasOne(CollectionTracking::class);
    }
}

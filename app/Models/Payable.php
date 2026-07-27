<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{
    protected $fillable = [
        'company_id',
        'vendor_name',
        'vendor_email',
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
}

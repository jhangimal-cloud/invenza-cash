<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'contact_email',
        'status',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function receivables()
    {
        return $this->hasMany(Receivable::class);
    }

    public function payables()
    {
        return $this->hasMany(Payable::class);
    }
}

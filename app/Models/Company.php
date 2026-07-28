<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'contact_email',
        'status',
        'max_users',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function maxUsers(): int
    {
        return $this->max_users ?? config('platform.default_max_users_per_company');
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

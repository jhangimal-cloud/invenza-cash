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
        'intelligence_enabled',
        'intelligence_monthly_budget_usd',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function maxUsers(): int
    {
        return $this->max_users ?? config('platform.default_max_users_per_company');
    }

    public function intelligenceEnabled(): bool
    {
        return (bool) $this->intelligence_enabled;
    }

    public function intelligenceMonthlyBudget(): ?float
    {
        return $this->intelligence_monthly_budget_usd !== null
            ? (float) $this->intelligence_monthly_budget_usd
            : null;
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'total_usage_limit',
        'usage_per_user',
        'current_usage',
        'start_date',
        'expiry_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value'    => 'decimal:2',
            'total_usage_limit' => 'integer',
            'usage_per_user'    => 'integer',
            'current_usage'     => 'integer',
            'is_active'         => 'boolean',
            'start_date'        => 'datetime',
            'expiry_date'       => 'datetime',
        ];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'disabled';
        }

        $now = now();

        if ($this->start_date && $this->start_date > $now) {
            return 'scheduled';
        }

        if ($this->expiry_date && $this->expiry_date < $now) {
            return 'expired';
        }

        if ($this->total_usage_limit && $this->current_usage >= $this->total_usage_limit) {
            return 'expired';
        }

        return 'active';
    }

    public function isValidForUse(): bool
    {
        return $this->status === 'active';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deal extends Model
{
    use HasFactory;

    protected $fillable = [
        'initiator_id',
        'counterparty_id',
        'deal_id',
        'title',
        'amount',
        'buyer_pays',
        'seller_gets',
        'charge_total',
        'counterparty_email',
        'initiator_role',
        'status',
        'deadline',
        'workspace',
        'accepted_at',
        'completed_at',
        'admin_approved',
        'buyer_accepted',
        'custom_service',
        'custom_service_charge',
        'buyer_reviewed',
        'seller_reviewed',
        'category_id',
        'coupon_id',
        'original_charge',
        'coupon_discount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'buyer_pays' => 'decimal:2',
            'seller_gets' => 'decimal:2',
            'charge_total' => 'decimal:2',
            'custom_service_charge' => 'decimal:2',
            'original_charge' => 'decimal:2',
            'coupon_discount' => 'decimal:2',
            'admin_approved' => 'boolean',
            'buyer_accepted' => 'boolean',
            'custom_service' => 'boolean',
            'buyer_reviewed' => 'boolean',
            'seller_reviewed' => 'boolean',
            'deadline' => 'date',
            'workspace' => 'array',
            'accepted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counterparty_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}

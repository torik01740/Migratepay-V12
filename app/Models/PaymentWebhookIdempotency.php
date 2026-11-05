<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentWebhookIdempotency extends Model
{
    use HasFactory;

    protected $table = 'payment_webhook_idempotencies';

    protected $fillable = [
        'gateway_slug',
        'payment_reference',
        'status',
        'transaction_id',
        'user_id',
        'amount',
        'response_payload',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'response_payload' => 'array',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


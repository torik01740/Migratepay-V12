<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'currency',
        'instructions',
        'fee_percentage',
        'fee_fixed',
        'api_key',
        'secret_key',
        'store_id',
        'store_password',
        'is_active',
        'sandbox_mode',
    ];

    protected $hidden = [
        'api_key',
        'secret_key',
        'store_password',
    ];

    protected function casts(): array
    {
        return [
            'fee_percentage' => 'decimal:2',
            'fee_fixed' => 'decimal:2',
            'is_active' => 'boolean',
            'sandbox_mode' => 'boolean',
            'api_key' => 'encrypted',
            'secret_key' => 'encrypted',
            'store_password' => 'encrypted',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function toPublicArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'currency' => $this->currency,
            'instructions' => $this->instructions,
            'fee_percentage' => (string)$this->fee_percentage,
            'fee_fixed' => (string)$this->fee_fixed,
            'is_active' => $this->is_active,
        ];
    }
}

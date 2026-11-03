<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'placement',
        'position_behavior',
        'start_date',
        'end_date',
        'targeting',
        'design',
        'elements',
    ];

    protected function casts(): array
    {
        return [
            'targeting' => 'array',
            'design' => 'array',
            'elements' => 'array',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    /**
     * Scope query to only active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

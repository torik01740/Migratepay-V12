<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'deal_id',
        'reviewer_id',
        'reviewee_id',
        'role',
        'communication',
        'behavior',
        'delivery',
        'reliability',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'communication' => 'integer',
            'behavior' => 'integer',
            'delivery' => 'integer',
            'reliability' => 'integer',
        ];
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}

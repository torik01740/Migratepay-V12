<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id_string',
        'name',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'date_of_birth',
        'nid_number',
        'division',
        'district',
        'wallet_balance',
        'status',
        'role',
        'remember_token',
        'username',
        'headline',
        'location',
        'address',
        'bio',
        'skills',
        'languages',
        'education',
        'certifications',
        'professional_links',
        'cover_photo',
        'profile_photo',
        'cover_position_y',
        'profile_position_x',
        'profile_position_y',
        'cover_position_x',
        'verification_status',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'verification_updated_at',
        'gender',
        'id_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'wallet_balance' => 'decimal:2',
            'skills' => 'array',
            'languages' => 'array',
            'education' => 'array',
            'professional_links' => 'array',
            'verified_at' => 'datetime',
            'verification_updated_at' => 'datetime',
            'date_of_birth' => 'date',
        ];
    }

    /**
     * Check if user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is KYC verified.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'VERIFIED';
    }

    /**
     * Check if account is active.
     */
    public function isActive(): bool
    {
        return strtolower((string)$this->status) === 'active';
    }

    public function dealsInitiated(): HasMany
    {
        return $this->hasMany(Deal::class, 'initiator_id');
    }

    public function dealsReceived(): HasMany
    {
        return $this->hasMany(Deal::class, 'counterparty_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    public function withdrawalMethods(): HasMany
    {
        return $this->hasMany(WithdrawalMethod::class);
    }

    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}

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
        'kyc_document_path',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'two_factor_pending_secret',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_pending_secret',
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
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'array',
        ];
    }

    /**
     * Determine if two-factor authentication is active for this account.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return !empty($this->two_factor_secret) && $this->two_factor_confirmed_at !== null;
    }

    /**
     * Check if user is an administrator.
     */
    public function isAdmin(): bool
    {
        return strtolower((string)$this->role) === 'admin';
    }

    /**
     * Check if user has any administrative or staff role.
     */
    public function isStaff(): bool
    {
        $role = strtolower(str_replace(['_', ' '], '-', (string)$this->role));
        return in_array($role, ['admin', 'co-admin', 'editorial', 'editorial-team', 'support', 'support-team'], true);
    }

    /**
     * Check if user has a specific role or one of given roles.
     */
    public function hasRole(string ...$roles): bool
    {
        $normalizedRoles = array_map(fn($r) => strtolower(str_replace(['_', ' '], '-', $r)), $roles);
        $userRole = strtolower(str_replace(['_', ' '], '-', (string)$this->role));
        return in_array($userRole, $normalizedRoles, true);
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

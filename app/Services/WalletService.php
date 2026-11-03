<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class WalletService
{
    /**
     * Get user's current wallet balance as an exact decimal string.
     */
    public function getBalance(int $userId): string
    {
        $user = User::findOrFail($userId);
        return DecimalSafeMath::canonicalize($user->wallet_balance, 2);
    }

    /**
     * Credit user wallet balance atomically with an auditable transaction ledger.
     */
    public function credit(
        User $user,
        string $amount,
        string $type,
        string $referenceType,
        int $referenceId,
        string $description,
        ?string $idempotencyKey = null
    ): Transaction {
        $cleanAmount = DecimalSafeMath::canonicalize($amount, 2);

        if (DecimalSafeMath::comp($cleanAmount, '0.00') <= 0) {
            throw new InvalidArgumentException("Credit amount must be strictly greater than zero. Received: {$amount}");
        }

        return DB::transaction(function () use ($user, $cleanAmount, $type, $referenceType, $referenceId, $description, $idempotencyKey) {
            // Check for existing transaction if idempotency key is specified
            if ($idempotencyKey) {
                $existing = Transaction::where('reference_type', $referenceType)
                    ->where('reference_id', $referenceId)
                    ->where('type', $type)
                    ->where('description', 'LIKE', "%[IDEMPOTENCY:{$idempotencyKey}]%")
                    ->first();

                if ($existing) {
                    return $existing;
                }
            }

            $lockedUser = User::lockForUpdate()->findOrFail($user->id);
            $balanceBefore = DecimalSafeMath::canonicalize($lockedUser->wallet_balance, 2);
            $newBalance = DecimalSafeMath::add($balanceBefore, $cleanAmount, 2);

            $lockedUser->wallet_balance = $newBalance;
            $lockedUser->save();

            $finalDescription = $idempotencyKey 
                ? "{$description} [IDEMPOTENCY:{$idempotencyKey}]"
                : $description;

            return Transaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => $type,
                'amount'         => $cleanAmount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $newBalance,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'description'    => $finalDescription,
            ]);
        });
    }

    /**
     * Debit user wallet balance atomically with pessimistic locking and overdraft prevention.
     */
    public function debit(
        User $user,
        string $amount,
        string $type,
        string $referenceType,
        int $referenceId,
        string $description,
        ?string $idempotencyKey = null
    ): Transaction {
        $cleanAmount = DecimalSafeMath::canonicalize($amount, 2);

        if (DecimalSafeMath::comp($cleanAmount, '0.00') <= 0) {
            throw new InvalidArgumentException("Debit amount must be strictly greater than zero. Received: {$amount}");
        }

        return DB::transaction(function () use ($user, $cleanAmount, $type, $referenceType, $referenceId, $description, $idempotencyKey) {
            if ($idempotencyKey) {
                $existing = Transaction::where('reference_type', $referenceType)
                    ->where('reference_id', $referenceId)
                    ->where('type', $type)
                    ->where('description', 'LIKE', "%[IDEMPOTENCY:{$idempotencyKey}]%")
                    ->first();

                if ($existing) {
                    return $existing;
                }
            }

            $lockedUser = User::lockForUpdate()->findOrFail($user->id);
            $balanceBefore = DecimalSafeMath::canonicalize($lockedUser->wallet_balance, 2);

            if (DecimalSafeMath::comp($balanceBefore, $cleanAmount) < 0) {
                throw new RuntimeException("Insufficient funds. Available: ৳{$balanceBefore}, Required: ৳{$cleanAmount}");
            }

            $newBalance = DecimalSafeMath::sub($balanceBefore, $cleanAmount, 2);

            $lockedUser->wallet_balance = $newBalance;
            $lockedUser->save();

            $finalDescription = $idempotencyKey 
                ? "{$description} [IDEMPOTENCY:{$idempotencyKey}]"
                : $description;

            return Transaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => $type,
                'amount'         => $cleanAmount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $newBalance,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'description'    => $finalDescription,
            ]);
        });
    }

    /**
     * Lock funds from buyer for an escrow deal.
     */
    public function lockForEscrow(User $buyer, string $amount, Deal $deal, string $description): Transaction
    {
        return $this->debit(
            $buyer,
            $amount,
            'escrow_lock',
            Deal::class,
            $deal->id,
            $description,
            "deal_{$deal->id}_escrow_lock"
        );
    }

    /**
     * Release escrowed funds to seller upon deal completion.
     */
    public function releaseEscrow(User $seller, string $amount, Deal $deal, string $description): Transaction
    {
        return $this->credit(
            $seller,
            $amount,
            'escrow_release',
            Deal::class,
            $deal->id,
            $description,
            "deal_{$deal->id}_escrow_release"
        );
    }

    /**
     * Refund locked escrow funds back to buyer upon deal cancellation or dispute resolution.
     */
    public function refundEscrow(User $buyer, string $amount, Deal $deal, string $description): Transaction
    {
        return $this->credit(
            $buyer,
            $amount,
            'escrow_refund',
            Deal::class,
            $deal->id,
            $description,
            "deal_{$deal->id}_escrow_refund"
        );
    }

    /**
     * Get transaction history for a user with pagination.
     */
    public function getTransactionHistory(int $userId, int $perPage = 20)
    {
        return Transaction::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}

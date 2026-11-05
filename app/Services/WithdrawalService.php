<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WithdrawalMethod;
use App\Models\WithdrawalRequest;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WithdrawalService
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * Get user's withdrawal methods.
     */
    public function getUserMethods(int $userId)
    {
        return WithdrawalMethod::where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Add or update a withdrawal method for a user.
     */
    public function storeMethod(User $user, array $data, ?int $methodId = null): array
    {
        $payload = [
            'user_id' => $user->id,
            'type'    => $data['type'],
        ];

        if ($data['type'] === 'bank') {
            $payload = array_merge($payload, [
                'bank_name'      => $data['bank_name'] ?? null,
                'branch'         => $data['branch'] ?? null,
                'routing_number' => $data['routing_number'] ?? null,
                'account_name'   => $data['account_name'] ?? null,
                'account_number' => $data['account_number'] ?? null,
            ]);
        } else {
            $payload = array_merge($payload, [
                'mobile_provider' => $data['mobile_provider'] ?? null,
                'mobile_number'   => $data['mobile_number'] ?? null,
            ]);
        }

        if ($methodId) {
            $method = WithdrawalMethod::where('user_id', $user->id)->findOrFail($methodId);
            $method->update($payload);
            $message = 'Withdrawal method updated successfully.';
        } else {
            $method = WithdrawalMethod::create($payload);
            $message = 'Withdrawal method added successfully.';
        }

        return [
            'method'  => $method,
            'message' => $message,
        ];
    }

    /**
     * Delete a withdrawal method.
     */
    public function deleteMethod(User $user, int $methodId): void
    {
        $method = WithdrawalMethod::where('user_id', $user->id)->findOrFail($methodId);

        $pendingExists = WithdrawalRequest::where('withdrawal_method_id', $method->id)
            ->where('status', 'pending')
            ->exists();

        if ($pendingExists) {
            throw new RuntimeException('Cannot delete this method while a withdrawal is pending.');
        }

        $method->delete();
    }

    /**
     * Get user's withdrawal requests.
     */
    public function getUserWithdrawals(int $userId)
    {
        return WithdrawalRequest::with(['withdrawalMethod', 'user'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Initiate a withdrawal request with atomic balance deduction and transaction logging.
     */
    public function requestWithdrawal(User $user, array $data): array
    {
        // Enforce KYC requirement if active
        if (SystemSetting::get('security.require_kyc_withdrawal', false)) {
            if (!$user->isVerified()) {
                throw new RuntimeException("Account identity verification (KYC) is required before requesting withdrawals. Please submit your identity verification documents in Profile & Verification.");
            }
        }

        $method = WithdrawalMethod::where('user_id', $user->id)->findOrFail($data['withdrawal_method_id']);
        $amount = DecimalSafeMath::canonicalize($data['amount'], 2);

        if (DecimalSafeMath::comp($amount, '0.00') <= 0) {
            throw new RuntimeException("Withdrawal amount must be greater than zero.");
        }

        // Fee logic: 1.5% for mobile provider, 0% for bank transfer
        $fee = '0.00';
        if ($method->type === 'mobile') {
            $fee = DecimalSafeMath::mul($amount, '0.015', 2);
        }
        $netAmount = DecimalSafeMath::sub($amount, $fee, 2);

        return DB::transaction(function () use ($user, $method, $amount, $fee, $netAmount) {
            $referenceId = 'WDR-' . strtoupper(substr(uniqid(), -6));

            $withdrawal = WithdrawalRequest::create([
                'reference_id'         => $referenceId,
                'user_id'              => $user->id,
                'withdrawal_method_id' => $method->id,
                'amount'               => $amount,
                'fee'                  => $fee,
                'net_amount'           => $netAmount,
                'type'                 => $method->type,
                'status'               => 'pending',
            ]);

            $transaction = $this->walletService->debit(
                $user,
                $amount,
                'withdrawal',
                WithdrawalRequest::class,
                $withdrawal->id,
                "Withdrawal requested: {$referenceId} (৳{$amount})",
                "withdrawal_request_{$withdrawal->id}"
            );

            return [
                'withdrawal'     => $withdrawal->load('withdrawalMethod'),
                'wallet_balance' => $this->walletService->getBalance($user->id),
                'transaction'    => $transaction,
            ];
        });
    }

    /**
     * Admin: List all withdrawal requests with optional status filter.
     */
    public function getAdminWithdrawals(?string $status = null)
    {
        $query = WithdrawalRequest::with(['user', 'withdrawalMethod', 'reviewer'])
            ->orderBy('created_at', 'desc');

        if ($status && in_array($status, ['pending', 'approved', 'rejected', 'processing', 'completed'])) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Admin: Approve a withdrawal request.
     */
    public function approveWithdrawal(int $id, User $admin, ?string $adminNote = null): WithdrawalRequest
    {
        return DB::transaction(function () use ($id, $admin, $adminNote) {
            $withdrawal = WithdrawalRequest::lockForUpdate()->with(['user', 'withdrawalMethod'])->findOrFail($id);

            if ($withdrawal->status !== 'pending') {
                throw new RuntimeException('This withdrawal has already been processed.');
            }

            $withdrawal->update([
                'status'       => 'approved',
                'admin_note'   => $adminNote,
                'reviewed_by'  => $admin->id,
                'reviewed_at'  => now(),
                'processed_at' => now(),
            ]);

            AuditLogService::log(
                'finance.withdrawal_approved',
                "Admin {$admin->email} approved withdrawal {$withdrawal->reference_id} of ৳{$withdrawal->amount}",
                ['withdrawal_id' => $withdrawal->id, 'admin_id' => $admin->id, 'user_id' => $withdrawal->user_id, 'amount' => $withdrawal->amount]
            );

            Notification::create([
                'user_id'        => $withdrawal->user_id,
                'type'           => 'withdrawal_approved',
                'title'          => 'Withdrawal Approved',
                'message'        => "Your withdrawal request of ৳{$withdrawal->amount} (Ref: {$withdrawal->reference_id}) has been approved.",
                'reference_type' => WithdrawalRequest::class,
                'reference_id'   => $withdrawal->id,
            ]);

            return $withdrawal->fresh(['user', 'withdrawalMethod', 'reviewer']);
        });
    }

    /**
     * Admin: Reject a withdrawal request and atomically refund funds to user wallet.
     */
    public function rejectWithdrawal(int $id, User $admin, string $adminNote): WithdrawalRequest
    {
        return DB::transaction(function () use ($id, $admin, $adminNote) {
            $withdrawal = WithdrawalRequest::lockForUpdate()->with(['user', 'withdrawalMethod'])->findOrFail($id);

            if ($withdrawal->status !== 'pending') {
                throw new RuntimeException('This withdrawal has already been processed.');
            }

            $user = User::lockForUpdate()->findOrFail($withdrawal->user_id);
            $cleanAmount = DecimalSafeMath::canonicalize($withdrawal->amount, 2);

            $withdrawal->update([
                'status'      => 'rejected',
                'admin_note'  => $adminNote,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            AuditLogService::log(
                'finance.withdrawal_rejected',
                "Admin {$admin->email} rejected withdrawal {$withdrawal->reference_id} of ৳{$cleanAmount}. Reason: {$adminNote}",
                ['withdrawal_id' => $withdrawal->id, 'admin_id' => $admin->id, 'user_id' => $withdrawal->user_id, 'amount' => $cleanAmount, 'reason' => $adminNote]
            );

            $this->walletService->credit(
                $user,
                $cleanAmount,
                'withdrawal_rejected',
                WithdrawalRequest::class,
                $withdrawal->id,
                "Withdrawal rejected - funds refunded: {$withdrawal->reference_id}",
                "withdrawal_rejection_{$withdrawal->id}"
            );

            Notification::create([
                'user_id'        => $withdrawal->user_id,
                'type'           => 'withdrawal_rejected',
                'title'          => 'Withdrawal Rejected',
                'message'        => "Your withdrawal request of ৳{$cleanAmount} (Ref: {$withdrawal->reference_id}) was rejected. Funds have been refunded to your wallet. Reason: {$adminNote}",
                'reference_type' => WithdrawalRequest::class,
                'reference_id'   => $withdrawal->id,
            ]);

            return $withdrawal->fresh(['user', 'withdrawalMethod', 'reviewer']);
        });
    }
}

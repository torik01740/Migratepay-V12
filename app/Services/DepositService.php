<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Notification;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DepositService
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * Create a new deposit request for a user with optional proof in private storage.
     */
    public function createDeposit(User $user, array $data, $screenshotFile = null): Deposit
    {
        $amount = DecimalSafeMath::canonicalize($data['amount'], 2);

        if (DecimalSafeMath::comp($amount, '0.00') <= 0) {
            throw new RuntimeException("Deposit amount must be greater than zero.");
        }

        $screenshotPath = null;
        if ($screenshotFile) {
            $screenshotPath = $screenshotFile->store('deposits', 'private');
        } elseif (!empty($data['screenshot_path'])) {
            $screenshotPath = $data['screenshot_path'];
        }

        $deposit = Deposit::create([
            'user_id'         => $user->id,
            'amount'          => $amount,
            'gateway'         => $data['gateway'],
            'proof_note'      => $data['proof_note'] ?? null,
            'screenshot_path' => $screenshotPath,
            'status'          => 'pending',
            'admin_read'      => false,
        ]);

        return $deposit->load('user');
    }

    /**
     * Get user's deposit history.
     */
    public function getUserDeposits(int $userId)
    {
        return Deposit::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Admin: List all deposits with optional status filter.
     */
    public function getAdminDeposits(?string $status = null)
    {
        $query = Deposit::with(['user', 'reviewer'])->orderBy('created_at', 'desc');

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Mark a deposit as read by admin.
     */
    public function markAsRead(int $id): Deposit
    {
        $deposit = Deposit::findOrFail($id);
        $deposit->update(['admin_read' => true]);
        return $deposit;
    }

    /**
     * Admin approves deposit: atomically credits wallet using WalletService and records transaction.
     */
    public function approveDeposit(int $id, User $admin, ?string $adminNote = null): array
    {
        return DB::transaction(function () use ($id, $admin, $adminNote) {
            $deposit = Deposit::lockForUpdate()->findOrFail($id);

            if ($deposit->status !== 'pending') {
                throw new RuntimeException("This deposit has already been {$deposit->status}.");
            }

            $user = User::lockForUpdate()->findOrFail($deposit->user_id);
            $cleanAmount = DecimalSafeMath::canonicalize($deposit->amount, 2);

            $deposit->update([
                'status'      => 'approved',
                'admin_note'  => $adminNote,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            AuditLogService::log(
                'finance.deposit_approved',
                "Admin {$admin->email} approved deposit ID {$deposit->id} of ৳{$cleanAmount} ({$deposit->gateway})",
                ['deposit_id' => $deposit->id, 'admin_id' => $admin->id, 'user_id' => $user->id, 'amount' => $cleanAmount]
            );

            $transaction = $this->walletService->credit(
                $user,
                $cleanAmount,
                'deposit',
                Deposit::class,
                $deposit->id,
                "Deposit approved via {$deposit->gateway}. Amount: ৳{$cleanAmount}",
                "deposit_approval_{$deposit->id}"
            );

            Notification::create([
                'user_id'        => $user->id,
                'type'           => 'deposit_approved',
                'title'          => 'Deposit Approved',
                'message'        => "Your deposit of ৳{$cleanAmount} via {$deposit->gateway} has been approved. Your wallet has been credited.",
                'reference_type' => Deposit::class,
                'reference_id'   => $deposit->id,
            ]);

            return [
                'deposit'        => $deposit->fresh(['user', 'reviewer']),
                'wallet_balance' => $this->walletService->getBalance($user->id),
                'transaction'    => $transaction,
            ];
        });
    }

    /**
     * Admin rejects deposit: updates status without modifying wallet balance.
     */
    public function rejectDeposit(int $id, User $admin, string $adminNote): Deposit
    {
        return DB::transaction(function () use ($id, $admin, $adminNote) {
            $deposit = Deposit::lockForUpdate()->findOrFail($id);

            if ($deposit->status !== 'pending') {
                throw new RuntimeException("This deposit has already been {$deposit->status}.");
            }

            $deposit->update([
                'status'      => 'rejected',
                'admin_note'  => $adminNote,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            AuditLogService::log(
                'finance.deposit_rejected',
                "Admin {$admin->email} rejected deposit ID {$deposit->id} of ৳{$deposit->amount}. Reason: {$adminNote}",
                ['deposit_id' => $deposit->id, 'admin_id' => $admin->id, 'user_id' => $deposit->user_id, 'amount' => $deposit->amount, 'reason' => $adminNote]
            );

            Notification::create([
                'user_id'        => $deposit->user_id,
                'type'           => 'deposit_rejected',
                'title'          => 'Deposit Rejected',
                'message'        => "Your deposit of ৳{$deposit->amount} via {$deposit->gateway} was rejected. Reason: {$adminNote}",
                'reference_type' => Deposit::class,
                'reference_id'   => $deposit->id,
            ]);

            return $deposit->fresh(['user', 'reviewer']);
        });
    }

    /**
     * Store proof screenshot securely in private disk.
     */
    public function storeScreenshot($file): array
    {
        $path = $file->store('deposits', 'private');

        return [
            'path' => $path,
            'url'  => route('private.files.stream', ['type' => 'deposit', 'id' => 0]),
        ];
    }
}

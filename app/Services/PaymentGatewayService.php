<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\Deposit;
use App\Models\PaymentGateway;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PaymentGatewayService
{
    public function __construct(
        protected WalletService $walletService,
        protected DealService $dealService
    ) {}

    /**
     * Initiate a gateway checkout session.
     */
    public function createCheckout(User $user, string $gatewaySlug, string $amount, string $purpose = 'deposit', ?int $dealId = null): array
    {
        $gateway = PaymentGateway::where('slug', $gatewaySlug)->first();
        if (!$gateway || !$gateway->is_active) {
            throw new RuntimeException("Payment gateway '{$gatewaySlug}' is not active or unavailable.");
        }

        $cleanAmount = DecimalSafeMath::canonicalize($amount, 2);
        if (DecimalSafeMath::comp($cleanAmount, '10.00') < 0) {
            throw new RuntimeException("Minimum payment amount is ৳10.00.");
        }

        // Calculate gateway fee
        $feePct = DecimalSafeMath::canonicalize($gateway->fee_percentage ?? '0.00', 2);
        $fraction = DecimalSafeMath::div($feePct, '100.00', 4);
        $gatewayFee = DecimalSafeMath::mul($cleanAmount, $fraction, 2);
        $totalPayable = DecimalSafeMath::add($cleanAmount, $gatewayFee, 2);

        $paymentRef = 'PAY-' . strtoupper(substr(uniqid(), -8));

        // Create pending deposit or deal payment record
        $deposit = null;
        if ($purpose === 'deposit') {
            $deposit = Deposit::create([
                'user_id'    => $user->id,
                'amount'     => $cleanAmount,
                'gateway'    => $gatewaySlug,
                'proof_note' => "Initiated online checkout Ref: {$paymentRef}",
                'status'     => 'pending',
                'admin_read' => false,
            ]);
        }

        // Return session info with verification hash
        $secretKey = $gateway->secret_key ?: config('app.key');
        $signature = hash_hmac('sha256', "{$paymentRef}|{$cleanAmount}|{$gatewaySlug}|{$user->id}", $secretKey);

        return [
            'payment_reference' => $paymentRef,
            'deposit_id'        => $deposit?->id,
            'deal_id'           => $dealId,
            'amount'            => $cleanAmount,
            'fee'               => $gatewayFee,
            'total_payable'     => $totalPayable,
            'currency'          => $gateway->currency ?? 'BDT',
            'gateway'           => $gateway->toPublicArray(),
            'signature'         => $signature,
            'checkout_url'      => route('payments.checkout.redirect', ['ref' => $paymentRef]),
        ];
    }

    /**
     * Process gateway webhook or server callback idempotently.
     */
    public function processCallback(string $gatewaySlug, array $payload, ?string $signatureHeader = null): array
    {
        $gateway = PaymentGateway::where('slug', $gatewaySlug)->first();
        if (!$gateway) {
            throw new RuntimeException("Payment gateway '{$gatewaySlug}' not found.");
        }

        $secretKey = $gateway->secret_key ?: config('app.key');

        // Verify signature if provided
        if ($signatureHeader) {
            $expectedSignature = hash_hmac('sha256', json_encode($payload), $secretKey);
            if (!hash_equals($expectedSignature, $signatureHeader)) {
                Log::warning("PaymentGateway webhook signature mismatch for {$gatewaySlug}");
                throw new RuntimeException("Invalid webhook signature.");
            }
        }

        $paymentRef = $payload['payment_reference'] ?? $payload['tran_id'] ?? null;
        $status = strtolower($payload['status'] ?? '');
        $amount = isset($payload['amount']) ? DecimalSafeMath::canonicalize($payload['amount'], 2) : '0.00';
        $userId = (int)($payload['user_id'] ?? 0);
        $depositId = (int)($payload['deposit_id'] ?? 0);

        if (!$paymentRef) {
            throw new RuntimeException("Missing payment reference in gateway callback.");
        }

        // Check if transaction with this idempotency key was already completed
        $existingTxn = Transaction::where('description', 'LIKE', "%[PAYMENT_REF:{$paymentRef}]%")->first();
        if ($existingTxn) {
            return [
                'success' => true,
                'status'  => 'already_processed',
                'message' => 'Payment has already been processed successfully.',
                'transaction_id' => $existingTxn->id,
            ];
        }

        if (!in_array($status, ['success', 'completed', 'paid', 'valid'])) {
            return [
                'success' => false,
                'status'  => 'failed',
                'message' => "Payment status reported as '{$status}'.",
            ];
        }

        return DB::transaction(function () use ($gateway, $paymentRef, $amount, $userId, $depositId) {
            $user = User::lockForUpdate()->find($userId);
            if (!$user && $depositId) {
                $deposit = Deposit::find($depositId);
                if ($deposit) {
                    $user = User::lockForUpdate()->find($deposit->user_id);
                }
            }

            if (!$user) {
                throw new RuntimeException("User account not found for payment reference {$paymentRef}");
            }

            // Update deposit if matching deposit exists
            if ($depositId) {
                $deposit = Deposit::lockForUpdate()->find($depositId);
                if ($deposit && $deposit->status === 'pending') {
                    $deposit->update([
                        'status'      => 'approved',
                        'admin_note'  => "Automated gateway settlement (Ref: {$paymentRef})",
                        'reviewed_at' => now(),
                    ]);
                }
            }

            // Credit wallet idempotently
            $transaction = $this->walletService->credit(
                $user,
                $amount,
                'deposit',
                Deposit::class,
                $depositId ?: 0,
                "Online payment deposit via {$gateway->name} [PAYMENT_REF:{$paymentRef}]",
                "payment_{$paymentRef}"
            );

            return [
                'success'        => true,
                'status'         => 'credited',
                'payment_ref'    => $paymentRef,
                'amount'         => $amount,
                'wallet_balance' => $this->walletService->getBalance($user->id),
                'transaction_id' => $transaction->id,
            ];
        });
    }
}

<?php

namespace Tests\Feature\Domain;

use App\Models\Deal;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;

class WalletServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walletService = app(WalletService::class);
    }

    public function test_credit_increments_balance_and_creates_transaction(): void
    {
        $user = User::factory()->create(['wallet_balance' => '100.00']);

        $txn = $this->walletService->credit(
            $user,
            '50.25',
            'deposit',
            User::class,
            $user->id,
            'Test credit deposit'
        );

        $this->assertEquals('150.25', $this->walletService->getBalance($user->id));
        $this->assertEquals('100.00', $txn->balance_before);
        $this->assertEquals('150.25', $txn->balance_after);
        $this->assertEquals('50.25', $txn->amount);
        $this->assertDatabaseHas('transactions', [
            'id' => $txn->id,
            'user_id' => $user->id,
            'amount' => '50.25',
        ]);
    }

    public function test_debit_decrements_balance_and_creates_transaction(): void
    {
        $user = User::factory()->create(['wallet_balance' => '200.00']);

        $txn = $this->walletService->debit(
            $user,
            '75.50',
            'withdrawal',
            User::class,
            $user->id,
            'Test debit withdrawal'
        );

        $this->assertEquals('124.50', $this->walletService->getBalance($user->id));
        $this->assertEquals('200.00', $txn->balance_before);
        $this->assertEquals('124.50', $txn->balance_after);
        $this->assertEquals('75.50', $txn->amount);
    }

    public function test_insufficient_funds_throws_exception_and_prevents_deduction(): void
    {
        $user = User::factory()->create(['wallet_balance' => '50.00']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Insufficient funds');

        $this->walletService->debit(
            $user,
            '100.00',
            'withdrawal',
            User::class,
            $user->id,
            'Should fail'
        );

        $this->assertEquals('50.00', $this->walletService->getBalance($user->id));
    }

    public function test_negative_or_zero_amount_is_strictly_rejected(): void
    {
        $user = User::factory()->create(['wallet_balance' => '50.00']);

        $this->expectException(InvalidArgumentException::class);
        $this->walletService->credit(
            $user,
            '-10.00',
            'deposit',
            User::class,
            $user->id,
            'Should fail'
        );
    }

    public function test_idempotency_key_prevents_duplicate_transactions(): void
    {
        $user = User::factory()->create(['wallet_balance' => '100.00']);

        $txn1 = $this->walletService->credit(
            $user,
            '25.00',
            'deposit',
            User::class,
            $user->id,
            'Test Deposit',
            'DEP-UNIQUE-123'
        );

        // Attempting duplicate credit with same idempotency key
        $txn2 = $this->walletService->credit(
            $user,
            '25.00',
            'deposit',
            User::class,
            $user->id,
            'Test Deposit',
            'DEP-UNIQUE-123'
        );

        $this->assertEquals($txn1->id, $txn2->id);
        $this->assertEquals('125.00', $this->walletService->getBalance($user->id));
        $this->assertDatabaseCount('transactions', 1);
    }
}

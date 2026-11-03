<?php

namespace Tests\Feature\Domain;

use App\Models\Deposit;
use App\Models\User;
use App\Services\DepositService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class DepositWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected DepositService $depositService;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
        $this->depositService = app(DepositService::class);
    }

    public function test_customer_can_submit_deposit_request(): void
    {
        $user = User::factory()->create(['wallet_balance' => '0.00']);
        $file = UploadedFile::fake()->image('receipt.png');

        $response = $this->actingAs($user)->postJson('/api/deposits', [
            'amount'     => '500.00',
            'gateway'    => 'bkash',
            'proof_note' => 'TrxID: 9X8A7B6C',
            'screenshot' => $file,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('deposit.amount', '500.00');
        $response->assertJsonPath('deposit.status', 'pending');

        $this->assertDatabaseHas('deposits', [
            'user_id' => $user->id,
            'amount'  => '500.00',
            'gateway' => 'bkash',
            'status'  => 'pending',
        ]);
    }

    public function test_admin_approval_credits_wallet_atomically(): void
    {
        $user = User::factory()->create(['wallet_balance' => '100.00']);
        $admin = User::factory()->create(['role' => 'admin']);

        $deposit = Deposit::create([
            'user_id' => $user->id,
            'amount'  => '250.00',
            'gateway' => 'bank_transfer',
            'status'  => 'pending',
        ]);

        $response = $this->actingAs($admin)->postJson("/api/admin/deposits/{$deposit->id}/approve", [
            'admin_note' => 'Payment verified in corporate bank statement.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('wallet_balance', '350.00');

        $this->assertDatabaseHas('deposits', [
            'id'          => $deposit->id,
            'status'      => 'approved',
            'reviewed_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type'    => 'deposit',
            'amount'  => '250.00',
        ]);

        $this->assertEquals('350.00', $user->fresh()->wallet_balance);
    }

    public function test_admin_cannot_approve_already_approved_deposit(): void
    {
        $user = User::factory()->create(['wallet_balance' => '100.00']);
        $admin = User::factory()->create(['role' => 'admin']);

        $deposit = Deposit::create([
            'user_id' => $user->id,
            'amount'  => '250.00',
            'gateway' => 'bank_transfer',
            'status'  => 'pending',
        ]);

        $this->depositService->approveDeposit($deposit->id, $admin);

        // Second approval attempt must fail
        $this->expectException(RuntimeException::class);
        $this->depositService->approveDeposit($deposit->id, $admin);

        // Wallet should only have been credited once
        $this->assertEquals('350.00', $user->fresh()->wallet_balance);
    }

    public function test_admin_rejection_does_not_credit_wallet(): void
    {
        $user = User::factory()->create(['wallet_balance' => '100.00']);
        $admin = User::factory()->create(['role' => 'admin']);

        $deposit = Deposit::create([
            'user_id' => $user->id,
            'amount'  => '500.00',
            'gateway' => 'bkash',
            'status'  => 'pending',
        ]);

        $response = $this->actingAs($admin)->postJson("/api/admin/deposits/{$deposit->id}/reject", [
            'admin_note' => 'Fake TrxID provided.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('deposit.status', 'rejected');

        $this->assertEquals('100.00', $user->fresh()->wallet_balance);
        $this->assertDatabaseMissing('transactions', [
            'user_id' => $user->id,
            'amount'  => '500.00',
        ]);
    }
}

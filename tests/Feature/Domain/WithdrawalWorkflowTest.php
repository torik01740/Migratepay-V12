<?php

namespace Tests\Feature\Domain;

use App\Models\User;
use App\Models\WithdrawalMethod;
use App\Models\WithdrawalRequest;
use App\Services\WithdrawalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected WithdrawalService $withdrawalService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withdrawalService = app(WithdrawalService::class);
    }

    public function test_customer_can_store_and_manage_withdrawal_methods(): void
    {
        $user = User::factory()->create();

        // Bank method
        $response = $this->actingAs($user)->postJson('/api/withdrawal-methods', [
            'type'           => 'bank',
            'bank_name'      => 'City Bank Ltd',
            'branch'         => 'Gulshan',
            'routing_number' => '12345678',
            'account_name'   => 'John Doe',
            'account_number' => '987654321',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('withdrawal_methods', [
            'user_id'   => $user->id,
            'type'      => 'bank',
            'bank_name' => 'City Bank Ltd',
        ]);

        // Mobile method
        $response2 = $this->actingAs($user)->postJson('/api/withdrawal-methods', [
            'type'            => 'mobile',
            'mobile_provider' => 'bKash',
            'mobile_number'   => '01700000000',
        ]);

        $response2->assertStatus(200);
        $this->assertDatabaseHas('withdrawal_methods', [
            'user_id'         => $user->id,
            'type'            => 'mobile',
            'mobile_provider' => 'bKash',
        ]);
    }

    public function test_request_withdrawal_debits_wallet_and_applies_correct_fee(): void
    {
        $user = User::factory()->create(['wallet_balance' => '1000.00']);
        $method = WithdrawalMethod::create([
            'user_id'         => $user->id,
            'type'            => 'mobile',
            'mobile_provider' => 'bKash',
            'mobile_number'   => '01700000000',
        ]);

        $response = $this->actingAs($user)->postJson('/api/withdrawals', [
            'withdrawal_method_id' => $method->id,
            'amount'               => '500.00',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('withdrawal.amount', '500.00');
        $response->assertJsonPath('withdrawal.fee', '7.50'); // 1.5% of 500 = 7.50
        $response->assertJsonPath('withdrawal.net_amount', '492.50');
        $response->assertJsonPath('wallet_balance', '500.00');

        $this->assertEquals('500.00', $user->fresh()->wallet_balance);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type'    => 'withdrawal',
            'amount'  => '500.00',
        ]);
    }

    public function test_insufficient_balance_rejects_withdrawal(): void
    {
        $user = User::factory()->create(['wallet_balance' => '100.00']);
        $method = WithdrawalMethod::create([
            'user_id'         => $user->id,
            'type'            => 'mobile',
            'mobile_provider' => 'bKash',
            'mobile_number'   => '01700000000',
        ]);

        $response = $this->actingAs($user)->postJson('/api/withdrawals', [
            'withdrawal_method_id' => $method->id,
            'amount'               => '500.00',
        ]);

        $response->assertStatus(500); // Exception intercepted
        $this->assertEquals('100.00', $user->fresh()->wallet_balance);
    }

    public function test_admin_rejection_refunds_funds_back_to_wallet(): void
    {
        $user = User::factory()->create(['wallet_balance' => '1000.00']);
        $admin = User::factory()->create(['role' => 'admin']);

        $method = WithdrawalMethod::create([
            'user_id'         => $user->id,
            'type'            => 'bank',
            'bank_name'       => 'City Bank',
            'account_number'  => '12345678',
        ]);

        $result = $this->withdrawalService->requestWithdrawal($user, [
            'withdrawal_method_id' => $method->id,
            'amount'               => '400.00',
        ]);

        $withdrawal = $result['withdrawal'];
        $this->assertEquals('600.00', $user->fresh()->wallet_balance);

        // Admin rejects the withdrawal
        $response = $this->actingAs($admin)->postJson("/api/admin/withdrawals/{$withdrawal->id}/reject", [
            'admin_note' => 'Invalid bank routing number.',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('withdrawal_requests', [
            'id'     => $withdrawal->id,
            'status' => 'rejected',
        ]);

        // Balance should be restored to 1000.00
        $this->assertEquals('1000.00', $user->fresh()->wallet_balance);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type'    => 'withdrawal_rejected',
            'amount'  => '400.00',
        ]);
    }
}

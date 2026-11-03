<?php

namespace Tests\Feature\Domain;

use App\Models\Deposit;
use App\Models\PaymentGateway;
use App\Models\User;
use App\Services\PaymentGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentGatewayWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentGatewayService $gatewayService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gatewayService = app(PaymentGatewayService::class);
    }

    public function test_initiate_checkout_creates_pending_record_and_signature(): void
    {
        $user = User::factory()->create();
        $gateway = PaymentGateway::create([
            'name'           => 'bKash Merchant',
            'slug'           => 'bkash',
            'fee_percentage' => '1.50',
            'is_active'      => true,
            'secret_key'     => 'test_bkash_secret_123',
        ]);

        $response = $this->actingAs($user)->postJson('/api/payments/checkout', [
            'gateway' => 'bkash',
            'amount'  => '1000.00',
            'purpose' => 'deposit',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('checkout.amount', '1000.00');
        $response->assertJsonPath('checkout.fee', '15.00'); // 1.5% of 1000 = 15.00
        $response->assertJsonPath('checkout.total_payable', '1015.00');
        $this->assertNotNull($response->json('checkout.payment_reference'));
        $this->assertNotNull($response->json('checkout.signature'));

        $this->assertDatabaseHas('deposits', [
            'user_id' => $user->id,
            'amount'  => '1000.00',
            'status'  => 'pending',
        ]);
    }

    public function test_valid_webhook_callback_credits_wallet_idempotently(): void
    {
        $user = User::factory()->create(['wallet_balance' => '100.00']);
        $gateway = PaymentGateway::create([
            'name'       => 'Nagad Online',
            'slug'       => 'nagad',
            'is_active'  => true,
            'secret_key' => 'nagad_secret_key_456',
        ]);

        $deposit = Deposit::create([
            'user_id' => $user->id,
            'amount'  => '500.00',
            'gateway' => 'nagad',
            'status'  => 'pending',
        ]);

        $payload = [
            'payment_reference' => 'PAY-NAGAD-998877',
            'status'            => 'success',
            'amount'            => '500.00',
            'user_id'           => $user->id,
            'deposit_id'        => $deposit->id,
        ];

        $signature = hash_hmac('sha256', json_encode($payload), 'nagad_secret_key_456');

        $response = $this->withHeader('X-Signature', $signature)
            ->postJson('/api/payments/webhook/nagad', $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('status', 'credited');
        $response->assertJsonPath('wallet_balance', '600.00'); // 100 + 500 = 600

        $this->assertEquals('600.00', $user->fresh()->wallet_balance);
        $this->assertEquals('approved', $deposit->fresh()->status);

        // Send duplicate webhook callback
        $dupResponse = $this->withHeader('X-Signature', $signature)
            ->postJson('/api/payments/webhook/nagad', $payload);

        $dupResponse->assertStatus(200);
        $dupResponse->assertJsonPath('status', 'already_processed');

        // Balance should STILL be 600.00 (Zero duplicate credit)
        $this->assertEquals('600.00', $user->fresh()->wallet_balance);
    }

    public function test_invalid_signature_rejects_webhook(): void
    {
        $user = User::factory()->create(['wallet_balance' => '0.00']);
        PaymentGateway::create([
            'name'       => 'SSLCommerz',
            'slug'       => 'sslcommerz',
            'is_active'  => true,
            'secret_key' => 'real_secret_789',
        ]);

        $payload = [
            'payment_reference' => 'PAY-SSL-112233',
            'status'            => 'success',
            'amount'            => '1000.00',
            'user_id'           => $user->id,
        ];

        $response = $this->withHeader('X-Signature', 'tampered_signature_xyz')
            ->postJson('/api/payments/webhook/sslcommerz', $payload);

        $response->assertStatus(500); // Invalid signature throws exception
        $this->assertEquals('0.00', $user->fresh()->wallet_balance);
    }
}

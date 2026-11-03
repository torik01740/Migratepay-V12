<?php

namespace Tests\Feature\Security;

use App\Models\PaymentGateway;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GatewayVaultTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_gateway_endpoint_returns_only_public_fields(): void
    {
        PaymentGateway::create([
            'name' => 'Stripe Checkout',
            'slug' => 'stripe',
            'icon' => 'stripe-icon',
            'currency' => 'USD',
            'instructions' => 'Pay via credit card',
            'fee_percentage' => '2.90',
            'fee_fixed' => '0.30',
            'api_key' => 'pk_live_super_secret_public_key',
            'secret_key' => 'sk_live_super_confidential_secret_key',
            'store_password' => 'store_password_123',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/public/gateway-settings');
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertNotEmpty($data);

        $gateway = $data[0];
        $this->assertEquals('Stripe Checkout', $gateway['name']);
        $this->assertEquals('stripe', $gateway['slug']);

        // Assert strictly no sensitive keys exist in public response
        $this->assertArrayNotHasKey('api_key', $gateway);
        $this->assertArrayNotHasKey('secret_key', $gateway);
        $this->assertArrayNotHasKey('store_password', $gateway);

        $content = $response->getContent();
        $this->assertStringNotContainsString('sk_live_super_confidential_secret_key', $content);
        $this->assertStringNotContainsString('store_password_123', $content);
    }

    public function test_credentials_are_encrypted_at_rest_in_database(): void
    {
        $gateway = PaymentGateway::create([
            'name' => 'bKash Merchant',
            'slug' => 'bkash',
            'api_key' => 'bkash_app_key_2026',
            'secret_key' => 'bkash_app_secret_very_confidential',
            'store_password' => 'bkash_merchant_password_999',
            'is_active' => true,
        ]);

        // Query raw database row directly via DB::table (bypassing Eloquent decrypt cast)
        $rawRow = DB::table('payment_gateways')->where('id', $gateway->id)->first();

        // Raw values in DB must NOT be plaintext
        $this->assertNotEquals('bkash_app_secret_very_confidential', $rawRow->secret_key);
        $this->assertNotEquals('bkash_merchant_password_999', $rawRow->store_password);

        // Accessing through Eloquent decrypts seamlessly
        $gateway->refresh();
        $this->assertEquals('bkash_app_secret_very_confidential', $gateway->secret_key);
        $this->assertEquals('bkash_merchant_password_999', $gateway->store_password);
    }

    public function test_admin_can_update_gateway(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $gateway = PaymentGateway::create([
            'name' => 'Nagad',
            'slug' => 'nagad',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->postJson("/api/admin/gateway-settings/{$gateway->id}", [
            'name' => 'Nagad Payout',
            'instructions' => 'Updated payout instructions',
            'secret_key' => 'new_nagad_secret_key_vault',
        ]);

        $response->assertStatus(200);

        $gateway->refresh();
        $this->assertEquals('Nagad Payout', $gateway->name);
        $this->assertEquals('new_nagad_secret_key_vault', $gateway->secret_key);
    }
}

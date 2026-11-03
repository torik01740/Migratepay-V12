<?php

namespace Tests\Feature\Database;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Deal;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_22_canonical_domain_tables_exist(): void
    {
        $expectedTables = [
            'users',
            'deals',
            'transactions',
            'deposits',
            'withdrawal_methods',
            'withdrawal_requests',
            'reports',
            'reviews',
            'support_tickets',
            'support_ticket_messages',
            'categories',
            'coupons',
            'coupon_usages',
            'banners',
            'notifications',
            'password_reset_tokens',
            'ai_settings',
            'ai_knowledge_bases',
            'ai_chat_histories',
            'payment_gateways',
            'system_settings',
            'cms_pages',
            'blog_posts',
        ];

        foreach ($expectedTables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Expected table '{$table}' does not exist in schema.");
        }
    }

    public function test_user_wallet_balance_enforces_decimal_precision(): void
    {
        $user = User::factory()->create([
            'wallet_balance' => '7887.50',
        ]);

        $this->assertEquals('7887.50', (string)$user->wallet_balance);
        $this->assertIsString((string)$user->wallet_balance);
    }

    public function test_deal_and_transaction_relational_integrity(): void
    {
        $initiator = User::factory()->create();
        $counterparty = User::factory()->create();

        $category = Category::create(['name' => 'Web Dev', 'slug' => 'web-dev']);
        $coupon = Coupon::create(['code' => 'DISC10', 'discount_value' => '10.00']);

        $deal = Deal::create([
            'initiator_id' => $initiator->id,
            'counterparty_id' => $counterparty->id,
            'deal_id' => 'DX-TEST-999',
            'title' => 'Test Relational Deal',
            'amount' => '12000.00',
            'buyer_pays' => '12240.00',
            'seller_gets' => '11760.00',
            'charge_total' => '480.00',
            'counterparty_email' => $counterparty->email,
            'category_id' => $category->id,
            'coupon_id' => $coupon->id,
            'status' => 'funds_secured',
        ]);

        $transaction = Transaction::create([
            'user_id' => $initiator->id,
            'type' => 'escrow_lock',
            'amount' => '12240.00',
            'balance_before' => '15000.00',
            'balance_after' => '2760.00',
            'reference_type' => Deal::class,
            'reference_id' => $deal->id,
            'description' => 'Escrow funded for deal DX-TEST-999',
        ]);

        $this->assertEquals($initiator->id, $deal->initiator->id);
        $this->assertEquals($counterparty->id, $deal->counterparty->id);
        $this->assertEquals($deal->id, $transaction->reference->id);
    }
}

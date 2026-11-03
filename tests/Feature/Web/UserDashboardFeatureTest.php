<?php

namespace Tests\Feature\Web;

use App\Models\User;
use App\Models\Category;
use App\Models\PaymentGateway;
use App\Models\Deal;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserDashboardFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'username' => 'clientuser',
            'name' => 'Client User',
            'email' => 'client@migratepay.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'wallet_balance' => 12500.00,
            'status' => 'Active',
            'verification_status' => 'VERIFIED',
        ]);
    }

    public function test_customer_can_render_dashboard_shell(): void
    {
        $response = $this->actingAs($this->customer)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Customer Dashboard | MigratePay Escrow');
        $response->assertSee('data-portal="user"', false);
    }

    public function test_customer_can_query_user_profile(): void
    {
        $response = $this->actingAs($this->customer)->getJson('/api/user/profile');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'user' => [
                'username' => 'clientuser',
                'email' => 'client@migratepay.com',
                'verification_status' => 'VERIFIED',
            ],
        ]);
    }

    public function test_customer_can_update_profile_information(): void
    {
        $response = $this->actingAs($this->customer)->postJson('/api/user/profile', [
            'name' => 'Updated Client Name',
            'phone' => '+8801700123456',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $this->customer->id,
            'name' => 'Updated Client Name',
            'phone' => '+8801700123456',
        ]);
    }

    public function test_customer_can_change_password(): void
    {
        $response = $this->actingAs($this->customer)->postJson('/api/user/change-password', [
            'current_password' => 'Secret123!',
            'new_password' => 'NewSecret456!',
            'new_password_confirmation' => 'NewSecret456!',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->customer->refresh();
        $this->assertTrue(Hash::check('NewSecret456!', $this->customer->password));
    }

    public function test_customer_can_retrieve_deals_list(): void
    {
        $seller = User::factory()->create(['role' => 'user', 'email' => 'seller@example.com']);

        Deal::create([
            'deal_id' => 'DL-TEST-001',
            'title' => 'Web App Development Contract',
            'initiator_id' => $this->customer->id,
            'counterparty_id' => $seller->id,
            'counterparty_email' => 'seller@example.com',
            'initiator_role' => 'buyer',
            'amount' => 5000.00,
            'buyer_pays' => 5150.00,
            'seller_gets' => 5000.00,
            'charge_total' => 150.00,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->customer)->getJson('/api/deals');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'deals']);
        $this->assertCount(1, $response->json('deals'));
    }

    public function test_customer_can_retrieve_public_categories_for_deal_creation(): void
    {
        Category::create([
            'name' => 'Software & Technology',
            'slug' => 'software-tech',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->customer)->getJson('/api/categories');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'categories']);
        $this->assertGreaterThanOrEqual(1, count($response->json('categories')));
    }

    public function test_customer_can_retrieve_transactions_history(): void
    {
        $response = $this->actingAs($this->customer)->getJson('/api/transactions');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['success', 'data']);
    }

    public function test_customer_can_retrieve_deposit_methods(): void
    {
        $response = $this->actingAs($this->customer)->getJson('/api/deposit-methods');

        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_customer_can_retrieve_withdrawal_methods(): void
    {
        $response = $this->actingAs($this->customer)->getJson('/api/withdrawal-methods');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_customer_can_retrieve_and_create_support_tickets(): void
    {
        $listResponse = $this->actingAs($this->customer)->getJson('/api/support/tickets');
        $listResponse->assertStatus(200);
        $this->assertIsArray($listResponse->json());

        $createResponse = $this->actingAs($this->customer)->postJson('/api/support/tickets', [
            'subject' => 'Need help with escrow inspection timeline',
            'message' => 'Please explain the dispute window duration for milestones.',
            'priority' => 'medium',
        ]);

        $createResponse->assertStatus(201);
        $createResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('support_tickets', [
            'user_id' => $this->customer->id,
            'subject' => 'Need help with escrow inspection timeline',
        ]);
    }

    public function test_customer_cannot_access_admin_portal(): void
    {
        $response = $this->actingAs($this->customer)->get('/admin');
        $response->assertRedirect('/auth?role=admin');
    }
}

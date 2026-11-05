<?php

namespace Tests\Feature\Web;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Deal;
use App\Models\Deposit;
use App\Models\PaymentGateway;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'username' => 'sysadmin',
            'name' => 'System Admin',
            'email' => 'admin@migratepay.com',
            'password' => Hash::make('AdminSecret123!'),
            'role' => 'admin',
            'status' => 'Active',
            'verification_status' => 'VERIFIED',
        ]);

        $this->customer = User::factory()->create([
            'username' => 'clientuser',
            'name' => 'Client User',
            'email' => 'client@migratepay.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'wallet_balance' => 10000.00,
            'status' => 'Active',
            'verification_status' => 'VERIFIED',
        ]);
    }

    public function test_guest_is_redirected_from_admin_shell(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/auth?role=admin');
    }

    public function test_regular_user_is_forbidden_from_admin_shell(): void
    {
        $response = $this->actingAs($this->customer)->get('/admin');
        $response->assertRedirect('/auth?role=admin');
    }

    public function test_admin_can_render_admin_dashboard_shell(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('MigratePay | Control Tower Admin');
        $response->assertSee('data-portal="admin"', false);
    }

    public function test_regular_user_is_forbidden_from_admin_api(): void
    {
        $endpoints = [
            '/api/admin/users',
            '/api/admin/deposits',
            '/api/admin/withdrawals',
            '/api/admin/deals',
            '/api/admin/gateway-settings',
            '/api/admin/analytics/dashboard',
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->actingAs($this->customer)->getJson($endpoint);
            $response->assertStatus(403);
        }
    }

    public function test_admin_can_query_users_directory(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/api/admin/users');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'users' => [
                '*' => ['id', 'username', 'email', 'role', 'status'],
            ],
        ]);
    }

    public function test_admin_can_update_user_status(): void
    {
        $targetUser = User::factory()->create([
            'status' => 'Active',
            'role' => 'user',
        ]);

        $response = $this->actingAs($this->admin)->putJson("/api/admin/users/{$targetUser->id}/status", [
            'status' => 'Suspended',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'status' => 'Suspended',
        ]);
    }

    public function test_admin_can_approve_national_kyc_verification(): void
    {
        $targetUser = User::factory()->create([
            'verification_status' => 'PENDING',
            'role' => 'user',
        ]);

        $response = $this->actingAs($this->admin)->putJson("/api/admin/users/{$targetUser->id}/verification", [
            'verification_status' => 'VERIFIED',
            'rejection_reason' => '',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'verification_status' => 'VERIFIED',
        ]);
    }

    public function test_admin_can_list_and_approve_deposit_crediting_wallet(): void
    {
        $depositUser = User::factory()->create([
            'wallet_balance' => 500.00,
            'role' => 'user',
        ]);

        $deposit = Deposit::create([
            'user_id' => $depositUser->id,
            'amount' => 2000.00,
            'gateway' => 'bKash',
            'proof_note' => 'BKS-TEST-9988',
            'status' => 'pending',
            'screenshot_path' => 'receipts/test.png',
        ]);

        $response = $this->actingAs($this->admin)->postJson("/api/admin/deposits/{$deposit->id}/approve", [
            'admin_note' => 'bKash receipt confirmed verified.',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'status' => 'approved',
        ]);

        // User wallet balance must be updated from 500 to 2500
        $this->assertDatabaseHas('users', [
            'id' => $depositUser->id,
            'wallet_balance' => 2500.00,
        ]);
    }

    public function test_admin_can_reject_withdrawal_and_refund_wallet(): void
    {
        $withdrawUser = User::factory()->create([
            'wallet_balance' => 1000.00,
            'role' => 'user',
        ]);

        // Payout initiated previously deducted 3000 from wallet
        $withdrawal = WithdrawalRequest::create([
            'reference_id' => 'WDR-TEST-001',
            'user_id' => $withdrawUser->id,
            'amount' => 3000.00,
            'fee' => 0.00,
            'net_amount' => 3000.00,
            'type' => 'bank',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->postJson("/api/admin/withdrawals/{$withdrawal->id}/reject", [
            'admin_note' => 'Account holder name mismatch on bank record.',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('withdrawal_requests', [
            'id' => $withdrawal->id,
            'status' => 'rejected',
        ]);

        // Refund must be returned to user wallet: 1000 + 3000 = 4000
        $this->assertDatabaseHas('users', [
            'id' => $withdrawUser->id,
            'wallet_balance' => 4000.00,
        ]);
    }

    public function test_admin_can_crud_deal_category(): void
    {
        // 1. Create Category
        $createRes = $this->actingAs($this->admin)->postJson('/api/admin/categories', [
            'name' => 'Digital Assets & Code',
            'slug' => 'digital-assets-code',
            'description' => 'Escrow for website code, apps, and domains.',
            'is_active' => 1,
        ]);

        $createRes->assertStatus(201);
        $categoryId = $createRes->json('category.id');

        $this->assertDatabaseHas('categories', [
            'id' => $categoryId,
            'slug' => 'digital-assets-code',
        ]);

        // 2. Update Category
        $updateRes = $this->actingAs($this->admin)->putJson("/api/admin/categories/{$categoryId}", [
            'name' => 'Digital Assets & Source Code',
            'slug' => 'digital-assets-source-code',
            'description' => 'Updated classification.',
            'is_active' => 1,
        ]);

        $updateRes->assertStatus(200);
        $this->assertDatabaseHas('categories', [
            'id' => $categoryId,
            'name' => 'Digital Assets & Source Code',
        ]);

        // 3. Delete Category
        $deleteRes = $this->actingAs($this->admin)->deleteJson("/api/admin/categories/{$categoryId}");
        $deleteRes->assertStatus(200);
        $this->assertDatabaseMissing('categories', [
            'id' => $categoryId,
        ]);
    }

    public function test_admin_can_query_analytics_and_ledger(): void
    {
        // Analytics Dashboard
        $analyticsRes = $this->actingAs($this->admin)->getJson('/api/admin/analytics/dashboard');
        $analyticsRes->assertStatus(200);
        $analyticsRes->assertJsonStructure(['barData', 'metrics']);

        // Master Ledger
        $ledgerRes = $this->actingAs($this->admin)->getJson('/api/admin/reports/ledger');
        $ledgerRes->assertStatus(200);
        $this->assertIsArray($ledgerRes->json());
    }
}

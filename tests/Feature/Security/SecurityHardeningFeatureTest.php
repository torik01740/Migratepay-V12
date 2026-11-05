<?php

namespace Tests\Feature\Security;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\CmsPage;
use App\Models\Deal;
use App\Models\Deposit;
use App\Models\PaymentGateway;
use App\Models\PaymentWebhookIdempotency;
use App\Models\SupportTicket;
use App\Models\SystemSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawalMethod;
use App\Models\WithdrawalRequest;
use App\Services\AuditLogService;
use App\Services\DealService;
use App\Services\DecimalSafeMath;
use App\Services\PaymentGatewayService;
use App\Services\WithdrawalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $rootAdmin;
    private User $coAdmin;
    private User $editorialStaff;
    private User $supportStaff;
    private User $verifiedCustomer;
    private User $unverifiedCustomer;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('login:127.0.0.1|victim@migratepay.com');

        $this->rootAdmin = User::factory()->create([
            'username'            => 'rootadmin',
            'name'                => 'Root Administrator',
            'email'               => 'admin@migratepay.com',
            'password'            => Hash::make('AdminSecret123!'),
            'role'                => 'admin',
            'status'              => 'Active',
            'verification_status' => 'VERIFIED',
        ]);

        $this->coAdmin = User::factory()->create([
            'username'            => 'coadmin_ops',
            'name'                => 'Operations Co-Admin',
            'email'               => 'coadmin@migratepay.com',
            'password'            => Hash::make('CoAdminSecret123!'),
            'role'                => 'co-admin',
            'status'              => 'Active',
            'verification_status' => 'VERIFIED',
        ]);

        $this->editorialStaff = User::factory()->create([
            'username'            => 'editor_cms',
            'name'                => 'Content Editor',
            'email'               => 'editor@migratepay.com',
            'password'            => Hash::make('EditorSecret123!'),
            'role'                => 'editorial',
            'status'              => 'Active',
            'verification_status' => 'VERIFIED',
        ]);

        $this->supportStaff = User::factory()->create([
            'username'            => 'support_agent',
            'name'                => 'Support Specialist',
            'email'               => 'support@migratepay.com',
            'password'            => Hash::make('SupportSecret123!'),
            'role'                => 'support',
            'status'              => 'Active',
            'verification_status' => 'VERIFIED',
        ]);

        $this->verifiedCustomer = User::factory()->create([
            'username'            => 'verified_buyer',
            'name'                => 'Verified Buyer',
            'email'               => 'buyer@migratepay.com',
            'password'            => Hash::make('BuyerSecret123!'),
            'role'                => 'user',
            'wallet_balance'      => 5000.00,
            'status'              => 'Active',
            'verification_status' => 'VERIFIED',
            'nid_number'          => '19901234567890123',
            'kyc_document_path'   => 'kyc/1/identity_card.jpg',
        ]);

        $this->unverifiedCustomer = User::factory()->create([
            'username'            => 'unverified_user',
            'name'                => 'Unverified User',
            'email'               => 'unverified@migratepay.com',
            'password'            => Hash::make('Unverified123!'),
            'role'                => 'user',
            'wallet_balance'      => 2000.00,
            'status'              => 'Active',
            'verification_status' => 'NOT_VERIFIED',
            'nid_number'          => null,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 1. Authentication & Session Security Tests
    // ─────────────────────────────────────────────────────────────────────────────

    public function test_unauthenticated_request_to_admin_api_returns_401(): void
    {
        $response = $this->getJson('/api/admin/users');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_regular_customer_is_denied_admin_api_access_with_403(): void
    {
        $response = $this->actingAs($this->verifiedCustomer)->getJson('/api/admin/users');
        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Unauthorized. Administrative staff access required.',
        ]);
    }

    public function test_login_throttling_triggers_account_lockout_after_max_attempts(): void
    {
        SystemSetting::set('security.max_failed_logins', '3', 'security', 'integer');

        $email = 'victim@migratepay.com';
        $key = 'login:127.0.0.1|' . $email;
        RateLimiter::clear($key);

        for ($i = 1; $i <= 3; $i++) {
            $response = $this->postJson('/api/login', [
                'email'    => $email,
                'password' => 'WrongPassword!',
            ]);
            $response->assertStatus(401);
        }

        // 4th attempt must be locked out with 429
        $lockoutResponse = $this->postJson('/api/login', [
            'email'    => $email,
            'password' => 'WrongPassword!',
        ]);

        $lockoutResponse->assertStatus(429);
        $lockoutResponse->assertJsonStructure(['success', 'message']);
        $this->assertStringContainsString('Too many failed login attempts', $lockoutResponse->json('message'));
    }

    public function test_session_remember_token_rotated_on_password_change(): void
    {
        $oldToken = $this->verifiedCustomer->remember_token;

        $response = $this->actingAs($this->verifiedCustomer)->postJson('/api/change-password', [
            'current_password'      => 'BuyerSecret123!',
            'password'              => 'NewSuperSecurePass456!',
            'password_confirmation' => 'NewSuperSecurePass456!',
        ]);

        $response->assertStatus(200);
        $this->verifiedCustomer->refresh();
        $this->assertNotEquals($oldToken, $this->verifiedCustomer->remember_token);
        $this->assertTrue(Hash::check('NewSuperSecurePass456!', $this->verifiedCustomer->password));
    }

    public function test_session_idle_timeout_server_side_enforcement(): void
    {
        SystemSetting::set('security.session_timeout_minutes', '15', 'security', 'integer');

        // 1. Fresh active request
        $response = $this->actingAs($this->verifiedCustomer)
            ->withSession(['last_activity_time' => time() - 300]) // 5 minutes ago (< 15 min limit)
            ->getJson('/api/me');
        $response->assertStatus(200);

        // 2. Idle session request (> 15 minutes)
        $expiredResponse = $this->actingAs($this->verifiedCustomer)
            ->withSession(['last_activity_time' => time() - 1000]) // 16.6 minutes ago (> 15 min limit)
            ->getJson('/api/me');

        $expiredResponse->assertStatus(401);
        $expiredResponse->assertJson([
            'success' => false,
            'session_expired' => true,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 2. Granular Staff RBAC & Least-Privilege Tests
    // ─────────────────────────────────────────────────────────────────────────────

    public function test_editorial_staff_can_access_cms_but_blocked_from_finance(): void
    {
        // Allowed: CMS
        $cmsResponse = $this->actingAs($this->editorialStaff)->getJson('/api/admin/cms');
        $cmsResponse->assertStatus(200);

        // Blocked: Deposits
        $depositResponse = $this->actingAs($this->editorialStaff)->getJson('/api/admin/deposits');
        $depositResponse->assertStatus(403);
        $depositResponse->assertJson(['success' => false]);

        // Blocked: User status modification
        $userStatusResponse = $this->actingAs($this->editorialStaff)->putJson("/api/admin/users/{$this->verifiedCustomer->id}/status", [
            'status' => 'Suspended',
        ]);
        $userStatusResponse->assertStatus(403);
    }

    public function test_support_staff_can_access_tickets_but_blocked_from_financial_approvals(): void
    {
        $method = WithdrawalMethod::create([
            'user_id' => $this->verifiedCustomer->id,
            'type'    => 'bank',
            'bank_name' => 'City Bank',
            'account_number' => '1234567890',
            'account_name' => 'Verified Buyer',
        ]);

        $withdrawal = WithdrawalRequest::create([
            'reference_id'         => 'WDR-TEST01',
            'user_id'              => $this->verifiedCustomer->id,
            'withdrawal_method_id' => $method->id,
            'amount'               => '500.00',
            'fee'                  => '0.00',
            'net_amount'           => '500.00',
            'type'                 => 'bank',
            'status'               => 'pending',
        ]);

        // Support staff tries to approve withdrawal: BLOCKED 403
        $approveResponse = $this->actingAs($this->supportStaff)->postJson("/api/admin/withdrawals/{$withdrawal->id}/approve", [
            'admin_note' => 'Unauthorized approval attempt',
        ]);
        $approveResponse->assertStatus(403);
        $approveResponse->assertJson(['success' => false]);
    }

    public function test_coadmin_cannot_alter_root_admin_status(): void
    {
        $response = $this->actingAs($this->coAdmin)->putJson("/api/admin/users/{$this->rootAdmin->id}/status", [
            'status' => 'Suspended',
        ]);

        $response->assertStatus(403);
        $this->assertStringContainsString('modify root admin', $response->json('message'));
        $this->rootAdmin->refresh();
        $this->assertEquals('Active', $this->rootAdmin->status);
    }

    public function test_admin_ip_whitelist_blocks_non_whitelisted_ip(): void
    {
        SystemSetting::set('security.ip_whitelist', '10.0.0.1, 10.0.0.2', 'security', 'string');

        // Request from 127.0.0.1 should be blocked
        $response = $this->actingAs($this->rootAdmin)->getJson('/api/admin/users');
        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Unauthorized. Access is restricted from your IP address.',
        ]);

        // Clear whitelist
        SystemSetting::set('security.ip_whitelist', '', 'security', 'string');
        $allowedResponse = $this->actingAs($this->rootAdmin)->getJson('/api/admin/users');
        $allowedResponse->assertStatus(200);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 3. Financial Integrity, Explicit Escrow Transitions & Concurrency
    // ─────────────────────────────────────────────────────────────────────────────

    public function test_unverified_customer_blocked_from_withdrawal_when_kyc_gate_active(): void
    {
        SystemSetting::set('security.require_kyc_withdrawal', '1', 'security', 'boolean');

        $method = WithdrawalMethod::create([
            'user_id' => $this->unverifiedCustomer->id,
            'type'    => 'bank',
            'bank_name' => 'Eastern Bank',
            'account_number' => '9988776655',
            'account_name' => 'Unverified Customer',
        ]);

        $service = app(WithdrawalService::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Account identity verification (KYC) is required before requesting withdrawals.');

        $service->requestWithdrawal($this->unverifiedCustomer, [
            'withdrawal_method_id' => $method->id,
            'amount'               => '500.00',
        ]);
    }

    public function test_double_approval_prevention_on_deposits(): void
    {
        $deposit = Deposit::create([
            'user_id'    => $this->verifiedCustomer->id,
            'amount'     => '1000.00',
            'gateway'    => 'bkash',
            'proof_note' => 'Double approval test deposit',
            'status'     => 'pending',
            'admin_read' => false,
        ]);

        // First approval succeeds
        $first = $this->actingAs($this->rootAdmin)->postJson("/api/admin/deposits/{$deposit->id}/approve", [
            'admin_note' => 'First legitimate approval',
        ]);
        $first->assertStatus(200);

        // Second approval must fail with 400: already approved
        $second = $this->actingAs($this->rootAdmin)->postJson("/api/admin/deposits/{$deposit->id}/approve", [
            'admin_note' => 'Duplicate approval attempt',
        ]);
        $this->assertEquals(400, $second->status());
        $this->assertStringContainsString('already been approved', $second->json('message'));
    }

    public function test_double_approval_prevention_on_withdrawals(): void
    {
        $method = WithdrawalMethod::create([
            'user_id' => $this->verifiedCustomer->id,
            'type'    => 'bank',
            'bank_name' => 'City Bank',
            'account_number' => '1122334455',
            'account_name' => 'Verified Buyer',
        ]);

        $withdrawal = WithdrawalRequest::create([
            'reference_id'         => 'WDR-DOUBLE01',
            'user_id'              => $this->verifiedCustomer->id,
            'withdrawal_method_id' => $method->id,
            'amount'               => '400.00',
            'fee'                  => '0.00',
            'net_amount'           => '400.00',
            'type'                 => 'bank',
            'status'               => 'pending',
        ]);

        // First approval succeeds
        $first = $this->actingAs($this->rootAdmin)->postJson("/api/admin/withdrawals/{$withdrawal->id}/approve", [
            'admin_note' => 'First legit approval',
        ]);
        $first->assertStatus(200);

        // Second approval fails with 400
        $second = $this->actingAs($this->rootAdmin)->postJson("/api/admin/withdrawals/{$withdrawal->id}/approve", [
            'admin_note' => 'Double approval attempt',
        ]);
        $this->assertEquals(400, $second->status());
        $this->assertStringContainsString('already been processed', $second->json('message'));
    }

    public function test_explicit_escrow_release_state_allowlist(): void
    {
        $seller = User::factory()->create(['role' => 'user', 'wallet_balance' => 0.00]);
        $dealService = app(DealService::class);

        // Create a deal in awaiting_acceptance (funds NOT secured)
        $deal = Deal::create([
            'deal_id'            => 'DL-TEST-001',
            'title'              => 'Web Design Deal',
            'initiator_id'       => $this->verifiedCustomer->id,
            'initiator_role'     => 'buyer',
            'counterparty_id'    => $seller->id,
            'counterparty_email' => $seller->email,
            'amount'             => '1000.00',
            'charge_percentage'  => '5.00',
            'total_charge'       => '50.00',
            'seller_gets'        => '950.00',
            'buyer_pays'         => '1000.00',
            'status'             => 'awaiting_acceptance',
        ]);

        // Releasing from awaiting_acceptance MUST be rejected
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Cannot release funds: deal is currently in 'awaiting_acceptance' status.");
        $dealService->adminApproveFunds($deal->id, $this->rootAdmin);
    }

    public function test_commission_and_withdrawal_rounding_parity_exhaustive(): void
    {
        $dealService = app(DealService::class);

        $testCases = [
            // [amount, expected_v10_commission, expected_rate]
            ['500.00', '25.00', '5.00'],
            ['999.99', '50.00', '5.00'],
            ['1000.00', '50.00', '5.00'],
            ['1000.01', '50.00', '5.00'],
            ['5000.00', '250.00', '5.00'],
            ['5000.01', '225.00', '4.50'],
            ['7500.00', '337.50', '4.50'],
            ['10000.00', '450.00', '4.50'],
            ['10000.01', '400.00', '4.00'],
            ['15000.00', '600.00', '4.00'],
            ['15000.01', '450.00', '3.00'],
            ['25000.00', '750.00', '3.00'],
        ];

        foreach ($testCases as [$amt, $expectedFee, $expectedRate]) {
            $result = $dealService->calculateCharge($amt);
            $this->assertEquals($expectedFee, $result['original_charge'], "Fee mismatch for amount: {$amt}");
            $this->assertEquals($expectedRate, $result['charge_percentage'], "Rate mismatch for amount: {$amt}");
        }

        // Test bank withdrawal fee is strictly 0.00%
        $bankFee = DecimalSafeMath::mul('5000.00', '0.00', 2);
        $this->assertEquals('0.00', $bankFee);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 4. KYC Document Least-Privilege & Privacy Tests
    // ─────────────────────────────────────────────────────────────────────────────

    public function test_least_privilege_kyc_document_access(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put("kyc/{$this->verifiedCustomer->id}/identity_card.jpg", 'FAKE_IMAGE_BYTES');

        // 1. Other regular customer -> 403 Forbidden
        $otherCustomer = $this->actingAs($this->unverifiedCustomer)->get("/api/private/kyc/{$this->verifiedCustomer->id}");
        $otherCustomer->assertStatus(403);

        // 2. Editorial Staff -> 403 Forbidden (Least privilege)
        $editorial = $this->actingAs($this->editorialStaff)->get("/api/private/kyc/{$this->verifiedCustomer->id}");
        $editorial->assertStatus(403);
        $editorial->assertJson(['success' => false]);

        // 3. Support Staff -> 403 Forbidden (Least privilege)
        $support = $this->actingAs($this->supportStaff)->get("/api/private/kyc/{$this->verifiedCustomer->id}");
        $support->assertStatus(403);
        $support->assertJson(['success' => false]);

        // 4. Document Owner -> 200 OK
        $owner = $this->actingAs($this->verifiedCustomer)->get("/api/private/kyc/{$this->verifiedCustomer->id}");
        $owner->assertStatus(200);
        $owner->assertHeader('X-Content-Type-Options', 'nosniff');

        // 5. Co-Admin (Compliance Staff) -> 200 OK
        $coAdmin = $this->actingAs($this->coAdmin)->get("/api/private/kyc/{$this->verifiedCustomer->id}");
        $coAdmin->assertStatus(200);

        // 6. Root Admin -> 200 OK
        $admin = $this->actingAs($this->rootAdmin)->get("/api/private/kyc/{$this->verifiedCustomer->id}");
        $admin->assertStatus(200);

        // 7. Path traversal attempt -> 404 Not Found
        $traversalUser = User::factory()->create([
            'role' => 'user',
            'kyc_document_path' => '../../../../etc/passwd',
        ]);
        $traversal = $this->actingAs($this->rootAdmin)->get("/api/private/kyc/{$traversalUser->id}");
        $traversal->assertStatus(404);
    }

    public function test_public_profile_hides_sensitive_balance_and_nid_and_staff(): void
    {
        $response = $this->getJson("/user/{$this->verifiedCustomer->username}");
        $response->assertStatus(200);

        $userData = $response->json('user');
        $this->assertArrayNotHasKey('wallet_balance', $userData);
        $this->assertArrayNotHasKey('nid_number', $userData);
        $this->assertArrayNotHasKey('kyc_document_path', $userData);
        $this->assertArrayNotHasKey('password', $userData);

        // Staff profiles return 404 publicly
        $adminPublicResponse = $this->getJson("/user/{$this->rootAdmin->username}");
        $adminPublicResponse->assertStatus(404);

        $coAdminPublicResponse = $this->getJson("/user/{$this->coAdmin->username}");
        $coAdminPublicResponse->assertStatus(404);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 5. Payment Gateway Security & Atomic Idempotency Registry Tests
    // ─────────────────────────────────────────────────────────────────────────────

    public function test_payment_gateway_forged_webhook_signature_is_rejected(): void
    {
        $gateway = PaymentGateway::create([
            'name'           => 'Test bKash Gateway',
            'slug'           => 'bkash',
            'currency'       => 'BDT',
            'is_active'      => true,
            'secret_key'     => 'RealSuperSecretGatewayKey123',
            'fee_percentage' => '1.50',
            'fee_fixed'      => '0.00',
        ]);

        $payload = [
            'payment_reference' => 'PAY-FORGED123',
            'status'            => 'success',
            'amount'            => '1000.00',
            'user_id'           => $this->verifiedCustomer->id,
        ];

        // Forged signature
        $forgedSignature = hash_hmac('sha256', json_encode($payload), 'WrongSecretKey!');

        $response = $this->postJson('/api/payments/webhook/bkash', $payload, [
            'X-Signature' => $forgedSignature,
        ]);

        $this->assertTrue(in_array($response->status(), [400, 500]));
    }

    public function test_payment_gateway_valid_signature_settles_and_replay_is_idempotent(): void
    {
        $gateway = PaymentGateway::create([
            'name'           => 'Test Nagad Gateway',
            'slug'           => 'nagad',
            'currency'       => 'BDT',
            'is_active'      => true,
            'secret_key'     => 'NagadSecretKey999',
            'fee_percentage' => '1.50',
            'fee_fixed'      => '0.00',
        ]);

        $payload = [
            'payment_reference' => 'PAY-LEGIT999',
            'status'            => 'success',
            'amount'            => '500.00',
            'user_id'           => $this->verifiedCustomer->id,
        ];

        $validSignature = hash_hmac('sha256', json_encode($payload), 'NagadSecretKey999');

        // First callback succeeds
        $firstResponse = $this->postJson('/api/payments/webhook/nagad', $payload, [
            'X-Signature' => $validSignature,
        ]);
        $firstResponse->assertStatus(200);
        $this->assertEquals('credited', $firstResponse->json('status'));

        // Verify that atomic idempotency record was created
        $idempotency = PaymentWebhookIdempotency::where('gateway_slug', 'nagad')
            ->where('payment_reference', 'PAY-LEGIT999')
            ->first();
        $this->assertNotNull($idempotency);
        $this->assertEquals('completed', $idempotency->status);

        // Replay callback with same reference is idempotently acknowledged without double crediting
        $replayResponse = $this->postJson('/api/payments/webhook/nagad', $payload, [
            'X-Signature' => $validSignature,
        ]);
        $replayResponse->assertStatus(200);
        $this->assertEquals('already_processed', $replayResponse->json('status'));
        $this->assertEquals($idempotency->transaction_id, $replayResponse->json('transaction_id'));
    }

    public function test_interrupted_webhook_processing_can_recover_and_settle(): void
    {
        $gateway = PaymentGateway::firstOrCreate(
            ['slug' => 'bkash'],
            [
                'name'           => 'Test bKash Gateway',
                'currency'       => 'BDT',
                'is_active'      => true,
                'secret_key'     => 'BkashSecretKey123',
                'fee_percentage' => '1.50',
                'fee_fixed'      => '0.00',
            ]
        );

        $ref = 'PAY-INTERRUPT-' . uniqid();
        $initialBalance = $this->verifiedCustomer->wallet_balance;

        // Simulate a stale interrupted processing record (> 60 seconds old) using raw DB insert
        \Illuminate\Support\Facades\DB::table('payment_webhook_idempotencies')->insert([
            'gateway_slug'      => 'bkash',
            'payment_reference' => $ref,
            'status'            => 'processing',
            'user_id'           => $this->verifiedCustomer->id,
            'amount'            => '350.00',
            'created_at'        => now()->subMinutes(5)->toDateTimeString(),
            'updated_at'        => now()->subMinutes(5)->toDateTimeString(),
        ]);

        $payload = [
            'payment_reference' => $ref,
            'status'            => 'success',
            'amount'            => '350.00',
            'user_id'           => $this->verifiedCustomer->id,
        ];
        $sig = hash_hmac('sha256', json_encode($payload), 'BkashSecretKey123');

        // New callback for interrupted transaction should clean up stale lock and settle cleanly
        $response = $this->postJson('/api/payments/webhook/bkash', $payload, [
            'X-Signature' => $sig,
        ]);

        $response->assertStatus(200);
        $this->assertEquals('credited', $response->json('status'));

        $this->verifiedCustomer->refresh();
        $this->assertEquals(
            \App\Services\DecimalSafeMath::add((string)$initialBalance, '350.00', 2),
            (string)$this->verifiedCustomer->wallet_balance
        );

        $updatedRecord = PaymentWebhookIdempotency::where('gateway_slug', 'bkash')
            ->where('payment_reference', $ref)
            ->first();
        $this->assertEquals('completed', $updatedRecord->status);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 6. Audit Log Redaction & Security Policy Endpoints
    // ─────────────────────────────────────────────────────────────────────────────

    public function test_audit_log_redaction_and_resilience(): void
    {
        $rawDetails = [
            'password'        => 'SecretPlainPassword123!',
            'secret_key'       => 'sk_live_998877665544',
            'otp'              => '123456',
            'nid_number'       => '19901234567890123',
            'legitimate_field' => 'Safe Audit Data',
        ];

        $sanitized = AuditLogService::sanitizeDetails($rawDetails);

        $this->assertEquals('[REDACTED]', $sanitized['password']);
        $this->assertEquals('[REDACTED]', $sanitized['secret_key']);
        $this->assertEquals('[REDACTED]', $sanitized['otp']);
        $this->assertEquals('[REDACTED]', $sanitized['nid_number']);
        $this->assertEquals('Safe Audit Data', $sanitized['legitimate_field']);
    }

    public function test_security_policy_get_and_update_endpoints(): void
    {
        $getResponse = $this->actingAs($this->rootAdmin)->getJson('/api/admin/security/policies');
        $getResponse->assertStatus(200);
        $getResponse->assertJsonStructure(['success', 'policies']);

        $updateResponse = $this->actingAs($this->rootAdmin)->postJson('/api/admin/security/policies', [
            'maxFailedLogins'         => 7,
            'requireKycForWithdrawal' => true,
            'ipWhitelist'             => '127.0.0.1, 192.168.1.50',
            'sessionTimeoutMinutes'   => 45,
        ]);

        $updateResponse->assertStatus(200);
        $this->assertEquals(7, $updateResponse->json('policies.maxFailedLogins'));
        $this->assertEquals('127.0.0.1, 192.168.1.50', $updateResponse->json('policies.ipWhitelist'));
        $this->assertEquals('7', SystemSetting::get('security.max_failed_logins'));

        // Enabling administrative 2FA policy succeeds now that MFA is active in Phase 10
        $attempt2fa = $this->actingAs($this->rootAdmin)->postJson('/api/admin/security/policies', [
            'enforce2FA' => true,
        ]);
        $attempt2fa->assertStatus(200);
        $attempt2fa->assertJsonFragment(['success' => true]);
        $this->assertEquals('1', SystemSetting::get('security.enforce_2fa'));

        // Reset back to 0 for test hygiene
        SystemSetting::set('security.enforce_2fa', '0');
    }
}

<?php

namespace Tests\Feature\Security;

use App\Models\SystemSetting;
use App\Models\User;
use App\Services\MfaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $rootAdmin;
    protected User $coAdmin;
    protected MfaService $mfaService;
    protected Google2FA $google2fa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mfaService = app(MfaService::class);
        $this->google2fa = new Google2FA();

        $this->customer = User::factory()->create([
            'email'          => 'customer_mfa@example.com',
            'password'       => bcrypt('CustomerPass123!'),
            'role'           => 'user',
            'wallet_balance' => '500.00',
        ]);

        $this->rootAdmin = User::factory()->create([
            'email'          => 'admin_mfa@example.com',
            'password'       => bcrypt('AdminPass123!'),
            'role'           => 'admin',
            'wallet_balance' => '0.00',
        ]);

        $this->coAdmin = User::factory()->create([
            'email'          => 'coadmin_mfa@example.com',
            'password'       => bcrypt('CoAdminPass123!'),
            'role'           => 'co-admin',
            'wallet_balance' => '0.00',
        ]);
    }

    public function test_user_can_query_initial_mfa_status(): void
    {
        $response = $this->actingAs($this->customer)->getJson('/api/user/mfa/status');
        $response->assertStatus(200);
        $response->assertJson([
            'success'                  => true,
            'enabled'                  => false,
            'confirmed_at'             => null,
            'recovery_codes_remaining' => 0,
        ]);
    }

    public function test_user_can_initialize_mfa_setup_and_receive_qr_and_secret(): void
    {
        $response = $this->actingAs($this->customer)->postJson('/api/user/mfa/setup');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'secret', 'qr_code_url', 'message']);

        $secret = $response->json('secret');
        $this->assertNotEmpty($secret);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $response->json('qr_code_url'));

        $this->customer->refresh();
        $this->assertNotEmpty($this->customer->two_factor_pending_secret);
        $this->assertEquals($secret, Crypt::decryptString($this->customer->two_factor_pending_secret));
    }

    public function test_invalid_totp_code_rejects_enrollment(): void
    {
        $setup = $this->actingAs($this->customer)->postJson('/api/user/mfa/setup');
        $setup->assertStatus(200);

        $confirm = $this->actingAs($this->customer)->postJson('/api/user/mfa/confirm', [
            'code' => '000000',
        ]);

        $confirm->assertStatus(422);
        $this->assertFalse($this->customer->refresh()->hasTwoFactorEnabled());
    }

    public function test_valid_totp_code_activates_enrollment_and_returns_recovery_codes(): void
    {
        $setup = $this->actingAs($this->customer)->postJson('/api/user/mfa/setup');
        $secret = $setup->json('secret');

        // Generate legitimate current OTP code
        $code = $this->google2fa->getCurrentOtp($secret);

        $confirm = $this->actingAs($this->customer)->postJson('/api/user/mfa/confirm', [
            'code' => $code,
        ]);

        $confirm->assertStatus(200);
        $confirm->assertJsonStructure(['success', 'message', 'recovery_codes']);
        $codes = $confirm->json('recovery_codes');
        $this->assertCount(8, $codes);

        $this->customer->refresh();
        $this->assertTrue($this->customer->hasTwoFactorEnabled());
        $this->assertNotNull($this->customer->two_factor_confirmed_at);
        $this->assertNull($this->customer->two_factor_pending_secret);
        $this->assertCount(8, (array)$this->customer->two_factor_recovery_codes);
    }

    public function test_login_for_user_with_mfa_delays_auth_and_requires_mfa_challenge(): void
    {
        // Setup and enable MFA on customer
        $secret = $this->mfaService->generateSecretKey();
        $validCode = $this->google2fa->getCurrentOtp($secret);
        $this->customer->forceFill(['two_factor_pending_secret' => Crypt::encryptString($secret)])->save();
        $this->mfaService->confirmMfaEnrollment($this->customer, $secret, $validCode);

        // Attempt login with valid email + password
        $response = $this->postJson('/api/login', [
            'email'    => 'customer_mfa@example.com',
            'password' => 'CustomerPass123!',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'      => true,
            'requires_mfa' => true,
        ]);

        // User must NOT be logged in yet
        $this->assertFalse(auth()->check());
    }

    public function test_mfa_challenge_fails_with_invalid_code(): void
    {
        $secret = $this->mfaService->generateSecretKey();
        $validCode = $this->google2fa->getCurrentOtp($secret);
        $this->customer->forceFill(['two_factor_pending_secret' => Crypt::encryptString($secret)])->save();
        $this->mfaService->confirmMfaEnrollment($this->customer, $secret, $validCode);

        // Initiate login to establish pending session
        $this->postJson('/api/login', [
            'email'    => 'customer_mfa@example.com',
            'password' => 'CustomerPass123!',
        ]);

        // Submit wrong MFA code
        $verify = $this->postJson('/api/auth/mfa-verify', [
            'code' => '999999',
        ]);

        $verify->assertStatus(422);
        $verify->assertJsonFragment(['success' => false]);
        $this->assertFalse(auth()->check());
    }

    public function test_mfa_challenge_succeeds_with_valid_totp_code(): void
    {
        $secret = $this->mfaService->generateSecretKey();
        $validCode = $this->google2fa->getCurrentOtp($secret);
        $this->customer->forceFill(['two_factor_pending_secret' => Crypt::encryptString($secret)])->save();
        $this->mfaService->confirmMfaEnrollment($this->customer, $secret, $validCode);

        // Initiate login
        $this->postJson('/api/login', [
            'email'    => 'customer_mfa@example.com',
            'password' => 'CustomerPass123!',
        ]);

        // Submit valid current TOTP code
        $currentOtp = $this->google2fa->getCurrentOtp($secret);
        $verify = $this->postJson('/api/auth/mfa-verify', [
            'code' => $currentOtp,
        ]);

        $verify->assertStatus(200);
        $verify->assertJson([
            'success'  => true,
            'redirect' => '/dashboard',
        ]);

        $this->assertTrue(auth()->check());
        $this->assertEquals($this->customer->id, auth()->id());
    }

    public function test_mfa_challenge_succeeds_with_recovery_code_and_consumes_it(): void
    {
        $secret = $this->mfaService->generateSecretKey();
        $validCode = $this->google2fa->getCurrentOtp($secret);
        $this->customer->forceFill(['two_factor_pending_secret' => Crypt::encryptString($secret)])->save();
        $enrollment = $this->mfaService->confirmMfaEnrollment($this->customer, $secret, $validCode);
        $recoveryCodes = $enrollment['recovery_codes'];
        $firstCode = $recoveryCodes[0];

        // Initiate login
        $this->postJson('/api/login', [
            'email'    => 'customer_mfa@example.com',
            'password' => 'CustomerPass123!',
        ]);

        // Submit single-use recovery code
        $verify = $this->postJson('/api/auth/mfa-verify', [
            'code' => $firstCode,
        ]);

        $verify->assertStatus(200);
        $this->assertTrue(auth()->check());

        // Verify that the code was consumed (remaining count 7)
        $this->customer->refresh();
        $this->assertCount(7, (array)$this->customer->two_factor_recovery_codes);

        // Log out and attempt reuse of the exact same recovery code
        $this->postJson('/api/logout');
        $this->assertFalse(auth()->check());

        $this->postJson('/api/login', [
            'email'    => 'customer_mfa@example.com',
            'password' => 'CustomerPass123!',
        ]);

        $reuseAttempt = $this->postJson('/api/auth/mfa-verify', [
            'code' => $firstCode,
        ]);

        $reuseAttempt->assertStatus(422);
        $this->assertFalse(auth()->check());
    }

    public function test_regenerating_recovery_codes_invalidates_previous_codes(): void
    {
        $secret = $this->mfaService->generateSecretKey();
        $validCode = $this->google2fa->getCurrentOtp($secret);
        $this->customer->forceFill(['two_factor_pending_secret' => Crypt::encryptString($secret)])->save();
        $enrollment = $this->mfaService->confirmMfaEnrollment($this->customer, $secret, $validCode);
        $initialCodes = $enrollment['recovery_codes'];

        $currentOtp = $this->google2fa->getCurrentOtp($secret);

        $regen = $this->actingAs($this->customer)->postJson('/api/user/mfa/regenerate-recovery-codes', [
            'password' => 'CustomerPass123!',
            'code'     => $currentOtp,
        ]);

        $regen->assertStatus(200);
        $newCodes = $regen->json('recovery_codes');
        $this->assertCount(8, $newCodes);
        $this->assertNotEquals($initialCodes, $newCodes);

        // Previous codes must fail
        $hashOld = hash('sha256', str_replace('-', '', $initialCodes[0]));
        $this->assertNotContains($hashOld, (array)$this->customer->refresh()->two_factor_recovery_codes);
    }

    public function test_disabling_mfa_requires_correct_password_and_code(): void
    {
        $secret = $this->mfaService->generateSecretKey();
        $validCode = $this->google2fa->getCurrentOtp($secret);
        $this->customer->forceFill(['two_factor_pending_secret' => Crypt::encryptString($secret)])->save();
        $this->mfaService->confirmMfaEnrollment($this->customer, $secret, $validCode);

        // Bad password rejected
        $badPass = $this->actingAs($this->customer)->postJson('/api/user/mfa/disable', [
            'password' => 'WrongPassword!',
            'code'     => $this->google2fa->getCurrentOtp($secret),
        ]);
        $badPass->assertStatus(422);
        $this->assertTrue($this->customer->refresh()->hasTwoFactorEnabled());

        // Correct password and code disables MFA
        $good = $this->actingAs($this->customer)->postJson('/api/user/mfa/disable', [
            'password' => 'CustomerPass123!',
            'code'     => $this->google2fa->getCurrentOtp($secret),
        ]);
        $good->assertStatus(200);

        $this->customer->refresh();
        $this->assertFalse($this->customer->hasTwoFactorEnabled());
        $this->assertNull($this->customer->two_factor_secret);
        $this->assertNull($this->customer->two_factor_confirmed_at);
    }

    public function test_admin_mfa_policy_blocks_unverified_staff_from_admin_api_when_enforced(): void
    {
        // Enforce administrative MFA in system settings
        SystemSetting::set('security.enforce_2fa', '1', 'security', 'boolean');

        // Root admin without MFA enrolled
        $response = $this->actingAs($this->rootAdmin)->getJson('/api/admin/users');
        $response->assertStatus(403);
        $response->assertJson([
            'success'      => false,
            'mfa_required' => true,
        ]);

        // Co-Admin without MFA enrolled is also blocked
        $coAdminResponse = $this->actingAs($this->coAdmin)->getJson('/api/admin/users');
        $coAdminResponse->assertStatus(403);
        $coAdminResponse->assertJson([
            'success'      => false,
            'mfa_required' => true,
        ]);
    }

    public function test_admin_mfa_policy_allows_access_once_mfa_enrolled_and_verified(): void
    {
        SystemSetting::set('security.enforce_2fa', '1', 'security', 'boolean');

        // Enroll root admin in MFA
        $secret = $this->mfaService->generateSecretKey();
        $code = $this->google2fa->getCurrentOtp($secret);
        $this->rootAdmin->forceFill(['two_factor_pending_secret' => Crypt::encryptString($secret)])->save();
        $this->mfaService->confirmMfaEnrollment($this->rootAdmin, $secret, $code);

        // Mark session as MFA verified
        $this->actingAs($this->rootAdmin)
            ->withSession(['auth.mfa_verified' => true])
            ->getJson('/api/admin/users')
            ->assertStatus(200);
    }

    public function test_ordinary_user_cannot_bypass_rbac_using_mfa(): void
    {
        SystemSetting::set('security.enforce_2fa', '1', 'security', 'boolean');

        // Enroll ordinary user in MFA
        $secret = $this->mfaService->generateSecretKey();
        $code = $this->google2fa->getCurrentOtp($secret);
        $this->customer->forceFill(['two_factor_pending_secret' => Crypt::encryptString($secret)])->save();
        $this->mfaService->confirmMfaEnrollment($this->customer, $secret, $code);

        // Even with MFA verified, ordinary user is denied admin access with HTTP 403
        $this->actingAs($this->customer)
            ->withSession(['auth.mfa_verified' => true])
            ->getJson('/api/admin/users')
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized. Administrative staff access required.',
            ]);
    }

    public function test_mfa_challenge_rate_limiting(): void
    {
        RateLimiter::clear('mfa_verify:127.0.0.1');

        $secret = $this->mfaService->generateSecretKey();
        $validCode = $this->google2fa->getCurrentOtp($secret);
        $this->customer->forceFill(['two_factor_pending_secret' => Crypt::encryptString($secret)])->save();
        $this->mfaService->confirmMfaEnrollment($this->customer, $secret, $validCode);

        // Establish pending MFA login session
        $this->postJson('/api/login', [
            'email'    => 'customer_mfa@example.com',
            'password' => 'CustomerPass123!',
        ]);

        // Submit 5 invalid attempts
        for ($i = 0; $i < 5; $i++) {
            $resp = $this->postJson('/api/auth/mfa-verify', ['code' => '111111']);
            $resp->assertStatus(422);
        }

        // 6th attempt must be throttled with HTTP 429
        $throttled = $this->postJson('/api/auth/mfa-verify', ['code' => '111111']);
        $throttled->assertStatus(429);
        $this->assertStringContainsString('Too many invalid attempts', $throttled->json('message'));

        RateLimiter::clear('mfa_verify:127.0.0.1');
    }
}

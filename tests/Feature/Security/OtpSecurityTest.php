<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OtpSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_never_leaks_plaintext_otp(): void
    {
        $user = User::factory()->create([
            'email' => 'pulok@gmail.com',
        ]);

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'pulok@gmail.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $content = $response->getContent();

        // Retrieve hashed token from database
        $tokenRecord = DB::table('password_reset_tokens')->where('email', 'pulok@gmail.com')->first();
        $this->assertNotNull($tokenRecord);
        $this->assertEquals(64, strlen($tokenRecord->token)); // SHA-256 hash length

        // Assert response NEVER contains the plaintext code or raw hash
        $this->assertStringNotContainsString('Simulated', $content);
        $this->assertStringNotContainsString('use OTP', $content);
        $this->assertStringNotContainsString($tokenRecord->token, $content);
    }

    public function test_otp_expires_after_5_minutes(): void
    {
        $email = 'pulok@gmail.com';
        $rawOtp = '123456';
        $hashedOtp = hash('sha256', $rawOtp);

        // Store OTP with timestamp 6 minutes in the past
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $hashedOtp,
            'created_at' => Carbon::now()->subMinutes(6)->toDateTimeString(),
        ]);

        $response = $this->postJson('/api/verify-otp', [
            'email' => $email,
            'otp' => $rawOtp,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Verification code has expired. Please request a new one.',
        ]);
    }

    public function test_otp_attempt_limits_enforced(): void
    {
        $email = 'pulok@gmail.com';
        $rawOtp = '123456';
        $hashedOtp = hash('sha256', $rawOtp);

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $hashedOtp,
            'created_at' => Carbon::now(),
        ]);

        // 3 failed attempts
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/api/verify-otp', [
                'email' => $email,
                'otp' => '999999', // wrong OTP
            ]);
            $response->assertStatus(400);
        }

        // 4th attempt should be blocked by rate limiter (429)
        $response4 = $this->postJson('/api/verify-otp', [
            'email' => $email,
            'otp' => '999999',
        ]);
        $response4->assertStatus(429);
    }

    public function test_valid_otp_can_reset_password_and_is_immediately_consumed(): void
    {
        $user = User::factory()->create([
            'email' => 'pulok@gmail.com',
            'password' => 'old_secret_pass',
        ]);

        $rawOtp = '654321';
        $hashedOtp = hash('sha256', $rawOtp);

        DB::table('password_reset_tokens')->insert([
            'email' => 'pulok@gmail.com',
            'token' => $hashedOtp,
            'created_at' => Carbon::now(),
        ]);

        $response = $this->postJson('/api/reset-password', [
            'email' => 'pulok@gmail.com',
            'otp' => $rawOtp,
            'password' => 'new_super_secret_password_2026',
            'password_confirmation' => 'new_super_secret_password_2026',
        ]);

        $response->assertStatus(200);

        // Verify token was purged from table
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'pulok@gmail.com',
        ]);

        // Verify password was updated and user can authenticate
        $user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('new_super_secret_password_2026', $user->password));
    }
}

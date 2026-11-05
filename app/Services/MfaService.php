<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use RuntimeException;

class MfaService
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
        $this->google2fa->setWindow(1); // 1 window = +/- 30 seconds drift tolerance
    }

    /**
     * Generate a new RFC 6238 Base32 secret key.
     */
    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey(32);
    }

    /**
     * Generate an otpauth:// URI.
     */
    public function getOtpAuthUrl(User $user, string $secret): string
    {
        $appName = config('app.name', 'MigratePay');
        return $this->google2fa->getQRCodeUrl($appName, $user->email, $secret);
    }

    /**
     * Generate SVG QR code as base64 data URI for frontend image rendering.
     */
    public function getQrCodeDataUri(User $user, string $secret): string
    {
        $url = $this->getOtpAuthUrl($user, $secret);

        $renderer = new ImageRenderer(
            new RendererStyle(220, 1, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(10, 17, 40))),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $svg = $writer->writeString($url);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Verify a 6-digit TOTP code against a plaintext Base32 secret.
     */
    public function verifyKey(string $secret, string $code): bool
    {
        $cleanCode = preg_replace('/\s+/', '', $code);
        return (bool)$this->google2fa->verifyKey($secret, $cleanCode, 1);
    }

    /**
     * Generate secure 10-character recovery codes (e.g. 5A9B-C3D2).
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $plaintext = [];
        $hashed = [];

        for ($i = 0; $i < $count; $i++) {
            $chunk1 = strtoupper(Str::random(4));
            $chunk2 = strtoupper(Str::random(4));
            $code = "{$chunk1}-{$chunk2}";
            $plaintext[] = $code;
            $hashed[] = hash('sha256', str_replace('-', '', $code));
        }

        return [
            'plaintext' => $plaintext,
            'hashed'    => $hashed,
        ];
    }

    /**
     * Finalize and activate MFA enrollment after user confirms their first valid TOTP code.
     */
    public function confirmMfaEnrollment(User $user, string $secret, string $code): array
    {
        if (!$this->verifyKey($secret, $code)) {
            AuditLogService::log(
                'mfa.enrollment_failed',
                "User {$user->email} failed MFA confirmation: invalid code",
                ['user_id' => $user->id]
            );
            throw new RuntimeException('Invalid two-factor authentication code. Please check your authenticator clock.');
        }

        $recovery = $this->generateRecoveryCodes(8);

        $user->forceFill([
            'two_factor_secret'         => Crypt::encryptString($secret),
            'two_factor_confirmed_at'   => now(),
            'two_factor_recovery_codes' => $recovery['hashed'],
            'two_factor_pending_secret' => null,
        ])->save();

        AuditLogService::log(
            'mfa.enrolled',
            "User {$user->email} successfully enrolled and activated two-factor authentication",
            ['user_id' => $user->id]
        );

        return [
            'success'        => true,
            'message'        => 'Two-factor authentication has been enabled successfully.',
            'recovery_codes' => $recovery['plaintext'],
        ];
    }

    /**
     * Verify a code during authentication challenge: accepts either a 6-digit TOTP code or a recovery code.
     */
    public function verifyCodeOrRecovery(User $user, string $input): array
    {
        $cleanInput = trim($input);

        // 1. Check if valid 6-digit TOTP
        if (preg_match('/^\d{6}$/', $cleanInput) && !empty($user->two_factor_secret)) {
            try {
                $decryptedSecret = Crypt::decryptString($user->two_factor_secret);
                if ($this->verifyKey($decryptedSecret, $cleanInput)) {
                    AuditLogService::log(
                        'mfa.verified',
                        "User {$user->email} verified MFA challenge via TOTP authenticator code",
                        ['user_id' => $user->id]
                    );
                    return ['valid' => true, 'type' => 'totp'];
                }
            } catch (\Throwable $e) {
                // Decryption or verification failure
            }
        }

        // 2. Check if valid recovery code
        $cleanRecovery = strtoupper(str_replace(['-', ' '], '', $cleanInput));
        $recoveryHash = hash('sha256', $cleanRecovery);
        $storedCodes = (array)($user->two_factor_recovery_codes ?? []);

        if (in_array($recoveryHash, $storedCodes, true)) {
            // Remove the used recovery code (single use only)
            $remainingCodes = array_values(array_filter($storedCodes, fn($h) => $h !== $recoveryHash));

            $user->forceFill([
                'two_factor_recovery_codes' => $remainingCodes,
            ])->save();

            AuditLogService::log(
                'mfa.recovery_code_used',
                "User {$user->email} authenticated using a single-use recovery code. Remaining codes: " . count($remainingCodes),
                ['user_id' => $user->id, 'remaining_count' => count($remainingCodes)]
            );

            return [
                'valid'           => true,
                'type'            => 'recovery_code',
                'remaining_codes' => count($remainingCodes),
            ];
        }

        AuditLogService::log(
            'mfa.challenge_failed',
            "User {$user->email} failed MFA verification challenge",
            ['user_id' => $user->id]
        );

        return ['valid' => false];
    }

    /**
     * Disable MFA: Requires user password re-authentication + valid TOTP or recovery code.
     */
    public function disableMfa(User $user, string $password, string $code): bool
    {
        if (!Hash::check($password, $user->password)) {
            throw new RuntimeException('Incorrect account password.');
        }

        $verification = $this->verifyCodeOrRecovery($user, $code);
        if (!$verification['valid']) {
            throw new RuntimeException('Invalid two-factor authentication code or recovery code.');
        }

        $user->forceFill([
            'two_factor_secret'         => null,
            'two_factor_confirmed_at'   => null,
            'two_factor_recovery_codes' => null,
            'two_factor_pending_secret' => null,
        ])->save();

        AuditLogService::log(
            'mfa.disabled',
            "User {$user->email} disabled two-factor authentication",
            ['user_id' => $user->id]
        );

        return true;
    }

    /**
     * Regenerate recovery codes: Requires user password + valid TOTP code.
     */
    public function regenerateRecoveryCodes(User $user, string $password, string $code): array
    {
        if (!Hash::check($password, $user->password)) {
            throw new RuntimeException('Incorrect account password.');
        }

        $verification = $this->verifyCodeOrRecovery($user, $code);
        if (!$verification['valid']) {
            throw new RuntimeException('Invalid two-factor authentication code.');
        }

        $recovery = $this->generateRecoveryCodes(8);

        $user->forceFill([
            'two_factor_recovery_codes' => $recovery['hashed'],
        ])->save();

        AuditLogService::log(
            'mfa.recovery_codes_regenerated',
            "User {$user->email} regenerated two-factor recovery codes",
            ['user_id' => $user->id]
        );

        return $recovery['plaintext'];
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\MfaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MfaController extends Controller
{
    protected MfaService $mfaService;

    public function __construct(MfaService $mfaService)
    {
        $this->mfaService = $mfaService;
    }

    /**
     * Get the current user's MFA enrollment status.
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $storedCodes = (array)($user->two_factor_recovery_codes ?? []);

        return response()->json([
            'success'                  => true,
            'enabled'                  => $user->hasTwoFactorEnabled(),
            'confirmed_at'             => $user->two_factor_confirmed_at?->toIso8601String(),
            'recovery_codes_remaining' => count($storedCodes),
        ]);
    }

    /**
     * Begin MFA setup: generates a secret and SVG QR code data URI.
     */
    public function setup(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasTwoFactorEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is already active on this account.',
            ], 400);
        }

        $secret = $this->mfaService->generateSecretKey();
        $qrCodeDataUri = $this->mfaService->getQrCodeDataUri($user, $secret);

        // Store pending secret until confirmed
        $user->forceFill([
            'two_factor_pending_secret' => Crypt::encryptString($secret),
        ])->save();

        return response()->json([
            'success'     => true,
            'secret'      => $secret,
            'qr_code_url' => $qrCodeDataUri,
            'message'     => 'Scan the QR code with Google Authenticator or enter the secret key manually, then confirm with a 6-digit code.',
        ]);
    }

    /**
     * Confirm and activate MFA enrollment by submitting the first TOTP code.
     */
    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if (empty($user->two_factor_pending_secret)) {
            return response()->json([
                'success' => false,
                'message' => 'No pending two-factor setup found. Please start setup again.',
            ], 400);
        }

        try {
            $secret = Crypt::decryptString($user->two_factor_pending_secret);
            $result = $this->mfaService->confirmMfaEnrollment($user, $secret, $request->input('code'));

            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Regenerate emergency recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string',
            'code'     => 'required|string',
        ]);

        $user = $request->user();

        if (!$user->hasTwoFactorEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is not active.',
            ], 400);
        }

        try {
            $codes = $this->mfaService->regenerateRecoveryCodes(
                $user,
                $request->input('password'),
                $request->input('code')
            );

            return response()->json([
                'success'        => true,
                'message'        => 'Recovery codes have been regenerated. Previous codes are now invalid.',
                'recovery_codes' => $codes,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Disable MFA on account.
     */
    public function disable(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string',
            'code'     => 'required|string',
        ]);

        $user = $request->user();

        if (!$user->hasTwoFactorEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is not currently active.',
            ], 400);
        }

        try {
            $this->mfaService->disableMfa(
                $user,
                $request->input('password'),
                $request->input('code')
            );

            return response()->json([
                'success' => true,
                'message' => 'Two-factor authentication has been disabled.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

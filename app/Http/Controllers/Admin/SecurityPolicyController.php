<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SecurityPolicyController extends Controller
{
    /**
     * Get platform security policies.
     */
    public function getPolicies(): JsonResponse
    {
        $policies = [
            'enforce2FA'              => (bool)SystemSetting::get('security.enforce_2fa', false),
            'mfaAvailable'            => true,
            'sessionTimeoutMinutes'   => (int)SystemSetting::get('security.session_timeout_minutes', 30),
            'maxFailedLogins'         => (int)SystemSetting::get('security.max_failed_logins', 5),
            'requireKycForWithdrawal' => (bool)SystemSetting::get('security.require_kyc_withdrawal', true),
            'ipWhitelist'             => (string)SystemSetting::get('security.ip_whitelist', ''),
            'autoLockSuspiciousDeals' => (bool)SystemSetting::get('security.auto_lock_suspicious_deals', true),
        ];

        return response()->json([
            'success'  => true,
            'policies' => $policies,
        ]);
    }

    /**
     * Update platform security policies.
     */
    public function updatePolicies(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enforce2FA'              => 'sometimes|boolean',
            'sessionTimeoutMinutes'   => 'sometimes|integer|min:5|max:1440',
            'maxFailedLogins'         => 'sometimes|integer|min:1|max:20',
            'requireKycForWithdrawal' => 'sometimes|boolean',
            'ipWhitelist'             => 'nullable|string|max:1000',
            'autoLockSuspiciousDeals' => 'sometimes|boolean',
        ]);

        if (array_key_exists('enforce2FA', $validated)) {
            SystemSetting::set('security.enforce_2fa', $validated['enforce2FA'] ? '1' : '0', 'security', 'boolean');
        }

        if (array_key_exists('sessionTimeoutMinutes', $validated)) {
            SystemSetting::set('security.session_timeout_minutes', (string)$validated['sessionTimeoutMinutes'], 'security', 'integer');
        }

        if (array_key_exists('maxFailedLogins', $validated)) {
            SystemSetting::set('security.max_failed_logins', (string)$validated['maxFailedLogins'], 'security', 'integer');
        }

        if (array_key_exists('requireKycForWithdrawal', $validated)) {
            SystemSetting::set('security.require_kyc_withdrawal', $validated['requireKycForWithdrawal'] ? '1' : '0', 'security', 'boolean');
        }

        if (array_key_exists('ipWhitelist', $validated)) {
            SystemSetting::set('security.ip_whitelist', trim((string)$validated['ipWhitelist']), 'security', 'string');
        }

        if (array_key_exists('autoLockSuspiciousDeals', $validated)) {
            SystemSetting::set('security.auto_lock_suspicious_deals', $validated['autoLockSuspiciousDeals'] ? '1' : '0', 'security', 'boolean');
        }

        $user = $request->user();
        AuditLogService::log(
            'security.policies_updated',
            "Administrator {$user->email} updated platform security policies",
            ['admin_id' => $user->id, 'updated_keys' => array_keys($validated)]
        );

        return response()->json([
            'success'  => true,
            'message'  => 'Platform security policies updated and persisted successfully.',
            'policies' => [
                'enforce2FA'              => (bool)SystemSetting::get('security.enforce_2fa', false),
                'mfaAvailable'            => true,
                'sessionTimeoutMinutes'   => (int)SystemSetting::get('security.session_timeout_minutes', 30),
                'maxFailedLogins'         => (int)SystemSetting::get('security.max_failed_logins', 5),
                'requireKycForWithdrawal' => (bool)SystemSetting::get('security.require_kyc_withdrawal', true),
                'ipWhitelist'             => (string)SystemSetting::get('security.ip_whitelist', ''),
                'autoLockSuspiciousDeals' => (bool)SystemSetting::get('security.auto_lock_suspicious_deals', true),
            ],
        ]);
    }
}


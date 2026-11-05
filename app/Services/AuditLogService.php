<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuditLogService
{
    /**
     * Sensitive payload keys that must never be recorded into plain audit logs.
     */
    protected static array $redactedKeys = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'secret_key',
        'api_key',
        'store_password',
        'otp',
        'token',
        'remember_token',
        'nid_number',
        'auth_token',
        'access_token',
    ];

    /**
     * Record a high-priority administrative, security, or financial audit entry.
     * Guaranteed fail-safe: never throws exceptions that disrupt primary transactions.
     */
    public static function log(
        string $action,
        mixed $staffOrMessage = null,
        mixed $targetTypeOrDetails = null,
        mixed $targetId = null,
        array $details = [],
        ?string $ipAddress = null
    ): void {
        try {
            $staff = null;
            $description = null;
            $targetType = null;
            $finalDetails = $details;

            if ($staffOrMessage instanceof User) {
                $staff = $staffOrMessage;
                if (is_string($targetTypeOrDetails)) {
                    $targetType = $targetTypeOrDetails;
                } elseif (is_array($targetTypeOrDetails)) {
                    $finalDetails = $targetTypeOrDetails;
                }
            } elseif (is_string($staffOrMessage)) {
                $description = $staffOrMessage;
                $staff = auth()->user();
                if (is_array($targetTypeOrDetails)) {
                    $finalDetails = $targetTypeOrDetails;
                } elseif (is_string($targetTypeOrDetails)) {
                    $targetType = $targetTypeOrDetails;
                }
            } elseif (is_array($staffOrMessage)) {
                $finalDetails = $staffOrMessage;
                $staff = auth()->user();
            } else {
                $staff = auth()->user();
            }

            $ip = $ipAddress ?? request()?->ip() ?? '127.0.0.1';

            // Sanitize sensitive credentials and personally identifiable data
            $cleanDetails = static::sanitizeDetails($finalDetails);

            $entry = [
                'timestamp'   => now()->toIso8601String(),
                'action'      => $action,
                'description' => $description,
                'staff_id'    => $staff?->id,
                'staff_name'  => $staff?->name ?? 'System',
                'staff_email' => $staff?->email ?? 'system@migratepay.com',
                'staff_role'  => $staff?->role ?? 'system',
                'ip_address'  => $ip,
                'target_type' => $targetType,
                'target_id'   => $targetId,
                'details'     => $cleanDetails,
            ];

            // Append structured JSON line to local audit log file
            $json = json_encode($entry, JSON_UNESCAPED_SLASHES);
            $logPath = storage_path('logs/audit.log');

            $written = @file_put_contents($logPath, $json . PHP_EOL, FILE_APPEND | LOCK_EX);
            if ($written && function_exists('chmod')) {
                @chmod($logPath, 0640);
            }

            // Also forward to application log
            Log::info("[AUDIT] [{$action}] by {$entry['staff_email']}" . ($description ? " - {$description}" : ''), $cleanDetails);
        } catch (\Throwable $e) {
            // Fail open for logging: never crash an ongoing financial or authentication transaction
            Log::error("[AUDIT FAILURE] Failed to write audit log entry: " . $e->getMessage());
        }
    }

    /**
     * Recursively mask sensitive fields to prevent credential and PII leakage.
     */
    public static function sanitizeDetails(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $lowerKey = strtolower((string)$key);

            if (in_array($lowerKey, static::$redactedKeys, true)) {
                $sanitized[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $sanitized[$key] = static::sanitizeDetails($value);
            } elseif (is_string($value) && strlen($value) > 200) {
                // Truncate overly verbose strings (e.g. base64 or long dumps)
                $sanitized[$key] = substr($value, 0, 197) . '...';
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }
}

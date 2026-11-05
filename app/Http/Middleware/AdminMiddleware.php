<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AuditLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Authentication check
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }
            return redirect()->guest('/login');
        }

        $user = auth()->user();

        // 2. Staff authorization check
        if (!$user->isStaff()) {
            AuditLogService::log(
                'rbac.unauthorized_access',
                "Non-staff user {$user->id} attempted to access administrative area",
                ['user_id' => $user->id, 'role' => $user->role, 'ip' => $request->ip(), 'path' => $request->path()],
                'warning'
            );

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Administrative staff access required.',
                ], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        // 3. IP Whitelist check (if configured in system settings)
        $whitelist = SystemSetting::get('security.ip_whitelist');
        if (!empty($whitelist)) {
            $allowedIps = array_filter(array_map('trim', preg_split('/[,\s]+/', (string)$whitelist)));
            $clientIp = $request->ip();

            if (!empty($allowedIps) && !in_array($clientIp, $allowedIps, true) && !in_array('*', $allowedIps, true)) {
                AuditLogService::log(
                    'security.ip_blocked',
                    "Administrative access blocked from non-whitelisted IP: {$clientIp}",
                    ['user_id' => $user->id, 'ip' => $clientIp, 'allowed_ips' => $allowedIps],
                    'warning'
                );

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized. Access is restricted from your IP address.',
                    ], 403);
                }
                abort(403, 'Access denied from your IP address.');
            }
        }

        // 3.5 Administrative MFA Policy Enforcement
        $userRole = strtolower(str_replace(['_', ' '], '-', (string)$user->role));
        $enforce2fa = (bool)SystemSetting::get('security.enforce_2fa', false);

        if ($enforce2fa && in_array($userRole, ['admin', 'co-admin'], true)) {
            // Must have enrolled in MFA
            if (!$user->hasTwoFactorEnabled()) {
                AuditLogService::log(
                    'rbac.mfa_enrollment_required',
                    "Administrative access blocked: staff {$user->email} ({$user->role}) has not enrolled in mandatory MFA",
                    ['user_id' => $user->id, 'role' => $user->role, 'path' => $request->path()],
                    'warning'
                );
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success'      => false,
                        'mfa_required' => true,
                        'message'      => 'Two-factor authentication enrollment is mandatory for administrative accounts. Please activate 2FA in account security settings.',
                    ], 403);
                }
                return redirect('/auth?mfa_setup=required');
            }

            // Must have completed MFA challenge during this session
            if (!session('auth.mfa_verified', false)) {
                AuditLogService::log(
                    'rbac.mfa_verification_required',
                    "Administrative access blocked: staff {$user->email} has not verified MFA challenge in current session",
                    ['user_id' => $user->id, 'role' => $user->role, 'path' => $request->path()],
                    'warning'
                );
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success'      => false,
                        'mfa_required' => true,
                        'message'      => 'Two-factor authentication verification required to access administrative functionality.',
                    ], 403);
                }
                return redirect('/auth?mfa_challenge=required');
            }
        }

        // 4. Granular Staff RBAC Enforcement

        // Full Admin has unrestricted access
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Co-Admin: Broad operations, but restricted from modifying root admins and gateway secrets
        if ($userRole === 'co-admin') {
            if ($request->is('api/admin/gateway-settings/*') && $request->isMethod('post')) {
                return $this->forbiddenResponse($request, 'Co-Admins are not authorized to modify payment gateway credentials.');
            }
            if ($request->is('api/admin/security/policies') && $request->isMethod('post')) {
                return $this->forbiddenResponse($request, 'Co-Admins are not authorized to modify system security policies.');
            }
            if ($request->is('api/admin/users/*') && in_array($request->method(), ['PUT', 'POST', 'DELETE'])) {
                $targetUserId = $request->route('user') instanceof User ? $request->route('user')->id : $request->route('user');
                if ($targetUserId) {
                    $targetUser = User::find($targetUserId);
                    if ($targetUser && $targetUser->isAdmin()) {
                        return $this->forbiddenResponse($request, 'Co-Admins cannot modify root administrator accounts.');
                    }
                }
            }
            return $next($request);
        }

        // Editorial Team: Limited to Content, CMS, Blog, Categories, Banners, Media
        if (in_array($userRole, ['editorial', 'editorial-team'], true)) {
            $isAllowed = $request->is('admin*')
                || $request->is('api/admin/analytics*')
                || $request->is('api/admin/reports/ledger')
                || $request->is('api/admin/cms*')
                || $request->is('api/admin/blog*')
                || $request->is('api/admin/categories*')
                || $request->is('api/categories*')
                || $request->is('api/admin/banners*')
                || $request->is('api/admin/media*')
                || $request->is('api/admin/announcements*')
                || $request->is('api/admin/menus*')
                || $request->is('api/notifications*');

            if (!$isAllowed) {
                AuditLogService::log(
                    'rbac.access_denied',
                    "Editorial staff user {$user->id} denied access to {$request->method()} {$request->path()}",
                    ['user_id' => $user->id, 'role' => $user->role, 'path' => $request->path()],
                    'warning'
                );
                return $this->forbiddenResponse($request, 'Unauthorized. Editorial staff can only access CMS, blog, and content management.');
            }

            return $next($request);
        }

        // Support Team: Limited to Support Tickets, Dispute Reports, AI Assistant, Reviews, and Read-Only inquiries
        if (in_array($userRole, ['support', 'support-team'], true)) {
            // Block financial approvals/rejections outright
            if ($request->is('*/approve', '*/reject', '*/approve-funds', '*/resolve')) {
                if ($request->is('api/admin/reports/*/resolve')) {
                    // Support is allowed to resolve customer dispute reports
                } else {
                    AuditLogService::log(
                        'rbac.access_denied',
                        "Support staff user {$user->id} attempted unauthorized financial approval on {$request->path()}",
                        ['user_id' => $user->id, 'role' => $user->role, 'path' => $request->path()],
                        'warning'
                    );
                    return $this->forbiddenResponse($request, 'Unauthorized. Support staff are not authorized to approve or reject financial transactions.');
                }
            }

            // Block user modifications
            if ($request->is('api/admin/users/*') && in_array($request->method(), ['PUT', 'POST', 'DELETE'])) {
                return $this->forbiddenResponse($request, 'Unauthorized. Support staff cannot modify user accounts or verification status.');
            }

            // Allowed support paths
            $isAllowed = $request->is('admin*')
                || $request->is('api/admin/support-tickets*')
                || $request->is('api/admin/reports*')
                || $request->is('api/admin/ai-*')
                || $request->is('api/admin/knowledge-base*')
                || $request->is('api/admin/reviews*')
                || ($request->is('api/admin/users*') && $request->isMethod('get'))
                || ($request->is('api/admin/deals*') && $request->isMethod('get'))
                || ($request->is('api/admin/deposits*') && $request->isMethod('get'))
                || ($request->is('api/admin/withdrawals*') && $request->isMethod('get'))
                || $request->is('api/notifications*');

            if (!$isAllowed) {
                AuditLogService::log(
                    'rbac.access_denied',
                    "Support staff user {$user->id} denied access to {$request->method()} {$request->path()}",
                    ['user_id' => $user->id, 'role' => $user->role, 'path' => $request->path()],
                    'warning'
                );
                return $this->forbiddenResponse($request, 'Unauthorized. Insufficient staff permissions for this action.');
            }

            return $next($request);
        }

        return $this->forbiddenResponse($request, 'Unauthorized staff role.');
    }

    /**
     * Generate forbidden response based on request format.
     */
    protected function forbiddenResponse(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 403);
        }

        abort(403, $message);
    }
}

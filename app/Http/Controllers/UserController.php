<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get user reviews (Public / Customer Facing endpoint).
     * Accessible by customers to inspect counterparty reputation.
     * Preserves: resources/js/api/userAndSupport.js: getUserReviews(userId)
     */
    public function reviews(User $user): JsonResponse
    {
        $reviews = $user->reviewsReceived()->with('reviewer:id,name,username,profile_photo')->latest()->get();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'verification_status' => $user->verification_status,
            ],
            'reviews' => $reviews,
        ]);
    }

    /**
     * Admin list users.
     * Protected by AdminMiddleware / policy.
     * Preserves: resources/js/api/adminApi.js: apiClient.get('/api/users')
     */
    public function index(): JsonResponse
    {
        $users = User::latest()->get();

        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }

    /**
     * Admin update user status (Active / Inactive / Suspended).
     * Protected by AdminMiddleware / policy.
     * Preserves: resources/js/api/adminApi.js: apiClient.put('/api/users/{id}/status')
     */
    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $authUser = $request->user();

        // Privilege escalation barrier
        if ($user->isAdmin() && !$authUser->isAdmin()) {
            AuditLogService::log(
                'rbac.privilege_escalation_blocked',
                "User {$authUser->id} ({$authUser->role}) attempted to modify root admin user {$user->id}",
                ['actor_id' => $authUser->id, 'target_user_id' => $user->id],
                'warning'
            );
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only primary administrators can modify root admin accounts.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:Active,active,Inactive,inactive,Suspended,suspended',
        ]);

        if ($user->id === $authUser->id && in_array(strtolower($validated['status']), ['inactive', 'suspended'])) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot deactivate or suspend your own administrative account.',
            ], 422);
        }

        $user->status = ucfirst($validated['status']);
        $user->save();

        AuditLogService::log(
            'admin.user_status_updated',
            "Admin {$authUser->email} updated status of user {$user->id} ({$user->email}) to {$user->status}",
            ['admin_id' => $authUser->id, 'target_user_id' => $user->id, 'status' => $user->status]
        );

        return response()->json([
            'success' => true,
            'message' => "User status updated to {$user->status}.",
            'user' => $user,
        ]);
    }

    /**
     * Admin update verification status (VERIFIED / REJECTED / PENDING).
     * Protected by AdminMiddleware / policy.
     * Preserves: resources/js/api/adminApi.js: apiClient.put('/api/users/{id}/verification')
     */
    public function updateVerification(Request $request, User $user): JsonResponse
    {
        $authUser = $request->user();

        // Privilege escalation barrier
        if ($user->isAdmin() && !$authUser->isAdmin()) {
            AuditLogService::log(
                'rbac.privilege_escalation_blocked',
                "User {$authUser->id} ({$authUser->role}) attempted to alter verification of root admin user {$user->id}",
                ['actor_id' => $authUser->id, 'target_user_id' => $user->id],
                'warning'
            );
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only primary administrators can alter root admin verification.',
            ], 403);
        }

        $validated = $request->validate([
            'verification_status' => 'required|string|in:VERIFIED,NOT_VERIFIED,REJECTED,PENDING',
            'rejection_reason' => 'nullable|string',
        ]);

        $user->verification_status = $validated['verification_status'];
        if ($validated['verification_status'] === 'VERIFIED') {
            $user->verified_at = now();
            $user->verified_by = auth()->id();
            $user->rejection_reason = null;
        } elseif ($validated['verification_status'] === 'REJECTED') {
            $user->rejection_reason = $validated['rejection_reason'] ?? 'Verification documents rejected by administrator.';
        }
        $user->verification_updated_at = now();
        $user->save();

        AuditLogService::log(
            'admin.user_verification_updated',
            "Admin {$authUser->email} updated verification of user {$user->id} to {$user->verification_status}",
            ['admin_id' => $authUser->id, 'target_user_id' => $user->id, 'verification_status' => $user->verification_status]
        );

        return response()->json([
            'success' => true,
            'message' => "User verification status updated to {$user->verification_status}.",
            'user' => $user,
        ]);
    }
}

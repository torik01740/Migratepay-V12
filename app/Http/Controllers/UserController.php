<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $validated = $request->validate([
            'status' => 'required|string|in:Active,active,Inactive,inactive,Suspended,suspended',
        ]);

        $user->status = ucfirst($validated['status']);
        $user->save();

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

        return response()->json([
            'success' => true,
            'message' => "User verification status updated to {$user->verification_status}.",
            'user' => $user,
        ]);
    }
}

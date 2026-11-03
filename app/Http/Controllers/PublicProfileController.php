<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    /**
     * List public user profiles (excluding administrators).
     */
    public function index(Request $request)
    {
        $query = User::with('reviewsReceived')
            ->where('role', '!=', 'admin');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('user_id_string', 'like', "%{$search}%");
            });
        }

        if ($request->filled('location')) {
            $location = $request->input('location');
            $query->where(function ($q) use ($location) {
                $q->where('division', 'like', "%{$location}%")
                  ->orWhere('district', 'like', "%{$location}%");
            });
        }

        $profiles = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success'  => true,
                'profiles' => $profiles,
            ]);
        }

        return view('pages.profiles', compact('profiles'));
    }

    /**
     * Show public profile for a user.
     */
    public function show(Request $request, string $identifier)
    {
        $user = User::with('reviewsReceived.reviewer')
            ->where(function ($q) use ($identifier) {
                $q->where('user_id_string', $identifier)
                  ->orWhere('username', $identifier)
                  ->orWhere('id', $identifier);
            })
            ->firstOrFail();

        // Never expose administrator profile publicly
        if ($user->role === 'admin') {
            abort(404, 'User profile not found.');
        }

        $completedDealsCount = Deal::where(function ($q) use ($user) {
            $q->where('initiator_id', $user->id)
              ->orWhere('counterparty_id', $user->id);
        })->where('status', 'completed')->count();

        $responseRate = '100%';

        $data = [
            'success'              => true,
            'user'                 => $user,
            'completed_deals_count'=> $completedDealsCount,
            'response_rate'        => $responseRate,
        ];

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($data);
        }

        return view('pages.profile-show', compact('user', 'completedDealsCount', 'responseRate'));
    }
}

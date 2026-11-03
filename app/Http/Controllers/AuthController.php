<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Authenticate a user and start their session.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials['email'] = strtolower(trim($credentials['email']));

        if (Auth::attempt($credentials, true)) {
            $user = Auth::user();

            // Check if user is suspended or banned
            if (in_array(strtolower($user->status), ['suspended', 'banned', 'inactive'])) {
                Auth::logout();
                $request->session()->invalidate();
                return response()->json([
                    'success' => false,
                    'message' => "Your account is {$user->status}. Please contact support.",
                ], 403);
            }

            $request->session()->regenerate();

            return response()->json([
                'success'  => true,
                'message'  => 'Login successful',
                'user'     => $user,
                'role'     => $user->role,
                'redirect' => $user->role === 'admin' ? '/admin' : '/dashboard',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'The provided credentials do not match our records.',
        ], 401);
    }

    /**
     * Terminate the user session.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Register a new user account.
     */
    public function register(Request $request): JsonResponse
    {
        if ($request->has('email')) {
            $request->merge(['email' => strtolower(trim($request->input('email')))]);
        }
        if ($request->has('username')) {
            $request->merge(['username' => strtolower(trim($request->input('username')))]);
        }

        $validatedData = $request->validate([
            'name'          => 'required|string|max:255',
            'username'      => 'required|string|max:255|unique:users',
            'email'         => 'required|string|email|max:255|unique:users',
            'phone'         => 'nullable|string|max:30|unique:users',
            'password'      => 'required|string|min:8|confirmed',
            'date_of_birth' => 'nullable|date',
            'nid_number'    => 'nullable|string|max:100',
            'division'      => 'nullable|string|max:100',
            'district'      => 'nullable|string|max:100',
            'gender'        => 'nullable|string|in:Male,Female,Other',
        ]);

        $user = User::create([
            'user_id_string'      => 'USR-' . time() . rand(10, 99),
            'name'                => $validatedData['name'],
            'username'            => strtolower(trim($validatedData['username'])),
            'email'               => strtolower(trim($validatedData['email'])),
            'phone'               => $validatedData['phone'] ?? null,
            'password'            => Hash::make($validatedData['password']),
            'date_of_birth'       => $validatedData['date_of_birth'] ?? null,
            'nid_number'          => $validatedData['nid_number'] ?? null,
            'division'            => $validatedData['division'] ?? null,
            'district'            => $validatedData['district'] ?? null,
            'gender'              => $validatedData['gender'] ?? null,
            'wallet_balance'      => '0.00',
            'status'              => 'Active',
            'role'                => 'user',
            'verification_status' => 'NOT_VERIFIED',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully! Please log in with your credentials.',
            'user'    => $user,
        ], 201);
    }

    /**
     * Check availability of username or email during registration.
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $email = $request->has('email') ? strtolower(trim($request->input('email'))) : null;
        $username = $request->has('username') ? strtolower(trim($request->input('username'))) : null;

        if ($email && User::where('email', $email)->exists()) {
            return response()->json(['success' => false, 'message' => 'The email has already been taken.']);
        }
        if ($username && User::where('username', $username)->exists()) {
            return response()->json(['success' => false, 'message' => 'The username has already been taken.']);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Return currently authenticated user data with fresh wallet balance.
     */
    public function me(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $user = User::find(Auth::id());

        return response()->json([
            'success' => true,
            'user'    => $user,
        ]);
    }

    /**
     * Authenticated user: Request KYC account verification.
     */
    public function requestVerification(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if ($user->verification_status === 'VERIFIED') {
            return response()->json(['success' => false, 'message' => 'Your account is already verified.'], 400);
        }

        $request->validate([
            'id_type'       => 'nullable|string|max:50',
            'nid_number'    => 'nullable|string|max:100',
            'document_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'note'          => 'nullable|string|max:1000',
        ]);

        if ($request->has('id_type')) {
            $user->id_type = $request->id_type;
        }
        if ($request->has('nid_number')) {
            $user->nid_number = $request->nid_number;
        }

        if ($request->hasFile('document_file')) {
            $docPath = $request->file('document_file')->store("kyc/{$user->id}", 'private');
            $user->kyc_document_path = $docPath;
        }

        $user->verification_status = 'PENDING';
        $user->verification_updated_at = now();
        $user->rejection_reason = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Verification request submitted successfully and is pending review.',
            'user'    => $user,
        ]);
    }

    /**
     * Authenticated user: Update password.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password does not match.',
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }

    /**
     * Handle Forgot Password — Generate cryptographically secure OTP.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $email = strtolower(trim($request->email));

        $throttleKey = 'forgot-password:' . $request->ip() . '|' . $email;
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => "Too many password reset requests. Please try again in {$seconds} seconds.",
            ], 429);
        }
        RateLimiter::hit($throttleKey, 300);

        $user = User::where('email', $email)->first();

        if ($user) {
            $otp = sprintf("%06d", random_int(100000, 999999));
            $hashedOtp = hash('sha256', $otp);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token'      => $hashedOtp,
                    'created_at' => now(),
                ]
            );

            try {
                Mail::raw("Your MigratePay password reset verification code is: {$otp}. It is valid for 5 minutes.", function ($message) use ($user) {
                    $message->to($user->email)->subject('Password Reset Verification Code - MigratePay');
                });
            } catch (\Throwable $e) {
                Log::error('Password reset email failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'If an account exists with that email address, an OTP verification code has been sent.',
        ]);
    }

    /**
     * Verify OTP (Step 2)
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string',
        ]);

        $email = strtolower(trim($request->email));
        $throttleKey = 'verify-otp:' . $request->ip() . '|' . $email;

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => "Maximum verification attempts exceeded. Please request a new OTP in {$seconds} seconds.",
            ], 429);
        }

        $hashedOtp = hash('sha256', trim($request->otp));

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $hashedOtp)
            ->first();

        if (!$record) {
            RateLimiter::hit($throttleKey, 300);
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code.',
            ], 400);
        }

        if (Carbon::parse($record->created_at)->addMinutes(5)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired. Please request a new one.',
            ], 400);
        }

        RateLimiter::clear($throttleKey);

        return response()->json([
            'success' => true,
            'message' => 'Verification code verified successfully.',
        ]);
    }

    /**
     * Reset Password using validated OTP.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'otp'      => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = strtolower(trim($request->email));
        $hashedOtp = hash('sha256', trim($request->otp));

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $hashedOtp)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code.',
            ], 400);
        }

        if (Carbon::parse($record->created_at)->addMinutes(5)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired. Please request a new one.',
            ], 400);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User account not found.',
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You can now log in with your new password.',
        ]);
    }
}

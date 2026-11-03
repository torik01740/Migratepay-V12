<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Update authenticated user's profile information and photos.
     */
    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if ($request->filled('username') && is_string($request->input('username'))) {
            $username = ltrim(trim($request->input('username')), '@');
            $request->merge(['username' => strtolower($username)]);
        }

        if ($request->has('dob')) {
            $request->merge(['date_of_birth' => $request->input('dob')]);
        }
        if ($request->has('idType')) {
            $request->merge(['id_type' => $request->input('idType')]);
        }
        if ($request->has('nid')) {
            $request->merge(['nid_number' => $request->input('nid')]);
        }

        $validated = $request->validate([
            'username'           => 'nullable|string|max:100|unique:users,username,' . $user->id,
            'name'               => 'nullable|string|max:255',
            'phone'              => 'nullable|string|max:50',
            'date_of_birth'      => 'nullable|date',
            'gender'             => 'nullable|string|max:50',
            'id_type'            => 'nullable|string|max:50',
            'nid_number'         => 'nullable|string|max:100',
            'headline'           => 'nullable|string|max:255',
            'location'           => 'nullable|string|max:255',
            'address'            => 'nullable|string|max:500',
            'bio'                => 'nullable|string',
            'skills'             => 'nullable|array',
            'languages'          => 'nullable|array',
            'education'          => 'nullable|array',
            'certifications'     => 'nullable|array',
            'professional_links' => 'nullable|array',
            'cover_photo'        => 'nullable|string',
            'profile_photo'      => 'nullable|string',
            'cover_position_x'   => 'nullable|numeric',
            'cover_position_y'   => 'nullable|numeric',
            'profile_position_x' => 'nullable|numeric',
            'profile_position_y' => 'nullable|numeric',
        ]);

        foreach (['profile_photo', 'cover_photo'] as $photoType) {
            if (!empty($validated[$photoType]) && preg_match('/^data:image\/([^;]+);base64,/', $validated[$photoType], $matches)) {
                $data = substr($validated[$photoType], strpos($validated[$photoType], ',') + 1);
                $decoded = base64_decode($data);
                $extension = in_array(strtolower($matches[1] ?? ''), ['png', 'jpg', 'jpeg', 'webp']) ? strtolower($matches[1]) : 'png';
                $fileName = $photoType . 's/' . uniqid() . '.' . $extension;
                Storage::disk('public')->put($fileName, $decoded);
                $validated[$photoType] = 'storage/' . $fileName;
            } elseif (isset($validated[$photoType]) && empty($validated[$photoType])) {
                unset($validated[$photoType]);
            }
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user'    => $user->fresh(),
        ]);
    }
}

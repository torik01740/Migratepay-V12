<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Admin: List all banners.
     */
    public function index(): JsonResponse
    {
        $banners = Banner::orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $banners]);
    }

    /**
     * Public: List active banners for frontend display.
     */
    public function publicIndex(): JsonResponse
    {
        $banners = Banner::active()->get();
        return response()->json(['data' => $banners]);
    }

    /**
     * Admin: Store a new banner.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'status'            => 'required|string|in:active,scheduled,draft,expired,disabled',
            'placement'         => 'required|string|in:above_header,below_footer',
            'position_behavior' => 'required|string|in:static,sticky,fixed',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'targeting'         => 'nullable|array',
            'design'            => 'nullable|array',
            'elements'          => 'nullable|array',
        ]);

        $banner = Banner::create($validated);
        return response()->json(['data' => $banner], 201);
    }

    /**
     * Admin: Show a banner.
     */
    public function show(Banner $banner): JsonResponse
    {
        return response()->json(['data' => $banner]);
    }

    /**
     * Admin: Update a banner.
     */
    public function update(Request $request, Banner $banner): JsonResponse
    {
        $validated = $request->validate([
            'name'              => 'sometimes|string|max:255',
            'status'            => 'sometimes|string|in:active,scheduled,draft,expired,disabled',
            'placement'         => 'sometimes|string|in:above_header,below_footer',
            'position_behavior' => 'sometimes|string|in:static,sticky,fixed',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'targeting'         => 'nullable|array',
            'design'            => 'nullable|array',
            'elements'          => 'nullable|array',
        ]);

        $banner->update($validated);
        return response()->json(['data' => $banner]);
    }

    /**
     * Admin: Delete a banner.
     */
    public function destroy(Banner $banner): JsonResponse
    {
        $banner->delete();
        return response()->json(['message' => 'Banner deleted successfully']);
    }
}

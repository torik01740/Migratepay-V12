<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Admin: Get all coupons with statistics.
     */
    public function index(): JsonResponse
    {
        $coupons = Coupon::withCount('usages')->orderBy('created_at', 'desc')->get();

        $stats = [
            'total_coupons'        => Coupon::count(),
            'active_coupons'       => Coupon::where('is_active', true)->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', now());
            })->count(),
            'expired_coupons'      => Coupon::where('expiry_date', '<', now())->count(),
            'total_uses'           => CouponUsage::count(),
            'total_discount_given' => CouponUsage::sum('discount_amount'),
        ];

        return response()->json([
            'success' => true,
            'stats'   => $stats,
            'coupons' => $coupons,
        ]);
    }

    /**
     * Admin: Store a new coupon.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'code'              => 'required|string|max:50|unique:coupons,code',
            'discount_type'     => 'required|in:percentage,fixed',
            'discount_value'    => 'required|numeric|min:0.01',
            'total_usage_limit' => 'nullable|integer|min:1',
            'usage_per_user'    => 'nullable|integer|min:1',
            'start_date'        => 'nullable|date',
            'expiry_date'       => 'nullable|date|after_or_equal:start_date',
            'is_active'         => 'boolean',
        ]);

        if ($request->discount_type === 'percentage' && $request->discount_value > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Percentage discount cannot exceed 100%.',
            ], 422);
        }

        $coupon = Coupon::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Coupon created successfully.',
            'coupon'  => $coupon,
        ], 201);
    }

    /**
     * Admin: Show a specific coupon with usage history.
     */
    public function show(int $id): JsonResponse
    {
        $coupon = Coupon::findOrFail($id);
        $usages = CouponUsage::with(['user', 'deal'])->where('coupon_id', $id)->orderBy('created_at', 'desc')->get();

        $stats = [
            'total_discount_given' => $usages->sum('discount_amount'),
            'average_discount'     => $usages->count() > 0 ? $usages->avg('discount_amount') : 0,
        ];

        return response()->json([
            'success' => true,
            'coupon'  => $coupon,
            'usages'  => $usages,
            'stats'   => $stats,
        ]);
    }

    /**
     * Admin: Update coupon.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'code'              => 'required|string|max:50|unique:coupons,code,' . $id,
            'discount_type'     => 'required|in:percentage,fixed',
            'discount_value'    => 'required|numeric|min:0.01',
            'total_usage_limit' => 'nullable|integer|min:1',
            'usage_per_user'    => 'nullable|integer|min:1',
            'start_date'        => 'nullable|date',
            'expiry_date'       => 'nullable|date|after_or_equal:start_date',
            'is_active'         => 'boolean',
        ]);

        $coupon->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Coupon updated successfully.',
            'coupon'  => $coupon,
        ]);
    }

    /**
     * Admin: Delete coupon.
     */
    public function destroy(int $id): JsonResponse
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully.',
        ]);
    }
}

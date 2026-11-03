<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Submit a review for a completed deal.
     */
    public function store(Request $request, int $dealId): JsonResponse
    {
        $request->validate([
            'rating'        => 'required|integer|min:1|max:5',
            'communication' => 'nullable|integer|min:1|max:5',
            'behavior'      => 'nullable|integer|min:1|max:5',
            'delivery'      => 'nullable|integer|min:1|max:5',
            'reliability'   => 'nullable|integer|min:1|max:5',
            'comment'       => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        $deal = Deal::findOrFail($dealId);

        if (!in_array($deal->status, ['completed', 'buyer_review', 'released'])) {
            return response()->json([
                'success' => false,
                'message' => 'Can only review completed deals.',
            ], 422);
        }

        $isBuyer = ($deal->initiator_role === 'buyer' && $deal->initiator_id === $user->id)
            || ($deal->initiator_role === 'seller' && $deal->counterparty_id === $user->id);
        $isSeller = ($deal->initiator_role === 'seller' && $deal->initiator_id === $user->id)
            || ($deal->initiator_role === 'buyer' && $deal->counterparty_id === $user->id);

        if (!$isBuyer && !$isSeller) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a party to this deal.',
            ], 403);
        }

        $role = $isBuyer ? 'buyer' : 'seller';

        $alreadyReviewed = Review::where('deal_id', $deal->id)
            ->where('reviewer_id', $user->id)
            ->exists();

        if ($alreadyReviewed) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted a review for this deal.',
            ], 422);
        }

        $revieweeId = $isBuyer
            ? ($deal->initiator_role === 'seller' ? $deal->initiator_id : $deal->counterparty_id)
            : ($deal->initiator_role === 'buyer' ? $deal->initiator_id : $deal->counterparty_id);

        if (!$revieweeId) {
            return response()->json([
                'success' => false,
                'message' => 'Counterparty account not found.',
            ], 422);
        }

        $comm = $request->input('communication', $request->input('rating'));
        $beh = $request->input('behavior', $request->input('rating'));
        $del = $request->input('delivery', $request->input('rating'));
        $rel = $request->input('reliability', $request->input('rating'));

        $review = Review::create([
            'deal_id'       => $deal->id,
            'reviewer_id'   => $user->id,
            'reviewee_id'   => $revieweeId,
            'role'          => $role,
            'communication' => $comm,
            'behavior'      => $beh,
            'delivery'      => $del,
            'reliability'   => $rel,
            'comment'       => $request->input('comment'),
        ]);

        if ($isBuyer) {
            $deal->buyer_reviewed = true;
        } else {
            $deal->seller_reviewed = true;
        }
        $deal->save();

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully.',
            'review'  => $review->load('reviewer'),
        ], 201);
    }

    /**
     * Check if user can review this deal.
     */
    public function canReview(int $dealId): JsonResponse
    {
        $user = Auth::user();
        $deal = Deal::findOrFail($dealId);

        if (!in_array($deal->status, ['completed', 'buyer_review', 'released'])) {
            return response()->json(['can_review' => false, 'reason' => 'Deal not completed.']);
        }

        $isBuyer = ($deal->initiator_role === 'buyer' && $deal->initiator_id === $user->id)
            || ($deal->initiator_role === 'seller' && $deal->counterparty_id === $user->id);
        $isSeller = ($deal->initiator_role === 'seller' && $deal->initiator_id === $user->id)
            || ($deal->initiator_role === 'buyer' && $deal->counterparty_id === $user->id);

        if (!$isBuyer && !$isSeller) {
            return response()->json(['can_review' => false, 'reason' => 'Not a party to this deal.']);
        }

        $alreadyReviewed = Review::where('deal_id', $deal->id)
            ->where('reviewer_id', $user->id)
            ->exists();

        if ($alreadyReviewed) {
            return response()->json(['can_review' => false, 'reason' => 'Already reviewed.']);
        }

        return response()->json([
            'can_review' => true,
            'deal_id'    => $deal->deal_id,
            'role'       => $isBuyer ? 'buyer' : 'seller',
        ]);
    }

    /**
     * Get reviews received by a user (Public endpoint).
     */
    public function userReviews(int $userId): JsonResponse
    {
        $reviews = Review::with(['reviewer', 'deal'])
            ->where('reviewee_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $overallAvg = $reviews->isNotEmpty()
            ? round($reviews->avg(fn ($r) => ($r->communication + $r->behavior + $r->delivery + $r->reliability) / 4), 1)
            : null;

        return response()->json([
            'success'     => true,
            'reviews'     => $reviews,
            'overall_avg' => $overallAvg,
        ]);
    }

    /**
     * Admin: List all reviews.
     */
    public function adminIndex(): JsonResponse
    {
        $reviews = Review::with(['reviewer', 'reviewee', 'deal'])->latest()->get();
        return response()->json([
            'success' => true,
            'reviews' => $reviews,
        ]);
    }

    /**
     * Admin: Delete or moderate a review.
     */
    public function destroy(int $id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted.',
        ]);
    }
}

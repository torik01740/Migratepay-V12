<?php

namespace App\Http\Controllers;

use App\Services\DealService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DealController extends Controller
{
    public function __construct(
        protected DealService $dealService
    ) {}

    /**
     * Public charge calculator API.
     */
    public function calculateChargeAPI(Request $request): JsonResponse
    {
        $amount = (string)$request->query('amount', '1000');
        $payer = (string)($request->query('charge_payer') ?? $request->query('payer', 'shared_50'));
        $coupon = $request->query('coupon');
        $sellerPct = (string)$request->query('seller_pct', '0');
        $buyerPct = (string)$request->query('buyer_pct', '0');

        $result = $this->dealService->calculateCharge(
            $amount,
            $payer,
            $coupon,
            Auth::user(),
            $sellerPct,
            $buyerPct
        );

        return response()->json(array_merge([
            'success' => true,
            'data'    => $result,
        ], $result));
    }

    /**
     * Create a new deal.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'amount'             => 'required|numeric|min:10',
            'counterparty_email' => 'required|email',
            'initiator_role'     => 'required|in:buyer,seller',
            'charge_payer'       => 'required|in:shared_50,buyer_100,seller_100,custom',
            'seller_pct'         => 'nullable|numeric|min:0|max:100',
            'buyer_pct'          => 'nullable|numeric|min:0|max:100',
            'deadline'           => 'nullable|date',
            'category_id'        => 'nullable|integer',
            'custom_category'    => 'nullable|string|max:100',
            'coupon_code'        => 'nullable|string|max:50',
        ]);

        $deal = $this->dealService->createDeal(Auth::user(), $validated);

        return response()->json([
            'success' => true,
            'message' => 'Deal created successfully.',
            'deal'    => $deal,
        ], 201);
    }

    /**
     * List user deals.
     */
    public function index(): JsonResponse
    {
        $deals = $this->dealService->getUserDeals(Auth::user());
        return response()->json([
            'success' => true,
            'deals'   => $deals,
        ]);
    }

    /**
     * Show a single deal.
     */
    public function show(int $id): JsonResponse
    {
        $deal = $this->dealService->getDealForUser($id, Auth::user());
        return response()->json([
            'success' => true,
            'deal'    => $deal,
        ]);
    }

    /**
     * Accept a deal invitation.
     */
    public function accept(int $id): JsonResponse
    {
        $deal = $this->dealService->acceptDeal($id, Auth::user());
        return response()->json([
            'success' => true,
            'message' => 'Deal accepted successfully.',
            'deal'    => $deal,
        ]);
    }

    /**
     * Decline a deal invitation.
     */
    public function decline(int $id): JsonResponse
    {
        $deal = $this->dealService->declineDeal($id, Auth::user());
        return response()->json([
            'success' => true,
            'message' => 'Deal invitation declined.',
            'deal'    => $deal,
        ]);
    }

    /**
     * Release funds from escrow to seller.
     */
    public function release(int $id): JsonResponse
    {
        $result = $this->dealService->releaseFunds($id, Auth::user());
        return response()->json([
            'success'        => true,
            'message'        => 'Funds released to seller successfully.',
            'deal'           => $result['deal'],
            'wallet_balance' => $result['wallet_balance'],
        ]);
    }

    /**
     * Send workspace chat message.
     */
    public function sendMessage(Request $request, int $id): JsonResponse
    {
        $request->validate(['text' => 'required|string']);
        $deal = $this->dealService->sendWorkspaceMessage($id, Auth::user(), $request->input('text'));

        return response()->json([
            'success' => true,
            'deal'    => $deal,
        ]);
    }

    /**
     * Submit work or deliverables for a deal.
     */
    public function submitWork(Request $request, int $id): JsonResponse
    {
        $files = $request->file('files') ?? ($request->hasFile('file') ? [$request->file('file')] : []);
        $fileName = $request->input('file_name');
        $fileSize = $request->input('file_size');

        $deal = $this->dealService->submitWorkDeliverable($id, Auth::user(), $files, $fileName, $fileSize);

        return response()->json([
            'success' => true,
            'message' => 'Work submitted successfully.',
            'deal'    => $deal,
        ]);
    }

    /**
     * Buyer reviews work submission.
     */
    public function reviewSubmission(Request $request, int $id): JsonResponse
    {
        $request->validate(['decision' => 'required|in:approved,rejected']);
        $result = $this->dealService->reviewSubmission($id, Auth::user(), $request->input('decision'));

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'deal'    => $result['deal'],
        ]);
    }

    /**
     * Request deadline extension.
     */
    public function requestExtension(Request $request, int $id): JsonResponse
    {
        $request->validate(['days' => 'required|integer|min:1|max:90']);
        $deal = $this->dealService->requestExtension($id, Auth::user(), (int)$request->input('days'));

        return response()->json([
            'success' => true,
            'message' => 'Extension requested.',
            'deal'    => $deal,
        ]);
    }

    /**
     * Buyer approves deadline extension.
     */
    public function approveExtension(Request $request, int $id): JsonResponse
    {
        $request->validate(['extension_index' => 'required|integer|min:0']);
        $deal = $this->dealService->approveExtension($id, Auth::user(), (int)$request->input('extension_index'));

        return response()->json([
            'success' => true,
            'message' => 'Extension approved.',
            'deal'    => $deal,
        ]);
    }

    /**
     * Report an issue in a deal.
     */
    public function reportIssue(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'reason'      => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
        ]);

        $report = $this->dealService->reportIssue($id, Auth::user(), $validated);

        return response()->json([
            'success' => true,
            'message' => 'Issue reported successfully.',
            'report'  => $report,
        ], 201);
    }

    /**
     * Get reports for a deal.
     */
    public function getReports(int $id): JsonResponse
    {
        $reports = $this->dealService->getReports($id, Auth::user());
        return response()->json([
            'success' => true,
            'reports' => $reports,
        ]);
    }

    /**
     * Request custom service for a deal.
     */
    public function requestCustomService(int $id): JsonResponse
    {
        $result = $this->dealService->requestCustomService($id, Auth::user());
        return response()->json([
            'success' => true,
            'message' => 'Custom service requested.',
            'charge'  => $result['charge'],
            'deal'    => $result['deal'],
        ]);
    }

    /**
     * Admin: List all deals.
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $deals = $this->dealService->getAdminDeals($request->query('status'));
        return response()->json([
            'success' => true,
            'deals'   => $deals,
        ]);
    }

    /**
     * Admin: Approve release of funds.
     */
    public function adminApproveFunds(int $id): JsonResponse
    {
        $result = $this->dealService->adminApproveFunds($id, Auth::user());
        return response()->json([
            'success'        => true,
            'message'        => 'Funds released by admin.',
            'deal'           => $result['deal'],
            'wallet_balance' => $result['wallet_balance'],
        ]);
    }

    /**
     * Admin: List all reports.
     */
    public function adminReports(Request $request): JsonResponse
    {
        $reports = $this->dealService->getAdminReports($request->query('status'));
        return response()->json([
            'success' => true,
            'reports' => $reports,
        ]);
    }

    /**
     * Admin: Resolve a report.
     */
    public function adminResolveReport(Request $request, int $id): JsonResponse
    {
        $request->validate(['resolution_note' => 'required|string|max:1000']);
        $report = $this->dealService->resolveReport($id, Auth::user(), $request->input('resolution_note'));

        return response()->json([
            'success' => true,
            'message' => 'Report resolved.',
            'report'  => $report,
        ]);
    }
}

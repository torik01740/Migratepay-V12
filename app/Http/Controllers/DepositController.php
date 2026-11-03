<?php

namespace App\Http\Controllers;

use App\Services\DepositService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepositController extends Controller
{
    public function __construct(
        protected DepositService $depositService
    ) {}

    /**
     * Get user deposit history.
     */
    public function index(): JsonResponse
    {
        $deposits = $this->depositService->getUserDeposits(Auth::id());
        return response()->json([
            'success'  => true,
            'deposits' => $deposits,
        ]);
    }

    /**
     * Submit a new deposit request.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount'          => 'required|numeric|min:1',
            'gateway'         => 'required|string|max:50',
            'proof_note'      => 'nullable|string|max:500',
            'screenshot_path' => 'nullable|string',
            'screenshot'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $screenshotFile = $request->file('screenshot');
        $deposit = $this->depositService->createDeposit(Auth::user(), $validated, $screenshotFile);

        return response()->json([
            'success' => true,
            'message' => 'Deposit request submitted successfully.',
            'deposit' => $deposit,
        ], 201);
    }

    /**
     * Upload deposit proof screenshot to private storage.
     */
    public function uploadScreenshot(Request $request): JsonResponse
    {
        $request->validate([
            'screenshot' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $result = $this->depositService->storeScreenshot($request->file('screenshot'));

        return response()->json([
            'success' => true,
            'path'    => $result['path'],
            'url'     => $result['url'],
        ]);
    }

    /**
     * Admin: List all deposits with optional status filter.
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $deposits = $this->depositService->getAdminDeposits($request->query('status'));
        return response()->json([
            'success'  => true,
            'deposits' => $deposits,
        ]);
    }

    /**
     * Admin: Approve a deposit request.
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $request->validate(['admin_note' => 'nullable|string|max:500']);
        $result = $this->depositService->approveDeposit($id, Auth::user(), $request->input('admin_note'));

        return response()->json([
            'success'        => true,
            'message'        => 'Deposit approved successfully.',
            'deposit'        => $result['deposit'],
            'wallet_balance' => $result['wallet_balance'],
        ]);
    }

    /**
     * Admin: Reject a deposit request.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate(['admin_note' => 'required|string|max:500']);
        $deposit = $this->depositService->rejectDeposit($id, Auth::user(), $request->input('admin_note'));

        return response()->json([
            'success' => true,
            'message' => 'Deposit rejected.',
            'deposit' => $deposit,
        ]);
    }

    /**
     * Admin: Mark deposit notification as read.
     */
    public function markAsRead(int $id): JsonResponse
    {
        $deposit = $this->depositService->markAsRead($id);
        return response()->json([
            'success' => true,
            'deposit' => $deposit,
        ]);
    }
}
